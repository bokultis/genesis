<?php
/**
 * ConfigToEmailsWrapper add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author zeka
 */
class Contact_Form_ConfigToEmailsWrapper
{
    private $_forms;

    private $_values;
    private $_messages;

    public function __construct($data) {
        $this->_forms = array();
        $this->_values = array();
        $this->_messages = array();
        $this->init($data);
    }
    
    protected function init($data){
        if( $data == null || empty($data) ){
            $configToEmails = new Contact_Form_ConfigToEmails(array());
            $this->values[] = $configToEmails->getValues();
            $this->messages[] = $configToEmails->getMessages();
            $this->_forms[] = $configToEmails;

        }
        else{
            foreach($data as $key => $value){
                 $configToEmails = new Contact_Form_ConfigToEmails($value);
                 $this->_values[$key] = $configToEmails->getValues();
                 $this->_messages[$key] = $configToEmails->getMessages();
                 $this->_forms[$key] = $configToEmails;
            }
        }
        
    }

    public function isValid(){
        $valid = true;
        foreach($this->_forms as $key => $value){
            $valid = $valid && $value->isValid();
        }
        return $valid;
    }


    public function getValues(){
        return $this->_values;
    }


    public function getMessages(){
        return $this->_messages;
    }

}
