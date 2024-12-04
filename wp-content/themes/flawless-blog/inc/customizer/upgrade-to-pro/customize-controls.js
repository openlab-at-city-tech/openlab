( function( api ) {

	// Extends our custom "one page" section.
	api.sectionConstructor['flawless-blog'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );
