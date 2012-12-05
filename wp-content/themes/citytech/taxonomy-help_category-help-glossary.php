<?php
/*
Template Name: Help
*/
/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
		<?php openlab_help_cats_loop(); ?>
	</div>
    <div id="sidebar" class="sidebar widget-area">
		<?php get_sidebar('help'); ?>
	</div>
<?php get_footer(); 
/**end layout**/

function openlab_help_cats_loop() { ?>
	
	<div id="help-top"></div>
    
	<?php 
	//first display the parent category
	global $post;
	$term = get_query_var('term');
	$parent_term = get_term_by( 'slug' , $term , 'help_category' );
	
    $args = array(
				  	'post_type' => 'help_glossary',
					'orderby' => 'menu_order',
					'order' => 'ASC',
					);
	
	
	$temp = $wp_query; 
	$wp_query = null;
	$wp_query = query_posts($args);//new WP_Query($args); ?> 	
    
    <h1 class="parent-cat">Glossary</h1>
    <div class="glossary-description"><p><?php echo $parent_term->description; ?></p></div>
	
<?php	while ( have_posts() ) : the_post();
	
		$post_id = get_the_ID(); 
		?>
    	
        <div class="glossary-wrapper">
        	<h3 class="glossary-title"><?php the_title(); ?></h3>
        	<div class="glossary-entry"><?php the_content(); ?></div>
    		<div class="clearfloat"></div>
        </div><!--glossary-wrapper-->
        
    <?php endwhile; // end of the loop. 
		  wp_reset_query(); ?>
		  
          <a href="#help-top">Go To Top</a>

<?php }//end openlab_help_loop()