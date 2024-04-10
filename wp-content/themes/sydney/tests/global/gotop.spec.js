// @ts-check
const { test, expect } = require('@playwright/test');

import { is_pro } from '../utils/pro';

import { setCustomizerSetting } from '../utils/customizer';

//Test if the sidebar exists on various pages
test.describe('Go to top tests', () => {

	//Go to top button
	test('Go to top button is visible', async ({ page }) => {
		await page.goto('http://tests.local/');

		//scroll down
		await page.evaluate(() => {
			window.scrollTo(0, 1000);
		} );

		//check if the button is visible and has the correct position
		await expect(page.locator( '.go-top' )).toBeVisible();
	});

	//Check if the button works
	test('Go to top button works', async ({ page }) => {
		await page.goto('http://tests.local/');

		//scroll down
		await page.evaluate(() => {
			window.scrollTo(0, 1000);
		} );

		await page.click( '.go-top' );

		//wait to go up
		await page.waitForTimeout( 1000 );

		//get scroll position
		const scroll_position = await page.evaluate(() => {
			return window.scrollY;
		} );

		//check if the scroll position is 0
		await expect( scroll_position ).toBe( 0 );
	} );

	//Button background color
	test('Button background color', async ({ page }) => {
		await page.goto('http://tests.local/');

		//scroll down
		await page.evaluate(() => {
			window.scrollTo(0, 1000);
		} );

		await expect(page.locator( '.go-top' )).toHaveCSS( 'background-color', 'rgb(255, 208, 10)' );
	} );

	//Button color
	test('Button color', async ({ page }) => {
		await page.goto('http://tests.local/');

		//scroll down
		await page.evaluate(() => {
			window.scrollTo(0, 1000);
		} );

		await expect(page.locator( '.go-top' )).toHaveCSS( 'color', 'rgb(255, 255, 255)' );
	} );

	//Test icon + text mode
	test('Icon + text mode', async ({ page }) => {
	
		setCustomizerSetting('scrolltop_type', 'text');

		await page.goto('http://tests.local/');
		await page.reload();

		await expect(page.locator( '.go-top' )).toHaveText( 'Back to top' );

		setCustomizerSetting('scrolltop_type', 'icon');
	} );

	//Test position
	test('Position', async ({ page }) => {
			
		setCustomizerSetting('scrolltop_position', 'left');

		await page.goto('http://tests.local/');
		await page.reload();

		await expect(page.locator( '.go-top' )).toHaveClass( /position-left/ );

		setCustomizerSetting('scrolltop_position', 'right');
	} );

	//Test device visibility
	test('Device visibility', async ({ page }) => {
			
		setCustomizerSetting('scrolltop_visibility', 'desktop-only');

		await page.goto('http://tests.local/');
		await page.reload();

		await expect(page.locator( '.go-top' )).toHaveClass( /visibility-desktop-only/ );

		setCustomizerSetting('scrolltop_visibility', 'all');
	} );
});