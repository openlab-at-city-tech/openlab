export const refreshAdminMenu = async () => {
	try {
		const response = await fetch( window.location.href, { credentials: 'same-origin' } );
		const html = await response.text();
		const freshMenu = new DOMParser()
			.parseFromString( html, 'text/html' )
			.getElementById( 'menu-posts-dlm_download' );
		const currentMenu = document.getElementById( 'menu-posts-dlm_download' );

		if ( freshMenu && currentMenu ) {
			currentMenu.outerHTML = freshMenu.outerHTML;
		}
	} catch ( e ) {
		// Sidebar stays stale until the next navigation — not worth surfacing.
	}
};
