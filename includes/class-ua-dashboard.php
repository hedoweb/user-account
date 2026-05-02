<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dashboard tab — welcome message and shortcuts.
 */
class UA_Dashboard {

	public static function register_tab() {
		$active = get_option( 'ua_active_tabs', array( 'dashboard', 'profile' ) );
		if ( ! in_array( 'dashboard', (array) $active, true ) ) {
			return;
		}

		UA_Tabs::register(
			'dashboard',
			array(
				'label'    => __( 'Dashboard', 'user-account' ),
				'callback' => array( __CLASS__, 'render' ),
				'order'    => 10,
			)
		);
	}

	public static function render() {
		include UA_PATH . 'public/views/tab-dashboard.php';
	}
}
