<?php
if( !defined( 'TYPO3_MODE' ) ) {
  die( 'Access denied.' );
}
$TCA['tx_mailchimpsubscribe_domain_model_subscription'] = array(
  'ctrl' => $TCA['tx_mailchimpsubscribe_domain_model_subscription']['ctrl'],
  'interface' => array( 'showRecordFieldList' => '' ),
  'types' => array(  ),
  'palettes' => array(  ),
  'columns' => array( 'email' => array( 'exclude' => 1,'label' => 'LLL:EXT:mailchimp_subscribe/Resources/Private/Language/locallang_db.xml:tx_mailchimpsubscribe_domain_model_subscription.email','config' => array('type' => 'input') ) )
);
?>