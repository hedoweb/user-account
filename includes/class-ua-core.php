<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core singleton — loads dependencies and registers top-level hooks.
 */
class UA_Core {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->load_dependencies();
		$this->register_hooks();
	}

	// Prevent cloning and unserialization.
	private function __clone() {}
	public function __wakeup() {}

	private function load_dependencies() {
		require_once UA_PATH . 'includes/class-ua-page.php';
		require_once UA_PATH . 'includes/class-ua-shortcode.php';
	}

	private function register_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( 'UA_Shortcode', 'register' ) );
		add_action( 'template_redirect', array( 'UA_Page', 'maybe_redirect_to_login' ) );
	}

	public function load_textdomain() {
		load_plugin_textdomain(
			'user-account',
			false,
			dirname( plugin_basename( UA_PATH . 'user-account.php' ) ) . '/languages'
		);
	}
}
