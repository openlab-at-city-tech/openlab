<?php global $p; ?>
<div class="project small <?php echo $p; ?>" id="project-<?php echo $post->post_name;?>">
	<div class="inside">
	<a href="<?php the_permalink(); ?>" rel="bookmark" >	
		<?php the_post_thumbnail("ttrust_one_third_cropped", array('class' => 'thumb', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?>
		<span class="title"><span><?php the_title(); ?></span></span>
	</a>
	</div>																																
</div>