<?php

class Application_Model_UserArticleRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance === null)
        {
            self::$_instance = new self();
            self::$_instance->_table = 'user_article';
        }

        return self::$_instance;
    }

    /**
     * @param $id
     * @return Application_Model_Entity_UserArticle | null
     */
    private function getArticle($id)
    {
        return $this->articleFromRow($this->_db->query('SELECT * FROM ' . $this->_table .
            ' WHERE articleId=? AND userId=?', [$id, $this->_userId])->fetch());
    }

    public function getArticleMeta($id)
    {
        return $this->articleFromRow($this->_db->query('SELECT unread, favorite FROM user_article WHERE userId=? AND articleId=?',
            [$this->_userId, $id])->fetch());
    }

    public function getArticleInformationFromUser()
    {
        return NULL;
        return array_map([$this, 'articleFromRow'], $this->_db->query(
            'SELECT * FROM ' . $this->_table . ' WHERE userId=?', $this->_userId)->fetchAll());
    }

    public function update(Application_Model_Entity_UserArticle $article)
    {
        $this->_db->update($this->_table,
            ['unread' => $article->isUnread(),
             'favorite' => $article->isFavorite()],
            [$this->_db->quoteInto('articleId=?', $article->getArticleId()),
             $this->_db->quoteInto('userId=?', $this->_userId)]
        );
    }

    public function add(Application_Model_Entity_UserArticle $article, $favorite = 0)
    {
        $this->_db->insert($this->_table,
            ['userId'    => $this->_userId,
             'articleId' => $article->getArticleId(),
             'unread'    => $article->isUnread(),
             'favorite'  => $favorite]
        );
    }

    public function isFavorite($id)
    {
        return $this->_db->query('SELECT favorite FROM ' . $this->_table . ' ' .
            'WHERE articleId=? AND userId=?', [$id, $this->_userId])->fetch()['favorite'] === '1';
    }

    public function favorite($id)
    {
        $article = $this->getArticle($id);

        // article is unread
        if (is_null($article))
        {
            $article = new Application_Model_Entity_UserArticle(
                ['userId'   => $this->_userId,
                 'articleId' => $id,
                 'unread'   => 1,
                 'favorite' => 1]
            );
            $this->add($article, 1);
        }
        // change favorite state
        else
        {
            if ($article->isUnread() && $article->isFavorite())
            {
                $this->_db->delete($this->_table,
                    [$this->_db->quoteInto('articleId=?', $id),
                        $this->_db->quoteInto('userId=?', $this->_userId)]
                );
                $article->setFavorite(0);
            }
            else
            {
                $article->setFavorite(!$article->isFavorite());
                $this->update($article);
            }
        }

        return $article;
    }

    public function unread($id)
    {
        $article = $this->getArticle($id);

        if (is_null($article))
        {
            $article = new Application_Model_Entity_UserArticle(
                ['userId'   => $this->_userId,
                    'articleId' => $id,
                    'unread'   => 0,
                    'favorite' => 0]
            );
            $this->add($article);
        }
        // if article is favorite set the unread bit
        else if ($article->isFavorite($id))
        {
            $article->setUnread(!$article->isUnread());
            $this->update($article);
        }
        // delete the entry in user_article if the article should be read
        else
        {
            $this->_db->delete($this->_table,
                [$this->_db->quoteInto('articleId=?', $id),
                 $this->_db->quoteInto('userId=?', $this->_userId)]
            );
            $article->setUnread(1);
        }

        return $article;
    }

    /**
     * @param $row
     * @return Application_Model_Entity_UserArticle|null
     */
    private function articleFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_UserArticle($row);
        }
        else
        {
            return null;
        }
    }
}
