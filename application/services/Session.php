<?php

class Application_Service_Session
{
    private static $_instance = null;

    protected $_session;

    public static function getInstance()
    {
        if (self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        $this->_session = new Zend_Session_Namespace('FeaderSession');

        // 1 week lifetime
        $this->_session->setExpirationSeconds(604800);

        if ($this->_session->updateTime)
        {
            $date = new DateTime();
            $date = $date->diff(new DateTime($this->_session->updateTime));

            if ($date->i >= 1)
            {
                $this->setColor(null);
                $this->setMenuStatic(null);
                $this->setLanguage(null);
                $this->_session->updateTime = date('Y-m-d H:i:s');
            }
        }
        else
        {
            $this->_session->updateTime = date('Y-m-d H:i:s');
        }
    }

    // userId
    public function setSessionId($id)
    {
        $this->_session->userid = $id;
        return $this;
    }

    public function getSessionId()
    {
        return $this->_session->userid;
    }

    public function clearSession()
    {
        Zend_Session::destroy(true);
    }

    // color
    public function setColor($color)
    {
        $this->_session->color = $color;
        return $this;
    }

    public function getColor()
    {
        return $this->_session->color;
    }

    // menuStatic
    public function setMenuStatic($menuStatic)
    {
        $this->_session->menuStatic = $menuStatic;
        return $this;
    }

    public function getMenuStatic()
    {
        return $this->_session->menuStatic;
    }

    // language
    public function setLanguage($language)
    {
        $this->_session->language = $language;
        return $this;
    }

    public function getLanguage()
    {
        return $this->_session->language;
    }

    // ad count
    public function addAdCount()
    {
        if (!$this->_session->adCount)
        {
            $this->_session->adCount = 1;
        }
        else
        {
            $this->_session->adCount++;
        }
        return $this;
    }

    public function clearAdCount()
    {
        $this->_session->adCount = 0;
    }

    public function getAdCount()
    {
        return (null === $this->_session->adCount ? 0 : $this->_session->adCount);
    }


}
