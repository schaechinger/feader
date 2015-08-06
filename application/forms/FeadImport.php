<?php

class Application_Form_FeadImport extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        // file
        $this->addElement('file', 'opml',
            ['label'       => $translate->_('import_feads_label'),
             'required'    => true,
             'filters'     => ['StringTrim'],
             'validators'  =>
                [['validator' => 'Size', 8 * 1024 * 1024]]]
        );
        $this->getElement('opml')->addValidator('Extension', false, 'xml');

        // submit
        $this->addElement('submit', 'submit',
            ['label'  => $translate->_('import'),
             'ignore' => true]);

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
