<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */


/* If the current layout is no-sidebar, let's just get out of here.
 * If the current layout is a 3-column one with 2 sidebars on the right or left
 * Pilcrow enables a "Feature Widget Area" that should span both sidebar columns
 * and adds a containing div around the main sidebars for the content-sidebar-sidebar
 * and sidebar-sidebar-layouts so the layout holds together with a short content area and long featured widget area
 */
$options = pilcrow_get_theme_options();

if ( 'no-sidebar' == $options['theme_layout'] )
	return;

$feature_widget_area_layouts = array( 'content-sidebar-sidebar', 'sidebar-sidebar-content' );
if ( in_array( $options['theme_layout'], $feature_widget_area_layouts ) ) :
?>

<div id="main-sidebars">

	<?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>
	<div id="feature" class="widget-area" role="complementary">
		<ul class="xoxo sidebar-list">
			<?php dynamic_sidebar( 'sidebar-3' ); ?>
		</ul>
	</div><!-- #feature.widget-area -->
	<?php endif; // ends the check for the current layout that determines the availability of the feature widget area ?>

<?php endif; // ends the check for the current layout that determines the #main-sidebars markup ?>

	<div id="sidebar" class="widget-area" role="complementary">
		<?php do_action( 'before_sidebar' ); ?>
		<ul class="xoxo sidebar-list">

			<?php
				/* When we call the dynamic_sidebar() function, it'll spit out
				 * the widgets for that widget area. If it instead returns false,
				 * then the sidebar simply doesn't exist, so we'll hard-code in
				 * some default sidebar stuff just in case.
				 */
				if ( ! dynamic_sidebar( 'sidebar-1' ) ) :
			?>

			<li class="widget widget_search">
				<h3 class="widget-title"><?php _e( 'Search', 'pilcrow' ); ?></h3>
				<?php get_search_form(); ?>
			</li>

			<li class="widget widget_recent_entries">
				<h3 class="widget-title"><?php _e( 'Recent Entries', 'pilcrow' ); ?></h3>
				<ul>
					<?php
						$recent_entries = new WP_Query( array(
							'posts_per_page' => 10,
						) );

						while ( $recent_entries->have_posts() ) :
							$recent_entries->the_post();
					?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>

			<li class="widget widget_links">
				<h3 class="widget-title"><?php _e( 'Links', 'pilcrow' ); ?></h3>
				<ul>
					<?php wp_list_bookmarks(); ?>
				</ul>
			</li>

		<?php endif; // end primary widget area ?>
		</ul>
	</div><!-- #sidebar .widget-area -->

	<?php
		/* If the current layout is a 3-column one, Pilcrow enables a second widget area called Secondary Widget Area
		 * This widget area will not appear for two-column layouts
		 */
		$secondary_widget_area_layouts = array( 'content-sidebar-sidebar', 'sidebar-sidebar-content', 'sidebar-content-sidebar' );
		if ( in_array( $options['theme_layout'], $secondary_widget_area_layouts ) ) :
	?>
	<div id="secondary-sidebar" class="widget-area" role="complementary">
		<ul class="xoxo sidebar-list">
		<?php // A second sidebar for widgets. Pilcrow uses the secondary widget area for three column layouts.
		if ( ! dynamic_sidebar( 'sidebar-2' ) ) : ?>

			<li class="widget widget_meta">
				<h3 class="widget-title"><?php _e( 'Meta', 'pilcrow' ); ?></h3>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</li>

		<?php endif; ?>
		</ul>
	</div><!-- #secondary-sidebar .widget-area -->
	<?php endif; // ends the check for the current layout that determins if the third column is visible.

// add a containing div around the main sidebars for the content-sidebar-sidebar and sidebar-sidebar-layouts
// so the layout holds together with a short content area and long featured widget area
if ( in_array( $options['theme_layout'], $feature_widget_area_layouts ) )
	echo '</div><!-- #main-sidebars -->';
