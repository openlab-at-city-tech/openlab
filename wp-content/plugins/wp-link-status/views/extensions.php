<?php

// Load views class
require_once dirname(__FILE__).'/views.php';

/**
 * Extensions class
 *
 * @package WP Link Status
 * @subpackage Views
 */
class WPLNST_Views_Extensions extends WPLNST_Views {



	/**
	 * Show scan edit form
	 */
	public static function view($args) {

		// Vars
		extract($args);

		// Plugin images
		$base_url = plugins_url('assets/images/extensions/', WPLNST_FILE);

		?><div id="wplnst-extensions">


			<div class="wplnst-extensions-section">

				<p><a href="http://seedplugins.com/wp-link-status/" target="_blank"><img src="<?php echo $base_url.'pro-banner.jpg'; ?>" width="590" height="300" border="0" /></a></p>

				<p style="width: 590px; text-align: center;"><a href="http://seedplugins.com/wp-link-status/" target="_blank" class="button-primary">Get WP Link Status Pro version</a></p>

			</div>


			<div class="wplnst-extensions-section">

				<h3>Advanced filters</h3>

				<p><img src="<?php echo $base_url.'wpls-web-crawler-results-advanced.png'; ?>" width="800" height="120" border="0" /></p>

			</div>


			<div class="wplnst-extensions-section">

				<h3>Inline result editing</h3>

				<p><img src="<?php echo $base_url.'wpls-web-crawler-results-actions.png'; ?>" width="800" height="100" border="0" /></p>

				<p><img src="<?php echo $base_url.'wpls-web-crawler-edit.png'; ?>" width="800" height="300" border="0" /></p>

			</div>


			<div class="wplnst-extensions-section">

				<h3>Extra URL Tools</h3>

				<p><img src="<?php echo $base_url.'wpls-web-crawler-url-tools.png'; ?>" width="800" height="363" border="0" /></p>

				<p><img src="<?php echo $base_url.'wpls-web-crawler-url-tools-results.png'; ?>" width="800" height="427" border="0" /></p>

			</div>

			<p style="width: 800px; text-align: center;"><a href="http://seedplugins.com/wp-link-status/" target="_blank" class="button-primary">Get WP Link Status Pro version</a></p>


		</div><?php
	}



}