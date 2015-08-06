<?php

class Application_Model_Acl extends Zend_Acl
{
    private static $_instance;


    public static function getInstance()
    {
        if (self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private  function __construct()
    {
        $this->setup();
    }

    private function setup()
    {
        // add roles
        $this->addRole(new Zend_Acl_Role('guest'))
             ->addRole(new Zend_Acl_Role('user'), 'guest')
             ->addRole(new Zend_Acl_Role('admin'));

        // add resources
        $this->addResource(new Zend_Acl_Resource('home'))
             ->addResource(new Zend_Acl_Resource('user'))
             ->addResource(new Zend_Acl_Resource('panel'));

        // set permissions
        $this->allow('guest', null, 'basic')
             ->allow('user', ['user', 'home'], ['manage', 'share', 'view'])
             ->deny('user', 'panel', null)
             ->allow('admin');
    }
}
