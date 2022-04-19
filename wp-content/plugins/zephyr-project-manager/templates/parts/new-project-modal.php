<?php 
	/**
	* Template for displaying the New Project modal
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Zephyr;
	use Inc\Core\Tasks;
	use Inc\Core\Members;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;
	use Inc\Base\BaseController;
	use Inc\ZephyrProjectManager;
	
	echo Projects::project_modal();
?>
