<?php

class AdminController extends Feader_ControllerAbstract
{
    public function init()
    {
        parent::init();

        $this->_resource = 'panel';
        $this->isAllowed('control');
    }

    public function indexAction()
    {

    }

    public function adAction()
    {
        $this->view->ads = Application_Model_AdvertisementRepository::getInstance()->getAdvertisements();
    }

    public function galleryAction()
    {

    }

    public function newsAction()
    {

    }

    public function testAction()
    {

    }

    public function userAction()
    {

    }
}
