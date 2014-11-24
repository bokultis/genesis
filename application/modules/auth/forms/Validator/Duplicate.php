<?php
/**
 * Date validator
 *
 * @package Modules
 * @subpackage Consumer
 * @copyright Horisen
 * @author bane
 */
class Auth_Form_Validator_Duplicate extends Zend_Validate_Abstract
{
    const DATE_INVALID = 'INV_REL_INT';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::DATE_INVALID => 'Enter new one password.'
    );


    /**
     * data
     *
     * @var mixed
     */
    protected $_data;

    /**
     * Sets validator options
     *
     * @param  array $data
     * @param string $lang
     * @return void
     */
    public function __construct($data){
        $this->_data = $data;        
    }

    public function isValid($value, $context = null){
        $result = true;
        
        if ($value == $this->_data["old_password"]) {
            $this->_error(self::DATE_INVALID);
            $result = false;
        } else {
            $result = true;
        }
        
        return $result;
    }
}