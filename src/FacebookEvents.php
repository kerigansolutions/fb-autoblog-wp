<?php

namespace KeriganSolutions\FacebookFeed;

use GuzzleHttp\Client;
use KeriganSolutions\FacebookFeed\Feed\Feed;
use KeriganSolutions\FacebookFeed\Fetchers\FacebookRequest;
use KeriganSolutions\FacebookFeed\Fetchers\EventsFetcher;
use KeriganSolutions\FacebookFeed\Parsers\EventParser;

class FacebookEvents
{
    public function fetch($limit = 5, $before = null, $after = null)
    {
        $facebook = new FacebookRequest($limit, $before, $after);
        $events   = new EventsFetcher();
        $response = $facebook->fetch($events);
        $feed     = new Feed(new EventParser(), $response);

        return $feed->output();
    }
}
