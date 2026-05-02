<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$ua_options = array(
	'ua_page_id',
	'ua_welcome_message',
	'ua_shortcuts',
	'ua_active_tabs',
);

foreach ( $ua_options as $option ) {
	delete_option( $option );
}

$ua_meta_keys = array( 'ua_avatar', 'ua_bio' );

foreach ( $ua_meta_keys as $meta_key ) {
	delete_metadata( 'user', 0, $meta_key, '', true );
}
