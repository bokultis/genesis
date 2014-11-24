<?php
/**
 * Contact form
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_Form_Contact extends Contact_Form_Base
{        
    public static function getFields(){
        return array(
            'gender'        => array(
                'name'      => 'Gender',
                'unlimited' => true
             ),
            'first_name'    => array(
                'name'      => 'First Name',
                'filters'   => array(new HCMS_Filter_Capitalize(), new Zend_Filter_StringTrim(), new Zend_Filter_StripTags())
            ),
            'last_name'     => array(
                'name'      => 'Last Name',
                'filters'   => array(new HCMS_Filter_Capitalize(), new Zend_Filter_StringTrim(), new Zend_Filter_StripTags())
            ),
            'subject'       => array(
                'name'      => 'Subject',
                'filters'   => array(new HCMS_Filter_Capitalize(), new Zend_Filter_StringTrim(), new Zend_Filter_StripTags())
            ),
            'company'       => array(
                'name'      => 'Company',
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
            'street'        => array(
                'name'      => 'Street',
                'filters'   => array(new Zend_Filter_StringTrim(),new Zend_Filter_StripTags())
            ),
            'zip'           => array(
                'name'          => 'ZIP',
                'validators'    => array(
                    new Zend_Validate_GreaterThan(0),
                    new Zend_Validate_Int()
                ),
                'messsages'     => array(
                    'Zip code must be greater then 0.',
                    'Zip code must be integer number.'
                )
            ),
            'city'          => array(
                'name'      => 'City',
                'filters'   => array(new Zend_Filter_StringTrim(),new Zend_Filter_StripTags())
            ),
            'country'       => array(
                'name'      => 'Country'
            ),
            'phone'         => array(
                'name'          => 'Phone',
                'validators'    => array(
                    new HCMS_Validate_Telephone(array("charlist"=>array(" ","/","-")))
                ),
                'messsages'     => array(
                    'Please specify valid Phone number.'
                )                
            ),
            'mobile'        => array(
                'name'          => 'Mobile',
                'validators'    => array(
                    new HCMS_Validate_Telephone(array("charlist"=>array(" ","/","-")))
                ),
                'messsages'     => array(
                    'Please specify valid Mobile number.'
                )                
            ),
            'fax'           => array(
                'name'          => 'Fax',
                'validators'    => array(
                    new HCMS_Validate_Telephone(array("charlist"=>array(" ","/","-")))
                ),
                'messsages'     => array(
                    'Please specify valid Fax number.'
                )                
            ),
            'description'   => array(
                'name'      => 'Description',
                'filters'   => array(new Zend_Filter_StringTrim(),new Zend_Filter_StripTags())
            ),
            'message'   => array(
                'name'      => 'Message',
                'filters'   => array(new Zend_Filter_StringTrim(),new Zend_Filter_StripTags())
            ),            
            'interest'      => array(
                'name'      => 'Interest'
            )
        );
    }        
   
    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null, array $settings = array(), array $captchaSettings = array(), Zend_Controller_Request_Abstract $request = null) {
        //$this->_captcha = $this->_createCaptcha($captchaSettings);               
        
        $this->_addFields($settings, self::getFields());      
                
        /*$this->validatorRules['captcha'] = array(
            'presence' => 'required',
            new HCMS_Validate_Captcha($this->_captcha,$request->getParams())
        );*/
        
        parent::__construct($this->filterRules,$this->validatorRules, $data, $options);
    }
}