<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX handlers — profile save, avatar upload/removal, password change.
 */
class UA_Ajax {

	public static function register() {
		add_action( 'wp_ajax_ua_save_profile',   array( __CLASS__, 'save_profile' ) );
		add_action( 'wp_ajax_ua_change_password', array( __CLASS__, 'change_password' ) );
	}

	// -------------------------------------------------------------------------
	// Profile
	// -------------------------------------------------------------------------

	public static function save_profile() {
		check_ajax_referer( 'ua_save_profile', 'ua_nonce' );

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json_error( array( 'message' => __( 'Not logged in.', 'user-account' ) ) );
		}

		$data = array(
			'first_name' => sanitize_text_field( wp_unslash( $_POST['first_name'] ?? '' ) ),
			'last_name'  => sanitize_text_field( wp_unslash( $_POST['last_name'] ?? '' ) ),
			'user_email' => sanitize_email( wp_unslash( $_POST['user_email'] ?? '' ) ),
			'ua_bio'     => sanitize_textarea_field( wp_unslash( $_POST['ua_bio'] ?? '' ) ),
		);

		if ( ! is_email( $data['user_email'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid email address.', 'user-account' ) ) );
		}

		$existing = get_user_by( 'email', $data['user_email'] );
		if ( $existing && (int) $existing->ID !== $user_id ) {
			wp_send_json_error( array( 'message' => __( 'This email is already in use.', 'user-account' ) ) );
		}

		do_action( 'ua_before_save_profile', $user_id, $data );

		// Avatar removal.
		if ( ! empty( $_POST['remove_avatar'] ) ) {
			delete_user_meta( $user_id, 'ua_avatar' );
		}

		// Avatar upload.
		if ( ! empty( $_FILES['ua_avatar_file']['name'] ) ) {
			$result = self::handle_avatar_upload();
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}
			update_user_meta( $user_id, 'ua_avatar', $result );
		}

		$update = wp_update_user(
			array(
				'ID'         => $user_id,
				'first_name' => $data['first_name'],
				'last_name'  => $data['last_name'],
				'user_email' => $data['user_email'],
			)
		);

		if ( is_wp_error( $update ) ) {
			wp_send_json_error( array( 'message' => $update->get_error_message() ) );
		}

		update_user_meta( $user_id, 'ua_bio', $data['ua_bio'] );

		do_action( 'ua_after_save_profile', $user_id, $data );

		wp_send_json_success(
			array(
				'message'    => __( 'Profile updated.', 'user-account' ),
				'avatar_url' => UA_Profile::get_avatar_url( $user_id ),
			)
		);
	}

	// -------------------------------------------------------------------------
	// Avatar upload (private helper)
	// -------------------------------------------------------------------------

	/**
	 * Called only from save_profile(), which already verified the
	 * ua_save_profile nonce — no separate nonce check needed here.
	 * $_FILES values are passed as-is to wp_check_filetype_and_ext()
	 * and wp_handle_upload(), which perform their own validation;
	 * sanitize_text_field() would corrupt a legitimate tmp_name path.
	 */
	private static function handle_avatar_upload() {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		if ( ! isset( $_FILES['ua_avatar_file'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in caller save_profile().
			return new WP_Error( 'no_file', __( 'No file was uploaded.', 'user-account' ) );
		}

		$allowed_mimes = array(
			'jpg|jpeg' => 'image/jpeg',
			'png'      => 'image/png',
			'gif'      => 'image/gif',
			'webp'     => 'image/webp',
		);

		$file = $_FILES['ua_avatar_file']; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified in caller; validated below.

		$file_info = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed_mimes );

		if ( ! $file_info['type'] ) {
			return new WP_Error(
				'invalid_type',
				__( 'Invalid file type. Please upload a JPG, PNG, GIF or WebP image.', 'user-account' )
			);
		}

		// 2 MB limit.
		if ( $file['size'] > 2 * MB_IN_BYTES ) {
			return new WP_Error(
				'file_too_large',
				__( 'Image must be smaller than 2 MB.', 'user-account' )
			);
		}

		$uploaded = wp_handle_upload(
			$file,
			array(
				'test_form' => false,
				'mimes'     => $allowed_mimes,
			)
		);

		if ( isset( $uploaded['error'] ) ) {
			return new WP_Error( 'upload_error', $uploaded['error'] );
		}

		return $uploaded['url'];
	}

	// -------------------------------------------------------------------------
	// Password
	// -------------------------------------------------------------------------

	public static function change_password() {
		check_ajax_referer( 'ua_change_password', 'ua_pwd_nonce' );

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json_error( array( 'message' => __( 'Not logged in.', 'user-account' ) ) );
		}

		// Passwords are intentionally left unsanitized — sanitize_text_field()
		// would strip characters a user legitimately chose for their password.
		// wp_check_password()/wp_set_password() handle the raw value safely.
		$current  = isset( $_POST['current_password'] ) ? wp_unslash( $_POST['current_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$new      = isset( $_POST['new_password'] )     ? wp_unslash( $_POST['new_password'] )     : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$confirm  = isset( $_POST['confirm_password'] ) ? wp_unslash( $_POST['confirm_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! $current || ! $new || ! $confirm ) {
			wp_send_json_error( array( 'message' => __( 'Please fill in all password fields.', 'user-account' ) ) );
		}

		if ( $new !== $confirm ) {
			wp_send_json_error( array( 'message' => __( 'New passwords do not match.', 'user-account' ) ) );
		}

		$user = get_userdata( $user_id );
		if ( ! wp_check_password( $current, $user->user_pass, $user_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Current password is incorrect.', 'user-account' ) ) );
		}

		wp_set_password( $new, $user_id );

		// Keep the user authenticated after the password reset.
		wp_set_auth_cookie( $user_id, true );

		wp_send_json_success( array( 'message' => __( 'Password updated.', 'user-account' ) ) );
	}
}
