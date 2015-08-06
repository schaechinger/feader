<?php

class Application_Model_AdvertisementRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'advertisement';
        }

        return self::$_instance;
    }

    /**
     * @return Application_Model_Entity_Advertisement[]|null
     */
    public function getAdvertisements()
    {
        $query = 'SELECT * FROM ' . $this->_table .  ' ORDER BY `active` DESC';
        return array_map([$this, 'advertisementFromRow'], $this->_db->query($query)->fetchAll()
        );
    }

    public function getAdForFead($id)
    {
        $where = $this->_db->quoteInto('WHERE active=?', 1);
        $ads = $this->_db->query('SELECT code FROM ' . $this->_table .
                ' ' . $where)->fetchAll();
        srand(time());
        return $ads[mt_rand(0, sizeof($ads) - 1)];
    }

    public function add(Application_Model_Entity_Advertisement $ad)
    {
        $this->_db->insert($this->_table,
            ['title'  => $ad->getTitle(),
             'code'   => $ad->getCode(),
             'active' => $ad->isActive()]
        );
    }

    public function update(Application_Model_Entity_Advertisement $ad)
    {
        $this->_db->update($this->_table,
            ['title'  => $ad->getTitle(),
             'code'   => $ad->getCode(),
             'active' => $ad->isActive(),
             'feadIds' => $ad->getFeadIds()],
            $this->_db->quoteInto('id=?', [$ad->getId()])
        );
    }

    /**
     * @param $row
     * @return Application_Model_Entity_Advertisement|null
     */
    private function advertisementFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_Advertisement($row);
        }
        else
        {
            return null;
        }
    }
}
