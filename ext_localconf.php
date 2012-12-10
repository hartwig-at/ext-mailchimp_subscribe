<?php
if( !defined( 'TYPO3_MODE' ) ) {
	die( 'Access denied.' );
}
Tx_Extbase_Utility_Extension::configurePlugin(  'mailchimp_subscribe',  'Form',  array(    'Form' => 'display,subscribe,update,onPowermailSubmit'  ),  array(    'Form' => ''  ));
$signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_SignalSlot_Dispatcher');
$signalSlotDispatcher->connect( 'Tx_Powermail_Controller_FormsController', 'createActionAfterSubmitView', 'Tx_MailchimpSubscribe_Controller_FormController', 'onPowermailSubmitAction', FALSE );
?>