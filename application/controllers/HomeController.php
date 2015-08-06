<?php

class HomeController extends Feader_ControllerAbstract
{
    public function init()
    {
        parent::init();

        $this->_resource = 'home';
    }

	public function indexAction()
	{
        $this->isAllowed('view');
	}

    public function favoritesAction()
    {
        $this->isAllowed('view');
    }

    public function feadAction()
    {
        $this->isAllowed('view');
        $this->view->feadId = $this->getParam('id');
        if ($this->_getParam('folder'))
        {
            $this->view->folder = $this->getParam('folder');
            $this->view->feadId = null;
        }

        $form = new Application_Form_FeadAdd();
        $form->setAction('/fead/add');
        $this->view->form = $form;

        if (!is_null($this->view->feadId))
        {
            $this->view->feadTitle = Application_Model_FeadRepository::getInstance()->getFeadForId($this->view->feadId);
        }
        else if (!is_null($this->view->folder))
        {
            $this->view->folderTitle = Application_Model_UserFolderRepository::getInstance()->getFolder($this->view->folder)->getTitle();
        }
    }

    public function notprivilegedAction()
    {
        $this->_helper->layout->disableLayout();

        $resource = $this->getParam('resource');
        $privilege = $this->getParam('privilege');
    }

    public function tagsAction()
    {
        $this->isAllowed('view');
    }

    public function todayAction()
    {
        $this->isAllowed('view');
    }

    public function unreadAction()
    {
        $this->isAllowed('view');
    }
}
