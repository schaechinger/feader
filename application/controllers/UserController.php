<?php

class UserController extends Feader_ControllerAbstract
{
    public function init()
    {
        parent::init();

        $this->_resource = 'user';
    }

    public function indexAction()
    {
        $this->isAllowed('view');

        $request = $this->getRequest();
        $form = new Application_Form_UserInvite();

        if ($this->getRequest()->isPost())
        {
            $this->view->post = true;
            if ($form->isValid($request->getPost()))
            {
                if (!$this->_userRepo->emailExists($form->getValue('email')))
                {
                    $this->_mail->sendInvitation($form->getValue('email'), $this->isLoggedin());
                    $this->view->success = true;
                }
                else if ($form->getValue('email') === $this->isLoggedin()->getEmail())
                {
                    $form->getElement('email')->addError('There is no need to invite yourself');
                }
                else
                {
                    $form->getElement('email')->addError('Your friend already signed up to feader');
                }
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $this->isAllowed('view');
        $request = $this->getRequest();
        $form = new Application_Form_UserEdit();

        $id = $this->_session->getSessionId();

        if (!isset($id))
        {
            $this->login();
        }

        $user = $this->isLoggedin();
        $form->getElement('firstname')->setValue($user->getFirstName());
        $form->getElement('lastname')->setValue($user->getLastName());
        //$form->getElement('email')->setValue($user->getEmail());

        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                //$email = strtolower($form->getValue('email'));
                $user = $this->isLoggedin();
                $user->setFirstName($form->getValue('firstname'));
                $user->setLastName($form->getValue('lastname'));
                //$user->setEmail($email);
                $this->_userRepo->update($user);

                $this->redirect('/user');
            }
        }

        $this->view->form = $form;
    }

    public function feadsAction()
    {
        $this->isAllowed('manage');
    }
	
