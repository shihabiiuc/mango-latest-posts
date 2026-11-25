<?php
/**
 * Plugins screen links.
 *
 * @package ShihabiiucPostGrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a single "Need a custom WordPress site?" link to this plugin's row on the
 * Plugins screen. Nothing is shown on the front end and no notices are displayed.
 */
class Shihabiiuc_PG_Admin {

	const CONTACT_URL = 'https://shihabiiuc.com/contact/';

	/**
	 * Hook into the Plugins screen.
	 */
	public static function init() {
		add_filter( 'plugin_row_meta', array( __CLASS__, 'row_meta' ), 10, 2 );
	}

	/**
	 * Add the link below the plugin description.
	 *
	 * @param string[] $links Existing meta links.
	 * @param string   $file  Plugin basename of the row being rendered.
	 * @return string[]
	 */
	public static function row_meta( $links, $file ) {
		if ( plugin_basename( SHIHABIIUC_PG_FILE ) !== $file ) {
			return $links;
		}

		$links[] = sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( self::CONTACT_URL ),
			esc_html__( 'Need a custom WordPress site?', 'shihabiiuc-post-grid' )
		);

		return $links;
	}
}
