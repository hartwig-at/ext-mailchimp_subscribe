<?php
class Tx_MailchimpSubscribe_Controller_FormController extends Tx_Extbase_MVC_Controller_ActionController {
private $implementation;
private function getImplementation() {
  if( null == $this->implementation ) {
    $this->implementation = new MailchimpSubscribeFormControllerImplementation($this);
  }
  return $this->implementation;
}
function __construct() {
}
/**
* frontendUserRepository
* @var Tx_Extbase_Domain_Repository_FrontendUserRepository
*/
protected $frontendUserRepository;
/**
* injectFrontendUserRepository
* @param Tx_Extbase_Domain_Repository_FrontendUserRepository $frontendUserRepository
*/
public function injectFrontendUserRepository(Tx_Extbase_Domain_Repository_FrontendUserRepository $frontendUserRepository) {
  $this->frontendUserRepository = $frontendUserRepository;
}
/**
* subscriptionRepository
* @var Tx_MailchimpSubscribe_Domain_Repository_SubscriptionRepository
*/
protected $subscriptionRepository;
/**
* injectSubscriptionRepository
* @param Tx_MailchimpSubscribe_Domain_Repository_SubscriptionRepository $subscriptionRepository
*/
public function injectSubscriptionRepository(Tx_MailchimpSubscribe_Domain_Repository_SubscriptionRepository $subscriptionRepository) {
  $this->subscriptionRepository = $subscriptionRepository;
}

/**
*/
public function displayAction() { return $this->getImplementation()->displayAction(); }
/**
* @param Tx_MailchimpSubscribe_Domain_Model_Subscription $subscription
*/
public function subscribeAction($subscription) { return $this->getImplementation()->subscribeAction($subscription); }
/**
* @param Tx_MailchimpSubscribe_Domain_Model_Subscription $subscription
*/
public function updateAction($subscription) { return $this->getImplementation()->updateAction($subscription); }
/**
* @param mixed $data
* @param mixed $object
*/
public function onPowermailSubmitAction($data,$object) { return $this->getImplementation()->onPowermailSubmitAction($data,$object); }

}
require_once('MailchimpSubscribeFormControllerImplementation.php');

?>