<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles creation and detection of the My Account page.
 */
class UA_Page {

	/**
	 * Creates the account page on plugin activation.
	 * Bails silently if the page already exists.
	 */
	public static function on_activation() {
		$page_id = (int) get_option( UA_PAGE_OPTION );

		if ( $page_id ) {
			$page = get_post( $page_id );
			if ( $page && 'trash' !== $page->post_status ) {
				return;
			}
		}

		$page_id = wp_insert_post(
			array(
				'post_title'     => __( 'My Account', 'user-account' ),
				'post_name'      => 'my-account',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'comment_status' => 'closed',
			)
		);

		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_option( UA_PAGE_OPTION, $page_id );
		}
	}

	/**
	 * Redirects to login if the account page is accessed while logged out.
	 */
	public static function maybe_redirect_to_login() {
		if ( ! self::is_account_page() || is_user_logged_in() ) {
			return;
		}

		$redirect_url = apply_filters( 'ua_redirect_url', wp_login_url( get_permalink() ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Returns true when the current request is the account page.
	 */
	public static function is_account_page() {
		$page_id = (int) get_option( UA_PAGE_OPTION );
		return $page_id && is_page( $page_id );
	}
}
