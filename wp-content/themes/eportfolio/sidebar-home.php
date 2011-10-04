<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

<div class="widget brownbox">
	<h3>Frequent Questions</h3>
	<ul>
	<?php $posts = get_posts('numberposts=2&category='.$ahstheme_faqid);
	foreach ($posts as $post): setup_postdata($post); ?>
		<li>
			<h4><a href="<?php echo get_permalink($post->ID) ?>"><?php echo $post->post_title ?></a></h4>
			<?php echo ahs_excerpt(get_the_content(),427,'<ul><li>') ?>
		</li>
	<?php endforeach; ?>
	</ul>
	<div class="read_more"><a href="<?php echo esc_url(home_url('/')); ?>?cat=<?php echo $ahstheme_faqid ?>">Read More</a></div>
</div>

<div class="widget brownbox">
	<h3>Recommendations</h3>

	<?php $posts = get_posts('numberposts=1&category='.$ahstheme_recid);
	foreach ($posts as $post): setup_postdata($post); $line2 = get_post_meta($post->ID, 'recommendation_line2', true); ?>
		<img src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/quote-1.png" alt="open quote" style="float: left; margin: 0 10px 0 20px" />
		<?php echo the_excerpt() ?>
		<p><strong><?php echo $post->post_title ?>,<br /><?php echo $line2 ?></strong></p>
	<?php endforeach; ?>
	
	<div class="read_more"><a href="<?php echo esc_url(home_url('/')); ?>?cat=<?php echo $ahstheme_recid ?>">Read More</a></div>
	
</div>