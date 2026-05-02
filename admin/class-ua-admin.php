<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Settings > User Account admin page.
 */
class UA_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	public static function add_menu() {
		add_options_page(
			__( 'User Account Settings', 'user-account' ),
			__( 'User Account', 'user-account' ),
			'manage_options',
			'user-account',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function register_settings() {
		register_setting(
			'ua_settings_group',
			'ua_welcome_message',
			array(
				'sanitize_callback' => 'wp_kses_post',
				'default'           => '',
			)
		);

		register_setting(
			'ua_settings_group',
			'ua_shortcuts',
			array(
				'sanitize_callback' => array( __CLASS__, 'sanitize_shortcuts' ),
				'default'           => array(),
			)
		);

		register_setting(
			'ua_settings_group',
			'ua_active_tabs',
			array(
				'sanitize_callback' => array( __CLASS__, 'sanitize_active_tabs' ),
				'default'           => array( 'dashboard', 'profile' ),
			)
		);
	}

	public static function sanitize_shortcuts( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}

		$clean = array();

		foreach ( array_slice( $input, 0, 3 ) as $item ) {
			$title = sanitize_text_field( $item['title'] ?? '' );
			$url   = esc_url_raw( $item['url'] ?? '' );

			$clean[] = array(
				'title' => $title,
				'url'   => $url,
			);
		}

		return $clean;
	}

	public static function sanitize_active_tabs( $input ) {
		$allowed = array( 'dashboard', 'profile' );

		if ( empty( $input ) || ! is_array( $input ) ) {
			return array();
		}

		return array_values( array_intersect( $input, $allowed ) );
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		include UA_PATH . 'admin/views/settings-page.php';
	}
}
