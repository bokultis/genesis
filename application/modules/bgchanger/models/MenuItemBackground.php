<?php
/**
 * MenuItemBackground Entity class
 *
 * @package Bgchanger
 * @subpackage Models
 * @copyright Horisen
 * @author dzonz
 */
class Bgchanger_Model_MenuItemBackground extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;
    protected $_cms_menu_item_id;
    protected $_content;
    
    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_cms_menu_item_id() {
        return $this->_cms_menu_item_id;
    }

    public function set_cms_menu_item_id($_cms_menu_item_id) {
        $this->_cms_menu_item_id = $_cms_menu_item_id;
        return $this;
    }
    
    public function get_content($key = null) {
        if(!isset ($key)){
            return $this->_content;
        }
        else{
            if(isset ($this->_content[$key])){
                return $this->_content[$key];
            }
            else{
                return null;
            }
        }
    }

    public function set_content($_content) {
        $this->_content = $_content;
        return $this;
    }
}