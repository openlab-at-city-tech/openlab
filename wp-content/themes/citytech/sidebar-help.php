<?php
/**
 * The Help Sidebar
 *
 */?>

        <h2 class="sidebar-title">Help</h2>
    	<?php $args = array(
				'theme_location' => 'helpmenu',
				'container' => 'div',
                'container_id' => 'help-menu',
				'menu_class' => 'sidebar-nav',
			);
		wp_nav_menu( $args ); 
		?>
		
        <h2 class="sidebar-help-title help-tags-title">Find a Help Topic With Tags</h2>
        <div id="help-tags-copy"><p>Find answers throughout Help that correspond to the tags below:</p></div>
        <div id="help-tags">
        <?php  $terms = get_terms("help_tags");
			   $count = count($terms);
			   if ( $count > 0 ){
				   foreach ( $terms as $term ) {
					 echo '<a href="'.get_term_link($term).'" class="tag-count-'.$term->count.'">' . $term->name . '</a> ';
				   }
			   } ?>
               <div class="clearfloat"></div>
        </div>
        <div id="support-team">
        <h2 class="sidebar-help-title support-team-title">Our Support Team</h2>
        <?php 
					$args=array(
					  'name' => 'contact-us',
					  'post_type' => 'help',
					  'post_status' => 'publish',
					  'numberposts' => 1
					);
					$my_posts = get_posts($args);
					
					if( $my_posts ) {
						$post_id = $my_posts[0]->ID;
					}
									$args = array(
									'post_type' => 'attachment',
									'numberposts' => -1,
									'post_status' => 'any',
									'post_parent' => $post_id
								);
								$attachments = get_posts($args);
								
								if ($attachments) {
									$i = 0;
									
									echo '<ul id="team-thumbs">';
									foreach ($attachments as $attachment) {
										$thumb_class = "thumb-wrapper";
										if ( $i%3 == 2 ) { $thumb_class .= " clear-right"; };
										
										echo '<li class="'.$thumb_class.'">';
											echo '<a href="'.get_permalink( get_page_by_path('/help/contact-us') ).'">';
											  echo '<div class="team-thumb">';
												  //use wordpress native thumbnail size for hard crop, then resize to fit container requirements
												  $src = wp_get_attachment_image_src($attachment->ID, 'thumbnail');
												  echo '<img src="'.$src[0].'" width="51" height="51" >';
											  echo '</div>';
											  
											  echo '<div class="team-name">';
											  echo $attachment->post_excerpt;
											  echo '</div>';
											echo '</a>';
										echo '</li>';
										$i++;
										}//end for each
										echo '</ul>';
								} //end if ?>
                                <div class="clearfloat"></div>
        </div><!--support team-->
        <h3 id="help-contact-us"><a href="<?php echo esc_url( get_permalink( get_page_by_path('/help/contact-us') ) ); ?>">Contact Us <div id="mail-icon"></div></a></h3>
        
        <div id="creative-commons">
        	<p>Help Content:
            <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank">Creative Commons</a></p>
        </div>