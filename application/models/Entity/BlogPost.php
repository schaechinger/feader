<?php

class Application_Model_Entity_BlogPost extends Application_Model_Entity_EntityAbstract
{
    private $_id;
    private $_userId;
    private $_title;
    private $_language;
    private $_content;
    private $_tags;
    private $_date;

    // id
    public function setId($id)
    {
        $this->_id = $id;
    }

    public function getId()
    {
        return $this->_id;
    }

    // userId
    public function setUserId($userId)
    {
        $this->_userId = $userId;
        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    // title
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    // language
    public function setLanguage($language)
    {
        $this->_language = $language;
        return $this;
    }

    public function getLanguage()
    {
        return $this->_language;
    }

    // content
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->_content;
    }

    // tags
    public function setTags($tags)
    {
        $this->_tags = $tags;
        return $this;
    }

    public function getTags()
    {
        return $this->_tags;
    }

    // date
    public function setDate($date)
    {
        $this->_date = $date;
        return $this;
    }

    public function getDate()
    {
        return $this->_date;
    }
}
