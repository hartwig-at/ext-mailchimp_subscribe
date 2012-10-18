<?php
if( !defined( 'TYPO3_MODE' ) ) {
	die( 'Access denied.' );
}
t3lib_extMgm::addStaticFile('mailchimp_subscribe', 'Configuration/TypoScript/form', 'Subscription Form');
if( TYPO3_MODE == 'BE' ) {  $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['mailchimpsubscribe_form_wizicon'] =    t3lib_extMgm::extPath('mailchimp_subscribe') . 'Resources/Private/Php/class.mailchimpsubscribe_form_wizicon.php';}
Tx_Extbase_Utility_Extension::registerPlugin(
  'mailchimp_subscribe',
  'Form',
  'LLL:EXT:mailchimp_subscribe/Resources/Private/Language/locallang_be.xml:form_title'
);
$TCA['tt_content']['types']['list']['subtypes_addlist']['mailchimpsubscribe_form'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue('mailchimpsubscribe_form', 'FILE:EXT:mailchimp_subscribe/Configuration/FlexForms/flexform_form.xml');
$TCA['tx_mailchimpsubscribe_domain_model_subscription'] = array(
  'ctrl' => array(
    'title'                    => 'LLL:EXT:mailchimp_subscribe/Resources/Private/Language/locallang_db.xml:tx_mailchimpsubscribe_domain_model_subscription',
    'label'                    => '',
'hideTable'=>1,
    'dividers2tabs'            => TRUE,
    'dynamicConfigFile'        => t3lib_extMgm::extPath( 'mailchimp_subscribe' ) . 'Configuration/TCA/Subscription.php',
    'iconfile'                 => t3lib_extMgm::extRelPath( 'mailchimp_subscribe' ) . 'Resources/Public/Icons/tx_mailchimpsubscribe_domain_model_subscription.png',
  )
);
t3lib_extMgm::addStaticFile('mailchimp_subscribe', 'Configuration/TypoScript', 'MailChimp Subscribe Base');
?>