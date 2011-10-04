<?php global $vigilance; ?>
<div id="sidebar">
	<?php if ($vigilance->sideimgState() != '' ) {
		if ($vigilance->sideimgState() != 'hide' ) {
			if (is_file(STYLESHEETPATH . '/sidebar-imagebox.php' )) include(STYLESHEETPATH . '/sidebar-imagebox.php' );
			else include(TEMPLATEPATH . '/sidebar-imagebox.php' );
		}
	} ?>
	<?php if ($vigilance->feedState() == 'disabled' ) : else : ?>
		<?php if (is_file(STYLESHEETPATH . '/sidebar-feedbox.php' )) include(STYLESHEETPATH . '/sidebar-feedbox.php' ); else include(TEMPLATEPATH . '/sidebar-feedbox.php' ); ?>
	<?php endif; ?>
	<ul>
		<?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar( 'wide_sidebar' ) ) : ?>
			<li class="widget widget_recent_entries">
				<h2 class="widgettitle"><?php _e( 'Recent Articles', 'vigilance' ); ?></h2>
				<ul>
					<?php $side_posts = get_posts( 'numberposts=10' ); foreach($side_posts as $post) : ?>
						<li><a href= "<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</li>
		<?php endif; ?>
	</ul>
	<?php if (is_active_sidebar( 'left_sidebar' )) echo '<ul class="thin-sidebar spad">';?>
		<?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar( 'left_sidebar' ) ) : endif; ?>
	<?php if (is_active_sidebar( 'left_sidebar' )) echo '</ul>'; ?>
	<?php if (is_active_sidebar( 'right_sidebar' )) echo '<ul class="thin-sidebar">'; ?>
		<?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar( 'right_sidebar' ) ) : endif; ?>
	<?php if (is_active_sidebar( 'right_sidebar' )) echo '</ul>' ;?>
</div><!--end sidebar-->