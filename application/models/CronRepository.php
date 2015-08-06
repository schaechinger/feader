<?php

class Application_Model_CronRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'cron';
        }

        return self::$_instance;
    }

    public function error($code, $message)
    {
        $this->_db->insert($this->_table,
            ['type' => $code,
             'uri'  => $message,
             'date' => new Zend_Db_Expr('NOW()')]
        );
    }

    public function noaccess($from)
    {
        $this->_db->insert($this->_table,
            ['type' => 'noaccess',
             'uri'  => $from,
             'date' => new Zend_Db_Expr('NOW()')]
        );
    }

    public function thumb($fead, $url)
    {
        if (!$url)
        {
            return;
        }
        $this->_db->insert($this->_table,
            ['type' => 'image',
             'uri'  => $fead . ': ' . $url,
             'date' => new Zend_Db_Expr('NOW()')]
        );
    }
}
