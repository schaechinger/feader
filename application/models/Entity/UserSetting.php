<?php

class Application_Model_Entity_UserSetting extends Application_Model_Entity_EntityAbstract
{
    private $_id;
    private $_color;
    private $_language;
    private $_preview;
    private $_sunset;
    private $_menuStatic;

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

    // color
    public function setColor($color)
    {
        $this->_color = $color;
        return $this;
    }

    public function getColor()
    {
        return $this->_color;
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

    // preview
    public function setPreview($preview)
    {
        $this->_preview = $preview;
        return $this;
    }

    public function getPreview()
    {
        return $this->_preview;
    }

    // sunset
    public function setSunset($sunset)
    {
        $this->_sunset = $sunset;
        return $this;
    }

    public function getSunset()
    {
        return $this->_sunset;
    }

    // menuStatic
    public function setMenuStatic($menuStatic)
    {
        $this->_menuStatic = $menuStatic;
        return $this;
    }

    public function getMenuStatic()
    {
        return $this->_menuStatic;
    }
}
