<?php
/*
Template Name: Help
*/

/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
		<?php openlab_help_loop(); ?>
	</div>
    <div id="sidebar" class="sidebar widget-area">
		<?php get_sidebar('help'); ?>
	</div>
<?php get_footer(); 
/**end layout**/
?>

<?php function openlab_help_loop() {

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
        
        			<?php openlab_help_navigation(); ?>
    
    <?php endwhile; // end of the loop. ?>

<?php }//end openlab_help_loop()
