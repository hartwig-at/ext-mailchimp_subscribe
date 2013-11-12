<?php
if( !defined( 'TYPO3_MODE' ) ) {
	die( 'Access denied.' );
}
Tx_Extbase_Utility_Extension::configurePlugin(  'mailchimp_subscribe',  'Form',  array(    'Form' => 'display,onPowermailSubmit'  ),  array(    'Form' => 'subscribe,update'  ));
$signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_SignalSlot_Dispatcher');
$signalSlotDispatcher->connect( 'Tx_Powermail_Controller_FormsController', 'createActionAfterSubmitView', 'Tx_MailchimpSubscribe_Controller_FormController', 'onPowermailSubmitAction', FALSE );
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_MailchimpSubscribe_Tasks_ResetSubscriptionsTask'] = array(	'extension'        => 'mailchimp_subscribe',	'title'            => 'LLL:EXT:mailchimp_subscribe/Resources/Private/Language/locallang_be.xml:resetsubscriptions.name',	'description'      => 'LLL:EXT:mailchimp_subscribe/Resources/Private/Language/locallang_be.xml:resetsubscriptions.description','additionalFields' => 'tx_mailchimpsubscribe_tasks_resetsubscriptionstaskfields');
?>