<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Profile tab — reads user data and exposes helper methods for the template.
 */
class UA_Profile {

	public static function register_tab() {
		UA_Tabs::register(
			'profile',
			array(
				'label'    => __( 'My Profile', 'user-account' ),
				'callback' => array( __CLASS__, 'render' ),
				'order'    => 20,
			)
		);
	}

	public static function render() {
		include UA_PATH . 'public/views/tab-profile.php';
	}

	/**
	 * Returns profile field values for a given user.
	 * Filterable via ua_profile_fields.
	 *
	 * @param int $user_id
	 * @return array
	 */
	public static function get_user_data( $user_id ) {
		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return array();
		}

		$data = array(
			'first_name' => $user->first_name,
			'last_name'  => $user->last_name,
			'user_email' => $user->user_email,
			'ua_bio'     => (string) get_user_meta( $user_id, 'ua_bio', true ),
			'ua_avatar'  => (string) get_user_meta( $user_id, 'ua_avatar', true ),
		);

		return apply_filters( 'ua_profile_fields', $data, $user_id );
	}

	/**
	 * Returns the avatar URL for a user — custom upload first, then Gravatar.
	 *
	 * @param int $user_id
	 * @return string
	 */
	public static function get_avatar_url( $user_id ) {
		$custom = get_user_meta( $user_id, 'ua_avatar', true );

		if ( $custom ) {
			return esc_url( $custom );
		}

		return get_avatar_url( $user_id, array( 'size' => 96 ) );
	}
}
