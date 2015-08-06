<?php

class Application_Model_Entity_Signup extends Application_Model_Entity_EntityAbstract
{
    private $_id;
    private $_userId;
    private $_firstName;
    private $_lastName;
    private $_email;
    private $_password;
    private $_language;
    private $_date;
    private $_validated;
    private $_notify;

    // id
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    // userId
    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    public function getUserId()
    {
        return $this->_userId;
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

    // language
    public function setLanguage($language)
    {
        $this->_language = $language;
        return $this;
    }

    public function getLanguage()
    {
        return $this->_language;
    }

    // date

    public function setDate($date)
    {
        $this->_date = $date;
        return $this;
    }

    public function getDate()
    {
        return $this->_date;
    }

    // validated
    public function setValidated($validated)
    {
        $this->_validated = $validated;
        return $this;
    }

    public function isValidated()
    {
        return $this->_validated;
    }

    // notify
    public function setNotify($notify)
    {
        $this->_notify = $notify;
        return $this;
    }

    public function mayNotify()
    {
        return $this->_notify;
    }
}