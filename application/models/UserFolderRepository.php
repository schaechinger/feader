<?php

class Application_Model_UserFolderRepository extends Application_Model_RepositoryAbstract
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance === null)
        {
            self::$_instance = new self();
            self::$_instance->_table = 'user_folder';
        }

        return self::$_instance;
    }

    /**
     * @param $title
     * @param int $folder
     * @return int|null
     */
    public function add($title, $folder = 0)
    {
        if (is_null($this->_db->query('SELECT userId FROM ' . $this->_table . ' WHERE title=? AND userId=?',
            [$title, $this->_userId])->fetch()['userId']))
        {
            $id = $this->_db->query('SELECT count(*) as folders FROM ' . $this->_table . ' WHERE userId=?',
                        [$this->_userId])->fetch()['folders'];
            $id++;

            $this->_db->insert($this->_table,
                ['id'     => $id,
                 'userId' => $this->_userId,
                 'title'  => $title,
                 'folder' => $folder]);

            return $id;
        }

        return null;
    }

    /**
     * Delete a folder
     * // TODO move all feads and folders within to the parent folder
     * @param $id int
     */
    public function delete($id)
    {
        $folder = $this->getFolder($id);
        $this->_db->delete($this->_table,
            [$this->_db->quoteInto('id=?', $id),
                $this->_db->quoteInto('userId=?', $this->_userId)]);
        $this->move($id, $folder->getFolder());
        Application_Model_UserFeadRepository::getInstance()->move($id, $folder->getFolder());
    }

    /**
     * @param $id int
     * @return Application_Model_Entity_UserFolder|null
     */
    public function getFolder($id)
    {
        return $this->folderFromRow($this->_db->query('SELECT * FROM ' . $this->_table . ' WHERE id=? AND userId=?',
                    [$id, $this->_userId])->fetch());
    }

    public function listFoldersForUser()
    {
        $query = 'SELECT id, title, `order`, folder ' .
            'FROM ' . $this->_table . '  ' .
            'WHERE userId=? ORDER BY `folder`, `order` ASC';

        return array_map([$this, 'folderFromRow'], $this->_db->query($query,
            [$this->_userId])->fetchAll());
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

    public function update(Application_Model_Entity_UserFolder $folder)
    {
        $this->_db->update($this->_table,
            ['title' => $folder->getTitle(),
             'order' => $folder->getOrder(),
             'folder' => $folder->getFolder()],
            [$this->_db->quoteInto('id=?', $folder->getId()),
             $this->_db->quoteInto('userid=?', $this->_userId)]);
    }

    /**
     * @param $row
     * @return Application_Model_Entity_UserFolder|null
     */
    private function folderFromRow($row)
    {
        if (is_array($row))
        {
            return new Application_Model_Entity_UserFolder($row);
        }
        else
        {
            return null;
        }
    }
}
