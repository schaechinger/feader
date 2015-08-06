<?php

class Application_Model_ValidationRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'validation';
        }

        return self::$_instance;
    }

    public function addValidation(Application_Model_Entity_User $user)
    {
        $code = Application_Model_Hash::hash($user->getEmail());
        $this->_db->insert($this->_table, ['code' => $code, 'email' => $user->getEmail()]);

        return $code;
    }

    public function codeExists($code)
    {
        return ($this->_db->query('SELECT code FROM ' . $this->_table . ' WHERE code=?', [$code])->fetch()['code'] === $code);
    }

    public function validateCode($code)
    {
        $row = $this->_db->query('SELECT * FROM ' . $this->_table . ' WHERE code=?', [$code])->fetch();

        if ($row['code'] !== $code)
        {
            return false;
        }
        else
        {
            Application_Model_SignUpRepository::getInstance()->setValidated($row['email']);
            $user = Application_Model_UserRepository::getInstance()->getByEmail($row['email']);
            if (!is_null($user))
            {
                $user->setRole('user');
                Application_Model_UserRepository::getInstance()->update($user);
            }
            $this->delete($code);
            return true;
        }
    }

    public function delete($code)
    {
        $this->_db->delete($this->_table, $this->_db->quoteInto('code=?', $code));
    }
}
