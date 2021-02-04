<?php

namespace src\Controller;

use Exception;
use src\Controller\API\Slack\Message;
use src\Controller\API\Slack\Channel;
use src\Controller\API\Twilio\TextMessage;

/**
 * Handles incoming requests to the application
 * @package src\Controller
 */
class DefaultController{

    /**
     * Handles incoming text messages being sent to the Twilio number
     */
    public function incomingMessageRoute(): void{
        // file_put_contents("var/log/incoming.txt", "\n".json_encode($_POST), FILE_APPEND);

        // retrieve the message body and the customer phone number from the incoming text message
        $text = $_ENV['slack']['message_prefix']." - ".$_POST['Body'];
        $number = $_POST['From'];
        $number = preg_replace("/[^0-9]/", "", $number);

        // grab all attachments from the incoming text message
        $attachments = [];
        try {
            $twilio = new TwilioController();
            $attachments = $twilio->getMediaFromMessage($number);
        }catch (Exception $e){
            file_put_contents("var/log/error.txt", $e->getMessage()."\n", FILE_APPEND);
        }finally {

            // Create a Slack message from the incoming text message
            Message::create($number, $text, json_encode($attachments));
        }
    }

    /**
     * Handles messages from Slack meant to be outgoing to customers
     */
    public function outgoingMessageRoute(): void{

        // retrieve the message being passed in from Slack
        $body = file_get_contents('php://input');
        $outgoing_message = json_decode($body, true);

        //file_put_contents("var/log/outgoing.txt", "\n".$body, FILE_APPEND);

        //Messages sent from a Slack user are the only event without a subtype.
        //Without this, the customer will receive text message of all channel activities
        //including their own messages and users joining and leaving the channel
        if(isset($outgoing_message['event']['subtype']) && $outgoing_message['event']['subtype'] != "file_share"){
            return;
        }

        // get the text of the Slack message, which will be the body of the outgoing text message
        $message = $_ENV['twilio']['message_prefix']." - ".$outgoing_message['event']['text'];

        // get the channel information that the Slack message was sent through
        $channel = $outgoing_message['event']['channel'];
        $channel_data = Channel::get($channel);

        if($channel_data["ok"] == false){
            return;
        }

        // determine the customer's phone number from the Slack channel's name
        $number = $channel_data["channel"]["name"];

        // grab all media that was sent in the Slack message
        $media = [];
        $slack = new SlackController();
        if(isset($outgoing_message['event']['files'])){
            $media = $slack->getMediaFromMessage($outgoing_message['event']['files']);
        }

        // send the needed data to the TextMessage controller to be sent through Twilio as a text message
        try {
            TextMessage::send($number, $message, $media);
        }catch (\Exception $e){
            file_put_contents("var/log/error.txt", json_encode($e));
        }

        // any media in the Slack channels was made public during the "getMediaFromMessage" function call, so that
        // Twilio could retrieve the media files.
        // Those files should now be remade private.
        if(isset($outgoing_message['event']['files'])){
            $slack->privatizeFiles($outgoing_message['event']['files']);
        }

    }
}