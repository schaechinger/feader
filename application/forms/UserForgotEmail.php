<?php

class Application_Form_UserForgotEmail extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        $this->setMethod('post');

        // email
        $this->addElement('text', 'email',
            ['label'        => $translate->_('email'),
             'required'		=> true,
             'filters'		=> ['StringTrim'],
             'validators'	=> ['EmailAddress']]
        );

        // submit
        $this->addElement('submit', 'submit',
            ['label'        => $translate->_('forgot_send_email'),
             'ignore'       => true]);

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
