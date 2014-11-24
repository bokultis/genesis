<?php
/**
 * MenuItemBackground Mapper
 *
 * @package Bgchanger
 * @subpackage Models
 * @copyright Horisen
 * @author dzonz
 */
class Bgchanger_Model_MenuItemBackgroundMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Bgchanger_Model_MenuItemBackgroundMapper
     */
    protected static $_instance = null;

    protected static $_settings = null;
    
    public static $_translatedFields = array();
    /**
     *
     * @var Bgchanger_Model_DbTable_MenuItemBackground
     */
    protected $_dbTable;

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Bgchanger_Model_DbTable_MenuItemBackground();
    }

    /**
     * get instance
     *
     *
     * @return Bgchanger_Model_MenuItemBackgroundMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Get box configuration
     * @param string $boxCode
     * @return array|null
     */
    public function getSettings()
    {
        if(self::$_settings === null){
            //self::$_boxes = json_decode(file_get_contents($filePath), true);
            $settings = HCMS_Utils::loadThemeConfig('settings.php', 'bgchanger');
            foreach ($settings as $code => $setting) {
                if(isset($settings[$code]['params']['images_dims'])){
                    $section = isset($settings[$code]['params']['images_section'])? $settings[$code]['params']['images_section']: 'default';                    
                    $settings[$code]['params']['images'] = $this->getImagesParams($settings[$code]['params']['images_dims'], $section);                    
                }
            }            
            self::$_settings = $settings;
        }

        return self::$_settings;
    }   
    
    /**
    * Get image params
    * 
    * @param array $dims
    * @param boolean $independent960
    * @return array
    */
    public function getImagesParams($dims, $section = 'default'){
        $imagesConf = HCMS_Utils::loadThemeConfig('picture.php');
        $variations = $imagesConf[$section];

        $result = array();
        $i = 0;
        foreach ($variations as $query => $suffix) {
            $width = $dims[$i * 2];
            $height = $dims[$i * 2 + 1];
            $elements = explode('_', $suffix);
            $vp = $elements[0];
            $density = (count($elements) >= 2)? $elements[1]: 1;
            $result['img_' . $suffix] = array(
                "name" => "Viewport width $vp and density $density",
                "media_query" => $query,
                "options" => array(
                    "minwidth" => $width,
                    "maxwidth" => $width,
                    "minheight" => $height,
                    "maxheight" => $height
                )
            );
            $i++;
        }

        return $result;

    }   
    

    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Bgchanger_Model_MenuItemBackground $item
     * @return boolean
     */
    public function find($id, Bgchanger_Model_MenuItemBackground $item) {
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('mibg'=>'cms_bgchanger'),array('mibg.*'))
                ->where("mibg.id = ?", $id);
        
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $item->setOptions($row->toArray());
        return true;
    }

    /**
     * Find all items
     * @param array $criteria
     *
     * @return array
     */
    public function fetchAll($criteria = array(), $order = array(), $indexField = false) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
            ->from(array('mi'   => 'cms_menu_item'),    array())
            ->join(array('mibg' => 'cms_bgchanger'), "mi.id = mibg.cms_menu_item_id",  array('mibg.*'))
            ->joinLeft(array('m' => 'cms_menu'), "m.code = mi.menu",  array('menu_name' => 'name'));
        
        if(isset ($criteria['application_id'])){
            $select->where('mi.application_id = ?', $criteria['application_id']);
        }
        
        if(isset ($criteria['cms_menu_item_id'])){
            $select->where('mi.id = ?', $criteria['cms_menu_item_id']);
        }
        
        if(isset ($criteria['menu'])){
            $select->where('m.code = ?', $criteria['menu']);
        }
        
        if(is_array($order) && count($order) > 0 ){
            $select->order($order);
        }

        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        
        $items   = array();
        foreach ($resultSet as $row) {
            $item = new Bgchanger_Model_MenuItemBackground();
            
            $arr = $row->toArray();
            //$item->setOptions($arr);
            $this->_rowToEntity($arr, $item);
            if ($indexField) {
                $items[$arr[$indexField]] = $item;
            } else
                $items[] = $item;
        }
        return $items;
    }
    
     /**
     * Save entity
     *
     * @param Bgchanger_Model_MenuItemBackground $item
     */
    public function save(Bgchanger_Model_MenuItemBackground $item) {
        $data = array();
        $this->_entityToRow($item, $data);
        
//        if (null != $item->get_id()) {
//            $data['id'] = $item->get_id();
//        }
//        
//        if(null != $item->get_cms_menu_item_id()){
//            $data['cms_menu_item_id'] = $item->get_cms_menu_item_id();
//        }
//       
//        if(null != $item->get_image_path()){
//            $data['image_path'] = $item->get_image_path();
//        } else {
//            $data['image_path'] = '';
//        }
        $id = $item->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $item->set_id($id);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
        }
    }
    
    public function _rowToEntity(array $row, Bgchanger_Model_MenuItemBackground $item){
        $row = $this->_makeTranslationData($row, self::$_translatedFields, array('content'));
        $item->setOptions($row);
    }

    public function _entityToRow(Bgchanger_Model_MenuItemBackground $item,array &$row){
        $this->_populateDataArr($row, $item, array('id','cms_menu_item_id','content'),array('content'));
    }
    
}