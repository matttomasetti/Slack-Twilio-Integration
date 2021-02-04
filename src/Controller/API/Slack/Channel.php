<?php

namespace src\Controller\API\Slack;

/**
 * Controller providing CRUD functionality for Channels
 * through Slacks API
 * @package src\Controller\API\Slack
 */
class Channel {

    /**
     * Get all channels in the workspace
     * @return array Slack's API response containing data on Slack channels
     */
    static public function getAll(): array{
        $call = new SlackCall();
        $response = $call->send("conversations.list");
        return $response;
    }

    /**
     * Get a specific channel with the given ID in the workspace
     * @param string $channel_id the ID of the channel to retrieve
     * @return array Slack's API response containing data on the Slack channel
     */
    static public function get(string $channel_id): array{
        $call = new SlackCall();
        $data = $call->formatData(['channel'=>$channel_id]);
        $response = $call->send("conversations.info", $data);
        return $response;
    }


    /**
     * Get a specific channel with a given name in the workspace
     * @param string $channel_name the name of the channel to retrieve
     * @return array Slack's API response containing data on the Slack channel
     */
    static public function getByName(string $channel_name): array{

        //Slack's API doesn't have an API call to retrieve a channel by name
        //So grab all the channel and loop through until a match is found
        //between the Channel Name and the parameter is found
        $channels = Channel::getAll();

        if($channels["ok"] == true) {
            foreach ($channels["channels"] as $channel) {
                if ($channel["name"] == $channel_name) {
                    return $channel;
                }
            }
        }

        //If a match is not found, or the API returns an error
        //return an empty array
        return [];
    }

    /**
     * Create a channel in the workspace with the given name
     * @param string $channel_name the name of the channel to create
     * @return array Slack's API response containing data on the created Slack channel
     */
    static public function create(string $channel_name): array {
        $call = new SlackCall();
        $data = $call->formatData(['name'=>$channel_name]);
        $response = $call->send("conversations.create", $data);
        return $response;
    }


    /**
     * Get the message history of a specific channel with the given ID in the workspace
     * @param string $channel_id the ID of the channel who's history is to be retrieved
     * @return array Slack's API response containing the message history data of the Slack channel
     */
    static public function getHistory(string $channel_id): array{
        $call = new SlackCall();
        $data = $call->formatData(['channel'=>$channel_id]);
        $response = $call->send("conversations.history", $data);
        return $response;
    }

    /**
     * Get the members of a specific channel with the given ID in the workspace
     * @param string $channel_id the ID of the channel who's members is to be retrieved
     * @return array Slack's API response containing the members of the Slack channel
     */
    static public function getMembers(string $channel_id): array{
        $call = new SlackCall();
        $data = $call->formatData(['channel'=>$channel_id]);
        $response = $call->send("conversations.members", $data);
        return $response;
    }

    /**
     * Adds a list of users by their user ID to a given channel
     * @param string $channel_id the ID of the channel to add the users to
     * @param string $user_ids a comma separated list of Slack user IDs
     * @return array Slack's API response containing data on the Slack channel
     */
    static public function inviteMember(string $channel_id, string $user_ids): array{
        $call = new SlackCall();
        $data = $call->formatData(['channel'=>$channel_id, "users"=>$user_ids]);
        $response = $call->send("conversations.invite", $data);
        return $response;
    }

    /**
     * Remove a single users by their user ID from a given channel
     * @param string $channel_id the ID of the channel to remove the user from
     * @param string $user_id the ID of the user to be removed
     * @return array Slack's API response containing data on the Slack channel
     */
    static public function kickMember(string $channel_id, string $user_id): array{
        $call = new SlackCall();
        $data = $call->formatData(['channel'=>$channel_id, "user"=>$user_id]);
        $response = $call->send("conversations.kick", $data);
        return $response;
    }





}