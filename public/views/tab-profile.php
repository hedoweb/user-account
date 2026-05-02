<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id   = get_current_user_id();
$user_data = UA_Profile::get_user_data( $user_id );
?>

<div class="ua-profile">

	<!-- Profile info form -->
	<form id="ua-profile-form" class="ua-form" novalidate>
		<?php wp_nonce_field( 'ua_save_profile', 'ua_nonce' ); ?>

		<div class="ua-form-section">

			<div class="ua-avatar-wrap">
				<img src="<?php echo esc_url( UA_Profile::get_avatar_url( $user_id ) ); ?>"
					alt="<?php esc_attr_e( 'Avatar', 'user-account' ); ?>"
					class="ua-avatar-preview"
					width="96" height="96">

				<div class="ua-avatar-actions">
					<label for="ua_avatar_file" class="ua-btn ua-btn--secondary">
						<?php esc_html_e( 'Change avatar', 'user-account' ); ?>
					</label>
					<input type="file" id="ua_avatar_file" name="ua_avatar_file"
						accept="image/jpeg,image/png,image/gif,image/webp"
						class="ua-sr-only">

					<?php if ( $user_data['ua_avatar'] ) : ?>
						<button type="button" id="ua-remove-avatar" class="ua-btn ua-btn--link">
							<?php esc_html_e( 'Remove', 'user-account' ); ?>
						</button>
					<?php endif; ?>
				</div>
			</div>

			<div class="ua-field-row">
				<div class="ua-field">
					<label for="ua_first_name"><?php esc_html_e( 'First name', 'user-account' ); ?></label>
					<input type="text" id="ua_first_name" name="first_name"
						value="<?php echo esc_attr( $user_data['first_name'] ); ?>">
				</div>

				<div class="ua-field">
					<label for="ua_last_name"><?php esc_html_e( 'Last name', 'user-account' ); ?></label>
					<input type="text" id="ua_last_name" name="last_name"
						value="<?php echo esc_attr( $user_data['last_name'] ); ?>">
				</div>
			</div>

			<div class="ua-field">
				<label for="ua_email"><?php esc_html_e( 'Email address', 'user-account' ); ?></label>
				<input type="email" id="ua_email" name="user_email"
					value="<?php echo esc_attr( $user_data['user_email'] ); ?>"
					required>
			</div>

			<div class="ua-field">
				<label for="ua_bio"><?php esc_html_e( 'Bio', 'user-account' ); ?></label>
				<textarea id="ua_bio" name="ua_bio" rows="4"><?php echo esc_textarea( $user_data['ua_bio'] ); ?></textarea>
			</div>

		</div>

		<div class="ua-form-footer">
			<button type="submit" class="ua-btn ua-btn--primary">
				<?php esc_html_e( 'Save changes', 'user-account' ); ?>
			</button>
			<span class="ua-form-feedback" aria-live="polite"></span>
		</div>

	</form>

	<hr class="ua-separator">

	<!-- Password change form -->
	<form id="ua-password-form" class="ua-form" novalidate>
		<?php wp_nonce_field( 'ua_change_password', 'ua_pwd_nonce' ); ?>

		<h3 class="ua-section-title"><?php esc_html_e( 'Change password', 'user-account' ); ?></h3>

		<div class="ua-field">
			<label for="ua_current_password"><?php esc_html_e( 'Current password', 'user-account' ); ?></label>
			<input type="password" id="ua_current_password" name="current_password" autocomplete="current-password">
		</div>

		<div class="ua-field-row">
			<div class="ua-field">
				<label for="ua_new_password"><?php esc_html_e( 'New password', 'user-account' ); ?></label>
				<input type="password" id="ua_new_password" name="new_password" autocomplete="new-password">
			</div>

			<div class="ua-field">
				<label for="ua_confirm_password"><?php esc_html_e( 'Confirm new password', 'user-account' ); ?></label>
				<input type="password" id="ua_confirm_password" name="confirm_password" autocomplete="new-password">
			</div>
		</div>

		<div class="ua-form-footer">
			<button type="submit" class="ua-btn ua-btn--primary">
				<?php esc_html_e( 'Update password', 'user-account' ); ?>
			</button>
			<span class="ua-form-feedback" aria-live="polite"></span>
		</div>

	</form>

</div>
