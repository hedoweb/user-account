<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_tab = UA_Tabs::get_active_tab();
?>

<?php do_action( 'ua_before_account' ); ?>

<div class="ua-account-wrapper">

	<nav class="ua-tabs-nav" aria-label="<?php esc_attr_e( 'Account navigation', 'user-account' ); ?>">
		<ul>
			<?php UA_Tabs::render_nav( $active_tab ); ?>
		</ul>
	</nav>

	<div class="ua-tabs-content">
		<?php UA_Tabs::render_content( $active_tab ); ?>
	</div>

</div>

<?php do_action( 'ua_after_account' ); ?>
