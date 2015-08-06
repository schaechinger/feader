<?php

class Application_Form_UserSignup extends Zend_Form
{
	public function init()
	{
        $translate = Application_Service_Language::getInstance();

		$this->setMethod('post');

		// firstname
		$this->addElement('text', 'firstname',
		    ['label'     	=> $translate->_('firstname'),
			 'required'		=> true,
			 'filters'		=> ['StringTrim'],
			 'validators'	=>
                [['validator' => 'StringLength', 'options' => [1, 100]]]]
		);
        $this->getElement('firstname')->setAttrib('class', 'firstFocus');
		
		// lastname
		$this->addElement('text', 'lastname',
			['label'     	=> $translate->_('lastname'),
			 'required'		=> true,
			 'filters'		=> ['StringTrim'],
			 'validators'	=>
				[['validator' => 'StringLength', 'options' => [1, 100]]]]
		);

		// email
		$this->addElement('text', 'email',
			['label'	    => $translate->_('email'),
			 'required'		=> true,
			 'filters'		=> ['StringTrim'],
			 'validators'	=> ['EmailAddress']]
		);

		// password
		$this->addElement('password', 'password',
			['label'     	=> $translate->_('password'),
			 'required'		=> true,
             'validators'   =>
                [['validator' => 'StringLength', 'options' => [8, null]]]]
		);
		
		// re-enter password
		$this->addElement('password', 'repassword',
			['label'     	=> $translate->_('password_reenter'),
			 'required'		=> true]
		);

        // agree to terms, data usage and data privacy / cookie usage
        $this->addElement('checkbox', 'agree',
            ['label'        => $translate->_('signup_agree'),
             'required'     => true]
        );
        $this->getElement('agree')->setAttrib('class', 'checkbox');
        $this->getElement('agree')->addValidator(new Zend_Validate_InArray(array(1)), false);

        // agree to sending emails
        $this->addElement('checkbox', 'agreenotify',
            ['label'        => $translate->_('signup_agree_notify'),
             'required'     => false]
        );
        $this->getElement('agreenotify')->setAttrib('class', 'checkbox');

		// submit
		$this->addElement('submit', 'submit',
			['label'		=> $translate->_('signup'),
             'require'      => false]
		);

        // code
        $this->addElement('hidden', 'code',
            ['required' => false]);
		
		// csrf
		$this->addElement('hash', 'csrf',
			['ignore'		=> true]
        );
	}
}
