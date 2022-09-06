<?php

namespace KeriganSolutions\FacebookFeed\WP;

use KeriganSolutions\FacebookFeed\FacebookEvents;
use Illuminate\Support\Carbon;

class FacebookEvent extends FacebookObject {

  public $postType = 'kma-fb-event';
  public $singularName = 'Facebook Event';
  public $pluralName = 'Facebook Events';
  public $enabled = false;

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
    $post->event_times = get_post_meta($post->ID, 'event_times', true);

    return $post;
  }

  /**
   * Transform the object to our liking for storage in WP schema
   */
  public function transform ($input)
  {
    $startDateTime = Carbon::parse($input->start_time);
    $endDateTime = isset($input->end_time) ? Carbon::parse($input->end_time) : $startDateTime;

    $event_times = [];
    $sortDate = $startDateTime->format('YmdHi');

    if(isset($input->event_times)){
      foreach($input->event_times as $time){
        if(date('YmdHi',strtotime($time->start_time)) >= date('YmdHi')) {
          array_unshift($event_times, $time);
        }
      }

      if(isset($event_times[0])){
        $sortDate = date('YmdHi', strtotime($event_times[0]->end_time));
      }
    }

    $output = [
      'ID' => 0,
      'post_content' => isset($input->description) ? $input->description : null,
      'post_title' => $input->id,
      'post_status' => 'publish',
      'post_type' => 'kma-fb-event',
      'post_date' => date('Y-m-d H:i:s', strtotime("-1 hour")),
      'meta_input' => [
        'is_canceled' => $input->is_canceled,
        'is_draft' => $input->is_draft,
        'start' => $startDateTime->copy()->format('YmdHi'),
        'end' => $endDateTime ? $endDateTime->copy()->format('YmdHi') : null,
        'datestamp' => $sortDate,
        'event_name' => $input->name,
        'event_link' => 'https://www.facebook.com/events/' . $input->id,
        'where' => isset($input->place) ? $input->place->name : null,
        'full_image_url' => isset($input->cover) ? $input->cover->source : null,
        'event_date' => $endDateTime != null && $endDateTime->diffInDays($startDateTime) > 1 ? $startDateTime->copy()->format('M d') . ' - '. $endDateTime->copy()->format('M d, Y') : $startDateTime->format('M d, Y'),
        'event_time' => $endDateTime != null ? $startDateTime->copy()->format('g:i A') . ' - '. $endDateTime->copy()->format('g:i A') : $startDateTime->copy()->format('g:i A'),
        'event_times' => $event_times
      ]
    ];

    // print_r($output);
    // die();

    return $output;
  }

  /**
   * Contact the specified Facebook Service we need
   */
  public function service()
  {
    return new FacebookEvents($this->facebookPageID, $this->facebookToken);
  }

}
