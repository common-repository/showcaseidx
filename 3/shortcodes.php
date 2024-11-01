<?php

add_shortcode( 'showcaseidx_signin',   showcaseidx_build_shortcode( 'authform' ) );
add_shortcode( 'showcaseidx_cma',      showcaseidx_build_shortcode( 'cmaform' ) );
add_shortcode( 'showcaseidx_contact',  showcaseidx_build_shortcode( 'contactform' ) );
add_shortcode( 'showcaseidx_search',   showcaseidx_build_shortcode( 'searchform' ) );
add_shortcode( 'showcaseidx_map',      showcaseidx_build_shortcode( 'searchmap' ) );
add_shortcode( 'showcaseidx_hotsheet', showcaseidx_build_shortcode( 'hotsheet', array( 'name' => '' ) ) );

function showcaseidx_build_shortcode( $type, $allowed = array() ) {
  return function ( $attrs ) use ( $type, $allowed ) {
    $attrs = shortcode_atts( $allowed, $attrs, 'showcaseidx_' . $type );

    $response = showcase_retrieve_widget( $type, $attrs );

    if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
      $widget = json_decode( wp_remote_retrieve_body( $response ) );

      return $widget->widget;
    } else {
      return '';
    }
  };
}

function showcase_retrieve_widget( $widget, $attrs ) {
  $cookies = array();
  foreach ( $_COOKIE as $name => $value ) {
    $cookies[] = new WP_Http_Cookie( array( 'name' => $name, 'value' => $value ) );
  }

  $query = $attrs;
  $query['website_uuid'] = get_option( 'showcaseidx_website_uuid' );

  return wp_remote_get(
    SHOWCASEIDX_SEARCH_HOST . '/app/renderWidget/' . $widget . '?' . http_build_query( $query ),
    array(
      'timeout' => 10,
      'httpversion' => '1.1',
      'cookies' => $cookies
    )
  );
}
