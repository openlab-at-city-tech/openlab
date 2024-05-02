// @ts-check
const { test, expect } = require('@playwright/test');

// Ajax add to cart on shop catalog
test('Ajax add to cart on shop catalog', async ({ page }) => {
    await page.goto('http://tests.local/?post_type=product');
    await page.locator( 'body.woocommerce-shop .site-main .products .product' ).first().hover();
    await page.locator( 'body.woocommerce-shop .site-main .products .product .add_to_cart_button' ).first().click();

    const responsePromise = page.waitForResponse('http://tests.local/?wc-ajax=add_to_cart');
    const response = await responsePromise;

    await expect( response.status() ).toBe( 200 );
});

// Ajax add to cart on search page (with results as grid)
test('Ajax add to cart on search page (with results as grid)', async ({ page }) => {
    await page.goto('http://tests.local/?s=a&post_type=product');
    await page.locator( 'body.search .site-main .products .product' ).first().hover();
    await page.locator( 'body.search .site-main .products .product .add_to_cart_button' ).first().click();

    const responsePromise = page.waitForResponse('http://tests.local/?wc-ajax=add_to_cart');
    const response = await responsePromise;

    await expect( response.status() ).toBe( 200 );
});