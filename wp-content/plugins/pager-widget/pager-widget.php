<?php
/**
 * Plugin Name: Pager Widget
 * Plugin URI: https://github.com/uhm-coe/pager-widget
 * Description: Widget that provides "Parent | Previous | Next" buttons to navigate between pages at the same hierarchy level (and up to the parent page). You can modify the settings to choose which words you want to use. To enable, first activate the plugin, then add the widget to a sidebar in the Widgets settings page.
 * Requires at least: 2.8
 * Version: 1.8.2
 * Author: Paul Ryan
 * Author URI: http://www.linkedin.com/in/paulrryan/
 * License: GPL3
 *
 * @package pager-widget
 */

/**
 * PagerWidget Class
 */
class PagerWidget extends WP_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'wp-pager',
			'Pager Widget',
			array(
				'description' => 'Print "Parent | Previous | Next" links to navigate between pages at the same level in the page hierarchy (and up to the parent page).',
			)
		);
	}


	/**
	 * Function to display widget.
	 *
	 * @see WP_Widget::widget
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$labelParent                 = esc_attr( $instance['labelParent'] );
		$labelPrev                   = esc_attr( $instance['labelPrev'] );
		$labelNext                   = esc_attr( $instance['labelNext'] );
		$pageDepth                   = intval( $instance['pageDepth'] );
		$isStoryMode                 = intval( $instance['isStoryMode'] );
		$sortStoryModeAlphabetically = intval( $instance['sortStoryModeAlphabetically'] );

		echo $before_widget;

		// Get page object (since we're outside of the loop).
		global $post;

		// Don't do anything if we're not on a hierarchical post type (e.g., page).
		if ( ! is_post_type_hierarchical( get_post_type( $post ) ) ) {
			return;
		}

		// Operate the pager in story mode (walk through site map tree).
		if ( 1 === intval( $isStoryMode ) ) {
			// Print links to parent, previous, and next page.
			echo "<div id='linksPrevNext'>";

			// Get all pages in site. Sort by title if checkbox to sort alphabetically
			// is chosen; otherwise sort by menu_order.
			$allPages   = get_pages( array(
				'sort_column' => 1 === intval( $sortStoryModeAlphabetically ) ? 'post_title' : 'menu_order',
				'sort_order'  => 'asc',
			) );
			$totalPages = count( $allPages );
			for ( $i = 0; $i < $totalPages; $i++ ) {
				if ( $allPages[ $i ]->ID === $post->ID ) {
					break;
				}
			}

			$nextURI   = $i < count( $allPages ) - 1 ? get_permalink( $allPages[ $i + 1 ]->ID ) : '';
			$nextTitle = strlen( $nextURI ) > 0 ? $allPages[ $i + 1 ]->post_title : '';
			$prevURI   = $i > 0 ? get_permalink( $allPages[ $i - 1 ]->ID ) : '';
			$prevTitle = strlen( $prevURI ) > 0 ? $allPages[ $i - 1 ]->post_title : '';

			if ( strlen( $prevURI ) > 0 ) {
				echo "<a href='$prevURI' title='$prevTitle'>" . str_replace( '%title', $prevTitle, $labelPrev ) . '</a>';
			}
			// Display a break if both prev and next links are shown.
			if ( strlen( $prevURI ) > 0 && strlen( $nextURI ) > 0 ) {
				echo '&nbsp;&nbsp; | &nbsp;&nbsp;';
			}
			if ( strlen( $nextURI ) > 0 ) {
				echo "<a href='$nextURI' title='$nextTitle'>" . str_replace( '%title', $nextTitle, $labelNext ) . '</a>';
			}

			echo '</div>';

		} else { // Operate the pager in classic mode (page between siblings at a certain depth and their parent).
			// Make sure we're on a level $pageDepth page in the hierarchy that has siblings,
			// or on a level $pageDepth-1 page that has children.
			$hierarchyDepth = 0;
			$page           = $post;
			while ( $page->post_parent ) {
				$page = get_post( $page->post_parent );
				$hierarchyDepth++;
			}
			$children = wp_list_pages( "child_of={$post->ID}&echo=0" );
			$siblings = wp_list_pages( "title_li=&child_of={$post->post_parent}&echo=0&depth=1" );

			if ( ! ( ( $hierarchyDepth === $pageDepth && $siblings ) || ( ( $pageDepth - 1 ) === $hierarchyDepth && $children ) ) ) {
				echo $after_widget;
				return;
			}

			// Print links to parent, previous, and next page.
			echo "<div id='linksPrevNext'>";
			if ( ( $pageDepth - 1 ) === $hierarchyDepth && $children ) { // we're on a level $pageDepth-1 page that has children
				// Get links to children pages.
				$numberOfMatches = preg_match_all( "/<a href=[\"|\'](.*?)[\"|\'].*?>(.*?)<\/a>/i", $children, $matches, PREG_PATTERN_ORDER );
				$parentURI       = get_permalink( $post->ID );
				$parentTitle     = $post->post_title;
				$nextURI         = '';
				if ( count( $matches[1] ) > 0 ) {
					$nextURI   = $matches[1][0];
					$nextTitle = $matches[2][0];
				}
				if ( strlen( $nextURI ) > 0 ) {
					echo "<a href='$nextURI' title='$nextTitle'>" . str_replace( '%title', $nextTitle, $labelNext ) . '</a>';
				}
			} elseif ( $hierarchyDepth === $pageDepth && $siblings ) { // level $pageDepth page that has siblings.
				// Get links to sibling pages.
				$numberOfMatches = preg_match_all( "/<a href=[\"|\'](.*?)[\"|\'].*?>(.*?)<\/a>/i", $siblings, $matches, PREG_PATTERN_ORDER );
				// Get links to parent, previous, and next page.
				$currentSlug  = get_permalink( $post->ID ); // $post->post_name;
				$parentURI    = get_permalink( $post->post_parent );
				$parentTitle  = get_the_title( $post->post_parent );
				$prevURI      = get_permalink( $post->post_parent );
				$nextURI      = '';
				$totalMatches = count( $matches[1] );
				for ( $i = 0; $i < $totalMatches; $i++ ) {
					if ( strpos( $matches[1][ $i ], $currentSlug ) !== false ) { // we found the current page.
						if ( $i < count( $matches[1] ) - 1 ) {
							$nextURI   = $matches[1][ $i + 1 ];
							$nextTitle = $matches[2][ $i + 1 ];
						}
						break;
					}
					$prevURI   = $matches[1][ $i ];
					$prevTitle = $matches[2][ $i ];
				}
				echo "  <a href='$parentURI' title='$parentTitle'>" . str_replace( '%title', $parentTitle, $labelParent ) . '</a>';
				echo '  &nbsp;&nbsp; | &nbsp;&nbsp;';
				if ( strlen( $prevURI ) > 0 && $prevURI !== $parentURI ) {
					echo "<a href='$prevURI' title='$prevTitle'>" . str_replace( '%title', $prevTitle, $labelPrev ) . '</a>';
				}
				if ( strlen( $prevURI ) > 0 && $prevURI !== $parentURI && strlen( $nextURI ) > 0 ) {
					echo '&nbsp;&nbsp; | &nbsp;&nbsp;';
				}
				if ( strlen( $nextURI ) > 0 ) {
					echo "<a href='$nextURI' title='$nextTitle'>" . str_replace( '%title', $nextTitle, $labelNext ) . '</a>';
				}
			}
			echo '</div>';
		}

		echo $after_widget;
	}


	/**
	 * Function to save posted form data from widget admin panel.
	 *
	 * @see WP_Widget::update
	 *
	 * @param array $new_instance New settings for this instance as input by user.
	 * @param array $old_instance Existing settings for this instance.
	 */
	public function update( $new_instance, $old_instance ) {
		if ( ! isset( $new_instance['submit'] ) ) {
			return false;
		}

		$instance                                = $old_instance;
		$instance['labelParent']                 = wp_strip_all_tags( $new_instance['labelParent'] );
		$instance['labelPrev']                   = wp_strip_all_tags( $new_instance['labelPrev'] );
		$instance['labelNext']                   = wp_strip_all_tags( $new_instance['labelNext'] );
		$instance['pageDepth']                   = intval( $new_instance['pageDepth'] );
		$instance['isStoryMode']                 = isset( $new_instance['isStoryMode'] ) ? intval( $new_instance['isStoryMode'] ) : 0;
		$instance['sortStoryModeAlphabetically'] = isset( $new_instance['sortStoryModeAlphabetically'] ) ? intval( $new_instance['sortStoryModeAlphabetically'] ) : 0;
		return $instance;
	}


	/**
	 * Function to display options when widget added to sidebar.
	 *
	 * @see WP_Widget::form
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'labelParent'                 => 'Back to Overview',
			'labelPrev'                   => '&laquo; Previous Page',
			'labelNext'                   => 'Next Page &raquo;',
			'pageDepth'                   => 1,
			'isStoryMode'                 => 0,
			'sortStoryModeAlphabetically' => 0,
		) );

		echo '<p>Note: This widget only works on hierarchical post types (for example, Pages).</p><br/>';

		$valueParent = esc_attr( $instance['labelParent'] );
		$idParent    = $this->get_field_id( 'labelParent' );
		$nameParent  = $this->get_field_name( 'labelParent' );
		echo "<label for='$idParent'>Label for Parent link: ";
		echo "<input type='text' class='widefat' id='$idParent' name='$nameParent' value='$valueParent' />";
		echo '</label><br/><br/>';

		$valuePrev = esc_attr( $instance['labelPrev'] );
		$idPrev    = $this->get_field_id( 'labelPrev' );
		$namePrev  = $this->get_field_name( 'labelPrev' );
		echo "<label for='$idPrev'>Label for Previous link: ";
		echo "<input type='text' class='widefat' id='$idPrev' name='$namePrev' value='$valuePrev' />";
		echo '</label><br/><br/>';

		$valueNext = esc_attr( $instance['labelNext'] );
		$idNext    = $this->get_field_id( 'labelNext' );
		$nameNext  = $this->get_field_name( 'labelNext' );
		echo "<label for='$idNext'>Label for Next link: ";
		echo "<input type='text' class='widefat' id='$idNext' name='$nameNext' value='$valueNext' />";
		echo '</label><br/><br/>';

		echo '<small>Note: you can use %title to display the page title in the pager links</small><br/><br/>';

		$valueDepth = intval( $instance['pageDepth'] );
		$idDepth    = $this->get_field_id( 'pageDepth' );
		$nameDepth  = $this->get_field_name( 'pageDepth' );
		echo "<label for='$idDepth'>Show pager on this level of the hierarchy (0 is top level): ";
		echo "<input type='text' class='widefat' id='$idDepth' name='$nameDepth' value='$valueDepth' />";
		echo '</label><br/><br/>';

		$valueStoryMode = intval( $instance['isStoryMode'] );
		$checked        = 1 === $valueStoryMode ? "checked='checked'" : '';
		$idStoryMode    = $this->get_field_id( 'isStoryMode' );
		$nameStoryMode  = $this->get_field_name( 'isStoryMode' );
		echo "<input type='checkbox' id='$idStoryMode' name='$nameStoryMode' value='1' $checked />";
		echo "<label for='$idStoryMode'> Enable Story Mode (page through all site content, not just content under a parent item). Note that the level specified above has no meaning if this is enabled.</label><br/><br/>";

		$valueSortAlpha = intval( $instance['sortStoryModeAlphabetically'] );
		$checked        = 1 === $valueSortAlpha ? "checked='checked'" : '';
		$idSortAlpha    = $this->get_field_id( 'sortStoryModeAlphabetically' );
		$nameSortAlpha  = $this->get_field_name( 'sortStoryModeAlphabetically' );
		echo "<input type='checkbox' id='$idSortAlpha' name='$nameSortAlpha' value='1' $checked />";
		echo "<label for='$idSortAlpha'> Sort Story Mode alphabetically (page through all site content alphabetically by title). If this is not enabled, Story Mode content will be sorted by menu order.</label><br/><br/>";

		echo '<small>Note: you can apply CSS styles to #linksPrevNext</small><br/>';

		$idSubmit   = $this->get_field_id( 'submit' );
		$nameSubmit = $this->get_field_name( 'submit' );
		echo "<input type='hidden' id='$idSubmit' name='$nameSubmit' value='1' />";
	}
}

// Initalize widget object.
add_action( 'widgets_init', function() {
	return register_widget( 'PagerWidget' );
} );
