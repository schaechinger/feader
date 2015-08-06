<?php

class Application_Model_FakeMailRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'fakemail';
        }

        return self::$_instance;
    }

    public function isValid($domain)
    {
        return !($this->_db->query('SELECT domain FROM ' . $this->_table . ' WHERE domain=?',
                                 [$domain])->fetch()['domain']);
    }
}
