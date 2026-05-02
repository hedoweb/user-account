<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user    = wp_get_current_user();
$welcome_message = get_option( 'ua_welcome_message', '' );
$shortcuts       = get_option( 'ua_shortcuts', array() );

if ( ! is_array( $shortcuts ) ) {
	$shortcuts = array();
}
?>

<div class="ua-dashboard">

	<p class="ua-welcome">
		<?php
		if ( $welcome_message ) {
			// Allow admins to use {display_name} as a placeholder.
			echo wp_kses_post( str_replace( '{display_name}', esc_html( $current_user->display_name ), $welcome_message ) );
		} else {
			printf(
				/* translators: %s: user display name */
				esc_html__( 'Welcome, %s!', 'user-account' ),
				esc_html( $current_user->display_name )
			);
		}
		?>
	</p>

	<?php
	$valid_shortcuts = array_filter(
		array_slice( $shortcuts, 0, 3 ),
		function ( $s ) {
			return ! empty( $s['title'] ) && ! empty( $s['url'] );
		}
	);
	?>

	<?php if ( ! empty( $valid_shortcuts ) ) : ?>
		<ul class="ua-shortcuts">
			<?php foreach ( $valid_shortcuts as $shortcut ) : ?>
				<li>
					<a href="<?php echo esc_url( $shortcut['url'] ); ?>">
						<?php echo esc_html( $shortcut['title'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

</div>
