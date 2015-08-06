<?php

class Application_Model_Entity_Share extends Application_Model_Entity_EntityAbstract
{
    private $_articleId;
    private $_publicKey;

    // articleId
    public function setArticleId($articleId)
    {
        $this->_articleId = $articleId;
        return $this;
    }

    public function getArticleId()
    {
        return $this->_articleId;
    }

    // publicKey
    public function setPublicKey($publicKey)
    {
        $this->_publicKey= $publicKey;
        return $this;
    }

    public function getPublicKey()
    {
        return $this->_publicKey;
    }
}
