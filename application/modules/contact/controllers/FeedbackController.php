<?php
/**
 * Index Controller
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */

include_once 'BaseController.php';

class Contact_FeedbackController extends Contact_BaseController {     
    
    protected $_emailSettings = array(
        'subject_admin'     => 'New Feedback Form Submission',
        'subject_respond'   => 'Thank you for your Feedback',
        'template_admin'    => 'feedback.phtml',
        'template_respond'  => '%s/feedback_auto_respond.phtml'
    );
    
    //key from config.php
    protected $_paramsKey = 'feedback';
    
    public function indexAction(){
        $data = $this->getRequest()->getPost('data');
        
        //create form object
        $form = new Contact_Form_Feedback($data,null,$this->_emailParams,$this->getRequest());

        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {
                $values = $form->getValues();
                //send email
                $this->sendContactEmail($values, Contact_Form_Feedback::getFields(), CURR_LANG, $this->_emailParams);
                //sending done, return success
                return $this->redirectLanding();
            }
            else {
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        $this->view->data = $data;        
        $this->view->contact_settings = $this->_emailParams;
    }
}