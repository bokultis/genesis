<?php

/**
 * Translation Admin controller
 *
 * @package Translation
 * @subpackage Controllers
 * @copyright Horisen
 * @author marko
 */
class Translation_AdminController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;

    public function init(){
        parent::init();
        $this->_formHelper = new HCMS_Controller_Action_Form($this->_request, $this->getResponse());//$this->getHelper('ajaxForm');
    }

    /**
     * index pages
     */
    public function indexAction() {
        $error = '';
        $option = Translation_Model_TranslationMapper::getInstance()->getOptions($this->view);
        HCMS_Utils::generateFormatedOptions(Translation_Model_TranslationMapper::getInstance()->getTranslationSection($error), $section, $sectionValue, false);
        $this->view->section = $sectionValue;
        HCMS_Utils::generateFormatedOptions(Translation_Model_TranslationMapper::getInstance()->getTranslationMenu(), $menu, $menuValue, false);
        $this->view->menu = $menuValue;
        HCMS_Utils::generateFormatedOptions(Translation_Model_TranslationMapper::getInstance()->getTranslationType(), $type, $typeValue, false);
        $this->view->type = $typeValue;
        $this->view->option = json_encode($option);
    }

   /**
     *  List ajax action
     */
    public function listAction() {
        $parameters = array();
        $parameters =  $this->_request->getParams();
//        $parameters['sidx']= $this->_request->getParam('sidx');
//        $parameters['sord']= $this->_request->getParam('sord');
        //paging
        $page = $this->_request->getParam('page');
        $perPage = $this->_request->getParam('rows');
        if(!isset ($page) || $page < 1){
            $page = 1;
        }
        if(!isset ($perPage) || $perPage < 1 || $perPage > 300){
            $perPage = 10;
        }
       
        $respons = Translation_Model_TranslationMapper::getInstance()->getData($page, $perPage, $parameters);
        $this->_helper->json->sendJson($respons);
        
    }
    
    /**
     *  Edit ajax action
     */
    public function editAction(){
       
        $parameters =$this->getRequest()->getPost();
        if($parameters['oper'] == 'del'){
            Translation_Model_TranslationMapper::getInstance()->delete($parameters['id']);
            echo  "null";die;
        }
        //we have custom form for add
        $language_id = $parameters['language_id'];
        $key = htmlspecialchars_decode($parameters['id']);
        if($key != "_empty" && $key != "" || $parameters["key_id"] != ""){
            $newKey =  htmlspecialchars_decode($parameters["key_id"]);
            $menu_id = $parameters['tk_menu_id'];
            $type_id = $parameters['tk_type_id'];
            $section = $parameters['t_section'];
            $section = $parameters['t_section'];
            $dataSaved = false;
            foreach ($language_id as $lang_id => $value) {
                $dataSaved = Translation_Model_TranslationMapper::getInstance()->save($key, $lang_id, htmlspecialchars_decode($value), $section, false, $newKey);
            }
            if($dataSaved)
                $dataSaved = Translation_Model_TranslationMapper::getInstance()->saveTranslationKey($key, $menu_id, "menu_id", false, $newKey);
            if(!$dataSaved)
                $dataSaved = Translation_Model_TranslationMapper::getInstance()->saveTranslationKey($key, $type_id, "type_id", false, $newKey);
        }else{
            $dataSaved = false; 
        }
//        if(!$dataSaved){
//            $errors = array("key_id" => array("isEmpty" => $this->view->translate("You must give a non-empty value for field Key ")));
//            $this->_formHelper->returnError( $errors);
//        }
        echo  "null";die;
    }
     /**
     * save data
     */
    public function saveAction(){
        $dataSaved = false;
        $key = $this->_getParam('key');
        $language_id = $this->_getParam('lang_id');
        $val = $this->_getParam('val');
        $section = $this->_getParam('section');
        switch ($language_id){
            case "tk.menu_id":
            case "tk.type_id":
                $field = explode(".", $language_id);
                $dataSaved = Translation_Model_TranslationMapper::getInstance()->saveTranslationKey($key, $val, $field[1]);
                break;
            case "t.key":
                $dataSaved = Translation_Model_TranslationMapper::getInstance()->editKey($key,$val);
                break;
            case "t.section":
                $dataSaved =  Translation_Model_TranslationMapper::getInstance()->editSection($key,$val);
                break;
            default :
                $language_id = explode("[", $language_id);
                $language_id = explode("]", $language_id[1]);
                $dataSaved = Translation_Model_TranslationMapper::getInstance()->save($key, $language_id[0], $val, $section); 
                break;    
        }
        
        if($dataSaved){
            $this->_helper->json->sendJson(array(
                'success'   => true,
                'message'   => $this->view->translate('Data Saved.')
            ));
        }else{
           $this->_helper->json->sendJson(array(
                        'status'   => true,
                        'message'   => $this->view->translate('Error in data entry!')
                    ));
            
        }
    }
    
    public function exportToExcelAction(){
        $data = $this->getRequest()->getPost('data');
        $data['grid_params']=json_decode($data["grid_params"], true); 
        
        $submit = $this->getRequest()->getPost('submit');
        $grid_params =json_decode(urldecode($this->_getParam('grid_params')),true);
        if ($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action cancelled.'));
        }
        if ($this->getRequest()->isPost()){
            if(isset($data["language_id"]) && count($data["language_id"]) > 0){
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'save-excel')), $this->translate('Translation successfully exported. Save the file.'), $data); //
            }
            else{
                $this->_formHelper->returnError( array("language_id"=>array("isEmpty"=> $this->translate("Select the column you want to export."))));
            }
        }else{
            $translateLanguages = Translation_Model_TranslationMapper::getInstance()->getTranslateLanguage();
            $export = array_merge(array(array("id"=>"section_id", "name"=>"Section"), array("id"=>"menu_id", "name"=>"Menu"), array("id"=>"type_id", "name"=>"Type")), $translateLanguages);
            $this->view->translateLanguages = $export;
            $this->view->grid_params = json_encode($grid_params);
        }
    }
    
    public function saveExcelAction(){
        $data = json_decode(urldecode($this->getRequest()->getParam('data')), true);
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        return Translation_Model_TranslationMapper::getInstance()->exportTranslation($data["language_id"], $data["grid_params"]);
    }
    
    public function importFromExcelAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if ($this->getRequest()->isPost()){
            try{
                $adapter = new Zend_File_Transfer_Adapter_Http();
                $adapter->addValidator("Count",false, array("min"=>1, "max"=>1))
                        ->addValidator("Size",false,array("max"=>1000000))
                        ->addValidator("Extension",false,array("extension"=>"xls", "case" => true));

                $adapter->setDestination(APPLICATION_PATH . "/../tmp/");

                $files = $adapter->getFileInfo();
                foreach($files as $fieldname=>$fileinfo){
                    if (($adapter->isUploaded($fileinfo["name"]))&& ($adapter->isValid($fileinfo['name']))){
                        $extension = substr($fileinfo['name'], strrpos($fileinfo['name'], '.') + 1);
                        $filename = 'file_'.date('Ymdhs').'.'.$extension;
                        $adapter->addFilter('Rename',array('target'=>APPLICATION_PATH . "/../tmp/".$filename,'overwrite'=>true));
                        $adapter->receive($fileinfo["name"]);
                    }
                }
                if(count($adapter->getMessages()) > 0 ){
                    return $this->returnAjaxResult(FALSE, $adapter->getMessages());
                }else{
                    $errors = array();
                    $files = $adapter->getFileInfo();
                    foreach ($files as $file){
                        Translation_Model_TranslationMapper::getInstance()->importTranslation($file['destination']."/".$file['name'], $errors);
                    }
                    if(count($errors)>0){
                        foreach ($errors as $error){
                            $message[]= $error["message"];
                        }
                        return $this->returnAjaxResult(FALSE, $message);
                    }else{
                        return $this->returnAjaxResult(TRUE, $this->translate('Translation successfully imported.'));
                    }
                };
            }
            catch (Exception $ex){
               return $this->returnAjaxResult(FALSE, $ex->getMessage());
            }
        }
        die();
    }
    
    private function returnAjaxResult($success,$message){
         echo json_encode(array(
            'success'   => $success,
            'message'   => $message
        ));
    }
}