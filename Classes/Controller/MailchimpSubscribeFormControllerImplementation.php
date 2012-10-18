<?php
require_once("FormController.php");
class MailchimpSubscribeFormControllerImplementation extends Tx_MailchimpSubscribe_Controller_FormController {

  /**
   * controller
   * @var Tx_MailchimpSubscribe_Controller_FormController
   */
  private $controller = NULL;

  private $DEFAULT_EMAIL = "john.doe@example.org";

  public function __construct( $interface ) {
    $this->controller = $interface;
  }

  public function displayAction(Tx_MailchimpSubscribe_Domain_Model_Subscription $subscription = NULL) {
    $email = $this->DEFAULT_EMAIL;
    $action = "subscribe";

    /** @var $user Tx_Extbase_Domain_Model_FrontendUser */
    $user = $this->getFrontendUser();
    if( NULL != $user ) {
      $email = $user->getEmail();
    }

    // If a user is logged in, see if his email is already subscribed
    $existingSubscription = $this->controller->subscriptionRepository->findOneByEmail( $email );
    if( null != $existingSubscription ) {
      // Use this subscription instance in the form
      $subscription = $existingSubscription;
      $action = "update";

    } else if( NULL == $subscription ) {
      // If no subscription exists and none was provided, create a new one
      $subscription = new Tx_MailchimpSubscribe_Domain_Model_Subscription();
      $subscription->setEmail( $email );
    }

    $this->controller->view->assign( "action", $action );
    $this->controller->view->assign( "subscription", $subscription );
  }

  public function subscribeAction( Tx_MailchimpSubscribe_Domain_Model_Subscription $subscription ) {
    if( $this->DEFAULT_EMAIL == $subscription->getEmail() ) {
      $this->controller->view->assign( "response", "John is already getting enough mail. Why not sign up with your address?" );
      return;
    }
    // Persist subscription
    $this->controller->subscriptionRepository->add( $subscription );

    $mailChimpApi = t3lib_extMgm::extPath( "mailchimp_subscribe" ) . "Resources/Private/Php/MCAPI.class.php";
    require_once( $mailChimpApi );

    //API Key - see http://admin.mailchimp.com/account/api
    $apikey = 'YOUR MAILCHIMP APIKEY';

    // A List Id to run examples against. use lists() to view all
    // Also, login to MC account, go to List, then List Tools, and look for the List ID entry
    $listId = 'YOUR MAILCHIMP LIST ID - see lists() method';

    // A Campaign Id to run examples against. use campaigns() to view all
    $campaignId = 'YOUR MAILCHIMP CAMPAIGN ID - see campaigns() method';

    //some email addresses used in the examples:
    $my_email = 'INVALID@example.org';
    $boss_man_email = 'INVALID@example.com';

    //just used in xml-rpc examples
    $apiUrl = 'http://api.mailchimp.com/1.3/';

    $apikey = $this->controller->settings[ "apiKey" ];
    $listId = $this->controller->settings[ "list" ];
    $my_email = $subscription->getEmail();

    $api = new MCAPI($apikey);
    /*
    $merge_vars = array('FNAME'=>'Test', 'LNAME'=>'Account',
                        'GROUPINGS'=>array(
                          array('name'=>'Your Interests:', 'groups'=>'Bananas,Apples'),
                          array('id'=>22, 'groups'=>'Trains'),
                        )
    );*/
    $merge_vars = array();

    // By default this sends a confirmation email - you will not see new members
    // until the link contained in it is clicked!
    $retval = $api->listSubscribe( $listId, $my_email, $merge_vars );
    $response = NULL;
    if ($api->errorCode){
      $response .= "Unable to load listSubscribe()!\n";
      $response .= "\tCode=".$api->errorCode."\n";
      $response .= "\tMsg=".$api->errorMessage."\n";

    } else {
      $response .= "Subscribed - look for the confirmation email!\n";
    }

    $this->controller->view->assign( "response", $response );
  }

  public function updateAction(Tx_MailchimpSubscribe_Domain_Model_Subscription $subscription) {
    $this->controller->view->assign( "response", "Your subscription has been updated." );
  }

  /**
   * Gets a frontend user which is taken from the global registry or as fallback from TSFE->fe_user.
   *
   * @throws LogicException
   * @return  tslib_feUserAuth The current extended frontend user object
   */
  protected function getFrontendUser() {
    if( $GLOBALS['TSFE']->fe_user ) {
      $user = $GLOBALS['TSFE']->fe_user;
      $feUserId = $user->user[ 'uid' ];
      $frontendUser = $this->controller->frontendUserRepository->findOneByUid( $feUserId );
      if( NULL == $frontendUser ) throw new LogicException( "\$frontendUser is null \$feUserId is '$feUserId'. Did you set the ExtBase Storage PID for this extension?" );
      return $frontendUser;
    }
    return NULL;
  }
}