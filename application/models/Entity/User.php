<?php

class Application_Model_Entity_User extends Application_Model_Entity_EntityAbstract
{
    protected $_id;
    protected $_firstName;
    protected $_lastName;
    protected $_email;
    protected $_password;
    protected $_role;

    /**
     * get feads for id
     * @return Application_Model_Entity_Fead[]
     */
    public function getFeads()
    {
        return Application_Model_FeadRepository::getInstance()->getFeadsForUser($this->getId());
    }

    // id
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    // firstName
    public function setFirstName($firstName)
    {
        $this->_firstName = $firstName;
        return $this;
    }

    public function getFirstName()
    {
        return $this->_firstName;
    }

    // lastName
    public function setLastName($lastName)
    {
        $this->_lastName = $lastName;
        return $this;
    }

    public function getLastName()
    {
        return $this->_lastName;
    }

    // full name
    public function getName()
    {
        return "$this->_firstName $this->_lastName";
    }

    // email
    public function setEmail($email)
    {
        $this->_email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    // password
    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }

    public function getPassword()
    {
        return $this->_password;
    }

    // role
    public function getRole()
    {
        return $this->_role;
    }

    public function setRole($role)
    {
        $this->_role = $role;
    }
}
