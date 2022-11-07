<?php

use KeriganSolutions\FacebookFeed\WP\Admin;

$facebook = new Admin();

if (!function_exists('get_mix_asset')) {
  function get_mix_asset($path)
  {
    $manifestDirectory = wp_normalize_path(dirname(__FILE__) . '/mix-manifest.json');

    if (file_exists($manifestDirectory)) {
      $manifest = json_decode(file_get_contents($manifestDirectory), true);
      if (array_key_exists($path, $manifest)) {
        return $manifest[$path];
      }
    }
    return $path;
  }
}

$tokenExpires    = $facebook->getTokenExpiryDate();
$FacebookPageID  = (isset($_POST['facebook_page_id']) ? sanitize_text_field($_POST['facebook_page_id']) : get_option('facebook_page_id'));
$FacebookToken   = (isset($_POST['facebook_token']) ? sanitize_text_field($_POST['facebook_token']) : get_option('facebook_token'));
$FacebookSecret  = (isset($_POST['facebook_app_secret']) ? sanitize_text_field($_POST['facebook_app_secret']) : get_option('facebook_app_secret'));

if (isset($_POST['facebook_submit_settings'])) {
    update_option('facebook_page_id',
        isset($_POST['facebook_page_id']) ? sanitize_text_field($_POST['facebook_page_id']) : $FacebookPageID);
    update_option('facebook_token',
        isset($_POST['facebook_token']) ? sanitize_text_field($_POST['facebook_token']) : $FacebookToken);
}

if (isset($_POST['facebook_submit_secret_settings']) && $_POST['facebook_submit_secret_settings'] == 'yes') {
    update_option('facebook_app_secret',
        isset($_POST['facebook_app_secret']) ? sanitize_text_field($_POST['facebook_app_secret']) : $FacebookSecret);
}

?>
<link href="/styles<?php echo get_mix_asset('/facebook-admin.css' ); ?>" rel="stylesheet">
<div id="kma-facebook-settings" class="text-base" style="margin-left:-20px;">
  <div class="p-8 lg:p-12">
    <h1 class="font-bold text-xl lg:text-4xl text-primary">
      Facebook Settings
    </h1>
  </div>
  <div class="section px-8 pb-8 lg:px-12">
    <div class="grid grid-cols-12 gap-4 lg:gap-8">

      <!-- Needs App Secret -->
      <?php if(!get_option('facebook_app_secret')){ ?>
      <div class="col-span-12 p-8 bg-white shadow-lg shadow-primary/20" >
        <p id="secret-headline" class="text-gray-600 text-2xl mb-4">Assign App Secret</p>
        <p class="is-small">If you don't have this, ask your developer.</p>

        <form
          enctype="multipart/form-data"
          name="facebook_secret_settings"
          id="facebook_secret_settings"
          method="post"
          action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>"
        >
          <input type="hidden" name="facebook_submit_secret_settings" value="yes">
          <div class="input-wrapper">
            <input
              type="text"
              class="text-input px-4"
              name="facebook_app_secret"
              id="facebook_app_secret"
              value="<?= $FacebookSecret; ?>"
            >
            <button class="form-button bg-primary hover:bg-white border-2 border-transparent hover:border-primary text-white hover:text-primary rounded" >
              Save
            </button>
          </div>
        </form>

      </div>
      <?php } ?>

      <!-- Request a token -->
      <div class="col-span-12 p-8 bg-white shadow-lg shadow-primary/20" >
        <facebook-auth>
          <?php get_option('facebook_token') ? _e('Renew Authorization') : _e('Authorize App') ?>
        </facebook-auth>
      </div>

      <div class="col-span-12 p-8 bg-white shadow-lg shadow-primary/20" >

        <div id="accountoptions" class="columns is-multiline"></div>
        <div id="error"></div>

        <form
          enctype="multipart/form-data"
          name="facebook_settings"
          id="facebook_settings"
          method="post"
          action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>"
        >
          <input type="hidden" name="facebook_submit_settings" value="yes">
          <div class="grid md:grid-cols-2 gap-4 lg:gap-8 pb-8">
            <div>
              <p class="text-gray-400 text uppercase font-bold mb-2">Facebook Page ID</p>
              <div class="input-wrapper">
                <input
                  type="text"
                  class="text-input px-4"
                  name="facebook_page_id"
                  id="fbcompanyid"
                  value="<?= $FacebookPageID; ?>"
                  size="40"
                >
              </div>
            </div>
            <div>
              <p class="text-gray-400 text uppercase font-bold mb-2">Facebook Access Token</p>
              <div class="input-wrapper">
                <input
                  type="text"
                  class="text-input px-4"
                  name="facebook_token"
                  id="facebooktoken"
                  value="<?= $FacebookToken; ?>"
                  size="40"
                >
              </div>
            </div>
          </div>

          <div class="flex space-x-4">
            <input
              class="form-button bg-primary hover:bg-white border-2 border-transparent hover:border-primary text-white hover:text-primary rounded"
              type="submit"
              name="Submit"
              value="<?php _e('Update Settings') ?>"
            />
          </div>

        </form>
      </div>

      <?php if(get_option('facebook_token') && $facebook->postsEnabled){ ?>
        <div class="col-span-12 md:col-span-6 p-8 bg-white shadow-lg shadow-primary/20" >
          <p class="text-gray-400 text uppercase font-bold mb-2">Facebook Posts</p>
          <sync-tool id="kma-fb-posts-sync-tool" endpoint="kma-fb-post" num-sync="4" num-build="200" ></sync-tool>
        </div>
      <?php } ?>

      <?php if(get_option('facebook_token') && $facebook->eventsEnabled){ ?>
        <div class="col-span-12 md:col-span-6 p-8 bg-white shadow-lg shadow-primary/20" >
          <p class="text-gray-400 text uppercase font-bold mb-2">Facebook Events</p>
          <sync-tool id="kma-fb-events-sync-tool" endpoint="kma-fb-event" num-sync="200" num-build="200" ></sync-tool>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
<script src="/scripts<?php echo get_mix_asset('/facebook-admin.js'); ?>" ></script>
<script>
  window.fbAsyncInit = function () {
    FB.init({
      appId: '<?php echo $facebook->appId; ?>',
      cookie: true,
      xfbml: true,
      version: 'v12.0'
    });
    FB.AppEvents.logPageView();
  };

  (function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>
