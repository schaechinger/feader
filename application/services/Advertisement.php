<?php

class Application_Service_Advertisement
{
    private static $_instance = null;

    private $_ads;

    public static function getInstance()
    {
        if (null === self::$_instance)
        {
            self::$_instance = new Application_Service_Advertisement();
        }
        return self::$_instance;
    }

    public function __construct()
    {

    }
}
