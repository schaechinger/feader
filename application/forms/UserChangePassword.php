<?php

class Application_Form_UserChangePassword extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        $this->setMethod('post');

        // email
        $this->addElement('text', 'passwordold',
            ['label'        => $translate->_('password_old'),
             'required'		=> true,
             'filters'		=> ['StringTrim'],
             'validators'	=> ['EmailAddress']]
        );

        // password
        $this->addElement('password', 'passwordnew',
            ['label'     	=> $translate->_('password_new'),
             'required'		=> true,
             'validators'   =>
                [['validator' => 'StringLength', 'options' => [8, null]]]]
        );

        // re-enter password
        $this->addElement('password', 'repasswordnew',
            ['label'     	=> $translate->_('password_reenter_new'),
             'required'		=> true]
        );

        // submit
        $this->addElement('submit', 'submit',
            ['label'        => $translate->_('passwprd_change'),
             'ignore'       => true]);

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
