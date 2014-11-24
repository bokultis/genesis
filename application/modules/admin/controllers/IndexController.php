<?php

/**
 * Admin Index Controller
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author milan
 */
class Admin_IndexController extends HCMS_Controller_Action_Admin {

    public function init() {
        //if login action do not check
        if ($this->getRequest()->getActionName() == 'login') {
            $this->_checkAuth = false;
        }
        parent::init();
    }

    protected function _updateAttemp(Auth_Model_User $user, $count){
        $date = new Zend_Date();
        $user->set_attempt_login_dt($date->toString('yyyy-MM-dd HH:mm:ss'));
        $user->set_attempt_login($count);
        //update user but not set new password
        $user->set_password("");
        Auth_Model_UserMapper::getInstance()->getInstance()->save($user);        
    }
    
    protected function _setLoginError($errorMessage = 'Wrong username or password'){
        $this->view->username = $this->_getParam('username');
        $this->view->messages = $this->translate($errorMessage);        
    }

    /**
     * Login action
     */
    public function loginAction() {
        $this->view->layout()->setLayout('login');        
        
        $total_number_attempt = $this->_getBootstrapOption('total_number_attempt', 'default', 3);
        $lock_login_time = $this->_getBootstrapOption('total_number_attempt', 'lock_login_time', 3600);
        $expire_password = 3600 * 24 * $this->_getBootstrapOption('total_number_attempt', 'expire_password_day', 90);

        $user = new Auth_Model_User();
        $isExistUser = Auth_Model_UserMapper::getInstance()->getInstance()->findByUsername($this->_getParam('username', ""), $user);
        
        if(!$isExistUser){
            return $this->_setLoginError();
        }
        
        $aclLoader = HCMS_Acl_Loader::getInstance();

        //check permission
        $isMaster = $aclLoader->getAcl()->isAllowed($aclLoader->getRoleCode($user->get_role_id()), "admin", "master");
        
        //password expired for non master
        if(!$isMaster && strtotime($user->get_changed_password_dt()) + $expire_password > strtotime("now")){
            return $this->_setLoginError();
        }
        
        //check attemp count
        if ($user->get_attempt_login() >= $total_number_attempt) {
            //unlock attempts
            if (strtotime($user->get_attempt_login_dt()) + $lock_login_time < strtotime("now")) {
                $this->_updateAttemp($user, 0);
            }
            return $this->_setLoginError();            
        }
        //login
        if ($this->getRequest()->isPost()) {
            $adapter = new Admin_Model_Auth_Adapter($this->_applicationId, $this->_getParam('username'), $this->_getParam('password'));
            $result = Zend_Auth::getInstance()->authenticate($adapter);
            if ($result->isValid()) {
                //updated logged time
                Auth_Model_UserMapper::getInstance()->getInstance()->updateUserLogged($result->getIdentity());
                $this->_updateAttemp($user, 0);                            

                Zend_Session::regenerateId();
                $defaultUrl = $this->view->url(array('module' => 'admin', 'controller' => 'index', 'action' => 'index'), 'default', true);
                return $this->returnThere($defaultUrl);
            } else {
                $this->_updateAttemp($user, $user->get_attempt_login() + 1);
                return $this->_setLoginError(implode(' ', $result->getMessages()));
            }
        }
    }

    /**
     * Logout action
     */
    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        //redirect to landing page
        $this->_redirect($this->view->url(array('controller' => 'index', 'action' => 'login')));
    }

    public function filemanagerAction() {
        
    }

    public function widgetAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('layout')->disableLayout();
        }

        $this->view->hostingQuota = $this->_fileHelper->getQuota();
        $this->view->hostingFreeSpace = $this->_fileHelper->getFreeSpace();
        $this->view->hostingUsed = $this->view->hostingQuota - $this->view->hostingFreeSpace;
        $this->view->hostingQuotaStr = HCMS_Utils::formatBytes($this->view->hostingQuota);
        $this->view->hostingFreeSpaceStr = HCMS_Utils::formatBytes($this->view->hostingFreeSpace);
        $this->view->hostingUsedStr = HCMS_Utils::formatBytes($this->view->hostingUsed);
    }
    
    public function indexAction() {
        //get user object
        $user = new Auth_Model_User();
        Auth_Model_UserMapper::getInstance()->find($this->_admin->get_id(), $user);
        //store dashbord
        if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->isPost()) {
            $dashboard = $this->_getParam('dashboard');
            if(!isset ($dashboard)){
                $dashboard = array();
            }
            $userData = $user->get_data();
            $userData['dashboard'] = $dashboard;
            $user->set_data($userData);
            $user->set_password(null);
            Auth_Model_UserMapper::getInstance()->save($user);
            return $this->getHelper('json')->direct(array(
                'success'   => true
            ));
        }
        $this->view->widgetClasses = array();
        $this->view->widgetJsFiles = array();
        $this->view->widgetCssFiles = array();
        //list modules
        $modules = Application_Model_ModuleMapper::getInstance()->fetchAll(array('status'=>'active'));
        /* @var $module Application_Model_Module */
        foreach ($modules as $module) {
            $widgets = $module->get_data('widgets');
            if(!isset ($widgets)){
                continue;
            }
            foreach ($widgets as $widgetClass => $widgetArr) {
                $this->view->widgetClasses[] = $widgetClass;
                foreach ($widgetArr['jsFiles'] as $jsFile) {
                    $this->view->widgetJsFiles[] = $jsFile;
                }
                foreach ($widgetArr['cssFiles'] as $cssFile) {
                    $this->view->widgetCssFiles[] = $cssFile;
                }
            }
        }
        //get configured user's dashboard
        $userDashboard = $user->get_data('dashboard');
        if(!isset ($userDashboard)){            
            $userDashboard = array(
                'region1'=> array(
                    'widgets'=> array(
                    )
                ),
                'region2'=> array(
                    'widgets'=> array(
                    )
                )
            );
            $i = 0;
            foreach ($this->view->widgetClasses as $widgetClass) {
                $widgetArr = array(
                    'componentClass' => $widgetClass,
                    'settings' => array()
                );
                $regionIndex = ($i % 2) + 1;
                $userDashboard['region' . $regionIndex]['widgets'][] = $widgetArr;
                $i++;
            }
        }
        $this->view->userDashboard = $userDashboard;
    }
                }
