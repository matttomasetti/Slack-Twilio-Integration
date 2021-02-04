<?php


namespace src\Controller;

use DateTime;
use src\Controller\API\Slack\Channel;
use src\Controller\API\Slack\File;

/**
 * Controller to add functionality for interacting with Slack
 * @package src\Controller
 */
class SlackController {

    /**
     * Gets a list of Slack channels that have been inactive for over 24 hours
     * @return array list of Slack channels that have been inactive for over 24 hours
     * @throws \Exception
     */
    public function getInactiveTextMessageChannels(): array {
        $text_message_channels = [];

        //get all channels
        $channels = Channel::getAll();
        foreach ($channels["channels"] as $channel){

            //check if valid text message channel
            //we only want to check channels that are a valid phone number, and
            //not channels that are used internally
            if(!preg_match("/^\d{11}$/", $channel['name'])){
                continue;
            }

            //check if abandoned
            if($this->isInactive($channel['id'])){
                $text_message_channels[] = $channel;
            }
        }

        return $text_message_channels;

    }

    /**
     * Checks if a given channel should be marked as "inactive" ( if it has been over 24 hours since the last
     * message in the channel was sent )
     * @param string $channel_id The id of the channel who's activeness is to be checked
     * @return bool True if the channel is in active
     * @throws \Exception
     */
    public function isInactive(string $channel_id): bool{

        // get the Channel's event history
        $history = Channel::getHistory($channel_id);

        // return false if the channel isn't found
        if($history["ok"] != true){
            return false;
        }

        // return true if the channel's history is empty
        if(empty($history["messages"])){
            return true;
        }

        // get the timestamp of 24 hours ago
        $date = (new DateTime())->modify('-24 hours');
        $unix_time = $date->getTimestamp();

        // loop through the channel's event history until we find a valid message event
        foreach ($history["messages"] as $message) {

            // ignore channel join events
            if(isset($message["subtype"]) && $message["subtype"] == "channel_join"){
                continue;
            }

            // we should be left with just message events
            // if the last message was send before 24hours ago
            // the channel is defined as "inactive"
            if ($message["ts"] < $unix_time) {
                return true;
            }else{
                return false;
            }
        }

        return false;

    }

    /**
     * Removes all the members that are in a given channel
     * @param string $channel_id The ID of the channel who's members should be removed
     */
    public function removeMembers(string $channel_id): void{

        // get all the members in the channel
        $members = Channel::getMembers($channel_id);

        // make sure we have a valid list of members
        if($members["ok"] != true){
            return;
        }
        if(empty($members["members"])){
            return;
        }

        // remove each member from the channel using Slack's API's kick functionality
        foreach ($members["members"] as $member){
            Channel::kickMember($channel_id, $member);
        }
    }

    /**
     * Takes in a list of files that are attached to a slack message, makes them publicly accessible,
     * and returns an array containing a download link for each file
     * @param array $files List of files that are attached to a Slack message
     * @return array List of download links for all the media
     */
    public function getMediaFromMessage(array $files): array{
        $media = [];

        // loop through array of files
        foreach($files as $file){

            // make each file public so it can be accessed externally
            File::makePublic($file["id"]);

            // generate the public download url
            $parts = explode("-", $file["permalink_public"]);
            $secret = $parts[sizeof($parts) - 1];
            $url = $file["url_private_download"]."?pub_secret=".$secret;

            // add the url to the list of files
            $media[] = $url;

        }

        // return the list of download links for all the media
        return $media;
    }

    /**
     * Takes in an array of Slack files that should be made private
     * @param array $files List of files to make private
     */
    public function privatizeFiles($files): void{

        // Loop through the array of files, making each file private
        foreach($files as $file){
            File::makePrivate($file["id"]);
        }
    }


}