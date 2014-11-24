<?php

/**
 * Admin Menu Background controller
 *
 * @package Bgchanger
 * @subpackage Controllers
 * @copyright Horisen
 * @author dzonz
 */
class Bgchanger_AdminMenuController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;
    protected $_defaultLang = null;

    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
        $this->_defaultLang = Application_Model_TranslateMapper::getInstance()->getDefaultLang();
    }

    /**
     * List pages
     */
    public function menuAction() {
        $this->view->languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->menus = Cms_Model_MenuMapper::getInstance()->getMenus();
        $this->view->defaultLang = $this->_defaultLang;
    }

    /**
     * Ajax listing of pages
     */
    public function menuListAction() {
        //criteria
        $criteria = array(
            'application_id' => $this->_applicationId
        );
        if (null != $this->_getParam('langFilter')) {
            $criteria['lang'] = $this->_getParam('langFilter');
        }
        if(null != $this->_getParam('searchFilter')){
            $criteria['search_filter'] = $this->_getParam('searchFilter');
        }
        if(null != $this->_getParam('menuFilter')){
            $criteria['menu'] = $this->_getParam('menuFilter');
        }else{
            $criteria['menu'] = 'main';
        }
        
        //order
        $order = $this->_request->getParam('order');
        if (isset($order)) {
            $order = array($order);
        } else {
            $order = array("p.name ASC");
        }
        $records = Cms_Model_MenuItemMapper::getInstance()->fetchZendNavigationArray($criteria);
        $backgrounds = Bgchanger_Model_MenuItemBackgroundMapper::getInstance()->fetchAll(array(), array(), 'cms_menu_item_id');
        
        /* @var $record Cms_Model_MenuItem */
        $data = array(
            'rows' => array()
        );
        $data = $this->createMenuList($records, $backgrounds, $data);
        
        $this->_helper->json->sendJson($data);
    }
    
    private function createMenuList($records, $backgrounds, &$data){
        foreach ($records as $key => $record) {
            $item = $record['entity'];
            $lang = (null != $this->_getParam('langFilter'))? $this->_getParam('langFilter'): CURR_LANG;
            if(!is_null($item->get_page_id())){
                //default path
                $module = 'cms';
                $controller = 'page';
                $action = 'index';
            }
            else{
                $mca =explode("/", $item->get_path()) ;
                $module =(isset($mca[0]))? $mca[0]:"";
                $controller =(isset($mca[1]))? $mca[1]:"";
                $action =(isset($mca[2]))? $mca[2]:"";
            }
            $urlParams = array(
                'module'        => $module,
                'controller'    => $controller,
                'action'        => $action,
                'page_id'       => $item->get_page_id(),
                'lang'          => $lang
            );
            $image_path = '';
            if (isset($backgrounds[$item->get_id()])){
                $image = $backgrounds[$item->get_id()]->get_content();
                $first_el_key = key($image);
                if(isset($image[$first_el_key]) && $image[$first_el_key] != ""){
                    $image_path = $image[$first_el_key];
                }
            }
            
            $data['rows'][] = array(
                'id' => $item->get_id(),
                'name' => $item->get_name(),
                'image_path' => $image_path,
                'level' => $item->get_level(),
                'hidden' => $item->get_hidden(),
                'url'   => $this->view->url($urlParams,'cms',true)
            );
            
            if(count($record['pages']) > 0){
                $this->createMenuList($record['pages'], $backgrounds, $data);
            }
        }
        return $data;
    }


    public function menuEditAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        $langFilter = $this->_getParam('langFilter');        
        $menuFilter = $this->_getParam('menuFilter');        
        
        /* get routes from application.ini */
        $bootstrap = $this->getInvokeArg('bootstrap');
        $routes =  array_keys($bootstrap->getResource('router')->getRoutes());
        
        /* get all modules */
        $criteria['application_id'] = $this->_applicationId;
        $modules = Application_Model_ModuleMapper::getInstance()->fetchAll($criteria);
       
        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'menu')), $this->translate('Action canceled'));
        }        
       
        //create form object
        $form = new Bgchanger_Form_MenuItemBackground($data);
        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {                
                $values = $form->getValues();
                                
                //create entity object from submitted values, and save
                $item = new Bgchanger_Model_MenuItemBackground($values);
               
            // TRY TO FIND EXISTING BACKGROUND SETUP
                $menus = Bgchanger_Model_MenuItemBackgroundMapper::getInstance()->fetchAll(array(
                    'cms_menu_item_id' => $id
                ));
                
                if ($menus && count($menus) > 0)
                    $item = $menus[0];
                
                $item->set_cms_menu_item_id($id);
                
                
                if (!$data['content']) 
                    $data['content'] = '';
                
                $item->set_content($data['content']);
                //new entity
                /*if(!isset ($data['id']) || $data['id'] <= 0){
                    $item->set_application_id($this->_applicationId);
                } else {                    
                    $existingMenu = new Bgchanger_Model_MenuItemBackground();
                    
                    if(!Cms_Model_MenuItemMapper::getInstance()->find($data['id'], $existingMenu)){
                        throw new Exception("Menu not found");
                    }
                    
                    if((int)$existingMenu->get_application_id() != $this->_applicationId){
                        throw new Exception("Cannot edit this Menu Item.");
                    }                    
                }*/
                Bgchanger_Model_MenuItemBackgroundMapper::getInstance()->save($item);                
                
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'menu')), $this->translate('Menu Item saved.'));
            } else {
                //we have errors - return json or continue
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        //first run of the form - grab data from mapper
        elseif(!$this->_formHelper->getRequest()->isPost()) {
            //edit action
            if(isset ($id) && $id > 0) {
                $item = new Bgchanger_Model_MenuItemBackground();
                
                $menus = Bgchanger_Model_MenuItemBackgroundMapper::getInstance()->fetchAll(array(
                    'cms_menu_item_id' => $id
                ));
                
                if ($menus && count($menus) > 0)
                    $item = $menus[0];
                
                //fetch data
                $item->set_cms_menu_item_id($id);
                $data = $item->toArray();
                //populate form with data
                $form->setData($data);
            }
        }
        $this->view->menus = Cms_Model_MenuMapper::getInstance()->getMenus();
        $this->view->menuFilter = $menuFilter;
        $this->view->langFilter = $langFilter;
        $this->view->routes = $routes;
        $this->view->modules = $modules;
        $page = new Cms_Model_Page();
        if(isset($data['page_id']))
            Cms_Model_PageMapper::getInstance()->find($data['page_id'], $page);
        $this->view->page_title = $page->get_title();
        $this->view->data = $data;
    }
    
    public function editMainMenuAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        //create form object
        $form = new Cms_Form_Menu($data);
        if($form->isValid()) {                
            $values = $form->getValues();
            $manu = new Cms_Model_Menu($values); 
            Cms_Model_MenuMapper::getInstance()->save($manu); 
        }else{
            //we have errors - return json or continue
            $this->_formHelper->returnError($form->getMessages());
        }
        
        $data = array('menu' => Cms_Model_MenuMapper::getInstance()->getMenus(),
                'success'   => true,
                'message'   => $this->translate("New Menu added"),
                'selected' =>$manu->get_code());
       
        $this->_helper->json->sendJson($data);
    }
    
    protected function _findImageCodes($width, $height, $settings){
        $result = array();
        foreach ($settings as $imageCode => $imageData) {
            $imageOptions = $imageData['options'];
            if(     $width >= $imageOptions['minwidth'] && $width <= $imageOptions['maxwidth'] &&
                    $height >= $imageOptions['minheight'] && $height <= $imageOptions['maxheight']){
                $result[] = $imageCode;
            }
        }
        return $result;
    }
    
    public function unzipAction(){
        $zipFile = $this->_getParam('zip_file');

        //get teaser box
        $settings = Bgchanger_Model_MenuItemBackgroundMapper::getInstance()->getSettings();

        if(!isset($zipFile) || !isset($settings) || !isset($settings['bgchanger']['params']['images'])){
            return $this->_helper->json(array(
                'success'   => false,
                'message'   => 'Invalid parameters provided'
            ));            
        }
        //init file server
        Zend_Registry::set("fileserver_helper", $this->_fileHelper);
        $fileServer = new Admin_Model_FileServer();
        //unzip package
        try {
            $fileServer->unzip($zipFile, true);
        } catch (Exception $exc) {
            return $this->_helper->json(array(
                'success'   => false,
                'message'   => $exc->getMessage()
            ));
        }
        $result = array();
        //list images
        $files = array();
        $dir = pathinfo($zipFile, PATHINFO_DIRNAME) . '/' . pathinfo($zipFile, PATHINFO_FILENAME);
        $realDir = $fileServer->getRealPath($dir);
        $fileHandle = opendir($realDir);
        
        if ($result === FALSE) {
            return $this->_helper->json(array(
                'success'   => false,
                'message'   => 'Directory not created'
            ));
        }
        clearstatcache();
        while (false !== ($file = readdir($fileHandle))) {
            if($file == '.' || $file == '..' || $file == '.DS_Store') {
                continue;
            }
            $path = $realDir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                $files[] = $path;
            }
        }
        closedir($fileHandle);
        //find defined box image keys
        foreach ($files as $imagePath) {
            $imageInfo = getimagesize($imagePath);
            if(is_array($imageInfo) && count($imageInfo) >= 2){
                $result[$dir . '/' . basename($imagePath)] = $this->_findImageCodes($imageInfo[0], $imageInfo[1], $settings['bgchanger']['params']['images']);
            }
        }
        
        return $this->_helper->json(array(
            'success'   => true,
            'message'   => 'Unzipped',
            'images'    => $result
        ));
        
    }
}