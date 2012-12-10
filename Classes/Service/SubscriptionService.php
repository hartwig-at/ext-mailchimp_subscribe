<?php
class Tx_MailchimpSubscribe_Service_SubscriptionService extends Tx_Extbase_MVC_Controller_ActionController {
private $implementation;
private function getImplementation() {
  if( null == $this->implementation ) {
    $this->implementation = new MailchimpSubscribeSubscriptionServiceImplementation($this);
  }
  return $this->implementation;
}
function __construct() {
}

/**
* @param mixed $email
*/
public function subscribe($email) { return $this->getImplementation()->subscribe($email); }

}
require_once('MailchimpSubscribeSubscriptionServiceImplementation.php');

?>