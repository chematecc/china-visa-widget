// Block "China Visa Check": settings in the sidebar, the live checker as the preview (rendered by the plugin's PHP).
( function ( blocks, element, blockEditor, components, i18n, ServerSideRender ) {
	var el = element.createElement;
	var __ = i18n.__;
	var languages = ( window.chinavisacheckBlock && window.chinavisacheckBlock.languages ) || {};
	var langOptions = [ { label: __( 'Same as this site', 'china-visa-check' ), value: '' } ].concat(
		Object.keys( languages ).map( function ( code ) {
			return { label: languages[ code ], value: code };
		} )
	);

	blocks.registerBlockType( 'chinavisacheck/checker', {
		apiVersion: 2,
		title: __( 'China Visa Check', 'china-visa-check' ),
		description: __( 'Visitors pick their passport and see whether they need a visa for China.', 'china-visa-check' ),
		icon: 'airplane',
		category: 'widgets',
		keywords: [ 'visa', 'china', 'travel', 'passport' ],
		attributes: {
			passport: { type: 'string', default: '' },
			lang: { type: 'string', default: '' },
			theme: { type: 'string', default: 'auto' },
			credit: { type: 'boolean', default: false },
		},
		edit: function ( props ) {
			var a = props.attributes;
			var set = function ( key ) {
				return function ( value ) {
					var next = {};
					next[ key ] = value;
					props.setAttributes( next );
				};
			};
			return el(
				'div',
				blockEditor.useBlockProps(),
				el(
					blockEditor.InspectorControls,
					{},
					el(
						components.PanelBody,
						{ title: __( 'Checker settings', 'china-visa-check' ) },
						el( components.SelectControl, {
							label: __( 'Language', 'china-visa-check' ),
							value: a.lang,
							options: langOptions,
							onChange: set( 'lang' ),
						} ),
						el( components.TextControl, {
							label: __( 'Passport shown first', 'china-visa-check' ),
							help: __( 'Two-letter country code, e.g. JP. Empty: the visitor\'s own country.', 'china-visa-check' ),
							value: a.passport,
							maxLength: 2,
							onChange: function ( value ) {
								set( 'passport' )( value.replace( /[^a-zA-Z]/g, '' ).toLowerCase() );
							},
						} ),
						el( components.SelectControl, {
							label: __( 'Colours', 'china-visa-check' ),
							value: a.theme,
							options: [
								{ label: __( 'Follow the visitor\'s device', 'china-visa-check' ), value: 'auto' },
								{ label: __( 'Light', 'china-visa-check' ), value: 'light' },
								{ label: __( 'Dark', 'china-visa-check' ), value: 'dark' },
							],
							onChange: set( 'theme' ),
						} ),
						el( components.ToggleControl, {
							label: __( 'Show a "China Visa Check" link under it', 'china-visa-check' ),
							checked: a.credit,
							onChange: set( 'credit' ),
						} )
					)
				),
				el( ServerSideRender, { block: 'chinavisacheck/checker', attributes: a } )
			);
		},
		save: function () {
			return null; // rendered by PHP, so the checker always uses the plugin's current markup
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n, window.wp.serverSideRender );
