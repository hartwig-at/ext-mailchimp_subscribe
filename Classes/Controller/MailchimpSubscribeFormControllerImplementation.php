<?php
/**
 * Copyright (C) 2012, Oliver Salzburg
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Created: 2012-10-17
 *
 * @author     Oliver Salzburg
 * @copyright  Copyright (C) 2012, Oliver Salzburg
 * @license    http://opensource.org/licenses/mit-license.php MIT License
 * @package    TYPO3
 * @subpackage tx_mailchimp_subscribe
 */
require_once( "FormController.php" );
class MailchimpSubscribeFormControllerImplementation extends Tx_MailchimpSubscribe_Controller_FormController {

  /**
   * controller
   * @var Tx_MailchimpSubscribe_Controller_FormController
   */
  private $controller = NULL;

  private $DEFAULT_EMAIL = "john.doe@example.org";

  /**
   * Error code constants
   */
  const List_AlreadySubscribed = 214;

  public function __construct( $interface ) {
    $this->controller = $interface;
  }

  public function displayAction( Tx_MailchimpSubscribe_Domain_Model_Subscription $subscription = NULL ) {
    $email  = $this->DEFAULT_EMAIL;
    $action = "subscribe";

    // Due to some unknown problem, this grabs the wrong FE user sometimes (reason still unknown)
    if( $this->controller->settings[ "prefillEmail" ] ) {
      /** @var $user Tx_Extbase_Domain_Model_FrontendUser */
      $user = $this->getFrontendUser();
      if( NULL != $user ) {
        $email = $user->getEmail();
      }
    }

    // If a user is logged in, see if his email is already subscribed
    $existingSubscription = $this->controller->subscriptionRepository->findOneByEmail( $email );
    if( NULL != $existingSubscription ) {
      if( $this->controller->settings[ "hideIfListed" ] ) {
        $this->controller->view->assign( "registered", 1 );
        return;
      }
      $subscriptionCanBeUpdated = false;
      if( $subscriptionCanBeUpdated ) {
        // Use this subscription instance in the form
        $subscription = $existingSubscription;
        $action       = "update";

      } else {
        // If we don't allow updating of subscriptions, just create a fresh one
        $subscription = new Tx_MailchimpSubscribe_Domain_Model_Subscription();
        $subscription->setEmail( $email );
      }

    } else {
      if( NULL == $subscription ) {
        // If no subscription exists and none was provided, create a new one
        $subscription = new Tx_MailchimpSubscribe_Domain_Model_Subscription();
        $subscription->setEmail( $email );
      }
    }

    $this->controller->view->assign( "action", $action );
    $this->controller->view->assign( "subscription", $subscription );
  }

  public function subscribeAction( Tx_MailchimpSubscribe_Domain_Model_Subscription $subscription ) {
    if( $this->DEFAULT_EMAIL == $subscription->getEmail() ) {
      $this->controller->view->assign( "response-key", "default-email-used" );
      return;
    }

    if( !self::isEmailValid( $subscription->getEmail() ) ) {
      $this->controller->view->assign( "response-key", "invalid-email" );
      return;
    }

    // Check if the user provided an email that's already on the list
    $existingSubscription = $this->controller->subscriptionRepository->findOneByEmail( $subscription->getEmail() );
    if( null != $existingSubscription ) {
      $this->controller->view->assign( "response-key", "already-listed" );
      return;
    }

    $mailChimpApi = t3lib_extMgm::extPath( "mailchimp_subscribe" ) . "Resources/Private/Php/MCAPI.class.php";
    require_once( $mailChimpApi );

    $apikey   = $this->controller->settings[ "apiKey" ];
    $listId   = $this->controller->settings[ "list" ];
    $my_email = $subscription->getEmail();

    $api = new MCAPI( $apikey );
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
    $retval   = $api->listSubscribe( $listId, $my_email, $merge_vars );
    $response = NULL;
    if( $api->errorCode ) {
      if( self::List_AlreadySubscribed == $api->errorCode ) {
        // User was already on the list. Remember it!
        $this->controller->subscriptionRepository->add( $subscription );
        $this->controller->view->assign( "response-key", "response-success" );

      } else {
        $responseDetails = "Unable to load listSubscribe()! - ";
        $this->controller->view->assign( "response-key", "response-error" );
        $this->controller->view->assign( "response-code", $api->errorCode );
        $this->controller->view->assign( "response-message", $api->errorMessage );
      }

    } else {
      // Success! Persist subscription
      $this->controller->subscriptionRepository->add( $subscription );
      $this->controller->view->assign( "response-key", "response-success" );
    }

  }

