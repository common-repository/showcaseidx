<?php
/*
Plugin Name: Showcase IDX
Plugin URI: https://showcaseidx.com/
Description: Interactive, responsive, SEO-friendly real estate property search.
Author: Showcase IDX
Version: 2.7.12
Author URI: https://showcaseidx.com/company/
*/

add_option( 'showcaseidx_product_version', '3' );

if ( get_option( 'showcaseidx_product_version' ) != '3' ) {
  require_once(dirname(__FILE__) . '/2/showcaseidx.php');
} else {
  require_once(dirname(__FILE__) . '/3/install.php');

  require_once(dirname(__FILE__) . '/3/resources.php');
  require_once(dirname(__FILE__) . '/3/workarounds.php');
  require_once(dirname(__FILE__) . '/3/page.php');
  require_once(dirname(__FILE__) . '/3/shortcodes.php');
  require_once(dirname(__FILE__) . '/3/routes.php');

  require_once(dirname(__FILE__) . '/3/admin.php');

  register_activation_hook( __FILE__, 'showcaseidx_plugin_activation' );
  register_deactivation_hook( __FILE__, 'showcaseidx_plugin_deactivation' );
}
