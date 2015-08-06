<?php

class Application_Plugin_Maintenance extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $publicControllers = [
            'cron',
            'index',
            'share'
        ];
        if (false !== array_search($request->getControllerName(), $publicControllers)) {
            return;
        }
        
        $user = Application_Model_UserRepository::getInstance()->
            get(Application_Service_Session::getInstance()->getSessionId());
        if ($user && 'admin' === $user->getRole()) {
            return;
        }

        $request->setModuleName('default');
        $request->setControllerName('maintenance');
        $request->setActionName('index');
    }
}