  public function updateAction( Tx_MailchimpSubscribe_Domain_Model_Subscription $subscription ) {
    $this->controller->view->assign( "response-key", "already-listed" );
  }

  public function onPowermailSubmitAction( $data, $object ) {
    // Given that we're being called from Powermail, the $this->controller->settings array
    // contains the settings for PowerMail (we assume this is by-design), we have to grab
    // our own configuration data.
    $configurationManager = t3lib_div::makeInstance('Tx_Extbase_Configuration_ConfigurationManager');
    $settings = $configurationManager->getConfiguration(
      Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
      "MailchimpSubscribe","Form"
    );

    // Did the user specify the ID of a "newsletter checkbox"?
    $newsletterCheckboxId = $settings[ "powermail" ][ "newsletterCheckboxId" ];
    $emailInputId         = $settings[ "powermail" ][ "emailInputId" ];
    if( $data[ $newsletterCheckboxId ] && $data[ $emailInputId ] ) {
      // The checkbox array was found (Yay!)
      // Check if the first entry in this array was checked
      // (Yes, that means only the first checkbox in this array is respected)
      if( strlen( $data[ $newsletterCheckboxId ][ 0 ] ) > 1 ) {
        // Looks like this user wants to have some mail... OK!
        $email = $data[ $emailInputId ];

        /** @var $subscribeService Tx_MailchimpSubscribe_Service_SubscriptionService */
        $subscribeService = t3lib_div::makeInstance( "Tx_MailchimpSubscribe_Service_SubscriptionService" );
        $subscribeService->subscribe( $email );
      }
    }
  }

  /**
   * Gets a frontend user which is taken from the global registry or as fallback from TSFE->fe_user.
   *
   * @throws LogicException
   * @return  tslib_feUserAuth The current extended frontend user object
   */
  protected function getFrontendUser() {
    if( $GLOBALS[ 'TSFE' ]->fe_user ) {
      $user         = $GLOBALS[ 'TSFE' ]->fe_user;
      $feUserId     = $user->user[ 'uid' ];
      $frontendUser = $this->controller->frontendUserRepository->findOneByUid( $feUserId );
      if( NULL == $frontendUser ) {
        throw new LogicException( "\$frontendUser is null \$feUserId is '$feUserId'. Did you set the ExtBase Storage PID for this extension?" );
      }
      return $frontendUser;
    }
    return NULL;
  }

  /**
   * Validate an email address.
   *
   * @see http://www.linuxjournal.com/article/9585?page=0,3
   *
   * @param $email The email address
   *
   * @return bool true if the email address has the email address format.
   */
  protected static function isEmailValid( $email ) {
    $isValid = true;
    $atIndex = strrpos( $email, "@" );
    if( is_bool( $atIndex ) && !$atIndex ) {
      $isValid = false;
    } else {
      $domain    = substr( $email, $atIndex + 1 );
      $local     = substr( $email, 0, $atIndex );
      $localLen  = strlen( $local );
      $domainLen = strlen( $domain );
      if( $localLen < 1 || $localLen > 64 ) {
        // local part length exceeded
        $isValid = false;
      } else {
        if( $domainLen < 1 || $domainLen > 255 ) {
          // domain part length exceeded
          $isValid = false;
        } else {
          if( $local[ 0 ] == '.' || $local[ $localLen - 1 ] == '.' ) {
            // local part starts or ends with '.'
            $isValid = false;
          } else {
            if( preg_match( '/\\.\\./', $local ) ) {
              // local part has two consecutive dots
              $isValid = false;
            } else {
              if( !preg_match( '/^[A-Za-z0-9\\-\\.]+$/', $domain ) ) {
                // character not valid in domain part
                $isValid = false;
              } else {
                if( preg_match( '/\\.\\./', $domain ) ) {
                  // domain part has two consecutive dots
                  $isValid = false;
                } else {
                  if( !preg_match( '/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace( "\\\\", "", $local ) ) ) {
                    // character not valid in local part unless
                    // local part is quoted
                    if( !preg_match( '/^"(\\\\"|[^"])+"$/', str_replace( "\\\\", "", $local ) ) ) {
                      $isValid = false;
                    }
                  }
                }
              }
            }
          }
        }
      }
      /*
      if($isValid && !(checkdnsrr( $domain, "MX" ) || checkdnsrr( $domain, "A" ))) {
        // domain not found in DNS
        $isValid = false;
      }
      */
    }
    return $isValid;
  }
}