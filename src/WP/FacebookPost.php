<?php

namespace KeriganSolutions\FacebookFeed\WP;

use KeriganSolutions\FacebookFeed\FacebookFeed;
use Illuminate\Support\Carbon;

class FacebookPost extends FacebookObject {

  public $postType = 'kma-fb-post';
  public $singularName = 'Facebook Post';
  public $pluralName = 'Facebook Posts';
  public $enabled = false;

  /**
   * Contact the specified Facebook Service we need
   */
  public function service()
  {
    return new FacebookFeed($this->facebookPageID, $this->facebookToken);
  }

  /**
   * format fields used in our view (the front-end)
   */
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
    $post->album = get_post_meta($post->ID, 'subattachments', true);

    return $post;
  }

  /**
   * Transform the object to our liking for storage in WP schema
   */
  public function transform ($input)
  {
    // print_r($input);

    $output = [
      'ID' => 0,
      'post_date' => Carbon::parse($input->created_time)->copy()->setTimezone(wp_timezone_string())->format('Y-m-d H:i:s'),
      'post_content' => $input->message,
      'post_title' => $input->id,
      'post_status' => 'publish',
      'post_type' => $this->postType,
      'meta_input' => [
        'post_link' => isset($input->permalink_url) ? $input->permalink_url : '',
        'full_image_url' => isset($input->full_picture) ? $input->full_picture : '',
        'status_type' => isset($input->status_type) ? $input->status_type : '',
      ]
    ];

    if(isset($input->attachments)){
      $media = $input->attachments->data[0];
      $output['meta_input']['attachments'] = $input->attachments->data;
      $output['meta_input']['target_url'] = isset($media->target->url ) ? $media->target->url : '';
      $output['meta_input']['image_src'] = isset($media->media->image->src ) ? $media->media->image->src : '';
      $output['meta_input']['description'] = isset($media->description ) ? $media->description : '';
      $output['meta_input']['media_type'] = isset($media->media_type ) ? $media->media_type : '';
      $output['meta_input']['type'] = isset($media->type ) ? $media->type : '';
      $output['meta_input']['title'] = isset($media->title ) ? $media->title : '';
      $output['meta_input']['url'] = isset($media->url) ? $media->url : $input->permalink_url;
      $output['meta_input']['unshimmed_url'] = isset($media->unshimmed_url) ? $media->url : $input->permalink_url;
      $output['meta_input']['subattachments'] = [];

      if(isset($media->subattachments) && is_array($media->subattachments->data)){
        foreach($media->subattachments->data as $attachment){
          if($attachment->type == 'photo'){
            $output['meta_input']['subattachments'][] = [
              'type' => $attachment->type,
              'src'  => $attachment->media->image->src,
              'width' => $attachment->media->image->width,
              'height' => $attachment->media->image->height,
              'url' => $attachment->url,
            ];
          }
        }
      }
    }

    // print_r($output);
    // die();

    return $output;
  }

}
