<?php

class Application_Form_UserFeadback extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        $this->setMethod('post');

        // messagge
        $this->addElement('textarea', 'message',
            ['label'        => $translate->_('feadback_label'),
             'required'		=> true,
             'filters'		=> ['StringTrim']]
        );
        $this->getElement('message')->setAttrib('class', 'firstFocus');

        // submit
        $this->addElement('submit', 'submit',
            ['label'        => $translate->_('feadback_send'),
             'ignore'       => true]);

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
