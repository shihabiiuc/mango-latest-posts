<?php
/**
 * Gutenberg block: Shihabiiuc Post Grid.
 *
 * @package ShihabiiucPostGrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the dynamic block and renders it on the server.
 */
class Shihabiiuc_PG_Block {

	/**
	 * Block attribute => renderer setting. Numbers are pixels.
	 *
	 * @var array<string, string>
	 */
	const ATTRIBUTE_MAP = array(
		'posts'           => 'posts',
		'columns'         => 'columns',
		'layout'          => 'layout',
		'excerptWords'    => 'excerpt_words',
		'categories'      => 'categories',
		'offset'          => 'offset',
		'excludeCurrent'  => 'exclude_current',
		'readMoreText'    => 'read_more_text',
		'titleTag'        => 'title_tag',
		'titleColor'      => 'title_color',
		'titleFontSize'   => 'title_font_size',
		'excerptColor'    => 'excerpt_color',
		'excerptFontSize' => 'excerpt_font_size',
		'buttonBg'        => 'button_bg',
		'buttonColor'     => 'button_color',
		'buttonFontSize'  => 'button_font_size',
		'cardBg'          => 'card_bg',
		'borderColor'     => 'border_color',
		'borderWidth'     => 'border_width',
		'borderRadius'    => 'border_radius',
		'cardPadding'     => 'card_padding',
		'gap'             => 'gap',
	);

	/**
	 * Register the block from block.json.
	 */
	public static function register() {
		register_block_type(
			SHIHABIIUC_PG_DIR . 'blocks/blog-grid',
			array(
				'render_callback' => array( __CLASS__, 'render' ),
			)
		);

		wp_set_script_translations( 'shihabiiuc-post-grid-blog-grid-editor-script', 'shihabiiuc-post-grid' );
	}

	/**
	 * Render callback.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function render( $attributes ) {
		$args = array();

		foreach ( self::ATTRIBUTE_MAP as $attribute => $setting ) {
			if ( isset( $attributes[ $attribute ] ) ) {
				$args[ $setting ] = $attributes[ $attribute ];
			}
		}

		if ( isset( $attributes['buttonPaddingY'] ) || isset( $attributes['buttonPaddingX'] ) ) {
			$args['button_padding'] = sprintf(
				'%dpx %dpx',
				isset( $attributes['buttonPaddingY'] ) ? (int) $attributes['buttonPaddingY'] : 10,
				isset( $attributes['buttonPaddingX'] ) ? (int) $attributes['buttonPaddingX'] : 22
			);
		}

		$html = Shihabiiuc_PG_Renderer::render( $args );

		if ( '' === $html ) {
			return '';
		}

		return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes(), $html );
	}
}
