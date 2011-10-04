<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

<div class="widget greybox">
	<h3>Recent Posts</h3>
	<ul>
	<?php $posts = get_posts('numberposts=2'); foreach ($posts as $post): 
	setup_postdata($post); 
	$image_url=ahs_getimg($post->ID,array(65,65));
	?>
		<li>
			<a href="<?php the_permalink() ?>"><img src="<?php echo $image_url ?>" height="65" width="65" /></a>
			<a href="<?php the_permalink() ?>"><?php the_time('d/m/Y'); ?> <?php echo $post->post_title; ?></a><br />
			<p><?php echo ahs_excerpt(get_the_excerpt(),120) ?>...</p>
		</li>
	<?php endforeach; ?>
	</ul>
</div>

<div class="widget">
	<h3>Tag Cloud</h3>
	<?php wp_tag_cloud() ?>
</div>

<div class="widget subscribe">
	<h3>Subscribe</h3>
	<p><a href="<?php bloginfo('rss2_url'); ?>"><img src="<?php bloginfo('stylesheet_directory') ?>/images/social/rss.png" alt="RSS Feed" /></a>
	<?php 
		global $ahstheme_sm;
		foreach ($ahstheme_sm as $m) {
			if ($url = get_settings('ahstheme_social_'.$m)) {
				echo '<a href="http://'.$url.'" target="_blank"><img src="';
				bloginfo('stylesheet_directory');
				echo '/images/social/'.strtolower($m).'.png" alt="'.$m.'" /></a> ';
			}
		}	
	?></p>
</div>