<?php

add_action( 'plugins_loaded', 'showcaseidx_plugins_loaded');
function showcaseidx_plugins_loaded() {
  add_option( 'showcaseidx_install_id',   showcaseidx_uuid() );
  add_option( 'showcaseidx_website_uuid', '' );
  add_option( 'showcaseidx_search_page',  'properties' );

  define( 'SHOWCASEIDX_SEARCH_HOST', get_option( 'showcaseidx_dev_search_host' ) ?: 'https://search.showcaseidx.com' );
  define( 'SHOWCASEIDX_AGENT_HOST', get_option( 'showcaseidx_dev_agent_host' ) ?: 'https://admin.showcaseidx.com' );
}

add_action( 'showcaseidx_activation', 'showcaseidx_activation_check' );
function showcaseidx_activation_check() {
  $response = wp_remote_get( SHOWCASEIDX_AGENT_HOST . '/api/provision/' . get_option( 'showcaseidx_install_id' ) );
  $code = wp_remote_retrieve_response_code( $response );
  $website = json_decode( wp_remote_retrieve_body( $response ), true );

  if ( $code == 200 && is_array( $website ) && count( $website ) != 0 ) {
    showcaseidx_update_default_url( $website );
    update_option( 'showcaseidx_website_uuid', $website['uuid'] );
  } elseif ( $code != '' ) {
    update_option( 'showcaseidx_website_uuid', '' );
  }
}

function showcaseidx_update_default_url( $website ) {
  if ( $website['root_url'] != get_site_url() ||
       $website['default_pathname'] != get_option( 'showcaseidx_search_page' ) ) {
    wp_remote_request(
      SHOWCASEIDX_AGENT_HOST . '/api/provision/' . $website['id'],
      array(
        'method' => 'PUT',
        'body' => array(
                  'root_url' => get_site_url(),
          'default_pathname' => get_option( 'showcaseidx_search_page' )
        )
      )
    );
  }
}

function showcaseidx_plugin_activation() {
  if ( !wp_next_scheduled( 'showcaseidx_activation' ) ) {
    wp_schedule_event( time(), 'hourly', 'showcaseidx_activation' );
  }

  showcaseidx_activation_check();
}

function showcaseidx_plugin_deactivation() {
  wp_clear_scheduled_hook( 'showcaseidx_activation' );
}

// From http://php.net/manual/en/function.uniqid.php#94959
function showcaseidx_uuid() {
  return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    // 32 bits for "time_low"
    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

    // 16 bits for "time_mid"
    mt_rand( 0, 0xffff ),

    // 16 bits for "time_hi_and_version",
    // four most significant bits holds version number 4
    mt_rand( 0, 0x0fff ) | 0x4000,

    // 16 bits, 8 bits for "clk_seq_hi_res",
    // 8 bits for "clk_seq_low",
    // two most significant bits holds zero and one for variant DCE1.1
    mt_rand( 0, 0x3fff ) | 0x8000,

    // 48 bits for "node"
    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
  );
}
