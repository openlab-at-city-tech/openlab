module.exports = {
	'Test Something' (browser) {
		browser
			.url( 'http://openlabdev.org' )
			.waitForElementVisible( '#openlab-main-content' )
			.assert.containsText( '#open-lab-join', 'Join the OpenLab' )
			.end();
	}
};
