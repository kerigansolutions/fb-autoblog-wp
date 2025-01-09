<?php

namespace KeriganSolutions\FacebookFeed\Fetchers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use KeriganSolutions\FacebookFeed\Contracts\DataFetcher;

class EventsFetcher implements DataFetcher
{
    const EVENTS = 'description,end_time,name,place,start_time,cover,event_times,is_online,is_draft,is_canceled,guest_list_enabled,online_event_format,online_event_third_party_url,scheduled_publish_time,ticket_uri,ticket_uri_start_sales_time,ticketing_privacy_uri,ticketing_terms_uri,timezone,updated_time,attending_count,declined_count,interested_count,category,created_time,id,is_page_owned,maybe_count,noreply_count,owner,parent_group,type,comments,feed,videos,ticket_tiers,posts.limit(0),picture,photos.limit(10),roles.limit(10),live_videos,discount_code_enabled,can_guests_invite';
    const EVENT_PHOTO = 'photos{images}';

    protected $client;
    protected $accessToken;
    protected $pageId;


    public function __construct($accessToken, $pageId)
    {
        $this->client = new Client(['base_uri' => 'https://graph.facebook.com/v21.0']);
        $this->accessToken = $accessToken;
        $this->pageId      = $pageId;
    }

    public function get($limit, $before, $after)
    {
        try {
            $response = $this->client->request(
                'GET',
                '/' . $this->pageId .
                '/events/?fields=' . self::EVENTS .
                '&since=' . strtotime('-1 year') .
                '&limit=' . $limit .
                '&before=' . $before .
                '&after=' . $after .
                '&access_token=' . $this->accessToken
            );

            $feed = json_decode($response->getBody());

            return $feed;
        } catch (\Exception $e) {
            // Most likely a bad token or improperly formatted request
            echo $e->getMessage();
            echo '<p>This content is currently unavailable due to an error.</p>';
        }
    }

    public function getEventPhoto($eventId)
    {
        try {
            $response = $this->client->request(
                'GET',
                $eventId .
                '?fields=' . self::EVENT_PHOTO .
                '&access_token=' . $this->accessToken
            );

            return json_decode($response->getBody())->photos->data[0]->images[0]->source;

        } catch (ClientException $e) {
            // Most likely a bad token or improperly formatted request
            echo $e->getMessage();
            echo '<p>This content is currently unavailable due to an error.</p>';
        }
    }
}