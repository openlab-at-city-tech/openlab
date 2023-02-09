(function($){
	$(document).ready( function() {
		setTimeout( function() {
			const neveDashboardEl = document.getElementById( 'neve-dashboard' )
			if ( neveDashboardEl ) {
				const neveDashboardLis = neveDashboardEl.querySelectorAll( '.navigation li' );
				for ( const dbLi of neveDashboardLis ) {
					const starterSitesLink = dbLi.querySelector( '.starter-sites' )
					if ( starterSitesLink ) {
						dbLi.remove()
					}
				}
			}
		}, 1000 )
	} );
})(jQuery)
