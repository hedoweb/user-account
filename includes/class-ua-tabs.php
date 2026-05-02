<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages tab registration, retrieval, and rendering.
 */
class UA_Tabs {

	private static $tabs = array();

	/**
	 * Registers a tab.
	 *
	 * @param string $id   Unique tab identifier.
	 * @param array  $args { label, callback, order }
	 */
	public static function register( $id, $args ) {
		self::$tabs[ $id ] = wp_parse_args(
			$args,
			array(
				'label'    => '',
				'callback' => null,
				'order'    => 50,
			)
		);
	}

	/**
	 * Returns all tabs sorted by order, after applying the ua_tabs filter.
	 */
	public static function get_tabs() {
		$tabs = apply_filters( 'ua_tabs', self::$tabs );

		uasort(
			$tabs,
			function ( $a, $b ) {
				return (int) $a['order'] - (int) $b['order'];
			}
		);

		return $tabs;
	}

	/**
	 * Returns the active tab ID, falling back to the first registered tab.
	 */
	public static function get_active_tab() {
		$tabs    = self::get_tabs();
		$default = key( $tabs );

		if ( empty( $default ) ) {
			return '';
		}

		$requested = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $default; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return array_key_exists( $requested, $tabs ) ? $requested : $default;
	}

	/**
	 * Renders the tab navigation <li> items.
	 *
	 * @param string $active_tab Active tab ID.
	 */
	public static function render_nav( $active_tab ) {
		foreach ( self::get_tabs() as $id => $tab ) {
			$url   = add_query_arg( 'tab', $id );
			$class = 'ua-tab-link' . ( $id === $active_tab ? ' ua-tab-active' : '' );

			printf(
				'<li><a href="%s" class="%s" data-tab="%s">%s</a></li>',
				esc_url( $url ),
				esc_attr( $class ),
				esc_attr( $id ),
				esc_html( $tab['label'] )
			);
		}
	}

	/**
	 * Renders each tab panel with its content.
	 *
	 * @param string $active_tab Active tab ID.
	 */
	public static function render_content( $active_tab ) {
		foreach ( self::get_tabs() as $id => $tab ) {
			$class = 'ua-tab-panel' . ( $id === $active_tab ? ' ua-tab-active' : '' );

			printf(
				'<div class="%s" id="ua-tab-%s">',
				esc_attr( $class ),
				esc_attr( $id )
			);

			do_action( 'ua_before_tab', $id );

			if ( is_callable( $tab['callback'] ) ) {
				call_user_func( $tab['callback'] );
			}

			do_action( 'ua_after_tab', $id );

			echo '</div>';
		}
	}
}
