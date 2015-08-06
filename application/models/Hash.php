<?php

class Application_Model_Hash
{
    private static $salt = 'HUFDBuzcbz7q36c7e';


    public static function hash($value)
    {
        return sha1($value . self::$salt);
    }
}