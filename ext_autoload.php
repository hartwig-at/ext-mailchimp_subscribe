<?php
$extensionPath=t3lib_extMgm::extPath('mailchimp_subscribe');return array('tx_mailchimpsubscribe_service_subscriptionservice' => $extensionPath . 'Classes/Service/SubscriptionService.php',
'tx_mailchimpsubscribe_tasks_resetsubscriptionstaskfields' => $extensionPath . 'Classes/Tasks/ResetSubscriptionsTaskFields.php',
'tx_mailchimpsubscribe_tasks_resetsubscriptionstask' => $extensionPath . 'Classes/Tasks/ResetSubscriptionsTask.php',
);?>