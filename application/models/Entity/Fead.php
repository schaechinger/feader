<?php

class Application_Model_Entity_Fead extends Application_Model_Entity_EntityAbstract
{
    private $_id;
    private $_title;
    private $_url;
    private $_order;
    private $_folder;
    private $_type;

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

    // url
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->_url;
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

    // type
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }
}
