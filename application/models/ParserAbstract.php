<?php

abstract class Application_Model_ParserAbstract
{
    /** @var int */
    protected $_feadId;
    /** @var String */
    protected $_data;
    /** @var Application_Model_Entity_Fead */
    protected $_fead;
    /** @var Application_Model_Entity_Article[] */
    protected $_articles = [];
    /** @var int */
    private $_currentArticle = 0;

    public function __construct($data, $id = 0)
    {
        $this->_data = $data;
        $this->_feadId = $id;
    }

    public function setFeadId($id)
    {
        $this->_feadId = $id;
    }

    /**
     * parse the complete data
     * @param $date String the date of the latest article in the database
     * @return void
     */
    public abstract function parseWithMinDate($date = null);

    /**
     * retrieve all information about the fead itself
     * @return Fead
     */
    public function getFeadInfo() {
        return $this->_fead;
    }

    /**
     * retrieve the next article or null
     * @return Application_Model_Entity_Article|null
     */
    public function nextArticle()
    {
        // check if another article exists
        if ($this->_currentArticle === sizeof($this->_articles)) {
            return null;
        }

        return $this->_articles[$this->_currentArticle++];
    }

    /**
     * convert a string into the required date format for the database
     * important: returned date is in german local time (mez)!
     * @param $string String the given string
     * @return string|null
     */
    public function parseDate($string)
    {
        $localTime = new DateTime();
        $date = strtotime($string);

        // format yyyy-mm-ddThh:ii:ssZ
        if ('z' === strpos(strtolower($string), -1, 1)) {
            $string = strtolower($string);
            $string = str_replace('z', '', $string);
            $string = str_replace('t', ' ', $string);
            $date = new DateTime($string, DateTimeZone::UTC);
            $date->setTimezone($localTime->getTimezone());
            $date =  $date->format('Y-m-d H:i:s');
        }
        else if ($date) {
            $date = date('Y-m-d H:i:s', $date);
            $date = new DateTime($date);
            $date = $date->format('Y-m-d H:i:s');
        }

        if ($date && date('Y-m-d H:i:s') > $date) {
            return $date;
        }

        // if the date can not be parsed or is in future return the current date
        return date('Y-m-d H:i:s');
    }
}
