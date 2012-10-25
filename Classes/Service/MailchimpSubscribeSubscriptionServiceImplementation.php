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
 * Created: 2012-10-18 19:25
 *
 * @author     Oliver Salzburg
 * @copyright  Copyright (C) 2012, Oliver Salzburg
 * @license    http://opensource.org/licenses/mit-license.php MIT License
 * @package    TYPO3
 * @subpackage tx_mailchimp_subscribe
 */
require_once("SubscriptionService.php");
class MailchimpSubscribeSubscriptionServiceImplementation extends Tx_MailchimpSubscribe_Service_SubscriptionService {

  /**
   * Error code constants
   */
  const List_AlreadySubscribed = 214;

  /**
   * @var mixed
   */
  protected $settings = NULL;

  /**
   * subscriptionRepository
   * @var Tx_MailchimpSubscribe_Domain_Repository_SubscriptionRepository
   */
  protected $subscriptionRepository;

  /**
   * injectSubscriptionRepository
   * @param Tx_MailchimpSubscribe_Domain_Repository_SubscriptionRepository $subscriptionRepository
   */
  public function injectSubscriptionRepository(Tx_MailchimpSubscribe_Domain_Repository_SubscriptionRepository $subscriptionRepository) {
    $this->subscriptionRepository = $subscriptionRepository;
  }

  public function subscribe( $email ) {
    // First of all, make sure to grab our OWN settings. Who knows from what domain we're being invoked.
    $configurationManager = t3lib_div::makeInstance( "Tx_Extbase_Configuration_ConfigurationManager" );
    if( NULL === $this->settings ) {
      $this->settings = $configurationManager->getConfiguration(
        Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
        "MailchimpSubscribe","Form"
      );
    }

    // Check if the user provided an email that's already on the list
    if( null == $this->subscriptionRepository ) {
      $this->subscriptionRepository = t3lib_div::makeInstance( "Tx_MailchimpSubscribe_Domain_Repository_SubscriptionRepository" );
    }
    $existingSubscription = $this->subscriptionRepository->findOneByEmail( $email );
    if( NULL != $existingSubscription ) {
      throw new Tx_MailchimpSubscribe_Exception_AlreadySignedUpException();
    }

    $subscription = new Tx_MailchimpSubscribe_Domain_Model_Subscription();
    $subscription->setEmail( $email );
    $this->internalSubscribe( $subscription );
  }

  private function internalSubscribe(Tx_MailchimpSubscribe_Domain_Model_Subscription $subscription) {
    $mailChimpApiLocation = t3lib_extMgm::extPath( "mailchimp_subscribe" ) . "Resources/Private/Php/MCAPI.class.php";
    require_once( $mailChimpApiLocation );

    $apikey   = $this->settings[ "apiKey" ];
    $listId   = $this->settings[ "list" ];
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
        $this->subscriptionRepository->add( $subscription );

      } else {
        throw new SignUpException( $api->errorMessage, $api->errorCode );
      }

    } else {
      // Success! Persist subscription
      $this->subscriptionRepository->add( $subscription );
    }
  }
}
