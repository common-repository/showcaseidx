<?php

add_action( 'admin_init', 'showcaseidx_admin_init' );
function showcaseidx_admin_init() {
  register_setting( 'showcaseidx-settings-group', 'showcaseidx_search_page', 'showcaseidx_search_page_sanitize' );
}

function showcaseidx_search_page_sanitize($input) {
  return trim(trim($input), '/');
}

add_action('current_screen', 'showcaseidx_redirect_admin_page');
function showcaseidx_redirect_admin_page() {
  if ( get_current_screen()->base == 'toplevel_page_showcaseidx' && isset( $_GET["showcaseidx_change_version"] ) ) {
    update_option( 'showcaseidx_product_version', '2' );
    wp_redirect( admin_url( '/admin.php?page=showcaseidx' ) );
    exit();
  }
}

add_action( 'admin_menu', 'showcaseidx_admin_menu' );
function showcaseidx_admin_menu() {
  $admin_page = add_menu_page(
    'Showcase IDX Admin',
    'Showcase IDX',
    'manage_options',
    'showcaseidx',
    'showcaseidx_admin_page',
    plugin_dir_url( dirname( __FILE__ ) ) . 'images/menu.png',
    '100.100100'
  );

  add_action( 'load-' . $admin_page, 'showcaseidx_admin_page_enqueue' );
}

function showcaseidx_admin_page_enqueue() {
  wp_enqueue_style( 'showcaseidx-admin', plugin_dir_url( dirname( __FILE__ ) ) . 'css/admin.css' );
}

function showcaseidx_admin_page() {
  showcaseidx_activation_check();
  ?>

  <div class="wrap sidx-admin">
    <h2 class="sidx-title">
      <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>images/logo.svg" height="20">
      Showcase IDX
    </h2>

    <div class="card">
      <?php
      if ( !get_option( 'showcaseidx_website_uuid' ) ) {
        ?>
        <h2 class="center">Welcome to Showcase IDX</h2>

        <p class="center">
          In order to get started, we need to activate this plugin with<br>
          your Showcase IDX account. Click the button below to continue:
        </p>

        <p class="submit center">
          <a class="button button-primary" href="<?php echo SHOWCASEIDX_AGENT_HOST . '/provision/' . get_option( 'showcaseidx_install_id' ) . '?return_to=' . menu_page_url( 'showcaseidx-admin', false ); ?>">Activate Your Wordpress Plugin</a>
        </p>

        <h3 class="center">Don't have a Showcase IDX account?</h3>
        <h2 class="center"><a href="http://showcaseidx.com/plans-pricing/" target="_blank">Sign up for Showcase IDX</a></h2>
        <?php
      } else {
        ?>
          <form method="post" action="options.php">
            <?php settings_fields( 'showcaseidx-settings-group' ); ?>
            <h3>Default Search Page URL</h3>
            <p class="description">This will be the primary search results page for Showcase IDX. Any links to listings will live under this URL. <a href="">Learn more about the default search page.</a></p>
            <p>
              <strong class="url"><?= get_site_url(); ?>/</strong>
              <input class="big-input" type="text" id="showcaseidx_search_page" name="showcaseidx_search_page" value="<?php echo get_option( 'showcaseidx_search_page' ); ?>"></td>
              <strong class="url">/</strong>
            </p>
            <p class="description"></p>

            <?php submit_button('Save Your Changes'); ?>
          </form>
        <?php
      }
      ?>
    </div>
  </div>

  <?php
}
