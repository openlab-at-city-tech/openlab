<?php 
	/**
	* Template for displaying the project grid
	*/
	
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Projects;
?>

<div class="zpm_project_grid">
	<?php foreach ( $projects as $project ) : ?>
		<?php echo Projects::new_project_cell( $project ); ?>
	<?php endforeach; ?>
</div>