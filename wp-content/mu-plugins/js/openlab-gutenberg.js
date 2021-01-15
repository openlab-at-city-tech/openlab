wp.domReady( function() {
	var openLabFixedToolbar = localStorage.getItem('openLabFixedToolbar');
	var hasFixedToolbar = wp.data.select('core/edit-post').isFeatureActive('fixedToolbar');

	// Use "Top Toolbar" by default but allow users to change the setting.
	if ( ! hasFixedToolbar && ! openLabFixedToolbar ) {
		wp.data.dispatch('core/edit-post').toggleFeature('fixedToolbar');

		// Set flag that default value was set.
		localStorage.setItem('openLabFixedToolbar', 'yes');
	}
} );
