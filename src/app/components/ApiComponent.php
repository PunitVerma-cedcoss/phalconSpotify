<?php

namespace App\Components;

use Phalcon\Di\Injectable;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

/**
 * helper class for hitting the api and getting response
 */
class ApiComponent extends Injectable
{
    /**
     * returns whatever the response of the passed url
     *
     * @param [string] $q
     * @return ARRAY
     */
    public function getAccessToken($auth_token)
    {
        $url = "https://accounts.spotify.com/api/token";
        $client = new Client();
        $response = $client->request(
            'POST',
            $url,
            [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $auth_token,
                    'redirect_uri' => $this->REDIRECT_URI
                ],
                'headers'  => [
                    'Authorization' => $this->CLIENT_ID_SEC,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]
        );
        $response = json_decode($response->getBody()->getContents(), true);
        // checking if got correct data
        if (isset($response["access_token"])) {
            // now set up the session
            $this->session->set("access_token", $response["access_token"]);
            $this->session->set("expires_in", $response["expires_in"]);
            $this->session->set("refresh_token", $response["refresh_token"]);
            $this->session->set("scope", $response["scope"]);
        }
        // return $response;
    }
    public function getUserPlayList()
    {
    }
    public function getRefreshedToken()
    {
        $url = "https://accounts.spotify.com/api/token";
        $client = new Client();
        $response = $client->request(
            'POST',
            $url,
            [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $this->session->get("refresh_token"),
                ],
                'headers'  => [
                    'Authorization' => $this->CLIENT_ID_SEC,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]
        );
        $response = json_decode($response->getBody()->getContents(), true);
        // checking if got correct data
        if (isset($response["access_token"])) {
            // now set up the session
            $this->session->set("access_token", $response["access_token"]);
            $this->session->set("expires_in", $response["expires_in"]);
            $this->session->set("scope", $response["scope"]);
        }
    }
    public function getCurrentUserDetails()
    {
        $url = "https://api.spotify.com/v1/me";
        $client = new Client();

        $response = $client->request(
            'GET',
            $url,
            [
                'headers'  => [
                    'Authorization' => "Bearer " . $this->session->get("access_token"),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]
        );
        $response = json_decode($response->getBody()->getContents(), true);

        // set id in session
        $this->session->set("user_id", "31gcafrmfjpqcrr4jvnztgpbsqm4");
        echo "<pre>";
        print_r($response);
        die;
    }
    public function search($filters, $q)
    {
        $url = "https://api.spotify.com/v1/search?type=" . implode(",", $filters) . "&include_external=audio&q=" . $q . "&limit=5";
        try {
            $client = new Client();

            $response = $client->request(
                'GET',
                $url,
                [
                    'headers'  => [
                        'Authorization' => "Bearer " . $this->session->get("access_token"),
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ]
                ]
            );
            $response = json_decode($response->getBody()->getContents(), true);
            echo "<pre>";
            return $response;
        } catch (Guzzle\Http\Exception\BadResponseException $e) {
            die("your token is expired");
        }
    }
    public function getCurrentUserPlayLists()
    {
        $url = "https://api.spotify.com/v1/me/playlists";
        $client = new Client();

        $response = $client->request(
            'GET',
            $url,
            [
                'headers'  => [
                    'Authorization' => "Bearer " . $this->session->get("access_token"),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]
        );
        $response = json_decode($response->getBody()->getContents(), true);
        return $response;
    }
    public function createPlayLists($name, $desc, $ispublic)
    {
        $url = "https://api.spotify.com/v1/users/" . $this->session->get("user_id") . "/playlists";
        $client = new Client();

        $response = $client->request(
            'POST',
            $url,
            [
                'body' =>
                json_encode([
                    'name' => $name,
                    'description' => $desc,
                    'public' => $ispublic
                ]),
                'headers'  => [
                    'Authorization' => "Bearer " . $this->session->get("access_token"),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]
        );
        $response = json_decode($response->getBody()->getContents(), true);
        return $response;
    }
    public function addToPlayLists($playListId, $itemId)
    {
        $url = "https://api.spotify.com/v1/playlists/" . $playListId . "/tracks";
        $client = new Client();

        $response = $client->request(
            'POST',
            $url,
            [
                'body' =>
                json_encode([

                    'uris' => [$itemId],
                    "position" => 0
                ]),
                'headers'  => [
                    'Authorization' => "Bearer " . $this->session->get("access_token"),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]
        );
        $response = json_decode($response->getBody()->getContents(), true);
        return $response;
    }
}
