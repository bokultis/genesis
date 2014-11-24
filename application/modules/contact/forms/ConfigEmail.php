<?php
/**
 * ConfigEmail add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author Boris
 */
class Contact_Form_ConfigEmail extends HCMS_Form_Simple
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
            'from_email'  => array(
                'presence'      => 'required',
                new Zend_Validate_StringLength(array('max' => 100)),
                new HCMS_Validate_EmailAddress(
                    array(
                        'allow' => Zend_Validate_Hostname::ALLOW_DNS,
                        'mx'    => true,
                        'deep'  => true
                    )
                ),
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify from email.'
                )
            ),
            'from_name'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify from name.'
                )
            ),
            'reply_email'  => array(
                'presence'      => 'required',
                new Zend_Validate_StringLength(array('max' => 100)),
                new HCMS_Validate_EmailAddress(
                    array(
                        'allow' => Zend_Validate_Hostname::ALLOW_DNS,
                        'mx'    => true,
                        'deep'  => true
                    )
                ),
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify reply email.'
                )
            ),
            'subject_contact'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'NotEmpty',
                'messages'      =>'Please specify subject.'
            ), 
            'mailtext_respond_contact'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'NotEmpty',
                'messages'      =>'Please specify Mailtext.'
            ), 
            'landing_page_text'  => array(
                'presence'      => 'required',
                'allowEmpty'    => true,
                'messages'      =>'Please specify Mailtext.'
            ),    
            'transport'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify transport.'
                )
            ),
            'confirmation_email' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please check a configuration email option.'
                )
            ),             
            'default_country' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please check a Default country option.'
                )
            ),            
            'ip_country_detection' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please check a IP country option.'
                )
            )            
        );
        
        //country rules
        if($data['default_country'] != 'yes'){
            $validatorRules['default_country_on_top'] = array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please check a Default On Top country option.'
                )                
            );
            $validatorRules['selected_countries'] = array(
                'presence'      => 'required',
                'allowEmpty'    => true
            );
        }                        
        //custom fields
        $fields = Contact_Form_Contact::getFields();
        foreach ($fields as $fieldId => $field) {
            if(!isset($field['unlimited']) || !$field['unlimited']){
                $validatorRules[$fieldId . '_form_field_count']  = array(
                    'presence'      => 'required',
                    'allowEmpty'    => true,
                    'NotEmpty',
                    'messages'      =>'Please specify characters count.'
                ); 
                $validatorRules['show_' . $fieldId . '_form_field_count']  = array(
                    'presence'      => 'required',
                    'allowEmpty'    => false,

                );                
            } 
            $validatorRules['show_' .$fieldId . '_form_field_required']  = array(
                'presence'      => 'required',
                'allowEmpty'    => false                
            ); 
            $validatorRules['show_' .$fieldId . '_form_field']  = array(
                'presence'      => 'required',
                'allowEmpty'    => false                
            );
        }

        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
