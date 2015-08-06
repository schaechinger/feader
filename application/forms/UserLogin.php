<?php

class Application_Form_UserLogin extends Zend_Form
{
	public function init()
	{
        $translate = Application_Service_Language::getInstance();

		$this->setMethod('post');
		
		// email
		$this->addElement('text', 'email',
			['label'	    => $translate->_('email'),
			 'required'		=> true,
			 'filters'		=> ['StringTrim'],
			 'validators'	=> ['EmailAddress']]
		);
        $this->getElement('email')->setAttrib('class', 'firstFocus');

        // password
        $this->addElement('password', 'password',
            ['label'        => $translate->_('password'),
             'required'		=> true]
        );
		
		// submit
		$this->addElement('submit', 'submit',
			['label'		=> $translate->_('login'),
             'igonore'      => true]
		);

        // redirect
        $this->addElement('hidden', 'redirect',
            ['ignore'       => true,
                'required'		=> false]
        );

		// csrf
		$this->addElement('hash', 'csrf',
			['ignore'       => true]
		);
	}
}
