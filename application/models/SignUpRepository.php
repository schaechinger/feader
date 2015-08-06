<?php

class Application_Model_SignUpRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'signup';
        }

        return self::$_instance;
    }

    // check if email exists
    public function emailExists($email)
    {
        return !is_null($this->_db->query('SELECT email FROM ' . $this->_table . ' WHERE email=?', $email)->fetch()['email']);
    }

    public function get($id)
    {
        return $this->signUpFromRow($this->_db->query('SELECT * FROM ' . $this->_table . ' WHERE id=?',
            $id)->fetch());
    }

    public function processSignup($limit, $date = null)
    {
        if (!is_null($date))
        {
            return array_map([$this, 'signUpFromRow'], $this->_db->query('SELECT * FROM ' . $this->_table .
                ' WHERE `date` > ? LIMIT 0, ' . $limit, [$date])->fetchAll());
        }
        else
        {
            return array_map([$this, 'signUpFromRow'], $this->_db->query('SELECT * FROM ' . $this->_table .
                ' LIMIT 0, ' . $limit)->fetchAll());
        }
    }

    public function setId($id, $email)
    {
        $this->_db->update($this->_table,
            ['userId' => $id],
            $this->_db->quoteInto('email=?', [$email]));
    }

    /**
     * @param Application_Model_Entity_User $user
     * @return string
     */
    public function signUp(Application_Model_Entity_User $user, $notify, $code = null)
    {
        $language = 'en';
        try
        {
            $locale = new Zend_Locale(Zend_Locale::BROWSER);
            $language = $locale->getLanguage();
            if ($language !== 'de') {
                $language = 'en';
            }
        }
        catch (Exception $e)
        { }

        $this->_db->insert($this->_table,
            ['firstName' => $user->getFirstName(),
             'lastName'  => $user->getLastName(),
             'email'     => $user->getEmail(),
             'password'  => $user->getPassword(),
             'language'  => $language,
             'notify'    => $notify,
             'date'      => new Zend_Db_Expr('NOW()'),
             'code'      => $code]);

        // get the user's id
        $user->setId($this->_db->lastInsertId($this->_table));

        // insert a dataset with the validation code to the validation table
        return Application_Model_ValidationRepository::getInstance()->addValidation($user);
    }

    public function setValidated($email)
    {
        return $this->_db->update($this->_table,
            ['validated' => 1],
            $this->_db->quoteInto('email=?', $email));
    }

    /**
     * @param $row
     * @return Application_Model_Entity_SignUp|null
     */
    private function signUpFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_Signup($row);
        }
        else
        {
            return null;
        }
    }
}
