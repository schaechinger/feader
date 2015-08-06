<?php

class IndexController extends Feader_ControllerAbstract
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('entrance');

        if (!is_null($this->_session->getSessionId()))
        {
            $this->redirect('home/fead');
        }

        $request = $this->getRequest();
        $form = new Application_Form_UserLogin();

        $form->setAction('user/login');
        $this->view->form = $form;
    }

    public function feadbackAction()
    {
        $this->_resource = 'user';
        $this->isAllowed('view');
        $request = $this->getRequest();
        $form = new Application_Form_UserFeadback();

        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                Application_Model_FeadbackRepository::getInstance()->send($form->getValue('message'));
                $mail = new Application_Service_Mail();
                $mail->report('New feadback from ' . $this->isLoggedin()->getName() . ':<br>' . $form->getValue('message'));
                echo '<div class="center"><br>' . $this->_translate->_('feadback_send_successful') . '</div>';
                return;
            }
        }

        $this->view->form = $form;
    }

    public function shortcutsAction()
    {

    }

    public function unsupportedAction()
    {
        $this->_helper->layout->setLayout('entrance');
    }
}
