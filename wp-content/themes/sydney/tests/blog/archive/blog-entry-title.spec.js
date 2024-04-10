// @ts-check
const { test, expect } = require('@playwright/test');

test.describe('Blog post titles', () => {

	test('Post titles are visible', async ({ page }) => {
		await page.goto('http://tests.local/my-blog-page/');
		await expect(page.locator( '.content-area article' ).first().locator('.entry-title')).toBeVisible();
	} );

	test('Post titles have correct font sizes', async ({ page }) => {

		await page.goto('http://tests.local/my-blog-page/');

		await expect(page.locator( '.content-area article' ).first().locator('.entry-title')).toHaveCSS( 'font-size', '32px' );

		await page.setViewportSize({ width: 768, height: 1024 });
		await expect(page.locator( '.content-area article' ).first().locator('.entry-title')).toHaveCSS( 'font-size', '28px' );

		await page.setViewportSize({ width: 375, height: 812 });
		await expect(page.locator( '.content-area article' ).first().locator('.entry-title')).toHaveCSS( 'font-size', '24px' );
	} );

	test('Post titles have the correct color', async ({ page }) => {
		await page.goto('http://tests.local/my-blog-page/');
		await expect(page.locator( '.content-area article' ).first().locator('.entry-title a')).toHaveCSS( 'color', 'rgb(0, 16, 46)' );
	} );

});