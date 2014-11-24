<?php
/**
 * Menu Item add/edit form
 *
 * @package Modules
 * @subpackage Bgchanger
 * @copyright Horisen
 * @author marko
 */
class Bgchanger_Form_MenuItemBackground extends HCMS_Form_Simple
{
    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null) {
        $filterRules = array();
        $validatorRules = array(
            'id'  => array(
                'allowEmpty'    => true
            ),
            'cms_menu_item_id'  => array(
                'presence'      => 'required',
                new Zend_Validate_Digits(),
                'messages'      => array(
                    0 => 'Menu is not selected.'
                )
            ),
//            'image_path' => array(
//                'allowEmpty'    => true
//            )
            'content' => array(
                'allowEmpty'    => true
            )
            
        );
        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}