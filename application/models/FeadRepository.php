<?php

class Application_Model_FeadRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'fead';
        }

        return self::$_instance;
    }

    public function addFead(Application_Model_Entity_Fead $fead)
    {
        $id = $this->urlExists($fead->getUrl());

        if (!$id)
        {
            $this->_db->insert($this->_table,
                ['title' => $fead->getTitle(),
                 'url'   => $fead->getUrl()]);
            $id = $this->_db->lastInsertId($this->_table);

            Application_Service_Fead::getInstance()->updateFead($fead->getUrl(), $id);
        }

        Application_Model_UserFeadRepository::getInstance()->add($id);

        return $id;
    }

    /**
     * @param $id int
     * @return Application_Model_Entity_Fead|null
     */
    public function get($id)
    {
        return $this->feadFromRow($this->_db->query('SELECT * FROM fead WHERE id=?',
                    [$id])->fetch());
    }

    /**
     * @return array
     */
    public function getAllIds()
    {
        return $this->_db->query('SELECT DISTINCT feadId as id FROM user_fead')->fetchAll();
    }

    public function getFeadForId($id)
    {
        return $this->_db->query('SELECT title FROM user_fead WHERE userId=? AND feadId=?',
                    [$this->_userId, $id])->fetch()['title'];
    }

    public function listFeadsForUser()
    {
        $query = 'SELECT t1.id as id, t2.title as title, t1.url as url, t2.order as `order`, t2.folder as folder ' .
            'FROM ' . $this->_table . ' t1 ' . 'JOIN user_fead t2 ON (t1.id=t2.feadId) ' .
            'WHERE t2.userId=? ORDER BY folder, `order` ASC';

        return array_map([$this, 'feadFromRow'], $this->_db->query($query,
                        [$this->_userId])->fetchAll());
    }

    /**
     * @param $id
     * @param $feadId
     * @return Application_Model_Entity_Fead[]
     */
    public function getFeadsForUser($id = null, $feadId = null)
    {
        $whereUser = '';
        if (!is_null($id))
        {
            $whereUser = $this->_db->quoteInto(' t2.userId=?', $id);
        }

        $whereFead = '';
        if (!is_null($feadId))
        {
            $whereFead = $this->_db->quoteInto(' t2.feadId=?', $feadId);
        }

        $and = '';
        if (!is_null($id) && !is_null($feadId))
        {
            $and = ' AND';
        }

        $where = '';
        if (!is_null($id) || !is_null($feadId))
        {
            $where = ' WHERE';
        }

        $query = 'SELECT t1.id as id, t2.title as title, t1.url as url, t2.order as `order`, t2.folder as folder ' .
            'FROM ' . $this->_table . ' t1 ' . 'JOIN user_fead t2 ON (t1.id=t2.feadId)' .
            $where . $whereUser . $and . $whereFead . ' ORDER BY `order` ASC';

        return array_map([$this, 'feadFromRow'], $this->_db->query($query, [$id])->fetchAll());
    }

    public function getUnreadCountForFead($feadId = null)
    {
        $where = '';
        if (!is_null($feadId))
        {
            $where = ' AND a.feadId=' . $feadId;
        }

        $border = date('Y-m-d H:i:s', strtotime('-1 hour'));

        return $this->_db->query('SELECT count(*) as unread FROM article a ' .
                    'LEFT OUTER JOIN user_article ua ON (a.id=ua.articleId AND ua.userId=?) ' .
                    'WHERE (ua.userId IS NULL OR ua.unread=1)' . $where/* . ' AND a.dateModified>?'*/,
                    [$this->_userId /*, $border*/])->fetch()['unread'];
    }

    public function markAllAsreadForFead($feadId, $type)
    {
        $where = '';
        if (!is_null($feadId))
        {
            $where = ' AND a.feadId=' . $feadId;
        }
        $favorites = 'ua.userId IS NULL OR ua.unread=1';
        if ('favorites' === $type)
        {
            $favorites = 'ua.favorite=1 AND ua.unread=1';
        }

        $ids = $this->_db->query('SELECT DISTINCT a.id, ua.favorite ' .
            'FROM article a LEFT JOIN user_article ua ON (a.id=ua.articleId AND ua.userId=?) ' .
            'JOIN user_fead uf ON (a.feadId=uf.feadId AND uf.userId=?) ' .
            'WHERE (' . $favorites . ') AND a.feadId IN (' .
                'SELECT feadId FROM user_fead WHERE userId=?)' . $where,
                [$this->_userId, $this->_userId, $this->_userId])->fetchAll();

        $updateIds = '';
        $insertIds = '';

        foreach ($ids as $id)
        {
            if (null !== $id['favorite'])
            {
                if ('' === $updateIds)
                {
                    $updateIds = 'userId=' . $this->_userId . ' AND (articleId=' . $id['id'];
                }
                else
                {
                    $updateIds .= ' OR articleId=' . $id['id'];
                }
            }
            else
            {
                $insertIds .= '(' . $this->_userId . ', ' . $id['id'] . '), ';
            }
        }

        $insertIds = trim($insertIds, ', ');



        if ('' !== $updateIds)
        {
            $update = 'UPDATE user_article ' .
                'SET unread=0 ' .
                'WHERE ' . $updateIds . ')';

            $this->_db->query($update);
        }

        if ('' !== $insertIds)
        {
            $insert = 'INSERT INTO user_article ' .
                    '(userId, articleId) VALUES ' . $insertIds;
            $this->_db->query($insert);
        }
    }

    /**
     * @return Application_Model_Entity_Fead[]
     */
    public function processFeads()
    {
        return array_map([$this, 'feadFromRow'], $this->_db->query('SELECT * FROM ' . $this->_table)->fetchAll());
    }

    public function urlExists($url)
    {
        $row = $this->_db->query('SELECT url, id FROM ' . $this->_table . ' WHERE url=?',
                [$url])->fetch();

        if ($row['url'] !== $url)
        {
            return 0;
        }
        else
        {
            return $row['id'];
        }
    }

    /**
     * @param $row
     * @return Application_Model_Entity_Fead|null
     */
    private function feadFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_Fead($row);
        }
        else
        {
            return null;
        }
    }
}
