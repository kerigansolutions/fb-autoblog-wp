<?php

namespace KeriganSolutions\FacebookFeed\Fetchers;

use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use KeriganSolutions\FacebookFeed\Contracts\DataFetcher;

class PostFetcher implements DataFetcher
{
    const FEED = 'permalink_url,description,full_picture,message,object_id,type,status_type,caption,created_time,link,attachments{target,media}';

    protected $client;
    protected $pageId;
    protected $accessToken;

    public function __construct($accessToken, $pageId)
    {
        $this->client = new Client(['base_uri' => 'https://graph.facebook.com/v2.11']);
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
            // Most likely a bad token or improperly formatted request
            echo $e->getMessage();
            echo '<p>This content is currently unavailable due to an error.</p>';
        }
    }
}