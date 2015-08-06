<?php

class Application_Form_AdvertisementAdd extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        $this->setMethod('post');

        // title
        $this->addElement('text', 'name',
            ['label'    => $translate->_('title'),
             'required' => true,
             'filters'  => ['StringTrim']]
        );

        // code
        $this->addElement('textarea', 'code',
            ['label'    => $translate->_('code'),
             'required' => true]
        );

        // active
        $this->addElement('checkbox', 'active',
            ['label'        => $translate->_('active'),
             'required'     => true]
        );

        // submit
        $this->addElement('submit', 'submit',
            ['label'  => $translate->_('add'),
             'ignore' => true]);

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
