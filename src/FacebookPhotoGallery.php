<?php
namespace KeriganSolutions\FacebookFeed;

use GuzzleHttp\Client;

class FacebookPhotoGallery
{
    protected $client;
    protected $pageId;
    protected $accessToken;

    public function __construct($pageId, $accessToken)
    {
        $this->pageId      = $pageId;
        $this->accessToken = $accessToken;
        $this->client      = new Client(['base_uri' => 'https://graph.facebook.com/v13.0/']);
    }
    
    public function albums($limit = null, $before = null, $after = null)
    {
        $fields = 'id,name,link,created_time,cover_photo{picture,images.limit(1)}';

        $response = $this->client->request(
            'GET',
            $this->pageId .
            '/albums?fields=' . $fields .
            '&before=' . $before .
            '&after=' . $after .
            '&limit=' . $limit .
            '&access_token=' . $this->accessToken
        );

        $results = json_decode($response->getBody());

        return $results;
    }

    public function albumPhotos($albumId, $limit = null, $before = null, $after = null)
    {
        $fields = 'images,name';

        $response = $this->client->request(
            'GET',
            $albumId .
            '/photos?fields=' . $fields .
            '&before=' . $before .
            '&after=' . $after .
            '&limit=' . $limit .
            '&access_token=' . $this->accessToken
        );

        $results = json_decode($response->getBody());

        return $results;
    }
}
