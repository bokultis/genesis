<?php
/**
 * ConfigParameters add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author zeka
 */
class Contact_Form_ConfigParameters extends HCMS_Form_Simple
{
    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     * @param string $transport
     */
    public function __construct(array $data = null, array $options = null, $transport = null) {
        $filterRules = array();
        if($transport == 'smtp'){
            $validatorRules = array(
                'server'  => array(
                    'presence'      => 'required',
                    'allowEmpty'    => false,
                    'messages'      => array(
                        0 => 'Please specify server.'
                    )
                ),
                'auth'  => array(
                    'presence'      => 'required',
                    'allowEmpty'    => false,
                    'messages'      => array(
                        0 => 'Please specify auth.'
                    )
                ),
                'username'  => array(
                    'presence'      => 'required',
                    'allowEmpty'    => false,
                    'messages'      => array(
                        0 => 'Please specify username.'
                    )
                ),
                'password'  => array(
                    'presence'      => 'required',
                    'allowEmpty'    => false,
                    'messages'      => array(
                        0 => 'Please specify password.'
                    )
                ),
                'port'  => array(
                    'presence'      => 'required',
                    'allowEmpty'    => false,
                    'messages'      => array(
                        0 => 'Please specify port.'
                    )
                )
            );
        }else{
             $validatorRules = array();
        }
        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
