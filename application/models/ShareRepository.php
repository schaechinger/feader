<?php

class Application_Model_ShareRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
            self::$_instance->_table = 'share_article';
        }

        return self::$_instance;
    }

    public function getIdForKey($key)
    {
        return $this->_db->query('SELECT articleId FROM ' . $this->_table . ' WHERE publicKey=?',
                    $key)->fetch()['articleId'];
    }

    public function getKeyForId($id)
    {
        return $this->_db->query('SELECT publicKey FROM ' . $this->_table . ' WHERE articleId=?',
                    $id)->fetch()['publicKey'];
    }

    public function increaseKey($key)
    {
        $this->_db->query('UPDATE ' . $this->_table . ' SET clicks=clicks+1 WHERE publicKey=?',
            [$key]);
    }

    public function addArticle($id)
    {
        $key = $this->generateKey();

        while ($this->getIdForKey($key))
        {
            $key = $this->generateKey();
        }

        $this->_db->insert($this->_table,
            ['articleId' => $id,
             'publicKey' => $key,
             'userId'    => $this->_userId]);

        return $key;
    }

    private function generateKey()
    {
        $bucket = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $chars = strlen($bucket);

        $key = '';

        for ($i = 0; $i < 6; $i++)
        {
            $index = mt_rand(0, $chars - 1);
            $key .= $bucket[$index];
        }

        return $key;
    }

    /**
     * @param $row
     * @return Application_Model_Entity_Share|null
     */
    private function shareFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_Share($row);
        }
        else
        {
            return null;
        }
    }
}
