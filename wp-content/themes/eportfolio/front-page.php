<?php 
	global $options;
	foreach ($options as $value) { 
		if ( get_option( $value['id'] ) === FALSE && isset( $value['std'] ) ) { 
			$$value['id'] = $value['std']; 
		} else { 
			$$value['id'] = get_option( $value['id'] ); 
		} 
	} 
?>

<?php get_header() ?>
<?php the_post(); ?>

<h2>Welcome to ePortfolio @ City Tech</h2>

<?php $post = get_post($ahstheme_welcomepage); setup_postdata($post); $image_url=ahs_getimg($post->ID,array(217,300)); ?>
<p><img src="<?php echo $image_url ?>" width="217" class="alignleft" /><?php echo ahs_excerpt(get_the_content(),920) ?>&hellip;</p>
<div class="read_more"><a href="<?php echo esc_url(home_url('/')); ?>?page_id=<?php echo $ahstheme_welcomepage ?>">Read More</a></div>

<div class="hr"></div>

<h2>Recent Projects</h2>

<ul id="sp" class="fourcol">
<?php
	$posts = get_posts('numberposts=4&category='.$ahstheme_projectscat);
	if (empty($posts)) {
		echo '<li><i>No posts in this category! Add a post and give it your Projects category.</i></li>';
	} else {
		foreach ($posts as $post): $image_url=ahs_getimg($post->ID,array(137,137)); setup_postdata($post); ?>
		<li>
			<div class="cutoff_img"><a href="<?php echo get_permalink($post->ID) ?>"><img src="<?php echo $image_url ?>" width="137" /></a></div>
		</li>
<?php endforeach; }?>
</ul>

<div class="clr"></div>

<div class="read_more"><a href="<?php echo esc_url(home_url('/')); ?>?cat=<?php echo $ahstheme_projectscat ?>">See More Projects</a></div>

<div class="hr"></div>

<ul id="lastrow" class="twocol">
	<li>
		<h2>What is ePortfolio?</h2>
		<?php $post = get_post($ahstheme_whatispage); setup_postdata($post); ?>
		<p><?php echo ahs_excerpt(get_the_excerpt(),170) ?>...</p>
		<div class="read_more"><a href="<?php echo get_permalink($post->ID) ?>">Read More</a></div>
	</li>
	<li>
		<h2>Recent Post</h2>

	<?php $posts = get_posts('numberposts=1&category='.$ahstheme_blogcat);
	foreach ($posts as $post): setup_postdata($post); ?>
		<p><?php echo ahs_excerpt(get_the_excerpt(),170) ?>...</p>
		<div class="read_more"><a href="<?php echo get_permalink($post->ID) ?>">Read More</a></div>
<?php endforeach; ?>


	</li>
</ul>

<?php get_footer() ?>