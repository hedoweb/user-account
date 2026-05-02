<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
?>

<?php do_action( 'ua_before_account' ); ?>

<div class="ua-account-wrapper">

	<nav class="ua-tabs-nav" aria-label="<?php esc_attr_e( 'Account navigation', 'user-account' ); ?>">
		<ul>
			<!-- Tab nav items will be rendered here by UA_Tabs -->
		</ul>
	</nav>

	<div class="ua-tabs-content">
		<!-- Tab content will be rendered here by UA_Tabs -->
	</div>

</div>

<?php do_action( 'ua_after_account' ); ?>
