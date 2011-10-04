<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

<?php get_header() ?>
<?php the_post(); ?>

<h2>Critical Writing</h2>

<ul id="cw" class="twocol">
<?php
	$posts = get_posts('numberposts=2&category='.$ahstheme_writingcat);
	if (empty($posts)) {
		echo '<li><i>No posts in this category! Add a post and give it your Critical Writing category.</i></li>';
	} else {
		foreach ($posts as $post): $image_url=ahs_getimg($post->ID,array(300,300)); setup_postdata($post); ?>
		<li>
			<div class="cutoff_img"><a href="<?php echo get_permalink($post->ID) ?>"><img src="<?php echo $image_url ?>" width="300" /></a></div>
			<h3><a href="<?php echo get_permalink($post->ID) ?>"><?php echo $post->post_title ?></a></h3>
			<p><?php echo ahs_excerpt(get_the_excerpt(),120) ?>...</p>
			<div class="read_more"><a href="<?php echo get_permalink($post->ID) ?>">&raquo; Read More</a></div>
		</li>
<?php endforeach; } ?>
</ul>

<div class="clr"></div>
<div class="hr"></div>

<h2>Sample Projects</h2>

<ul id="sp" class="fourcol">
<?php
	$posts = get_posts('numberposts=4&category='.$ahstheme_projectscat);
	if (empty($posts)) {
		echo '<li><i>No posts in this category! Add a post and give it your Projects category.</i></li>';
	} else {
		foreach ($posts as $post): $image_url=ahs_getimg($post->ID,array(137,137)); setup_postdata($post); ?>
		<li>
			<div class="cutoff_img small"><a href="<?php echo get_permalink($post->ID) ?>"><img src="<?php echo $image_url ?>" width="137" /></a></div>
		</li>
<?php endforeach; }?>
</ul>

<div class="clr"></div>

<div class="read_more"><a href="/?cat=<?php echo $ahstheme_projectscat ?>">&raquo; See More Projects</a></div>

<div class="hr"></div>

<ul id="lastrow" class="twocol">
	<li>
		<h2>Academic Profile</h2>
		<?php $post = get_post($ahstheme_profilepage); setup_postdata($post); ?>
		<p><?php echo ahs_excerpt(get_the_excerpt(),170) ?>...</p>
		<div class="read_more"><a href="<?php echo get_permalink($post->ID) ?>">&raquo; Read More</a></div>
	</li>
	<li>
		<h2>From the Blog</h2>

	<?php $posts = get_posts('numberposts=1&category='.$ahstheme_blogcat);
	foreach ($posts as $post): setup_postdata($post); ?>
		<h3><a href="<?php echo get_permalink($post->ID) ?>"><?php echo $post->post_title ?></a></h3>
		<p><?php echo ahs_excerpt(get_the_excerpt(),120) ?>...</p>
		<div class="read_more"><a href="<?php echo get_permalink($post->ID) ?>">&raquo; Read More</a></div>
<?php endforeach; ?>

		
	</li>
</ul>

<?php get_footer() ?>