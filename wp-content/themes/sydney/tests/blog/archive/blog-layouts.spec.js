// @ts-check
const { test, expect } = require('@playwright/test');

test.describe.configure({ mode: 'serial' });

import { adminLoginAction } from '../../utils/login';
import { setCustomizerSetting } from '../../utils/customizer';

test.describe('Blog layout tests', () => {

	//after all
	test.afterAll( async ({ page }) => {
		setCustomizerSetting('blog_layout', 'layout3');
	});

	const blog_layouts = [
		'layout1',
		'layout2',
		'layout3',
		'layout4',
		'layout5',
		'layout6'
	];

	//Check the correct layout class
	blog_layouts.forEach( (layout) => {
		test(`Blog layout is ${layout}`, async ({ page }) => {

			setCustomizerSetting('blog_layout', layout);

			await page.goto('http://tests.local/my-blog-page/');
			await page.reload();

			await expect(page.locator( '.content-area' )).toHaveClass( `content-area archive-wrapper sidebar-right ${layout} col-md-9` );

			//perform layout specific checks
			switch (layout) {
				case 'layout1':
				case 'layout2':
					await expect(page.locator( '.content-area article' ).last()).toHaveClass( /col-md-12/ );
					break;
				case 'layout3':
				case 'layout5':
					await expect(page.locator( '.content-area article' ).last()).toHaveClass( /col-lg-6 col-md-6/ );
					break;
				case 'layout4':
				case 'layout6':	
					await expect(page.locator( '.content-area article' ).last()).toHaveClass( /col-md-12/ );
					await expect(page.locator( '.content-inner' ).first()).toHaveCSS( 'display', 'flex' );
					break;
				case 'layout6':
					await expect(page.locator( '.layout6 article:nth-of-type(even) .list-image' ).first()).toHaveCSS( 'order', '1' );
					break;
			}

			setCustomizerSetting('blog_layout', 'layout3');
		});
	} );
});