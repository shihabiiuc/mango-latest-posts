<?php
/**
 * Shared renderer used by the Elementor widget, the Gutenberg block and the shortcode.
 *
 * @package ShihabiiucPostGrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the post query and prints the blog grid markup.
 */
class Shihabiiuc_PG_Renderer {

	const IMAGE_SIZE = 'shihabiiuc-pg-card';

	const MAX_POSTS   = 24;
	const MAX_COLUMNS = 4;
	const MAX_WORDS   = 100;
	const MAX_OFFSET  = 50;

	/**
	 * Style settings and the CSS custom property each one sets.
	 *
	 * @var array<string, array{0: string, 1: string}> Setting => [ CSS variable, sanitizer ].
	 */
	const STYLE_VARS = array(
		'title_color'       => array( '--shihabiiuc-pg-title-color', 'color' ),
		'title_font_size'   => array( '--shihabiiuc-pg-title-size', 'length' ),
		'excerpt_color'     => array( '--shihabiiuc-pg-excerpt-color', 'color' ),
		'excerpt_font_size' => array( '--shihabiiuc-pg-excerpt-size', 'length' ),
		'button_bg'         => array( '--shihabiiuc-pg-button-bg', 'color' ),
		'button_color'      => array( '--shihabiiuc-pg-button-color', 'color' ),
		'button_font_size'  => array( '--shihabiiuc-pg-button-size', 'length' ),
		'button_padding'    => array( '--shihabiiuc-pg-button-padding', 'box' ),
		'card_bg'           => array( '--shihabiiuc-pg-card-bg', 'color' ),
		'border_color'      => array( '--shihabiiuc-pg-border-color', 'color' ),
		'border_width'      => array( '--shihabiiuc-pg-border-width', 'length' ),
		'border_radius'     => array( '--shihabiiuc-pg-radius', 'length' ),
		'card_padding'      => array( '--shihabiiuc-pg-card-padding', 'box' ),
		'gap'               => array( '--shihabiiuc-pg-gap', 'length' ),
	);

	/**
	 * Default settings. Empty style values fall back to the stylesheet defaults.
	 *
	 * @return array
	 */
	public static function defaults() {
		$defaults = array(
			'posts'           => 3,
			'columns'         => 3,
			'layout'          => 'grid',
			'excerpt_words'   => 30,
			'categories'      => array(),
			'offset'          => 0,
			'exclude_current' => true,
			'read_more_text'  => __( 'Read More', 'shihabiiuc-post-grid' ),
			'title_tag'       => 'h3',
			// When false, no inline CSS variables are printed (Elementor outputs its own CSS).
			'inline_styles'   => true,
		);

		foreach ( array_keys( self::STYLE_VARS ) as $key ) {
			$defaults[ $key ] = '';
		}

		return $defaults;
	}

