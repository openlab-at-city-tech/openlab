<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */
?>
<?php
	// Let's check to see if we've chosen to use the optional sidebar before we generate the markup
	$options = get_option( 'modularity_theme_options' );
	if ( $options['sidebar'] == 1 ) :
?>
<div class="span-8 last">
	<div id="sidebar">

		<?php dynamic_sidebar( 'sidebar' ); ?>

	</div>
</div>
<?php endif; ?>