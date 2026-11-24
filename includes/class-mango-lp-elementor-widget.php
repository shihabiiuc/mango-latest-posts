<?php
/**
 * Elementor widget: Mango Blog Grid.
 *
 * @package MangoLatestPosts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * Elementor widget wrapping the shared renderer.
 *
 * Style controls write CSS custom properties (or plain CSS) through Elementor's
 * own CSS output, so changes preview instantly and support breakpoints.
 */
class Mango_LP_Elementor_Widget extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mango_blog_grid';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Mango Blog Grid', 'mango-latest-posts' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * Widget categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'general' );
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'posts', 'blog', 'latest', 'recent', 'grid', 'masonry', 'mango' );
	}

	/**
	 * Stylesheets the widget needs.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'mango-lp-style' );
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {
		$this->register_content_controls();
		$this->register_query_controls();
		$this->register_card_style_controls();
		$this->register_text_style_controls( 'title', __( 'Title', 'mango-latest-posts' ), '.mango-lp__title', '#222222' );
		$this->register_text_style_controls( 'excerpt', __( 'Excerpt', 'mango-latest-posts' ), '.mango-lp__excerpt', '#424242' );
		$this->register_button_style_controls();
	}

	/**
	 * Content tab: layout and text.
	 */
	private function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Layout', 'mango-latest-posts' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'posts',
			array(
				'label'   => __( 'Number of posts', 'mango-latest-posts' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => Mango_LP_Renderer::MAX_POSTS,
				'default' => 3,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'       => __( 'Layout', 'mango-latest-posts' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'grid',
				'options'     => array(
					'grid'    => __( 'Equal-height grid', 'mango-latest-posts' ),
					'masonry' => __( 'Masonry grid', 'mango-latest-posts' ),
				),
				'description' => __( 'Grid crops images to 1200 × 628. Masonry keeps their original proportions.', 'mango-latest-posts' ),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'mango-latest-posts' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors'      => array(
					'{{WRAPPER}} .mango-lp' => '--mango-lp-cols: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'excerpt_words',
			array(
				'label'       => __( 'Excerpt length (words)', 'mango-latest-posts' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => Mango_LP_Renderer::MAX_WORDS,
				'default'     => 30,
				'description' => __( 'Set to 0 to hide the excerpt.', 'mango-latest-posts' ),
			)
		);

		$this->add_control(
			'read_more_text',
			array(
				'label'   => __( 'Button text', 'mango-latest-posts' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Read More', 'mango-latest-posts' ),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title HTML tag', 'mango-latest-posts' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => array(
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'p'   => 'p',
					'div' => 'div',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content tab: which posts to show.
	 */
	private function register_query_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'mango-latest-posts' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'categories',
			array(
				'label'       => __( 'Categories', 'mango-latest-posts' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->get_category_options(),
				'description' => __( 'Leave empty to show posts from all categories.', 'mango-latest-posts' ),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'       => __( 'Offset', 'mango-latest-posts' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => Mango_LP_Renderer::MAX_OFFSET,
				'default'     => 0,
				'description' => __( 'Number of newest posts to skip.', 'mango-latest-posts' ),
			)
		);

		$this->add_control(
			'exclude_current',
			array(
				'label'        => __( 'Hide current post', 'mango-latest-posts' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => __( 'On single post pages, do not list the post being viewed.', 'mango-latest-posts' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: card box.
	 */
	private function register_card_style_controls() {
		$this->start_controls_section(
			'section_style_card',
			array(
				'label' => __( 'Card', 'mango-latest-posts' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background color', 'mango-latest-posts' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .mango-lp' => '--mango-lp-card-bg: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'     => __( 'Border color', 'mango-latest-posts' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cfcfcf',
				'selectors' => array( '{{WRAPPER}} .mango-lp' => '--mango-lp-border-color: {{VALUE}};' ),
			)
		);

		$this->add_slider( 'border_width', __( 'Border width', 'mango-latest-posts' ), '--mango-lp-border-width', 1, 20 );
		$this->add_slider( 'border_radius', __( 'Border radius', 'mango-latest-posts' ), '--mango-lp-radius', 7, 50 );
		$this->add_slider( 'card_padding', __( 'Inner padding', 'mango-latest-posts' ), '--mango-lp-card-padding', 12, 60, true );
		$this->add_slider( 'gap', __( 'Gap between cards', 'mango-latest-posts' ), '--mango-lp-gap', 30, 100, true );

		$this->end_controls_section();
	}

	/**
	 * Style tab: color + typography for a text element.
	 *
	 * @param string $key      Control prefix.
	 * @param string $label    Section label.
	 * @param string $selector Element selector.
	 * @param string $color    Default color.
	 */
	private function register_text_style_controls( $key, $label, $selector, $color ) {
		$this->start_controls_section(
			'section_style_' . $key,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			$key . '_color',
			array(
				'label'     => __( 'Color', 'mango-latest-posts' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $color,
				'selectors' => array( '{{WRAPPER}} .mango-lp' => '--mango-lp-' . $key . '-color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $key . '_typography',
				'selector' => '{{WRAPPER}} ' . $selector . ', {{WRAPPER}} ' . $selector . ' a',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: Read More button.
	 */
	private function register_button_style_controls() {
		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Button', 'mango-latest-posts' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .mango-lp__button',
			)
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab( 'button_normal', array( 'label' => __( 'Normal', 'mango-latest-posts' ) ) );

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Background color', 'mango-latest-posts' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#00a63e',
				'selectors' => array( '{{WRAPPER}} .mango-lp' => '--mango-lp-button-bg: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text color', 'mango-latest-posts' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .mango-lp' => '--mango-lp-button-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'button_hover', array( 'label' => __( 'Hover', 'mango-latest-posts' ) ) );

		$this->add_control(
			'button_hover_bg',
			array(
				'label'     => __( 'Background color', 'mango-latest-posts' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .mango-lp' => '--mango-lp-button-hover-bg: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'button_hover_color',
			array(
				'label'     => __( 'Text color', 'mango-latest-posts' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .mango-lp' => '--mango-lp-button-hover-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'mango-latest-posts' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .mango-lp' => '--mango-lp-button-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_slider( 'button_radius', __( 'Border radius', 'mango-latest-posts' ), '--mango-lp-button-radius', 4, 50 );

		$this->end_controls_section();
	}

	/**
	 * Add a px slider that writes a CSS custom property.
	 *
	 * @param string $id         Control ID.
	 * @param string $label      Label.
	 * @param string $css_var    CSS custom property.
	 * @param int    $default    Default value in px.
	 * @param int    $max        Maximum.
	 * @param bool   $responsive Whether the control is responsive.
	 */
	private function add_slider( $id, $label, $css_var, $default, $max, $responsive = false ) {
		$args = array(
			'label'      => $label,
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array(
				'px' => array(
					'min' => 0,
					'max' => $max,
				),
			),
			'default'    => array(
				'unit' => 'px',
				'size' => $default,
			),
			'selectors'  => array( '{{WRAPPER}} .mango-lp' => $css_var . ': {{SIZE}}{{UNIT}};' ),
		);

		if ( $responsive ) {
			$this->add_responsive_control( $id, $args );
		} else {
			$this->add_control( $id, $args );
		}
	}

	/**
	 * Category options for the SELECT2 control.
	 *
	 * @return array<string, string>
	 */
	private function get_category_options() {
		$options = array();
		$terms   = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) ) {
			return $options;
		}

		foreach ( $terms as $term ) {
			$options[ (string) $term->term_id ] = $term->name;
		}

		return $options;
	}

	/**
	 * Render the widget on the front end and in the editor preview.
	 */
	protected function render() {
		$settings = wp_parse_args(
			$this->get_settings_for_display(),
			array(
				'posts'           => 3,
				'layout'          => 'grid',
				'excerpt_words'   => 30,
				'categories'      => array(),
				'offset'          => 0,
				'exclude_current' => 'yes',
				'read_more_text'  => '',
				'title_tag'       => 'h3',
			)
		);

		// Columns, colors and sizes are printed by Elementor as CSS, so only content settings are passed.
		Mango_LP_Renderer::output(
			array(
				'posts'           => $settings['posts'],
				'layout'          => $settings['layout'],
				'excerpt_words'   => $settings['excerpt_words'],
				'categories'      => ! empty( $settings['categories'] ) ? $settings['categories'] : array(),
				'offset'          => $settings['offset'],
				'exclude_current' => 'yes' === $settings['exclude_current'],
				'read_more_text'  => $settings['read_more_text'],
				'title_tag'       => $settings['title_tag'],
				'inline_styles'   => false,
			)
		);
	}
}
