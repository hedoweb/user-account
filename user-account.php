<?php
/**
 * Plugin Name:       User Account
 * Plugin URI:
 * Description:       Lightweight "My Account" page for logged-in WordPress users, no dependencies required.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Esperluat
 * Text Domain:       user-account
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UA_VERSION', '1.0.0' );
define( 'UA_PATH', plugin_dir_path( __FILE__ ) );
define( 'UA_URL', plugin_dir_url( __FILE__ ) );
define( 'UA_PAGE_OPTION', 'ua_page_id' );

require_once UA_PATH . 'includes/class-ua-core.php';

register_activation_hook( __FILE__, array( 'UA_Page', 'on_activation' ) );

UA_Core::get_instance();
