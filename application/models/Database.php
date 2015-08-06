<?php

class Application_Model_Database extends Zend_Db_Adapter_Pdo_Mssql
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance === null)
        {
            self::connect();
        }

        return self::$_instance;
    }

    private static function connect()
    {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $options = $bootstrap->getOptions();

        self::$_instance = Zend_Db::factory($options['database']['adapter'],
              ['host'     => $options['database']['params']['host'],
               'username' => $options['database']['params']['username'],
               'password' => $options['database']['params']['password'],
               'dbname'   => $options['database']['params']['dbname']]
        );
    }

    public function openConnection()
    {


        try
        {
            self::connect();

            if (!$this->isConnected())
            {
                //$this->_db->closeConnection();
                //throw new PDOException;
            }
        }
        catch (PDOException $pdo)
        {
            echo '<div class="big huge">' .
                '   <span class="icon-warning-sign"></span>' .
                '   <h1>feader reached the maximum limit of users</h1>' .
                '   <h2>We\'re working hard to fix this. Please come back later!</h2>' .
                '</div>';
            die();
        }
    }

    public function setUTF8()
    {
        $this->query('SET NAMES utf8');
    }

    public function closeConnection()
    {
        $this->closeConnection();
    }
}