	/**
	 * Merge with defaults and sanitize every setting.
	 *
	 * @param array $args Raw settings.
	 * @return array
	 */
	public static function normalize( $args ) {
		$args = wp_parse_args( (array) $args, self::defaults() );

		$args['posts']         = self::clamp( $args['posts'], 1, self::MAX_POSTS );
		$args['columns']       = self::clamp( $args['columns'], 1, self::MAX_COLUMNS );
		$args['excerpt_words'] = self::clamp( $args['excerpt_words'], 0, self::MAX_WORDS );
		$args['offset']        = self::clamp( $args['offset'], 0, self::MAX_OFFSET );
		$args['layout']        = ( 'masonry' === $args['layout'] ) ? 'masonry' : 'grid';
		$args['categories']    = self::sanitize_categories( $args['categories'] );

		$args['exclude_current'] = filter_var( $args['exclude_current'], FILTER_VALIDATE_BOOLEAN );
		$args['inline_styles']   = filter_var( $args['inline_styles'], FILTER_VALIDATE_BOOLEAN );

		$args['read_more_text'] = sanitize_text_field( (string) $args['read_more_text'] );
		if ( '' === $args['read_more_text'] ) {
			$args['read_more_text'] = __( 'Read More', 'shihabiiuc-post-grid' );
		}

		$allowed_tags      = array( 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' );
		$args['title_tag'] = in_array( $args['title_tag'], $allowed_tags, true ) ? $args['title_tag'] : 'h3';

		foreach ( self::STYLE_VARS as $key => $config ) {
			$args[ $key ] = self::sanitize_style_value( $args[ $key ], $config[1] );
		}

		return $args;
	}

	/**
	 * Render the grid and return it as a string.
	 *
	 * @param array $args Settings.
	 * @return string
	 */
	public static function render( $args ) {
		ob_start();
		self::output( $args );
		return (string) ob_get_clean();
	}

	/**
	 * Print the grid.
	 *
	 * @param array $args Settings.
	 */
	public static function output( $args ) {
		$args  = self::normalize( $args );
		$posts = self::get_posts( $args );

		wp_enqueue_style( 'shihabiiuc-pg-style' );

		if ( empty( $posts ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				echo '<p class="shihabiiuc-pg-empty">' . esc_html__( 'Shihabiiuc Post Grid: no posts found for these settings.', 'shihabiiuc-post-grid' ) . '</p>';
			}
			return;
		}

		$classes = array( 'shihabiiuc-pg', 'shihabiiuc-pg--' . $args['layout'] );
		$style   = $args['inline_styles'] ? self::build_inline_style( $args ) : '';
		$tag     = $args['title_tag'];
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $style ? ' style="' . esc_attr( $style ) . '"' : ''; ?>>
			<?php foreach ( $posts as $post ) : ?>
				<?php
				$permalink = get_permalink( $post );
				$title     = get_the_title( $post );
				$excerpt   = $args['excerpt_words'] > 0 ? self::get_excerpt( $post, $args['excerpt_words'] ) : '';
				?>
				<article class="shihabiiuc-pg__card">
					<a class="shihabiiuc-pg__thumb" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
						<?php self::output_image( $post, $args['layout'] ); ?>
					</a>
					<div class="shihabiiuc-pg__body">
						<<?php echo tag_escape( $tag ); ?> class="shihabiiuc-pg__title">
							<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
						</<?php echo tag_escape( $tag ); ?>>
						<?php if ( '' !== $excerpt ) : ?>
							<p class="shihabiiuc-pg__excerpt"><?php echo esc_html( $excerpt ); ?></p>
						<?php endif; ?>
						<a class="shihabiiuc-pg__button" href="<?php echo esc_url( $permalink ); ?>">
							<span class="shihabiiuc-pg__button-text"><?php echo esc_html( $args['read_more_text'] ); ?></span>
							<span class="shihabiiuc-pg__sr-only">: <?php echo esc_html( $title ); ?></span>
							<svg class="shihabiiuc-pg__chevron" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Query the posts to display.
	 *
	 * The current post is skipped in PHP (instead of with post__not_in) so the query stays cacheable.
	 *
	 * @param array $args Normalized settings.
	 * @return WP_Post[]
	 */
	private static function get_posts( $args ) {
		$exclude_id = 0;
		if ( $args['exclude_current'] && is_singular() ) {
			$exclude_id = (int) get_queried_object_id();
		}

		$query_args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $args['posts'] + ( $exclude_id ? 1 : 0 ),
			'offset'              => $args['offset'],
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( ! empty( $args['categories'] ) ) {
			$query_args['category__in'] = $args['categories'];
		}

		/**
		 * Filter the WP_Query arguments used by Shihabiiuc Post Grid.
		 *
		 * @param array $query_args WP_Query arguments.
		 * @param array $args       Normalized widget/block/shortcode settings.
		 */
		$query_args = apply_filters( 'shihabiiuc_pg_query_args', $query_args, $args );

		$query = new WP_Query( $query_args );
		$posts = array();

		foreach ( $query->posts as $post ) {
			if ( (int) $post->ID === $exclude_id ) {
				continue;
			}
			$posts[] = $post;
			if ( count( $posts ) >= $args['posts'] ) {
				break;
			}
		}

		return $posts;
	}

	/**
	 * Print the featured image, or the placeholder when the post has none.
	 *
	 * @param WP_Post $post   Post.
	 * @param string  $layout 'grid' (cropped 1200x628) or 'masonry' (original proportions).
	 */
	private static function output_image( $post, $layout ) {
		$size     = ( 'masonry' === $layout ) ? 'large' : self::IMAGE_SIZE;
		$thumb_id = get_post_thumbnail_id( $post );

		if ( $thumb_id ) {
			$image = wp_get_attachment_image( $thumb_id, $size, false, array( 'class' => 'shihabiiuc-pg__img' ) );
			if ( $image ) {
				// Core-generated and escaped; wp_kses_post() would strip srcset/sizes.
				echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return;
			}
		}

		/**
		 * Filter the placeholder image URL used for posts without a featured image.
		 *
		 * @param string $url Placeholder image URL.
		 */
		$placeholder = apply_filters( 'shihabiiuc_pg_placeholder_image_url', SHIHABIIUC_PG_URL . 'assets/images/default-image.jpg' );

		// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Bundled plugin asset, not a media library attachment.
		printf(
			'<img class="shihabiiuc-pg__img shihabiiuc-pg__img--placeholder" src="%s" width="1200" height="628" alt="" loading="lazy" decoding="async" />',
			esc_url( $placeholder )
		);
	}

	/**
	 * Get a plain-text excerpt trimmed to a number of words.
	 *
	 * Uses the manual excerpt when there is one, otherwise the post content.
	 *
	 * @param WP_Post $post  Post.
	 * @param int     $words Number of words.
	 * @return string
	 */
	private static function get_excerpt( $post, $words ) {
		$text = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;
		$text = excerpt_remove_blocks( $text );
		$text = strip_shortcodes( $text );
		$text = wp_strip_all_tags( $text );
		$text = trim( preg_replace( '/\s+/', ' ', $text ) );

		return wp_trim_words( $text, $words, '…' );
	}

	/**
	 * Build the inline CSS custom properties for the shortcode and block.
	 *
	 * @param array $args Normalized settings.
	 * @return string
	 */
	private static function build_inline_style( $args ) {
		$vars = array();

		if ( 3 !== $args['columns'] ) {
			$vars[] = '--shihabiiuc-pg-columns:' . $args['columns'];
			$vars[] = '--shihabiiuc-pg-columns-tablet:' . min( 2, $args['columns'] );
		}

		foreach ( self::STYLE_VARS as $key => $config ) {
			if ( '' !== $args[ $key ] ) {
				$vars[] = $config[0] . ':' . $args[ $key ];
			}
		}

		return implode( ';', $vars );
	}

	/**
	 * Clamp a value to an integer range.
	 *
	 * @param mixed $value Value.
	 * @param int   $min   Minimum.
	 * @param int   $max   Maximum.
	 * @return int
	 */
	private static function clamp( $value, $min, $max ) {
		return max( $min, min( $max, (int) $value ) );
	}

	/**
	 * Accept category IDs or slugs (array or comma-separated string) and return IDs.
	 *
	 * @param mixed $categories Categories.
	 * @return int[]
	 */
	private static function sanitize_categories( $categories ) {
		if ( is_string( $categories ) ) {
			$categories = explode( ',', $categories );
		}

		$ids = array();
		foreach ( (array) $categories as $category ) {
			$category = trim( (string) $category );
			if ( '' === $category ) {
				continue;
			}
			if ( ctype_digit( $category ) ) {
				$ids[] = (int) $category;
				continue;
			}
			$term = get_term_by( 'slug', sanitize_title( $category ), 'category' );
			if ( $term ) {
				$ids[] = (int) $term->term_id;
			}
		}

		return array_values( array_unique( array_filter( $ids ) ) );
	}

	/**
	 * Sanitize a style value.
	 *
	 * @param mixed  $value Value.
	 * @param string $type  'color', 'length' or 'box'.
	 * @return string Sanitized value, or '' when invalid.
	 */
	private static function sanitize_style_value( $value, $type ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}

		switch ( $type ) {
			case 'color':
				return self::sanitize_color( $value );
			case 'length':
				return self::sanitize_length( $value );
			case 'box':
				$parts = preg_split( '/\s+/', $value );
				if ( count( $parts ) > 4 ) {
					return '';
				}
				$parts = array_map( array( __CLASS__, 'sanitize_length' ), $parts );
				return in_array( '', $parts, true ) ? '' : implode( ' ', $parts );
		}

		return '';
	}

	/**
	 * Allow a number with an optional CSS unit. Bare numbers become pixels.
	 *
	 * @param string $value Value.
	 * @return string
	 */
	private static function sanitize_length( $value ) {
		if ( preg_match( '/^(\d+(?:\.\d+)?)(px|em|rem|%|vw|vh)?$/', strtolower( trim( $value ) ), $m ) ) {
			return $m[1] . ( isset( $m[2] ) && '' !== $m[2] ? $m[2] : 'px' );
		}
		return '';
	}

	/**
	 * Allow hex, rgb(a), hsl(a) and "transparent" colors.
	 *
	 * @param string $value Value.
	 * @return string
	 */
	private static function sanitize_color( $value ) {
		$value = strtolower( $value );

		if ( 'transparent' === $value ) {
			return $value;
		}
		if ( preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/', $value ) ) {
			return $value;
		}
		if ( preg_match( '/^(rgb|hsl)a?\(\s*[\d.%\s,\/]+\)$/', $value ) ) {
			return $value;
		}

		return '';
	}
}
