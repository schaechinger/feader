<?php

class Application_Model_UserSettingRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance === null)
        {
            self::$_instance = new self();
            self::$_instance->_table = 'user_setting';
        }

        return self::$_instance;
    }

    public function addSetting($id, $language)
    {
        $this->_db->insert($this->_table,
            ['id'       => $id,
             'language' => $language]
        );
    }

    public function getSetting()
    {
        return $this->settingFromRow($this->_db->query('SELECT * FROM ' . $this->_table . ' WHERE id=?',
            $this->_userId)->fetch());
    }

    public function update($setting, $value)
    {
        $this->_db->update(
            $this->_table,
            [$setting => $value],
            $this->_db->quoteInto('id=?', $this->_userId)
        );
    }

    /**
     * @param $row
     * @return Application_Model_Entity_UserSetting|null
     */
    private function settingFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_UserSetting($row);
        }
        else
        {
            return null;
        }
    }
}
