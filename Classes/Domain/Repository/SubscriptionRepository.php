<?php
class Tx_MailchimpSubscribe_Domain_Repository_SubscriptionRepository extends Tx_Extbase_Persistence_Repository {
private $implementation;
private function getImplementation() {
  if( null == $this->implementation ) {
    $this->implementation = new MailchimpSubscribeSubscriptionRepositoryImplementation($this);
  }
  return $this->implementation;
}

}

?>