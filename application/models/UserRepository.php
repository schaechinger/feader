<?php

class Application_Model_UserRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'user';
        }

        return self::$_instance;
    }

    // add a user through the cron job after sign uo
    public function add(Application_Model_Entity_Signup $signUp)
    {
        $role = 'guest';
        if ($signUp->isValidated())
        {
            $role = 'user';
        }
        $this->_db->insert($this->_table,
            ['firstName'      => $signUp->getFirstName(),
             'lastName'       => $signUp->getLastName(),
             'email'          => $signUp->getEmail(),
             'password'       => $signUp->getPassword(),
             'role'           => $role,
             'feaderVersion'  => Application_Model_AdminRepository::getInstance()->get('feaderVersion'),
             'termVersion'    => Application_Model_AdminRepository::getInstance()->get('termsVersion'),
             'privacyVersion' => Application_Model_AdminRepository::getInstance()->get('privacyVersion'),
             'date'           => $signUp->getDate()]);

        return $this->_db->lastInsertId($this->_table);
    }

    // check if email exists
    public function emailExists($email)
    {
        return (!is_null($this->_db->query('SELECT email FROM ' . $this->_table . ' WHERE email=?', $email)->fetch()['email']));
    }

    // delete user
	public function delete($id)
	{
	    // delete validation if available
        // delete user_settings
        // delete signup
        // delete user_article
        // delete user_folder
        // delete user_fead
        // delete user

        // set new next user id if it was the latest user
	}

    /**
     * @param $id
     * @return Application_Model_Entity_User|null
     */
    public function get($id)
    {
        return $this->userFromRow($this->_db->query('SELECT * FROM ' . $this->_table . ' WHERE id=?',
            $id)->fetch());
    }

    public function getByEmail($email)
    {
        return $this->userFromRow($this->_db->query('SELECT * FROM ' . $this->_table . ' WHERE email=?',
            [$email])->fetch());
    }

    public function getLatestDate()
    {
        return $this->_db->query('SELECT date FROM ' . $this->_table . ' ORDER BY `date` DESC LIMIT 0, 1')->fetch()['date'];
    }

    public function getRole($id)
    {
        return $this->_db->query('SELECT role FROM ' . $this->_table . ' WHERE id=?',
        [$id])->fetch()['role'];
    }

    public function login($email, $password)
    {
        $user = $this->userFromRow($this->_db->query('SELECT * FROM ' . $this->_table .
            ' WHERE email=?', [$email])->fetch());

        if (!is_null($user))
        {
            // user is correct
            if ($user->getPassword() === $password)
            {
                // add entry to login table
                $this->_db->insert('user_login',
                    ['userId' => $user->getId(),
                     'date'   => new Zend_Db_Expr('NOW()')]);
                return $user;
            }
            // password is wrong
            else
            {
                return new Application_Model_Entity_User();
            }
        }
        // user does not exist
        else
        {
            return null;
        }
    }

	public function update(Application_Model_Entity_User $user)
    {
        return $this->_db->update($this->_table,
            ['firstName' => $user->getFirstName(),
             'lastName'  => $user->getLastName(),
             'email'     => $user->getEmail(),
             'password'  => $user->getPassword(),
             'role'      => $user->getRole()],
            $this->_db->quoteInto('id=?', $user->getId()));
	}

    public function summary()
    {
        return array_map([$this, 'userFromRow'], $this->_db->query(
                'SELECT * FROM ' . $this->_table)->fetchAll());
    }

    /**
     * @param $row
     * @return Application_Model_Entity_User|null
     */
    private function userFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_User($row);
        }
        else
        {
            return null;
        }
    }
}