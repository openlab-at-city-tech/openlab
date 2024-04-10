export const adminLoginAction = async (page) => {
	await page.waitForLoadState( 'networkidle' );
	await page.locator('#user_login').fill('vlad');
	await page.locator('#user_pass').fill('magicasteaua');
	await page.click('#wp-submit');
}