/**
 * HTML Component CSS.
 *
 * @param string builder_type Builder Type.
 * @param string html_count HTML Count.
 *
 */
function astra_builder_html_css( builder_type = 'header', html_count ) {

    for ( var index = 1; index <= html_count; index++ ) {

		let selector = ( 'header' === builder_type ) ? '.ast-header-html-' + index : '.footer-widget-area[data-section="section-fb-html-' + index + '"]';

		let section = ( 'header' === builder_type ) ? 'section-hb-html-' + index : 'section-fb-html-' + index;

		var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
			mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

        // HTML color.
        astra_color_responsive_css(
			builder_type + '-html-' + index + '-color',
            'astra-settings[' + builder_type + '-html-' + index + 'color]',
            'color',
            selector + ' .ast-builder-html-element'
		);

		// Link color.
        astra_color_responsive_css(
			builder_type + '-html-' + index + '-l-color',
            'astra-settings[' + builder_type + '-html-' + index + 'link-color]',
            'color',
            selector + ' .ast-builder-html-element a'
		);

		// Link Hover color.
        astra_color_responsive_css(
			builder_type + '-html-' + index + '-l-h-color',
            'astra-settings[' + builder_type + '-html-' + index + 'link-h-color]',
            'color',
            selector + ' .ast-builder-html-element a:hover'
		);

		// Advanced Visibility CSS Generation.
		astra_builder_visibility_css( section, selector, 'block' );

        // Margin.
		wp.customize( 'astra-settings[' + section + '-margin]', function( value ) {
			value.bind( function( margin ) {
				if(
					margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
					margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
					margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
				) {
					var dynamicStyle = '';
					dynamicStyle += selector + ' {';
					dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
					dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
					dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
					dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
					dynamicStyle += '} ';

					dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
					dynamicStyle += selector + ' {';
					dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
					dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
					dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
					dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
					dynamicStyle += '} ';
					dynamicStyle += '} ';

					dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
					dynamicStyle += selector + ' {';
					dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
					dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
					dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
					dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
					dynamicStyle += '} ';
					dynamicStyle += '} ';
					astra_add_dynamic_css( section + '-margin-toggle-button', dynamicStyle );
				}
			} );
		} );

		// Typography CSS Generation.
		astra_responsive_font_size(
			'astra-settings[font-size-' + section + ']',
			selector + ' .ast-builder-html-element'
		);
    }
}

/**
 * Button Component CSS.
 *
 * @param string builder_type Builder Type.
 * @param string button_count Button Count.
 *
 */
