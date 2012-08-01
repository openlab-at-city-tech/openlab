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
		
        <h2 class="help-tags-title">Find a Help Topic With Tags</h2>
        <div id="help-tags-copy"><p>Fing answers throughout Help that correspond to the tags below:</p></div>
        <div id="help-tags">
        <?php  $terms = get_terms("help_tags");
			   $count = count($terms);
			   if ( $count > 0 ){
				   foreach ( $terms as $term ) {
					 echo '<a href="'.get_term_link($term).'">' . $term->name . '</a> ';
				   }
			   } ?>   
        </div>