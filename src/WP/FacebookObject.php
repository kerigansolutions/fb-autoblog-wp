<?php

namespace KeriganSolutions\FacebookFeed\WP;

use KeriganSolutions\FacebookFeed\FacebookFeed;
use KeriganSolutions\FacebookFeed\FacebookReviews;
use KeriganSolutions\FacebookFeed\FacebookPhotoGallery;
use KeriganSolutions\FacebookFeed\FacebookEvents;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Carbon;
use stdClass;

/**
 * This file is not intended to be used directly. Rather, it is meant to be
 * extended for each object type that we want to sync form Facebook. Functions
 * that are used by all object types should live in this file so they can
 * be extended in each object's specific file.
 */

class FacebookObject {

  protected $facebookPageID;
  protected $facebookToken;
  protected $appSecret;
  protected $appId = '353903931781568';

  public $postType = 'kma-fb-object';
  public $singularName = 'Facebook Object';
  public $pluralName = 'Facebook Objects';
  public $shortcode = 'fbobject';
  public $enabled = false;

  /**
   * Get secrets and stuff from WordPress schema
   */
  public function __construct()
  {
      $this->facebookPageID = get_option('facebook_page_id');
      $this->facebookToken = get_option('facebook_token');
      $this->appSecret = get_option('facebook_app_secret');
  }

  public function fields ($post)
  {
    $post->post_link = get_post_meta($post->ID, 'post_link', true);
    $post->full_image_url = get_post_meta($post->ID, 'full_image_url', true);
    $post->attachments = get_post_meta($post->ID, 'attachments', true);
    $post->type = get_post_meta($post->ID, 'type', true);
    $post->status_type = get_post_meta($post->ID, 'status_type', true);
    $post->target_url = get_post_meta($post->ID, 'target_url', true);
    $post->image_src = get_post_meta($post->ID, 'image_src', true);
    $post->description = get_post_meta($post->ID, 'description', true);
    $post->media_type = get_post_meta($post->ID, 'media_type', true);
    $post->title = get_post_meta($post->ID, 'title', true);
    $post->url = get_post_meta($post->ID, 'url', true);
    $post->unshimmed_url = get_post_meta($post->ID, 'unshimmed_url', true);

    return $post;
  }

  /**
   * Get object out of schema and send to front-end
   */
  public function query ($num, $args = [])
  {
    $request = [
      'posts_per_page' => $num,
      'offset'         => 0,
      'order'          => 'DESC',
      'orderby'        => 'date_posted',
      'post_type'      => $this->postType,
      'post_status'    => 'publish',
    ];

    $request   = array_merge($request, $args);
    $postArray = get_posts($request);

    $output = [];
    foreach($postArray as $post){
      $output[] = $this->fields($post);
    }
    return $output;
  }

  /**
   * Format async json response
   */
  public function sync ($request)
  {
    $num = $request->get_param('num');
    $this->getRemote($num ?? 30);
  }

  /**
   * Transform the object to our liking
   */
  public function transform ($input)
  {

    $output = [
      'ID' => 0,
      'post_date' => Carbon::parse($input->created_time)->copy()->setTimezone('America/New_York')->format('Y-m-d H:i:s'),
      'post_content' => '',
      'post_title' => $input->id,
      'post_status' => 'publish',
      'post_type' => $this->postType,
    ];

    return $output;
  }

  /**
   * Save the object to local schema
   */
  public function save ($object)
  {

    // mobile update or event with no content
    if(isset($object->status_type) && ($object->status_type == 'mobile_status_update' && $object->message == '') || isset($object->status_type) && $object->status_type == 'created_event'){
      return null; // silently discard
    }

    $postArray = $this->transform($object);
    $postExists = get_page_by_title($object->id, OBJECT, $this->postType);

    // If exists, update the post. Otherwise, add a new one
    if(isset($postExists->ID)){

      // Catch cancelled events that were added already
      if(isset($object->is_canceled) && $object->is_canceled){
        wp_delete_post($postExists->ID);
      }else{
        $postArray['ID'] = $postExists->ID;
        wp_update_post($postArray);
      }

    }else{
      if(!isset($object->is_canceled) || !$object->is_canceled){
          wp_insert_post($postArray);
      }
    }

  }

  /**
   * Contact the specified Facebook Service we need
   */
  public function service()
  {
    return new FacebookFeed($this->facebookPageID,$this->facebookToken);
  }

  /**
   * Contact Facebook's Graph API utilizing our composer package
   * for all the heavy lifting.
   */
  public function getRemote ($num = 4)
  {
    try{
      $feed = $this->service();
      $response = $feed->fetch($num);
    }catch(RequestException $e){
      $response = $e->getResponse()->getBody(true);
    }

    if($response->posts){
      foreach($response->posts as $object){
        $this->save($object);
      }

      wp_send_json_success();
    } else {
      wp_send_json_error();
    }

  }

  /**
   * Schedule a cron to keep things updated
   */
  public function cron()
  {
    if(! wp_next_scheduled('sync-' . $this->postType)){
      wp_schedule_event(
        strtotime('01:00:00'),
        'hourly',
        'sync-' . $this->postType
      );
    }

    add_action('sync-' . $this->postType, [$this,'getRemote']);
  }

  /**
   * For getting the Facebook resource asynchronously
   */
  public function endpoint ()
  {

    // Gets resource from facebook
    register_rest_route( 'kerigansolutions/v1', '/sync-' . $this->postType,
      [
        'methods'  => 'GET',
        'callback' => [ $this, 'sync' ],
        'permission_callback' => '__return_true'
      ]
    );

    // Gets resource from local schema
    register_rest_route( 'kerigansolutions/v1', '/get-' . $this->postType,
      [
        'methods'  => 'GET',
        'callback' => [ $this, 'collection' ],
        'permission_callback' => '__return_true'
      ]
    );
  }

  public function collection () {
    return rest_ensure_response($this->query(-1));
  }

  /**
   * Creates a post type for our object
   */
  public function createPostType()
  {
    register_post_type( $this->postType,
    array(
      'labels' => array(
        'name' => __( $this->pluralName ),
        'singular_name' => __( $this->singularName )
      ),
      'supports' => ['title','custom-fields'],
      'public' => $this->enabled,
      'has_archive' => false,
      'rewrite' => false,
      'exclude_from_search' => true,
      'publicly_queryable' => false,
      'show_in_menu' => $this->enabled,
      'show_in_rest' => false,
      'menu_icon' => 'dashicons-facebook',
    ));
  }

  /**
   * Create a shortcode so we can pull these modules into content areas
   */
  public function shortcodeFunction ($atts)
  {
    return null;
  }

  /**
   * Enables the object in our environment
   */
  public function use ()
  {
    $this->enabled = true;

    add_action( 'rest_api_init', [$this,'endpoint'] );
    add_action( 'init', [$this,'createPostType'], 50 );
    add_shortcode( $this->shortcode, 'shortcodeFunction' );

    $this->cron();
  }

}

?>
