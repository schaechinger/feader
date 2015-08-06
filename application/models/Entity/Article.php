<?php

class Application_Model_Entity_Article extends Application_Model_Entity_EntityAbstract
{
    private $_id;
    private $_feadId;
    private $_title;
    private $_preview;
    private $_url;
    private $_thumb;
    private $_dateCreated;
    private $_dateModified;
    private $_content;
    private $_guid;

    private $_feadTitle;
    private $_favorite = 0;
//    private $_explUnread = false;
    private $_unread = 1;
    private $_tags;

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

    // feadid
    public function setFeadId($feadId)
    {
        $this->_feadId = $feadId;
        return $this;
    }

    public function getFeadId()
    {
        return $this->_feadId;
    }

    // title
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    // preview
    public function setPreview($preview)
    {
        $this->_preview = $preview;
        return $this;
    }

    public function getPreview()
    {
        return $this->_preview;
    }

    // url
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    // thumb
    public function setThumb($thumb)
    {
        $this->_thumb = $thumb;
        return $this;
    }

    public function getThumb()
    {
        return $this->_thumb;
    }

    // dateCreated
    public function setDateCreated($dateCreated)
    {
        $this->_dateCreated = $dateCreated;
        return $this;
    }

    public function getDateCreated()
    {
        return $this->_dateCreated;
    }

    // dateModified
    public function setDateModified($date)
    {
        $this->_dateModified = $date;

        /*
        $border = date('Y-m-d H:i:s', strtotime('-1 hour'));
        if ($date < $border && !$this->_explUnread)
        {
            $this->setUnread(0);
        }
        */

        return $this;
    }

    public function getDate()
    {
        return $this->_dateModified;
    }

    // content
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->_content;
    }

    // feadtitle
    public function setFeadTitle($feadTitle)
    {
        $this->_feadTitle = $feadTitle;
        return $this;
    }

    public function getFeadTitle()
    {
        return $this->_feadTitle;
    }

    // favorite
    public function getFavorite()
    {
        return $this->_favorite;
    }

    public function setFavorite($favorite)
    {
        if (null !== $favorite)
        {
            $this->_favorite = intval($favorite);
        }
        return $this;
    }

    // unread
    public function getUnread()
    {
        return $this->_unread;
    }

    public function setUnread($unread)
    {
        /*
        if (1 === intval($unread))
        {
            $this->_explUnread = true;
        }
        */

        if (null !== $unread)
        {
            $this->_unread = intval($unread);
        }
        return $this;
    }

    // tags
    public function getTags()
    {
        return $this->_tags;
    }

    public function setTags($tags)
    {
        $this->_tags = $tags;
        return $this;
    }

    // guid
    public function setGuid($guid)
    {
        $this->_guid = $guid;
        return $this;
    }

    public function getGuid()
    {
        return $this->_guid;
    }

    // fill
    public function fill() {
        /*
        $this->_title;
        $this->_preview;
        $this->_url;
        $this->_thumb;
        $this->_dateCreated;
        $this->_dateModified = ;
        $this->_content = "";
        */
        if (is_null($this->_guid)) {
            $this->_guid = "";
        }
    }
}
