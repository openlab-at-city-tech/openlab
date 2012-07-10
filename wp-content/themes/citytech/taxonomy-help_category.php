<?php
/*
Template Name: Help
*/

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'openlab_help_cats_loop');
function openlab_help_cats_loop() {
	
	//first display the parent category
	global $post;
	$parent_cat_name = single_term_title('',false);
	$term = get_query_var('term');
	$parent_term = get_term_by( 'slug' , $term , 'help_category' );

	
    $args = array(	'tax_query' => array(
									  array(
										  'taxonomy' => 'help_category',
										  'field' => 'slug',
										  'include_children' => false,
										  'terms' => array($parent_term->slug),
										  'operator' => 'IN'
									  )
								  ),
				  	'post_type' => 'help',
					);
	
	
	$temp = $wp_query; 
	$wp_query = null;
	$wp_query = new WP_Query($args); ?> 	
    
    <h2 class="parent-cat"><?php echo $parent_cat_name; ?></h2>
	
<?php	while ( have_posts() ) : the_post(); 
	
		$post_id = get_the_ID(); 
		?>
    	
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <div class="help-tags"><?php echo get_the_term_list($post_id, 'help_tags', '', ', ',''); ?></div>
    
    <?php endwhile; // end of the loop. 
		  wp_reset_query();
		  
		  //now iterate through each child category
		  $child_cats = get_categories( array('child_of' => $parent_term -> term_id, 'taxonomy' => 'help_category') );
		  foreach ($child_cats as $child)
		  {
			  $child_cat_id = $child->cat_ID;
			  echo '<h3 class="child-cat">'.$child->name.'</h3>';
			  
				$tax_args = array(
					  'taxonomy' => 'help_category',
					  'field' => 'slug',
					  'terms' => $child -> slug,
					  'include_children' => false
					  );
    			$args = array(	'post_type' => 'help',
				  	'tax_query' => array($tax_args)
					);

			  while ( have_posts() ) : the_post(); 
			  ?>
			  
			  <h1 class="entry-title"><?php //the_title(); ?></h1>
		  
		<?php endwhile; // end of the loop. 
			  wp_reset_query();

		  } ?>

<?php }//end openlab_help_loop() ?>

<?php add_action('genesis_before_sidebar_widget_area', 'cuny_help_menu');
      function cuny_help_menu() {
	  	get_sidebar('help');
	  } ?>
<?php genesis(); ?>