<?php if(get_the_author_meta('description')) : ?>	
<div class="enigma_author_detail_section">
	<div class="enigma_heading_title2">
	<h3><?php _e('About Author','enigma'); ?></h3>		
	</div>
	<div class="enigma_author_detail_wrapper">
		<?php echo get_avatar( get_the_author_meta('email') , 90 ); ?>
		<h4 class="enigma_author_detail_name"><?php the_author(); ?></h4>
		<p><?php echo get_the_author_meta('description'); ?></p>
		<ul class="social" style="margin-top:10px;padding-left:0px;text-align:left">
		<?php 
		$youtube_profile = get_the_author_meta( 'youtube_profile' );
		if ( $youtube_profile && $youtube_profile != '' ) {
		echo '<li class="youtube" data-toggle="tooltip" data-placement="top" title="Youtube"><a href="' . esc_url($youtube_profile) . '"><i class="fa fa-youtube"></i></a></li>';
		}						
		$twitter_profile = get_the_author_meta( 'twitter_profile' );
		if ( $twitter_profile && $twitter_profile != '' ) {
			echo '<li class="twitter" data-toggle="tooltip" data-placement="top" title="Twiiter"><a href="' . esc_url($twitter_profile) . '"><i class="fa fa-twitter"></i></a></li>';
		}				
		$facebook_profile = get_the_author_meta( 'facebook_profile' );
		if ( $facebook_profile && $facebook_profile != '' ) {
			echo '<li class="facebook" data-toggle="tooltip" data-placement="top" title="Facebook"><a href="' . esc_url($facebook_profile) . '"><i class="fa fa-facebook"></i></a></li>';
		}
				
		$linkedin_profile = get_the_author_meta( 'linkedin_profile' );
		if ( $linkedin_profile && $linkedin_profile != '' ) {
		       echo '<li class="linkedin" data-toggle="tooltip" data-placement="top" title="Linkedin"><a href="' . esc_url($linkedin_profile) . '"><i class="fa fa-linkedin"></i></a></a></li>';
		}		
	?>
	</ul>	
	</div>				
</div>
<?php endif; ?>	