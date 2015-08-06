<?php

class Application_Model_UserFeadRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance === null)
        {
            self::$_instance = new self();
            self::$_instance->_table = 'user_fead';
        }

        return self::$_instance;
    }

    public function add($feadId)
    {
        if (is_null($this->_db->query('SELECT userId FROM ' . $this->_table . ' WHERE feadId=? AND userId=?',
            [$feadId, $this->_userId])->fetch()['userId']))
        {
            $title = $this->_db->query('SELECT title FROM fead WHERE id=?', $feadId)->fetch()['title'];
            $this->_db->insert($this->_table,
                ['userId' => $this->_userId,
                 'feadId' => $feadId,
                 'title'  => $title]);
        }
    }

    public function clickDecrease($id)
    {
        $this->_db->query('UPDATE ' . $this->_table . ' SET clicks=clicks-1 WHERE userId=? AND feadId=? AND clicks<>0',
                [$this->_userId, $id]);
    }

    /**
     * Increase the number of read articles of this fead
     * @param $id int
     */
    public function clickIncrease($id)
    {
        $this->_db->query('UPDATE ' . $this->_table . ' SET clicks=clicks+1 WHERE userId=? AND feadId=?',
                [$this->_userId, $id]);
    }

    public function clickRanking()
    {
        return $this->_db->query('SELECT feadId, title, clicks FROM user_fead WHERE userId=? AND clicks>0 ORDER BY clicks DESC LIMIT 0, 5',
                [$this->_userId])->fetchAll();
    }

    /**
     * Delete a fead for the current user
     * TODO remove entries in user_article for this fead
     * @param $id int
     */
    public function delete($id)
    {
        $this->_db->delete($this->_table,
            [$this->_db->quoteInto('feadId=?', $id),
                $this->_db->quoteInto('userId=?', $this->_userId)]);
    }

    public function move($from, $to)
    {
        $this->_db->update($this->_table,
            ['order'  => 0,
             'folder' => $to],
            [$this->_db->quoteInto('folder=?', $from),
             $this->_db->quoteInto('userId=?', $this->_userId)]
        );
    }

    public function reorder($folders)
    {
        foreach ($folders as $folder)
        {
            foreach($folder->feads as $i => $fead)
            {
                // folder
                if (0 === strpos($fead, 'f'))
                {
                    $fead = substr($fead, 2);
                    $this->_db->update('user_folder',
                        ['order'  => ($i + 1),
                         'folder' => $folder->id],
                        [$this->_db->quoteInto('id=?', $fead),
                         $this->_db->quoteInto('userId=?', $this->_userId)]);
                }
                // fead
                else
                {
                    $this->_db->update($this->_table,
                        ['order' => ($i + 1),
                         'folder' => $folder->id],
                        [$this->_db->quoteInto('feadId=?', $fead),
                         $this->_db->quoteInto('userId=?', $this->_userId)]);
                }
            }


        }
    }

    public function update(Application_Model_Entity_Fead $fead)
    {
        $this->_db->update($this->_table,
            ['title' => $fead->getTitle(),
             'order' => (int) $fead->getOrder(),
             'folder' => (int) $fead->getFolder()],
            [$this->_db->quoteInto('feadid=?', $fead->getId()),
             $this->_db->quoteInto('userid=?', $this->_userId)]);
    }
}
