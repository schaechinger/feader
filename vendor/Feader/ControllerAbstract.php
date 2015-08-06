<?php

abstract class Feader_ControllerAbstract extends Zend_Controller_Action
{
    /** @var Application_Model_Acl */
    protected $_acl;
    /** @var Zend_Locale */
    protected $_locale;
    /** @var Application_Service_Mail */
    protected $_mail;
    /** @var Application_Service_Session */
    protected $_session;
    /** @var Application_Service_Language */
    protected $_translate;
    /** @var Application_Model_UserRepository */
    protected $_userRepo;

    protected $_resource = null;

    public function init()
    {
        parent::init();

        $this->_acl = Application_Model_Acl::getInstance();
        $isCron = (0 === strpos($_SERVER['REQUEST_URI'], '/cron'));
        if (!$isCron)
        {
            $this->_session = Application_Service_Session::getInstance();
        }

        if (('index' === $this->getParam('controller') && 'unsupported' === $this->getParam('action')))
        {
//            $this->redirect('index/unsupported');
        }

        $this->_mail = new Application_Service_Mail();
        $this->_userRepo = Application_Model_UserRepository::getInstance();

        $frontendOptions = ['lifetime' => 60, 'automatic_serialization' => true];
        $backendOptions = ['cache_dir' => substr(APPLICATION_PATH, 0, strrpos(APPLICATION_PATH, '/')) . '/data/cache/'];
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

        Zend_Registry::set('Zend_Locale',  new Zend_Locale('de'));
        Zend_Locale::setCache($cache);

        if (!$isCron && $this->isLoggedin())
        {
            if (is_null($this->_session->getColor()))
            {
                $this->_session->setColor(Application_Model_UserSettingRepository::getInstance()
                    ->getSetting()->getColor());
            }
            if (is_null($this->_session->getMenuStatic()))
            {
                $this->_session->setMenuStatic(Application_Model_UserSettingRepository::getInstance()
                    ->getSetting()->getMenuStatic());
            }
            if (is_null($this->_session->getLanguage()))
            {
                $this->_session->setLanguage(Application_Model_UserSettingRepository::getInstance()
                    ->getSetting()->getLanguage());
            }
        }

        $this->_translate = Application_Service_Language::getInstance();
        Zend_Registry::set('Zend_Translate', $this->_translate);
        $this->view->translate = $this->_translate;

        if (!$isCron)
        {
            $this->view->staticMenu = intval($this->_session->getMenuStatic());
            $this->view->color = $this->_session->getColor();
        }
    }

    /**
     * @return Application_Model_Entity_User|bool
     */
    public function isLoggedin()
    {
        $id = $this->_session->getSessionId();

        if (null === $id)
        {
            return false;
        }
        else
        {
            return $this->_userRepo->get($id);
        }
    }

    public function isAllowed($privilege)
    {
        $id = $this->_session->getSessionId();
        if (!is_null($id))
        {
            $user = $this->_userRepo->get($id);
            if (!is_null($user))
            {
                if ($this->_acl->isAllowed($user->getRole(), $this->_resource, $privilege))
                {
                    return true;
                }
                else
                {
                    $this->redirect('home/notprivileged?resource=' . $this->_resource . '&privilege=' .
                    $privilege . '&role=' . $user->getRole());
                }
            }
        }

        $this->login();
    }

    public function login()
    {
        $this->redirect('user/login?redirect=' . $this->getParam('controller') . '/' . $this->getParam('action'));
    }
}
