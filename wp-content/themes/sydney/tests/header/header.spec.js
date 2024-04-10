// @ts-check
const { test, expect } = require('@playwright/test');

import { adminLoginAction } from '../utils/login';

// Front-End - Desktop tests
test.describe('Front-End — Desktop tests', () => {

	// Set the viewport
	test.use({ viewport: { width: 1920, height: 1080 } });

	//First level dropdowns
	test('Primary Menu - first level dropdowns are visible', async ({ page }) => {
			await page.goto('http://tests.local/');
			await page.locator( '.main-header .mainnav .menu > li.menu-item-has-children' ).first().hover();
			await expect(page.locator( '.main-header .mainnav .menu li.menu-item-has-children > .sub-menu > li' ).first().getByRole( 'link' ) ).toBeVisible();
	});

	//Second level dropdowns
	test('Primary Menu - second level dropdowns are visible', async ({ page }) => {
		await page.goto('http://tests.local/');
		await page.locator( '.main-header .mainnav .menu > li.menu-item-has-children' ).first().hover();
		await page.locator( '.main-header .mainnav .menu > li.menu-item-has-children > .sub-menu > li' ).hover();
		await expect(page.locator( '.main-header .mainnav .menu li.menu-item-has-children > .sub-menu > li > .sub-menu > li' ).last().getByRole( 'link' ) ).toBeVisible();
	});	

	//Header search toggle is working
	test('Header search toggle is working', async ({ page }) => {
			await page.goto('http://tests.local/');
			await page.locator( '#masthead .header-col .header-search' ).click();
			await expect(page.locator( '#masthead .header-search-form' ) ).toBeVisible();
	});

	// Mini cart appear when we mouse hover the mini cart
	test('Mini cart appears when we mouse hover the mini cart icon', async ({ page }) => {
			await page.goto('http://tests.local/');
			await page.locator( '#site-header-cart' ).hover();
			await expect(page.locator( '#site-header-cart .widget_shopping_cart' )).toBeVisible();
	});

	//Test one of the two row layouts
	test('Bottom row visible for header layout 4', async ({ page }) => {

        // Increase the test timeout
        test.slow();
        
        await page.goto('http://tests.local/wp-admin/customize.php');

        // Login to admin
        await adminLoginAction( page );

        // Wait for the page to load
        await page.waitForLoadState( 'networkidle' );

		//go to the header options
		await page.locator('#accordion-panel-sydney_panel_header').click();
		await page.locator('#accordion-section-sydney_section_main_header').click();

		//change to layout 4
		await page.locator('#input_header_layout_desktop label').filter({ hasText: 'Layout 4' }).locator('div').click();

		//run the test
		await page.waitForSelector( '#customize-preview iframe' ).then( async ( iframe ) => {
			const frame = await iframe.contentFrame();
			if ( frame ) {
				await expect(frame.locator( '.bottom-header-row' )).toBeVisible();
 			} else {
				throw new Error( 'Could not find the frame' );
			}
		} );

    });

	//Test the sticky header
	test('Sticky header is working', async ({ page }) => {
		await page.goto('http://tests.local/');

		//scroll down
		await page.evaluate(() => {
			window.scrollBy(0, 1000);
		} );

		//check if the sticky header is visible
		await expect(page.locator( '#masthead.sticky-header' )).toBeVisible();
	});	

	//Test the transparent header
	test('Transparent header is working', async ({ page }) => {
		await page.goto('http://tests.local/');

		await expect(page.locator( '#masthead' )).toHaveCSS( 'background-color', 'rgba(0, 0, 0, 0)' );
	} );

	//Test that the header is not transparent when we scroll down
	test('Transparent header should not be transparent when we scroll down', async ({ page }) => {
		await page.goto('http://tests.local/');

		//scroll down
		await page.evaluate(() => {
			window.scrollBy(0, 1000);
		} );

		await expect(page.locator( '#masthead' )).not.toHaveCSS( 'background-color', 'rgba(0, 0, 0, 0)' );
	} );

});

// Front-End - Mobile tests
test.describe('Front-End — Mobile tests', () => {
   
    // Set the viewport to mobile
    test.use({ viewport: { width: 600, height: 900 } });

    // Mobile offcanvas menu toggle is working
    test('Mobile offcanvas menu toggle is working', async ({ page }) => {        
        await page.goto('http://tests.local/');
        await page.locator( '.mobile-header .menu-toggle' ).click();
        await expect(page.locator( '.sydney-offcanvas-menu .menu > li' ).first().getByRole( 'link' ) ).toBeVisible();
    });

	// Mobile offcanvas menu dropdown toggle is working
	test('Mobile offcanvas menu dropdown toggle is working', async ({ page }) => {        
        await page.goto('http://tests.local/');
        await page.locator( '.mobile-header .menu-toggle' ).click();
		await page.locator( '.sydney-offcanvas-menu .menu > li.menu-item-has-children > .dropdown-symbol' ).click();
		await expect(page.locator( '.sydney-offcanvas-menu .menu > li.menu-item-has-children > .sub-menu > li:nth-child(1) > a' )).toBeVisible();
    });
});

