<?php

class Application_Model_AdminRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'admin';
        }

        return self::$_instance;
    }

    public function get($key)
    {
        return $this->_db->query('SELECT value FROM ' . $this->_table . ' WHERE id=?',
                                 [$key])->fetch()['value'];
    }

    public function update($key, $value)
    {
        $this->_db->update($this->_table,
            ['value' => $value,
            $this->_db->quoteInto('id=?', [$key])]
        );
    }
}
