<?php
/**
 * @package Coraline
 * @since Coraline 1.0
 */
?>

		<?php
			/* If the current layout is a 3-column one with 2 sidebars on the right or left
			 * Coraline enables a "Feature Widget Area" that should span both sidebar columns
			 * and adds a containing div around the main sidebars for the content-sidebar-sidebar
			 * and sidebar-sidebar-layouts so the layout holds together with a short content area and long featured widget area
			 */
			$options = coraline_get_theme_options();
			$current_layout = $options['theme_layout'];
			$feature_widget_area_layouts = array( 'content-sidebar-sidebar', 'sidebar-sidebar-content' );

			if ( in_array( $current_layout, $feature_widget_area_layouts ) ) :
		?>
		<div id="main-sidebars">

		<?php if ( is_active_sidebar( 'feature-widget-area' ) ) : ?>

		<div id="feature" class="widget-area" role="complementary">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'feature-widget-area' ); ?>
			</ul>
		</div><!-- #feature.widget-area -->

		<?php endif; // ends the check for the current layout that determines the availability of the feature widget area ?>

		<?php endif; // ends the check for the current layout that determines the #main-sidebars markup ?>

		<div id="primary" class="widget-area" role="complementary">
		<?php do_action( 'before_sidebar' ); ?>
			<ul class="xoxo">

			<?php // The primary sidebar used in all layouts
			if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>

				<li id="search" class="widget-container widget_search">
					<h3 class="widget-title"><?php _e( 'Search It!', 'coraline' ); ?></h3>
					<?php get_search_form(); ?>
				</li>

				<li class="widget-container">
					<h3 class="widget-title"><?php _e( 'Recent Entries', 'coraline' ); ?></h3>
						<ul>
							<?php
							$recent_entries = new WP_Query();
							$recent_entries->query( 'order=DESC&posts_per_page=10' );

							while ($recent_entries->have_posts()) : $recent_entries->the_post();
								?>
								<li><a href="<?php the_permalink() ?>"><?php the_title() ?></a></li>
								<?php
							endwhile;
							?>
						</ul>
				</li>

				<li class="widget-container">
					<h3 class="widget-title"><?php _e( 'Links', 'coraline' ); ?></h3>
						<ul>
							<?php wp_list_bookmarks( array( 'title_li' => '', 'categorize' => 0 ) ); ?>
						</ul>
				</li>

			<?php endif; // end primary widget area ?>
			</ul>
		</div><!-- #primary .widget-area -->

		<?php
			/* If the current layout is a 3-column one, Coraline enables a second widget area called Secondary Widget Area
			 * This widget area will not appear for two-column layouts
			 */
			$secondary_widget_area_layouts = array( 'content-sidebar-sidebar', 'sidebar-sidebar-content', 'sidebar-content-sidebar' );
			if ( in_array( $current_layout, $secondary_widget_area_layouts ) ) :
		?>
		<div id="secondary" class="widget-area" role="complementary">
			<ul class="xoxo">
			<?php // A second sidebar for widgets. Coraline uses the secondary widget area for three column layouts.
			if ( ! dynamic_sidebar( 'secondary-widget-area' ) ) : ?>

				<li class="widget-container widget_links">
					<h3 class="widget-title"><?php _e( 'Meta', 'coraline' ); ?></h3>
					<ul>
						<?php wp_register(); ?>
						<li><?php wp_loginout(); ?></li>
						<?php wp_meta(); ?>
					</ul>
				</li>

			<?php endif; ?>
			</ul>
		</div><!-- #secondary .widget-area -->
		<?php endif; // ends the check for the current layout that determins if the third column is visible ?>

		<?php
			// add a containing div around the main sidebars for the content-sidebar-sidebar and sidebar-sidebar-layouts
			// so the layout holds together with a short content area and long featured widget area
			if ( in_array( $current_layout, $feature_widget_area_layouts ) )
				echo '</div><!-- #main-sidebars -->';
		?>
