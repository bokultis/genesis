<?php

class IndexController extends HCMS_Controller_Action_Cms
{
    public function indexAction(){
        throw new Zend_Controller_Action_Exception("Page not found",404);
    }
}