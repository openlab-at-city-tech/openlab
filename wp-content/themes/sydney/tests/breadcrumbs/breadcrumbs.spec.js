// @ts-check
const { test, expect } = require('@playwright/test');

import { is_pro } from '../utils/pro';

//skip the test not pro
if ( !is_pro ) {
	test.skip();
}

//Breadcrumbs tests
test.describe('Breadcrumb tests', () => {
	//Breadcrumbs are visible on blog page
	test('Breadcrumbs are visible on blog page', async ({ page }) => {
		await page.goto('http://tests.local/my-blog-page/');
		await expect(page.locator( '.sydney-breadcrumb-trail' )).toBeVisible(); 
	});	

	//Breadcrumbs are visible on single post
	test('Breadcrumbs are visible on single post', async ({ page }) => {
		await page.goto('http://tests.local/hello-world/');
		await expect(page.locator( '.sydney-breadcrumb-trail' )).toBeVisible(); 
	} );

	//Breadcrumbs are visible on single page
	test('Breadcrumbs are visible on single page', async ({ page }) => {
		await page.goto('http://tests.local/sample-page/');
		await expect(page.locator( '.sydney-breadcrumb-trail' )).toBeVisible(); 
	} );

	//Breadcrumbs are visible on categories
	test('Breadcrumbs are visible on categories', async ({ page }) => {
		await page.goto('http://tests.local/category/travel/');
		await expect(page.locator( '.sydney-breadcrumb-trail' )).toBeVisible(); 
	} );

	//Breadcrumbs background color
	test('Breadcrumbs background color', async ({ page }) => {
		await page.goto('http://tests.local/category/travel/');
		await expect(page.locator( '.sydney-breadcrumb-trail' )).toHaveCSS( 'background-color', 'rgb(237, 237, 237)' ); 
	} );

	//Breadcrumbs text color
	test('Breadcrumbs text color', async ({ page }) => {
		await page.goto('http://tests.local/category/travel/');
		await expect(page.locator( '.sydney-breadcrumb-trail' )).toHaveCSS( 'color', 'rgb(49, 65, 214)' ); 
	} );

	//Breadcrumbs link color
	test('Breadcrumbs link color', async ({ page }) => {
		await page.goto('http://tests.local/category/travel/');
		await expect(page.locator( '.sydney-breadcrumb-trail a' ).first()).toHaveCSS( 'color', 'rgb(221, 51, 51)' ); 
	} );

	//Breadcrumbs alignment
	test('Breadcrumbs alignment', async ({ page }) => {
		await page.goto('http://tests.local/category/travel/');
		await expect(page.locator( '.sydney-breadcrumb-trail' )).toHaveCSS( 'text-align', 'center' ); 
	} );
});