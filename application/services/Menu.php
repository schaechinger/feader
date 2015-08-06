<?php

class Application_Service_Menu
{
    private $_url;
    private $_lists;

    public function generate($url)
    {
        $this->_url = $_SERVER['REDIRECT_URL'];

        $folders = Application_Model_UserFolderRepository::getInstance()->listFoldersForUser();
        $feads = Application_Model_FeadRepository::getInstance()->listFeadsForUser();

        $this->_lists = [];
        $this->_lists[0] = [];
        $this->_lists[0][0] = [];
        foreach ($folders as $folder)
        {
            $this->_lists[$folder->getId()] = [];
            $this->_lists[$folder->getId()][0] = [];
            if (0 !== $folder->getOrder())
            {
                $this->_lists[$folder->getFolder()][$folder->getOrder()] = $folder;
            }
            else
            {
                array_push($this->_lists[$folder->getFolder()][0], $folder);
            }
        }

        foreach ($feads as $fead)
        {
            if (0 !== $fead->getOrder())
            {
                $this->_lists[$fead->getFolder()][$fead->getOrder()] = $fead;
            }
            else
            {
                array_push($this->_lists[$fead->getFolder()][0], $fead);
            }
        }

        for ($l = 0; $l < sizeof($this->_lists); $l++)
        {
            uksort($this->_lists[$l], [$this, 'comp']);
        }

        $this->folder(0);
    }

    private function comp($a, $b)
    {
        return ($a > $b);
    }

    private function folder($level)
    {
        foreach ($this->_lists[$level] as $element)
        {
            // loop through 0 orders
            if (is_array($element))
            {
                foreach ($element as $e)
                {
                    if ('Application_Model_Entity_UserFolder' === get_class($e))
                    {
                        $this->printFolder($e);
                        $this->folder($e->getId());
                        echo '</ul></li>';
                    }
                    else
                    {
                        $this->printFead($e);
                    }
                }
            }
            // folder
            else if ('Application_Model_Entity_UserFolder' === get_class($element))
            {
                $this->printFolder($element);
                $this->folder($element->getId());
                echo '</ul></li>';
            }
            else
            {
                $this->printFead($element);
            }
        }
    }

    private function printFolder($folder)
    {
        echo '<li data-id="' . $folder->getId() . '" data-type="folder">
                <div class="element';

        $isSelected = '/home/fead/folder/' . $folder->getId() === $this->_url;
        if ($isSelected)
        {
            echo ' selected';
        }

        echo '">
                <p class="grid-85 tablet-grid-85 mobile-grid-85 feadTitle folder">
                        <span class="icon-chevron-down" onclick="expandFolder(' . $folder->getId() . ')" class="pointer"></span>
                        <a href="/home/fead/folder/' . $folder->getId() . '">';
        echo $folder->getTitle() .
                    '</a>
                </p>
                <p class="grid-15 tablet-grid-15 mobile-grid-15 unread">&nbsp;</p>
                <div class="clearfix"></div>
            </div>
            <ul id="folder' . $folder->getId() . '" class="sortable feadList';
        echo '" data-folder="' . $folder->getId() . '">';
    }

    private function printFead($fead)
    {
        $unread = Application_Model_FeadRepository::getInstance()
            ->getUnreadCountForFead($fead->getId());
        if (1 > $unread)
        {
            $unread = '&nbsp';
        }
        else if (999 < $unread) {
            $unread = 999;
        }

        echo '<li data-id="' . $fead->getId() . '">
                <div class="element';
        if ('/home/fead/id/' . $fead->getId() === $this->_url)
        {
            echo ' selected';
        }

        echo    '">
                    <p class="grid-85 tablet-grid-85 mobile-grid-85 feadTitle">
                        <a href="/home/fead/id/' . $fead->getId() . '"
                                class="feadTitle">' .
                            $fead->getTitle() .
                        '</a>
                    </p>
                    <p class="grid-15 tablet-grid-15 mobile-grid-15 unread">' . $unread . '</p>
                    <div class="clearfix"></div>
                </div>
            </li>';
    }
}
