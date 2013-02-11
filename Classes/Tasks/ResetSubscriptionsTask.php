<?php
class Tx_MailchimpSubscribe_Tasks_ResetSubscriptionsTask extends tx_scheduler_Task  {private $implementation;private function getImplementation() {  if( null == $this->implementation ) {    $this->implementation = new MailchimpSubscribeResetSubscriptionsTaskImplementation($this);  }  return $this->implementation;}function __construct() {parent::__construct();}
/**
*/
public function execute() { return $this->getImplementation()->execute(); }
}require_once('MailchimpSubscribeResetSubscriptionsTaskImplementation.php');
?>