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
        </div>
        <div id="support-team">
        <h2 class="sidebar-help-title support-team-title">Support Team</h2>
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
										if ( $i%2 ) { $thumb_class .= " clear-right"; };
										
										echo '<li class="'.$thumb_class.'">';
											echo '<div class="team-thumb">';
											echo wp_get_attachment_link($attachment->ID, 'thumbnail');
											echo '</div>';
											
											echo '<div class="team-name">';
											echo $attachment->post_excerpt;
											echo '</div>';
										
										echo '</li>';
										$i++;
										}//end for each
										echo '</ul>';
								} //end if ?>
        </div>