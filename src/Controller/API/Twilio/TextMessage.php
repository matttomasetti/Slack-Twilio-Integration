<?php

namespace src\Controller\API\Twilio;

use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;


/**
 * Controller providing text message functionality
 * through Twilio's API.
 * @package src\Controller\API\Twilio
 */
class TextMessage {

    /**
     * Sends a text message from a number specified in the env.ini file to a given number
     * with a given message
     * @param string $number The number to send the text message to
     * @param string $message The body of the text message
     * @param array $media All, if any, videos and images to send with the text message
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    static public function send(string $number, string $message, array $media = []): void {

        // The SID associated with the Twilio account
        $sid = $_ENV['twilio']['sid'];

        // The API token associated with the Twilio account
        $token = $_ENV['twilio']['token'];

        $twilio = new Client($sid, $token);

        try {
            // Create and send the message
            $message = $twilio->messages->create("+{$number}", // to
                [
                    "body" => $message,
                    "from" => "+{$_ENV['twilio']['number']}",
                    "mediaURL" => $media
                ]
            );
        }catch (TwilioException $e){
            file_put_contents("var/log/error.txt", $e->getMessage());
        }
    }

}