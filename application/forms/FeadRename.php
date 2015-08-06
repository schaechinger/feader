<?php

class Application_Form_FeadRename extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        $this->setMethod('post');

        // name
        $this->addElement('text', 'name',
            ['label'        => $translate->_('rename_fead_label'),
             'required'		=> true,
             'filters'		=> ['StringTrim']]
        );
        $this->getElement('name')->setAttrib('class', 'firstFocus');

        // submit
        $this->addElement('submit', 'submit',
            ['label'        => $translate->_('rename_fead'),
             'ignore'       => true]);

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
