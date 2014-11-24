<?php
/**
 * Subscribe form
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_Form_Subscribe extends Contact_Form_Base
{        
    public static function getFields(){
        return array(
            'first_name'    => array(
                'name'      => 'First name',
                'filters'   => array(new HCMS_Filter_Capitalize(), new Zend_Filter_StringTrim(), new Zend_Filter_StripTags())
            ),
            'last_name'    => array(
                'name'      => 'Last name',
                'filters'   => array(new HCMS_Filter_Capitalize(), new Zend_Filter_StringTrim(), new Zend_Filter_StripTags())
            ),            
            'email'         => array(
                'name'          => 'Email',
                'filters'   => array(new Zend_Filter_StringTrim(),new Zend_Filter_StripTags()),
                'validators'    => array(
                    new HCMS_Validate_EmailAddress(
                        array(
                        'allow' => Zend_Validate_Hostname::ALLOW_DNS,
                        'mx'    => true,
                        'deep'  => true
                        )
                    )                    
                ),
                'messsages'     => array(
                    'Please specify valid Email address'
                )
            ),
            'subscribe'        => array(
                'name'      => 'Subscribe'
            )
        );
    }        
   
    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null, array $settings = array(), Zend_Controller_Request_Abstract $request = null) {                       
        $this->_addFields($settings, self::getFields());                        
        $this->addAntispam();
        parent::__construct($this->filterRules,$this->validatorRules, $data, $options);
    }
}