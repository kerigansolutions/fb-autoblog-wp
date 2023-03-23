<?php

namespace KeriganSolutions\FacebookFeed\WP;

use KeriganSolutions\FacebookFeed\FacebookEvents;
use Illuminate\Support\Carbon;
use stdClass;

class FacebookEvent extends FacebookObject {

  public $postType = 'kma-fb-event';
  public $singularName = 'Facebook Event';
  public $pluralName = 'Facebook Events';
  public $enabled = false;
  public $syncNum = 100;

  /**
   * format fields used in our view (the front-end)
   */
  public function fields ($post)
  {
    $post->is_canceled = get_post_meta($post->ID, 'is_canceled', true);
    $post->is_draft = get_post_meta($post->ID, 'is_draft', true);
    $post->datestamp = get_post_meta($post->ID, 'datestamp', true);
    $post->event_name = get_post_meta($post->ID, 'event_name', true);
    $post->event_link = get_post_meta($post->ID, 'event_link', true);
    $post->where = get_post_meta($post->ID, 'where', false);
    $post->full_image_url = get_post_meta($post->ID, 'full_image_url', true);
    $post->event_date = get_post_meta($post->ID, 'event_date', true);
    $post->event_time = get_post_meta($post->ID, 'event_time', true);

    return $post;
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

    // what time is it right now using WP timezone setting
    $now = Carbon::now()->setTimezone(wp_timezone_string());
    $yesterday = Carbon::now()->subDay();

    // loop through event times and save one for each
    if(isset($object->event_times)){

      foreach($object->event_times as $time){
        if(Carbon::parse($time->start_time)->setTimezone(wp_timezone_string())->format('YmdHi') >= $now->format('YmdHi')) {

          $object->start_time = $time->start_time;
          $object->end_time = $time->end_time;

          $object->start_object = Carbon::parse($object->start_time)->setTimezone(wp_timezone_string());
          $object->end_object = isset($object->end_time) ? Carbon::parse($object->end_time)->setTimezone(wp_timezone_string()) : null;
          $object->sort_date = (int) $object->start_object->format('YmdHi');

          $postArray = $this->transform($object);
          $postExists = $this->getPageBySlug($postArray['post_name']);

          // If exists, update the post. Otherwise, add a new one
          if(isset($postExists->ID)){

            // Catch cancelled events that were added already
            if((isset($object->is_canceled) && $object->is_canceled) || $object->sort_date < $yesterday->format('YmdHi')){
              wp_delete_post($postExists->ID);
            }else{
              $postArray['ID'] = $postExists->ID;
              wp_update_post($postArray);
            }

          }else{
            if((!isset($object->is_canceled) || !$object->is_canceled) && $object->sort_date > $yesterday->format('YmdHi')){
                wp_insert_post($postArray);
            }
          }

        }
      }

    // singular event with one time
    } else {

      $object->start_object = Carbon::parse($object->start_time)->setTimezone(wp_timezone_string());
      $object->end_object = isset($object->end_time) ? Carbon::parse($object->end_time)->setTimezone(wp_timezone_string()) : null;
      $object->sort_date = (int) $object->start_object->format('YmdHi');

      $postArray = $this->transform($object);
      $postExists = $this->getPageBySlug($postArray['post_name']);

      // If exists, update the post. Otherwise, add a new one
      if(isset($postExists->ID)){

        // Catch cancelled events that were added already
        if((isset($object->is_canceled) && $object->is_canceled) || $object->sort_date < $yesterday->format('YmdHi')){
          wp_delete_post($postExists->ID);
        }else{
          $postArray['ID'] = $postExists->ID;
          wp_update_post($postArray);
        }

      }else{
        if((!isset($object->is_canceled) || !$object->is_canceled) && $object->sort_date > $yesterday->format('YmdHi')){
            wp_insert_post($postArray);
        }
      }

    }

  }

  public function getPageBySlug($slug)
  {
    $post = get_posts([ 'post_type' => $this->postType, 'name' => $slug ]);
    return is_array($post) && count($post) > 0 ? $post[0] : false;
  }

  /**
   * Transform the object to our liking for storage in WP schema
   */
  public function transform ($input)
  {
    $output = [
      'ID' => 0,
      'post_content' => isset($input->description) ? $input->description : null,
      'post_title' => $input->name . ' @ ' . $input->start_object->format('M d, Y'),
      'post_name' => $this->slugify($input->name . '-' . $input->sort_date),
      'post_status' => 'publish',
      'post_type' => $this->postType,
      'meta_input' => [
        'is_canceled' => $input->is_canceled,
        'is_draft' => $input->is_draft,
        'start' => $input->start_object->copy()->format('YmdHi'),
        'end' => $input->end_object ? $input->end_object->copy()->format('YmdHi') : null,
        'datestamp' => $input->sort_date,
        'event_name' => $input->name,
        'event_link' => 'https://www.facebook.com/events/' . $input->id,
        'where' => isset($input->place) ? $input->place->name : null,
        'full_image_url' => isset($input->cover) ? $input->cover->source : null,
        'event_date' => $input->end_object != null && $input->end_object->diffInDays($input->start_object) > 1 ? $input->start_object->copy()->format('M d') . ' - '. $input->end_object->copy()->format('M d, Y') : $input->start_object->format('M d, Y'),
        'event_time' => $input->end_object != null ? $input->start_object->copy()->format('g:i A') . ' - '. $input->end_object->copy()->format('g:i A') : $input->start_object->copy()->format('g:i A'),
      ]
    ];

    return $output;
  }

  public function slugify($text, string $divider = '-')
  {
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, $divider);

    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }

  /**
   * Contact the specified Facebook Service we need
   */
  public function service()
  {
    return new FacebookEvents($this->facebookPageID, $this->facebookToken);
  }

  /**
   * Format async json response
   */
  public function sync ($request)
  {
    $num = $request->get_param('num');
    $this->getRemote($num ? $num : 100);
  }

}
