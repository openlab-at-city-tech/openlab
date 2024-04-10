// @ts-check
const { test, expect } = require('@playwright/test');

test.describe.configure({ mode: 'serial' });

import { adminLoginAction } from '../utils/login';

import { setCustomizerSetting } from '../utils/customizer';

// Front-End - Desktop tests
test.describe('Hero slider tests', () => {

	// Set the viewport
	test.use({ viewport: { width: 1920, height: 1080 } });

	//after all
	test.afterAll( async ({ page }) => {
		setCustomizerSetting('front_header_type', 'nothing');
		setCustomizerSetting('site_header_type', 'nothing');
	});

	//Hero slider is visible
	test('Hero slider is visible on home', async ({ page }) => {

		setCustomizerSetting('front_header_type', 'slider');

		await page.goto('http://tests.local/');
		await page.reload();
	
		await expect(page.locator( '#slideshow' )).toBeVisible();
		await expect(page.locator( '.maintitle' )).toHaveCount(2);
		await expect(page.locator( '.subtitle' )).toHaveCount(2);
		await expect(page.locator( '.roll-button.button-slider' )).toHaveCount(2);

		setCustomizerSetting('front_header_type', 'nothing');
    });

	//Hero slider is visible on other pages
	test('Hero slider is visible on other pages', async ({ page }) => {
        
		setCustomizerSetting('site_header_type', 'slider');

		await page.goto('http://tests.local/sample-page/');
		await page.reload();
		
		await expect(page.locator( '#slideshow' )).toBeVisible();
		await expect(page.locator( '.maintitle' )).toHaveCount(2);
		await expect(page.locator( '.subtitle' )).toHaveCount(2);
		await expect(page.locator( '.roll-button.button-slider' )).toHaveCount(2);
		
		setCustomizerSetting('site_header_type', 'nothing');
    });

	//Header image is visible
	test('Header image is visible on home', async ({ page }) => {

		setCustomizerSetting('front_header_type', 'image');

		await page.goto('http://tests.local/');
		await page.reload();

		await expect(page.locator( '.header-image' )).toBeVisible();

		setCustomizerSetting('front_header_type', 'nothing');

    });	

	//Header image is visible
	test('Header image is visible on on other pages', async ({ page }) => {

		setCustomizerSetting('site_header_type', 'image');

		await page.goto('http://tests.local/sample-page/');
		await page.reload();

		await expect(page.locator( '.header-image' )).toBeVisible();

		setCustomizerSetting('site_header_type', 'nothing');
    });
});