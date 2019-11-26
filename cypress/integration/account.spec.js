import faker from 'faker';

describe( 'Account', () => {
	const account = {
		firstName: faker.name.firstName(),
		lastName: faker.name.lastName(),
		email: 'tests@mail.citytech.cuny.edu',
		password: faker.internet.password(),
		type: 'Student'
	};

	account.userName = `${account.firstName}${account.lastName}`.toLowerCase(),

	before( () => {
		cy.logout();
		cy.visit('/register/');
	});

	context( 'Sign Up', () => {
		// Aliases.
		beforeEach( () => {
			cy.get('#signup_username').as('userName');
			cy.get('#field_241').as('firstName');
			cy.get('#field_3').as('lastName');
			cy.get('#signup_email').as('email');
			cy.get('#signup_email_confirm').as('emailConfirm');
			cy.get('#signup_password').as('pass');
			cy.get('#signup_password_confirm').as('passConfirm');
			cy.get('#field_1').as('displayName');
			cy.get('#field_7').as('type');
			cy.get('#signup_submit').as('submit');
		});

		it( 'has required fields', () => {
			// Currenly we have 9 required fields
			cy.get('[data-parsley-required=""]').should('have.length', 9);
		});

		it( 'should display correct error message', () => {
			cy.get('@userName').focus();
			cy.get('@firstName').focus();

			cy.get('#signup_username_error li')
				.should('contain', 'Username is required.');
		});

		it( 'allows to create account', () => {
			cy.get('@userName').type(account.userName);
			cy.get('@firstName').type(account.firstName);
			cy.get('@lastName').type(account.lastName);
			cy.get('@email').clear().type(account.email);
			cy.get('@emailConfirm').type(account.email);
			cy.get('@pass').type(account.password);
			cy.get('@passConfirm').type(account.password);
			cy.get('@displayName').type(`${account.firstName} ${account.lastName}`);
			cy.get('@type').select(account.type);

			cy.wait(2000);

			cy.get('select[name="departments-dropdown"]')
				.select('undecided');

			cy.get('@submit')
				.should('contain', 'Complete Sign Up')
				.click();

			cy.get('#signup_form').should('contain', 'Sign Up Complete!');
		});
	});

	context( 'My OpenLab', () => {
		before( () => {
			cy.visit('/');
		});

		it( 'allows user to login', () => {
			cy.get('#sidebar-user-login').type(account.userName);
			cy.get('#sidebar-user-pass').type(account.password);
			cy.get('#sidebar-wp-submit').click();

			cy.get('#open-lab-login')
				.should('contain', `${account.firstName} ${account.lastName}`);
		});

		it( 'displays account creation activity', () => {
			cy.visit(`/members/${account.userName}`);

			cy.get('#activity-stream .activity-content')
				.should('contain', 'registered member')
		});

		it( 'can delete account', () => {
			cy.get('#item-buttons').contains('My Settings').click();
			cy.get('.nav-inline').contains('Delete Account').click();

			cy.get('#delete-account-button').as('delete');

			cy.get('.bp-template-notice > p').should('contain', 'WARNING:');
			cy.get('#delete-account-understand').check('1');

			cy.get('@delete').should('not.be.disabled');
			cy.get('@delete').click();
		});
	});
});
