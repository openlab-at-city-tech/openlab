// @ts-check
const { test, expect } = require('@playwright/test');

import { is_pro } from '../utils/pro';

//Test if the sidebar exists on various pages
test.describe('Button tests', () => {

	//Button background
	test('Button background color (both GB and input)', async ({ page }) => {
		await page.goto('http://tests.local/buttons-test/');
		await expect(page.locator( '.wp-block-button:not(.is-style-outline)' ).first().locator( '.wp-block-button__link' )).toHaveCSS( 'background-color', 'rgb(255, 208, 10)' ); 
		await expect(page.locator( '.wp-block-search__button' ).first()).toHaveCSS( 'background-color', 'rgb(255, 208, 10)' ); 
	});

	//Search Button background hover
	test('Search Button background hover', async ({ page }) => {
		await page.goto('http://tests.local/buttons-test/');
		await page.locator( '.wp-block-search__button' ).first().hover();
		await expect(page.locator( '.wp-block-search__button' ).first()).toHaveCSS( 'background-color', 'rgba(255, 208, 10, 0.78)' );
	});

	//Button text color
	test('Button text color (both GB and input)', async ({ page }) => {
		await page.goto('http://tests.local/buttons-test/');
		await expect(page.locator( '.wp-block-button:not(.is-style-outline)' ).first().locator( '.wp-block-button__link' )).toHaveCSS( 'color', 'rgb(0, 16, 46)' ); 
		await expect(page.locator( '.wp-block-search__button' ).first()).toHaveCSS( 'color', 'rgb(0, 16, 46)' ); 
	} );

	//Search Button text color hover
	test('Search Button text color hover', async ({ page }) => {
		await page.goto('http://tests.local/buttons-test/');
		await page.locator( '.wp-block-search__button' ).first().hover();
		await expect(page.locator( '.wp-block-search__button' ).first()).toHaveCSS( 'color', 'rgb(255, 0, 0)' );
	});

	//Button border radius
	test('Button border radius (both GB and input)', async ({ page }) => {
		await page.goto('http://tests.local/buttons-test/');
		await expect(page.locator( '.wp-block-button:not(.is-style-outline)' ).first().locator( '.wp-block-button__link' )).toHaveCSS( 'border-radius', '15px' ); 
		await expect(page.locator( '.wp-block-search__button' ).first()).toHaveCSS( 'border-radius', '15px' ); 
	} );

	//Button padding
	test('Button padding (both GB and input)', async ({ page }) => {
		await page.goto('http://tests.local/buttons-test/');
		await expect(page.locator( '.wp-block-button:not(.is-style-outline)' ).first().locator( '.wp-block-button__link' )).toHaveCSS( 'padding', '12px 35px' ); 
		await expect(page.locator( '.wp-block-search__button' ).first()).toHaveCSS( 'padding', '12px 35px' ); 
	} );

	//Outline button background
	test('Outline button background', async ({ page }) => {
		await page.goto('http://tests.local/buttons-test/');
		await expect(page.locator( '.wp-block-button.is-style-outline' ).first()).toHaveCSS( 'background-color', 'rgba(0, 0, 0, 0)' ); 
	} );	

	//Font size
	test('Button Font size', async ({ page }) => {
		await page.goto('http://tests.local/buttons-test/');
		await expect(page.locator( '.wp-block-button' ).first().locator( '.wp-block-button__link' )).toHaveCSS( 'font-size', '15px' ); 
		await expect(page.locator( '.wp-block-search__button' ).first()).toHaveCSS( 'font-size', '15px' ); 
	} );

	//Text transform
	test('Button text transform', async ({ page }) => {
		await page.goto('http://tests.local/buttons-test/');
		await expect(page.locator( '.wp-block-button' ).first().locator( '.wp-block-button__link' )).toHaveCSS( 'text-transform', 'uppercase' ); 
		await expect(page.locator( '.wp-block-search__button' ).first()).toHaveCSS( 'text-transform', 'uppercase' ); 
	} );

});