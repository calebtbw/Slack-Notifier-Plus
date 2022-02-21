<?php
/**
 * WHMCS SDK Sample Addon Module Hooks File
 *
 * Hooks allow you to tie into events that occur within the WHMCS application.
 *
 * This allows you to execute your own code in addition to, or sometimes even
 * instead of that which WHMCS executes by default.
 *
 * @see https://developers.whmcs.com/hooks/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
require_once 'vendor/autoload.php';
use SS\SlackNotifierPlus\App\NotificationEvent;


/**
 * AfterModuleCreateFailed
 *
 * @see https://developers.whmcs.com/hooks-reference/module/#aftermodulecreatefailed
 */
add_hook('AfterModuleCreateFailed', 10, function($vars) {

    $failureMessage = $vars['failureResponseMessage'];

    $serviceid = $vars['params']['serviceid'];
    $userid = $vars['params']['clientsdetails']['userid'];

    $linkQuery = [
        'serviceid' => $serviceid,
        'userid' => $userid
    ];

    //fix message that sanitized or message that have array
    $reason = '';

    if(  is_array($failureMessage) ){

        foreach( $failureMessage as $section => $message ){

            if( !empty( $reason ) ) {

                $reason .= ' ' ;

            }

            if(  gettype(array_key_first($message)) === 'integer' ){

                $reason .= $section .':'. implode("|",$message);

            }else{


                $reason .= $section;

            }

        }

    }else{

        $exp = explode('[0] =>', $failureMessage);

        if( !empty( $exp[1] ) ){

            $strRaw = str_replace( ')',"", $exp[1]);

            $reason = trim($strRaw);

        }else{

            $reason = $failureMessage;

        }


    }

    $slackEvent = new NotificationEvent;

    /**
     * Send Notification to slack
     *
     * @uses \SS\SlackNotifierPlus\App\NotificationEvent::ModuleFailedEvent()
     */
    $slackEvent->ModuleFailedEvent($linkQuery , $reason);

});

