<?php

class Application_Form_UserEdit extends Zend_Form
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
        /*
        $this->addElement('text', 'email',
            ['label'        => $translate->_('email'),
             'required'		=> true,
             'filters'		=> ['StringTrim'],
             'validators'	=>
                ['EmailAddress']]
        );
        */

        // submit
        $this->addElement('submit', 'submit',
            ['label'			=> $translate->_('save'),
             'ignore'		=> true]
        );

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}