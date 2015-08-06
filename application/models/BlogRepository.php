<?php

class Application_Model_BlogRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'blogPost';
        }

        return self::$_instance;
    }

    /**
     * @param $offset
     * @return Application_Model_Entity_BlogPost[]|null
     */
    public function getPostsWithOffset($offset)
    {
        $query = 'SELECT * FROM ' . $this->_table .  ' ORDER BY `date` DESC LIMIT ' . $offset . ', 10';
        return array_map([$this, 'postFromRow'], $this->_db->query($query)->fetchAll()
        );
    }

    public function post(Application_Model_Entity_BlogPost $post)
    {
        $this->_db->insert($this->_table,
            ['userId'   => $post->getUserId(),
             'title'    => $post->getTitle(),
             'content'  => $post->getContent(),
             'date'     => new Zend_Db_Expr('NOW()'),
             'language' => $post->getLanguage(),
             'tags'     => $post->getTags()]
        );
    }

    public function update(Application_Model_Entity_BlogPost $post)
    {
        $this->_db->update($this->_table,
            ['userId'   => $post->getUserId(),
             'title'    => $post->getTitle(),
             'content'  => $post->getContent(),
             'date'     => new Zend_Db_Expr('NOW()'),
             'language' => $post->getLanguage(),
             'tags'     => $post->getTags()],
            $this->_db->quoteInto('id=?', [$post->getId()])
        );
    }

    /**
     * @param $row
     * @return Application_Model_Entity_BlogPost|null
     */
    private function postFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_BlogPost($row);
        }
        else
        {
            return null;
        }
    }
}
