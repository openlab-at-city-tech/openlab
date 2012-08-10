<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: Mainpage
*/

global $using_mainpage_nav_walker;
$digressit_options = get_option('digressit');
//global $digressit;
?>


<?php get_header(); ?>

<div id="container">
<?php get_dynamic_widgets(); ?>
<?php get_stylized_title(); ?>

<div id="content">
	<div id="mainpage">		
		<h3 class="toc"><?php echo $digressit_options['table_of_contents_label']; ?></h3>
		<div class="description"><?php echo html_entity_decode(get_bloginfo('description')); ?></div>
		<div class='comment-count-in-book'>There are <?php echo getAllCommentCount() ?> comments in this document</div>


		<?php wp_nav_menu(array('walker' => new mainpage_nav_walker(), 'depth'=> 3, 'fallback_cb'=> 'mainpage_default_menu', 'echo' => true, 'theme_location' => 'Main Page', 'menu_class' => 'navigation')); ?>

		<?php if($using_mainpage_nav_walker): ?>

			<?php 
			if (( $locations = get_nav_menu_locations() ) && isset( $locations[ 'Main Page' ] ) ){
				$menu = wp_get_nav_menu_object( $locations[ 'Main Page' ] );
			}
			$menu_items = wp_get_nav_menu_items( $menu->term_id );
			?>
			<?php mainpage_content_display($menu_items); ?>			
		<?php else: ?>
			<?php get_widgets('Mainpage Content'); ?>			
		<?php endif; ?>
		<div class="clear"></div>
	</div>
</div>


</div>
<?php get_footer(); ?>

