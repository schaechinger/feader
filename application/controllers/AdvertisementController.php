<?php

class AdvertisementController extends Feader_ControllerAbstract
{
    public function init()
    {
        parent::init();

        $this->_resource = 'user';
    }

    public function indexAction()
    {

    }

    public function addAction()
    {
        $this->isAllowed('control');

        $request = $this->getRequest();
        $form = new Application_Form_AdvertisementAdd();

        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                Application_Model_AdvertisementRepository::getInstance()->add(
                    new Application_Model_Entity_Advertisement(
                        ['title'  => $form->getValue('name'),
                         'code'   => $form->getValue('code'),
                         'active' => ('1' === $this->getParam('active'))]
                    )
                );

                $this->redirect('/admin/ad');
            }
        }

        $this->view->form = $form;
    }

    public function composeAction()
    {
        $this->isAllowed('compose');

        $request = $this->getRequest();
        $form = new Application_Form_BlogPost();

        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                Application_Model_BlogRepository::getInstance()->post(
                    new Application_Model_Entity_BlogPost(
                        ['userId'   => $this->_session->getSessionId(),
                         'title'    => $form->getValue('name'),
                         'content'  => $form->getValue('content'),
                         'language' => $form->getValue('language'),
                         'tags'     => $form->getValue('tags')]
                    )
                );
            }
        }
        else
        {
            $form->getElement('language')->setValue('en');
        }

        $this->view->form = $form;
    }
}
