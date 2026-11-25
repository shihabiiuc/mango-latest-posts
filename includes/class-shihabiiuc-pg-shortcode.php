<?php
/**
 * [shihabiiuc_post_grid] shortcode.
 *
 * @package ShihabiiucPostGrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode for classic editor, widgets, other page builders and theme templates.
 *
 * Example:
 * [shihabiiuc_post_grid posts="6" columns="3" layout="masonry" excerpt_words="20" category="news,tips"
 *  read_more_text="Continue reading" title_color="#111111" button_bg="#ff6600"]
 */
class Shihabiiuc_PG_Shortcode {

	const TAG = 'shihabiiuc_post_grid';

	/**
	 * Register the shortcode.
	 */
	public static function register() {
		add_shortcode( self::TAG, array( __CLASS__, 'render' ) );
	}

	/**
	 * Shortcode callback.
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string
	 */
	public static function render( $atts ) {
		$defaults = Shihabiiuc_PG_Renderer::defaults();
		unset( $defaults['inline_styles'], $defaults['categories'] );
		$defaults['category'] = '';

		$atts = shortcode_atts( $defaults, $atts, self::TAG );

		$atts['categories'] = $atts['category'];
		unset( $atts['category'] );

		return Shihabiiuc_PG_Renderer::render( $atts );
	}
}
