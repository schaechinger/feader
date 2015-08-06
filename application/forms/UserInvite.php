<?php

class Application_Form_UserInvite extends Zend_Form
{
    public function init()
    {
        $translate = Application_Service_Language::getInstance();

        $this->setMethod('post');

        // email
        $this->addElement('text', 'email',
            ['label'        => $translate->_('invite_label'),
                'required'		=> true,
                'filters'		=> ['StringTrim'],
                'validators'	=> ['EmailAddress']]
        );

        // submit
        $this->addElement('submit', 'submit',
            ['label' => $translate->_('invite_send'),
                'ignore' => true]);

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
