<?php
$options = get_option('digressit');
?>


<?php get_header(); ?>

<div id="container">
<?php get_dynamic_widgets(); ?>
<?php get_stylized_title(); ?>

<div id="content">
	<div id="mainpage">		
		<h3 class="toc"><?php echo $options['table_of_contents_label']; ?></h3>
		<div class="description"><?php html_entity_decode(get_bloginfo('description')); ?></div>
		<div class='comment-count-in-book'>There are <?php echo getAllCommentCount() ?> comments in this document</div>

		<?php wp_nav_menu(array('depth'=> 3, 'fallback_cb'=> 'mainpage_default_menu', 'echo' => true, 'theme_location' => 'Main Page', 'menu_class' => 'navigation')); ?>


		<?php get_widgets('Mainpage Content'); ?>
		<div class="clear"></div>
	</div>
</div>


</div>
<?php get_footer(); ?>

