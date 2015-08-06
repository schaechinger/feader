<?php

class ErrorController extends Feader_ControllerAbstract
{

    public function errorAction()
    {
        $this->_helper->layout->setLayout('entrance');

        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->code = 404;
                $this->view->message = $this->_translate->_('error_404');
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->code = 500;
                $this->view->message = $this->_translate->_('error_500');
                break;
        }

        Application_Model_CronRepository::getInstance()->error($this->getResponse()->getHttpResponseCode(), $errors->exception->getMessage());

        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasPluginResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

