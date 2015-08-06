<?php

class MaintenanceController extends Feader_ControllerAbstract
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('maintenance');
    }
}
