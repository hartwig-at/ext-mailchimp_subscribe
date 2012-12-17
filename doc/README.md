# EXT:mailchimp_subscribe  
Author: Oliver Salzburg (oliver.salzburg@gmail.com) - Hartwig Communication & Events

## Introduction
`EXT:mailchimp_subscribe` provides a simple FE plugin that displays a form that allows a user to subscribe himself to a predefined MailChimp list.

The extension is currently under development and is updated frequently.

## Installation
1. Install extension
2. Include static TypoScript
3. Add FE plugin anywhere you want
4. Contact author for any issues you run into

## How to use
There's two ways in which using `EXT:mailchimp_subscribe` makes sense.

1. Frontend Content Element
   For when you need a subscription form on a single page for a specific list.

2. TypoScript Object (recommended)
   For when you want to dynamically place the form on many or all pages.

## Use as Content Element
Just drop it on a page and fill in the parameters.

## Use via TypoScript
If you want to add the subscribe form to your page via TypoScript, use this code:

    lib.mailchimp_subscribe = USER
    lib.mailchimp_subscribe {
       userFunc      = tx_extbase_core_bootstrap->run
       extensionName = MailchimpSubscribe
       pluginName    = Form
       settings      < plugin.tx_mailchimpsubscribe.settings
       settings {
         // Customize to your liking (these values are examples)
         apiKey         = e2a7b9997caa5640206388a0f583a0b6-us5
         list           = c3877ee6db
         //hideIfListed = 0
       }
       switchableControllerActions {
         Form {
           1 = display
        }
      }
    }

One day, that may be part of the extension...

## Use with Powermail2
`EXT:mailchimp_subscribe` fully supports Powermail2 integration.

In fact, if you're using Powermail2 on your site, `EXT:mailchimp_subscribe` will automatically interface with it.
You just need to tell the extension in which form fields the email address and the confirmation checkbox are contained. Like so:

    plugin.tx_mailchimpsubscribe {
      settings {
        apiKey = e2a7b9997caa5640206388a0f583a0b6-us5
        list   = c3877ee6db
        powermail {
          emailInputId         = 8
          newsletterCheckboxId = 76
        }
      }
    }
