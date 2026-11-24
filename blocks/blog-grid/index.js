/**
 * Mango Blog Grid block (editor).
 *
 * Plain JavaScript using the WordPress globals, so no build step is needed.
 * The front end is rendered in PHP (see includes/class-mango-lp-block.php).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelColorSettings = wp.blockEditor.PanelColorSettings;
	var components = wp.components;
	var PanelBody = components.PanelBody;
	var RangeControl = components.RangeControl;
	var SelectControl = components.SelectControl;
	var ToggleControl = components.ToggleControl;
	var TextControl = components.TextControl;
	var CheckboxControl = components.CheckboxControl;
	var Disabled = components.Disabled;
	var Spinner = components.Spinner;
	var useSelect = wp.data.useSelect;
	var ServerSideRender = wp.serverSideRender;

	function px( label, value, onChange, min, max, placeholder ) {
		return el( RangeControl, {
			label: label,
			value: value,
			onChange: onChange,
			min: min,
			max: max,
			allowReset: true,
			initialPosition: placeholder,
			help: undefined === value ? __( 'Default:', 'mango-latest-posts' ) + ' ' + placeholder + 'px' : undefined,
		} );
	}

	function CategoryPicker( props ) {
		var categories = useSelect( function ( select ) {
			return select( 'core' ).getEntityRecords( 'taxonomy', 'category', {
				per_page: 100,
				hide_empty: true,
				_fields: 'id,name',
			} );
		}, [] );

		if ( ! categories ) {
			return el( Spinner );
		}
		if ( ! categories.length ) {
			return el( 'p', null, __( 'No categories with posts found.', 'mango-latest-posts' ) );
		}

		var selected = props.value || [];

		return el(
			'div',
			{ className: 'mango-lp-category-picker' },
			el( 'p', null, __( 'Categories (leave empty for all):', 'mango-latest-posts' ) ),
			categories.map( function ( category ) {
				return el( CheckboxControl, {
					key: category.id,
					label: category.name,
					checked: selected.indexOf( category.id ) !== -1,
					onChange: function ( checked ) {
						var next = selected.filter( function ( id ) {
							return id !== category.id;
						} );
						if ( checked ) {
							next.push( category.id );
						}
						props.onChange( next );
					},
				} );
			} )
		);
	}

	registerBlockType( 'mango-latest-posts/blog-grid', {
		edit: function ( props ) {
			var a = props.attributes;
			var set = function ( key ) {
				return function ( value ) {
					var update = {};
					update[ key ] = value;
					props.setAttributes( update );
				};
			};

			var inspector = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Content', 'mango-latest-posts' ), initialOpen: true },
					el( RangeControl, {
						label: __( 'Number of posts', 'mango-latest-posts' ),
						value: a.posts,
						onChange: set( 'posts' ),
						min: 1,
						max: 24,
					} ),
					el( SelectControl, {
						label: __( 'Layout', 'mango-latest-posts' ),
						value: a.layout,
						options: [
							{ label: __( 'Equal-height grid', 'mango-latest-posts' ), value: 'grid' },
							{ label: __( 'Masonry grid', 'mango-latest-posts' ), value: 'masonry' },
						],
						onChange: set( 'layout' ),
						help:
							'masonry' === a.layout
								? __( 'Images keep their original proportions.', 'mango-latest-posts' )
								: __( 'Images are cropped to 1200 × 628 from the center.', 'mango-latest-posts' ),
					} ),
					el( RangeControl, {
						label: __( 'Columns (desktop)', 'mango-latest-posts' ),
						value: a.columns,
						onChange: set( 'columns' ),
						min: 1,
						max: 4,
						help: __( 'Tablets show up to 2 columns and phones show 1.', 'mango-latest-posts' ),
					} ),
					el( RangeControl, {
						label: __( 'Excerpt length (words)', 'mango-latest-posts' ),
						value: a.excerptWords,
						onChange: set( 'excerptWords' ),
						min: 0,
						max: 100,
						help: __( 'Set to 0 to hide the excerpt.', 'mango-latest-posts' ),
					} ),
					el( TextControl, {
						label: __( 'Button text', 'mango-latest-posts' ),
						value: a.readMoreText,
						placeholder: __( 'Read More', 'mango-latest-posts' ),
						onChange: set( 'readMoreText' ),
					} ),
					el( SelectControl, {
						label: __( 'Title HTML tag', 'mango-latest-posts' ),
						value: a.titleTag,
						options: [ 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' ].map( function ( tag ) {
							return { label: tag.toUpperCase(), value: tag };
						} ),
						onChange: set( 'titleTag' ),
					} )
				),
				el(
					PanelBody,
					{ title: __( 'Query', 'mango-latest-posts' ), initialOpen: false },
					el( CategoryPicker, { value: a.categories, onChange: set( 'categories' ) } ),
					el( RangeControl, {
						label: __( 'Offset (skip the newest posts)', 'mango-latest-posts' ),
						value: a.offset,
						onChange: set( 'offset' ),
						min: 0,
						max: 50,
					} ),
					el( ToggleControl, {
						label: __( 'Hide the current post on single post pages', 'mango-latest-posts' ),
						checked: a.excludeCurrent,
						onChange: set( 'excludeCurrent' ),
					} )
				),
				el( PanelColorSettings, {
					title: __( 'Colors', 'mango-latest-posts' ),
					initialOpen: false,
					colorSettings: [
						{ label: __( 'Title', 'mango-latest-posts' ), value: a.titleColor, onChange: set( 'titleColor' ) },
						{ label: __( 'Excerpt', 'mango-latest-posts' ), value: a.excerptColor, onChange: set( 'excerptColor' ) },
						{ label: __( 'Button background', 'mango-latest-posts' ), value: a.buttonBg, onChange: set( 'buttonBg' ) },
						{ label: __( 'Button text', 'mango-latest-posts' ), value: a.buttonColor, onChange: set( 'buttonColor' ) },
						{ label: __( 'Card background', 'mango-latest-posts' ), value: a.cardBg, onChange: set( 'cardBg' ) },
						{ label: __( 'Card border', 'mango-latest-posts' ), value: a.borderColor, onChange: set( 'borderColor' ) },
					],
				} ),
				el(
					PanelBody,
					{ title: __( 'Typography', 'mango-latest-posts' ), initialOpen: false },
					px( __( 'Title font size', 'mango-latest-posts' ), a.titleFontSize, set( 'titleFontSize' ), 10, 60, 20 ),
					px( __( 'Excerpt font size', 'mango-latest-posts' ), a.excerptFontSize, set( 'excerptFontSize' ), 10, 40, 15 ),
					px( __( 'Button font size', 'mango-latest-posts' ), a.buttonFontSize, set( 'buttonFontSize' ), 10, 40, 14 )
				),
				el(
					PanelBody,
					{ title: __( 'Button', 'mango-latest-posts' ), initialOpen: false },
					px( __( 'Vertical padding', 'mango-latest-posts' ), a.buttonPaddingY, set( 'buttonPaddingY' ), 0, 60, 10 ),
					px( __( 'Horizontal padding', 'mango-latest-posts' ), a.buttonPaddingX, set( 'buttonPaddingX' ), 0, 100, 22 )
				),
				el(
					PanelBody,
					{ title: __( 'Card', 'mango-latest-posts' ), initialOpen: false },
					px( __( 'Border width', 'mango-latest-posts' ), a.borderWidth, set( 'borderWidth' ), 0, 20, 1 ),
					px( __( 'Border radius', 'mango-latest-posts' ), a.borderRadius, set( 'borderRadius' ), 0, 50, 7 ),
					px( __( 'Inner padding', 'mango-latest-posts' ), a.cardPadding, set( 'cardPadding' ), 0, 60, 12 ),
					px( __( 'Gap between cards', 'mango-latest-posts' ), a.gap, set( 'gap' ), 0, 100, 30 )
				)
			);

			return el(
				Fragment,
				null,
				inspector,
				el(
					'div',
					useBlockProps(),
					el(
						Disabled,
						null,
						el( ServerSideRender, {
							block: 'mango-latest-posts/blog-grid',
							attributes: a,
						} )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
