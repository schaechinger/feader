<?php

class Application_Model_FeadbackRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'feadback';
        }

        return self::$_instance;
    }

    public function send($message)
    {
        $this->_db->insert($this->_table,
            ['userId'   => $this->_userId,
             'message'    => $message,
             'date'  => new Zend_Db_Expr('NOW()')]
        );
    }

    /**
     * @param $row
     * @return Application_Model_Entity_BlogPost|null
     */
    private function postFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_BlogPost($row);
        }
        else
        {
            return null;
        }
    }
}
