<?php

namespace KeriganSolutions\FacebookFeed\Fetchers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use KeriganSolutions\FacebookFeed\Contracts\DataFetcher;

class PostFetcher implements DataFetcher
{
    const FEED = 'permalink_url,full_picture,message,status_type,created_time,attachments.limit(10){target,media,description,description_tags,media_type,title,type,unshimmed_url,url,subattachments},event,likes{id,name,pic_square,pic_large,link},actions,child_attachments,is_hidden,is_expired,is_published,properties,shares,place,story,story_tags,scheduled_publish_time,target,timeline_visibility,updated_time,comments,sharedposts.limit(10){is_expired,is_hidden,is_published,message,name,message_tags,shares,source,status_type,story,story_tags,subscribed,target,properties,place,link,permalink_url,attachments.limit(10){description_tags,description,media,media_type,target,title,type,subattachments,unshimmed_url,url},actions,type,timeline_visibility,privacy,id,full_picture,from,event,description,coordinates,backdated_time,admin_creator,sharedposts{message}},admin_creator,multi_share_end_card,multi_share_optimized,via,call_to_action,from,to{picture,can_post,id,link,name,pic,pic_crop,pic_large,pic_small,pic_square,profile_type,username},id,dynamic_posts{child_attachments,created,description,id,image_url,link,message,owner_id,place_id,product_id,title,comments,likes},feed_targeting,is_inline_created,is_popular,is_spherical,message_tags,parent_id,privacy,promotable_id,reactions{can_post,id,link,name,pic,pic_crop,pic_large,pic_small,pic_square,profile_type,type,username,picture},promotion_status,subscribed,targeting,video_buying_eligibility,width,insights,sponsor_tags,icon,height,expanded_width,expanded_height';

    protected $client;
    protected $pageId;
    protected $accessToken;

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