<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shortcuts   = get_option( 'ua_shortcuts', array() );
$active_tabs = get_option( 'ua_active_tabs', array( 'dashboard', 'profile' ) );

// Pad shortcuts to always show 3 rows.
while ( count( $shortcuts ) < 3 ) {
	$shortcuts[] = array( 'title' => '', 'url' => '' );
}
?>

<div class="wrap">

	<h1><?php esc_html_e( 'User Account Settings', 'user-account' ); ?></h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'ua_settings_group' ); ?>

		<!-- ---------------------------------------------------------------- -->
		<!-- Welcome message -->
		<!-- ---------------------------------------------------------------- -->
		<h2><?php esc_html_e( 'Dashboard', 'user-account' ); ?></h2>
		<table class="form-table" role="presentation">

			<tr>
				<th scope="row">
					<label for="ua_welcome_message"><?php esc_html_e( 'Welcome message', 'user-account' ); ?></label>
				</th>
				<td>
					<textarea id="ua_welcome_message" name="ua_welcome_message"
						class="large-text" rows="3"><?php echo esc_textarea( get_option( 'ua_welcome_message', '' ) ); ?></textarea>
					<p class="description">
						<?php esc_html_e( 'Use {display_name} to insert the user\'s name. HTML is allowed.', 'user-account' ); ?>
					</p>
				</td>
			</tr>

		</table>

		<!-- ---------------------------------------------------------------- -->
		<!-- Quick links -->
		<!-- ---------------------------------------------------------------- -->
		<h2><?php esc_html_e( 'Quick links', 'user-account' ); ?></h2>
		<table class="form-table" role="presentation">

			<?php foreach ( $shortcuts as $i => $shortcut ) : ?>
				<tr>
					<th scope="row">
						<?php
						printf(
							/* translators: %d: shortcut number */
							esc_html__( 'Shortcut %d', 'user-account' ),
							absint( $i ) + 1
						);
						?>
					</th>
					<td>
						<input type="text"
							name="ua_shortcuts[<?php echo (int) $i; ?>][title]"
							value="<?php echo esc_attr( $shortcut['title'] ); ?>"
							placeholder="<?php esc_attr_e( 'Label', 'user-account' ); ?>"
							class="regular-text">

						<input type="url"
							name="ua_shortcuts[<?php echo (int) $i; ?>][url]"
							value="<?php echo esc_attr( $shortcut['url'] ); ?>"
							placeholder="https://"
							class="regular-text">
					</td>
				</tr>
			<?php endforeach; ?>

		</table>

		<!-- ---------------------------------------------------------------- -->
		<!-- Active tabs -->
		<!-- ---------------------------------------------------------------- -->
		<h2><?php esc_html_e( 'Active tabs', 'user-account' ); ?></h2>
		<table class="form-table" role="presentation">

			<tr>
				<th scope="row"><?php esc_html_e( 'Tabs', 'user-account' ); ?></th>
				<td>
					<fieldset>
						<label>
							<input type="checkbox" name="ua_active_tabs[]" value="dashboard"
								<?php checked( in_array( 'dashboard', (array) $active_tabs, true ) ); ?>>
							<?php esc_html_e( 'Dashboard', 'user-account' ); ?>
						</label>
						<br>
						<label>
							<input type="checkbox" name="ua_active_tabs[]" value="profile"
								<?php checked( in_array( 'profile', (array) $active_tabs, true ) ); ?>>
							<?php esc_html_e( 'My Profile', 'user-account' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>

		</table>

		<?php submit_button(); ?>

	</form>

</div>
