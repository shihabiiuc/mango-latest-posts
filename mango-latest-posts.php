<?php
/**
 * Plugin Name:       Mango Latest Posts for Elementor and Beyond
 * Plugin URI:        https://shihabiiuc.com/
 * Description:       Show your latest blog posts in a clean card grid or masonry layout. Works as an Elementor widget ("Mango Blog Grid"), a Gutenberg block, and a shortcode for any theme or page builder.
 * Version:           1.0.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Shihab
 * Author URI:        https://shihabiiuc.com/about/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mango-latest-posts
 *
 * @package MangoLatestPosts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MANGO_LP_VERSION', '1.0.0' );
define( 'MANGO_LP_FILE', __FILE__ );
define( 'MANGO_LP_DIR', plugin_dir_path( __FILE__ ) );
define( 'MANGO_LP_URL', plugin_dir_url( __FILE__ ) );

require_once MANGO_LP_DIR . 'includes/class-mango-lp-renderer.php';
require_once MANGO_LP_DIR . 'includes/class-mango-lp-shortcode.php';
require_once MANGO_LP_DIR . 'includes/class-mango-lp-block.php';
require_once MANGO_LP_DIR . 'includes/class-mango-lp-admin.php';

/**
 * Register the image size, shared stylesheet, shortcode and block.
 */
function mango_lp_init() {
	// 1200x628, hard-cropped from the center. Used by the equal-height grid layout.
	add_image_size( Mango_LP_Renderer::IMAGE_SIZE, 1200, 628, true );

	wp_register_style(
		'mango-lp-style',
		MANGO_LP_URL . 'assets/css/mango-latest-posts.css',
		array(),
		MANGO_LP_VERSION
	);

	Mango_LP_Shortcode::register();
	Mango_LP_Block::register();
}
add_action( 'init', 'mango_lp_init' );

/**
 * Register the Elementor widget, only when Elementor is active.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 */
function mango_lp_register_elementor_widget( $widgets_manager ) {
	require_once MANGO_LP_DIR . 'includes/class-mango-lp-elementor-widget.php';
	$widgets_manager->register( new Mango_LP_Elementor_Widget() );
}
add_action( 'elementor/widgets/register', 'mango_lp_register_elementor_widget' );

Mango_LP_Admin::init();
