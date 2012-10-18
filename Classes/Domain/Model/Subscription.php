<?php
class Tx_MailchimpSubscribe_Domain_Model_Subscription extends Tx_Extbase_DomainObject_AbstractEntity {
/**
* email
* @var string
*/
protected $email;
public function getT3ManagedFields() {	 return $this->t3ManagedFields;}public function setT3ManagedFields( $t3ManagedFields ) {	 $this->t3ManagedFields = $t3ManagedFields;}
public function getEmail() {	 return $this->email;}public function setEmail( $email ) {	 $this->email = $email;}
}
?>