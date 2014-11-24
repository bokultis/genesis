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

class Contact_IndexController extends Contact_BaseController {
    
    //key from config.php
    protected $_paramsKey = 'contact';    
        
    protected $_interests = array(
            'company'   => 'Company',
            'product'   => 'Product',
            'retail'   => 'Retail',
            'communication'   => 'Communication',
            'promotions'   => 'Contests & Promotions',
            'other'   => 'Other'
    );   

    
    public function indexAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        
        //Zend_Debug::dump($this->_emailParams);die();
        //create form object
        $form = new Contact_Form_Contact($data,null,$this->_emailParams, $this->_globalSettings['captcha'], $this->getRequest());

        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {
                $values = $form->getValues();
                //persist data
                $contact = new Contact_Model_Contact($values);
                $contact    ->set_application_id($this->_applicationId)
                            ->set_language(CURR_LANG)
                            ->set_posted(HCMS_Utils_Time::timeTs2Mysql(time()));
                
                Contact_Model_ContactMapper::getInstance()->save($contact);
                //send email
                $this->sendContactEmail($values, Contact_Form_Contact::getFields(), CURR_LANG, $this->_emailParams);
                //sending done, return success
                return $this->redirectLanding();
            }
            else {
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        $this->view->data = $data;
        $this->view->emailParams = $this->_emailParams;
        
        //Captcha section 
        /*$captcha = $form->getCaptcha();
        $this->view->captchaId =$captcha->generate();
        $this->view->captcha = $captcha->render();*/

        //country assign        
        $countryHelper = $this->getHelper('country')->setSettings($this->_emailParams);
        $this->view->countries = $countryHelper->getList();
        //set default country
        $countryHelper->setDefault();
    }
}