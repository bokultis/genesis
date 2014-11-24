<?php
/**
 * Base contact Controller
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_BaseController extends HCMS_Controller_Action_Cms {

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
    protected $_paramsKey = null;
    
    protected $_emailParams = array();
    protected $_globalSettings = array();
    
    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;       
    
    protected function _loadEmailParams(){
        if(!isset($this->_paramsKey)){
            return false;
        }        
        $this->_globalSettings = HCMS_Utils::loadThemeConfig('config.php', 'contact');
        if(isset($this->_globalSettings['email'][$this->_paramsKey])){            
            $this->_emailParams = $this->_globalSettings['email'][$this->_paramsKey];
        }
    }
    

    public function init(){
        $this->_formHelper = $this->getHelper('ajaxForm');
        $this->_module = new Application_Model_Module();
        if(!Application_Model_ModuleMapper::getInstance()->findByCode($this->getRequest()->getModuleName(), $this->_module)){
            throw new Exception("Contact module not installed");
        }
        $this->_loadEmailParams();
        parent::init();
    }
        
    protected function sendContactEmail($formValues, array $fields, $language, $emailParams){
        $values = array();
        $app = $this->_application;
        $values['application'] = $app;
        $values['data'] = $formValues;
        $values['fields'] = $fields;
        
        //Zend_Debug::dump($values);die();
        
        $emailSettings = $this->_emailSettings;

        $transport = HCMS_Email_TransportFactory::createFactory($emailParams);
        
        //init view        
        $emailView = new Zend_View();
        $emailView->setScriptPath($this->getFrontController()->getModuleDirectory('contact') . '/views/scripts/email_templates/');
        $mvcView = clone Zend_Layout::getMvcInstance()->getView();
        if(isset($mvcView->theme)){
            $emailView->addScriptPath(APPLICATION_PATH . '/../themes/' . $mvcView->theme . '/views/contact/email_templates/');
        }       
        $emailView->assign($values);
        $body = $emailView->render(sprintf($emailSettings['template_admin'],$language));
        
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($body);
        $mail->setFrom($emailParams['from_email'], $emailParams['from_name']);
        
        foreach ($emailParams['to_emails'] as $toEmail) {
            $mail->addTo($toEmail['email'],$toEmail['name']);
        }
        $mail->setSubject($emailSettings['subject_admin']);
        $mail->send($transport);
        
        if($emailParams['confirmation_email'] == 'yes'){                
            $mail   ->clearRecipients()
                    ->clearSubject()
                    ->addTo($formValues['email'])
                    ->setBodyHtml($emailView->render(sprintf($emailSettings['template_respond'],$language)))
                    ->setSubject($this->translate($emailSettings['subject_respond']));
            
            if(isset ($emailParams['reply_email'])){
                $mail->setReplyTo($emailParams['reply_email']);
            }
            
            $mail->send($transport);
        }

    }

    public function landingAction(){
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
        $form = new Contact_Form_Contact($this->getRequest()->getParam("data"), null, $this->_emailParams, $this->_globalSettings, $this->getRequest());
        $captcha = $form->getCaptcha();
        $data = array('id'  => $captcha->generate(), 'html' => $captcha->render());
        $this->_helper->json->sendJson($data);
    }


}