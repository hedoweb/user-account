<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders the [user_account] shortcode.
 */
class UA_Shortcode {

	public static function register() {
		add_shortcode( 'user_account', array( __CLASS__, 'render' ) );
	}

	public static function render( $atts ) {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		ob_start();
		include UA_PATH . 'public/views/account-wrapper.php';
		return ob_get_clean();
	}
}
