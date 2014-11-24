<?php
/**
 * Subscription Mapper
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_Model_SubscriptionMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Contact_Model_SubscriptionMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Contact_Model_DbTable_Subscription
     */
    protected $_dbTable;

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Contact_Model_DbTable_Subscription();
    }

    /**
     * get instance
     *
     *
     * @return Contact_Model_SubscriptionMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Contact_Model_Subscription $subscription
     * @return boolean
     */
    public function find($id, Contact_Model_Subscription $subscription) {
        $result = $this->_dbTable->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $subscription->setOptions($row->toArray());
        return true;
    }
    
    /**
     * Find by code and populate entity
     *
     * @param string $id
     * @param string $code
     * @param Contact_Model_Subscription $subscription
     * @return boolean
     */
    public function findByCode($id, $code, Contact_Model_Subscription $subscription) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                  ->from(array('s'=>'contact_subscription'),array('s.*'))
                  ->where('s.code = ?', $code)
                    ->where('s.id = ?', $id);
        
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $subscription->setOptions($row->toArray());
        return true;
    }
    
    /**
     * Find by email and populate entity
     *
     * @param string $email
     * @param Contact_Model_Subscription $subscription
     * @return boolean
     */
    public function findByEmail($email, Contact_Model_Subscription $subscription) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                  ->from(array('s'=>'contact_subscription'),array('s.*'))
                  ->where('s.email = ?', $email);
        
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $subscription->setOptions($row->toArray());
        return true;
    }     

    /**
     * Find all Subscriptions
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     * @return array 
     */
    public function fetchAll($criteria = array(), $orderBy = array(), &$paging = null) {
        
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
                ->from(array('s'=>'contact_subscription'),array('s.*'));

        if(isset ($criteria['status'])){
            $select->where('s.status = ?', $criteria['status']);
        }
        if(isset ($criteria['subscribed_from'])){
            $select->where('s.subscribed_dt >= ?', $criteria['subscribed_from']);
        }        
        if(isset ($criteria['subscribed_to'])){
            $select->where('s.subscribed_dt < ?', $criteria['subscribed_to']);
        }
        if(isset ($criteria['unsubscribed_from'])){
            $select->where('s.unsubscribed_dt >= ?', $criteria['unsubscribed_from']);
        }        
        if(isset ($criteria['unsubscribed_to'])){
            $select->where('s.unsubscribed_dt < ?', $criteria['unsubscribed_to']);
        }         
        
        if(is_array($orderBy) && count($orderBy) > 0 ){
            $select->order($orderBy);
        }

        // init paginator
        if($paging != null){
            $resultSet = $this->_getPagingRows($paging, $select);
        }
        else{
            $resultSet = $this->_dbTable->fetchAll($select);
        }

        $subscriptions = array();
        if (0 == count($resultSet)) {
            return $subscriptions;
        }

        foreach ($resultSet as $row) {
            $rowArray = $row->toArray();
            $subscription = new Contact_Model_Subscription();
            $subscription->setOptions($rowArray);

            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }

    /**
     * Save entity
     *
     * @param Contact_Model_Subscription $subscription
     */
    public function save(Contact_Model_Subscription $subscription) {
        $data = array();
        $this->_populateDataArr($data, $subscription, array( 'id','first_name','last_name','email',
                                                        'status','subscribed_dt','unsubscribed_dt','code'
        ));
        $id = $subscription->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $subscriptionId = $this->_dbTable->insert($data);
            if($subscriptionId > 0){
                $subscription->set_id($subscriptionId);
                return true;
            }
            else{
                return false;
            }
        } else {
            $result  = $this->_dbTable->update($data, array('id = ?' => $id));
            return $result > 0;
        }
    }

    /**
     * Delete Page
     *
     * @param int $ids
     */
    public function delete(Contact_Model_Subscription $subscription){
        //delete page
        $result = $this->_dbTable->getAdapter()->delete('contact_subscription', array(
            'id = ?'   => $subscription->get_id()
        ));

        return ($result > 0);
    }


}