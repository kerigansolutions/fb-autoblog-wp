<?php

namespace KeriganSolutions\FacebookFeed\Fetchers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use KeriganSolutions\FacebookFeed\Contracts\DataFetcher;

class PostFetcher implements DataFetcher
{
    const FEED = 'permalink_url,full_picture,message,status_type,created_time,attachments.limit(10){target,media,description,description_tags,media_type,title,type,unshimmed_url,url,subattachments},event,likes{id,name,pic_square,pic_large,link},actions,child_attachments,is_hidden,is_expired,is_published,properties,shares,story';

    protected $client;
    protected $pageId;
    protected $accessToken;

    public function __construct($accessToken, $pageId)
    {
        $this->client = new Client(['base_uri' => 'https://graph.facebook.com/v13.0']);
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