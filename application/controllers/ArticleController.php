<?php

class ArticleController extends Feader_ControllerAbstract
{
    public function init()
    {
        parent::init();

        $this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {

    }

    public function favoriteAction()
    {
        $this->view->article = Application_Model_UserArticleRepository::getInstance()->favorite($this->getParam('id'));
    }

    public function showAction()
    {
        $articleId = $this->getParam('id');
        $feadId = $this->getParam('fead');
        if (is_null($articleId) || is_null($feadId))
        {
            $this->redirect('home/notprivileged');
        }

        $this->view->article = Application_Model_ArticleRepository::getInstance()->getArticle($articleId, $feadId);
        if (!is_null($this->view->article))
        {
            $this->view->fead = Application_Model_FeadRepository::getInstance()
                ->getFeadsForUser($this->isLoggedin()->getId(), $feadId)[0];

            $meta = Application_Model_UserArticleRepository::getInstance()
                        ->getArticleMeta($this->view->article->getId());

            $article = new Application_Model_Entity_UserArticle(
                ['articleId' => $this->view->article->getId(),
                 'feadId'    => $this->view->article->getFeadId(),
                 'unread'    => 0,
                 'favorite'  => 0]);
            $this->view->article->setUnread(0);

            if (is_null($meta))
            {
                Application_Model_UserArticleRepository::getInstance()->add($article);
                Application_Model_UserFeadRepository::getInstance()->clickIncrease($feadId);
            }
            else
            {
                $this->view->article->setFavorite($meta->isFavorite());
                $article->setFavorite($meta->isFavorite());
                if ($meta->isUnread())
                {
                    Application_Model_UserFeadRepository::getInstance()->clickIncrease($feadId);
                }
                Application_Model_UserArticleRepository::getInstance()->update($article);
            }
        }
        else
        {
            $this->redirect('home/notprivileged');
        }
    }

    public function tagAction()
    {

    }

    public function unreadAction()
    {
        $this->view->article = Application_Model_UserArticleRepository::getInstance()->unread($this->getParam('id'));
    }
}
