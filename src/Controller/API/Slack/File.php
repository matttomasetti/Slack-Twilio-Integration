<?php

namespace src\Controller\API\Slack;


/**
 * Controller providing CRUD functionality for Files
 * through Slacks API.
 * This is used for video files, since only images can be directly attached to a slack message.
 * @package src\Controller\API\Slack
 */
class File {

    /**
     * Creates a new Slack File from a file based on an external, publicly accessible URL
     * @param string $file_id an unique id to which the file can be referenced by later. This can be
     *                      any unique value.
     * @param string $url the url of the file that Slack is to import
     * @param string $title the title of the file, which will display when the file is shared
     * @return array Slack's API response containing data on the Slack file
     */
    static public function create(string $file_id, string $url, string $title = ""): array {

        //If not title is given, assign the new file a default title
        if(!$title || $title == ""){
            $title = "Unnamed Attachment";
        }

        //Remove any unusual characters from the title that could cause the API to reject it
        $title = preg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $title);
        $title = preg_replace("([\.]{2,})", '', $title);


        $call = new SlackCall();
        $data = $call->formatData([
            "external_id" => $file_id,
            "external_url" => $url,
            "title" => $title
        ]);

        $response = $call->send("files.remote.add", $data, "POST", true);

        return $response;
    }

    /**
     * Sends the given Slack File in the given Slack Channel
     * @param string $channel_id the id of the channel which the file it to be shared in
     * @param string $file_id the id of the file to be shared
     * @return array Slack's API response containing data on the Slack file
     */
    static public function share(string $channel_id, string $file_id): array {
        $call = new SlackCall();
        $data = $call->formatData([
            "channels" => $channel_id,
            "external_id" => $file_id
        ]);

        $response = $call->send("files.remote.share", $data, "POST", true);
        return $response;
    }

    /**
     * Makes a file publicly accessible.
     * By default, files are private. However, they need to be made public in order to share with people/services
     * outside of Slack
     * @param string $file_id the ID of the file to be public
     * @return array Slack's API response containing data on the Slack file
     */
    static public function makePublic(string $file_id): array{
        $call = new SlackCall();
        $data = $call->formatData([
            "file" => $file_id
        ]);

        $response = $call->send("files.sharedPublicURL", $data);
        return $response;
    }

    /**
     * Makes a file private.
     * @param string $file_id the ID of the file to be private
     * @return array Slack's API response containing data on the Slack file
     */
    static public function makePrivate(string $file_id): array{
        $call = new SlackCall();
        $data = $call->formatData([
            "file" => $file_id
        ]);

        $response = $call->send("files.revokePublicURL", $data);
        return $response;
    }

}