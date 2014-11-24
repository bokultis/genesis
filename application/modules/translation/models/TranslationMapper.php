<?php
/**
 * Translation Model Mapper
 *
 * @package Translation
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Translation_Model_TranslationMapper
{
    /**
     * singleton instance
     *
     * @var Translation_Model_TranslationMapper
     */
    protected static $_instance;
    
    /**
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_dbTable = null;
    
    /**
     *
     * @var 
     */
    protected $_prefix = '';

    /**
     * Singleton method to create/return instance of class
     *
     * @return Translation_Model_TranslationMapper
     */
    public static function getInstance() {
        if (!isset(self::$_instance) || Zend_Registry::isRegistered('translation_db')) {
            self::$_instance = new Translation_Model_TranslationMapper();
        }
        
        return self::$_instance;
    }

    /**
     * private constructor
     */
    private function  __construct(){
        $this->_dbTable = new Translation_Model_DbTable_Translation();
    }


    /**
     * Get data rows
     *
     * @param int $page
     * @param int $rows
     * @param array $parameters
     * @return Jqgrid_Model_DomainModel_RecordSet $response
     */
    public function getData($page,$rowCount,$parameters, $languageId = array("menu_id", "type_id", "section_id"), $translationLang = array(), $export = false) {
        $response = new stdClass();
        $translationSelect = $this->_dbTable->select()
                        ->setIntegrityCheck(false);
        
         $translationSelect ->distinct()
                            ->from(array('t' => $this->_prefix.'translate'), array('id', 'key', 'value', 'language_id', 'section'))
                            ->joinLeft(array('tk' => $this->_prefix.'translate_key'), 'tk.key = t.key', array('menu_id', 'type_id'))
                            ->joinLeft(array('tm' => $this->_prefix.'translate_menu'), 'tm.id = tk.menu_id', array('menu_name'=>'name'))
                            ->joinLeft(array('tt' => $this->_prefix.'translate_type'), 'tt.id = tk.type_id', array('type_name'=>'name'))
                            ->group('t.key');
        Translation_Model_Util::prepareDataParams($parameters);
        //add where conditions
        if(isset ($parameters['where']) && is_array($parameters['where'])) {
//            print_r($parameters['where']);die;
            foreach ($parameters['where'] as $whereCond) {
                $where  = explode(" ", $whereCond['cond']);
                $whereLang = explode("_", $where[0]);
                if(count($whereLang) > 0 && (int)(substr($whereLang[0],1))){
                    $translationSelect->joinLeft(array('lt' => $this->_prefix.'translate'), "(lt.key = t.key AND lt.language_id = '".addslashes(substr($whereLang[0],1))."')", array("translation_value" => "value"));
                    if( $where[1] == "IS")
                        $translationSelect->where("lt.value IS NULL OR lt.value LIKE '!%' OR  lt.value = ''");
                    else
                        $translationSelect->where("`lt`.`value` ".$where[1] ." ? ", $whereCond['value']);
                }else{
                    $translationSelect->where($whereCond['cond'], $whereCond['value']);
                    if(isset($where[1]) && $where[1] != "<>" && $whereCond['value'] == 'NULL'){
                        $translationSelect->orWhere("$where[0] is NULL");
                    }
                    if(isset($where[1]) && $where[1] == "<>" && $whereCond['value'] != "NULL") {
                        $translationSelect->orWhere("$where[0] is NULL");
                    }
                    if(isset($where[1]) && $where[1] == "<>" && $whereCond['value'] == "NULL"){
                        $translationSelect->orWhere("$where[0] is not NULL");
                    }   
                }
                
            }
        }
        if(isset($parameters['sidx']) && isset($parameters['sord'])) {
            if(! in_array(strtolower($parameters['sord']),array('asc','desc'))) {
                $parameters['sord'] = 'asc';
            }
            $translationSelect->order(array($parameters['sidx'] . ' ' . $parameters['sord']));
        }
        if(!$export){
            /* @var $paginator Zend_Paginator */
            $paginator = $this->generatePaginator($translationSelect, $page, $rowCount, $response); //Zend_Paginator::factory($translationSelect);
        }else{
            $paginator = $translationSelect->query()->fetchAll();
        }   
       
        $i=0;
        if(count($translationLang)== 0)
            $translationLang = $this->getTranslateLanguage();
        $keys = array();
        foreach ($paginator as $row){
            $keys[] = $row['key'];
        }
        
        $translateValue = $this->getRowKey($keys);
        
        foreach ($paginator as $row){
            $response->rows[$i]['id']= htmlspecialchars($row['key']);
            $response->rows[$i]['cell'] =  $this->getRowTranslation($translateValue, $row['key'], $translationLang, $row, (in_array("menu_id",$languageId,true))?true:false, (in_array("type_id",$languageId,true))?true:false, (in_array("section_id",$languageId,true))?true:false, $export);
            $i++;
        }
        return $response;
    }
    
    private function getRowTranslation($translateValue, $key, $translationLang, $paginatorRow,  $displayMenu = true, $displayType = true, $displaySection = true, $export = false){
        $rowTranslation = array();
        $rowTrans = array();
        
        $i = 0;
        
        $rowTranslation[0] = $key;
        if(!$export){
            $i++;
            $rowTranslation[$i] = $key;  
        }
            
        if($displaySection){
            $i++;
            $rowTranslation[$i] = $paginatorRow['section'];
        }
        if($displayMenu){ 
            $i++;
            $rowTranslation[$i] = $paginatorRow['menu_name'];
        } 
        if($displayType){
            $i++;
            $rowTranslation[$i] = $paginatorRow['type_name'];
        }
        foreach ($translateValue  as $row) {
            if($key == $row["key"])
                $rowTrans[] = array("language_id" => $row['language_id'],"value"=>$row['value']);
        }
        foreach ($translationLang as $lang){
            $i++;
            foreach($rowTrans as $rowT){
                if($rowT['language_id'] == $lang['id'])
                    $rowTranslation[$i] = $rowT["value"];
            }
            if(!isset($rowTranslation[$i]))
                 $rowTranslation[$i] = "";
        }
        return $rowTranslation;
    }

    private function getRowKey($keys){
        if(count($keys) > 0){
            $select = $this->_dbTable->select()
                    ->setIntegrityCheck(false);
            $select ->from($this->_prefix.'translate')
                     ->where("`key` IN (?)", $keys);
            $resultSet =$select->query()->fetchAll();

            return $resultSet;
        }else{
            return array();
        }
            
    }
    /**
     * save data
     * @param string $key
     * @param int $language_id
     * @param string $val
     * @return boolean
     */
    public function save($key, $language_id, $val, $section  = "", $import = false, $newKey = "") {
        $data = array(
                'key'           => ($newKey == "")?$key:$newKey,
                'language_id'   => $language_id,
                'value'         => $val
        );
        
        
        $selectTranslate = $this->_dbTable->select()
                                ->where('`language_id` = ?', $language_id)
                                ->where('`key` = ?', $key);
        if($section != "" && !$import){
            $data['section'] = $section; 
        }
        
        if($section != "" && $import)
            $selectTranslate->where('`section` = ?', $section);
        
        $rowTranslate = $selectTranslate->query()->fetchObject(); // ->toArray()
        if(is_object($rowTranslate)){
            $res = $this->_dbTable->update($data, array('id = ?' => $rowTranslate->id));
            if(!$import)
                $this->cacheCleanTranslation();
            return ($res == 1);
        }else{
            $res = $this->_dbTable->insert($data);
            if(!$import)
                $this->cacheCleanTranslation();
            return ($res > 0);
        }
    }
    
    public function editKey($key, $val, $import = false){
        $data = array(
                'key'           => $val
        );
        $selectTranslate = $this->_dbTable->select()
                                ->setIntegrityCheck(false)
                                ->from('translate')
                                ->where('`key` = ?', $key);
        $rowTranslate = $this->_dbTable->getAdapter()->query($selectTranslate)->fetchObject();
        if(is_object($rowTranslate)){
            $res = $this->_dbTable->getAdapter()->update($this->_prefix."translate", $data, array('`key`= ?' => $key));
            $this->_dbTable->getAdapter()->update($this->_prefix."translate_key", $data, array('`key`= ?' => $key));
            if(!$import)
                $this->cacheCleanTranslation();
            return ($res > 1);
        }else{
            $res = $this->_dbTable->getAdapter()->insert($this->_prefix."translate", $data);
            return ($res > 0);
        }
    }
    
    public function editSection($key, $val, $import = false){
        $data = array(
                'section'  => $val
        );
        $selectTranslate = $this->_dbTable->select()
                                ->setIntegrityCheck(false)
                                ->from($this->_prefix.'translate')
                                ->where('`key` = ?', $key);
        $rowTranslate = $this->_dbTable->getAdapter()->query($selectTranslate)->fetchObject();
        if(is_object($rowTranslate)){
            $res = $this->_dbTable->getAdapter()->update($this->_prefix."translate", $data, array('`key`= ?' => $key));
            if(!$import)
                $this->cacheCleanTranslation();
            return ($res > 1);
        }else{
            return false;
        }
    }
    /**
     * Save translation key (menu_id or type_id)
     * @param string $key
     * @param string $val
     * @param string $field
     * @return boolean 
     * 
     */
    public function saveTranslationKey($key, $val, $field, $import = false, $newKey = ""){
        $data = array(
                'key'           => ($newKey == "")?$key:$newKey,
                $field          => ($val == "NULL")? null:$val
        );
        $selectKey = $this->_dbTable->select()
                        ->setIntegrityCheck(false)
                        ->from($this->_prefix.'translate_key')
                        ->where('`key` = ?', $key);
               
        $rowKey = $this->_dbTable->fetchRow($selectKey);
        if(is_object($rowKey)){
            $res = $this->_dbTable->getAdapter()->update($this->_prefix."translate_key", $data, array('id = ?' => $rowKey->id));
            if(!$import)
                $this->cacheCleanTranslation();
            return ($res == 1);
        }else{
            $res = $this->_dbTable->getAdapter()->insert($this->_prefix."translate_key",$data);
            if(!$import)
                $this->cacheCleanTranslation();
            return ($res > 0);
        }
    }

    /**
     * get Translate Language
     * @param int $id
     * @return void
     * 
     */
    public function getTranslateLanguage($id = null){
         $select = $this->_dbTable->select()
                        ->setIntegrityCheck(false)
                        ->from($this->_prefix.'translate_language');
         return $this->_dbTable->fetchAll($select)->toArray();
    }
    
    /**
     * Get translation menu
     * 
     * @return array 
     */
    public function getTranslationMenu(){
        $data = array();
        $menuSelect = $this->_dbTable->select()
                ->setIntegrityCheck(false)
                ->from($this->_prefix.'translate_menu'); 
        $result = $this->_dbTable->fetchAll($menuSelect)->toArray();
        $data["NULL"] = "";
        foreach ($result as $val){
            $data[$val['id']] = $val['name'];
        }
        return $data; 
    }
    
    /**
     * Get section 
     * 
     * @return array 
     */
    public function getTranslationSection(&$error = ""){
        $data = array();
        $sectionSelect = $this->_dbTable
                            ->select()
                            ->setIntegrityCheck(false)
                            ->distinct()
                            ->from(array('t'  => $this->_prefix.'translate'), 'section');
        try {
            $result = $this->_dbTable->fetchAll($sectionSelect);
            $data["NULL"] = "";
            foreach ($result as $val){
                $data[$val['section']] = $val['section'];
            }
            return $data;
        } catch (Exception $e) {
            $error = 'Error message: ' . $e->getMessage();
            return $data;
        }
    }
    /**
     * Get translation type
     * 
     * @return array 
     */
    public function getTranslationType(){
        $data = array();
        $typeSelect = $this->_dbTable->select()
                    ->setIntegrityCheck(false)
                    ->from($this->_prefix.'translate_type'); ; 
        $result = $this->_dbTable->fetchAll($typeSelect)->toArray();
        $data["NULL"] = "";
        foreach ($result as $val){
            $data[$val['id']] = $val['name'];
        }
        return $data; 
    }
    
    public function importTranslation($fileDestination, $errors){
        require_once 'PHPExcel/PHPExcel/IOFactory.php';
        $arrayTemplate= array("Key", "Firstname", "Secondname", "Birthday");
        $inputFileType = PHPExcel_IOFactory::identify($fileDestination);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($fileDestination);
        $total_sheets=$objPHPExcel->getSheetCount(); // here 4
        $allSheetName=$objPHPExcel->getSheetNames(); // array ([0]=>'student',[1]=>'teacher',[2]=>'school',[3]=>'college')
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
        $highestRow = $objWorksheet->getHighestRow(); // here 5
        $highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn)-1;  // here 5
        $arr_data = array();
        for ($row = 1; $row <= $highestRow; ++$row) {
            for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                $value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                if(is_array($arr_data) ) { $arr_data[$row-1][$col]=$value; }
            }
        }
        $first = true;
        $imported = array();
        $i = 1;
        $translateLanguage  = $this->getTranslateLanguage();
        foreach ($translateLanguage as $key => $value) {
            $langName[$value["code"]] = $value['id'];
        }
        $menuIds = $this->getTranslationMenu();
        foreach ($menuIds as $key => $value) {
            if($value != "")
                $menuName[$value] = $key;
        }
        $typeIds = $this->getTranslationType();
        foreach ($typeIds as $key => $value) {
            if($value != "")
                $typeName[$value] = $key;
        }
        foreach ($arr_data as $data) {
            if($first){
                $arrayTemplate  = $data;
                foreach ($data as $key => $val) {
                    $keys[$val] = $key;
                }
//                print_r($keys);die;
            }elseif(!$first){
                $importData = array();
                foreach ($arrayTemplate as $value) {
                    switch ($value) {
                        case "Key":
                            $importData["key"] = $data[$keys["Key"]];
                            break;
                        case "Menu":
                            if($data[$keys["Menu"]] != "" && !isset($menuName[$data[$keys["Menu"]]])){
                                $dataMenu = array();
                                $dataMenu["id"] = "";
                                $dataMenu["name"] =  $data[$keys["Menu"]];
                                $menu = new Translation_Model_Menu($dataMenu);
                                Translation_Model_MenuMapper::getInstance()->save($menu);
                                $importData["menu_id"] = $menu->get_id();
                                $menuName[$menu->get_name()] = $menu->get_id();
                            }else{
                                $importData["menu_id"] = ($data[$keys["Menu"]] != "" && isset($menuName[$data[$keys["Menu"]]]))?$menuName[$data[$keys["Menu"]]]:""; 
                            }
                            break;
                        case "Type":
                            if(($data[$keys["Type"]] != "" && !isset($typeName[$data[$keys["Type"]]]))){
                                $dataType = array();
                                $dataType["id"] =  "";
                                $dataType["name"] =  $data[$keys["Type"]];
                                $type = new Translation_Model_Type($dataType);
                                Translation_Model_TypeMapper::getInstance()->save($type);
                                $importData["type_id"] = $type->get_id();
                                $typeName[$type->get_name()]= $type->get_id();
                            }else{
                                $importData["type_id"] = ($data[$keys["Type"]] != "" && isset($typeName[$data[$keys["Type"]]]))?$typeName[$data[$keys["Type"]]]:"";
                            }
                            break;
                        case "Section":
                            $importData["section"] = $data[$keys["Section"]];
                            break;
                        default:
                            if(isset($langName[$value])){
                               $importData["lang"][$langName[$value]] = $data[$keys[$value]]; // get language_id from $data[$value]
                            }
                            break;
                    }
                }
                foreach ($importData["lang"] AS $language_id=>$val){
                    if($val != ""){
                        $this->save($importData["key"], $language_id, $val, $importData["section"], true);  
                    }
                }
                
                /* deprecated since we changed the index in unique_trans (language_id, section, key (100))  
                if(isset($importData["section"]) && $importData["section"] != "" ){
                    $this->editSection($importData["key"], $importData["section"], true);
                }
                */
                if(isset($importData["menu_id"]) && $importData["menu_id"] != ""){
                    $this->saveTranslationKey($importData["key"], $importData["menu_id"], "menu_id", true);
                }
                if(isset($importData["type_id"]) && $importData["type_id"] != ""){
                    $this->saveTranslationKey($importData["key"], $importData["type_id"], "type_id", true);
                }
                
            }
            $i++;
            $first = false;
        }
        $this->cacheCleanTranslation();
        return true; //$imported;
    }
    
    public function exportTranslation($languageId, $grid_params){
        //data array
        $translation = array();

        $fileName = "translation-".Zend_Date::now().".xls";
        /** PHPExcel */
        require_once 'PHPExcel/PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setCreator("Horisen")
                                     ->setLastModifiedBy("Horisen")
                                     ->setTitle("Office  XLS Test Document")
                                     ->setSubject("Office  XLS Test Document")
                                     ->setDescription("Test document for Office XLS, generated using PHP classes.")
                                     ->setKeywords("office 5 openxml php")
                                     ->setCategory("Test result file");

        $langName = array();
        $translateLanguage  = $this->getTranslateLanguage();
        foreach ($translateLanguage as $key => $value) {
            $langName[$value["id"]] = array("id"=>$value['id'],"code"=>$value['code'], "name"=>$value['name']);
        }
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        //set width of columns
        $columns = array('A','B','C','D','E','F','G', "H", "I");
        foreach ($columns as $value){
            $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setAutoSize(true);
        }
        //set height of first row
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
        
        //style of header
         $styleHeader = array(
            'font' => array(
                'bold' => true,
//                'color' => array(
//                'argb' => PHPExcel_Style_Color::COLOR_GREEN,
//                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ),
            ),           
        );
        //style of report body
        $styleBody = array(
            'font' => array(
                'bold' => false,
//                'color' => array(
//                'argb' => PHPExcel_Style_Color::COLOR_GREEN,
//                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                'startcolor' => array(
//                    'argb' => PHPExcel_Style_Color::COLOR_GREEN,
//                ),
            ),
        );

        //header of report
        $activeSheet  = $objPHPExcel->getActiveSheet();
        $activeSheet  ->setCellValue('A1', 'Key');
        $a = 0;
        $abc = array("B", "C", "D", "E", "F", "G", "H", "I");
        if(in_array("section_id",$languageId,true)){
            $activeSheet  ->setCellValue($abc[$a]."1", 'Section');
            $a++;
        }
        if(in_array("menu_id",$languageId,true)){
            $activeSheet  ->setCellValue($abc[$a]."1", 'Menu');
            $a++;
        }
        if(in_array("type_id",$languageId,true)){
            $activeSheet  ->setCellValue($abc[$a]."1", 'Type');
            $a++;
        }
        $translationLang =array();
        foreach ($languageId AS $lang){
            if(((int)$lang)){
                $translationLang[] = $langName[$lang];
                $activeSheet -> setCellValue($abc[$a]."1", $langName[$lang]['code']);
                $a++;
            }
        }
        
        //apply style to header
        $activeSheet->getStyle('A1:'.$abc[$a-1].'1')->applyFromArray($styleHeader);
        //data from db
        $i =2;
        
        //get data for specific $languageId
        $translation = $this->getAllTranslation($languageId, $translationLang, $grid_params);
        
        foreach ($translation as $transl) {
            $s = 1;
            $activeSheet ->setCellValueByColumnAndRow(0 , $i,  $transl[0]);
            foreach($languageId AS $lang){
                 $activeSheet ->setCellValueByColumnAndRow($s , $i,  $transl[$s]);
                 $s++;
            }
            //apply style to row
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$abc[$a-1].$i)->applyFromArray($styleBody);
            // increment counter
            $i++;          
        }
        //------------- Buffering ----------//
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Translation');

        // redirect output to client browser
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');   

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //
        //save to file
        //$objWriter->save($file);
        // output to browser
        $objWriter->save('php://output');
        return true;
    }
    /**
     * Get All translation for export
     * @param type $languageId
     * @param type $translationLang
     * @param type $parameters
     * @return type 
     * 
     */
    public function getAllTranslation($languageId , $translationLang, $parameters){
        if($parameters["_search"] == '1') $parameters["_search"] = ""; else $parameters["_search"]= "false";
        
        Translation_Model_Util::prepareDataParams($parameters);
        $data  = $this->getData(1, "", $parameters, $languageId, $translationLang, true);
        $translation =  array();
        foreach ($data->rows as $row){
            $translation[] = $row['cell'];
        }
        return $translation; 
    }
    /**
     * Delete data
     *
     * @param int $id
     * @return int|bool
     */
    public function delete($del_id){
//        $this->_dbTable->select()
//                ->setIntegrityCheck(false)
//                ->from($this->_prefix.'translate_menu'); 
        $this->_dbTable->getAdapter()->delete( $this->_prefix.'translate',array('`key` = ?' => $del_id));
        return $this->_dbTable->getAdapter()->delete( $this->_prefix.'translate_key',array('`key` = ?' => $del_id));
    }
    /**
     * Clean cache translation 
     * 
     */
    public function cacheCleanTranslation(){
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $cacheClearUrl = $request->getScheme() . '://' . $request->getHttpHost().'/'.CURR_LANG.'/default/lang-js/cache-clean-translation' ;
        if($cacheClearUrl != ""){
            $client = new Zend_Http_Client();
            $client ->setUri($cacheClearUrl)
                    ->setConfig(array(
                                    'maxredirects' => 2,
                                    'timeout'      => 30))
                    ->request('GET');
        }
    }
    
  
    /**
     * Get options
     *
     * @return array
     */
    public function getOptions($view) {
        
        $urlHelper = $view; //Zend_Controller_Action_HelperBroker::getExistingHelper('url');
        $colNames = array();
        $colModel = array();
        $colNames[0] = "Key";
        $colModel[0] = array('name'=>"key_id", 'index'=>"key_id", 'autowidth'=> true, 'editable'=>true, 'hidedlg'=>true, 'hidden'=>true, "editrules"=>array('edithidden'=>true));
        $colNames[1] = "Key";
        $colModel[1] = array('name'=>"t.key", 'index'=>"t.key", 'autowidth'=> true, 'editable'=>false);
        $colNames[2] = "Section";
        $colModel[2] = array('name'=>"t.section", 'index'=>"t.section", 'autowidth'=> true, 'editable'=>true, 'stype'=>'select'); //'hidden'=>true
        $colNames[3] = "Menu";
        $colModel[3] = array('name'=>"tk.menu_id", 'index'=>"tk.menu_id", 'autowidth'=> true, 'editable'=>true, "edittype"=>"select", 'stype'=>'select', "editoptions"=>array("value"=>$this->getTranslationMenu()));
        $colNames[4] = "Type";
        $colModel[4] = array('name'=>"tk.type_id", 'index'=>"tk.type_id", 'autowidth'=> true, 'editable'=>true, "edittype"=>"select", 'stype'=>'select', "editoptions"=>array("value"=>$this->getTranslationType())); 
        //{name:'ship',index:'ship', width:90, editable: true, edittype:"select", 
        //formatoptions: {disabled : false}, editoptions:{value:"FE:FedEx;IN:InTime;TN:TNT;AR:ARAMEX"}},
        
        $translationLang = $this->getTranslateLanguage();
        $i = 4 ;
        foreach($translationLang as $colName ){
            $i++;
            $colNames[$i] = $colName["name"];
            $colModel[$i]= array('name'=>"language_id[".$colName["id"]."]", 'index'=>$colName["id"]."_langugeId", "id"=>$colName["id"] , 'autowidth'=> true, 'sortable' => false, 'editable'=>true ,
                                    "edittype"=>'textarea', "search" => true, "searchoptions"=>array("sopt"=>array('eq','ne', 'cn', 'nu'))
                                   );
        };

        return array(
                'url'           => $urlHelper->url(array('module'=>'translation','controller'=>'admin','action'=>'list'),null,true),
                'editurl'       => $urlHelper->url(array('module'=>'translation','controller'=>'admin','action'=>'edit'),null,true),
                'cellurl'       => $urlHelper->url(array('module'=>'translation','controller'=>'admin','action'=>'save'),null,true),
                'datatype'      => "json",
                'colNames'      => $colNames,// array('Extension Id','Extension', 'First name', 'Last name', 'Address','Group Member'),
                'colModel'      => $colModel,
                'mtype'         => 'POST',
                'rowNum'        => 40,
                'rowList'       => array(20,40,100),
                'sortname'      => 't.key',
//                'multiselect'   => true,
//                'multiboxonly'  => true,
                'sortorder'     => 'asc',
                'viewrecords'   => true,
//                'width'         => 900,
                'autowidth'     => true,
                'shrinkToFit'   => true,
                'height'        => '100%',
                'caption'       => "Translation",
                'rownumbers'    => true,
                'toppager'      => true,
                'cellEdit'      => true,
                'cellsubmit'    => 'remote',
                'autoencode'    => true
        );
    }
    
    /**
     * Function will generate Zend_Paginator object based on current page and
     * number of items per page.
     *
     * @param Zend_Db_Table_Select $select Zend_Db_Table_Select select object.
     * @param int $page Current page.
     * @param int $rowCount Number of items per page.
     * @param Jqgrid_Model_DomainModel_RecordSet $response Jqgrid response object.
     * @return Zend_Paginator
     */
    private function generatePaginator(Zend_Db_Select $select, $page, $rowCount, stdClass $response){
        /* @var $paginator Zend_Paginator */
        $paginator = Zend_Paginator::factory($select);

        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage($rowCount);

        //current page.
        $response->page = $page;
        //total numbers of pages.
        $response->total = $paginator->count();
        //total number of records.
        $response->records = $paginator->getTotalItemCount();
        return $paginator;
    }

    
}

