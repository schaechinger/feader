<?php

class Application_Model_Entity_UserFolder extends Application_Model_Entity_EntityAbstract
{
    private $_id;
    private $_userId;
    private $_title;
    private $_order;
    private $_folder;

    // id
    public function setId($id)
    {
        $this->_id = intval($id);
        return $this;
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
        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    // order
    public function setOrder($order)
    {
        $this->_order = intval($order);
        return $this;
    }

    public function getOrder()
    {
        return $this->_order;
    }

    // folder
    public function setFolder($folder)
    {
        $this->_folder = intval($folder);
        return $this;
    }

    public function getFolder()
    {
        return $this->_folder;
    }
}
