<?php
if( !defined( 'TYPO3_MODE' ) ) {
	die( 'Access denied.' );
}
Tx_Extbase_Utility_Extension::configurePlugin(  'mailchimp_subscribe',  'Form',  array(    'Form' => ''  ),  array(    'Form' => 'display,subscribe,update'  ));
?>