function astra_builder_button_css( builder_type = 'header', button_count ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	for ( var index = 1; index <= button_count; index++ ) {

		var section = ( 'header' === builder_type ) ? 'section-hb-button-' + index : 'section-fb-button-' + index;
		var context = ( 'header' === builder_type ) ? 'hb' : 'fb';
		var prefix = 'button' + index;
		var selector = '.ast-' + builder_type + '-button-' + index + ' .ast-builder-button-wrap';
		var button_selector = '.ast-' + builder_type + '-button-' + index + '[data-section*="section-' + context + '-button-"] .ast-builder-button-wrap';

		astra_css( 'flex', 'display', '.ast-' + builder_type + '-button-' + index + '[data-section="' + section + '"]' );

		// Button Text Color.
		astra_color_responsive_css(
			context + '-button-color',
			'astra-settings[' + builder_type + '-' + prefix + '-text-color]',
			'color',
			selector + ' .ast-custom-button'
		);
		astra_color_responsive_css(
			context + '-button-color-h',
			'astra-settings[' + builder_type + '-' + prefix + '-text-h-color]',
			'color',
			selector + ':hover .ast-custom-button'
		);

		// Button Background Color.
		astra_color_responsive_css(
			context + '-button-bg-color',
			'astra-settings[' + builder_type + '-' + prefix + '-back-color]',
			'background-color',
			selector + ' .ast-custom-button'
		);
		astra_color_responsive_css(
			context + '-button-bg-color-h',
			'astra-settings[' + builder_type + '-' + prefix + '-back-h-color]',
			'background-color',
			selector + ':hover .ast-custom-button'
		);

		// Button Typography.
		astra_responsive_font_size(
			'astra-settings[' + builder_type + '-' + prefix + '-font-size]',
			button_selector + ' .ast-custom-button'
		);
		astra_generate_outside_font_family_css(
			'astra-settings[' + builder_type + '-' + prefix + '-font-family]',
			button_selector + ' .ast-custom-button'
		);
		astra_generate_font_weight_css(
			'astra-settings[' + builder_type + '-' + prefix + '-font-family]',
			'astra-settings[' + builder_type + '-' + prefix + '-font-weight]',
			'font-weight',
			button_selector + ' .ast-custom-button'
		);

		astra_font_extras_css( builder_type + '-' + prefix + '-font-extras', button_selector + ' .ast-custom-button' );

		// Border Color.
		astra_color_responsive_css(
			context + '-button-border-color',
			'astra-settings[' + builder_type + '-' + prefix + '-border-color]',
			'border-color',
			selector + ' .ast-custom-button'
		);
		astra_color_responsive_css(
			context + '-button-border-color-h',
			'astra-settings[' + builder_type + '-' + prefix + '-border-h-color]',
			'border-color',
			selector + ' .ast-custom-button:hover'
		);

		// Advanced CSS Generation.
		astra_builder_advanced_css( section, button_selector + ' .ast-custom-button' );

		// Advanced Visibility CSS Generation.
		astra_builder_visibility_css( section, selector, 'block' );

		(function (index) {
			// Builder Type Border Radius Fields
			wp.customize('astra-settings[' + builder_type + '-button' + index + '-border-radius-fields]', function (setting) {
				setting.bind(function (border) {
					let globalSelector = '.ast-' + builder_type + '-button-'+ index +' .ast-custom-button';
					let dynamicStyle = globalSelector + '{ border-top-left-radius :' + border['desktop']['top'] + border['desktop-unit']
							+ '; border-bottom-right-radius :' + border['desktop']['bottom'] + border['desktop-unit'] + '; border-bottom-left-radius :'
							+ border['desktop']['left'] + border['desktop-unit'] + '; border-top-right-radius :' + border['desktop']['right'] + border['desktop-unit'] + '; } ';

					dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['tablet']['top'] + border['tablet-unit']
							+ '; border-bottom-right-radius :' + border['tablet']['bottom'] + border['tablet-unit'] + '; border-bottom-left-radius :'
							+ border['tablet']['left'] + border['tablet-unit'] + '; border-top-right-radius :' + border['tablet']['right'] + border['tablet-unit'] + '; } } ';

					dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['mobile']['top'] + border['mobile-unit']
							+ '; border-bottom-right-radius :' + border['mobile']['bottom'] + border['mobile-unit'] + '; border-bottom-left-radius :'
							+ border['mobile']['left'] + border['mobile-unit'] + '; border-top-right-radius :' + border['mobile']['right'] + border['mobile-unit'] + '; } } ';

					astra_add_dynamic_css( 'astra-settings[' + builder_type + '-button' + index + '-border-radius-fields]', dynamicStyle);
				});
			});
			wp.customize( 'astra-settings[' + builder_type + '-button'+ index +'-border-size]', function( setting ) {
				setting.bind( function( border ) {
					var dynamicStyle = '.ast-' + builder_type + '-button-'+ index +' .ast-custom-button {';
					dynamicStyle += 'border-top-width:'  + border.top + 'px;';
					dynamicStyle += 'border-right-width:'  + border.right + 'px;';
					dynamicStyle += 'border-left-width:'   + border.left + 'px;';
					dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';
					dynamicStyle += '} ';
					astra_add_dynamic_css( 'astra-settings[' + builder_type + '-button'+ index +'-border-size]', dynamicStyle );
				} );
			} );

			if( 'footer' == builder_type ) {
				wp.customize( 'astra-settings[footer-button-'+ index +'-alignment]', function( value ) {
					value.bind( function( alignment ) {

						if( alignment.desktop != '' || alignment.tablet != '' || alignment.mobile != '' ) {
							var dynamicStyle = '';
							dynamicStyle += '.ast-footer-button-'+ index +'[data-section="section-fb-button-'+ index +'"] {';
							dynamicStyle += 'justify-content: ' + alignment['desktop'] + ';';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
							dynamicStyle += '.ast-footer-button-'+ index +'[data-section="section-fb-button-'+ index +'"] {';
							dynamicStyle += 'justify-content: ' + alignment['tablet'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
							dynamicStyle += '.ast-footer-button-'+ index +'[data-section="section-fb-button-'+ index +'"] {';
							dynamicStyle += 'justify-content: ' + alignment['mobile'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							astra_add_dynamic_css( 'footer-button-'+ index +'-alignment', dynamicStyle );
						}
					} );
				} );
			}
		})(index);
	}
}

/**
 * Social Component CSS.
 *
 * @param string builder_type Builder Type.
 * @param string section Section.
 *
 */
function astra_builder_social_css( builder_type = 'header', social_count ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	for ( var index = 1; index <= social_count; index++ ) {

		let selector = '.ast-' + builder_type + '-social-' + index + '-wrap';
		let section = ( 'header' === builder_type ) ? 'section-hb-social-icons-' + index : 'section-fb-social-icons-' + index;
		var context = ( 'header' === builder_type ) ? 'hb' : 'fb';
		var visibility_selector = '.ast-builder-layout-element[data-section="' + section + '"]';

		// Icon Color.
		astra_color_responsive_css(
			context + '-soc-color',
			'astra-settings[' + builder_type + '-social-' + index + '-color]',
			'fill',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element svg'
		);

		astra_color_responsive_css(
			context + '-soc-label-color',
			'astra-settings[' + builder_type + '-social-' + index + '-color]',
			'color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element .social-item-label'
		);

		astra_color_responsive_css(
			context + '-soc-color-h',
			'astra-settings[' + builder_type + '-social-' + index + '-h-color]',
			'color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element:hover'
		);

		astra_color_responsive_css(
			context + '-soc-label-color-h',
			'astra-settings[' + builder_type + '-social-' + index + '-h-color]',
			'color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element:hover .social-item-label'
		);

		astra_color_responsive_css(
			context + '-soc-svg-color-h',
			'astra-settings[' + builder_type + '-social-' + index + '-h-color]',
			'fill',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element:hover svg'
		);

		// Icon Background Color.
		astra_color_responsive_css(
			context + '-soc-bg-color',
			'astra-settings[' + builder_type + '-social-' + index + '-bg-color]',
			'background-color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element'
		);

		astra_color_responsive_css(
			context + '-soc-bg-color-h',
			'astra-settings[' + builder_type + '-social-' + index + '-bg-h-color]',
			'background-color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element:hover'
		);

		// Icon Label Color.
		astra_color_responsive_css(
			context + '-soc-label-color',
			'astra-settings[' + builder_type + '-social-' + index + '-label-color]',
			'color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element span.social-item-label'
		);

		astra_color_responsive_css(
			context + '-soc-label-color-h',
			'astra-settings[' + builder_type + '-social-' + index + '-label-h-color]',
			'color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element:hover span.social-item-label'
		);

		// Icon Background Space.
		astra_css(
			'astra-settings[' + builder_type + '-social-' + index + '-bg-space]',
			'padding',
			selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element',
			'px'
		);

		// Icon Brand Color.
		astra_color_responsive_css(
			context + '-soc-color',
			'astra-settings[' + builder_type + '-social-' + index + '-brand-color]',
			'fill',
			selector + ' .ast-social-color-type-official svg'
		);

		astra_color_responsive_css(
			context + '-soc-label-color',
			'astra-settings[' + builder_type + '-social-' + index + '-brand-color]',
			'color',
			selector + ' .ast-social-color-type-official .social-item-label'
		);

		// Icon Label Brand Color.
		astra_color_responsive_css(
			context + '-soc-label-color',
			'astra-settings[' + builder_type + '-social-' + index + '-brand-label-color]',
			'color',
			selector + ' .ast-social-color-type-official span.social-item-label'
		);

		// Icon Border Radius Fields
		wp.customize('astra-settings[' + builder_type + '-social-' + index + '-radius-fields]', function (setting) {
			setting.bind(function (border) {

				let globalSelector = selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element';
				let dynamicStyle = globalSelector + '{ border-top-left-radius :' + border['desktop']['top'] + border['desktop-unit']
						+ '; border-bottom-right-radius :' + border['desktop']['bottom'] + border['desktop-unit'] + '; border-bottom-left-radius :'
						+ border['desktop']['left'] + border['desktop-unit'] + '; border-top-right-radius :' + border['desktop']['right'] + border['desktop-unit'] + '; } ';

				dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['tablet']['top'] + border['tablet-unit']
						+ '; border-bottom-right-radius :' + border['tablet']['bottom'] + border['tablet-unit'] + '; border-bottom-left-radius :'
						+ border['tablet']['left'] + border['tablet-unit'] + '; border-top-right-radius :' + border['tablet']['right'] + border['tablet-unit'] + '; } } ';

				dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['mobile']['top'] + border['mobile-unit']
						+ '; border-bottom-right-radius :' + border['mobile']['bottom'] + border['mobile-unit'] + '; border-bottom-left-radius :'
						+ border['mobile']['left'] + border['mobile-unit'] + '; border-top-right-radius :' + border['mobile']['right'] + border['mobile-unit'] + '; } } ';

				astra_add_dynamic_css( builder_type + '-social-' + index + '-radius-fields', dynamicStyle);
			});
		});

		// Typography CSS Generation.
		astra_responsive_font_size(
			'astra-settings[font-size-' + section + ']',
			selector
		);

		// Advanced Visibility CSS Generation.
		astra_builder_visibility_css( section, visibility_selector, 'block' );

		// Icon Spacing.
		(function( index ) {
			// Icon Size.
			wp.customize( 'astra-settings[' + builder_type + '-social-' + index + '-size]', function( value ) {
				value.bind( function( size ) {

					if( size.desktop != '' || size.tablet != '' || size.mobile != '' ) {
						var dynamicStyle = '';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element svg {';
						dynamicStyle += 'height: ' + size.desktop + 'px;';
						dynamicStyle += 'width: ' + size.desktop + 'px;';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element svg {';
						dynamicStyle += 'height: ' + size.tablet + 'px;';
						dynamicStyle += 'width: ' + size.tablet + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element svg {';
						dynamicStyle += 'height: ' + size.mobile + 'px;';
						dynamicStyle += 'width: ' + size.mobile + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						astra_add_dynamic_css( builder_type + '-social-' + index + '-size', dynamicStyle );
					}
				} );
			} );


			// Icon Space.
			wp.customize( 'astra-settings[' + builder_type + '-social-' + index + '-space]', function( value ) {
				value.bind( function( spacing ) {
					var space = '';
					var dynamicStyle = '';
					if ( spacing.desktop != '' ) {
						space = spacing.desktop/2;
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element {';
						dynamicStyle += 'margin-left: ' + space + 'px;';
						dynamicStyle += 'margin-right: ' + space + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:first-child {';
						dynamicStyle += 'margin-left: 0;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:last-child {';
						dynamicStyle += 'margin-right: 0;';
						dynamicStyle += '} ';
					}

					if ( spacing.tablet != '' ) {
						space = spacing.tablet/2;
						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element {';
						dynamicStyle += 'margin-left: ' + space + 'px;';
						dynamicStyle += 'margin-right: ' + space + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:first-child {';
						dynamicStyle += 'margin-left: 0;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:last-child {';
						dynamicStyle += 'margin-right: 0;';
						dynamicStyle += '} ';
						dynamicStyle += '} ';
					}

					if ( spacing.mobile != '' ) {
						space = spacing.mobile/2;
						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element {';
						dynamicStyle += 'margin-left: ' + space + 'px;';
						dynamicStyle += 'margin-right: ' + space + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:first-child {';
						dynamicStyle += 'margin-left: 0;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:last-child {';
						dynamicStyle += 'margin-right: 0;';
						dynamicStyle += '} ';
						dynamicStyle += '} ';
					}

					astra_add_dynamic_css( builder_type + '-social-icons-icon-space', dynamicStyle );
				} );
			} );

			// Color Type - Custom/Official
			wp.customize( 'astra-settings[' + builder_type + '-social-' + index + '-color-type]', function ( value ) {
				value.bind( function ( newval ) {

					var side_class = 'ast-social-color-type-' + newval;

					jQuery('.ast-' + builder_type + '-social-' + index + '-wrap .' + builder_type + '-social-inner-wrap').removeClass( 'ast-social-color-type-custom' );
					jQuery('.ast-' + builder_type + '-social-' + index + '-wrap .' + builder_type + '-social-inner-wrap').removeClass( 'ast-social-color-type-official' );
					jQuery('.ast-' + builder_type + '-social-' + index + '-wrap .' + builder_type + '-social-inner-wrap').addClass( side_class );
				} );
			} );

			// Margin.
			wp.customize( 'astra-settings[' + section + '-margin]', function( value ) {
				value.bind( function( margin ) {
					if(
						margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
						margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
						margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
					) {
						var dynamicStyle = '';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';
						astra_add_dynamic_css( section + '-margin', dynamicStyle );
					}
				} );
			} );

			if ( 'footer' === builder_type ) {
				// Alignment.
				wp.customize( 'astra-settings[footer-social-' + index + '-alignment]', function( value ) {
					value.bind( function( alignment ) {
						if( alignment.desktop != '' || alignment.tablet != '' || alignment.mobile != '' ) {
							var dynamicStyle = '';
							dynamicStyle += '[data-section="section-fb-social-icons-' + index + '"] .footer-social-inner-wrap {';
							dynamicStyle += 'text-align: ' + alignment['desktop'] + ';';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
							dynamicStyle += '[data-section="section-fb-social-icons-' + index + '"] .footer-social-inner-wrap {';
							dynamicStyle += 'text-align: ' + alignment['tablet'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
							dynamicStyle += '[data-section="section-fb-social-icons-' + index + '"] .footer-social-inner-wrap {';
							dynamicStyle += 'text-align: ' + alignment['mobile'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							astra_add_dynamic_css( 'footer-social-' + index + '-alignment', dynamicStyle );
						}
					} );
				} );

			}
		})( index );
	}
}

/**
 * Widget Component CSS.
 *
 * @param string builder_type Builder Type.
 * @param string section Section.
 *
 */
function astra_builder_widget_css( builder_type = 'header' ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
        mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	let widget_count = 'header' === builder_type ? AstraBuilderWidgetData.header_widget_count: AstraBuilderWidgetData.footer_widget_count;

	for ( var index = 1; index <= widget_count; index++ ) {

		var selector = '.' + builder_type + '-widget-area[data-section="sidebar-widgets-' + builder_type + '-widget-' + index + '"]';

		var section = AstraBuilderWidgetData.has_block_editor ? 'astra-sidebar-widgets-' + builder_type + '-widget-' + index : 'sidebar-widgets-' + builder_type + '-widget-' + index;

		// Widget Content Color.
		astra_color_responsive_css(
			builder_type + '-widget-' + index + '-color',
			'astra-settings[' + builder_type + '-widget-' + index + '-color]',
			'color',
			 ( AstraBuilderWidgetData.is_flex_based_css ) ? selector + '.' + builder_type + '-widget-area-inner' : selector + ' .' + builder_type + '-widget-area-inner'
		);

		// Widget Link Color.
		astra_color_responsive_css(
			builder_type + '-widget-' + index + '-link-color',
			'astra-settings[' + builder_type + '-widget-' + index + '-link-color]',
			'color',
			( AstraBuilderWidgetData.is_flex_based_css ) ? selector + '.' + builder_type + '-widget-area-inner a' : selector + ' .' + builder_type + '-widget-area-inner a'
		);

		// Widget Link Hover Color.
		astra_color_responsive_css(
			builder_type + '-widget-' + index + '-link-h-color',
			'astra-settings[' + builder_type + '-widget-' + index + '-link-h-color]',
			'color',
			( AstraBuilderWidgetData.is_flex_based_css ) ? selector + '.' + builder_type + '-widget-area-inner a:hover' : selector + ' .' + builder_type + '-widget-area-inner a:hover'
		);

		// Widget Title Color.
		astra_color_responsive_css(
			builder_type + '-widget-' + index + '-title-color',
			'astra-settings[' + builder_type + '-widget-' + index + '-title-color]',
			'color',
			selector + ' .widget-title, ' + selector + ' h1, ' + selector + ' .widget-area h1, ' + selector + ' h2, ' + selector + ' .widget-area h2, ' + selector + ' h3, ' + selector + ' .widget-area h3, ' + selector + ' h4, ' + selector + ' .widget-area h4, ' + selector + ' h5, ' + selector + ' .widget-area h5, ' + selector + ' h6, ' + selector + ' .widget-area h6'
		);

		// Widget Title Typography.
		astra_responsive_font_size(
			'astra-settings[' + builder_type + '-widget-' + index + '-font-size]',
			selector + ' .widget-title, ' + selector + ' h1, ' + selector + ' .widget-area h1, ' + selector + ' h2, ' + selector + ' .widget-area h2, ' + selector + ' h3, ' + selector + ' .widget-area h3, ' + selector + ' h4, ' + selector + ' .widget-area h4, ' + selector + ' h5, ' + selector + ' .widget-area h5, ' + selector + ' h6, ' + selector + ' .widget-area h6'
		);

		// Widget Content Typography.
		astra_responsive_font_size(
			'astra-settings[' + builder_type + '-widget-' + index + '-content-font-size]',
			( AstraBuilderWidgetData.is_flex_based_css ) ? selector + '.' + builder_type + '-widget-area-inner' : selector + ' .' + builder_type + '-widget-area-inner'
		);

		// Advanced Visibility CSS Generation.
		astra_builder_visibility_css( section, selector, 'block' );

		(function (index) {

			var marginControl = AstraBuilderWidgetData.has_block_editor ? 'astra-sidebar-widgets-' + builder_type + '-widget-' + index + '-margin' : 'sidebar-widgets-' + builder_type + '-widget-' + index + '-margin';

			wp.customize( 'astra-settings[' + marginControl + ']', function( value ) {
				value.bind( function( margin ) {
					var selector = '.' + builder_type + '-widget-area[data-section="sidebar-widgets-' + builder_type + '-widget-' + index + '"]';
					if(
						margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
						margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
						margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
					) {
						var dynamicStyle = '';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';
						astra_add_dynamic_css( 'sidebar-widgets-' + builder_type + '-widget-' + index + '-margin', dynamicStyle );
					}
				} );
			} );

			if ( 'footer' === builder_type ) {

				wp.customize( 'astra-settings[footer-widget-alignment-' + index + ']', function( value ) {
					value.bind( function( alignment ) {
						if( alignment.desktop != '' || alignment.tablet != '' || alignment.mobile != '' ) {
							var dynamicStyle = '';
							if( AstraBuilderWidgetData.is_flex_based_css ){
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"].footer-widget-area-inner {';
							}else{
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"] .footer-widget-area-inner {';
							}
							dynamicStyle += 'text-align: ' + alignment['desktop'] + ';';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
							if( AstraBuilderWidgetData.is_flex_based_css ){
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"].footer-widget-area-inner {';
							}else{
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"] .footer-widget-area-inner {';
							}
							dynamicStyle += 'text-align: ' + alignment['tablet'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
							if( AstraBuilderWidgetData.is_flex_based_css ){
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"].footer-widget-area-inner {';
							}else{
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"] .footer-widget-area-inner {';
							}
							dynamicStyle += 'text-align: ' + alignment['mobile'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							astra_add_dynamic_css( 'footer-widget-alignment-' + index, dynamicStyle );
						}
					} );
				} );

			}
		})(index);

	}

}

/**
 * Apply Visibility CSS for the element
 *
 * @param string section Section ID.
 * @param string selector Base Selector.
 * @param string default_property default CSS property.
 */
function astra_builder_visibility_css( section, selector, default_property = 'flex' ) {

    var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	wp.customize( 'astra-settings[' + section + '-visibility-responsive]', function( setting ) {
		setting.bind( function( visibility ) {

			let dynamicStyle = '';
			let is_desktop = ( ! visibility['desktop'] ) ? 'none' : default_property ;
			let is_tablet = ( ! visibility['tablet'] ) ? 'none' : default_property ;
			let is_mobile = ( ! visibility['mobile'] ) ? 'none' : default_property ;

			dynamicStyle += selector + ' {';
			dynamicStyle += 'display: ' + is_desktop + ';';
			dynamicStyle += '} ';

			dynamicStyle +=  '@media (min-width: ' + mobile_break_point + 'px) and (max-width: ' + tablet_break_point + 'px) {';
			dynamicStyle += '.ast-header-break-point ' + selector + ' {';
			dynamicStyle += 'display: ' + is_tablet + ';';
			dynamicStyle += '} ';
			dynamicStyle += '} ';

			dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
			dynamicStyle += '.ast-header-break-point ' + selector + ' {';
			dynamicStyle += 'display: ' + is_mobile + ';';
			dynamicStyle += '} ';
			dynamicStyle += '} ';

			astra_add_dynamic_css( section + '-visibility-responsive', dynamicStyle );
		} );

	} );
}

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	var section = 'section-above-footer-builder';
	var selector = '.site-above-footer-wrap[data-section="section-above-footer-builder"]';

	// Footer Vertical Alignment.
    astra_css(
        'astra-settings[hba-footer-vertical-alignment]',
        'align-items',
        selector + ' .ast-builder-grid-row, ' + selector + ' .site-footer-section'
    );

	// Border Bottom width.
	wp.customize( 'astra-settings[hba-footer-separator]', function( setting ) {
		setting.bind( function( separator ) {

			var dynamicStyle = '';

			if ( '' !== separator ) {
				dynamicStyle = selector + ' {';
				dynamicStyle += 'border-top-width: ' + separator + 'px;';
				dynamicStyle += 'border-top-style: solid';
				dynamicStyle += '} ';
			}

			astra_add_dynamic_css( 'hba-footer-separator', dynamicStyle );

		} );
	} );

	// Inner Space.
	wp.customize( 'astra-settings[hba-inner-spacing]', function( value ) {
		value.bind( function( spacing ) {
			var dynamicStyle = '';
			if ( spacing.desktop != '' ) {
				dynamicStyle += selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'grid-column-gap: ' + spacing.desktop + 'px;';
				dynamicStyle += '} ';
			}

			if ( spacing.tablet != '' ) {
				dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'grid-column-gap: ' + spacing.tablet + 'px;';
				dynamicStyle += 'grid-row-gap: ' + spacing.tablet + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';
			}

			if ( spacing.mobile != '' ) {
				dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'grid-column-gap: ' + spacing.mobile + 'px;';
				dynamicStyle += 'grid-row-gap: ' + spacing.mobile + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';
			}

			astra_add_dynamic_css( 'hba-inner-spacing-toggle-button', dynamicStyle );
		} );
	} );

	// Border Color.
	wp.customize( 'astra-settings[hba-footer-top-border-color]', function( setting ) {
		setting.bind( function( color ) {

			var dynamicStyle = '';

			if ( '' !== color ) {
				dynamicStyle = selector + ' {';
				dynamicStyle += 'border-top-color: ' + color + ';';
				dynamicStyle += 'border-top-style: solid';
				dynamicStyle += '} ';
			}

			astra_add_dynamic_css( 'hba-footer-top-border-color', dynamicStyle );

		} );
	} );

	// Primary Header - Layout.
	wp.customize( 'astra-settings[hba-footer-layout-width]', function( setting ) {
		setting.bind( function( layout ) {

			var dynamicStyle = '';

			if ( 'content' == layout ) {
				dynamicStyle = selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'max-width: ' + AstraBuilderPrimaryFooterData.footer_content_width + 'px;';
				dynamicStyle += 'margin-left: auto;';
				dynamicStyle += 'margin-right: auto;';
				dynamicStyle += '} ';
			}

			if ( 'full' == layout ) {
				dynamicStyle = selector + ' .ast-builder-grid-row {';
					dynamicStyle += 'max-width: 100%;';
					dynamicStyle += 'padding-right: 35px; padding-left: 35px;';
				dynamicStyle += '} ';
			}

			astra_add_dynamic_css( 'hba-footer-layout-width', dynamicStyle );

		} );
	} );

	// Responsive BG styles > Above Footer Row.
	astra_apply_responsive_background_css( 'astra-settings[hba-footer-bg-obj-responsive]', selector, 'desktop' );
	astra_apply_responsive_background_css( 'astra-settings[hba-footer-bg-obj-responsive]', selector, 'tablet' );
	astra_apply_responsive_background_css( 'astra-settings[hba-footer-bg-obj-responsive]', selector, 'mobile' );

	// Advanced CSS Generation.
	astra_builder_advanced_css( section, selector );

	// Advanced Visibility CSS Generation.
	astra_builder_visibility_css( section, selector, 'grid' );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	var section = 'section-below-footer-builder';
	var selector = '.site-below-footer-wrap[data-section="section-below-footer-builder"]';

	// Footer Vertical Alignment.
    astra_css(
        'astra-settings[hbb-footer-vertical-alignment]',
        'align-items',
        selector + ' .ast-builder-grid-row, ' + selector + ' .site-footer-section'
    );

	// Inner Space.
	wp.customize( 'astra-settings[hbb-inner-spacing]', function( value ) {
		value.bind( function( spacing ) {
			var dynamicStyle = '';
			if ( spacing.desktop != '' ) {
				dynamicStyle += selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'grid-column-gap: ' + spacing.desktop + 'px;';
				dynamicStyle += '} ';
			}
			
			if ( spacing.tablet != '' ) {
				dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'grid-column-gap: ' + spacing.tablet + 'px;';
				dynamicStyle += 'grid-row-gap: ' + spacing.tablet + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';
			}

			if ( spacing.mobile != '' ) {
				dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'grid-column-gap: ' + spacing.mobile + 'px;';
				dynamicStyle += 'grid-row-gap: ' + spacing.mobile + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';
			}

			astra_add_dynamic_css( 'hbb-inner-spacing-toggle-button', dynamicStyle );
		} );
	} );

	// Border Top width.
	wp.customize( 'astra-settings[hbb-footer-separator]', function( setting ) {
		setting.bind( function( separator ) {

			var dynamicStyle = '';

			if ( '' !== separator ) {
				dynamicStyle = selector + ' {';
				dynamicStyle += 'border-top-width: ' + separator + 'px;';
				dynamicStyle += 'border-top-style: solid';
				dynamicStyle += '} ';
			}

			astra_add_dynamic_css( 'hbb-footer-separator', dynamicStyle );

		} );
	} );

	// Border Color.

	wp.customize( 'astra-settings[hbb-footer-top-border-color]', function( setting ) {
		setting.bind( function( color ) {

			var dynamicStyle = '';

			if ( '' !== color ) {
				dynamicStyle = selector + ' {';
				dynamicStyle += 'border-top-color: ' + color + ';';
				dynamicStyle += 'border-top-style: solid';
				dynamicStyle += '} ';
			}

			astra_add_dynamic_css( 'hbb-footer-top-border-color', dynamicStyle );

		} );
	} );

	// Primary Header - Layout.
	wp.customize( 'astra-settings[hbb-footer-layout-width]', function( setting ) {
		setting.bind( function( layout ) {

			var dynamicStyle = '';

			if ( 'content' == layout ) {
				dynamicStyle = selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'max-width: ' + AstraBuilderPrimaryFooterData.footer_content_width + 'px;';
				dynamicStyle += 'margin-left: auto;';
				dynamicStyle += 'margin-right: auto;';
				dynamicStyle += '} ';
			}

			if ( 'full' == layout ) {
				dynamicStyle = selector + ' .ast-builder-grid-row {';
					dynamicStyle += 'max-width: 100%;';
					dynamicStyle += 'padding-right: 35px; padding-left: 35px;';
				dynamicStyle += '} ';
			}

			astra_add_dynamic_css( 'hbb-footer-layout-width', dynamicStyle );

		} );
	} );


	// Responsive BG styles > Below Footer Row.
	astra_apply_responsive_background_css( 'astra-settings[hbb-footer-bg-obj-responsive]', selector, 'desktop' );
	astra_apply_responsive_background_css( 'astra-settings[hbb-footer-bg-obj-responsive]', selector, 'tablet' );
	astra_apply_responsive_background_css( 'astra-settings[hbb-footer-bg-obj-responsive]', selector, 'mobile' );

	// Advanced CSS Generation.
	astra_builder_advanced_css( section, selector );

	// Advanced Visibility CSS Generation.
	astra_builder_visibility_css( section, selector, 'grid' );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	astra_builder_button_css( 'footer', AstraBuilderFooterButtonData.component_limit );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra Builder
 * @since 3.0.0
 */

( function( $ ) {

    var selector = '.ast-footer-copyright';
    var visibility_selector = '.ast-footer-copyright.ast-builder-layout-element';

    var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
        mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

    // HTML color.
    astra_css(
        'astra-settings[footer-copyright-color]',
        'color',
        selector
    );

    // Typography CSS Generation.
    astra_responsive_font_size(
        'astra-settings[font-size-section-footer-copyright]',
        selector
    );

    wp.customize( 'astra-settings[footer-copyright-alignment]', function( value ) {
        value.bind( function( alignment ) {
            if( alignment.desktop != '' || alignment.tablet != '' || alignment.mobile != '' ) {
                var dynamicStyle = '';
                dynamicStyle += '.ast-footer-copyright {';
                dynamicStyle += 'text-align: ' + alignment['desktop'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += '.ast-footer-copyright {';
                dynamicStyle += 'text-align: ' + alignment['tablet'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += '.ast-footer-copyright {';
                dynamicStyle += 'text-align: ' + alignment['mobile'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                astra_add_dynamic_css( 'footer-copyright-alignment', dynamicStyle );
            }
        } );
    } );

    // Margin.
    wp.customize( 'astra-settings[section-footer-copyright-margin]', function( value ) {
        value.bind( function( margin ) {
            if(
                margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
                margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
                margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
            ) {
                var dynamicStyle = '';
                dynamicStyle += selector + '.site-footer-focus-item {';
                dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += selector + '.site-footer-focus-item {';
                dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += selector + '.site-footer-focus-item {';
                dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';
                astra_add_dynamic_css( 'footer-copyright-margin', dynamicStyle );
            }
        } );
    } );

    // Advanced Visibility CSS Generation.
    astra_builder_visibility_css( 'section-footer-copyright', visibility_selector );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra Builder
 * @since 3.0.0
 */

( function( $ ) {

    var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
        mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

    astra_builder_html_css( 'footer', AstraBuilderFooterHTMLData.component_limit );

    for( var index = 1; index <= AstraBuilderFooterHTMLData.component_limit ; index++ ) {
		(function( index ) {
			wp.customize( 'astra-settings[footer-html-'+ index +'-alignment]', function( value ) {
				value.bind( function( alignment ) {
					if( alignment.desktop != '' || alignment.tablet != '' || alignment.mobile != '' ) {
						var dynamicStyle = '';
						dynamicStyle += '.footer-widget-area[data-section="section-fb-html-'+ index +'"] .ast-builder-html-element {';
						dynamicStyle += 'text-align: ' + alignment['desktop'] + ';';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += '.footer-widget-area[data-section="section-fb-html-'+ index +'"] .ast-builder-html-element {';
						dynamicStyle += 'text-align: ' + alignment['tablet'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += '.footer-widget-area[data-section="section-fb-html-'+ index +'"] .ast-builder-html-element {';
						dynamicStyle += 'text-align: ' + alignment['mobile'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						astra_add_dynamic_css( 'footer-html-'+ index +'-alignment', dynamicStyle );
					}
				} );
			} );
		})( index );
	}

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

    var selector = '#astra-footer-menu';
    var visibility_selector = '.footer-widget-area[data-section="section-footer-menu"]';

    var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
        mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

    wp.customize( 'astra-settings[footer-menu-alignment]', function( value ) {
        value.bind( function( alignment ) {
            if( alignment.desktop != '' || alignment.tablet != '' || alignment.mobile != '' ) {
                var dynamicStyle = '';
                dynamicStyle += '.footer-widget-area[data-section="section-footer-menu"] .astra-footer-vertical-menu .menu-item {';
                dynamicStyle += 'align-items: ' + alignment['desktop'] + ';';
                dynamicStyle += '} ';

                dynamicStyle += '.footer-widget-area[data-section="section-footer-menu"] .astra-footer-horizontal-menu {';
                dynamicStyle += 'justify-content: ' + alignment['desktop'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += '.footer-widget-area[data-section="section-footer-menu"] .astra-footer-tablet-vertical-menu {';
                dynamicStyle +=  'justify-content:' +  alignment['tablet'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '.footer-widget-area[data-section="section-footer-menu"] .astra-footer-tablet-vertical-menu .menu-item {';
                dynamicStyle +=  'display:' + 'grid;';
                dynamicStyle +=  'justify-content:' +  alignment['tablet'] + ';';
                dynamicStyle +=  'align-items: ' + alignment['tablet'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '.footer-widget-area[data-section="section-footer-menu"] .astra-footer-tablet-horizontal-menu {';
                dynamicStyle += 'justify-content: ' + alignment['tablet'] + ';';
                dynamicStyle += 'display: flex;';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += '.footer-widget-area[data-section="section-footer-menu"] .astra-footer-mobile-vertical-menu {';
                dynamicStyle +=  'display:' + 'grid;';
                dynamicStyle +=  'justify-content:' +  alignment['mobile'] + ';';
                dynamicStyle += '} ';

                dynamicStyle += '.footer-widget-area[data-section="section-footer-menu"] .astra-footer-mobile-vertical-menu .menu-item {';
                dynamicStyle +=  'justify-content:' +  alignment['mobile'] + ';';

                dynamicStyle += 'align-items: ' + alignment['mobile'] + ';';
                dynamicStyle += '} ';

                dynamicStyle += '.footer-widget-area[data-section="section-footer-menu"] .astra-footer-mobile-horizontal-menu {';
                dynamicStyle += 'justify-content: ' + alignment['mobile'] + ';';
                dynamicStyle += 'display: flex;';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                astra_add_dynamic_css( 'footer-menu-alignment', dynamicStyle );
            }
        } );
    } );

    /**
     * Typography CSS.
     */
    astra_responsive_font_size(
        'astra-settings[footer-menu-font-size]',
        selector + ' .menu-item > a'
    );

    /**
     * Menu - Colors
     */
    astra_color_responsive_css(
        'astra-footer-menu-preview',
        'astra-settings[footer-menu-color-responsive]',
        'color',
        selector + ' .menu-item > a'
    );

    // Menu - Hover Color
    astra_color_responsive_css(
        'astra-footer-menu-preview',
        'astra-settings[footer-menu-h-color-responsive]',
        'color',
        selector + ' .menu-item:hover > a'
    );

    // Menu - Active Color
    astra_color_responsive_css(
        'astra-footer-menu-preview',
        'astra-settings[footer-menu-a-color-responsive]',
        'color',
        selector + ' .menu-item.current-menu-item > a'
    );

    // Responsive BG styles > Footer Menu.
	astra_apply_responsive_background_css( 'astra-settings[footer-menu-bg-obj-responsive]', selector, 'desktop' );
	astra_apply_responsive_background_css( 'astra-settings[footer-menu-bg-obj-responsive]', selector, 'tablet' );
	astra_apply_responsive_background_css( 'astra-settings[footer-menu-bg-obj-responsive]', selector, 'mobile' );

    // Menu - Hover Background
    astra_color_responsive_css(
        'astra-footer-menu-preview',
        'astra-settings[footer-menu-h-bg-color-responsive]',
        'background',
        selector + ' .menu-item:hover > a'
    );

    // Menu - Active Background
    astra_color_responsive_css(
        'astra-footer-menu-preview',
        'astra-settings[footer-menu-a-bg-color-responsive]',
        'background',
        selector + ' .menu-item.current-menu-item > a'
    );

    /**
     * Spacing CSS.
     */
    wp.customize( 'astra-settings[footer-main-menu-spacing]', function( value ) {
        value.bind( function( padding ) {
            if(
                padding.desktop.bottom != '' || padding.desktop.top != '' || padding.desktop.left != '' || padding.desktop.right != '' ||
                padding.tablet.bottom != '' || padding.tablet.top != '' || padding.tablet.left != '' || padding.tablet.right != '' ||
                padding.mobile.bottom != '' || padding.mobile.top != '' || padding.mobile.left != '' || padding.mobile.right != ''
            ) {
                var dynamicStyle = '';
                dynamicStyle += selector + ' .menu-item > a {';
                dynamicStyle += 'padding-left: ' + padding['desktop']['left'] + padding['desktop-unit'] + ';';
                dynamicStyle += 'padding-right: ' + padding['desktop']['right'] + padding['desktop-unit'] + ';';
                dynamicStyle += 'padding-top: ' + padding['desktop']['top'] + padding['desktop-unit'] + ';';
                dynamicStyle += 'padding-bottom: ' + padding['desktop']['bottom'] + padding['desktop-unit'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += selector + ' .menu-item > a {';
                dynamicStyle += 'padding-left: ' + padding['tablet']['left'] + padding['tablet-unit'] + ';';
                dynamicStyle += 'padding-right: ' + padding['tablet']['right'] + padding['tablet-unit'] + ';';
                dynamicStyle += 'padding-top: ' + padding['tablet']['top'] + padding['tablet-unit'] + ';';
                dynamicStyle += 'padding-bottom: ' + padding['tablet']['bottom'] + padding['tablet-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += selector + ' .menu-item > a {';
                dynamicStyle += 'padding-left: ' + padding['mobile']['left'] + padding['mobile-unit'] + ';';
                dynamicStyle += 'padding-right: ' + padding['mobile']['right'] + padding['mobile-unit'] + ';';
                dynamicStyle += 'padding-top: ' + padding['mobile']['top'] + padding['mobile-unit'] + ';';
                dynamicStyle += 'padding-bottom: ' + padding['mobile']['bottom'] + padding['mobile-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                astra_add_dynamic_css( 'footer-menu-spacing', dynamicStyle );
            }
        } );
    } );

    // Margin.
    wp.customize( 'astra-settings[section-footer-menu-margin]', function( value ) {
        value.bind( function( margin ) {
            if(
                margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
                margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
                margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
            ) {
                var dynamicStyle = '';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';
                astra_add_dynamic_css( 'section-footer-menu-margin', dynamicStyle );
            }
        } );
    } );

    // Advanced Visibility CSS Generation.
    astra_builder_visibility_css( 'section-footer-menu', visibility_selector, 'block' );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	var section = 'section-primary-footer-builder';
	var selector = '.site-primary-footer-wrap[data-section="section-primary-footer-builder"]';

	// Primary Header - Layout.
	wp.customize( 'astra-settings[hb-footer-layout-width]', function( setting ) {
		setting.bind( function( layout ) {

			var dynamicStyle = '';

			if ( 'content' == layout ) {
				dynamicStyle = selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'max-width: ' + AstraBuilderPrimaryFooterData.footer_content_width + 'px;';
				dynamicStyle += 'margin-left: auto;';
				dynamicStyle += 'margin-right: auto;';
				dynamicStyle += '} ';
			}

			if ( 'full' == layout ) {
				dynamicStyle = selector + ' .ast-builder-grid-row {';
					dynamicStyle += 'max-width: 100%;';
					dynamicStyle += 'padding-right: 35px; padding-left: 35px;';
				dynamicStyle += '} ';
			}

			astra_add_dynamic_css( 'hb-footer-layout-width', dynamicStyle );

		} );
	} );

	// Footer Vertical Alignment.
    astra_css(
        'astra-settings[hb-footer-vertical-alignment]',
        'align-items',
        selector + ' .ast-builder-grid-row, ' + selector + ' .site-footer-section'
    );

	// Inner Space.
	wp.customize( 'astra-settings[hb-inner-spacing]', function( value ) {
		value.bind( function( spacing ) {
			var dynamicStyle = '';
			if ( spacing.desktop != '' ) {
				dynamicStyle += selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'grid-column-gap: ' + spacing.desktop + 'px;';
				dynamicStyle += '} ';
			}
			
			if ( spacing.tablet != '' ) {
				dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'grid-column-gap: ' + spacing.tablet + 'px;';
				dynamicStyle += 'grid-row-gap: ' + spacing.tablet + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';
			}

			if ( spacing.mobile != '' ) {
				dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += selector + ' .ast-builder-grid-row {';
				dynamicStyle += 'grid-column-gap: ' + spacing.mobile + 'px;';
				dynamicStyle += 'grid-row-gap: ' + spacing.mobile + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';
			}

			astra_add_dynamic_css( 'hb-inner-spacing-toggle-button', dynamicStyle );
		} );
	} );

	// Border Top width.
	astra_css(
		'astra-settings[hb-footer-main-sep]',
		'border-top-width',
		selector,
		'px'
	);

	// Border Color.
	astra_css(
		'astra-settings[hb-footer-main-sep-color]',
		'border-color',
		selector
	);

	var dynamicStyle = selector + ' {';
		dynamicStyle += 'border-top-style: solid';
	dynamicStyle += '} ';

	astra_add_dynamic_css( 'hb-footer-main-sep-color', dynamicStyle );

	// Responsive BG styles > Primary Footer Row.
	astra_apply_responsive_background_css( 'astra-settings[hb-footer-bg-obj-responsive]', selector, 'desktop' );
	astra_apply_responsive_background_css( 'astra-settings[hb-footer-bg-obj-responsive]', selector, 'tablet' );
	astra_apply_responsive_background_css( 'astra-settings[hb-footer-bg-obj-responsive]', selector, 'mobile' );

	// Responsive BG styles > Global Footer Row.
	astra_apply_responsive_background_css( 'astra-settings[footer-bg-obj-responsive]', '.site-footer', 'desktop' );
	astra_apply_responsive_background_css( 'astra-settings[footer-bg-obj-responsive]', '.site-footer', 'tablet' );
	astra_apply_responsive_background_css( 'astra-settings[footer-bg-obj-responsive]', '.site-footer', 'mobile' );

	// Advanced CSS Generation.
	astra_builder_advanced_css( section, selector );

	// Advanced CSS for Header Builder.
	astra_builder_advanced_css( 'section-footer-builder-layout', '.ast-hfb-header .site-footer' );

	// Advanced Visibility CSS Generation.
	astra_builder_visibility_css( section, selector, 'grid' );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

    astra_builder_social_css( 'footer', astraBuilderFooterSocial.component_limit );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra Builder
 * @since 3.0.0
 */

( function( $ ) {

    astra_builder_widget_css('footer');

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	wp.customize( 'astra-settings[hba-header-height]', function( value ) {
		value.bind( function( size ) {

			if( size.desktop != '' || size.tablet != '' || size.mobile != '' ) {
				var dynamicStyle = '';
				dynamicStyle += '.ast-above-header-bar .site-above-header-wrap, .ast-mobile-header-wrap .ast-above-header-bar{';
				dynamicStyle += 'min-height: ' + size.desktop + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '.ast-desktop .ast-above-header-bar .main-header-menu > .menu-item {';
				dynamicStyle += 'line-height: ' + size.desktop + 'px;';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += '.ast-above-header-bar .site-above-header-wrap, .ast-mobile-header-wrap .ast-above-header-bar{';
				dynamicStyle += 'min-height: ' + size.tablet + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += '.ast-above-header-bar .site-above-header-wrap, .ast-mobile-header-wrap .ast-above-header-bar{';
				dynamicStyle += 'min-height: ' + size.mobile + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				astra_add_dynamic_css( 'hba-header-height', dynamicStyle );
			}
		} );
	} );

	// Border Bottom width.
	wp.customize( 'astra-settings[hba-header-separator]', function( value ) {
		value.bind( function( border ) {

			var color = wp.customize( 'astra-settings[hba-header-bottom-border-color]' ).get(),
				dynamicStyle = '';

			dynamicStyle += '.ast-above-header.ast-above-header-bar, .ast-above-header-bar {';
			dynamicStyle += 'border-bottom-width: ' + border + 'px;';
			dynamicStyle += 'border-bottom-style: solid;';
			dynamicStyle += 'border-color:' + color + ';';
			dynamicStyle += '}';

			astra_add_dynamic_css( 'hba-header-separator', dynamicStyle );

		} );
	} );

	// Border Color.
	astra_css(
		'astra-settings[hba-header-bottom-border-color]',
		'border-color',
		'.ast-above-header.ast-above-header-bar, .ast-above-header-bar'
	);

	// Responsive BG styles > Below Header Row.
	astra_apply_responsive_background_css( 'astra-settings[hba-header-bg-obj-responsive]', '.ast-above-header.ast-above-header-bar', 'desktop' );
	astra_apply_responsive_background_css( 'astra-settings[hba-header-bg-obj-responsive]', '.ast-above-header.ast-above-header-bar', 'tablet' );
	astra_apply_responsive_background_css( 'astra-settings[hba-header-bg-obj-responsive]', '.ast-above-header.ast-above-header-bar', 'mobile' );
	
	if (document.querySelector(".ast-above-header-wrap .site-logo-img")) {
	astra_apply_responsive_background_css(
		"astra-settings[hba-header-bg-obj-responsive]",
		".ast-sg-element-wrap.ast-sg-logo-section, .ast-above-header.ast-above-header-bar",
		"desktop"
	);
	astra_apply_responsive_background_css(
		"astra-settings[hba-header-bg-obj-responsive]",
		".ast-sg-element-wrap.ast-sg-logo-section, .ast-above-header.ast-above-header-bar",
		"tablet"
	);
	astra_apply_responsive_background_css(
		"astra-settings[hba-header-bg-obj-responsive]",
		".ast-sg-element-wrap.ast-sg-logo-section, .ast-above-header.ast-above-header-bar",
		"mobile"
	);
}

	// Advanced CSS Generation.
	astra_builder_advanced_css( 'section-above-header-builder', '.ast-above-header.ast-above-header-bar, .ast-header-break-point #masthead.site-header .ast-above-header-bar' );

    // Advanced Visibility CSS Generation.
	astra_builder_visibility_css( 'section-above-header-builder', '.ast-above-header-bar', 'grid' );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since x.x.x
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	var selector = '.ast-header-account-wrap';
    var section = 'section-header-account';
	var visibility_selector = '.ast-header-account[data-section="section-header-account"]';

	wp.customize( 'astra-settings[header-account-icon-color]', function( value ) {
		value.bind( function( color ) {
			if( ! color ) {
				color = 'inherit';
			}
			var dynamicStyle =  selector + ' .ast-header-account-type-icon .ahfb-svg-iconset svg path:not( .ast-hf-account-unfill ), ' + selector + ' .ast-header-account-type-icon .ahfb-svg-iconset svg circle, .ast-mobile-popup-content' + selector + ' .ast-header-account-type-icon .ahfb-svg-iconset svg path:not( .ast-hf-account-unfill ), .ast-mobile-popup-content ' + selector + ' .ast-header-account-type-icon .ahfb-svg-iconset svg circle {';
			dynamicStyle += 'fill: ' + color + ';';
			dynamicStyle += '} ';
			astra_add_dynamic_css( 'header-account-icon-color', dynamicStyle );
		} );
	} );

	// Typography CSS Generation.
    astra_responsive_font_size(
        'astra-settings[font-size-section-header-account]',
        selector + ' .ast-header-account-text'
    );

	// Text size.
	astra_css(
		'astra-settings[header-account-type-text-color]',
		'color',
		selector + ' .ast-header-account-text, .ast-mobile-popup-content ' + selector + ' .ast-header-account-text'
	);

	// Icon Size.
	wp.customize( 'astra-settings[header-account-icon-size]', function( value ) {
		value.bind( function( size ) {
			if( size.desktop != '' || size.tablet != '' || size.mobile != '' ) {
				var dynamicStyle = '';
				dynamicStyle += selector + ' .ast-header-account-type-icon .ahfb-svg-iconset svg {';
				dynamicStyle += 'height: ' + size.desktop + 'px' + ';';
				dynamicStyle += 'width: ' + size.desktop + 'px' + ';';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += selector + ' .ast-header-account-type-icon .ahfb-svg-iconset svg {';
				dynamicStyle += 'height: ' + size.tablet + 'px' + ';';
				dynamicStyle += 'width: ' + size.tablet + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += selector + ' .ast-header-account-type-icon .ahfb-svg-iconset svg {';
				dynamicStyle += 'height: ' + size.mobile + 'px' + ';';
				dynamicStyle += 'width: ' + size.mobile + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '} ';
				astra_add_dynamic_css( 'header-account-icon-size', dynamicStyle );
			}
		} );
	} );

	// Image Width.
	wp.customize( 'astra-settings[header-account-image-width]', function( value ) {
		value.bind( function( size ) {
			if( size.desktop != '' || size.tablet != '' || size.mobile != '' ) {
				var dynamicStyle = '';
				dynamicStyle += selector + ' .ast-header-account-type-avatar .avatar {';
				dynamicStyle += 'width: ' + size.desktop + 'px' + ';';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += selector + ' .ast-header-account-type-avatar .avatar {';
				dynamicStyle += 'width: ' + size.tablet + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += selector + ' .ast-header-account-type-avatar .avatar {';
				dynamicStyle += 'width: ' + size.mobile + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '} ';
				astra_add_dynamic_css( 'header-account-image-width', dynamicStyle );
			}
		} );
	} );

	// Margin.
    wp.customize( 'astra-settings[header-account-margin]', function( value ) {
        value.bind( function( margin ) {
            if(
                margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
                margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
                margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
            ) {
				var selector = '.ast-header-account-wrap';
                var dynamicStyle = '';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';
                astra_add_dynamic_css( 'header-account-margin', dynamicStyle );
            }
        } );
	} );

	// Advanced Visibility CSS Generation.
	astra_builder_visibility_css( section, visibility_selector );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	wp.customize( 'astra-settings[hbb-header-height]', function( value ) {
		value.bind( function( size ) {

			if( size.desktop != '' || size.tablet != '' || size.mobile != '' ) {
				var dynamicStyle = '';
				dynamicStyle += '.ast-below-header-bar .site-below-header-wrap, .ast-mobile-header-wrap .ast-below-header-bar {';
				dynamicStyle += 'min-height: ' + size.desktop + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '.ast-desktop .ast-below-header-bar .main-header-menu > .menu-item {';
				dynamicStyle += 'line-height: ' + size.desktop + 'px;';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += '.ast-below-header-bar .site-below-header-wrap, .ast-mobile-header-wrap .ast-below-header-bar  {';
				dynamicStyle += 'min-height: ' + size.tablet + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += '.ast-below-header-bar .site-below-header-wrap, .ast-mobile-header-wrap .ast-below-header-bar  {';
				dynamicStyle += 'min-height: ' + size.mobile + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				astra_add_dynamic_css( 'hbb-header-height', dynamicStyle );
			}
		} );
	} );

	// Border Bottom width.
	wp.customize( 'astra-settings[hbb-header-separator]', function( value ) {
		value.bind( function( border ) {

			var color = wp.customize( 'astra-settings[hbb-header-bottom-border-color]' ).get(),
				dynamicStyle = '';

			dynamicStyle += '.ast-header-break-point .ast-below-header-bar, .ast-below-header-bar {';
			dynamicStyle += 'border-bottom-width: ' + border + 'px;';
			dynamicStyle += 'border-bottom-style: solid;';
			dynamicStyle += 'border-color:' + color + ';';
			dynamicStyle += '}';

			astra_add_dynamic_css( 'hbb-header-separator', dynamicStyle );

		} );
	} );

	// Border Color.
	astra_css(
		'astra-settings[hbb-header-bottom-border-color]',
		'border-color',
		'.ast-header-break-point .ast-below-header-bar, .ast-below-header-bar'
	);

	// Responsive BG styles > Below Header Row.
	astra_apply_responsive_background_css( 'astra-settings[hbb-header-bg-obj-responsive]', '.ast-below-header.ast-below-header-bar', 'desktop' );
	astra_apply_responsive_background_css( 'astra-settings[hbb-header-bg-obj-responsive]', '.ast-below-header.ast-below-header-bar', 'tablet' );
	astra_apply_responsive_background_css( 'astra-settings[hbb-header-bg-obj-responsive]', '.ast-below-header.ast-below-header-bar', 'mobile' );

	if (document.querySelector(".ast-below-header-bar .site-logo-img")) {
		astra_apply_responsive_background_css(
			"astra-settings[hbb-header-bg-obj-responsive]",
			".ast-sg-element-wrap.ast-sg-logo-section, .ast-below-header.ast-below-header-bar",
			"desktop"
		);
		astra_apply_responsive_background_css(
			"astra-settings[hbb-header-bg-obj-responsive]",
			".ast-sg-element-wrap.ast-sg-logo-section, .ast-below-header.ast-below-header-bar",
			"tablet"
		);
		astra_apply_responsive_background_css(
			"astra-settings[hbb-header-bg-obj-responsive]",
			".ast-sg-element-wrap.ast-sg-logo-section, .ast-below-header.ast-below-header-bar",
			"mobile"
		);
	}

	// Advanced CSS Generation.
	astra_builder_advanced_css( 'section-below-header-builder', '.ast-below-header.ast-below-header-bar' );

    // Advanced Visibility CSS Generation.
	astra_builder_visibility_css( 'section-below-header-builder', '.ast-below-header-bar', 'grid' );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	astra_builder_button_css( 'header', AstraBuilderButtonData.component_limit );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since x.x.x
 */

( function( $ ) {

	var selector = '.ast-edd-site-header-cart';
	var responsive_selector = '.astra-cart-drawer.edd-active';

	// Icon Color.
	astra_css(
		'astra-settings[edd-header-cart-icon-color]',
		'color',
		selector + ' .ast-edd-cart-menu-wrap .count, ' + selector + ' .ast-edd-cart-menu-wrap .count:after,' + selector + ' .ast-edd-header-cart-info-wrap'
	);

	// Icon Color.
	astra_css(
		'astra-settings[edd-header-cart-icon-color]',
		'border-color',
		selector + ' .ast-edd-cart-menu-wrap .count, ' + selector + ' .ast-edd-cart-menu-wrap .count:after'
	);

	// EDD Cart Colors.
	astra_color_responsive_css(
		'edd-cart-colors',
		'astra-settings[header-edd-cart-text-color]',
		'color',
		selector + ' .widget_edd_cart_widget span, ' + selector + ' .widget_edd_cart_widget strong, ' + selector + ' .widget_edd_cart_widget *, ' + responsive_selector + ' .widget_edd_cart_widget span, .astra-cart-drawer .widget_edd_cart_widget *, ' + responsive_selector + ' .astra-cart-drawer-title'
	);

	astra_color_responsive_css(
		'edd-cart-colors',
		'astra-settings[header-edd-cart-link-color]',
		'color',
		selector + ' .widget_edd_cart_widget a, ' + selector + ' .widget_edd_cart_widget a.edd-remove-from-cart, ' + selector + ' .widget_edd_cart_widget .cart-total, ' + selector + ' .widget_edd_cart_widget a.edd-remove-from-cart:after, '+ responsive_selector + ' .widget_edd_cart_widget a, ' + responsive_selector + ' .widget_edd_cart_widget a.edd-remove-from-cart, ' + responsive_selector + ' .widget_edd_cart_widget .cart-total, ' + responsive_selector + ' .widget_edd_cart_widget a.edd-remove-from-cart:after'
	);
	astra_color_responsive_css(
		'edd-cart-colors',
		'astra-settings[header-edd-cart-link-color]',
		'border-color',
		selector + ' .widget_edd_cart_widget a.edd-remove-from-cart:after,' + responsive_selector + ' .widget_edd_cart_widget a.edd-remove-from-cart:after,'
	);

	astra_color_responsive_css(
		'edd-cart-colors',
		'astra-settings[header-edd-cart-background-color]',
		'background-color',
		'.ast-builder-layout-element ' + selector + ' .widget_edd_cart_widget ,' + responsive_selector + ', ' + responsive_selector + '#astra-mobile-cart-drawer'
	);

	astra_color_responsive_css(
		'edd-cart-border-color',
		'astra-settings[header-edd-cart-background-color]',
		'border-color',
		'.ast-builder-layout-element ' + selector + ' .widget_edd_cart_widget, ' + responsive_selector + ' .widget_edd_cart_widget'
	);

	astra_color_responsive_css(
		'edd-cart-border-bottom-color',
		'astra-settings[header-edd-cart-background-color]',
		'border-bottom-color',
		'.ast-builder-layout-element ' + selector + ' .widget_edd_cart_widget:before, .ast-builder-layout-element ' + selector + ' .widget_edd_cart_widget:after, ' + responsive_selector + ' .widget_edd_cart_widget:before, ' + responsive_selector + ' .widget_edd_cart_widget:after'
	);

	astra_color_responsive_css(
		'edd-cart-colors',
		'astra-settings[header-edd-cart-separator-color]',
		'border-bottom-color',
		'.ast-builder-layout-element ' + selector + ' .widget_edd_cart_widget .edd-cart-item, .ast-builder-layout-element ' + selector + ' .widget_edd_cart_widget .edd-cart-number-of-items, .ast-builder-layout-element ' + selector + ' .widget_edd_cart_widget .edd-cart-meta,'+ responsive_selector + ' .widget_edd_cart_widget .edd-cart-item, ' + responsive_selector + ' .widget_edd_cart_widget .edd-cart-number-of-items, ' + responsive_selector + ' .widget_edd_cart_widget .edd-cart-meta, ' + responsive_selector + ' .astra-cart-drawer-header'
	);

	astra_color_responsive_css(
		'edd-cart-colors',
		'astra-settings[header-edd-checkout-btn-text-color]',
		'color',
		selector + ' .widget_edd_cart_widget .edd_checkout a, .widget_edd_cart_widget .edd_checkout a, '+ responsive_selector + ' .widget_edd_cart_widget .edd_checkout a'
	);

	astra_color_responsive_css(
		'edd-cart-colors',
		'astra-settings[header-edd-checkout-btn-background-color]',
		'background-color',
		selector + ' .widget_edd_cart_widget .edd_checkout a, .widget_edd_cart_widget .edd_checkout a,' + responsive_selector + ' .widget_edd_cart_widget .edd_checkout a, .widget_edd_cart_widget .edd_checkout a'
	);

	astra_color_responsive_css(
		'edd-cart-border-color',
		'astra-settings[header-edd-checkout-btn-background-color]',
		'border-color',
		selector + ' .widget_edd_cart_widget .edd_checkout a, .widget_edd_cart_widget .edd_checkout a,' + responsive_selector + ' .widget_edd_cart_widget .edd_checkout a, .widget_edd_cart_widget .edd_checkout a'
	);

	astra_color_responsive_css(
		'edd-cart-colors',
		'astra-settings[header-edd-checkout-btn-text-hover-color]',
		'color',
		selector +' .widget_edd_cart_widget .edd_checkout a:hover, .widget_edd_cart_widget .edd_checkout a:hover,' + responsive_selector +' .widget_edd_cart_widget .edd_checkout a:hover, .widget_edd_cart_widget .edd_checkout a:hover'
	);

	astra_color_responsive_css(
		'edd-cart-colors',
		'astra-settings[header-edd-checkout-btn-bg-hover-color]',
		'background-color',
		selector +' .widget_edd_cart_widget .edd_checkout a:hover, .widget_edd_cart_widget .edd_checkout a:hover, ' + responsive_selector + ' .widget_edd_cart_widget .edd_checkout a:hover, .widget_edd_cart_widget .edd_checkout a:hover'
	);

	/**
	 * Cart icon style
	 */
	wp.customize( 'astra-settings[edd-header-cart-icon-style]', function( setting ) {
		setting.bind( function( icon_style ) {
				var buttons = $(document).find('.ast-edd-site-header-cart');
				buttons.removeClass('ast-edd-menu-cart-fill ast-edd-menu-cart-outline');
				buttons.addClass( 'ast-edd-menu-cart-' + icon_style );
				var dynamicStyle = '.ast-edd-site-header-cart a, .ast-edd-site-header-cart a *{ transition: all 0s; } ';
				astra_add_dynamic_css( 'edd-header-cart-icon-style', dynamicStyle );
				wp.customize.preview.send( 'refresh' );
		} );
	} );

	/**
	 * EDD Cart Button Color
	 */
	wp.customize( 'astra-settings[edd-header-cart-icon-color]', function( setting ) {
		setting.bind( function( cart_icon_color ) {
			wp.customize.preview.send( 'refresh' );
		});
	});

	/**
	 * Button Border Radius
	 */
	wp.customize( 'astra-settings[edd-header-cart-icon-radius]', function( setting ) {
		setting.bind( function( border ) {

			var dynamicStyle = '.ast-edd-site-header-cart.ast-edd-menu-cart-outline .ast-addon-cart-wrap, .ast-edd-site-header-cart.ast-edd-menu-cart-fill .ast-addon-cart-wrap, .ast-edd-site-header-cart.ast-edd-menu-cart-outline .count, .ast-edd-site-header-cart.ast-edd-menu-cart-fill .count{ border-radius: ' + ( parseInt( border ) ) + 'px } ';
			astra_add_dynamic_css( 'edd-header-cart-icon-radius', dynamicStyle );

		} );
	} );

	/**
	 * Transparent Header EDD-Cart color options - Customizer preview CSS.
	 */
	wp.customize( 'astra-settings[transparent-header-edd-cart-icon-color]', function( setting ) {
		setting.bind( function( cart_icon_color ) {
			wp.customize.preview.send( 'refresh' );
		});
	});

	// Advanced Visibility CSS Generation.
	astra_builder_visibility_css( 'section-header-edd-cart', '.ast-header-edd-cart' );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra Builder
 * @since 3.0.0
 */

( function( $ ) {

    astra_builder_html_css( 'header', AstraBuilderHTMLData.component_limit );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544,
		isNavMenuEnabled      = AstraBuilderMenuData.nav_menu_enabled || false;

	for ( var index = 1; index <= AstraBuilderMenuData.component_limit; index++ ) {

		var prefix = 'menu' + index;
		var selector = '.ast-builder-menu-' + index;
		var section = 'section-hb-menu-' + index;

		// Advanced Visibility CSS Generation.
		astra_builder_visibility_css( section, selector );

		/**
		 * Typography CSS.
		 */

			// Menu Typography.
			astra_generate_outside_font_family_css(
				'astra-settings[header-' + prefix + '-font-family]',
				selector + ' .menu-item > .menu-link'
			);
			astra_generate_font_weight_css(
				'astra-settings[header-' + prefix + '-font-family]',
				'astra-settings[header-' + prefix + '-font-weight]',
				'font-weight',
				selector + ' .menu-item > .menu-link'
			);
			astra_css(
				'astra-settings[header-' + prefix + '-text-transform]',
				'text-transform',
				selector + ' .menu-item > .menu-link'
			);
			astra_responsive_font_size(
				'astra-settings[header-' + prefix + '-font-size]',
				selector + ' .menu-item > .menu-link'
			);
			astra_css(
				'astra-settings[header-' + prefix + '-line-height]',
				'line-height',
				selector + ' .menu-item > .menu-link'
			);
			astra_css(
				'astra-settings[header-' + prefix + '-letter-spacing]',
				'letter-spacing',
				selector + ' .menu-item > .menu-link',
				'px'
			);

		/**
		 * Color CSS.
		 */

			/**
			 * Menu - Colors
			 */

			// Menu - Normal Color
			astra_color_responsive_css(
				'astra-menu-color-preview',
				'astra-settings[header-' + prefix + '-color-responsive]',
				'color',
				selector + ' .main-header-menu .menu-item > .menu-link'
			);

			// Menu - Hover Color
			astra_color_responsive_css(
				'astra-menu-h-color-preview',
				'astra-settings[header-' + prefix + '-h-color-responsive]',
				'color',
				selector + ' .menu-link:hover, ' + selector + ' .main-header-menu > .menu-item:hover > .menu-link, ' + selector + ' .inline-on-mobile .ast-menu-toggle:hover, ' + selector + ' .inline-on-mobile .main-header-menu > .menu-item:hover > .ast-menu-toggle'
			);

			// Menu Toggle -  Color
			astra_color_responsive_css(
				'astra-builder-toggle',
				'astra-settings[header-' + prefix + '-color-responsive]',
				'color',
				selector + ' .menu-item > .ast-menu-toggle'
			);

			// Menu Toggle - Hover Color
			astra_color_responsive_css(
				'astra-menu-h-toogle-color-preview',
				'astra-settings[header-' + prefix + '-h-color-responsive]',
				'color',
				selector + ' .ast-menu-toggle:hover, ' + selector + ' .main-header-menu > .menu-item:hover > .ast-menu-toggle'
			);
			// Menu - Active Color
			astra_color_responsive_css(
				'astra-menu-active-color-preview',
				'astra-settings[header-' + prefix + '-a-color-responsive]',
				'color',
				selector + ' .menu-item.current-menu-item > .menu-link, ' + selector + ' .inline-on-mobile .menu-item.current-menu-item > .ast-menu-toggle, ' + selector + ' .current-menu-ancestor > .menu-link, ' + selector + '  .current-menu-ancestor > .ast-menu-toggle'
			);

			// Menu - Normal Background
			astra_apply_responsive_background_css( 'astra-settings[header-' + prefix + '-bg-obj-responsive]', selector + ' .main-header-menu, ' + selector + ' .main-header-menu .sub-menu', 'desktop' );
			astra_apply_responsive_background_css( 'astra-settings[header-' + prefix + '-bg-obj-responsive]', selector + ' .main-header-menu, ' + selector + ' .main-header-menu .sub-menu', 'tablet' );
			astra_apply_responsive_background_css( 'astra-settings[header-' + prefix + '-bg-obj-responsive]', selector + ' .main-header-menu, ' + selector + ' .main-header-menu .sub-menu', 'mobile' );

			// Menu - Hover Background
			astra_color_responsive_css(
				'astra-menu-bg-preview',
				'astra-settings[header-' + prefix + '-h-bg-color-responsive]',
				'background',
				selector + ' .menu-link:hover, ' + selector + ' .main-header-menu > .menu-item:hover > .menu-link, ' + selector + ' .inline-on-mobile .ast-menu-toggle:hover, ' + selector + ' .inline-on-mobile .main-header-menu > .menu-item:hover > .ast-menu-toggle'
			);

			// Menu - Active Background
			astra_color_responsive_css(
				'astra-builder',
				'astra-settings[header-' + prefix + '-a-bg-color-responsive]',
				'background',
				selector + ' .menu-item.current-menu-item > .menu-link, ' + selector + ' .inline-on-mobile .menu-item.current-menu-item > .ast-menu-toggle, ' + selector + ' .current-menu-ancestor > .menu-link, ' + selector + '  .current-menu-ancestor > .ast-menu-toggle'
			);

		/**
		 * Border CSS.
		 */

			(function (index) {

				// Menu 1 > Sub Menu Border Size.
				wp.customize( 'astra-settings[header-menu'+ index +'-submenu-border]', function( setting ) {
					setting.bind( function( border ) {

						var dynamicStyle = '.ast-builder-menu-'+ index +' .sub-menu, .ast-builder-menu-'+ index +' .inline-on-mobile .sub-menu, .ast-builder-menu-'+ index +' .main-header-menu.submenu-with-border .astra-megamenu, .ast-builder-menu-'+ index +' .main-header-menu.submenu-with-border .astra-full-megamenu-wrapper {';
						dynamicStyle += 'border-top-width:'  + border.top + 'px;';
						dynamicStyle += 'border-right-width:'  + border.right + 'px;';
						dynamicStyle += 'border-left-width:'   + border.left + 'px;';
						dynamicStyle += 'border-style: solid;';
						dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';

						dynamicStyle += '}';

						// Fix submenu top offset when above border is assigned.
						dynamicStyle += '.ast-builder-menu-'+ index + ' .sub-menu .sub-menu {';
						dynamicStyle += 'top:' + Number( -1 * border.top ) + 'px;';
						dynamicStyle += '}';

						const submenuTopOffset = wp.customize( 'astra-settings[header-menu'+ index +'-submenu-top-offset]' ).get();

						dynamicStyle += '.ast-desktop .ast-builder-menu-' + index + ' .main-header-menu > .menu-item > .sub-menu:before, .ast-desktop .ast-builder-menu-' + index + ' .main-header-menu > .menu-item > .astra-full-megamenu-wrapper:before {';
						dynamicStyle += 'height: calc( ' + ( border.top || 0 ) + 'px + ' + ( submenuTopOffset || 0 ) + 'px + 5px );';
						dynamicStyle += '}';

						astra_add_dynamic_css( 'header-menu'+ index +'-submenu-border', dynamicStyle );

					} );
				} );

				// Menu Spacing - Menu 1.
				wp.customize( 'astra-settings[header-menu'+ index +'-menu-spacing]', function( value ) {
					value.bind( function( padding ) {
						var dynamicStyle = '';
						dynamicStyle += '.ast-builder-menu-'+ index +' .main-header-menu .menu-item > .menu-link {';
						dynamicStyle += 'padding-left: ' + padding['desktop']['left'] + padding['desktop-unit'] + ';';
						dynamicStyle += 'padding-right: ' + padding['desktop']['right'] + padding['desktop-unit'] + ';';
						dynamicStyle += 'padding-top: ' + padding['desktop']['top'] + padding['desktop-unit'] + ';';
						dynamicStyle += 'padding-bottom: ' + padding['desktop']['bottom'] + padding['desktop-unit'] + ';';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += '.ast-header-break-point .ast-builder-menu-'+ index +' .main-header-menu .menu-item > .menu-link {';
						dynamicStyle += 'padding-left: ' + padding['tablet']['left'] + padding['tablet-unit'] + ';';
						dynamicStyle += 'padding-right: ' + padding['tablet']['right'] + padding['tablet-unit'] + ';';
						dynamicStyle += 'padding-top: ' + padding['tablet']['top'] + padding['tablet-unit'] + ';';
						dynamicStyle += 'padding-bottom: ' + padding['tablet']['bottom'] + padding['tablet-unit'] + ';';
						dynamicStyle += '} ';
						// Toggle top.
						dynamicStyle += '.ast-hfb-header .ast-builder-menu-'+ index +' .main-navigation ul .menu-item.menu-item-has-children > .ast-menu-toggle {';
						dynamicStyle += 'top: ' + padding['tablet']['top'] + padding['tablet-unit'] + ';';
						dynamicStyle += 'right: calc( ' + padding['tablet']['right'] + padding['tablet-unit'] + ' - 0.907em );'
						dynamicStyle += '} ';

						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += '.ast-header-break-point .ast-builder-menu-'+ index +' .main-header-menu .menu-item > .menu-link {';
						dynamicStyle += 'padding-left: ' + padding['mobile']['left'] + padding['mobile-unit'] + ';';
						dynamicStyle += 'padding-right: ' + padding['mobile']['right'] + padding['mobile-unit'] + ';';
						dynamicStyle += 'padding-top: ' + padding['mobile']['top'] + padding['mobile-unit'] + ';';
						dynamicStyle += 'padding-bottom: ' + padding['mobile']['bottom'] + padding['mobile-unit'] + ';';
						dynamicStyle += '} ';
						// Toggle top.
						dynamicStyle += '.ast-hfb-header .ast-builder-menu-'+ index +' .main-navigation ul .menu-item.menu-item-has-children > .ast-menu-toggle {';
						dynamicStyle += 'top: ' + padding['mobile']['top'] + padding['mobile-unit'] + ';';
						dynamicStyle += 'right: calc( ' + padding['mobile']['right'] + padding['mobile-unit'] + ' - 0.907em );'
						dynamicStyle += '} ';

						dynamicStyle += '} ';

						astra_add_dynamic_css( 'header-menu'+ index +'-menu-spacing-toggle-button', dynamicStyle );
					} );
				} );

				// Margin - Menu 1.
				wp.customize( 'astra-settings[section-hb-menu-'+ index +'-margin]', function( value ) {
					value.bind( function( margin ) {
						var selector = '.ast-builder-menu-'+ index +' .main-header-menu, .ast-header-break-point .ast-builder-menu-'+ index +' .main-header-menu';
						var dynamicStyle = '';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';
						astra_add_dynamic_css( 'section-hb-menu-'+ index +'-margin', dynamicStyle );
					} );
				} );

				/**
				 * Header Menu 1 > Submenu border Color
				 */
				wp.customize('astra-settings[header-menu'+ index +'-submenu-item-b-color]', function (value) {
					value.bind(function (color) {
						var insideBorder = wp.customize('astra-settings[header-menu'+ index +'-submenu-item-border]').get(),
							borderSize = wp.customize('astra-settings[header-menu'+ index +'-submenu-item-b-size]').get();
						if ('' != color) {
							if ( true == insideBorder ) {

								var dynamicStyle = '';

								dynamicStyle += '.ast-desktop .ast-builder-menu-'+ index +' .main-header-menu .menu-item .sub-menu .menu-link, .ast-header-break-point .main-navigation ul .menu-item .menu-link';
								dynamicStyle += '{';
								dynamicStyle += 'border-bottom-width:' + ( ( borderSize ) ? borderSize + 'px;' : '0px;' );
								dynamicStyle += 'border-color:' + color + ';';
								dynamicStyle += 'border-style: solid;';
								dynamicStyle += '}';
								dynamicStyle += '.ast-desktop .ast-builder-menu-'+ index +' .main-header-menu .menu-item .sub-menu .menu-item:last-child .menu-link{ border-style: none; }';

								astra_add_dynamic_css('header-menu'+ index +'-submenu-item-b-color', dynamicStyle);
							}
						} else {
							wp.customize.preview.send('refresh');
						}
					});
				});

				// Sub Menu - Divider Size.
				wp.customize( 'astra-settings[header-menu'+ index +'-submenu-item-b-size]', function( value ) {
					value.bind( function( borderSize ) {
						var selector = '.ast-desktop .ast-builder-menu-'+ index + ' .main-header-menu';
						var dynamicStyle = '';
						dynamicStyle += selector + ' .menu-item .sub-menu:last-child > .menu-item > .menu-link, .ast-header-break-point .main-navigation ul .menu-item .menu-link {';
						dynamicStyle += 'border-bottom-width: ' + borderSize + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .menu-item .sub-menu .menu-item:last-child .menu-link {';
						dynamicStyle += 'border-bottom-width: 0px';
						dynamicStyle += '} ';
						astra_add_dynamic_css( 'header-menu'+ index +'-submenu-item-b-size', dynamicStyle );
					} );
				} );

				/**
				 * Header Menu 1 > Submenu border Color
				 */
				wp.customize( 'astra-settings[header-menu'+ index +'-submenu-item-border]', function( value ) {
					value.bind( function( border ) {
						var color = wp.customize( 'astra-settings[header-menu'+ index +'-submenu-item-b-color]' ).get(),
							borderSize = wp.customize('astra-settings[header-menu'+ index +'-submenu-item-b-size]').get();

						var dynamicStyle = '.ast-desktop .ast-builder-menu-'+ index +' .main-header-menu.submenu-with-border .sub-menu .menu-link';

						if( true === border  ) {
							dynamicStyle += '{';
							dynamicStyle += 'border-bottom-width:' + ( ( borderSize ) ? borderSize + 'px;' : '0px;' );
							dynamicStyle += 'border-color:' + color + ';';
							dynamicStyle += 'border-style: solid;';
							dynamicStyle += '}';
						} else {
							dynamicStyle += '{';
							dynamicStyle += 'border-style: none;';
							dynamicStyle += '}';
						}

						dynamicStyle += '.ast-desktop .ast-builder-menu-'+ index +' .menu-item .sub-menu .menu-item:last-child .menu-link{ border-style: none; }';
						astra_add_dynamic_css( 'header-menu'+ index +'-submenu-item-border', dynamicStyle );

					} );
				} );

				// Animation preview support.
				// Adding customizer-refresh because if megamenu is enabled on menu then caluculated left & width JS value of megamenu wrapper wiped out due to partial refresh.
				wp.customize( 'astra-settings[header-menu'+ index +'-menu-hover-animation]', function( setting ) {
					setting.bind( function( animation ) {

						var menuWrapper = jQuery( '#ast-desktop-header #ast-hf-menu-'+ index  );
						menuWrapper.removeClass('ast-menu-hover-style-underline');
						menuWrapper.removeClass('ast-menu-hover-style-zoom');
						menuWrapper.removeClass('ast-menu-hover-style-overline');

						if ( isNavMenuEnabled ) {
							document.dispatchEvent( new CustomEvent( "astMenuHoverStyleChanged",  { "detail": {} }) );
						}

						menuWrapper.addClass( 'ast-menu-hover-style-' + animation );
					} );
				} );

				wp.customize( 'astra-settings[header-menu'+ index +'-submenu-container-animation]', function( setting ) {
					setting.bind( function( animation ) {

						var menuWrapper = jQuery( '#ast-desktop-header #ast-hf-menu-'+ index  );
						menuWrapper.removeClass('astra-menu-animation-fade');
						menuWrapper.removeClass('astra-menu-animation-slide-down');
						menuWrapper.removeClass('astra-menu-animation-slide-up');

						if ( '' != animation ) {
							menuWrapper.addClass( 'astra-menu-animation-' + animation );
						}
					} );
				} );

				// Stack on Mobile CSS
				wp.customize( 'astra-settings[header-menu'+ index +'-menu-stack-on-mobile]', function( setting ) {
					setting.bind( function( stack ) {

						var menu_div = jQuery( '#ast-mobile-header #ast-hf-menu-'+ index + '.main-header-menu'  );
						menu_div.removeClass('inline-on-mobile');
						menu_div.removeClass('stack-on-mobile');

						if ( false === stack ) {
							menu_div.addClass('inline-on-mobile');
						} else {
							menu_div.addClass('stack-on-mobile');
						}
					} );
				} );

				
				wp.customize( 'astra-settings[header-menu'+ index +'-submenu-border-radius-fields]', function( value ) {
					value.bind( function( border ) {

						const tabletBreakPoint = astraBuilderPreview.tablet_break_point || 768;
						const mobileBreakPoint = astraBuilderPreview.mobile_break_point || 544;
						const submenuBorderWidth = wp.customize.get()?.[ 'astra-settings[header-menu' + index + '-submenu-border]' ];

						const deviceSpecific = ( device ) => {
							return '.ast-builder-menu-' + index + ' li.menu-item .sub-menu, .ast-builder-menu-' + index + ' ul.inline-on-mobile li.menu-item .sub-menu {\
									border-top-left-radius: ' + border[ device ]['top'] + border[ device + '-unit' ] + ';\
									border-bottom-right-radius: ' + border[ device ]['bottom'] + border[ device + '-unit' ] + ';\
									border-bottom-left-radius: ' + border[ device ]['left'] + border[ device + '-unit' ] + ';\
									border-top-right-radius:' + border[ device ]['right'] + border[ device + '-unit' ] + ';\
								}\
								.ast-builder-menu-' + index + ' li.menu-item .sub-menu .menu-item:first-of-type > .menu-link, .ast-builder-menu-' + index + ' ul.inline-on-mobile li.menu-item .sub-menu .menu-item:first-of-type > .menu-link {\
									border-top-left-radius: calc(' + border[ device ]['top'] + border[ device + '-unit' ] + ' - ' + submenuBorderWidth?.top + 'px);\
									border-top-right-radius: calc(' + border[ device ]['right'] + border[ device + '-unit' ] + ' - ' + submenuBorderWidth?.top + 'px);\
								}\
								.ast-builder-menu-' + index + ' li.menu-item .sub-menu .menu-item:last-of-type > .menu-link, .ast-builder-menu-' + index + ' ul.inline-on-mobile li.menu-item .sub-menu .menu-item:last-of-type > .menu-link {\
									border-bottom-left-radius: calc(' + border[ device ]['left'] + border[ device + '-unit' ] + ' - ' + submenuBorderWidth?.top + 'px);\
									border-bottom-right-radius: calc(' + border[ device ]['bottom'] + border[ device + '-unit' ] + ' - ' + submenuBorderWidth?.top + 'px);\
								}';
						};

						dynamicStyle = deviceSpecific( 'desktop' ) +
							'@media ( max-width: ' + tabletBreakPoint + 'px ) { ' + deviceSpecific( 'tablet' ) + ' }\n' +
							'@media ( max-width: ' + mobileBreakPoint + 'px ) { ' + deviceSpecific( 'mobile' ) + ' }\n';

						astra_add_dynamic_css( 'header-menu'+ index +'-submenu-border-radius-fields', dynamicStyle );

					} );
				} );

				// Sub Menu - Top Offset.
				wp.customize( 'astra-settings[header-menu'+ index +'-submenu-top-offset]', function( value ) {
					value.bind( function( offset ) {

						var dynamicStyle = '.ast-desktop .ast-builder-menu-' + index + ' .main-header-menu > li.menu-item > .sub-menu, .ast-desktop .ast-builder-menu-' + index + ' ul.inline-on-mobile > li.menu-item > .sub-menu, .ast-desktop .ast-builder-menu-' + index + ' li.menu-item .astra-full-megamenu-wrapper {';
						dynamicStyle += 'margin-top: ' + offset + 'px';
						dynamicStyle += '}';

						const submenuBorder = wp.customize( 'astra-settings[header-menu'+ index +'-submenu-border]' ).get();

						dynamicStyle += '.ast-desktop .ast-builder-menu-' + index + ' .main-header-menu > .menu-item > .sub-menu:before, .ast-desktop .ast-builder-menu-' + index + ' .main-header-menu > .menu-item > .astra-full-megamenu-wrapper:before {';
						dynamicStyle += 'height: calc( ' + ( submenuBorder.top || 0 ) + 'px + ' + ( offset || 0 ) + 'px + 5px );';
						dynamicStyle += '}';

						astra_add_dynamic_css( 'header-menu'+ index +'-submenu-top-offset', dynamicStyle );

					} );
				} );

				// Sub Menu - Width.
				wp.customize( 'astra-settings[header-menu'+ index +'-submenu-width]', function( value ) {
					value.bind( function( width ) {

						var dynamicStyle = '.ast-builder-menu-' + index + ' li.menu-item .sub-menu, .ast-builder-menu-' + index + ' ul.inline-on-mobile li.menu-item .sub-menu {';
						dynamicStyle += 'width: ' + width + 'px';
						dynamicStyle += '}';

						astra_add_dynamic_css( 'header-menu'+ index +'-submenu-width', dynamicStyle );

					} );
				} );

				// Sub menu
				astra_font_extras_css( 'header-menu' + index + '-font-extras', '.ast-builder-menu-' + index + ' .menu-item > .menu-link' );

			})(index);


			// Sub Menu - Border Color.
			astra_css(
				'astra-settings[header-' + prefix + '-submenu-b-color]',
				'border-color',
				selector + ' li.menu-item .sub-menu, ' + selector + ' .inline-on-mobile li.menu-item .sub-menu '
			);
	}

	// Transparent header > Submenu link hover color.
	astra_color_responsive_css( 'astra-builder-transparent-submenu', 'astra-settings[transparent-submenu-h-color-responsive]', 'color', '.ast-theme-transparent-header .main-header-menu .menu-item .sub-menu .menu-item:hover > .menu-link' );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

    var selector = '.ast-builder-menu-mobile .main-navigation';
    var section = 'section-header-mobile-menu';

    // Advanced Visibility CSS Generation.
    astra_builder_visibility_css( section, selector, 'block' );

    /**
     * Typography CSS.
     */

        // Menu Typography.
        astra_generate_outside_font_family_css(
            'astra-settings[header-mobile-menu-font-family]',
            selector + ' .menu-item > .menu-link'
        );
        astra_generate_font_weight_css(
            'astra-settings[header-mobile-menu-font-family]',
            'astra-settings[header-mobile-menu-font-weight]',
            'font-weight',
            selector + ' .menu-item > .menu-link'
        );

        astra_responsive_font_size(
            'astra-settings[header-mobile-menu-font-size]',
            selector + ' .menu-item > .menu-link'
        );

		astra_font_extras_css( 'font-extras-header-mobile-menu', '.ast-builder-menu-mobile .main-navigation .menu-item > .menu-link' );

    /**
     * Color CSS.
     */

        /**
         * Menu - Colors
         */

        // Menu - Normal Color
        astra_color_responsive_css(
            'astra-menu-color-preview',
            'astra-settings[header-mobile-menu-color-responsive]',
            'color',
            selector + ' .main-header-menu .menu-item > .menu-link'
        );

        // Menu - Hover Color
        astra_color_responsive_css(
            'astra-menu-h-color-preview',
            'astra-settings[header-mobile-menu-h-color-responsive]',
            'color',
            selector + ' .menu-link:hover, ' + selector + ' .main-header-menu > .menu-item:hover > .menu-link, ' + selector + ' .inline-on-mobile .ast-menu-toggle:hover, ' + selector + ' .inline-on-mobile .main-header-menu > .menu-item:hover > .ast-menu-toggle'
        );

        // Menu Toggle -  Color
        astra_color_responsive_css(
            'astra-builder-toggle',
            'astra-settings[header-mobile-menu-color-responsive]',
            'color',
            selector + ' .main-header-menu .menu-item > .ast-menu-toggle'
        );

        // Menu Toggle - Hover Color
        astra_color_responsive_css(
            'astra-menu-h-toogle-color-preview',
            'astra-settings[header-mobile-menu-h-color-responsive]',
            'color',
            selector + ' .ast-menu-toggle:hover, ' + selector + ' .main-header-menu > .menu-item:hover > .ast-menu-toggle'
        );
        // Menu - Active Color
        astra_color_responsive_css(
            'astra-menu-active-color-preview',
            'astra-settings[header-mobile-menu-a-color-responsive]',
            'color',
            selector + ' .menu-item.current-menu-item > .menu-link, ' + selector + ' .inline-on-mobile .menu-item.current-menu-item > .ast-menu-toggle'
        );

        // Menu - Normal Background
        astra_apply_responsive_background_css( 'astra-settings[header-mobile-menu-bg-obj-responsive]', selector + ' .main-header-menu .menu-link, ' + selector + ' .main-header-menu .sub-menu', 'desktop' );
        astra_apply_responsive_background_css( 'astra-settings[header-mobile-menu-bg-obj-responsive]', selector + ' .main-header-menu .menu-link, ' + selector + ' .main-header-menu .sub-menu', 'tablet' );
        astra_apply_responsive_background_css( 'astra-settings[header-mobile-menu-bg-obj-responsive]', selector + ' .main-header-menu .menu-link, ' + selector + ' .main-header-menu .sub-menu', 'mobile' );

        // Menu - Hover Background
        astra_color_responsive_css(
            'astra-menu-bg-preview',
            'astra-settings[header-mobile-menu-h-bg-color-responsive]',
            'background',
            selector + ' .menu-link:hover, ' + selector + ' .main-header-menu > .menu-item:hover > .menu-link, ' + selector + ' .inline-on-mobile .ast-menu-toggle:hover, ' + selector + ' .inline-on-mobile .main-header-menu > .menu-item:hover > .ast-menu-toggle'
        );

        // Menu - Active Background
        astra_color_responsive_css(
            'astra-builder',
            'astra-settings[header-mobile-menu-a-bg-color-responsive]',
            'background',
            selector + ' .menu-item.current-menu-item > .menu-link, ' + selector + ' .inline-on-mobile .menu-item.current-menu-item > .ast-menu-toggle'
        );

    /**
     * Border CSS.
     */

        (function () {

            // Sub Menu - Divider Size.
            wp.customize( 'astra-settings[header-mobile-menu-submenu-item-b-size]', function( value ) {
                value.bind( function( borderSize ) {
                    var selector = '.ast-hfb-header .ast-builder-menu-mobile .main-navigation';
                    var dynamicStyle = '';
                    dynamicStyle += selector + ' .main-header-menu {';
                    dynamicStyle += 'border-top-width: ' + borderSize + 'px;';
                    dynamicStyle += '} ';
                    dynamicStyle += selector + ' .menu-item .sub-menu .menu-link, ' + selector + ' .menu-item .menu-link {';
                    dynamicStyle += 'border-bottom-width: ' + borderSize + 'px;';
                    dynamicStyle += '} ';
                    astra_add_dynamic_css( 'header-mobile-menu-submenu-item-b-size', dynamicStyle );
                } );
            } );

            // Menu 1 > Sub Menu Border Size.
            wp.customize( 'astra-settings[header-mobile-menu-submenu-border]', function( setting ) {
                setting.bind( function( border ) {

                    var dynamicStyle = '.ast-builder-menu-mobile  .sub-menu {';
                    dynamicStyle += 'border-top-width:'  + border.top + 'px;';
                    dynamicStyle += 'border-right-width:'  + border.right + 'px;';
                    dynamicStyle += 'border-left-width:'   + border.left + 'px;';
                    dynamicStyle += 'border-style: solid;';
                    dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';

                    dynamicStyle += '}';
                    astra_add_dynamic_css( 'header-mobile-menu-submenu-border', dynamicStyle );

                } );
            } );

            // Menu Spacing - Menu 1.
            wp.customize( 'astra-settings[header-mobile-menu-menu-spacing]', function( value ) {
                value.bind( function( padding ) {
                    var dynamicStyle = '';
                    dynamicStyle += '.ast-hfb-header .ast-builder-menu-mobile .main-header-menu .menu-item > .menu-link {';
                    dynamicStyle += 'padding-left: ' + padding['desktop']['left'] + padding['desktop-unit'] + ';';
                    dynamicStyle += 'padding-right: ' + padding['desktop']['right'] + padding['desktop-unit'] + ';';
                    dynamicStyle += 'padding-top: ' + padding['desktop']['top'] + padding['desktop-unit'] + ';';
                    dynamicStyle += 'padding-bottom: ' + padding['desktop']['bottom'] + padding['desktop-unit'] + ';';
                    dynamicStyle += '} ';

                    // Toggle top.
                    dynamicStyle += '.ast-hfb-header .ast-builder-menu-mobile .main-navigation ul .menu-item.menu-item-has-children > .ast-menu-toggle {';
                    dynamicStyle += 'top: ' + padding['desktop']['top'] + padding['desktop-unit'] + ';';
                    dynamicStyle += 'right: calc( ' + padding['desktop']['right'] + padding['desktop-unit'] + ' - 0.907em );'
                    dynamicStyle += '} ';

                    dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                    dynamicStyle += '.ast-header-break-point .ast-builder-menu-mobile .main-header-menu .menu-item > .menu-link {';
                    dynamicStyle += 'padding-left: ' + padding['tablet']['left'] + padding['tablet-unit'] + ';';
                    dynamicStyle += 'padding-right: ' + padding['tablet']['right'] + padding['tablet-unit'] + ';';
                    dynamicStyle += 'padding-top: ' + padding['tablet']['top'] + padding['tablet-unit'] + ';';
                    dynamicStyle += 'padding-bottom: ' + padding['tablet']['bottom'] + padding['tablet-unit'] + ';';
                    dynamicStyle += '} ';
                    // Toggle top.
                    dynamicStyle += '.ast-hfb-header .ast-builder-menu-mobile .main-navigation ul .menu-item.menu-item-has-children > .ast-menu-toggle {';
                    dynamicStyle += 'top: ' + padding['tablet']['top'] + padding['tablet-unit'] + ';';
                    dynamicStyle += 'right: calc( ' + padding['tablet']['right'] + padding['tablet-unit'] + ' - 0.907em );'
                    dynamicStyle += '} ';

                    dynamicStyle += '} ';

                    dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                    dynamicStyle += '.ast-header-break-point .ast-builder-menu-mobile .main-header-menu .menu-item > .menu-link {';
                    dynamicStyle += 'padding-left: ' + padding['mobile']['left'] + padding['mobile-unit'] + ';';
                    dynamicStyle += 'padding-right: ' + padding['mobile']['right'] + padding['mobile-unit'] + ';';
                    dynamicStyle += 'padding-top: ' + padding['mobile']['top'] + padding['mobile-unit'] + ';';
                    dynamicStyle += 'padding-bottom: ' + padding['mobile']['bottom'] + padding['mobile-unit'] + ';';
                    dynamicStyle += '} ';
                    // Toggle top.
                    dynamicStyle += '.ast-hfb-header .ast-builder-menu-mobile .main-navigation ul .menu-item.menu-item-has-children > .ast-menu-toggle {';
                    dynamicStyle += 'top: ' + padding['mobile']['top'] + padding['mobile-unit'] + ';';
                    dynamicStyle += 'right: calc( ' + padding['mobile']['right'] + padding['mobile-unit'] + ' - 0.907em );'
                    dynamicStyle += '} ';

                    dynamicStyle += '} ';

                    astra_add_dynamic_css( 'header-mobile-menu-menu-spacing-toggle-button', dynamicStyle );
                } );
            } );

            // Margin - Menu 1.
            wp.customize( 'astra-settings[section-header-mobile-menu-margin]', function( value ) {
                value.bind( function( margin ) {
                    var selector = '.ast-builder-menu-mobile .main-header-menu, .ast-header-break-point .ast-builder-menu-mobile .main-header-menu';
                    var dynamicStyle = '';
                    dynamicStyle += selector + ' {';
                    dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
                    dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
                    dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
                    dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
                    dynamicStyle += '} ';

                    dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                    dynamicStyle += selector + ' {';
                    dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
                    dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
                    dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
                    dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
                    dynamicStyle += '} ';
                    dynamicStyle += '} ';

                    dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                    dynamicStyle += selector + ' {';
                    dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
                    dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
                    dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
                    dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
                    dynamicStyle += '} ';
                    dynamicStyle += '} ';
                    astra_add_dynamic_css( 'section-header-mobile-menu-margin', dynamicStyle );
                } );
            } );

            /**
             * Header Menu 1 > Submenu border Color
             */
            wp.customize('astra-settings[header-mobile-menu-submenu-item-b-color]', function (value) {
                value.bind(function (color) {
                    var insideBorder = wp.customize('astra-settings[header-mobile-menu-submenu-item-border]').get(),
                        borderSize = wp.customize('astra-settings[header-mobile-menu-submenu-item-b-size]').get();
                    if ('' != color) {
                        if ( true == insideBorder ) {

                            var dynamicStyle = '';

                            dynamicStyle += '.ast-hfb-header .ast-builder-menu-mobile .main-navigation .menu-item .sub-menu .menu-link, .ast-hfb-header .ast-builder-menu-mobile .main-navigation .menu-item .menu-link';
                            dynamicStyle += '{';
                            dynamicStyle += 'border-bottom-width:' + ( ( true === insideBorder ) ? borderSize + 'px;' : '0px;' );
                            dynamicStyle += 'border-color:' + color + ';';
                            dynamicStyle += 'border-style: solid;';
                            dynamicStyle += '}';
                            dynamicStyle += '.ast-hfb-header .ast-builder-menu-mobile .main-navigation .main-header-menu';
                            dynamicStyle += '{';
                            dynamicStyle += 'border-top-width:' + ( ( true === insideBorder ) ? borderSize + 'px;' : '0px;' );
                            dynamicStyle += 'border-color:' + color + ';';
                            dynamicStyle += '}';

                            astra_add_dynamic_css('header-mobile-menu-submenu-item-b-color', dynamicStyle);
                        } else {
                            wp.customize.preview.send( 'refresh' );
                        }
                    } else {
                        wp.customize.preview.send('refresh');
                    }
                });
            });

            /**
             * Header Menu 1 > Submenu border Color
             */
            wp.customize( 'astra-settings[header-mobile-menu-submenu-item-border]', function( value ) {
                value.bind( function( border ) {
                    var color = wp.customize( 'astra-settings[header-mobile-menu-submenu-item-b-color]' ).get(),
                        borderSize = wp.customize('astra-settings[header-mobile-menu-submenu-item-b-size]').get();

                    if( true === border  ) {
                        var dynamicStyle = '.ast-builder-menu-mobile .main-navigation .main-header-menu .menu-item .sub-menu .menu-link, .ast-builder-menu-mobile .main-navigation .main-header-menu .menu-item .menu-link';
                        dynamicStyle += '{';
                        dynamicStyle += 'border-bottom-width:' + ( ( true === border ) ? borderSize + 'px;' : '0px;' );
                        dynamicStyle += 'border-color:'        + color + ';';
                        dynamicStyle += 'border-style: solid;';
                        dynamicStyle += '}';
                        dynamicStyle += '.ast-builder-menu-mobile .main-navigation .main-header-menu';
                        dynamicStyle += '{';
                        dynamicStyle += 'border-top-width:' + ( ( true === border ) ? borderSize + 'px;' : '0px;' );
                        dynamicStyle += 'border-style: solid;';
                        dynamicStyle += 'border-color:' + color + ';';
                        dynamicStyle += '}';

                        astra_add_dynamic_css( 'header-mobile-menu-submenu-item-border', dynamicStyle );
                    } else {
                        wp.customize.preview.send( 'refresh' );
                    }

                } );
            } );
        })();


        // Sub Menu - Border Color.
        astra_css(
            'astra-settings[header-mobile-menu-submenu-b-color]',
            'border-color',
            selector + ' li.menu-item .sub-menu, ' + selector + ' .main-header-menu'
        );

	// Transparent header > Submenu link hover color.
	astra_color_responsive_css( 'astra-builder-transparent-submenu', 'astra-settings[transparent-submenu-h-color-responsive]', 'color', '.ast-theme-transparent-header .main-header-menu .menu-item .sub-menu .menu-item:hover > .menu-link' );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
        mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	// Trigger Icon Color.
	astra_css(
		'astra-settings[mobile-header-toggle-btn-color]',
		'fill',
		'[data-section="section-header-mobile-trigger"] .ast-button-wrap .mobile-menu-toggle-icon .ast-mobile-svg'
	);

	// Trigger Label Color.
	astra_css(
		'astra-settings[mobile-header-toggle-btn-color]',
		'color',
		'[data-section="section-header-mobile-trigger"] .ast-button-wrap .mobile-menu-wrap .mobile-menu'
	);

	// Trigger Icon Width.
	astra_css(
		'astra-settings[mobile-header-toggle-icon-size]',
		'width',
		'[data-section="section-header-mobile-trigger"] .ast-button-wrap .mobile-menu-toggle-icon .ast-mobile-svg',
		'px'
	);

	// Trigger Icon Height.
	astra_css(
		'astra-settings[mobile-header-toggle-icon-size]',
		'height',
		'[data-section="section-header-mobile-trigger"] .ast-button-wrap .mobile-menu-toggle-icon .ast-mobile-svg',
		'px'
	);

	// Trigger Button Background Color.
	astra_css(
		'astra-settings[mobile-header-toggle-btn-bg-color]',
		'background',
		'[data-section="section-header-mobile-trigger"] .ast-button-wrap .menu-toggle.ast-mobile-menu-trigger-fill'
	);

	// Border Size for Trigger Button.
	wp.customize( 'astra-settings[mobile-header-toggle-btn-border-size]', function( setting ) {
		setting.bind( function( border ) {
			var dynamicStyle = '[data-section="section-header-mobile-trigger"] .ast-button-wrap .menu-toggle.main-header-menu-toggle {';
				dynamicStyle += 'border-top-width:'  + border.top + 'px;';
				dynamicStyle += 'border-right-width:'  + border.right + 'px;';
				dynamicStyle += 'border-left-width:'   + border.left + 'px;';
				dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';
				dynamicStyle += '} ';
			astra_add_dynamic_css( 'astra-settings[mobile-header-toggle-btn-border-size]', dynamicStyle );
		} );
	} );

	// Border Radius Fields.
	wp.customize( 'astra-settings[mobile-header-toggle-border-radius-fields]', function( setting ) {
		setting.bind( function( border ) {
			let globalSelector = '[data-section="section-header-mobile-trigger"] .ast-button-wrap .menu-toggle.main-header-menu-toggle';

			let dynamicStyle = globalSelector + '{ border-top-left-radius :' + border['desktop']['top'] + border['desktop-unit']
					+ '; border-bottom-right-radius :' + border['desktop']['bottom'] + border['desktop-unit'] + '; border-bottom-left-radius :'
					+ border['desktop']['left'] + border['desktop-unit'] + '; border-top-right-radius :' + border['desktop']['right'] + border['desktop-unit'] + '; } ';

			dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['tablet']['top'] + border['tablet-unit']
					+ '; border-bottom-right-radius :' + border['tablet']['bottom'] + border['tablet-unit'] + '; border-bottom-left-radius :'
					+ border['tablet']['left'] + border['tablet-unit'] + '; border-top-right-radius :' + border['tablet']['right'] + border['tablet-unit'] + '; } } ';

			dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['mobile']['top'] + border['mobile-unit']
					+ '; border-bottom-right-radius :' + border['mobile']['bottom'] + border['mobile-unit'] + '; border-bottom-left-radius :'
					+ border['mobile']['left'] + border['mobile-unit'] + '; border-top-right-radius :' + border['mobile']['right'] + border['mobile-unit'] + '; } } ';

			astra_add_dynamic_css( 'astra-settings[mobile-header-toggle-border-radius-fields]', dynamicStyle );
		} );
	} );

	// Border Color.
	astra_css(
		'astra-settings[mobile-header-toggle-border-color]',
		'border-color',
		'[data-section="section-header-mobile-trigger"] .ast-button-wrap .menu-toggle.ast-mobile-menu-trigger-outline, [data-section="section-header-mobile-trigger"] .ast-button-wrap .menu-toggle.ast-mobile-menu-trigger-fill'
	);

	// Margin.
    wp.customize( 'astra-settings[section-header-mobile-trigger' + '-margin]', function( value ) {
        value.bind( function( margin ) {
            if(
                margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
                margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
                margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
            ) {
				var selector = '[data-section="section-header-mobile-trigger"] .ast-button-wrap .menu-toggle.main-header-menu-toggle';
                var dynamicStyle = '';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';
                astra_add_dynamic_css( 'header-mobile-trigger-margin', dynamicStyle );
            }
        } );
    } );

	// Trigger Typography.
	astra_css(
		'astra-settings[mobile-header-label-font-size]',
		'font-size',
		'[data-section="section-header-mobile-trigger"] .ast-button-wrap .mobile-menu-wrap .mobile-menu',
		'px'
	);

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	// Close Icon Color.
	astra_css(
		'astra-settings[off-canvas-close-color]',
		'color',
		'.ast-mobile-popup-drawer.active .menu-toggle-close'
	);

	// Off-Canvas Background Color.
	wp.customize( 'astra-settings[off-canvas-background]', function( value ) {
		value.bind( function( bg_obj ) {
			var dynamicStyle = ' .ast-mobile-popup-drawer.active .ast-mobile-popup-inner, .ast-mobile-header-wrap .ast-mobile-header-content, .ast-desktop-header-content { {{css}} }';
			astra_background_obj_css( wp.customize, bg_obj, 'off-canvas-background', dynamicStyle );
		} );
	} );

	wp.customize( 'astra-settings[off-canvas-inner-spacing]', function ( value ) {
        value.bind( function ( spacing ) {
			var dynamicStyle = '';
			if( spacing != '' ) {
				dynamicStyle += '.ast-mobile-popup-content > *, .ast-mobile-header-content > *, .ast-desktop-popup-content > *, .ast-desktop-header-content > * {';
				dynamicStyle += 'padding-top: ' + spacing + 'px;';
				dynamicStyle += 'padding-bottom: ' + spacing + 'px;';
				dynamicStyle += '} ';
			}
			astra_add_dynamic_css( 'off-canvas-inner-spacing', dynamicStyle );
        } );
	} );

	wp.customize( 'astra-settings[mobile-header-type]', function ( value ) {
        value.bind( function ( newVal ) {

			var mobile_header = document.querySelectorAll( "#ast-mobile-header" );
			var desktop_header = document.querySelectorAll( "#ast-desktop-header" );
			var header_type = newVal;
			var off_canvas_slide = ( typeof ( wp.customize._value['astra-settings[off-canvas-slide]'] ) != 'undefined' ) ? wp.customize._value['astra-settings[off-canvas-slide]']._value : 'right';

			var side_class = '';

			if ( 'off-canvas' === header_type ) {

				if ( 'left' === off_canvas_slide ) {

					side_class = 'ast-mobile-popup-left';
				} else {

					side_class = 'ast-mobile-popup-right';
				}
			} else if ( 'full-width' === header_type ) {

				side_class = 'ast-mobile-popup-full-width';
			}

			jQuery('.ast-mobile-popup-drawer').removeClass( 'ast-mobile-popup-left' );
			jQuery('.ast-mobile-popup-drawer').removeClass( 'ast-mobile-popup-right' );
			jQuery('.ast-mobile-popup-drawer').removeClass( 'ast-mobile-popup-full-width' );
			jQuery('.ast-mobile-popup-drawer').addClass( side_class );

			if( 'full-width' === header_type ) {

				header_type = 'off-canvas';
			}

			for ( var k = 0; k < mobile_header.length; k++ ) {
				mobile_header[k].setAttribute( 'data-type', header_type );
			}
			for ( var k = 0; k < desktop_header.length; k++ ) {
				desktop_header[k].setAttribute( 'data-type', header_type );
			}

			var event = new CustomEvent( "astMobileHeaderTypeChange",
				{
					"detail": { 'type' : header_type }
				}
			);

			document.dispatchEvent(event);
        } );
	} );

	wp.customize( 'astra-settings[off-canvas-slide]', function ( value ) {
        value.bind( function ( newval ) {

			var side_class = '';

			if ( 'left' === newval ) {

				side_class = 'ast-mobile-popup-left';
			} else {

				side_class = 'ast-mobile-popup-right';
			}

			jQuery('.ast-mobile-popup-drawer').removeClass( 'ast-mobile-popup-left' );
			jQuery('.ast-mobile-popup-drawer').removeClass( 'ast-mobile-popup-right' );
			jQuery('.ast-mobile-popup-drawer').removeClass( 'ast-mobile-popup-full-width' );
			jQuery('.ast-mobile-popup-drawer').addClass( side_class );
        } );
	} );

    // Padding.
    wp.customize( 'astra-settings[off-canvas-padding]', function( value ) {
        value.bind( function( padding ) {
            if(
                padding.desktop.bottom != '' || padding.desktop.top != '' || padding.desktop.left != '' || padding.desktop.right != '' ||
                padding.tablet.bottom != '' || padding.tablet.top != '' || padding.tablet.left != '' || padding.tablet.right != '' ||
                padding.mobile.bottom != '' || padding.mobile.top != '' || padding.mobile.left != '' || padding.mobile.right != ''
            ) {
                var dynamicStyle = '';
                dynamicStyle += '.ast-mobile-popup-drawer.active .ast-desktop-popup-content, .ast-mobile-popup-drawer.active .ast-mobile-popup-content {';
                dynamicStyle += 'padding-left: ' + padding['desktop']['left'] + padding['desktop-unit'] + ';';
                dynamicStyle += 'padding-right: ' + padding['desktop']['right'] + padding['desktop-unit'] + ';';
                dynamicStyle += 'padding-top: ' + padding['desktop']['top'] + padding['desktop-unit'] + ';';
                dynamicStyle += 'padding-bottom: ' + padding['desktop']['bottom'] + padding['desktop-unit'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += '.ast-mobile-popup-drawer.active .ast-desktop-popup-content, .ast-mobile-popup-drawer.active .ast-mobile-popup-content {';
                dynamicStyle += 'padding-left: ' + padding['tablet']['left'] + padding['tablet-unit'] + ';';
                dynamicStyle += 'padding-right: ' + padding['tablet']['right'] + padding['tablet-unit'] + ';';
                dynamicStyle += 'padding-top: ' + padding['tablet']['top'] + padding['tablet-unit'] + ';';
                dynamicStyle += 'padding-bottom: ' + padding['tablet']['bottom'] + padding['tablet-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += '.ast-mobile-popup-drawer.active .ast-desktop-popup-content, .ast-mobile-popup-drawer.active .ast-mobile-popup-content {';
                dynamicStyle += 'padding-left: ' + padding['mobile']['left'] + padding['mobile-unit'] + ';';
                dynamicStyle += 'padding-right: ' + padding['mobile']['right'] + padding['mobile-unit'] + ';';
                dynamicStyle += 'padding-top: ' + padding['mobile']['top'] + padding['mobile-unit'] + ';';
                dynamicStyle += 'padding-bottom: ' + padding['mobile']['bottom'] + padding['mobile-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';
                astra_add_dynamic_css( 'off-canvas-padding', dynamicStyle );
            } else {
                astra_add_dynamic_css( 'off-canvas-padding', '' );
            }
		} );
	} );

	wp.customize( 'astra-settings[header-builder-menu-toggle-target]', function ( value ) {
        value.bind( function ( newval ) {
			var menuTargetClass   = 'ast-builder-menu-toggle-' + newval + ' ';

			jQuery( '.site-header' ).removeClass( 'ast-builder-menu-toggle-icon' );
			jQuery( '.site-header' ).removeClass( 'ast-builder-menu-toggle-link' );
			jQuery( '.site-header' ).addClass( menuTargetClass );
		} );
	} );

	wp.customize( 'astra-settings[header-offcanvas-content-alignment]', function ( value ) {
        value.bind( function ( newval ) {

			var alignment_class   = 'content-align-' + newval + ' ';
			var menu_content_alignment = 'center';

			jQuery('.ast-mobile-header-content').removeClass( 'content-align-flex-start' );
			jQuery('.ast-mobile-header-content').removeClass( 'content-align-flex-end' );
			jQuery('.ast-mobile-header-content').removeClass( 'content-align-center' );
			jQuery('.ast-mobile-popup-drawer').removeClass( 'content-align-flex-end' );
			jQuery('.ast-mobile-popup-drawer').removeClass( 'content-align-flex-start' );
			jQuery('.ast-mobile-popup-drawer').removeClass( 'content-align-center' );
			jQuery('.ast-desktop-header-content').removeClass( 'content-align-flex-start' );
			jQuery('.ast-desktop-header-content').removeClass( 'content-align-flex-end' );
			jQuery('.ast-desktop-header-content').removeClass( 'content-align-center' );

			jQuery('.ast-desktop-header-content').addClass( alignment_class );
			jQuery('.ast-mobile-header-content').addClass( alignment_class );
			jQuery('.ast-mobile-popup-drawer').addClass( alignment_class );



			if ( 'flex-start' === newval ) {
				menu_content_alignment = 'left';
			} else if ( 'flex-end' === newval ) {
				menu_content_alignment = 'right';
			}

			var dynamicStyle = '.content-align-' + newval + ' .ast-builder-layout-element {';
			dynamicStyle += 'justify-content: ' + newval + ';';
			dynamicStyle += '} ';

			dynamicStyle += '.content-align-' + newval + ' .main-header-menu {';
			dynamicStyle += 'text-align: ' + menu_content_alignment + ';';
			dynamicStyle += '} ';

			astra_add_dynamic_css( 'header-offcanvas-content-alignment', dynamicStyle );
        } );
	} );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	wp.customize( 'astra-settings[hb-header-height]', function( value ) {
		value.bind( function( size ) {

			if( size.desktop != '' || size.tablet != '' || size.mobile != '' ) {
				var dynamicStyle = '';
				dynamicStyle += '.ast-mobile-header-wrap .ast-primary-header-bar , .ast-primary-header-bar .site-primary-header-wrap {';
				dynamicStyle += 'min-height: ' + size.desktop + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '.ast-desktop:not(:has(.ast-header-sticked)) .ast-primary-header-bar .main-header-menu > .menu-item {';
				dynamicStyle += 'line-height: ' + size.desktop + 'px;';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += '.ast-mobile-header-wrap .ast-primary-header-bar , .ast-primary-header-bar .site-primary-header-wrap {';
				dynamicStyle += 'min-height: ' + size.tablet + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += '.ast-mobile-header-wrap .ast-primary-header-bar , .ast-primary-header-bar .site-primary-header-wrap {';
				dynamicStyle += 'min-height: ' + size.mobile + 'px;';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				astra_add_dynamic_css( 'hb-header-height', dynamicStyle );
			}
		} );
	} );

	// Primary Header - Layout > Content Width.
	wp.customize( 'astra-settings[hb-header-main-layout-width]', function( setting ) {
		setting.bind( function( layout ) {

			var dynamicStyle = '';

			if ( 'content' !== layout ) {
				dynamicStyle += '#masthead .ast-container, .site-header-focus-item + .ast-breadcrumbs-wrapper {';
				dynamicStyle += 'max-width: unset;';
				dynamicStyle += 'padding-left: 35px;';
				dynamicStyle += 'padding-right: 35px;';
				dynamicStyle += '} ';

			} else {
				dynamicStyle = '#masthead .ast-container, .site-header-focus-item + .ast-breadcrumbs-wrapper {';
				dynamicStyle += 'max-width: 100%';
				dynamicStyle += 'padding-left: 20px;';
				dynamicStyle += 'padding-right: 20px;';
				dynamicStyle += '} ';
			}

			dynamicStyle +=  '@media (max-width: ' + AstraBuilderPrimaryHeaderData.header_break_point + 'px) {';
			dynamicStyle += '#masthead .ast-mobile-header-wrap .ast-above-header-bar, #masthead .ast-mobile-header-wrap .ast-primary-header-bar, #masthead .ast-mobile-header-wrap .ast-below-header-bar {';
			dynamicStyle += 'padding-left: 20px;';
			dynamicStyle += 'padding-right: 20px;';
			dynamicStyle += '} ';
			dynamicStyle += '} ';

			astra_add_dynamic_css( 'hb-header-main-layout-width', dynamicStyle );
		} );
	} );

	// Border Bottom width.
	wp.customize( 'astra-settings[hb-header-main-sep]', function( value ) {
		value.bind( function( border ) {

			var color = wp.customize( 'astra-settings[hb-header-main-sep-color]' ).get(),
				dynamicStyle = '';

			dynamicStyle += '.ast-header-break-point .ast-primary-header-bar, .ast-primary-header-bar {';
			dynamicStyle += 'border-bottom-width: ' + border + 'px;';
			dynamicStyle += 'border-bottom-style: solid;';
			dynamicStyle += 'border-color:' + color + ';';
			dynamicStyle += '}';

			astra_add_dynamic_css( 'hb-header-main-sep', dynamicStyle );

		} );
	} );

	// Border Color.
	astra_css(
		'astra-settings[hb-header-main-sep-color]',
		'border-color',
		'.ast-header-break-point .ast-primary-header-bar, .ast-primary-header-bar'
	);

	// Responsive BG styles > Primary Header Row.
	astra_apply_responsive_background_css( 'astra-settings[hb-header-bg-obj-responsive]', '.main-header-bar', 'desktop' );
	astra_apply_responsive_background_css( 'astra-settings[hb-header-bg-obj-responsive]', '.ast-primary-header.main-header-bar', 'tablet' );
	astra_apply_responsive_background_css( 'astra-settings[hb-header-bg-obj-responsive]', '.ast-primary-header.main-header-bar', 'mobile' );

	const iframe = document.querySelector('#customize-preview iframe');

	// Style guide logo background colour preview.
	var desktopHeader = document.getElementById("ast-desktop-header");

	if (desktopHeader) {
		var mainHeaderWrap = desktopHeader.querySelector( '.ast-main-header-wrap.main-header-bar-wrap' );

		if (mainHeaderWrap && mainHeaderWrap.querySelector(".site-logo-img")) {
			astra_apply_responsive_background_css( "astra-settings[hb-header-bg-obj-responsive]", ".ast-sg-element-wrap.ast-sg-logo-section, .main-header-bar", "desktop" );
			astra_apply_responsive_background_css( "astra-settings[hb-header-bg-obj-responsive]", ".ast-sg-element-wrap.ast-sg-logo-section, .ast-primary-header.main-header-bar", "tablet" );
			astra_apply_responsive_background_css( "astra-settings[hb-header-bg-obj-responsive]", ".ast-sg-element-wrap.ast-sg-logo-section, .ast-primary-header.main-header-bar", "mobile" );
		}
	}

	// Advanced CSS Generation.
	astra_builder_advanced_css( 'section-primary-header-builder', '.ast-desktop .ast-primary-header-bar, .ast-header-break-point .ast-primary-header-bar' );

	// Advanced Visibility CSS Generation.
	astra_builder_visibility_css( 'section-primary-header-builder', '.ast-primary-header-bar', 'grid' );

	// Advanced CSS for Header Builder - Margin.
	wp.customize( 'astra-settings[section-header-builder-layout-margin]', function( value ) {
        value.bind( function( margin ) {
            if(
                margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
                margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
                margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
            ) {
                var dynamicStyle = '';
                dynamicStyle += '.ast-hfb-header .site-header {';
                dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += '.ast-hfb-header .site-header {';
                dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += '.ast-hfb-header .site-header {';
                dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';
                astra_add_dynamic_css( 'section-header-builder-layout-margin-toggle-button', dynamicStyle );
            }
        } );
    } );


} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	var selector = '.ast-header-search';
    var section = 'section-header-search';

	// Icon Color.
	astra_color_responsive_css(
		'header-search-icon-color',
		'astra-settings[header-search-icon-color]',
		'color',
		selector + ' .astra-search-icon, ' + selector + ' .search-field::placeholder,' + selector + ' .ast-icon'
	);

	// Icon Size.
	astra_css(
		'astra-settings[header-search-icon-space]',
		'font-size',
		selector + ' .astra-search-icon',
		'px'
	);

	// Icon Size.
	wp.customize( 'astra-settings[header-search-icon-space]', function( value ) {
		value.bind( function( size ) {
			if( size.desktop != '' || size.tablet != '' || size.mobile != '' ) {
				var dynamicStyle = '';
				dynamicStyle += selector + ' .astra-search-icon {';
				dynamicStyle += 'font-size: ' + size.desktop + 'px' + ';';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += selector + ' .astra-search-icon {';
				dynamicStyle += 'font-size: ' + size.tablet + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += selector + ' .astra-search-icon {';
				dynamicStyle += 'font-size: ' + size.mobile + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '} ';
				astra_add_dynamic_css( 'header-search-icon-space', dynamicStyle );
			}
		} );
	} );

	wp.customize( 'astra-settings[header-search-width]', function( setting ) {
		setting.bind( function( width ) {
		if ( width['desktop'] != '' || width['tablet'] != '' || width['mobile'] != '' ) {
				var dynamicStyle = '.ast-header-search form.search-form .search-field, .ast-header-search .ast-dropdown-active.ast-search-menu-icon.slide-search input.search-field {';
					dynamicStyle += 'width:'  + width['desktop'] + 'px;';
					dynamicStyle += '} ';
					dynamicStyle += '@media( max-width: ' + tablet_break_point + 'px ) {';
					dynamicStyle += '.ast-header-search form.search-form .search-field, .ast-header-search .ast-dropdown-active.ast-search-menu-icon.slide-search input.search-field, .ast-mobile-header-content .ast-search-menu-icon .search-form {';
					dynamicStyle += 'width:'  + width['tablet'] + 'px;';
					dynamicStyle += '} }';
					dynamicStyle += '@media( max-width: ' + mobile_break_point + 'px ) {';
					dynamicStyle += '.ast-header-search form.search-form .search-field, .ast-header-search .ast-dropdown-active.ast-search-menu-icon.slide-search input.search-field, .ast-mobile-header-content .ast-search-menu-icon .search-form {';
					dynamicStyle += 'width:'  + width['mobile'] + 'px;';
					dynamicStyle += '} }';
				astra_add_dynamic_css( 'astra-settings[header-search-width]', dynamicStyle );
			}
		});
	});

	// Margin.
    wp.customize( 'astra-settings[section-header-search-margin]', function( value ) {
        value.bind( function( margin ) {
            if(
                margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
                margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
                margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
            ) {
				var selector = '.ast-hfb-header .site-header-section > .ast-header-search, .ast-hfb-header .ast-header-search';
                var dynamicStyle = '';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';
                astra_add_dynamic_css( 'header-search-margin', dynamicStyle );
            }
        } );
    } );

	// Advanced Visibility CSS Generation.
	astra_builder_visibility_css( section, selector );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra HF Builder.
 * @since 3.0.0
 */

( function( $ ) {

    wp.customize( 'astra-settings[different-mobile-logo]', function ( value ) {
        value.bind( function ( newval ) {

			if ( '1' == newval ) {
                jQuery('.site-header').addClass( 'ast-has-mobile-header-logo' );
            } else {
                jQuery('.site-header').removeClass( 'ast-has-mobile-header-logo' );
            }
        } );
	} );

	// Margin.
    wp.customize( 'astra-settings[title_tagline-margin]', function( value ) {
		value.bind( function( margin ) {
			if(
				margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
                margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
                margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
            ) {
                var dynamicStyle = '',
                    selector = '.ast-builder-layout-element .ast-site-identity',
                    tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
                    mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';
                astra_add_dynamic_css( 'title_tagline-margin', dynamicStyle );
            }
        } );
    } );

	var section = 'title_tagline';
    var visibility_selector = '.ast-builder-layout-element[data-section="title_tagline"]';

    // Advanced Visibility CSS Generation.
    astra_builder_visibility_css( section, visibility_selector );
} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	astra_builder_social_css( 'header', astraBuilderHeaderSocial.component_limit );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra Builder
 * @since 3.0.0
 */

( function( $ ) {

	astra_builder_widget_css( 'header' );

} )( jQuery );

/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since x.x.x
 */

(function ($) {

	var selector = '.ast-site-header-cart';
	var responsive_selector = '.astra-cart-drawer';
	const appendSelector = '.woocommerce-js ';
	var tablet_break_point = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point = astraBuilderPreview.mobile_break_point || 544;

	// Icon Color.
	astra_css(
		'astra-settings[header-woo-cart-icon-color]',
		'color',
		selector + ' .ast-cart-menu-wrap .count, ' + selector + ' .ast-cart-menu-wrap .count:after,' + selector + ' .ast-woo-header-cart-info-wrap,' + selector + ' .ast-site-header-cart .ast-addon-cart-wrap'
	);

	// Icon Color.
	astra_css(
		'astra-settings[header-woo-cart-icon-color]',
		'border-color',
		selector + ' .ast-cart-menu-wrap .count, ' + selector + ' .ast-cart-menu-wrap .count:after'
	);

	// Icon BG Color.
	astra_css(
		'astra-settings[header-woo-cart-icon-color]',
		'border-color',
		'.ast-menu-cart-fill .ast-cart-menu-wrap .count, .ast-menu-cart-fill .ast-cart-menu-wrap'
	);


	// WooCommerce Cart Colors.

	astra_color_responsive_css(
		'woo-cart-text-color',
		'astra-settings[header-woo-cart-text-color]',
		'color',
		'.astra-cart-drawer-title, .ast-site-header-cart-data span, .ast-site-header-cart-data strong, .ast-site-header-cart-data .woocommerce-mini-cart__empty-message, .ast-site-header-cart-data .total .woocommerce-Price-amount, .ast-site-header-cart-data .total .woocommerce-Price-amount .woocommerce-Price-currencySymbol, .ast-header-woo-cart .ast-site-header-cart .ast-site-header-cart-data .widget_shopping_cart .mini_cart_item a.remove,' + responsive_selector + ' .widget_shopping_cart_content span, ' + responsive_selector + ' .widget_shopping_cart_content strong,' + responsive_selector + ' .woocommerce-mini-cart__empty-message, .astra-cart-drawer .woocommerce-mini-cart *, ' + responsive_selector + ' .astra-cart-drawer-title'
	);

	astra_color_responsive_css(
		'woo-cart-border-color',
		'astra-settings[header-woo-cart-text-color]',
		'border-color',
		'.ast-site-header-cart .ast-site-header-cart-data .widget_shopping_cart .mini_cart_item a.remove, ' + responsive_selector + ' .widget_shopping_cart .mini_cart_item a.remove'
	);

	astra_color_responsive_css(
		'woo-cart-link-color',
		'astra-settings[header-woo-cart-link-color]',
		'color',
		selector + ' .ast-site-header-cart-data .widget_shopping_cart_content a:not(.button),' + responsive_selector + ' .widget_shopping_cart_content a:not(.button)'
	);

	astra_color_responsive_css(
		'woo-cart-background-color',
		'astra-settings[header-woo-cart-background-color]',
		'background-color',
		'.ast-site-header-cart .widget_shopping_cart, .astra-cart-drawer'
	);

	astra_color_responsive_css(
		'woo-cart-border-color',
		'astra-settings[header-woo-cart-background-color]',
		'border-color',
		'.ast-site-header-cart .widget_shopping_cart, .astra-cart-drawer'
	);

	astra_color_responsive_css(
		'woo-cart-border-bottom-color',
		'astra-settings[header-woo-cart-background-color]',
		'border-bottom-color',
		'.ast-site-header-cart .widget_shopping_cart:before, .ast-site-header-cart .widget_shopping_cart:after, .open-preview-woocommerce-cart .ast-site-header-cart .widget_shopping_cart:before, #astra-mobile-cart-drawer, .astra-cart-drawer'
	);

	// Added Background Color Hover
	astra_color_responsive_css(
		'woo-cart-background-hover-color',
		'astra-settings[header-woo-cart-background-hover-color]',
		'background-color',
		'.ast-site-header-cart .widget_shopping_cart:hover, #astra-mobile-cart-drawer:hover'
	);

	astra_color_responsive_css(
		'woo-cart-border-color',
		'astra-settings[header-woo-cart-background-hover-color]',
		'border-color',
		'.ast-site-header-cart .widget_shopping_cart:hover, #astra-mobile-cart-drawer:hover'
	);

	astra_color_responsive_css(
		'woo-cart-border-bottom-color',
		'astra-settings[header-woo-cart-background-hover-color]',
		'border-bottom-color',
		'.ast-site-header-cart .widget_shopping_cart:hover,site-header-cart .widget_shopping_cart:hover:after, #astra-mobile-cart-drawer:hover,.ast-site-header-cart:hover .widget_shopping_cart:hover:before, .ast-site-header-cart:hover .widget_shopping_cart:hover:after, .open-preview-woocommerce-cart .ast-site-header-cart .widget_shopping_cart:hover:before'
	);
	astra_color_responsive_css(
		'woo-cart-separator-colors',
		'astra-settings[header-woo-cart-separator-color]',
		'border-top-color',
		'.ast-site-header-cart .widget_shopping_cart .woocommerce-mini-cart__total, #astra-mobile-cart-drawer .widget_shopping_cart .woocommerce-mini-cart__total, .astra-cart-drawer .astra-cart-drawer-header'
	);

	astra_color_responsive_css(
		'woo-cart-border-bottom-colors',
		'astra-settings[header-woo-cart-separator-color]',
		'border-bottom-color',
		'.ast-site-header-cart .widget_shopping_cart .woocommerce-mini-cart__total, #astra-mobile-cart-drawer .widget_shopping_cart .woocommerce-mini-cart__total, .astra-cart-drawer .astra-cart-drawer-header, .ast-site-header-cart .widget_shopping_cart .mini_cart_item, #astra-mobile-cart-drawer .widget_shopping_cart .mini_cart_item'
	);

	astra_color_responsive_css(
		'woo-cart-link-hover-colors',
		'astra-settings[header-woo-cart-link-hover-color]',
		'color',
		'.ast-site-header-cart .ast-site-header-cart-data .widget_shopping_cart_content a:not(.button):hover, .ast-site-header-cart .ast-site-header-cart-data .widget_shopping_cart .mini_cart_item a.remove:hover, .ast-site-header-cart .ast-site-header-cart-data .widget_shopping_cart .mini_cart_item:hover > a.remove,' + responsive_selector + ' .widget_shopping_cart_content a:not(.button):hover,' + responsive_selector + ' .widget_shopping_cart .mini_cart_item a.remove:hover,' + responsive_selector + ' .widget_shopping_cart .mini_cart_item:hover > a.remove'
	);

	astra_color_responsive_css(
		'woo-cart-border-colors',
		'astra-settings[header-woo-cart-link-hover-color]',
		'border-color',
		selector + ' .ast-site-header-cart-data .widget_shopping_cart .mini_cart_item a.remove:hover,' + selector + ' .ast-site-header-cart-data .widget_shopping_cart .mini_cart_item:hover > a.remove,' + responsive_selector + ' .widget_shopping_cart .mini_cart_item a.remove:hover,' + responsive_selector + ' .widget_shopping_cart .mini_cart_item:hover > a.remove'
	);

	astra_color_responsive_css(
		'woo-cart-btn-text-color',
		'astra-settings[header-woo-cart-btn-text-color]',
		'color',
		appendSelector + selector + ' .ast-site-header-cart-data .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout),' + appendSelector + responsive_selector + ' .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout)'
	);

	astra_color_responsive_css(
		'woo-cart-btn-bg-color',
		'astra-settings[header-woo-cart-btn-background-color]',
		'background-color',
		selector + ' .ast-site-header-cart-data .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout),' + appendSelector + responsive_selector + ' .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout)'
	);

	astra_color_responsive_css(
		'woo-cart-btn-hover-text-color',
		'astra-settings[header-woo-cart-btn-text-hover-color]',
		'color',
		selector + ' .ast-site-header-cart-data .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout):hover,' + appendSelector + responsive_selector + ' .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout):hover'
	);

	astra_color_responsive_css(
		'woo-cart-btn-hover-bg-color',
		'astra-settings[header-woo-cart-btn-bg-hover-color]',
		'background-color',
		selector + ' .ast-site-header-cart-data .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout):hover,' + appendSelector + responsive_selector + ' .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout):hover'
	);

	astra_color_responsive_css(
		'woo-cart-checkout-btn-text-color',
		'astra-settings[header-woo-checkout-btn-text-color]',
		'color',
		selector + ' .ast-site-header-cart-data .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.checkout.wc-forward,' + responsive_selector + ' .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.checkout.wc-forward'
	);

	astra_color_responsive_css(
		'woo-cart-checkout-btn-border-color',
		'astra-settings[header-woo-checkout-btn-background-color]',
		'border-color',
		selector + ' .ast-site-header-cart-data .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.checkout.wc-forward,' + responsive_selector + ' .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.checkout.wc-forward'
	);

	astra_color_responsive_css(
		'woo-cart-checkout-btn-background-color',
		'astra-settings[header-woo-checkout-btn-background-color]',
		'background-color',
		selector + ' .ast-site-header-cart-data .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.checkout.wc-forward,' + responsive_selector + ' .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.checkout.wc-forward'
	);

	astra_color_responsive_css(
		'woo-cart-checkout-btn-hover-text-color',
		'astra-settings[header-woo-checkout-btn-text-hover-color]',
		'color',
		selector + ' .ast-site-header-cart-data .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.checkout.wc-forward:hover,' + responsive_selector + ' .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.checkout.wc-forward:hover'
	);

	astra_color_responsive_css(
		'woo-cart-checkout-btn-bg-hover-background-color',
		'astra-settings[header-woo-checkout-btn-bg-hover-color]',
		'background-color',
		selector + ' .ast-site-header-cart-data .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.checkout.wc-forward:hover,' + responsive_selector + ' .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.button.checkout.wc-forward:hover'
	);

	/**
	 * Cart icon style
	 */
	wp.customize('astra-settings[woo-header-cart-icon-style]', function (setting) {
		setting.bind(function (icon_style) {

			var buttons = $(document).find('.ast-site-header-cart');
			buttons.removeClass('ast-menu-cart-fill ast-menu-cart-outline ast-menu-cart-none');
			buttons.addClass('ast-menu-cart-' + icon_style);
			var dynamicStyle = '.ast-site-header-cart a, .ast-site-header-cart a *{ transition: all 0s; } ';
			astra_add_dynamic_css('woo-header-cart-icon-style', dynamicStyle);
			wp.customize.preview.send('refresh');
		});
	});

	/**
	 * Desktop cart offcanvas width.
	 */
	 wp.customize( 'astra-settings[woo-slide-in-cart-width]', function( setting ) {
		setting.bind( function( wooSlideInConfig ) {

			const { desktop, mobile, tablet } = wooSlideInConfig;
			const desktopUnit = wooSlideInConfig['desktop-unit'];
			const tabletUnit = wooSlideInConfig['tablet-unit'];
			const mobileUnit = wooSlideInConfig['mobile-unit'];
			const offcanvasPosition = wp.customize( 'astra-settings[woo-desktop-cart-flyout-direction]' ).get();

			if( 'left' == offcanvasPosition ) {
				var dynamicStyle = '.ast-desktop .astra-cart-drawer { width: ' + desktop + desktopUnit + '; left: -' + desktop + desktopUnit + '; } ';
					dynamicStyle += '.ast-desktop .astra-cart-drawer.active { left: ' + desktop + desktopUnit + '; } ';
			} else {
				var dynamicStyle = '.ast-desktop .astra-cart-drawer { width: ' + desktop + desktopUnit + '; left: 100%; } ';
			}

			//Tablets.
			dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px ) and ( min-width: ' + mobile_break_point + 'px) {';
			dynamicStyle += '#astra-mobile-cart-drawer {';
			dynamicStyle += 'width: ' + tablet + tabletUnit + ';';
			dynamicStyle += '}';
			dynamicStyle += '}';

			// Mobile.
			dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) {';
			dynamicStyle += '#astra-mobile-cart-drawer {';
			dynamicStyle += 'width: ' + mobile + mobileUnit + ';';
			dynamicStyle += '}';
			dynamicStyle += '}';

			if( desktop > 500 ) {
				$( '#astra-mobile-cart-drawer .astra-cart-drawer-content' ).addClass('ast-large-view');
			} else {
				$( '#astra-mobile-cart-drawer .astra-cart-drawer-content' ).removeClass('ast-large-view');
			}
			astra_add_dynamic_css( 'woo-slide-in-cart-width', dynamicStyle );
		} );
	} );

	/**
	 * Cart icon type
	 */
	wp.customize( 'astra-settings[woo-header-cart-icon]', function( setting ) {
		setting.bind( function( icon_type ) {
			$( document.body ).trigger( 'wc_fragment_refresh' );
		} );
	} );

	/**
	 * Cart Click Action
	 */
	 wp.customize( 'astra-settings[woo-header-cart-click-action]', function( setting ) {
		setting.bind( function( clickAction ) {
			//Trigger refresh to reload markup.
			$( document.body ).trigger( 'wc_fragment_refresh' );
			wp.customize.preview.send('refresh');
		} );
	} );

	/**
	 * Cart icon hover style
	 */
	wp.customize('astra-settings[header-woo-cart-icon-hover-color]', function (setting) {
		setting.bind(function (color) {
			wp.customize.preview.send('refresh');
		});
	});

	/**
	 * Cart icon style
	 */
	wp.customize('astra-settings[header-woo-cart-icon-color]', function (setting) {
		setting.bind(function (color) {
			var dynamicStyle = '.ast-menu-cart-fill .ast-cart-menu-wrap .count, .ast-menu-cart-fill .ast-cart-menu-wrap { background-color: ' + color + '; } ';
			astra_add_dynamic_css('header-woo-cart-icon-color', dynamicStyle);
			wp.customize.preview.send('refresh');
		});
	});

	/**
	 * Cart Border Width.
	 */
	astra_css( 'astra-settings[woo-header-cart-border-width]', 'border-width', '.ast-menu-cart-outline .ast-addon-cart-wrap, .ast-theme-transparent-header .ast-menu-cart-outline .ast-addon-cart-wrap', 'px' );

	/**
	 * Cart Border Radius Fields
	 */
	wp.customize('astra-settings[woo-header-cart-icon-radius-fields]', function (setting) {
		setting.bind(function (border) {
			let globalSelector = '.ast-site-header-cart.ast-menu-cart-outline .ast-cart-menu-wrap, .ast-site-header-cart.ast-menu-cart-fill .ast-cart-menu-wrap, .ast-site-header-cart.ast-menu-cart-outline .ast-cart-menu-wrap .count, .ast-site-header-cart.ast-menu-cart-fill .ast-cart-menu-wrap .count, .ast-site-header-cart.ast-menu-cart-outline .ast-addon-cart-wrap, .ast-site-header-cart.ast-menu-cart-fill .ast-addon-cart-wrap ';
			let dynamicStyle = globalSelector + '{ border-top-left-radius :' + border['desktop']['top'] + border['desktop-unit']
					+ '; border-bottom-right-radius :' + border['desktop']['bottom'] + border['desktop-unit'] + '; border-bottom-left-radius :'
					+ border['desktop']['left'] + border['desktop-unit'] + '; border-top-right-radius :' + border['desktop']['right'] + border['desktop-unit'] + '; } ';

			dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['tablet']['top'] + border['tablet-unit']
					+ '; border-bottom-right-radius :' + border['tablet']['bottom'] + border['tablet-unit'] + '; border-bottom-left-radius :'
					+ border['tablet']['left'] + border['tablet-unit'] + '; border-top-right-radius :' + border['tablet']['right'] + border['tablet-unit'] + '; } } ';

			dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['mobile']['top'] + border['mobile-unit']
					+ '; border-bottom-right-radius :' + border['mobile']['bottom'] + border['mobile-unit'] + '; border-bottom-left-radius :'
					+ border['mobile']['left'] + border['mobile-unit'] + '; border-top-right-radius :' + border['mobile']['right'] + border['mobile-unit'] + '; } } ';

			astra_add_dynamic_css('woo-header-cart-icon-radius-fields', dynamicStyle);
		});
	});

	/**
	 * Transparent Header WOO-Cart color options - Customizer preview CSS.
	 */
	wp.customize('astra-settings[transparent-header-woo-cart-icon-color]', function (setting) {
		setting.bind(function (cart_icon_color) {
			wp.customize.preview.send('refresh');
		});
	});

	/**
     * Cart total label position.
     */
	 wp.customize('astra-settings[woo-header-cart-icon-total-label-position]', function (setting) {
		setting.bind(function (position) {
			var defaultCart = $(document).find('.cart-container');
			defaultPositionSelector = 'ast-cart-desktop-position-left ast-cart-desktop-position-right ast-cart-desktop-position-bottom ast-cart-mobile-position-left ast-cart-mobile-position-right ast-cart-mobile-position-bottom ast-cart-tablet-position-left ast-cart-tablet-position-right ast-cart-tablet-position-bottom';
			if($(selector).find('.ast-addon-cart-wrap').length){
				let iconCart = $(document).find('.ast-addon-cart-wrap');
				iconCart.removeClass(defaultPositionSelector);
				defaultCart.removeClass(defaultPositionSelector);
				iconCart.addClass('ast-cart-desktop-position-' + position.desktop);
				iconCart.addClass('ast-cart-mobile-position-' + position.mobile);
				iconCart.addClass('ast-cart-tablet-position-' + position.tablet);
			}
			else {
				defaultCart.removeClass(defaultPositionSelector);
				defaultCart.addClass('ast-cart-desktop-position-' + position.desktop);
				defaultCart.addClass('ast-cart-mobile-position-' + position.mobile);
				defaultCart.addClass('ast-cart-tablet-position-' + position.tablet);
			}

			var dynamicStyle = '.cart-container, .ast-addon-cart-wrap {display : flex; align-items : center; padding-top: 7px; padding-bottom: 5px;} ';
				dynamicStyle += '.astra-icon {line-height : 0.1;} ';
				if( position.desktop ){
					dynamicStyle += '@media (min-width: ' + tablet_break_point + 'px) {';
					dynamicStyle += cartPosition(position.desktop,'desktop');
					dynamicStyle += '} ';
				}
				if( position.tablet ){
					dynamicMobileStyle = cartPosition(position.tablet,'tablet');
					dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px ) and ( min-width: ' + mobile_break_point + 'px) {';
					dynamicStyle += dynamicMobileStyle;
					dynamicStyle += '} ';
				}
				if( position.mobile ){
					dynamictabletStyle = cartPosition(position.mobile,'mobile');
					dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) {';
					dynamicStyle += dynamictabletStyle;
					dynamicStyle += '} ';
				}

				function cartPosition(position,device) {
				switch(position){
					case "bottom":
					 	var dynamicStylePosition = '.ast-cart-'+ device +'-position-bottom { flex-direction : column;} ';
						dynamicStylePosition += '.ast-cart-'+ device +'-position-bottom .ast-woo-header-cart-info-wrap { order : 2; line-height : 1; margin-top  : 0.5em; } ';
						return dynamicStylePosition;
					case "right":
						dynamicStylePosition = '.ast-cart-'+ device +'-position-right .ast-woo-header-cart-info-wrap { order :  2; margin-left : 0.7em;} ';
						return dynamicStylePosition;
					case "left":
						dynamicStylePosition = '.ast-cart-'+ device +'-position-left .ast-woo-header-cart-info-wrap { margin-right : 0.5em;} ';
						return dynamicStylePosition;
					default:
					break;
				}
			}
			astra_add_dynamic_css( 'woo-header-cart-icon-total-label-position', dynamicStyle );
		});
	});

	// Advanced CSS Generation for cart padding and margin.
	astra_builder_advanced_css( 'section-header-woo-cart', '.woocommerce .ast-header-woo-cart .ast-site-header-cart .ast-addon-cart-wrap, .ast-header-woo-cart .ast-site-header-cart .ast-addon-cart-wrap' );

	// Advanced Visibility CSS Generation.
	astra_builder_visibility_css('section-header-woo-cart', '.ast-header-woo-cart');

	// Icon Size.
	wp.customize('astra-settings[header-woo-cart-icon-size]', function (value) {
		value.bind(function (size) {
			if (size.desktop != '' || size.tablet != '' || size.mobile != '') {
				var dynamicStyle = '';
				dynamicStyle += '.ast-icon-shopping-bag .ast-icon svg, .ast-icon-shopping-cart .ast-icon svg, .ast-icon-shopping-basket .ast-icon svg {';
				dynamicStyle += 'height: ' + size.desktop + 'px' + ';';
				dynamicStyle += 'width: ' + size.desktop + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += '.ast-icon-shopping-bag .ast-icon svg, .ast-icon-shopping-cart .ast-icon svg, .ast-icon-shopping-basket .ast-icon svg {';
				dynamicStyle += 'height: ' + size.tablet + 'px' + ';';
				dynamicStyle += 'width: ' + size.tablet + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += '.ast-icon-shopping-bag .ast-icon svg, .ast-icon-shopping-cart .ast-icon svg, .ast-icon-shopping-basket .ast-icon svg {';
				dynamicStyle += 'height: ' + size.mobile + 'px' + ';';
				dynamicStyle += 'width: ' + size.mobile + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '} ';


				dynamicStyle += '.ast-cart-menu-wrap, .ast-site-header-cart i.astra-icon {';
				dynamicStyle += 'font-size: ' + size.desktop + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px) {';
				dynamicStyle += '.ast-header-break-point.ast-hfb-header .ast-cart-menu-wrap, .ast-site-header-cart i.astra-icon {';
				dynamicStyle += 'font-size: ' + size.tablet + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '} ';

				dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) {';
				dynamicStyle += '.ast-header-break-point.ast-hfb-header .ast-cart-menu-wrap, .ast-site-header-cart i.astra-icon {';
				dynamicStyle += 'font-size:' + size.mobile + 'px' + ';';
				dynamicStyle += '} ';
				dynamicStyle += '} ';



				astra_add_dynamic_css('header-woo-cart-icon-size', dynamicStyle);
			}
		});
	});

	/**
	 * Cart Badge
	 */
	 wp.customize('astra-settings[woo-header-cart-badge-display]', function (setting) {
		setting.bind(function (badge) {
			if (!badge) {
				var dynamicStyle = '.astra-icon.astra-icon::after {  display:none; } ';
				dynamicStyle += '.ast-count-text {  display:none; } ';
			} else {
				var dynamicStyle = '.astra-icon.astra-icon::after {  display:block; } ';
				dynamicStyle += '.ast-count-text {  display:block; } ';
			}
			astra_add_dynamic_css('woo-header-cart-badge-display', dynamicStyle);
		});
	});

	/**
	 * Hide Cart Total Label
	 */
	wp.customize( 'astra-settings[woo-header-cart-total-label]', function( setting ) {
		setting.bind( function( toggle ) {
			$( document.body ).trigger( 'wc_fragment_refresh' );
		});
	});

})(jQuery);
