<?php global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

<div class="widget greybox">
	<h3>Professional Goals</h3>
	<?php $id=$ahstheme_goalspage; $post = get_post($id); ?>
	
	<p><?php echo ahs_excerpt($post->post_content,250); ?>...</p>
	
	<div class="read_more"><a href="<?php echo get_permalink($id) ?>">&raquo; Read More</a></div>
</div>

<div class="widget greybox">
	<h3>Contact</h3>
	
	<div id="write_to_me">
		<p><?php echo $ahstheme_contactintro ?></p>
		
		<div id="error" class="error"></div>
		
		<form id="contactform" onsubmit="return false">
			<input type="hidden" name="themedir" id="themedir" value="<?php bloginfo('stylesheet_directory') ?>" />
			<div class="input">
				<label>Name</label>
				<input type="text" name="name" id="name" />
			</div>
			<div class="input">
				<label>Email</label>
				<input type="text" name="email" id="email" />
			</div>
			<div class="input">
				<label>Comment</label>
				<textarea name="comment" id="comment"></textarea>
			</div>
			<div class="submit">
				<input type="submit" name="submit" value="&raquo; Send" />
			</div>
		</form>
	</div>
</div>