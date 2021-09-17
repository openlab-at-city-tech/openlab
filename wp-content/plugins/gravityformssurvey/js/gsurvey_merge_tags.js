/* global gform_survey_merge_tags_strings */

if ( window.gform ) {
	gform.addFilter( 'gform_merge_tags', 'gsurvey_add_merge_tags' );
}

/**
 * Add custom Survey merge tags to Gravity Forms
 *
 * @param mergeTags
 * @param elementId
 * @param hideAllFields
 * @param excludeFieldTypes
 * @param isPrepop
 * @param option
 */
function gsurvey_add_merge_tags( mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option ) {
	if ( isPrepop ) {
		return mergeTags;
	}

	jQuery.each(
		gform_survey_merge_tags_strings.merge_tags,
		function ( i, customMergeTag ) {
			mergeTags[ customMergeTag.group ].tags.push(
				{
					tag: customMergeTag.tag,
					label: customMergeTag.label
				}
			);
		}
	);

	return mergeTags;
}
