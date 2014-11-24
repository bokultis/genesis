<?php
/**
 * Contact base form
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_Form_Base extends HCMS_Form_Simple
{    
    /**
     *
     * @var Zend_Captcha_Base
     */
    protected $_captcha = null;
    
    protected $validatorRules = array();
    protected $filterRules = array();
            
    
    public static function getFields(){
        //implement this
        return array();
        /*return array(
            'gender'        => array(
                'name'      => 'Gender',
                'unlimited' => true
             ),
            'first_name'    => array(
                'name'      => 'First Name'
            ),
            'last_name'     => array(
                'name'      => 'Last Name'
            ),
            'email'         => array(
                'name'          => 'Email',
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
            )
        );*/
    }
        
    protected function _addField($settings, $fieldId, $fieldName){
        if(is_array($fieldName)){
            if(isset($fieldName['validators'])){
                $fieldValidators = $fieldName['validators'];
            }
            if(isset($fieldName['filters'])){
                $this->filterRules[$fieldId] = $fieldName['filters'];
            }            
            if(isset($fieldName['messages'])){
                $fieldMessages = $fieldName['messages'];
            }             
            if(isset($fieldName['name'])){
                $fieldName = $fieldName['name'];
            }
            else{
                return false;
            }          
        }
        //required
        if(isset($settings['show_' . $fieldId . '_form_field_required']) && $settings['show_' . $fieldId . '_form_field_required'] == 'yes'){
            $this->validatorRules[$fieldId]['presence'] = 'required';
            $this->validatorRules[$fieldId][] = new Zend_Validate_NotEmpty();
            $this->validatorRules[$fieldId]['messages'][] = sprintf('Please specify %s.', $fieldName) ;
        }else{
            $this->validatorRules[$fieldId]['allowEmpty'] = true;
        }
        //field count
        if(isset($settings['show_' . $fieldId . '_form_field_count']) && $settings['show_' . $fieldId . '_form_field_count'] == 'yes'){
            $count = isset($settings['show_' . $fieldId . '_form_field_count']) && $settings[$fieldId . '_form_field_count'] != 0 ? $settings[$fieldId . '_form_field_count']:1000;
            $this->validatorRules[$fieldId][] = new Zend_Validate_StringLength(array('max' => $count));
            $this->validatorRules[$fieldId]['messages'][] = sprintf('%s can not be longer than %d characters', $fieldName, $count);
        }
        //custom validators
        if(isset($fieldValidators)){
            $this->validatorRules[$fieldId] = $this->validatorRules[$fieldId] + $fieldValidators;
        }
        if(isset($fieldMessages)){
            $this->validatorRules[$fieldId]['messages'] = $this->validatorRules[$fieldId]['messages'] + $fieldMessages;
        }        
    }
    
    protected function _addFields($settings, array $fields){
        foreach ($fields as $fieldId => $fieldName) {
            $this->_addField($settings, $fieldId, $fieldName);
        }
    }
    
    protected function addAntispam($field = 'honeypot') {
        $this->validatorRules[$field] = array(
            'allowEmpty'  => true,
            new Zend_Validate_Callback(function($value){
                return $value == null;
            })
        );
    }


    /**
     * Get captcha object
     *
     * @param array $settings
     *
     * @return Zend_Captcha_Base
     */
    protected function _createCaptcha($settings){        
        if(isset ($settings['fontName'])){
            $fontName = $settings['fontName'];
        }
        else{
            $fontName = 'font4.ttf';
        }
        $defaultSettings = array(
            'name'          => 'captcha',
            'font'          => APPLICATION_PATH . '/fonts/' . $fontName,
            'wordLen'       => 3,
            'timeout'       => 300,
            'imgDir'        => './captcha/',
            'imgUrl'        => '/captcha/',
            'width'         => 150,
            'height'        => 40,
            'dotNoiseLevel' => 50,
            'lineNoiseLevel'=> 2
        );
        $settings = array_merge($defaultSettings, $settings);
        return new HCMS_Captcha_Image($settings);
    }

    /**
     * get captcha object
     *
     * @return Zend_Captcha_Base
     */
    public function getCaptcha(){
        return $this->_captcha;
    }
}