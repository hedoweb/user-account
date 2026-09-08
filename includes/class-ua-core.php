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
		require_once UA_PATH . 'includes/class-ua-tabs.php';
		require_once UA_PATH . 'includes/class-ua-dashboard.php';
		require_once UA_PATH . 'includes/class-ua-profile.php';
		require_once UA_PATH . 'includes/class-ua-ajax.php';
		require_once UA_PATH . 'includes/class-ua-shortcode.php';

		if ( is_admin() ) {
			require_once UA_PATH . 'admin/class-ua-admin.php';
		}
	}

	private function register_hooks() {
		add_action( 'init', array( 'UA_Shortcode', 'register' ) );
		add_action( 'init', array( 'UA_Dashboard', 'register_tab' ) );
		add_action( 'init', array( 'UA_Profile', 'register_tab' ) );
		add_action( 'init', array( 'UA_Ajax', 'register' ) );
		add_action( 'template_redirect', array( 'UA_Page', 'maybe_redirect_to_login' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		if ( is_admin() ) {
			add_action( 'init', array( 'UA_Admin', 'init' ) );
		}
	}

	public function enqueue_assets() {
		if ( ! is_user_logged_in() || ! UA_Page::is_account_page() ) {
			return;
		}

		wp_enqueue_style(
			'ua-public',
			UA_URL . 'public/css/ua-public.css',
			array(),
			UA_VERSION
		);

		wp_enqueue_script(
			'ua-public',
			UA_URL . 'public/js/ua-public.js',
			array( 'jquery' ),
			UA_VERSION,
			true
		);

		wp_localize_script(
			'ua-public',
			'ua_vars',
			array(
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'default_avatar' => get_avatar_url( get_current_user_id(), array( 'size' => 96 ) ),
				'i18n'           => array(
					'error' => __( 'Something went wrong. Please try again.', 'user-account' ),
				),
			)
		);
	}
}
