<?php 
namespace ElementsKit_Lite\Modules\Header_Footer\Theme_Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * MyListing support for the header footer.
 */
class MyListing {


	/**
	 * Run all the Actions / Filters.
	 */
	function __construct( $template_ids ) {
		global $elementskit_template_ids;
		
		$elementskit_template_ids = $template_ids;
		include 'my-listing-functions.php';
	}
}
