// @ts-check
const { test, expect } = require('@playwright/test');

import { is_pro } from '../utils/pro';

//Test if the sidebar exists on various pages
test.describe('Footer tests', () => {

	//Copyright bar is visible
	test('Copyright bar is visible', async ({ page }) => {
		await page.goto('http://tests.local/');
		await expect(page.locator( '#colophon' )).toBeVisible(); 
	});

	//Footer widgets are visible
	test('Footer widgets are visible', async ({ page }) => {
		await page.goto('http://tests.local/');
		await expect(page.locator( '#sidebar-footer' )).toBeVisible(); 
	});

	//Four footer areas
	test('Four footer areas are displayed', async ({ page }) => {
		await page.goto('http://tests.local/');
		await expect(page.locator( '.sidebar-column' )).toHaveCount(4); 
	} );

	//Credits are visible and have text
	test('Credits are visible and have text', async ({ page }) => {
		await page.goto('http://tests.local/');
		await expect(page.locator( '.sydney-credits' )).toBeVisible();
		await expect(page.locator( '.sydney-credits' )).not.toBeEmpty();
	} );

	//Social profile is visible
	test('Social profile is visible', async ({ page }) => {
		await page.goto('http://tests.local/');
		await expect(page.locator( '.social-profile' )).toBeVisible();
	} );

	if ( is_pro ) {
		//Footer separator is visible
		test('Footer separator is visible', async ({ page }) => {
			await page.goto('http://tests.local/');
			await expect(page.locator( '.footer-separator' )).toBeVisible();
		} );

		//Pre-footer is visible
		test('Pre-footer separator is visible', async ({ page }) => {
			await page.goto('http://tests.local/');
			await expect(page.locator( '.footer-contact' )).toBeVisible();
		} );		
	}
});