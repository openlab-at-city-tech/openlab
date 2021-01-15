wp.domReady( function() {
	var openLabFixedToolbar = localStorage.getItem( 'openLabFixedToolbar' );
	var hasFixedToolbar = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fixedToolbar' );
	var isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' );

	// Use "Top Toolbar" by default but allow users to change the setting.
	if ( ! hasFixedToolbar && ! openLabFixedToolbar ) {
		wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fixedToolbar' );

		// Set flag that default value was set.
		localStorage.setItem( 'openLabFixedToolbar', 'yes' );
	}

	// Disable default full-screen mode.
	if ( isFullscreenMode ) {
		wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' );
	}
} );
