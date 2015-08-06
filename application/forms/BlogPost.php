<?php

class Application_Form_BlogPost extends Zend_Form
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

        // language
        $this->addElement('text', 'language',
            ['label'    => $translate->_('language'),
             'required' => true]
        );

        // tags
        $this->addElement('text', 'tags',
            ['label'    => $translate->_('tags'),
             'required' => false,
             'filters'  => ['StringTrim']]
        );

        // content
        $this->addElement('textarea', 'content',
            ['label'    => $translate->_('content'),
             'required'	=> true,
             'filters'	=> ['StringTrim']]
        );

        // submit
        $this->addElement('submit', 'submit',
            ['label'  => $translate->_('post'),
             'ignore' => true]);

        // csrf
        $this->addElement('hash', 'csrf',
            ['ignore'		=> true]
        );
    }
}
