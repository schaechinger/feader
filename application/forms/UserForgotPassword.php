<?php

class Application_Form_UserForgotPassword extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        $this->setMethod('post');

        // email
        $this->addElement('text', 'code',
            ['label'        => $translate->_('forgot_code'),
             'required'		=> true,
             'filters'		=> ['StringTrim'],
             'validators'	=> ['EmailAddress']]
        );

        // password
        $this->addElement('password', 'password',
            ['label'     	=> $translate->_('forgot_password'),
             'required'		=> true,
             'validators'   =>
                [['validator' => 'StringLength', 'options' => [8, null]]]]
        );

        // re-enter password
        $this->addElement('password', 'repassword',
            ['label'     	=> $translate->_('forgot_password_reenter'),
             'required'		=> true]
        );

        // submit
        $this->addElement('submit', 'submit',
            ['label'        => $translate->_('forgot_send_password'),
             'ignore'       => true]);

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
