/* global jQuery, GFVersion, gform_advancedpostcreation_utils_strings */

/**
 * This file contains a collection of utility methods intended to be usable across the APC add-on.
 */

	GFVersion = gform_advancedpostcreation_utils_strings.GFVersion;

	window.GFAPCUtils = {
		/**
		 * Returns an option field by its name.
		 *
		 * @since 1.0
		 *
		 * @param {jQuery} $postDateSelect The post date setting dropdown.
		 * @param {String} option          The option name.
		 * @return {jQuery} The option field.
		 */
		'getPostDateOptionFieldByName': function ( $postDateSelect, option ) {

			if ( GFVersion >= '2.5'  ) {
				return $postDateSelect.parent().siblings( '.gform_advancedpostcreation_' + option );
			}

			return $postDateSelect.siblings( '.gform_advancedpostcreation_' + option );
		},

		/**
		 * Returns all option fields for the Post date setting.
		 *
		 * @since 1.0
		 *
		 * @param {jQuery} $postDateSelect The post date setting dropdown.
		 * @return {jQuery[]} The option field.
		 */
		'getPostDateOptionFields': function ( $postDateSelect ) {
			if ( GFVersion >= '2.5' ) {
				return $postDateSelect.parent().siblings( 'div[class^=gform_advancedpostcreation_]' );
			}
			return $postDateSelect.siblings(  'div[class^=gform_advancedpostcreation_]' );
		},

		/**
		 * Returns the name of a setting field accounting for the difference of prefixes between GF 2.5 and prior versions.
		 *
		 * @since 1.0
		 *
		 * @param {String} settingName The setting name without the prefix.
		 * @return {String} The full field setting name.
		 */
		'getSettingFieldName': function ( settingName ) {
			if ( GFVersion >= '2.5' ) {
					return '_gform_setting_' + settingName;
			}
			return '_gaddon_setting_' + settingName;
		}
	};
