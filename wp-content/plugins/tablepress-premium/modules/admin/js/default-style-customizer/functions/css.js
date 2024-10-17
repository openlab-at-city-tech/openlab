/**
 * JavaScript code for the "Default Style Customizer Screen" custom CSS code generation and export.
 *
 * @package TablePress
 * @subpackage Default Style Customizer Screen
 * @author Tobias Bäthge
 * @since 2.2.0
 */

/* globals tp */

/**
 * Internal dependencies.
 */
import { $ } from '../../../../../admin/js/common/functions';
import styleVariations from '../data/variations';

/**
 * Generates CSS code from the custom CSS properties and their values.
 *
 * @param {string} cssCode CSS code to parse.
 * @return {Object} Custom CSS properties and values.
 */
export const parseCssCode = function( cssCode ) {
	let cssProperties = {};

	const defaultCssPropertyValues = { ...styleVariations.default.style };

	const cssRulesStyle = document.createElement( 'style' );
	const doc = document.implementation.createHTMLDocument( '' );
	doc.body.appendChild( cssRulesStyle );
	cssRulesStyle.textContent = cssCode;

	[ ...cssRulesStyle.sheet.cssRules ].forEach( ( rule ) => {
		if ( '.tablepress' === rule.selectorText ) {
			Object.keys( defaultCssPropertyValues ).forEach( ( cssProperty ) => {
				const currentValue = rule.style.getPropertyValue( cssProperty ).trim();
				if ( '' !== currentValue ) {
					cssProperties[ cssProperty ] = currentValue;
				}
			} );
		}
	} );

	// Merge found custom CSS properties into default CSS properties.
	cssProperties = { ...defaultCssPropertyValues, ...cssProperties };

	// Reset entries that would create a circle reference to their default values.
	Object.entries( cssProperties ).forEach( ( [ cssProperty, cssPropertyValue] ) => {
		while ( cssPropertyValue.startsWith( 'var(' ) ) {
			const referencedCssProperty = ( ( cssPropertyValue.match( /var\(([-a-z]+)\)/ ) )?.[1] ).trim();
			if ( referencedCssProperty === cssProperty ) {
				cssProperties[ cssProperty ] = defaultCssPropertyValues[ cssProperty ];
				break;
			}
			cssPropertyValue = cssProperties[ referencedCssProperty ];
		}
	} );

	return cssProperties;
};

/**
 * Generates CSS code from the custom CSS properties and their values.
 *
 * @param {Object} cssProperties Custom CSS properties and values.
 * @return {string} CSS code.
 */
export const generateCssCode = ( cssProperties ) => {
	let cssCode = '';
	Object.entries( cssProperties ).forEach( ( [ cssProperty, currentValue ] ) => {
		if ( currentValue !== styleVariations.default.style[ cssProperty ] ) {
			cssCode += `\t${ cssProperty }: ${ currentValue };\n`;
		}
  	} );

  	if ( '' !== cssCode ) {
  		cssCode = `.tablepress {\n${ cssCode }}`;
  	}

	return cssCode;
};

/**
 * Imports custom CSS properties from the "Custom CSS" textarea on the "Plugin Options" screen.
 *
 * @return {Object} Custom CSS properties and values.
 */
export const importFromCustomCss = () => {
	let customCss = tp.CM_custom_css.getValue();
	customCss = customCss.match( /\/\* TABLEPRESS DEFAULT STYLING \*\/\n\.tablepress {.*?}/gs )?.[0] || '';

	return parseCssCode( customCss );
};

/**
 * Exports CSS code to the "Custom CSS" textarea on the "Plugin Options" screen.
 *
 * @param {Object} cssProperties Custom CSS properties and values.
 */
export const exportToCustomCss = ( cssProperties ) => {
	const defaultStyleCss = generateCssCode( cssProperties );

	let customCss = tp.CM_custom_css.getValue();
	customCss = customCss.replace( /\/\* TABLEPRESS DEFAULT STYLING \*\/.*?}/gs, '' ).trim();

	const cssIntro = ( '' !== defaultStyleCss ) ? '/* TABLEPRESS DEFAULT STYLING */\n' : '';
	const cssDelimiter = ( '' !== customCss && '' !== defaultStyleCss ) ? '\n\n' : '';

	customCss = `${ cssIntro }${ defaultStyleCss }${ cssDelimiter }${ customCss }`;

	tp.CM_custom_css.setValue( customCss );
};

/**
 * Saves the "Custom CSS" to the server, by submitting the form.
 */
export const saveCustomCss = () => {
	$( '#tablepress-page-form' ).submit();
};
