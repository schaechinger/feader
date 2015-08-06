<?php

class FeadController extends Feader_ControllerAbstract
{
    public function init()
    {
        parent::init();

        $this->_resource = 'user';
        $this->isAllowed('manage');
    }

    public function indexAction()
    {

    }

    public function addAction()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();
        $form = new Application_Form_FeadAdd();

        if ($this->getParam('url')) {
            $feads = Application_Service_Fead::getInstance()->searchFead($this->getParam('url'));

            // no fead found
            if (is_null($feads)) {
                echo json_encode(['error' => $this->_translate->_('add_fead_error')]);
            // one fead found (add it)
            } else if (1 === sizeof($feads)) {
                $userId = $this->_session->getSessionId();
                $fead = new Application_Model_Entity_Fead($feads[0]);
                $id = Application_Model_FeadRepository::getInstance()->addFead($fead);
                echo json_encode(['success' => $id]);
            // more feads found (select one)
            } else {
                echo json_encode(['feads' => $feads]);
            }
        } else {
            $this->view->form = $form;
        }
    }

    public function deleteAction()
    {
        if (!is_null($this->getParam('id'))) {
            Application_Model_UserFeadRepository::getInstance()->delete($this->getParam('id'));
        } else if (!is_null($this->getParam('folder'))) {
            Application_Model_UserFolderRepository::getInstance()->delete($this->getParam('folder'));
        }
    }

    public function folderAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_FeadCreateFolder();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $id = Application_Model_UserFolderRepository::getInstance()->add($form->getValue('folder'));

                if (null === $id) {
                    $form->getElement('folder')->addError($this->_translate->_('create_folder_error'));
                } else {
                    $this->redirect('fead/manage');
                }
            }
        }

        $this->view->form = $form;
    }

    public function importAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_FeadImport();

        if (!is_null($this->getParam('file'))) {
            $path = realpath(APPLICATION_PATH . '/../data/uploads/import') . '/' . $this->getParam('file') . '.xml';
            $document = new DOMDocument();
            $document->load($path);
            foreach ($document->getElementsByTagName('outline') as $element) {
                $url = $element->getAttributeNode('xmlUrl')->nodeValue;
                $title = $element->getAttributeNode('title')->nodeValue;
                echo '<div class="grid-100 tablet-grid-100 mobile-grid-100"><p>';
                if (null === $url) {
                    echo '<span class="icon-warning-sign"></span>';
                    // TODO folders
                } else if (!strpos($url, 'www.google.com/reader/public')) {
                    $fead = new Application_Model_Entity_Fead();
                    $fead->setTitle($title)
                        ->setUrl($url);

                    Application_Model_FeadRepository::getInstance()->addFead($fead);
                    echo '<span class="icon-ok-sign"></span>';
                }
                echo $title . '</p>';
                if (null === $url) {
                    echo '<p>' . $this->_translate->_('import_no_folders') . '</p>';
                }
                echo '<hr></div>';
            }
        } else if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $unique = uniqid($this->isLoggedin()->getId() . '-');
                $name = $_FILES['opml']['tmp_name'];

                echo 'from: ' . $name . '<br>';
                $dest = realpath(APPLICATION_PATH . '/../data/uploads/import/') . "/$unique.xml";
                echo 'to: ' . $dest . '<br>';
                if (move_uploaded_file($name, $dest)) {
                    chmod($dest, 0777);
                    $this->redirect('/fead/import/file/' . $unique);
                }
            }
            $this->view->form = $form;
        } else {
            $this->view->form = $form;
        }
    }

    public function loadAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->isAllowed('view');
        $page = $this->getParam('page');
        $latest = $this->getParam('latest');
        $feadId = null;

        if (null === $this->getParam('type')) {
            $feadId = $this->getParam('id');
            $folderId = $this->getParam('folder');
            if (!is_null($folderId) || '0' === $feadId) {
                $feadId = null;
                $this->view->displayFeadTitle = true;
            }

            $articles = Application_Model_ArticleRepository::getInstance()
                ->getArticlesForUser($feadId, $page, $latest, $folderId);
        } else if ('unread' === $this->getParam('type')) {
            $this->view->displayFeadTitle = true;
            $articles = Application_Model_ArticleRepository::getInstance()->getUnreadArticlesForUser($page, $latest);
        } else if ('favorites' === $this->getParam('type')) {
            $this->view->displayFeadTitle = true;
            $articles = Application_Model_ArticleRepository::getInstance()->getFavoritesForUser($page, $latest);
        }

        $this->view->articles = $articles;
    }

    public function manageAction()
    {
        $id = $this->_session->getSessionId();
        $this->view->feads = Application_Model_FeadRepository::getInstance()->listFeadsForUser();
        $this->view->folders = Application_Model_UserFolderRepository::getInstance()->listFoldersForUser();
    }

    public function readAction()
    {
        $this->_helper->layout()->disableLayout();
        Application_Model_FeadRepository::getInstance()->markAllAsreadForFead($this->getParam('id'),
            $this->getParam('type'));
    }

    public function renameAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_FeadRename();

        $feadid = $this->getParam('id');
        if (is_null($feadid)) {
            $this->redirect('fead/manage');
        }
        $id = $this->isLoggedin()->getId();
        $fead = Application_Model_FeadRepository::getInstance()->getFeadsForUser($id,
            $feadid)[0];

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $fead->setTitle($form->getValue('name'));
                Application_Model_UserFeadRepository::getInstance()->update($fead, $id);
                $this->redirect('fead/manage');
            }
        } else {
            $form->getElement('name')->setValue($fead->getTitle());
        }

        $this->view->form = $form;
    }

    public function renamefolderAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_FolderRename();

        $folderId = $this->getParam('id');
        if (is_null($folderId)) {
            $this->redirect('fead/manage');
        }
        $folder = Application_Model_UserFolderRepository::getInstance()->getFolder($folderId);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $folder->setTitle($form->getValue('name'));
                Application_Model_UserFolderRepository::getInstance()->update($folder);
                $this->redirect('fead/manage');
            }
        } else {
            $form->getElement('name')->setValue($folder->getTitle());
        }

        $this->view->form = $form;
    }

    public function updateAction()
    {
        $this->_helper->layout()->disableLayout();
        $struct = json_decode($this->getParam('struct'));

        Application_Model_UserFeadRepository::getInstance()->reorder($struct);
    }
}
