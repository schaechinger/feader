<?php

class Application_Model_ArticleRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;
    private $pageSize = 50;

    public static function getInstance()
    {
        if (self::$_instance === null)
        {
            self::$_instance = new self();
            self::$_instance->_table = 'article';
        }

        return self::$_instance;
    }

    public function addArticle(Application_Model_Entity_Article $article)
    {
        $border = date('Y-m-d H:i:s', strtotime('-1 day'));
        $result = $this->_db->query('SELECT id FROM ' . $this->_table . ' WHERE feadId=? AND (dateCreated=? OR ' .
                'title=? OR content=?) AND dateModified>? ORDER BY dateModified DESC',
            [$article->getFeadid(), $article->getDateCreated(), $article->getTitle(),
                $article->getContent(), $border])->fetch();

        if (!$result)
        {
            $this->_db->insert($this->_table,
                ['feadId'       => $article->getFeadId(),
                 'title'        => $article->getTitle(),
                 'preview'      => $article->getPreview(),
                 'url'          => $article->getUrl(),
                 'thumb'        => $article->getThumb(),
                 'dateCreated'  => $article->getDateCreated(),
                 'dateModified' => $article->getDate(),
                 'content'      => $article->getContent(),
                 'guid'         => $article->getGuid()]
            );
        }
        // update existing article
        else
        {
            $this->_db->update($this->_table,
                ['feadId'       => $article->getFeadId(),
                 'title'        => $article->getTitle(),
                 'preview'      => $article->getPreview(),
                 'url'          => $article->getUrl(),
                 'dateModified' => $article->getDate(),
                 'content'      => $article->getContent()],
                $this->_db->quoteInto('id=?', [$result['id']])
            );
        }
    }

    public function getLatestDateForFead($id)
    {
        return $this->_db->query('SELECT `dateModified` FROM ' . $this->_table .
            ' WHERE feadId=? ORDER BY `dateModified` DESC LIMIT 0, 1', [$id])->fetch()['dateModified'];
    }

    public function getArticle($id, $feadId=null)
    {
        $article = $this->getPublicArticle($id);
        if ($article && !is_null($this->_db->query('SELECT userId FROM user_fead WHERE feadId=? AND userId=?',
            [$article->getFeadId(), $this->_userId])->fetch()['userId']))
        {
            return $article;
        }
    }

    public function getArticlesForUser($feadid = null, $page = null, $latest = null, $folder = null)
    {
        $sql = '';
        if (!is_null($feadid))
        {
            $sql = $this->_db->quoteInto(' AND f.id=?', [$feadid]);
        }
        $limit = '';
        if (!is_null($page))
        {
            $limit = ' LIMIT ' . $page * $this->pageSize . ', ' . $this->pageSize;
        }
        $beginning = '';
        if (!is_null($latest))
        {
            $beginning = $this->_db->quoteInto(' AND a.id>?', [$latest]);
        }
        $group = '';
        if (!is_null($folder))
        {
            $group = $this->_db->quoteInto(' AND uf.folder=?', [$folder]);
            $sql = '';
        }

        return array_map([$this, 'articleFromRow'], $this->_db->query(
            'SELECT DISTINCT a.id as id, a.feadId as feadId, a.title as title, a.preview as preview, ' .
            'a.url as url, a.dateModified as `dateModified`, uf.title as feadTitle, ' .
            'ua.unread, ua.favorite ' .
            'FROM `user_fead` uf ' .
            'JOIN `fead` f ON (uf.feadId = f.id' . $group . ') ' .
            'JOIN `article` a ON (f.id = a.feadId) ' .
            'LEFT JOIN user_article ua ON (a.id=ua.articleId AND ua.userId=?) ' .
            'WHERE uf.userId=?' . $sql . $beginning . ' GROUP BY a.id, a.title, a.dateModified ORDER BY a.dateModified DESC' . $limit,
            [$this->_userId, $this->_userId])->fetchAll());
    }

    public function getFavoritesForUser($page = null)
    {
        $limit = '';
        if (!is_null($page))
        {
            $limit = ' LIMIT ' . $page * $this->pageSize . ', ' . $this->pageSize;
        }

        return array_map([$this, 'articleFromRow'], $this->_db->query(
            'SELECT DISTINCT a.id as id, a.feadId as feadId, a.title as title, a.preview as preview, ' .
            'a.url as url, a.dateModified as `dateModified`, uf.title as feadTitle, ua.favorite, ua.unread ' .
            'FROM article a LEFT JOIN user_article ua ON (a.id=ua.articleId AND ua.userId=?) ' .
            'JOIN user_fead uf ON (a.feadId=uf.feadId AND uf.userId=?) ' .
            'WHERE ua.favorite=1 AND a.feadId IN (' .
            'SELECT feadId FROM user_fead WHERE userId=?) ORDER BY a.dateModified DESC' . $limit,
            [$this->_userId, $this->_userId, $this->_userId])->fetchAll());
    }

    public function getPublicArticle($id)
    {
        return $this->articleFromRow($this->_db->query('SELECT * FROM ' . $this->_table . ' WHERE id=?',
                    $id)->fetch());
    }

    public function getUnreadArticlesForUser($page = null)
    {
        $limit = '';
        if (!is_null($page))
        {
            $limit = ' LIMIT ' . $page * $this->pageSize . ', ' . $this->pageSize;
        }

        return array_map([$this, 'articleFromRow'], $this->_db->query(
            'SELECT DISTINCT a.id as id, a.feadId as feadId, a.title as title, a.preview as preview, ' .
            'a.url as url, a.dateModified as `dateModified`, uf.title as feadTitle, ua.favorite, ua.unread ' .
            'FROM article a LEFT JOIN user_article ua ON (a.id=ua.articleId AND ua.userId=?) ' .
            'JOIN user_fead uf ON (a.feadId=uf.feadId AND uf.userId=?) ' .
            'WHERE (ua.userId IS NULL OR ua.unread=1) AND a.feadId IN (' .
            'SELECT feadId FROM user_fead WHERE userId=?) ORDER BY a.dateModified DESC' . $limit,
            [$this->_userId, $this->_userId, $this->_userId])->fetchAll());
    }

    public function isRead($id)
    {
        return !is_null($this->_db->query('SELECT id FROM user_article ' .
                'WHERE articleId=? AND userId=?', [$id, $this->_userId])->fetch()['id']);
    }

    /**
     * @param $row
     * @return Application_Model_Entity_Article|null
     */
    private function articleFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_Article($row);
        }
        else
        {
            return null;
        }
    }
}
