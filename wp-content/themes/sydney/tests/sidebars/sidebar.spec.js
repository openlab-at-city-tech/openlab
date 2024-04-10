// @ts-check
const { test, expect } = require('@playwright/test');

import { is_pro } from '../utils/pro';
import { adminLoginAction } from '../utils/login';


//Test if the sidebar exists on various pages
test.describe('Sidebar tests', () => {

	//Blog sidebar is visible
	test('Blog sidebar is visible', async ({ page }) => {
		await page.goto('http://tests.local/my-blog-page/');
		await expect(page.locator( '#secondary' )).toBeVisible(); 
	});

	//Single post sidebar is visible
	test('Single post sidebar is visible', async ({ page }) => {
		await page.goto('http://tests.local/similique-quis-a-libero-enim-quod-corporis-3/');
		await expect(page.locator( '#secondary' )).toBeVisible(); 
	});

	//Category sidebar is visible
	test('Category sidebar is visible', async ({ page }) => {
		await page.goto('http://tests.local/category/travel/');
		await expect(page.locator( '#secondary' )).toBeVisible(); 
	});

	//Page sidebar is visible
	test('Page sidebar is visible', async ({ page }) => {
		await page.goto('http://tests.local/sample-page/');
		await expect(page.locator( '#secondary' )).toBeVisible(); 
	} );

	//Post template No sidebar
	test('Post template: No sidebar', async ({ page }) => {
		await page.goto('http://tests.local/post-template-no-sidebar/');
		await expect(page.locator( '#secondary' )).not.toBeVisible(); 
	} );

	//Post template No sidebar
	test('Page template: No sidebar', async ({ page }) => {
		await page.goto('http://tests.local/no-sidebar-page/');
		await expect(page.locator( '#secondary' )).not.toBeVisible(); 
	} );

	//Set all posts to no sidebar
	test('All posts to no sidebar', async ({ page }) => {
        // Increase the test timeout
        test.slow();
        
		//Go to a post
        await page.goto('http://tests.local/wp-admin/customize.php?url=http%3A%2F%2Ftests.local%2Fsimilique-quis-a-libero-enim-quod-corporis-3%2F');

		//Login
        await adminLoginAction( page );

		//Disable the sidebar
		await page.locator('#sidebar_single_post').check();

		await expect(page.frameLocator('iframe').first().locator( '#secondary' )).not.toBeVisible(); 
	} );

	//Set all pages to no sidebar
	test('All pages to no sidebar', async ({ page }) => {
        // Increase the test timeout
        test.slow();
        
		//Go to a post
        await page.goto('http://tests.local/wp-admin/customize.php?url=http%3A%2F%2Ftests.local%2Fsample-page%2F');

		//Login
        await adminLoginAction( page );

		//Disable the sidebar
		await page.locator('#sidebar_single_page').check();

		await expect(page.frameLocator('iframe').first().locator( '#secondary' )).not.toBeVisible(); 
	} );	

});