<?php
/**
 * User add/edit form
 *
 * @package Modules
 * @subpackage Auth
 * @copyright Horisen
 * @author marko
 */
class Auth_Form_User extends HCMS_Form_Simple
{
    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null) {
         $passwordOpts = array(
            'requireAlpha' => true,
            'requireNumeric' => true,
            'requireCapital' => false,
            'minPasswordLength' => 8);

        $filterRules = array();
        $validatorRules = array(
            'id'  => array(
                'allowEmpty'    => true
            ),
            'role_id'  => array(
                'presence'      => ($data['isAdminLogged']) ? 'required' : '',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify role.'
                )
            ),
            'username'  => array(
                'presence'      => ($data['isAdminLogged']) ? 'required' : '',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify username.'
                )
            ),
            'password'  => array(
                'presence'      => ((isset($data['password']) && $data['password'] != '') || empty($data['id']))?'required':"",
                'allowEmpty'    => ((isset($data['password']) && $data['password'] != '') || empty($data['id']))?false:true,                
                new HCMS_Validate_PasswordConfirmation($data),                
                new Auth_Form_Validator_PasswordStrong($passwordOpts),
                new Auth_Form_Validator_LastPasswords($data),
                new Auth_Form_Validator_SpecialCharacters(),
                new Auth_Form_Validator_Duplicate($data),
            ),
            'old_password'  => array(
                 'presence'      => (isset($data['old_password']) && $data['old_password'] != '')?'required':"",
            ),            
            'first_name'  => array(
                'presence'      => ($data['isAdminLogged']) ? 'required' : '',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify First Name .'
                )
            ),
            'last_name' => array(
                'presence'      => ($data['isAdminLogged']) ? 'required' : '',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Last Name .'
                )
            ),
            'email' => array(
                'presence'      => ($data['isAdminLogged']) ? 'required' : '',
                'allowEmpty'    => false,
                new Zend_Validate_EmailAddress,
                 new Zend_Validate_Db_NoRecordExists(
                                array(
                                    'adapter' => Zend_Registry::get('db'),
                                    'table' => 'auth_user',
                                    'field' => 'email',
                                    'exclude' => array(
                                                        'field' => 'id',
                                                        'value' => isset($data['id'])? $data['id'] : ""
                                                )
                                )
                            ),
                'messages'      => array(
                    0 => 'Please specify Email.'
                )
            ),
            'lang' => array(
                'presence'      => ($data['isAdminLogged']) ? 'required' : '',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Language.'
                )
            ),
            'status' => array(
                'presence'      => ($data['isAdminLogged']) ? 'required' : '',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Status.'
                )
            ),
            'image_path' => array(
                'allowEmpty'    => true
            )
        );
        
        if(!$data['isAdminLogged']){
             $validatorRules["password"][] = new Auth_Form_Validator_Oldpsw($data);
        }
        
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

