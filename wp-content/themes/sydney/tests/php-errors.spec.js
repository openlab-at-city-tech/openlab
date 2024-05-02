/**
 * PHP Errors e2e Tests
 * 
 */

const { test, expect } = require('@playwright/test');

test.describe.configure({ mode: 'serial' });

import { adminLoginAction } from './utils/login';

import { activatePlugin, deactivatePlugin } from './utils/plugins';

/**
 * Front end PHP tests
 */
const pages = [
	{ url: 'http://tests.local/', name: 'Homepage' },
	{ url: 'http://tests.local/category/travel/', name: 'Category' },
	{ url: 'http://tests.local/est-aut-sed-eaque-consequatur-rerum/', name: 'Single post' },
	{ url: 'http://tests.local/sample-page/', name: 'Single page' },
	{ url: 'http://tests.local/my-blog-page/', name: 'Blog page' },
	{ url: 'http://tests.local/shop/', name: 'Shop page' },
	{ url: 'http://tests.local/product/beanie/', name: 'Single product' },
	{ url: 'http://tests.local/cart/', name: 'Cart page' },
	{ url: 'http://tests.local/checkout/', name: 'Checkout page' },
	{ url: 'http://tests.local/my-account/', name: 'My account page' },
	{ url: 'http://tests.local/random404page/', name: '404 page' },
	{ url: 'http://tests.local/?s=hello', name: 'Search page' },
	{ url: 'http://tests.local/category/travel/', name: 'Category page' },
];

const testPHPErrors = async ({ page, url, name }) => {
	await page.goto(url);
	await expect(page.locator('html')).not.toContainText(/(Fatal error:|Warning:)/);
};

test.describe('Test for PHP errors on different pages', () => {
	pages.forEach(({ url, name }) => {

		test(`Test PHP errors for ${name}`, async ({ page }) => {
			await testPHPErrors({ page, url, name });
		});

		test(`Test PHP errors for ${name} without plugins`, async ({ page }) => {
			await page.goto('http://tests.local/wp-login.php');
			await adminLoginAction(page);
			await deactivatePlugin('elementor', page);
			await deactivatePlugin('woocommerce', page);
			await deactivatePlugin('sydney-toolbox', page);

			await testPHPErrors({ page, url, name });

			await activatePlugin('elementor', page);
			await activatePlugin('woocommerce', page);
			await activatePlugin('sydney-toolbox', page);
		} );
	});
});

/**
 * Back end PHP tests
 */
const adminPages = [
	{ url: 'http://tests.local/wp-admin/', name: 'Dashboard' },
	{ url: 'http://tests.local/wp-admin/post-new.php', name: 'New post' },
	{ url: 'http://tests.local/wp-admin/post-new.php?post_type=page', name: 'New page' },
	{ url: 'http://tests.local/wp-admin/edit.php', name: 'Posts' },
	{ url: 'http://tests.local/wp-admin/edit.php?post_type=page', name: 'Pages' },
	{ url: 'http://tests.local/wp-admin/customize.php', name: 'Customizer' },
];

const testAdminPHPErrors = async ({ page, url, name }) => {
	await page.goto(url);
	await expect(page.locator('html')).not.toContainText(/(Fatal error:|Warning:)/);
};

test.describe('Test for PHP errors on different admin pages', () => {
	adminPages.forEach(({ url, name }) => {

		test(`Test PHP errors for ${name}`, async ({ page }) => {
			await testAdminPHPErrors({ page, url, name });
		});

		test(`Test PHP errors for ${name} without plugins`, async ({ page }) => {
			await page.goto('http://tests.local/wp-login.php');
			await adminLoginAction(page);
			await deactivatePlugin('elementor', page);
			await deactivatePlugin('woocommerce', page);
			await deactivatePlugin('sydney-toolbox', page);

			await testAdminPHPErrors({ page, url, name });

			await activatePlugin('elementor', page);
			await activatePlugin('woocommerce', page);
			await activatePlugin('sydney-toolbox', page);
		} );
	});
} );

/**
 * Additional test: customizer iframe
 */
test('Test PHP errors for customizer iframe', async ({ page }) => {
	test.slow();
	await page.goto('http://tests.local/wp-admin/customize.php');
	await adminLoginAction( page );
	await page.waitForLoadState( 'networkidle' );
	await expect( page.frameLocator('iframe').first().locator( 'html' ) ).not.toContainText(/(Fatal error:|Warning:)/);
});