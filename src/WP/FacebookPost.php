<?php

namespace KeriganSolutions\FacebookFeed\WP;

use KeriganSolutions\FacebookFeed\FacebookFeed;
use Illuminate\Support\Carbon;

class FacebookPost extends FacebookObject {

  public $postType = 'kma-fb-post';
  public $singularName = 'Facebook Post';
  public $pluralName = 'Facebook Posts';
  public $enabled = false;
  public $syncNum = 4;

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
    $post->actions = get_post_meta($post->ID, 'actions', true);
    $post->is_hidden = get_post_meta($post->ID, 'is_hidden', true);
    $post->is_expired = get_post_meta($post->ID, 'is_expired', true);
    $post->is_published = get_post_meta($post->ID, 'is_published', true);
    $post->permalink_url = get_post_meta($post->ID, 'permalink_url', true);
    $post->timeline_visibility = get_post_meta($post->ID, 'timeline_visibility', true);
    $post->updated_time = get_post_meta($post->ID, 'updated_time', true);
    $post->comments = get_post_meta($post->ID, 'comments', true);
    $post->from = get_post_meta($post->ID, 'from', true);
    $post->fb_id = get_post_meta($post->ID, 'fb_id', true);
    $post->is_popular = get_post_meta($post->ID, 'is_popular', true);
    $post->is_spherical = get_post_meta($post->ID, 'is_spherical', true);
    $post->shares = get_post_meta($post->ID, 'shares', true);
    $post->message = get_post_meta($post->ID, 'message', true);
    $post->parent_id = get_post_meta($post->ID, 'parent_id', true);
    $post->privacy = get_post_meta($post->ID, 'privacy', true);
    $post->promotable_id = get_post_meta($post->ID, 'promotable_id', true);
    $post->promotion_status = get_post_meta($post->ID, 'promotion_status', true);
    $post->subscribed = get_post_meta($post->ID, 'subscribed', true);
    $post->diff = get_post_meta($post->ID, 'diff', true);
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

    $media = isset($media->attachments->data[0]) ? $input->attachments->data[0] : false;
    $output['meta_input']['attachments'] = isset($input->attachments->data) ? $input->attachments->data : '';
    $output['meta_input']['actions'] = isset($input->actions) ? $input->actions : '';
    $output['meta_input']['status_type'] = isset($input->status_type) ? $input->status_type : '';
    $output['meta_input']['is_hidden'] = isset($input->is_hidden) ? $input->is_hidden : '';
    $output['meta_input']['is_expired'] = isset($input->is_expired) ? $input->is_expired : '';
    $output['meta_input']['is_published'] = isset($input->is_published) ? $input->is_published : '';
    $output['meta_input']['permalink_url'] = isset($input->permalink_url) ? $input->permalink_url : '';
    $output['meta_input']['timeline_visibility'] = isset($input->timeline_visibility) ? $input->timeline_visibility : '';
    $output['meta_input']['updated_time'] = isset($input->updated_time) ? $input->updated_time : '';
    $output['meta_input']['comments'] = isset($input->comments) ? $input->comments : '';
    $output['meta_input']['from'] = isset($input->from) ? $input->from : '';
    $output['meta_input']['fb_id'] = isset($input->id) ? $input->id : '';
    $output['meta_input']['is_popular'] = isset($input->is_popular) ? $input->is_popular : '';
    $output['meta_input']['is_spherical'] = isset($input->is_spherical) ? $input->is_spherical : '';
    $output['meta_input']['shares'] = isset($input->shares) ? $input->shares : '';
    $output['meta_input']['message'] = isset($input->message) ? $input->message : '';
    $output['meta_input']['parent_id'] = isset($input->parent_id) ? $input->parent_id : '';
    $output['meta_input']['privacy'] = isset($input->privacy) ? $input->privacy : '';
    $output['meta_input']['promotable_id'] = isset($input->promotable_id) ? $input->promotable_id : '';
    $output['meta_input']['promotion_status'] = isset($input->promotion_status) ? $input->promotion_status : '';
    $output['meta_input']['subscribed'] = isset($input->subscribed) ? $input->subscribed : '';
    $output['meta_input']['diff'] = isset($input->diff) ? $input->diff : '';
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

    // print_r($output);
    // die();

    return $output;
  }

  /**
   * Format async json response
   */
  public function sync ($request)
  {
    $num = $request->get_param('num');
    $this->getRemote($num ?? $this->syncNum);
  }

}
