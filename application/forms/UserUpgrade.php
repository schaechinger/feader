<?php

class Application_Form_UserUpgrade extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $translate = Application_Service_Language::getInstance();

        // email
        $this->addElement('text', 'email',
            ['label'        => $translate->_('email'),
             'required'		=> true,
             'filters'		=> ['StringTrim'],
             'validators'	=> ['EmailAddress']]
        );

        // code
        $this->addElement('text', 'code',
            ['label'        => 'Upgrade code',
             'required'		=> true,
             'filters'		=> ['StringTrim'],
             'validators'	=>
                 [['validator' => 'StringLength', 'options' => [10, null]]]]
        );

        // password
        $this->addElement('password', 'password',
            ['label'     	=> 'New password',
             'required'		=> true,
             'validators'   =>
                 [['validator' => 'StringLength', 'options' => [8, null]]]]
        );

        // re-enter password
        $this->addElement('password', 'repassword',
            ['label'     	=> 'Re-enter new password',
             'required'		=> true]
        );

        // submit
        $this->addElement('submit', 'submit',
            ['label'        => 'Upgrade account',
             'ignore'       => true]
        );

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
