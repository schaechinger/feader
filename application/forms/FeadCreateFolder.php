<?php

class Application_Form_FeadCreateFolder extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        $this->setMethod('post');

        // folder
        $this->addElement('text', 'folder',
            ['label'        => $translate->_('create_folder_label'),
             'required'		=> true,
             'filters'		=> ['StringTrim']]
        );
        $this->getElement('folder')->setAttrib('class', 'firstFocus');

        // submit
        $this->addElement('submit', 'submit',
            ['label' => $translate->_('create_folder'),
             'ignore' => true]);

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
