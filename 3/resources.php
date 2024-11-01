<?php

add_action('wp_enqueue_scripts', 'showcaseidx_load_resources');
function showcaseidx_load_resources($hook) {
  $response = wp_remote_get( SHOWCASEIDX_SEARCH_HOST . '/app/assets', array( 'timeout' => 5, 'httpversion' => '1.1' ) );

  if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
    $assets = json_decode( wp_remote_retrieve_body( $response ) );

    foreach ( $assets->css as $key => $src ) {
      wp_enqueue_style( "showcaseidx-css-$key", $src );
    }

    foreach ( $assets->js as $key => $src ) {
      wp_enqueue_script( "showcaseidx-js-$key", $src );
    }
  }
}
