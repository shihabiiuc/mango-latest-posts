<?php
/**
 * Plugin Name:       Shihabiiuc Post Grid
 * Plugin URI:        https://shihabiiuc.com/
 * Description:       Show your latest blog posts in a clean card grid or masonry layout. Works as an Elementor widget ("Shihabiiuc Post Grid"), a Gutenberg block, and a shortcode for any theme or page builder.
 * Version:           1.0.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Shihab
 * Author URI:        https://shihabiiuc.com/about/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       shihabiiuc-post-grid
 *
 * @package ShihabiiucPostGrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SHIHABIIUC_PG_VERSION', '1.0.0' );
define( 'SHIHABIIUC_PG_FILE', __FILE__ );
define( 'SHIHABIIUC_PG_DIR', plugin_dir_path( __FILE__ ) );
define( 'SHIHABIIUC_PG_URL', plugin_dir_url( __FILE__ ) );

require_once SHIHABIIUC_PG_DIR . 'includes/class-shihabiiuc-pg-renderer.php';
require_once SHIHABIIUC_PG_DIR . 'includes/class-shihabiiuc-pg-shortcode.php';
require_once SHIHABIIUC_PG_DIR . 'includes/class-shihabiiuc-pg-block.php';
require_once SHIHABIIUC_PG_DIR . 'includes/class-shihabiiuc-pg-admin.php';

/**
 * Register the image size, shared stylesheet, shortcode and block.
 */
function shihabiiuc_pg_init() {
	// 1200x628, hard-cropped from the center. Used by the equal-height grid layout.
	add_image_size( Shihabiiuc_PG_Renderer::IMAGE_SIZE, 1200, 628, true );

	wp_register_style(
		'shihabiiuc-pg-style',
		SHIHABIIUC_PG_URL . 'assets/css/shihabiiuc-post-grid.css',
		array(),
		SHIHABIIUC_PG_VERSION
	);

	Shihabiiuc_PG_Shortcode::register();
	Shihabiiuc_PG_Block::register();
}
add_action( 'init', 'shihabiiuc_pg_init' );

/**
 * Register the Elementor widget, only when Elementor is active.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 */
function shihabiiuc_pg_register_elementor_widget( $widgets_manager ) {
	require_once SHIHABIIUC_PG_DIR . 'includes/class-shihabiiuc-pg-elementor-widget.php';
	$widgets_manager->register( new Shihabiiuc_PG_Elementor_Widget() );
}
add_action( 'elementor/widgets/register', 'shihabiiuc_pg_register_elementor_widget' );

Shihabiiuc_PG_Admin::init();
