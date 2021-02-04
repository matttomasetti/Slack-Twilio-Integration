<?php

namespace src\Controller\API\Slack;

/**
 * Controller providing CRUD functionality for Messages
 * through Slacks API.
 * @package src\Controller\API\Slack
 */
class Message {

    /**
     * Send a new message in a slack channel.
     * This function will also create the channel is it does not already exist,
     * as well as add all members of the Slack user group to the channel
     * @param string $channel_name The name of the channel to send the message in
     * @param string $text The body text of the message
     * @param string $attachments JSON array of images to be sent with the message
     * @param bool $retry Whether to retry to send the message if sending fails
     * @return array Slack's API response containing data on the Slack message
     */
    static public function create(string $channel_name, string $text, string $attachments = "", bool $retry = false): array{
        $call = new SlackCall();

        //Ensure all members of the Slack user group are in the channel
        $channel_info = Channel::getByName($channel_name);
        $users = UserGroup::getMembers($_ENV["slack"]["group_id"]);
        if ($users["ok"] == true) {
            foreach ($users["users"] as $user) {
                Channel::inviteMember($channel_info["id"], $user);
            }
        }

        //Send the message into the Slack Channel
        $message_data = [
            'channel'=>$channel_name,
            'text'=>$text,
            'attachments'=> $attachments
        ];
        $data = $call->formatData($message_data);
        $response = $call->send("chat.postMessage", $data);

        //If the message fails to send due to the channel not being found
        //create the channel, and retry
        if(!$retry && $response["ok"] == false && $response["error"] == "channel_not_found"){
            Channel::create($channel_name);
            Message::create($channel_name, $text, $attachments, true);
        }

        return $response;
    }

}