<?php

class ShareController extends Feader_ControllerAbstract
{
    public function init()
    {
        parent::init();

        $this->_resource = 'user';
    }

    public function indexAction()
    {
        $this->redirect('/share/article');
    }

    public function addAction()
    {
        $this->isAllowed('share');
        $this->_helper->layout->disableLayout();

        $result = $this->generateKey();

        if (null === $result) {
            return;
        }

        echo 'mailto:?subject=' . str_replace('&', '%26', $result['article']->getTitle()) .
            '&body=http://fead.co/' . $result['key'];
    }

    public function articleAction()
    {
        $key = $this->getParam('public');
        $id = null;

        if (6 >= strlen($key))
        {
            $id = Application_Model_ShareRepository::getInstance()->getIdForKey($key);
        }

        if (null !== $id)
        {
            $this->view->key = $key;
            Application_Model_ShareRepository::getInstance()->increaseKey($key);
            $this->view->article = Application_Model_ArticleRepository::getInstance()->getPublicArticle($id);
            $this->view->fead = Application_Model_FeadRepository::getInstance()->get($this->view->article->getFeadId());
        }
    }

    public function socialAction() {
        $this->isAllowed('share');
        $this->_helper->layout->disableLayout();

        $result = $this->generateKey();
        $type = $this->getParam('type');

        if (null === $result) {
            return;
        }

        $title = $result['article']->getTitle();

        if ('facebook' === $type) {
            $this->redirect('https://www.facebook.com/share.php?i=348757995252076&u=http%3A%2F%2Ffead.co%2F' .
                $result['key'] . '&p[title]=' . urlencode($title));
        } else if ('twitter' === $type) {
            if (strlen($title) > 100) {
                $text = substr($title, 0, 100);
                $text = substr($text, 0, strrpos($text, ' '));
            } else {
                $text = $title;
            }
            $text = urlencode($text);
            $this->redirect('http://twitter.com/intent/tweet?via=feader_eu&related=feader_eu&url=http%3A%2F%2Ffead.co%2F' .
                $result['key'] . '&text=' . $text);
        }
    }

    private function generateKey() {
        $id = $this->getParam('id');
        $id = intval($id);
        $article = Application_Model_ArticleRepository::getInstance()->getArticle($id);

        if (!is_int($id))
        {
            return null;
        }
        else if (!$article)
        {
            return null;
        }
        else
        {
            $key = Application_Model_ShareRepository::getInstance()->getKeyForId($id);
            if (is_null($key))
            {
                $key = Application_Model_ShareRepository::getInstance()->addArticle($id);
            }

            return ['key'     => $key,
                    'article' => $article];
        }
    }
}