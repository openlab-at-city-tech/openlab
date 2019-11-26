describe( 'Portfolios', () => {
	const portfolio = {
		name: 'e2e Testing Portfolio',
		desc: 'This Portfolio was created via Cypress',
		slug: 'e2e-testing-portfolio',
		site: 'https://buddypress.org'
	};

	before( () => {
		cy.login();
		cy.keepAllCookies();
	});

	context( 'Create a Portfolio', () => {
		before( () => {
			cy.visit('/groups/create/step/group-details/?type=portfolio');

			// Aliases
			cy.get('#create-group-form').as('groupForm');
			cy.get('#group-name').as('groupName');
			cy.get("#group-desc").as('groupDesc');
			cy.get('#group-creation-create').as('createGroup');
		});

		it( 'checks required fields', () => {
			cy.get('@groupName').should('have.attr', 'required');
			cy.get('@groupDesc').should('have.attr', 'required');

			cy.get('@groupDesc').type(portfolio.desc);
			cy.get('input[name="new_or_old"]').check('external');
			cy.get('#external-site-url').type(portfolio.site);

			// Trigger error for School(s).
			cy.get('@createGroup').click();
			cy.get('.error p')
				.should('contain', 'You must provide a school and department.');
		});

		// @todo it matches portfolio site URL pattern.

		/**
		 * Skip this test for now. Find Feed AJAX request takes too long.
		 * Similar tests are anti-pattern based on Cypress docs.
		 *
		 * @link https://docs.cypress.io/guides/core-concepts/conditional-testing.html.
		 */
		it.skip( 'checks external site feed', () => {
			cy.get('input[name="new_or_old"]').check('external');
			cy.get('#external-site-url').type(portfolio.site);

			cy.get('#find-feeds').click();
			cy.wait(2000);

			cy.get('#external-feed-results').contains('label', 'Posts:');
		});

		it( 'allows to finish step one', () => {
			cy.get('#group-name').clear().type(portfolio.name);
			cy.get('#group-desc').type(portfolio.desc);

			// Check Schools.
			cy.get('input[name="schools[]"]').check('tech');
			cy.get('input[name="departments[]"]').check('communication-design');

			// Use external site for now.
			// Have issue with local/new site validation.
			cy.get('input[name="new_or_old"]').check('external');
			cy.get('#external-site-url').clear().type(portfolio.site);

			cy.get('#group-creation-create').click();
		});

		it( 'allows to update privacy settings', () => {
			cy.get('input[name="group-status"]').check('private');

			// External Sites doesn't have this option.
			// cy.get('input[name="blog_public"]').check('-1');

			cy.get('#group-creation-next').click();
		});

		it( 'allows to skip avatar upload', () => {
			cy.get('#group-creation-next').click();
		});

		it( 'should redirect to portfolio', () => {
			cy.get('#group-creation-finish').click();

			cy.get('#openlab-main-content')
				.should('contain', portfolio.name);
		});
	});

	context( 'Delete Portfolio', () => {
		it( 'can accesss settings', () => {
			cy.get('#item-buttons').contains('Settings').click();
		});

		it( 'can delete portfolio', () => {
			cy.get('.nav-inline')
				.contains('Delete Portfolio')
				.click();

			cy.get('#delete-group-button').as('delete');

			cy.get('#message > p').should('contain', 'WARNING:');
			cy.get('#delete-group-understand').check('1');

			cy.get('@delete').should('not.be.disabled');
			cy.get('@delete').click();
		});
	});
});
