<?php
/**
 * Subscribe Controller
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */

include_once 'BaseController.php';

class Contact_SubscribeController extends Contact_BaseController {    
    
    protected $_emailSettings = array(
        'subject_admin'     => 'New Subscription Form Submission',
        'subject_respond'   => 'Subscription confirmation',
        'template_admin'    => 'subscribe.phtml',
        'template_respond'  => '%s/subscribe_auto_respond.phtml'
    );
    
    //key from config.php
    protected $_paramsKey = 'subscribe';    
    
    public function indexAction(){
        //Zend_Debug::dump($this->_emailParams);die();
        $data = $this->getRequest()->getPost('data');
        //create form object
        $form = new Contact_Form_Subscribe($data,null,$this->_emailParams,$this->getRequest());

        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {
                $values = $form->getValues();
                $subscription = new Contact_Model_Subscription();
                //exist?
                if(!Contact_Model_SubscriptionMapper::getInstance()->findByEmail($values['email'], $subscription)){
                    //persist
                    $subscription->setOptions($values);
                    $subscription   ->set_code(md5(uniqid()))
                                        ->set_status('pending');
                    Contact_Model_SubscriptionMapper::getInstance()->save($subscription);                    
                }
                $data = $subscription->toArray();
                $data['new_status'] = ($values['subscribe'] == 'yes')? 'subscribed': 'unsubscribed';
                $data['link'] = $this->view->serverUrl() . $this->view->url(array(
                    'module'        => 'contact',
                    'controller'    => 'subscribe',
                    'action'        => 'status',
                    'id'            => $subscription->get_id(),
                    'code'          => $subscription->get_code(),
                    'new_status'  => $data['new_status'],
                    'lang'          => CURR_LANG
                ));
                //send email
                $this->sendContactEmail($data, Contact_Form_Subscribe::getFields(), CURR_LANG, $this->_emailParams);
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
    
    public function statusAction(){
        $id = $this->_getParam('id');
        $code = $this->_getParam('code');
        $status = $this->_getParam('new_status');
        
        //check input
        if(!isset($id)){
            throw new Zend_Exception('Subscription not found');
        }
        if(!isset($code)){
            throw new Zend_Exception('Subscription not found');
        } 
        if(!isset($status) || !in_array($status, array('subscribed','unsubscribed'))){
            throw new Zend_Exception('Subscription not found');
        }        
        
        $subscription = new Contact_Model_Subscription();
        if(!Contact_Model_SubscriptionMapper::getInstance()->findByCode($id, $code, $subscription)){
            throw new Zend_Exception('Subscription not found');
        }
        
        if($subscription->get_status() == $status){
            throw new Zend_Exception('Subscription already updated');
        }
        
        $subscription->set_status($status);
        $dt = date("Y-m-d H:i:s");
        if($status == 'subscribed'){
            $subscription->set_subscribed_dt($dt);
        }
        else{
            $subscription->set_unsubscribed_dt($dt);
        }
        Contact_Model_SubscriptionMapper::getInstance()->save($subscription);
        $this->view->subscription = $subscription;
    }    
}