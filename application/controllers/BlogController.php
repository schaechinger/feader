<?php

class BlogController extends Feader_ControllerAbstract
{
    public function init()
    {
        parent::init();

        $this->_resource = 'user';
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('entrance');

        $posts = Application_Model_BlogRepository::getInstance()->getPostsWithOffset(0);
        $this->view->posts = $posts;
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
    
    public function feadAction() {
        $this->view->type = $this->getParam('type');
    }
}
