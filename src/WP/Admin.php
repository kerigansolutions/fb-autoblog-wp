<?php

namespace KeriganSolutions\FacebookFeed\WP;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Admin
{
  protected $facebookPageID;
  protected $facebookToken;
  protected $appId;
  protected $appSecret;

  public $postsEnabled = false;
  public $eventsEnabled = false;
  public $photosEnabled = false;
  public $reviewsEnabled = false;

  public function __construct()
  {
    $this->facebookPageID = get_option('facebook_page_id');
    $this->facebookToken = get_option('facebook_token');
    $this->appSecret = get_option('facebook_app_secret');
    $this->appId = '353903931781568';

    $this->postsEnabled = env('FB_POSTS_ENABLED', $this->postsEnabled);
    $this->eventsEnabled = env('FB_EVENTS_ENABLED', $this->eventsEnabled);
    $this->photosEnabled = env('FB_PHOTOS_ENABLED', $this->photosEnabled);
    $this->reviewsEnabled = env('FB_REVIEWS_ENABLED', $this->reviewsEnabled);
  }

  /**
   * Get a token from Facebook to authorize the app
   */
  public function exchangeToken($request)
  {
    $token = $request->get_param( 'token' );
    $client = new Client();

    try {
      $response = $client->request('GET',
      'https://graph.facebook.com/v12.0/oauth/access_token?' .
      'grant_type=fb_exchange_token&' .
      'client_id=' . $this->appId . '&' .
      'client_secret=' . $this->appSecret . '&' .
      'fb_exchange_token=' . $token );
    } catch (RequestException $e) {
      echo $e->getResponse()->getBody(true);
    }

    return rest_ensure_response(json_decode($response->getBody()));
  }

  public function getAppToken()
  {
    $client = new Client();

    try {
      $response = $client->request('GET',
      'https://graph.facebook.com/oauth/access_token?' .
      'grant_type=client_credentials&' .
      'client_id=' . $this->appId . '&' .
      'client_secret=' . $this->appSecret );
    } catch (RequestException $e) {
      echo $e->getResponse()->getBody(true);
    }

    return json_decode($response->getBody())->access_token;
  }

  public function getTokenExpiryDate()
  {
    if($this->facebookToken == ''){
      return false;
    }

    $appToken = $this->getAppToken();
    $client = new Client();

    try {
      $response = $client->request('GET',
      'https://graph.facebook.com/debug_token?' .
      'input_token=' . $this->facebookToken . '&' .
      'access_token=' . $appToken );
    } catch (RequestException $e) {
      echo $e->getResponse()->getBody(true);
    }

    $data = json_decode($response->getBody())->data;
    return $data->expires_at;
  }

  /**
   * Add Options page so we can start changing stuff
   */
  public function use()
  {
    add_action('admin_menu', [$this,'addMenus']);
    add_action('rest_api_init', [$this,'addRoutes']);

    if($this->postsEnabled) {
      (new FacebookPost())->use();
    }

    if($this->eventsEnabled) {
      (new FacebookEvent())->use();
    }

    $this->mount();
  }

  public function mount()
  {
    // for extending the app
  }

  /**
   * Add endpoint for token renewal
   */
  public function addRoutes()
  {
    register_rest_route( 'kerigansolutions/v1', '/autoblogtoken',
      [
        'methods'  => 'GET',
        'callback' => [ $this, 'exchangeToken' ],
        'permission_callback' => '__return_true'
      ]
    );
  }

  /**
   * Add our options page to the menu
   */
  public function addMenus()
  {
    add_menu_page("Facebook Settings", "Facebook Settings", "administrator", 'kma-facebook', function () {
      include(wp_normalize_path( dirname(dirname(dirname(__FILE__))) . '/dist/AdminOverview.php'));
    }, "dashicons-admin-generic");
  }

}
