<?php

namespace SS\SlackNotifierPlus\App;

/**
 * Class Connection
 * @package SS\SlackNotifierPlus\App;
 */
abstract class Connection {
    /**
     * Slack API Url
     */
    const API_URL = "https://slack.com/api/";

    /**
     * @var string get from config file
     */
    private $token = '';

    /**
     * @var string get from config file
     */
    private $channel = '';

    private function setConfig(){
        /**
         * @var  $slack_token
         * @var $slack_channel
         */
        require_once __DIR__.'/config.php';

        if(  empty( $slack_token ) &&  empty( $slack_channel ) ){

            $error_message_attribute = [
                [
                    'label' => 'config error',
                    'value' => 'slack token or channel is not found. pleases add this information on config file'
                ]
            ];

            $this->sendEmailError($error_message_attribute);

            throw new \WHMCS\Exception(" slack token or channel is not found. pleases add this information on config file" );

        }

        $this->token = $slack_token;

        $this->channel = $slack_channel;

    }

    /**
     * Sending an email when an error happens
     *
     * @param $content
     */
    protected function sendEmailError( $attributes ){

        $command = 'SendAdminEmail';
        $postData = array(
            'customsubject' => 'Slack Notifier Notification Message',
            'custommessage' => '<h1>You have a problem in the configuration file!</h1>',
        );

        $results = localAPI($command, $postData);
        
    }

    /**
     * @param string $method
     * @param array $postdata
     * @return mixed
     */
    protected function call( $method, array $postdata = [])
    {

        $this->setConfig();

        $postdata["token"] = $this->token;
        $postdata["channel"] = $this->channel;
        
        

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $method);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($response);

        logModuleCall("slack", $method, $postdata, $response, $decoded, array($this->token));

        if (!isset($decoded->ok)) {
            throw new \WHMCS\Exception("Bad response: " . $response);
        }
        if ($decoded->ok == false) {

            $error_message_attribute = [
                [
                    'label' => 'slack api error',
                    'value' => "An error occurred: " . $decoded->error
                ]
            ];

            $this->sendEmailError($error_message_attribute);

            throw new \WHMCS\Exception("An error occurred: " . $decoded->error);
        }
        return $decoded;
    }

}