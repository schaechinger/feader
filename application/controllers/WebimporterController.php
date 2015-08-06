<?php

class WebimporterController extends Feader_ControllerAbstract
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('entrance');
    }

    public function addAction()
    {
        $this->_helper->layout->disableLayout();
        $url = $this->getParam('u');
        $status = 2;

        if (is_null($this->_session->getSessionId())) {
            $status = 3;
        } else if ($url) {
            $feads = Application_Service_Fead::getInstance()->searchFead($url);

            if (is_null($feads) || 1 !== sizeof($feads)) {
                $status = 2;
            } else {
                $fead = new Application_Model_Entity_Fead($feads[0]);
                $id = Application_Model_FeadRepository::getInstance()->addFead($fead);
                $status = 1;
            }
        }

        $this->redirect("/img/webimporter/$status.png");
    }

    public function statusAction()
    {
        $this->_helper->layout->disableLayout();

        if (is_null($this->_session->getSessionId())) {
            $status = 3;
        } else {
            $status = 4;
        }

//        echo file_get_contents("/img/webimporter/$status.png");

        $this->redirect("/img/webimporter/$status.png");
    }
}