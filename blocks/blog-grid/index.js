/**
 * Shihabiiuc Post Grid block (editor).
 *
 * Plain JavaScript using the WordPress globals, so no build step is needed.
 * The front end is rendered in PHP (see includes/class-shihabiiuc-pg-block.php).
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
			help: undefined === value ? __( 'Default:', 'shihabiiuc-post-grid' ) + ' ' + placeholder + 'px' : undefined,
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
			return el( 'p', null, __( 'No categories with posts found.', 'shihabiiuc-post-grid' ) );
		}

		var selected = props.value || [];

		return el(
			'div',
			{ className: 'shihabiiuc-pg-category-picker' },
			el( 'p', null, __( 'Categories (leave empty for all):', 'shihabiiuc-post-grid' ) ),
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

	registerBlockType( 'shihabiiuc-post-grid/blog-grid', {
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
					{ title: __( 'Content', 'shihabiiuc-post-grid' ), initialOpen: true },
					el( RangeControl, {
						label: __( 'Number of posts', 'shihabiiuc-post-grid' ),
						value: a.posts,
						onChange: set( 'posts' ),
						min: 1,
						max: 24,
					} ),
					el( SelectControl, {
						label: __( 'Layout', 'shihabiiuc-post-grid' ),
						value: a.layout,
						options: [
							{ label: __( 'Equal-height grid', 'shihabiiuc-post-grid' ), value: 'grid' },
							{ label: __( 'Masonry grid', 'shihabiiuc-post-grid' ), value: 'masonry' },
						],
						onChange: set( 'layout' ),
						help:
							'masonry' === a.layout
								? __( 'Images keep their original proportions.', 'shihabiiuc-post-grid' )
								: __( 'Images are cropped to 1200 × 628 from the center.', 'shihabiiuc-post-grid' ),
					} ),
					el( RangeControl, {
						label: __( 'Columns (desktop)', 'shihabiiuc-post-grid' ),
						value: a.columns,
						onChange: set( 'columns' ),
						min: 1,
						max: 4,
						help: __( 'Tablets show up to 2 columns and phones show 1.', 'shihabiiuc-post-grid' ),
					} ),
					el( RangeControl, {
						label: __( 'Excerpt length (words)', 'shihabiiuc-post-grid' ),
						value: a.excerptWords,
						onChange: set( 'excerptWords' ),
						min: 0,
						max: 100,
						help: __( 'Set to 0 to hide the excerpt.', 'shihabiiuc-post-grid' ),
					} ),
					el( TextControl, {
						label: __( 'Button text', 'shihabiiuc-post-grid' ),
						value: a.readMoreText,
						placeholder: __( 'Read More', 'shihabiiuc-post-grid' ),
						onChange: set( 'readMoreText' ),
					} ),
					el( SelectControl, {
						label: __( 'Title HTML tag', 'shihabiiuc-post-grid' ),
						value: a.titleTag,
						options: [ 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' ].map( function ( tag ) {
							return { label: tag.toUpperCase(), value: tag };
						} ),
						onChange: set( 'titleTag' ),
					} )
				),
				el(
					PanelBody,
					{ title: __( 'Query', 'shihabiiuc-post-grid' ), initialOpen: false },
					el( CategoryPicker, { value: a.categories, onChange: set( 'categories' ) } ),
					el( RangeControl, {
						label: __( 'Offset (skip the newest posts)', 'shihabiiuc-post-grid' ),
						value: a.offset,
						onChange: set( 'offset' ),
						min: 0,
						max: 50,
					} ),
					el( ToggleControl, {
						label: __( 'Hide the current post on single post pages', 'shihabiiuc-post-grid' ),
						checked: a.excludeCurrent,
						onChange: set( 'excludeCurrent' ),
					} )
				),
				el( PanelColorSettings, {
					title: __( 'Colors', 'shihabiiuc-post-grid' ),
					initialOpen: false,
					colorSettings: [
						{ label: __( 'Title', 'shihabiiuc-post-grid' ), value: a.titleColor, onChange: set( 'titleColor' ) },
						{ label: __( 'Excerpt', 'shihabiiuc-post-grid' ), value: a.excerptColor, onChange: set( 'excerptColor' ) },
						{ label: __( 'Button background', 'shihabiiuc-post-grid' ), value: a.buttonBg, onChange: set( 'buttonBg' ) },
						{ label: __( 'Button text', 'shihabiiuc-post-grid' ), value: a.buttonColor, onChange: set( 'buttonColor' ) },
						{ label: __( 'Card background', 'shihabiiuc-post-grid' ), value: a.cardBg, onChange: set( 'cardBg' ) },
						{ label: __( 'Card border', 'shihabiiuc-post-grid' ), value: a.borderColor, onChange: set( 'borderColor' ) },
					],
				} ),
				el(
					PanelBody,
					{ title: __( 'Typography', 'shihabiiuc-post-grid' ), initialOpen: false },
					px( __( 'Title font size', 'shihabiiuc-post-grid' ), a.titleFontSize, set( 'titleFontSize' ), 10, 60, 20 ),
					px( __( 'Excerpt font size', 'shihabiiuc-post-grid' ), a.excerptFontSize, set( 'excerptFontSize' ), 10, 40, 15 ),
					px( __( 'Button font size', 'shihabiiuc-post-grid' ), a.buttonFontSize, set( 'buttonFontSize' ), 10, 40, 14 )
				),
				el(
					PanelBody,
					{ title: __( 'Button', 'shihabiiuc-post-grid' ), initialOpen: false },
					px( __( 'Vertical padding', 'shihabiiuc-post-grid' ), a.buttonPaddingY, set( 'buttonPaddingY' ), 0, 60, 10 ),
					px( __( 'Horizontal padding', 'shihabiiuc-post-grid' ), a.buttonPaddingX, set( 'buttonPaddingX' ), 0, 100, 22 )
				),
				el(
					PanelBody,
					{ title: __( 'Card', 'shihabiiuc-post-grid' ), initialOpen: false },
					px( __( 'Border width', 'shihabiiuc-post-grid' ), a.borderWidth, set( 'borderWidth' ), 0, 20, 1 ),
					px( __( 'Border radius', 'shihabiiuc-post-grid' ), a.borderRadius, set( 'borderRadius' ), 0, 50, 7 ),
					px( __( 'Inner padding', 'shihabiiuc-post-grid' ), a.cardPadding, set( 'cardPadding' ), 0, 60, 12 ),
					px( __( 'Gap between cards', 'shihabiiuc-post-grid' ), a.gap, set( 'gap' ), 0, 100, 30 )
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
							block: 'shihabiiuc-post-grid/blog-grid',
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
