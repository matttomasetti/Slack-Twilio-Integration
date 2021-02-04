<?php


namespace src\Controller;


use src\Controller\API\Slack\Channel;
use src\Controller\API\Slack\File;

/**
 * Controller to add functionality for interacting with Twilio messages
 * @package src\Controller
 */
class TwilioController {

    /**
     * Grabs all media from an incoming Twilio message
     * @param string $channel_name The name of the channel which the media will be shared too
     * @return array An array of all images attached to the incoming text message ( videos are shared via Slack's
     *              File API calls )
     */
    public function getMediaFromMessage(string $channel_name): array{
        $attachments = [];

        // Let data on the Slack channel which the attachments will be shared to.
        // This is needed if any video files are to be shared, since they will be
        // sent immediately through an API call, instead of passed back to be sent
        // as attachments to the Slack message
        $channel_info = Channel::getByName($channel_name);

        // loop through each media file in the incoming message
        for($i = 0; $i < $_POST["NumMedia"]; $i++){

            // if the media file is a video, upload via Slacks File API endpoints
            if(strpos($_POST["MediaContentType".$i], "video") !== false) {

                try {
                    File::create($_POST["SmsMessageSid"] . $i, $_POST["MediaUrl" . $i], $_POST['Body']);
                    File::share($channel_info["id"], $_POST["SmsMessageSid"] . $i);
                } catch (\Exception $e) {
                    file_put_contents("var/log/error.txt", "\n" . $e->getMessage(), FILE_APPEND);

                }

            // if media file is a video, add to the "attachments" array to be returned back to the calling function
            }else if(strpos($_POST["MediaContentType".$i], "image") !== false){

                array_push($attachments, [
                    'title' => $_POST['Body'],
                    "image_url" => $_POST["MediaUrl" . $i]
                ]);
            }

            // all other file formats should be ignored
        }

        return $attachments;

    }

}