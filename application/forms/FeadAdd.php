<?php

class Application_Form_FeadAdd extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        $this->setMethod('post');

        // url
        $this->addElement('text', 'url',
            ['label'        => $translate->_('add_fead_label'),
             'required'		=> true,
             'filters'		=> ['StringTrim']]
        );
        $this->getElement('url')->setAttrib('onkeydown', 'if (13 === event.keyCode) addFead()');

        // submit
        $this->addElement('button', 'submit',
            ['label' => $translate->_('add_fead'),
             'ignore' => true]);
        $this->getElement('submit')->setAttrib('onclick', 'addFead()');

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
