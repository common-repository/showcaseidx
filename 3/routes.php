<?php

add_filter( 'do_parse_request', 'showcaseidx_parse_request', PHP_INT_MAX, 2 );
function showcaseidx_parse_request( $bool, WP $wp ) {
  $url = parse_url( $_SERVER['REQUEST_URI'] );

  // XML Sitemaps
  if ( preg_match( '#^/' . get_option( 'showcaseidx_search_page' ) . '/xmlsitemap/(\d*/)?$#', $url['path'], $matches ) ) {

    header( 'Content-Type: application/xml' );
    print showcaseidx_get_xmlsitemap( isset( $matches[1] ) ? $matches[1] : null );
    exit;
  }

  // Default Search Page
  if ( preg_match( '#^/' . get_option( 'showcaseidx_search_page' ) . '($|/.*)#', $url['path'], $matches ) ) {

    $path = $matches[1];
    $query = !isset( $url['query'] ) ?: $url['query'];

    if ( $path == '' ) {
      header( "HTTP/1.1 301 Moved Permanently" );
      header( "Location: " . get_home_url() . '/' . get_option( 'showcaseidx_search_page' ) . '/' );
      exit;
    }

    showcase_render_search_page( $wp, $path, $query );
  }

  return $bool;
}

function showcaseidx_get_xmlsitemap( $page = '' ) {
  $website_uuid = get_option( 'showcaseidx_website_uuid' );
  $api_url = SHOWCASEIDX_SEARCH_HOST . '/app/xmlsitemap/';

  $response = wp_remote_get( $api_url . $website_uuid . '/' . $page, array( 'timeout' => 5, 'httpversion' => '1.1' ) );

  if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
    return wp_remote_retrieve_body( $response );
  } else {
    return '';
  }
}
