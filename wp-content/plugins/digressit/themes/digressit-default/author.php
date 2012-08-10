<?php


global $wp, $current_user, $wp, $current_page_template, $current_user_comments;
get_currentuserinfo();
$current_page_template = 'author.php';

get_header();


//var_dump(function_exists('custom_author_page'));

if(has_action('custom_author_page', 'custom_author_page')):
	do_action('custom_author_page');
	
else:
?>


<div class="content-wrapper">
<?php get_dynamic_widgets(); ?>


<div id="content">
	<div id="frontpage">
		

		<!-- This sets the $curauth variable -->

		    <?php
		    if(isset($_GET['author_name'])) :
		        $curauth = get_user_by('slug', $author_name);
		    else :
		        $curauth = get_userdata(intval($author));
		    endif;
		    ?>

		    <h2>About: <?php echo $curauth->nickname; ?></h2>
		    <p>
			<?php if($curauth->user_description): ?>
			<?php echo $curauth->user_description; ?>
			<?php else: ?>
			<?php echo '<p>No Description</p>'; ?>				
			<?php endif; ?>
			</p>


			<?php if($curauth->user_url): ?>
		    <p>
				<strong>Website</strong>
				<?php echo $curauth->user_url; ?>
			</p>
			<?php endif; ?>
			

		    <h2>Posts by <?php echo $curauth->nickname; ?>:</h2>

			<p>
		    <ul>
		<!-- The Loop -->

		    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		        <li>
		            <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>">
		            <?php the_title(); ?></a>,
		            <?php the_time('d M Y'); ?> in <?php the_category('&');?>
		        </li>

		    <?php endwhile; else: ?>
		        <p><?php _e('No posts by this author.'); ?></p>

		    <?php endif; ?>
			</p>

		<!-- End Loop -->
		<?php endif; ?>
		
		
	    <h2>Comments</h2>
	    <p>
		<?php global $comments; ?>
		<?php $comments = get_comments_from_user($curauth->ID); ?>
		<?php if(count($comments)): ?>
		<?php foreach($comments as $comment): ?>
			<?php wp_list_comments(array('type' => 'comment', 'callback' => get_digressit_comments_function() )); ?>			
		<?php endforeach; ?>
		<?php else: ?>
				No comments.
		<?php endif; ?>
		</p>
		
	</div>

</div>
<?php get_footer(); ?>