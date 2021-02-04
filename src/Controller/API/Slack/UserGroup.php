<?php


namespace src\Controller\API\Slack;


/**
 * Controller providing CRUD functionality for User Groups
 * through Slacks API.
 * @package src\Controller\API\Slack
 */
class UserGroup {

    /**
     * Gets all members that belong to a given User Group
     * @param string $usergroup_id The ID of the User Group who's members should be retrieved
     * @return array Slack's API response containing member data on the Slack user group
     */
    static public function getMembers(string $usergroup_id): array{
        $call = new SlackCall();
        $data = $call->formatData(['usergroup'=>$usergroup_id]);
        $response = $call->send("usergroups.users.list", $data);
        return $response;
    }
}