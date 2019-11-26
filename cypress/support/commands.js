Cypress.Commands.add('login', () => {
	cy.request({
		url: '/wp-login.php',
		method: 'POST',
		form: true,
		body: {
			log: Cypress.env('wp_user'),
			pwd: Cypress.env('wp_pass'),
			rememberme: 'forever',
			testcookie: 1,
		},
	});
});

Cypress.Commands.add('logout', () => {
	cy.request({
		url: '/wp-login.php',
		method: 'POST',
		form: true,
		body: {},
	});
});

Cypress.Commands.add('keepAllCookies', () => {
	Cypress.Cookies.defaults({
		whitelist: () => true,
	});
	cy.getCookies().then(cookies =>
		cookies.forEach(cookie => Cypress.Cookies.preserveOnce(cookie.name))
	);
});

