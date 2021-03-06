<?php
/**
 * Generic contact Controller
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_GenericController extends HCMS_Controller_Action_Cms {

    /**
     *
     * @var Application_Model_Module
     */
    protected $_module;
    
    protected $_emailSettings = array(
        'subject_admin'     => 'New Contact Form Submission',
        'subject_respond'   => 'Thank you for your Submission',
        'template_admin'    => 'contact.phtml',
        'template_respond'  => '%s/contact_auto_respond.phtml'
    );
    protected $_formId = 'contact';
    
    protected $_formParams = array();
    protected $_fields = array();
    protected $_globalSettings = array();
    
    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;       
    
    protected function _loadParams(){      
        $this->_globalSettings = HCMS_Utils::loadThemeConfig('config.php', 'contact');
        $fieldTypes = HCMS_Utils::loadThemeConfig('types.php', 'contact');
        if($this->_request->getParam('form_id')){
            $this->_formId = $this->_request->getParam('form_id');
        }
        if(!isset($this->_globalSettings['forms'][$this->_formId])){            
            throw new Exception("Form not found");
        }
        $this->_formParams = $this->_globalSettings['forms'][$this->_formId];
        $this->_fields = Contact_Form_Generic::getFieldsArr($fieldTypes, $this->_formParams['fields']);
    }
    

    public function init(){
        $this->_formHelper = $this->getHelper('ajaxForm');
        $this->_module = new Application_Model_Module();
        if(!Application_Model_ModuleMapper::getInstance()->findByCode($this->getRequest()->getModuleName(), $this->_module)){
            throw new Exception("Contact module not installed");
        }
        $this->_loadParams();
        parent::init();
    }
    
    public function indexAction(){
        $data = $this->getRequest()->getPost('data');
        
        //print_r($this->_fields);
        //create form object
        $form = new Contact_Form_Generic($data,null,$this->_fields, $this->getRequest());

        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {
                $values = $form->getValues();
                //persist data
                if(isset($this->_formParams['db']['save']) && $this->_formParams['db']['save']){
                    $contact = new Contact_Model_Contact($values);
                    $contact    ->set_application_id($this->_applicationId)
                                ->set_language(CURR_LANG)
                                ->set_posted(HCMS_Utils_Time::timeTs2Mysql(time()));

                    Contact_Model_ContactMapper::getInstance()->save($contact);                    
                }
                //send email
                $this->sendContactEmail($values, $this->_fields, CURR_LANG);
                //sending done, return success
                return $this->redirectLanding();
            }
            else {
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        $this->view->data = $data;
        $this->view->fields = $this->_fields;
        $this->view->formParams = $this->_formParams;
        $this->view->formId = $this->_formId;
        if(isset($this->_formParams['template'])){
            $this->renderScript($this->_formParams['template']);
        }
    }
    

    protected function sendContactEmail($formValues, array $fields, $language){        
        $emailParams = array_merge(array(
                'subject_admin'     => 'New Contact Form Submission',
                'subject_respond'   => 'Thank you for your time',
                'template_admin'    => 'contact.phtml',
                'template_respond'  => '%s/contact_auto_respond.phtml'                
            ),
            $this->_globalSettings['email'], $this->_formParams['email']);

        $transport = HCMS_Email_TransportFactory::createFactory($emailParams['sender']);        
        //init view        
        $emailView = new Zend_View();
        $emailView->setScriptPath($this->getFrontController()->getModuleDirectory('contact') . '/views/scripts/email_templates/');
        $mvcView = clone Zend_Layout::getMvcInstance()->getView();
        if(isset($mvcView->theme)){
            $emailView->addScriptPath(APPLICATION_PATH . '/../themes/' . $mvcView->theme . '/views/contact/email_templates/');
        }       
        $emailView->assign(array(
            'application'   => $this->_application,
            'data'          => $formValues,
            'fields'        => $fields
        ));
        $body = $emailView->render(sprintf($emailParams['template_admin'],$language));
        
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($body);
        $mail->setFrom($emailParams['from_email'], $emailParams['from_name']);
        
        foreach ($emailParams['to_emails'] as $toEmail) {
            $mail->addTo($toEmail['email'],$toEmail['name']);
        }
        $mail->setSubject($emailParams['subject_admin']);
        $mail->send($transport);
        
        if($emailParams['confirmation_email'] == 'yes'){                
            $mail   ->clearRecipients()
                    ->clearSubject()
                    ->addTo($formValues['email'])
                    ->setBodyHtml($emailView->render(sprintf($emailParams['template_respond'],$language)))
                    ->setSubject($this->translate($emailParams['subject_respond']));
            
            if(isset ($emailParams['reply_email'])){
                $mail->setReplyTo($emailParams['reply_email']);
            }
            
            $mail->send($transport);
        }

    }

    public function landingAction(){
        if(isset($this->_formParams['template_landing'])){
            $this->renderScript($this->_formParams['template_landing']);
        }        
    }
    
    public function redirectLanding($message = 'Thank you'){
        return $this->_formHelper->returnSuccess($this->view->url(array(
            'module'        => 'contact',
            'controller'    => $this->_request->getControllerName(),
            'action'        => 'landing'
        )), $this->translate($message));
    }
    
    /*
     * Captcha Reload Action
     *
     */
    public function captchaReloadAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $captcha = new HCMS_Captcha_Image($this->_fields['captcha']['params']);       
        $data = array('id'  => $captcha->generate(), 'html' => $captcha->render());
        $this->_helper->json->sendJson($data);
    }


}