<?php
/*
Template Name: Help
*/

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'openlab_help_loop');
function openlab_help_loop() {

    global $paged, $post;
    $args = array(	'post_type' => 'help', 
				  	'p' => get_the_ID() );
	$temp = $wp_query; 
	$wp_query = null;
	$wp_query = new WP_Query($args); 	
	
	while ( have_posts() ) : the_post(); ?>
    	
        <?php $help_cats = get_the_term_list($post_id, 'help_category', '', ', ',''); ?>
        
        <?php if ($help_cats): ?>
        <h1 class="entry-title"><?php echo $help_cats; ?></h1>
        <div id="help-title"><h2 class="page-title"><?php the_title(); ?></h2></div>
        <?php elseif ($post->post_name == "openlab-help"): ?>
        <h1 class="entry-title"><?php echo the_title();?></h1>
        <div id="help-title"><h2 class="page-title"><?php _e('Do you have a question? You\'re in the right place!', 'buddypress') ?></h2></div>
		<?php else: ?>
         <h1 class="entry-title"><?php echo the_title();?></h1>       
		<?php endif; ?>
        
        <?php //print this page button - this is going to be absolutely positioned for now ?>
        <div class="print-page"><input type="button" value="Print this page"
onclick="window.print();return false;" /></div>
        
        <div id="help-identity">
        	<div class="cat-list">Category: <?php echo get_the_term_list($post_id, 'help_category', '', ', ',''); ?></div>
        	<div class="help-tags">Tags: <?php echo get_the_term_list($post_id, 'help_tags', '', ', ',''); ?></div>
        </div>
        
        <div class="entry-content"><?php the_content(); ?></div>
        
        			<nav id="nav-single">
                    <?php $next_post = get_next_post();
						  $prev_post = get_previous_post(); ?>
						<div class="nav-previous"><a href="<?php echo get_permalink( $prev_post->ID ); ?>">&larr; <?php echo $prev_post->post_title; ?></a></div>
                        <div class="nav-next"><a href="<?php echo get_permalink( $next_post->ID ); ?>"><?php echo $next_post->post_title; ?> &rarr;</a></div>
                        <div class="clearfloat"></div>
					</nav><!-- #nav-single -->
    
    <?php endwhile; // end of the loop. ?>

<?php }//end openlab_help_loop() ?>

<?php add_action('genesis_before_sidebar_widget_area', 'cuny_help_menu');
      function cuny_help_menu() {
	  	get_sidebar('help');
	  } ?>
<?php genesis(); ?>
