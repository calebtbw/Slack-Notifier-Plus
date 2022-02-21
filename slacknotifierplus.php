<?php
/**
 * Slack Notifier Plus
 * @copyright Copyright (c) Sphero Solutions
 */


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define Slack Notification Plus configuration parameters.
 *
 * @return array
 */
function slacknotifierplus_config()
{
    return [
        'name' => 'Slack Notifier Plus ',
        'description' => 'Custom cases for Slack notification.',
        'author' => '<a href="https://spherosolutions.net">Sphero Solutions</a>',
        'language' => 'english',
        'version' => '1.0',
        'fields' => []

    ];
}




