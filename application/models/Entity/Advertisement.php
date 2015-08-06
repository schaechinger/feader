<?php

class Application_Model_Entity_Advertisement extends Application_Model_Entity_EntityAbstract
{
    private $_id;
    private $_code;
    private $_title;
    private $_active;
    private $_feadIds;

    // id
    public function setId($id)
    {
        $this->_id = $id;
    }

    public function getId()
    {
        return $this->_id;
    }

    // code
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->_code;
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

    // active
    public function setActive($active)
    {
        $this->_active = $active;
        return $this;
    }

    public function isActive()
    {
        return $this->_active;
    }

    // feadIds
    public function setFeadIds($feadIds)
    {
        $this->_feadIds = $feadIds;
        return $this;
    }

    public function getFeadIds()
    {
        return $this->_feadIds;
    }
}
