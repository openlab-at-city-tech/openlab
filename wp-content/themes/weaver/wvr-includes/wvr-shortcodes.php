<?php
/*
 Weaver Shortcodes

*/

function weaver_show_posts_shortcode($args = '') {
    /* implement [weaver_get_posts opt1="val1" opt2="value"] shortcode */

/* DOC NOTES:
CSS styling: The group of posts will be wrapped with a <div> with a class called
.wvr-show-posts. You can add an additional class to that by providing a 'class=classname' option
(without the leading '.' used in the actual CSS definition). You can also provide inline styling
by providing a 'style=value' option where value is whatever styling you need, each terminated
with a semi-colon (;).

The optional header is in a <div> called .wvr_show_posts_header. You can add an additional class
name with 'header_class=classname'. You can provide inline styling with 'header_style=value'.

.wvr-show-posts .hentry {margin-top: 0px; margin-right: 0px; margin-bottom: 40px; margin-left: 0px;}
.widget-area .wvr-show-posts .hentry {margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;}
*/

    global $more;
    global $weaver_cur_post_id;

    extract(shortcode_atts(array(
	     /* query_posts options */
	    'cats' => '',			/* by slug, use - to exclude (cat, but convert slug to id) */
	    'tags' => '',			/* by slug (tag) */
	    'author' => '',			/* author - use nickname (auhor_name)*/
	    'single_post' => '',		/* by slug - only one article (name) */
	    'orderby' => 'date',		/* author | date | title | rand | modified | parent {date} (orderby) */
	    'sort' => 'DESC',			/* ASC | DESC {DESC} (order)*/
	    'number' => '5',			/* number of posts to show  {5} (posts_per_page)*/
	    /* formatting options */
	    'show' => 'full',			/* show: title | excerpt | full  */
	    'hide_title' => '',			/* hide the title? */
	    'hide_top_info' => '',		/* hide the top info line */
	    'hide_bottom_info' => '',		/* hide bottom info line */
	    'show_featured_image' => '', 	/* force showing featured image */
	    'show_avatar' => '',		/* show the author avatar */
	    'show_bio' => '',			/* show the bio below */
	    'excerpt_length' => '',		/* override excerpt length */
	    'style' => '',			/* inline CSS style for wvr-show-posts */
	    'class' => '',			/* optional class to allow outside styling */
	    'header' => '',			/* optional header for post */
	    'header_style' => '',		/* styling for the header */
	    'header_class' => '',		/* class for header */
	    'more_msg' => ''			/* replacement for Continue Reading */

    ), $args));

    $save_cur_post = $weaver_cur_post_id;
    $save_excerpt_length = weaver_getopt('ttw_excerpt_length');
    if (!empty($excerpt_length)) weaver_setopt('ttw_excerpt_length',$excerpt_length);
    $save_more_msg = weaver_getopt('ttw_excerpt_more_msg');
    if (!empty($more_msg)) weaver_setopt('ttw_excerpt_more_msg', $more_msg);

    /* Setup query arguments using the supplied args */
    $qargs = array(
	'ignore_sticky_posts' => 1
    );

    $qargs['orderby'] = $orderby;	/* enter opts that have defaults first */
    $qargs['order'] = $sort;
    $qargs['posts_per_page'] = $number;
    if (!empty($cats)) $qargs['cat'] = weaver_cat_slugs_to_ids($cats);
    if (!empty($tags)) $qargs['tag'] = $tags;
    if (!empty($single_post)) $qargs['name'] = $single_post;
    if (!empty($author)) $qargs['author_name'] = $author;

    $ourposts = new WP_Query($qargs);
	// now modify the query using custom fields for this page

    /* now start the content */

    $div_add = '';
    if (!empty($style)) $div_add = ' style="' . $style . '"';
    $content = '<div class="wvr-show-posts ' . $class . '"'  . $div_add . '/>';

    $h_add = '';
    if (!empty($header_style)) $h_add = ' style="' . $header_style . '"';

    if (!empty($header)) {
	$content .= '<div class="wvr-show-posts-header ' . $header_class . '"' . $h_add . '>' . $header . '</div>';
    }

    ob_start();	// use built-in weaver code to generate a weaver standard post

    while ( $ourposts->have_posts() ) {
	$ourposts->the_post();
	$weaver_cur_post_id = get_the_ID();
	weaver_per_post_style();

	if (!is_sticky()) {
	?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if (empty($hide_title)) { ?>
	<h2 class="entry-title"><?php weaver_post_title(); ?></h2>
	<?php } ?>

	<?php if (empty($hide_top_info)) weaver_posted_on('blog');

	if ($show == 'excerpt') {	/* excerpt? */ ?>
		<div class="entry-summary">
		<?php weaver_the_excerpt_featured(true /* always excerpt */, !empty($show_featured_image)); ?>
		</div><!-- .entry-summary -->

	<?php } else if ($show != 'title') { ?>
		<div class="entry-content">
		<?php
		$more = false; // let <!-- more --> work, still use single
		weaver_the_content_featured_single(!empty($show_featured_image) ); ?>
		</div><!-- .entry-content -->
	<?php }

	if ( !empty($show_bio) && get_the_author_meta( 'description' ) ) { // If a user has filled out their description, show a bio on their entries  ?>
		<div id="entry-author-info">
		    <div id="author-avatar">
			<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'weaver_author_bio_avatar_size', 60 ) ); ?>
		    </div><!-- #author-avatar -->
		    <div id="author-description">
			<h2><?php printf( esc_attr__( 'About %s', WEAVER_TRANS ), get_the_author() ); ?></h2>
			<?php the_author_meta( 'description' ); ?>
			<div id="author-link">
			    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
				<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', WEAVER_TRANS ), get_the_author() ); ?>
			    </a>
			</div><!-- #author-link	-->
		    </div><!-- #author-description -->
		</div><!-- #entry-author-info -->
	<?php
	}
	if (empty($hide_bottom_info)) weaver_posted_in('blog');
	else echo('<div style="clear:both;"></div>');
	?>
	</div><!-- #post-## -->
	<?php
	} // end !sticky

    } // end loop

    $content .= ob_get_clean();	// get the output

    // get posts

    $content .= '</div><!-- #wvr-show-posts -->';
    wp_reset_query();

    $weaver_cur_post_id = $save_cur_post;

    weaver_setopt('ttw_excerpt_length',$save_excerpt_length);
    weaver_setopt('ttw_excerpt_more_msg',$save_more_msg);
    return $content;
}

add_shortcode('weaver_show_posts', 'weaver_show_posts_shortcode');

/* ----------------- hide visual editor filter ----------------- */
function weaver_disable_visual_editor() {
  global $wp_rich_edit;

  if (!isset($_GET['post']))
      return;
  $post_id = $_GET['post'];
  $value = get_post_meta($post_id, 'hide_visual_editor', true);
  $raw = get_post_meta($post_id, 'wvr_raw_html', true);
  if($value == 'on' || $raw == 'on')
    $wp_rich_edit = false;
}
add_action('load-page.php', 'weaver_disable_visual_editor');
add_action('load-post.php', 'weaver_disable_visual_editor');
?>
