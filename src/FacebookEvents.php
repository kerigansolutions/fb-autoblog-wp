<?php

namespace KeriganSolutions\FacebookFeed;

use GuzzleHttp\Client;
use KeriganSolutions\FacebookFeed\Feed\Feed;
use KeriganSolutions\FacebookFeed\Fetchers\FacebookRequest;
use KeriganSolutions\FacebookFeed\Fetchers\EventsFetcher;
use KeriganSolutions\FacebookFeed\Parsers\EventParser;

class FacebookEvents
{
    public $pageId;
    public $accessToken;

    public function __construct($pageId, $accessToken)
    {
        $this->client = new Client(['base_uri' => 'https://graph.facebook.com/v13.0']);
        $this->accessToken = $accessToken;
        $this->pageId      = $pageId;
    }

    public function fetch($limit = 5, $before = null, $after = null)
    {
        $facebook = new FacebookRequest($limit, $before, $after);
        $events   = new EventsFetcher($this->accessToken, $this->pageId);
        $response = $facebook->fetch($events);
        $feed     = new Feed(new EventParser(), $response);

        return $feed->output();
    }
}
