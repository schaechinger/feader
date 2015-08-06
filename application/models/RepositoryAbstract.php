<?php

class Application_Model_RepositoryAbstract
{
    /** @var Zend_Db_Adapter_Abstract */
    static protected $dbAdapter;
    /** @var Zend_Db_Adapter_Abstract */
    protected $_db;
    protected $_table;
    protected $_userId;

    public function __construct()
    {
        $this->openConnection();
        $this->_userId = 0;
        if (0 !== strpos($_SERVER['REQUEST_URI'], '/cron'))
        {
            $this->_userId = Application_Service_Session::getInstance()->getSessionId();
        }
    }

    public function __destruct()
    {

    }

    public function openConnection()
    {
        if (!self::$dbAdapter)
        {
            $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            $options = $bootstrap->getOptions();

            try
            {
                self::$dbAdapter = Zend_Db::factory($options['database']['adapter'],
                    ['host'     => $options['database']['params']['host'],
                        'username' => $options['database']['params']['username'],
                        'password' => $options['database']['params']['password'],
                        'dbname'   => $options['database']['params']['dbname']]
                );

                if (!self::$dbAdapter->isConnected())
                {
                    //$this->closeConnection();
                    //throw new PDOException;
                }
            }
            catch (PDOException $pdo)
            {
                die('<div class="big huge">
                       <span class="icon-warning-sign"></span>
                       <h1>feader reached the maximum limit of parallel users</h1>
                       <h2>We\'re working hard to fix this. Please come back later!</h2>
                    </div>');
            }
        }

        $this->_db = self::$dbAdapter;
        $this->_db->query('SET NAMES utf8');
    }

    public function closeConnection()
    {
        $this->_db->closeConnection();
    }
}
