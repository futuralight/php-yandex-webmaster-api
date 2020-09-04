<?php

namespace Futuralight\YandexWebmaster;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Exception;


class Api
{
    const END_POINT = 'https://api.webmaster.yandex.net';

    protected $siteId = null;
    protected $oauthToken = null;
    protected $version;
    protected $client;

    private $userId = null;

    public function __construct($oauthToken, $version = "4")
    {
        $this->oauthToken = $oauthToken;
        $this->version = $version;
        $this->client = new Client(['headers' =>
        [
            'Authorization' => "OAuth {$this->oauthToken}",
            'Accept' => "application/json",
            'Content-type' => "application/json"
        ]]);
    }

    public function getUserId()
    {
        if (!$this->userId) {
            $response = $this->client->get(self::END_POINT . "/v{$this->version}/user/");
            $response = json_decode($response->getBody());
            $this->userId = $response->user_id;
        }

        return $this->userId;
    }

    public function getHosts()
    {
        $response = $this->client->get(self::END_POINT . "/v{$this->version}/user/" . $this->getUserId() . '/hosts/');
        return json_decode($response->getBody());
    }

    public function getHost($hostId)
    {
        $response = $this->client->get(self::END_POINT . "/v{$this->version}/user/" . $this->getUserId() . '/hosts/' . $hostId . '/');
        return json_decode($response->getBody());
    }

    public function addOriginalText($hostId, $text)
    {
        $content = json_encode(['content' => $text]);

        $response = $this->client->post(self::END_POINT . "/v{$this->version}/user/" . $this->getUserId() . '/hosts/' . $hostId . '/original-texts/', $content);
        $response = json_decode($response->getBody());

        return $response->text_id;
    }

    public function getHostQuota($hostId)
    {
        $response = $this->client->get(self::END_POINT . "/v{$this->version}/user/" . $this->getUserId() . '/hosts/' . $hostId . '/recrawl/quota');
        return json_decode($response->getBody());
    }

    public function postRecrawl($hostId, $url)
    {
        $response = $this->client->post(
            self::END_POINT . "/v{$this->version}/user/" . $this->getUserId() . '/hosts/' . $hostId . '/recrawl/queue',
            [
                RequestOptions::JSON => ['url' => $url]
            ]
        );
        return json_decode($response->getBody());
    }
}
