<?php
namespace KeriganSolutions\FacebookFeed;

/**
 * @version 1.0.0
 */

use KeriganSolutions\FacebookFeed\Feed\Feed;
use KeriganSolutions\FacebookFeed\Fetchers\ReviewsFetcher;
use KeriganSolutions\FacebookFeed\Fetchers\FacebookRequest;
use KeriganSolutions\FacebookFeed\Parsers\ReviewParser;

class FacebookReviews
{
    public $pageId;
    public $accessToken;

    public function __construct($pageId, $accessToken)
    {
        $this->accessToken = $accessToken;
        $this->pageId      = $pageId;
    }

    /**
     * @param int $limit The number of posts to display
     * @param string $before The cursor before the data
     * @param string $after The cursor after the data
     *
     * @return KeriganSolutions\FacebookFeed\Feed
     */
    public function fetch($limit = 5, $before = null, $after = null)
    {
        $facebook = new FacebookRequest($limit, $before, $after);
        $reviews  = new ReviewsFetcher($this->accessToken, $this->pageId);
        $response = $facebook->fetch($reviews);
        $parser   = new ReviewParser();
        $feed     = new Feed($parser, $response);

        return $feed->output();
    }
}
