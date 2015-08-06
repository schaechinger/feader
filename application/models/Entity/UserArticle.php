<?php

class Application_Model_Entity_UserArticle extends Application_Model_Entity_EntityAbstract
{
    private $_id;
    private $_articleId;
    private $_unread;
    private $_favorite;

    // id
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    // articleId
    public function setArticleId($articleId)
    {
        $this->_articleId = $articleId;
        return $this;
    }

    public function getArticleId()
    {
        return $this->_articleId;
    }

    // unread
    public function setUnread($unread)
    {
        $this->_unread =$unread;
        return $this;
    }

    public function isUnread()
    {
        return $this->_unread;
    }

    // favorite
    public function setFavorite($favorite)
    {
        $this->_favorite = $favorite;
        return $this;
    }

    public function isFavorite()
    {
        return $this->_favorite;
    }
}
