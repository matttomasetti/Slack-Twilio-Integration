<?php

namespace src\Controller\API\Slack;

/**
 * Class for sending API calls to Slack, as well as format
 * any necessary data.
 * @package src\Controller\API\Slack
 */
class SlackCall{

    /**
     * Formats a given associative array into a valid
     * POSTFIELDS string
     * @param array $data_array Associative array which it to be formatted
     * @return string Formatted POSTFIELDS string
     */
    public function formatData(array $data_array): string{
        $data_string = "";

        //conjoin the key and values of the associative array into
        //a valid POSTFIELDS string
        foreach ($data_array as $data_key=>$data_value){
            $data_string .= "{$data_key}=$data_value&";
        }

        //return the conjoined string (minus the trailing "&")
        return substr($data_string, 0, -1);
    }


    /**
     * Sends a CURL request to the Slack API using the given parameters and returns the decoded JSON response
     * @param string $uri The URI of the desired API call
     * @param string|null $data The data, if any, which it to be sent in the API call
     * @param string $method The HTTP method which is to be used for the API call
     * @param bool $bot_token Whether or not to use the bot oauth token ofr the API call
     * @return array Slack's response to the API request
     */
    public function send(string $uri, string $data = null, string $method = "POST", bool $bot_token = false): array{

        $curl = curl_init();

        //Determine whether to use the bot token or the default app token for authentication
        if($bot_token){
            $token = $_ENV['slack']['bot_token'];
        }else{
            $token = $_ENV['slack']['token'];
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://slack.com/api/{$uri}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        //file_put_contents("var/log/api.txt", $response."\n", FILE_APPEND);

        curl_close($curl);
        return json_decode($response, true);

    }

}

?>