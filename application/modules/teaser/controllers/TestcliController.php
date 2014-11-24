<?php

class Teaser_TestcliController extends HCLI_Controller_Action {

    /**
     * Logger
     * @var Zend_Log
     */
    protected $_logger = null;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        parent::__construct($request, $response, $invokeArgs);
        $this->_logger = Zend_Registry::get('Zend_Log');
    }

    public function echoAction() {
        $console = $this->getConsoleOptions(
                array('name|n=s' => 'Tell me your name')
        );
        $message = 'Hello ' . $console->getOption("name");
        echo $message, "\n";
        $this->_logger->log($message, Zend_Log::INFO);
        exit(0);
    }    
    
    public function maxAction(){        
        echo Teaser_Model_TeaserMapper::getInstance()->getMaxItemOrderNum(1);        
    }
    
    public function updateItemContent($lang) {
        $itemMapper = Teaser_Model_ItemMapper::getInstance();
        $items = $itemMapper->fetchAll(array(
            'lang'  => $lang
        ));
        /* @var  Teaser_Model_Item $item */
        foreach ($items as $item) {
            $box = Teaser_Model_TeaserMapper::getInstance()->getBox($item->get_box_code());
            if(!isset($box['params']['images'])){                
                continue;
            }
            foreach ($box['params']['images'] as $imageKey => $imageValues) {
                if($item->get_content($imageKey)){
                    continue;
                }
                $val768_2x = $item->get_content('img_768_2x');
                if(preg_match('/(.+)768_2x(.+)/i', $val768_2x, $matches)){
                    $newVal = $matches[1] . '960_2x' . $matches[2];
                    echo "$newVal\n";
                    $content = $item->get_content();
                    $content[$imageKey] = $newVal;
                    $item->set_content($content);
                    $itemMapper->populateTeaserIds($item);
                    
                    $itemMapper->save($item, $lang);
                    //print_r($item);
                }
            }
        }
    }
        
    
    public function updatecontentAction() {
        $this->updateItemContent('de');
        $this->updateItemContent('en');
    }
    
    public function imagecopyAction() {
        $directory = new RecursiveDirectoryIterator(APPLICATION_PATH . '/../public/images');
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/(.+)768_2x\.(jpg|png)$/i', RecursiveRegexIterator::GET_MATCH);
        foreach ($regex as $sourceFile => $matches) {
            $newFile = $matches[1] . '960_2x.' . $matches[2];
            echo "$newFile\n";
            copy($sourceFile, $newFile);
        }
    }

}