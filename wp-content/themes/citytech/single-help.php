<?php
/*
Template Name: Help
*/

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'openlab_help_loop');
function openlab_help_loop() {

    global $paged;
    $args = array(	'post_type' => 'help', 
				  	'p' => get_the_ID() );
	$temp = $wp_query; 
	$wp_query = null;
	$wp_query = new WP_Query($args); 	
	
	while ( have_posts() ) : the_post(); ?>
    	
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <div class="cat-list">category: <?php echo get_the_term_list($post_id, 'help_category', '', ', ',''); ?></div>
        <div class="help-tags">tags: <?php echo get_the_term_list($post_id, 'help_tags', '', ', ',''); ?></div>
        
        <div class="entry-content"><?php the_content(); ?></div>
    
    <?php endwhile; // end of the loop. ?>

<?php }//end openlab_help_loop() ?>

<?php add_action('genesis_before_sidebar_widget_area', 'cuny_help_menu');
      function cuny_help_menu() {
	  	get_sidebar('help');
	  } ?>
<?php genesis(); ?>
