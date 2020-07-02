<?php

namespace KeriganSolutions\FacebookFeed\Fetchers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use KeriganSolutions\FacebookFeed\Contracts\DataFetcher;

class PostFetcher implements DataFetcher
{
    const FEED = 'permalink_url,full_picture,message,status_type,created_time,attachments{target,media}';

    protected $client;
    protected $pageId;
    protected $accessToken;

    public function __construct($accessToken, $pageId)
    {
        $this->client = new Client(['base_uri' => 'https://graph.facebook.com/v7.0']);
        $this->accessToken = $accessToken;
        $this->pageId      = $pageId;
    }

    public function get($limit, $before, $after)
    {
        try {
            $response = $this->client->request(
                'GET',
                '/' . $this->pageId .
                '/posts/?fields=' . self::FEED .
                '&limit=' . $limit .
                '&access_token=' . $this->accessToken .
                '&before=' . $before .
                '&after=' . $after
            );

            return json_decode($response->getBody());

        } catch (ClientException $e) {
            return json_decode($e->getResponse()->getBody(true));
        }
    }
}