	public function forgotAction()
	{
        if (!is_null($this->_session->getSessionId()))
        {
            $this->redirect('home/fead');
        }

        $request = $this->getRequest();
        $form = new Application_Form_UserForgotEmail();

        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                $this->redirect('user/validation');
            }
        }

        $this->view->form = $form;
	}
	
	public function loginAction()
	{
        $responseTime = rand(0, 1000000);

        if (!is_null($this->_session->getSessionId()))
        {
            if (!is_null($this->getParam('redirect')))
            {
                $this->view->redirect = $this->getParam('redirect');
            }
            else
            {
                $this->redirect('home/fead');
            }
        }

        $this->_helper->layout->setLayout('entrance');

		$request = $this->getRequest();
		$form = new Application_Form_UserLogin();

		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($request->getPost()))
			{
                $email = strtolower($form->getValue('email'));
				$user = $this->_userRepo->login($email,
                        Application_Model_Hash::hash($form->getValue('password')));

                // pending account
                if (is_null($user) && Application_Model_SignUpRepository::getInstance()->emailExists($email))
                {
                    $form->getElement('email')->addError($this->_translate->_('login_pending'));
                }
                // not existing
                else if (is_null($user) || is_null($user->getEmail()))
                {
                    $form->getElement('email')->addError($this->_translate->_('login_incorrect'));
                }
                // deactivated
                else if ($user->getRole() === 'deactivated')
                {
                    $form->getElement('email')->addError($this->_translate->_('login_deactivated'));
                }
                // setup session
                else if ($user->getRole() === 'guest')
                {
                    $form->getElement('email')->addError($this->_translate->_('login_not_validated'));
                }
                else
                {
                    $this->_session->setSessionId($user->getId());

                    usleep($responseTime);

                    // redirect
                    if (!is_null($form->getValue('redirect')))
                    {
                        $this->redirect($form->getValue('redirect'));
                    }
                    $this->redirect('home/fead');
                }
            }
		}
        else if ($this->getParam('redirect'))
        {
            $form->getElement('redirect')->setValue($this->getParam('redirect'));
        }

		$this->view->form = $form;
	}
	
	public function logoutAction()
	{
        $this->_helper->layout->disableLayout();
        $this->_session->clearSession();

        if (!is_null($this->getParam('redirect')))
        {
            $this->redirect($this->getParam('redirect'));
        }
        $this->redirect('index');
	}

    public function prefsAction()
    {
        $this->_helper->layout->disableLayout();

        if (!is_null($this->getParam('color')))
        {
            // update db
            Application_Model_UserSettingRepository::getInstance()->update('color', $this->getParam('color'));
            // update session
            $this->_session->setColor($this->getParam('color'));
        }
        else if (!is_null($this->getParam('menuStatic')))
        {
            Application_Model_UserSettingRepository::getInstance()->update('menuStatic', $this->getParam('menuStatic'));
            $this->_session->setMenuStatic($this->getParam('menuStatic'));
        }
        else if (!is_null($this->getParam('language')))
        {
            Application_Model_UserSettingRepository::getInstance()->update('language', $this->getParam('language'));
            $this->_session->setLanguage($this->getParam('language'));
        }
    }
	
	public function signupAction()
	{
        $this->_helper->layout->setLayout('entrance');

		$request = $this->getRequest();
		$form = new Application_Form_UserSignup();

        if ($this->isLoggedin())
        {
            $this->view->loggedin = true;
        }
		else if ($this->getRequest()->isPost())
		{
            if ('0' === $this->getParam('agree'))
            {
                $form->getElement('agree')->addError($this->_translate->_('signup_not_agreed'));
            }
			if ($form->isValid($request->getPost()))
			{
                $valid = true;
                $email = strtolower($form->getValue('email'));
                if($form->getValue('password') !== $form->getValue('repassword'))
                {
                    $form->getElement('repassword')->addError($this->_translate->_('signup_not_matching'));
                    $valid = false;
                }
                if ($this->_userRepo->emailExists($email) ||
                    Application_Model_SignUpRepository::getInstance()->emailExists($email))
                {
                    $form->getElement('email')->addError($this->_translate->_('signup_existing'));
                    $valid = false;
                }
                $domain = substr($email, strpos($email, '@') + 1);
                if (!Application_Model_FakeMailRepository::getInstance()->isValid($domain)) {
                    $form->getElement('email')->addError($this->_translate->_('signup_fakemail'));
                    $valid = false;
                }
                if ($valid)
                {
                    $user = new Application_Model_Entity_User();
                    $user->setFirstName($form->getValue('firstname'));
                    $user->setLastName($form->getValue('lastname'));
                    $user->setEmail($email);
                    $user->setPassword(Application_Model_Hash::hash($form->getValue('password')));
                    $code = Application_Model_SignUpRepository::getInstance()
                            ->signup($user, '1' === $form->getValue('agreenotify'), $form->getValue('code'));
                    $this->_mail->sendSignUp($user, $code);

                    $this->_mail->report($user->getName() . ' signed up on feader with ' . $user->getEmail());

                    echo '<div class="center">' . $this->_translate->_('signup_success') . '</div>';
                    return;
                }
			}

            $this->view->form = $form;
		}
        else
        {
            $form->getElement('code')->setValue($this->getParam('code'));
            $this->view->form = $form;
        }
	}

    public function upgradeAction()
    {
        $this->isAllowed('view');
        $request = $this->getRequest();
        $form = new Application_Form_UserUpgrade();

        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                $valid = true;
                // check passwords
                if($form->getValue('password') !== $form->getValue('repassword'))
                {
                    $form->getElement('repassword')->addError('Your passwords don\'t match');
                    $valid = false;
                }
                // check email valid
                if($form->getValue('email') !== $form->getValue('reemail'))
                {
                    $form->getElement('reemail')->addError('Your emails don\'t match');
                    $valid = false;
                }
                if ($valid)
                {
                    $user = new Application_Model_Entity_User();
                    $user->setFirstname($form->getValue('firstname'));
                    $user->setLastname($form->getValue('lastname'));
                    $user->setEmail($form->getValue('email'));
                    $user->setPassword(Application_Model_Hash::hash($form->getValue('password')));
                    $code = $this->_userRepo->signup($user);
                    $this->_mail->sendSignUp($user, $code);
                }
            }
        }

        $this->view->form = $form;
    }

    public function validationAction()
    {
        $this->_helper->layout->setLayout('entrance');

        $request = $this->getRequest();
        $form = new Application_Form_UserValidation();

        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                if (Application_Model_ValidationRepository::getInstance()->validateCode($form->getValue('code')))
                {
                    $this->_session->clearSession();
                    echo '<div class="center">' . $this->_translate->_('validation_success') . '</div>';
                    return;
                }
                else
                {
                    $form->getElement('code')->addError($this->_translate->_('validation_invalid'));
                }
            }
        }
        else if (!is_null($this->getParam('code')))
        {
            if (Application_Model_ValidationRepository::getInstance()->validateCode($this->getParam('code')))
            {
                $this->_session->clearSession();
                echo '<div class="center">' . $this->_translate->_('validation_success') . '</div>';
                return;
            }
            else
            {
                $form->getElement('code')->addError($this->_translate->_('validation_invalid'));
            }
        }

        $this->view->form = $form;
    }
}

