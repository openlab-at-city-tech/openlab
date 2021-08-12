function gdocToggleAttrFields( changed, collection, shortcode ) {
	var isGoogleDoc, widthField, heightField, downloadlinkField, typeField, seamlessField, sizeField;

	widthField = attributeByName( 'width' );
	heightField = attributeByName( 'height' );
	downloadlinkField = attributeByName( 'downloadlink' );
	typeField = attributeByName( 'type' );
	seamlessField = attributeByName( 'seamless' );
	sizeField = attributeByName( 'size' );

	// hide declared fields by default
	widthField.$el.hide();
	heightField.$el.hide();
	downloadlinkField.$el.hide();
	typeField.$el.hide();
	seamlessField.$el.hide();
	sizeField.$el.hide();

	if ( typeof changed.value != 'undefined' && ( changed.value.indexOf( '://docs.google.com' ) > -1 || changed.value.indexOf( '://drive.google.com' ) > -1 ) ) {
		widthField.$el.show();
		heightField.$el.show();
		downloadlinkField.$el.show();

		isGoogleDoc = changed.value.indexOf( '/document/' ) > -1 || changed.value.indexOf( '/presentation/' ) > -1 || changed.value.indexOf( '/forms/' ) > -1 || changed.value.indexOf( '/spreadsheets/' ) > -1;

		if ( isGoogleDoc ) {
			typeField.$el.hide();
		} else {
			typeField.$el.show();
		}

		if ( changed.value.indexOf( '/document/' ) > -1 && changed.value.indexOf( '/pub' ) > -1 ) {
			seamlessField.$el.show();
		} else {
			seamlessField.$el.hide();
		}

		if ( changed.value.indexOf( '/presentation/' ) > -1 ) {
			sizeField.$el.show();
		} else {
			sizeField.$el.hide();
		}
	}

	function attributeByName(name) {
		return _.find(
			collection,
			function( viewModel ) {
				return name === viewModel.model.get('attr');
			}
		);
	}
}
wp.shortcake.hooks.addAction( 'gdoc.link', gdocToggleAttrFields );