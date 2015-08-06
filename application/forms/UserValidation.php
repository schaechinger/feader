<?php

class Application_Form_UserValidation extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        $this->setMethod('post');

        // code
        $this->addElement('text', 'code',
            ['label'        => $translate->_('validation_label'),
             'required'		=> true,
             'filters'		=> ['StringTrim']]
        );
        $this->getElement('code')->setAttrib('class', 'firstFocus');

        // submit
        $this->addElement('submit', 'submit',
            ['label'		=> $translate->_('validation'),
             'ignore'		=> true]
        );

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}