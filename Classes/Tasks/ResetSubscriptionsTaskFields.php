<?php
class Tx_MailchimpSubscribe_Tasks_ResetSubscriptionsTaskFields  implements tx_scheduler_AdditionalFieldProvider {private $implementation;private function getImplementation() {  if( null == $this->implementation ) {    $this->implementation = new MailchimpSubscribeResetSubscriptionsTaskFieldsImplementation($this);  }  return $this->implementation;}function __construct() {}
/**
* @param mixed $array &taskInfo
* @param mixed $task
* @param mixed $tx_scheduler_Module parentObject
*/
public function getAdditionalFields(array &$taskInfo,$task,tx_scheduler_Module $parentObject) { return $this->getImplementation()->getAdditionalFields($taskInfo,$task,$parentObject); }
/**
* @param mixed $array &submittedData
* @param mixed $tx_scheduler_Module parentObject
*/
public function validateAdditionalFields(array &$submittedData,tx_scheduler_Module $parentObject) { return $this->getImplementation()->validateAdditionalFields($submittedData,$parentObject); }
/**
* @param mixed $array submittedData
* @param mixed $tx_scheduler_Task task
*/
public function saveAdditionalFields(array $submittedData,tx_scheduler_Task $task) { return $this->getImplementation()->saveAdditionalFields($submittedData,$task); }
}require_once('MailchimpSubscribeResetSubscriptionsTaskFieldsImplementation.php');
?>