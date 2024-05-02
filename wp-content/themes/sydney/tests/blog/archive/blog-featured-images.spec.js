// @ts-check
const { test, expect } = require('@playwright/test');

import { setCustomizerSetting } from '../../utils/customizer';

test.describe('Blog featured image tests', () => {

	//Featured image is visible
	test('Featured image is visible', async ({ page }) => {
		await page.goto('http://tests.local/my-blog-page/');
		await expect(page.locator( '.content-area article' ).last().locator('.entry-thumb img')).toBeVisible();
	} );

	//Featured image is not visible
	test('Featured image is not visible', async ({ page }) => {

		setCustomizerSetting('index_feat_image', 0 );
		await page.goto('http://tests.local/my-blog-page/');
		await page.reload();

		await expect(page.locator( '.content-area article' ).last().locator('.entry-thumb img')).not.toBeVisible();

		setCustomizerSetting('index_feat_image', 1 );
	} );
});