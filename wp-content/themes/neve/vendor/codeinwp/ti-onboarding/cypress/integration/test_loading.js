let homeUrl = 'http://localhost:8056',
		timeout = 50000,
		routes = {
			aboutPage: homeUrl + '/wp-admin/themes.php?page=neve-welcome',
			init: homeUrl +
					'/index.php?rest_route=/ti-sites-lib/v1/initialize_sites_library',
			installPlugins: homeUrl +
					'/index.php?rest_route=/ti-sites-lib/v1/install_plugins',
			importContent: homeUrl +
					'/index.php?rest_route=/ti-sites-lib/v1/import_content',
			importThemeMods: homeUrl +
					'/index.php?rest_route=/ti-sites-lib/v1/import_theme_mods',
			importWidgets: homeUrl +
					'/index.php?rest_route=/ti-sites-lib/v1/import_widgets'
		};

describe( 'Test Initialization and import', function() {
	it( 'successfully loads', function() {
		wpLogin( cy );
		cy.get( '.nv-notice-wrapper' ).should( 'exist' ).and( 'be.visible' );

		aliasRestRoutes( cy );

		cy.visit( routes.aboutPage, {
			timeout
		} );
		cy.wait( 500 );
		cy.get( '[data-tab-id="sites_library"]' ).click();

		cy.wait( '@getSites' ).then( (req) => {
			expect( req.response.body.success ).to.be.true;
			expect( req.status ).to.equal( 200 );
		} );

		cy.get(
				'#templates-in-elementor .templates-wrapper > div:first-child .footer .button-primary' ).
				click();
		cy.get( '.modal__item' ).should( 'exist' ).and( 'be.visible' );
		cy.get( '.modal__item' ).children().should( 'have.length', 3 );
		cy.get( '.modal__item' ).find( 'button' ).contains( 'Import' ).click();
		cy.wait( '@plugins' ).then( (req) => {
			expect( req.response.body.success ).to.be.true;
			expect( req.status ).to.equal( 200 );
		} );

		cy.wait( '@content' ).then( (req) => {
			expect( req.response.body.success ).to.be.true;
			expect( req.status ).to.equal( 200 );
		} );

		cy.wait( '@themeMods' ).then( (req) => {
			expect( req.response.body.success ).to.be.true;
			expect( req.status ).to.equal( 200 );
		} );

		cy.wait( '@widgets' ).then( (req) => {
			expect( req.response.body.success ).to.be.true;
			expect( req.status ).to.equal( 200 );
		} );

		cy.get( '.modal__footer' ).should( 'exist' ).and( 'be.visible' );
		cy.get( '.modal__footer' ).children().should( 'have.length', 3 );
		cy.get( '.modal__footer' ).
				find( 'a' ).
				contains( 'View Website' ).
				click();

		cy.wait( 500 );
		cy.get( '.neve-main' ).should( 'exist' ).and( 'be.visible' );
	} );
} );

/**
 * Log into wordpress
 *
 * @param cy
 */
function wpLogin(cy) {
	cy.visit( homeUrl + '/wp-admin', {
		timeout
	} );
	cy.wait( 500 );
	cy.get( '#user_login' ).type( 'admin' );
	cy.get( '#user_pass' ).type( 'admin' );
	cy.get( '#wp-submit' ).click();
}

/**
 * Alias the rest routes.
 *
 * @param cy
 */
function aliasRestRoutes(cy) {
	cy.server().route( 'GET', routes.init ).as( 'getSites' );
	cy.server().route( 'POST', routes.installPlugins ).as( 'plugins' );
	cy.server().route( 'POST', routes.importContent ).as( 'content' );
	cy.server().route( 'POST', routes.importThemeMods ).as( 'themeMods' );
	cy.server().route( 'POST', routes.importWidgets ).as( 'widgets' );
}
