<?php
/**
 * [mango_latest_posts] shortcode.
 *
 * @package MangoLatestPosts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode for classic editor, widgets, other page builders and theme templates.
 *
 * Example:
 * [mango_latest_posts posts="6" columns="3" layout="masonry" excerpt_words="20" category="news,tips"
 *  read_more_text="Continue reading" title_color="#111111" button_bg="#ff6600"]
 */
class Mango_LP_Shortcode {

	const TAG = 'mango_latest_posts';

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
		$defaults = Mango_LP_Renderer::defaults();
		unset( $defaults['inline_styles'], $defaults['categories'] );
		$defaults['category'] = '';

		$atts = shortcode_atts( $defaults, $atts, self::TAG );

		$atts['categories'] = $atts['category'];
		unset( $atts['category'] );

		return Mango_LP_Renderer::render( $atts );
	}
}
