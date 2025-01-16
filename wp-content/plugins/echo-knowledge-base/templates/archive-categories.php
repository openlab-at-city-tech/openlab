 <?php
/**
 * Template for KB Category Archive Page front-end setup for V2
 *
 */

global $epkb_password_checked;

$kb_id = EPKB_Utilities::get_eckb_kb_id();
$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

/**
 * Display ARTICLE PAGE content
 */
if ( empty( $hide_header_footer ) ) {
	get_header();
}

if ( $kb_config['archive_page_v3_toggle'] == 'on' ) {
    if ( EPKB_KB_Handler::is_kb_category_taxonomy( $GLOBALS['taxonomy'] ) ) {
	    EPKB_Category_Archive_Setup::get_category_archive_page_v3( $kb_config );
    } else if (  EPKB_KB_Handler::is_kb_tag_taxonomy( $GLOBALS['taxonomy'] ) ) {
        EPKB_Tag_Archive_Setup::get_tag_archive_page( $kb_config );
    }
} else {
	epkb_category_archive_v2( $kb_config );
}

if ( empty( $hide_header_footer ) ) {
	get_footer();
}

 /**
  * V2 MAIN - Structure for Category Archive page
  *
  * @param $kb_config
  */
function epkb_category_archive_v2( $kb_config ) {

	// setup hooks for the new layout
	add_action( 'eckb-categories-archive__body__left-sidebar', 'epkb_display_categories_sidebar', 10, 3 );
	add_action( 'eckb-categories-archive__body__content__body', 'epkb_main_content', 10, 3 );
	add_action( 'eckb-categories-archive__body__content__header', 'epkb_archive_header', 10, 3 );

	generate_archive_structure_css_v2( $kb_config );

	// add theme name to Div for specific targeting
	$activeWPTheme = EPKB_Utilities::get_active_theme_classes( 'cp' ); ?>

	<!--- Category Archive Version 2 --->

	<!-- Categories Archive Container -->
	<div id="eckb-categories-archive-container-v2" class="eckb-category-archive-reset eckb-categories-archive-container-v2 <?php echo esc_attr( $activeWPTheme ); ?>">

		<!-- Categories Archive Header -->
		<div id="eckb-categories-archive__header"><?php epkb_category_archive_section( 'eckb-categories-archive__header', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

		<!-- Categories Archive Body -->
		<div id="eckb-categories-archive__body">

			<!-- Categories Archive Body - Left Sidebar -->
			<div id="eckb-categories-archive__body__left-sidebar"><?php epkb_category_archive_section( 'eckb-categories-archive__body__left-sidebar', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

			<!-- Categories Archive Body - Content -->
			<div id="eckb-categories-archive__body__content">

				<!-- Categories Archive Body - Content - Header -->
				<div id="eckb-categories-archive__body__content__header"><?php epkb_category_archive_section( 'eckb-categories-archive__body__content__header', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

				<!-- Categories Archive Body - Content - Body -->
				<div id="eckb-categories-archive__body__content__body"><?php epkb_category_archive_section( 'eckb-categories-archive__body__content__body', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

				<!-- Categories Archive Body - Content - Footer -->
				<div id="eckb-categories-archive__body__content__footer"><?php epkb_category_archive_section( 'eckb-categories-archive__body__content__footer', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

			</div>

			<!-- Categories Archive Body - Right Sidebar -->
			<div id="eckb-categories-archive__body__right-sidebar"><?php epkb_category_archive_section( 'eckb-categories-archive__body__right-sidebar', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

		</div>

		<!-- Categories Archive Header -->
		<div id="eckb-categories-archive__footer"><?php epkb_category_archive_section( 'eckb-categories-archive__footer', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

	</div>      <?php
}

function generate_archive_structure_css_v2( $kb_config ) {
	// NEW ARCHIVE VERSION 2

	//This controls the whole width of the Archive page ( Left Sidebar / Content / Right Sidebar )
	$archive_container_width        = $kb_config['archive-container-width-v2'];
	$archive_container_width_units  = $kb_config['archive-container-width-units-v2'];

	// Left Sidebar Settings
	$archive_left_sidebar_width     = $kb_config['archive-left-sidebar-width-v2'];
	$archive_left_sidebar_padding   = $kb_config['archive-left-sidebar-padding-v2'];
	$archive_left_sidebar_bgColor   = $kb_config['archive-left-sidebar-background-color-v2'];

	// Content Settings
	$archive_content_width          = $kb_config['archive-content-width-v2'];
	$archive_content_padding        = $kb_config['archive-content-padding-v2'];
	$archive_content_bgColor        = $kb_config['archive-content-background-color-v2'];

	// Categories Archive Body - Content
	$archive_body_content_title_fontSize      = '35';
	$archive_body_content_article_fontSize    = '15';

	// Right Sidebar Settings
	$archive_right_sidebar_width     = 0; // no right sidebar for v2

	// Advanced
	$mobile_width                    = $kb_config['archive-mobile-break-point-v2'];
	
	if ( ! empty( $_REQUEST['epkb-editor-page-loaded'] ) && is_numeric( $mobile_width ) ) {
		$mobile_width -= 400;
	}
		
	$is_left_sidebar_on = $kb_config['kb_sidebar_location'] == 'left-sidebar' ||
						  $kb_config['kb_main_page_layout'] == EPKB_Layout::CATEGORIES_LAYOUT;

	$is_right_sidebar_on = $kb_config['kb_sidebar_location'] == 'right-sidebar';


	$archive_length = '';

	// Deal with Sidebar options.
	/**
	 *  Grid Columns start at lines.
	 *
	 *  Left Sidebar Grid Start:    1 - 2;
	 *  Content Grid Start:         2 - 3;
	 *  Left Sidebar Grid Start:    3 - 4;
	 */
	// If No Left Sidebar Expend the Article Content 1 - 3
	if ( ! $is_left_sidebar_on ) {
		$archive_length = '
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
					      grid-template-columns:  0 '.$archive_content_width.'% '.$archive_right_sidebar_width.'%;
					}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__left-sidebar {
					display:none;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__content {
					grid-column-start: 1;
					grid-column-end: 3;
				}
			';
	}

	/**
	 * If No Right Sidebar
	 *  - Expend the Article Content 2 - 4
	 *  - Make Layout 2 Columns only and use the Two remaining values
	 */
	if ( ! $is_right_sidebar_on ) {
		$archive_length = '
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
					      grid-template-columns: '.$archive_left_sidebar_width.'% '.$archive_content_width.'% 0 ;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__right-sidebar {
					display:none;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__content {
					grid-column-start: 2;
					grid-column-end: 4;
				}
			';
	}
	// If No Sidebars Expand the Article Content 1 - 4
	if ( ! $is_left_sidebar_on && ! $is_right_sidebar_on ) {
		$archive_length = '
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
					      grid-template-columns: 0 '.$archive_content_width.'% 0;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__left-sidebar {
					display:none;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__right-sidebar {
					display:none;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__content {
					grid-column-start: 1;
					grid-column-end: 4;
				}
			';
	}
	/**
	 * If Both Sidebars are active
	 *  - Make Layout 3 Columns and divide their sizes according to the user settings
	 */
	if ( $is_left_sidebar_on && $is_right_sidebar_on ) {
		$archive_length = '
					#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
					      grid-template-columns: ' . $archive_left_sidebar_width . '% ' . $archive_content_width . '% ' . $archive_right_sidebar_width . '%;
					}
					';
	}	?>

	<!-- archive Version 2 Style -->
	<style><?php
		ob_start();
		echo esc_attr( $archive_length );   ?>
		#eckb-categories-archive-container-v2 {
			width:<?php echo esc_attr( $archive_container_width.$archive_container_width_units ); ?>;
		}
		#eckb-categories-archive-container-v2 #eckb-categories-archive__body__left-sidebar {
			padding: <?php echo esc_attr( $archive_left_sidebar_padding . 'px' ); ?>;
			background-color: <?php echo esc_attr( $archive_left_sidebar_bgColor ); ?>
		}
		#eckb-categories-archive-container-v2 #eckb-categories-archive__body__content {
			padding: <?php echo esc_attr( $archive_content_padding . 'px' ); ?>;
			background-color: <?php echo esc_attr( $archive_content_bgColor ); ?>
		}
		/* Right Sidebar */
		/*#eckb-categories-archive-container-v2 #eckb-categories-archive__body__right-sidebar {
			padding: <?php //echo esc_attr( $archive_right_sidebar_padding.'px' ); ?>;
			background-color: <?php //echo esc_attr( $archive_right_sidebar_bgColor ); ?>
		}*/
		/* Categories Archive Body - Content ----------------------------------------*/
		#eckb-categories-archive-container-v2 .eckb-category-archive-title h1 {
			font-size: <?php echo esc_attr( $archive_body_content_title_fontSize . 'px' ); ?>;
		}
		#eckb-categories-archive-container-v2 .eckb-article-container {
			font-size: <?php echo esc_attr( $archive_body_content_article_fontSize . 'px' ); ?>;
		}


		/* Media Queries ------------------------------------------------------------*/
		/* Grid Adjust Column sizes for smaller screen */
		/*@media only screen and ( max-width: <?php //echo esc_attr( $tablet_width ); ?>px ) {
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
				grid-template-columns: 20% 60% 20%;
			}
		}*/

		/* Grid becomes 1 Column Layout */
		@media only screen and ( max-width: <?php echo esc_attr( $mobile_width ); ?>px ) {

			#eckb-categories-archive-container-v2 {
				width:100%;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
				grid-template-columns: 0 100% 0;
			}

			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__content {
				grid-column-start: 1;
				grid-column-end: 4;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__left-sidebar {
				grid-column-start: 1;
				grid-column-end: 4;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__right-sidebar {
				grid-column-start: 1;
				grid-column-end: 4;
			}
		}   <?php
		$archive_styles = ob_get_clean();

		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo EPKB_Utilities::minify_css( $archive_styles );
	?></style>  <?php
}

 /**
  * V2 - Call all hooks for given Category section.
  *
  * @param $hook - both hook name and div id
  * @param $args
  */
 function epkb_category_archive_section( $hook, $args ) {
	 do_action( $hook, $args );
 }

 /**
  * V2 - FIRST display archive header
  *
  * @param $args
  * @noinspection PhpUnused*/
 function epkb_archive_header( $args ) {
	 $category_archive_title_icon = 'epkbfa epkbfa-folder-open';
	 $category_title = single_cat_title( '', false );
	 $category_title = empty( $category_title ) ? '' : $category_title;    ?>

	 <header class="eckb-category-archive-header">
		 <div class="eckb-category-archive-title">
			 <h1>
				 <span class="eckb-category-archive-title-icon <?php esc_attr_e( $category_archive_title_icon ); ?>"></span>
				 <span class="eckb-category-archive-title-desc"><?php echo esc_html( $args['config']['template_category_archive_page_heading_description'] ); ?></span>
				 <span class="eckb-category-archive-title-name"><?php echo esc_html( $category_title ); ?></span>
			 </h1>
		 </div>            <?php

		 epkb_archive_category_description();
		 epkb_archive_category_breadcrumb( $args['config'] );     ?>
	 </header>   <?php
 }

 /**
  * V2 - SECOND display main content
  *
  * @param $args
  * @noinspection PhpUnused*/
function epkb_main_content( $args ) {
	global $epkb_password_checked;

	$kb_config                   = $args['config'];
	$kb_id                       = $kb_config['id'];
	$read_more                   = $kb_config['template_category_archive_read_more'];
	$read_more_icon              = 'epkbfa epkbfa-long-arrow-right';
	$preset_style                = $kb_config['template_category_archive_page_style'];

	$meta_date_text              = $kb_config['template_category_archive_date'];
	$meta_author_text            = $kb_config['template_category_archive_author'];
	$meta_categories_text        = $kb_config['template_category_archive_categories'];

	$meta_date_on                = $kb_config['template_category_archive_date_on'];
	$meta_author_on              = $kb_config['template_category_archive_author_on'];
	$meta_categories_on          = $kb_config['template_category_archive_categories_on'];
	$include_children            = $kb_config['archive-show-sub-categories'] == 'on';

	// if category has no article then show proper message
	if ( ! have_posts() ) {
		echo '<main class="eckb-category-archive-main ' . esc_attr( $preset_style ) . '"><p>' . esc_html( $args['config']['category_empty_msg'] ) . '</p></main>';
		return;
	}

	// category and article sequence
	$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
	$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );

	// for WPML filter categories and articles given active language
	if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
		$category_seq_data = EPKB_WPML::apply_category_language_filter( $category_seq_data );
		$articles_seq_data = EPKB_WPML::apply_article_language_filter( $articles_seq_data );
	} ?>

	<main class="eckb-category-archive-main <?php esc_attr_e($preset_style); ?>">   <?php

		$term = get_queried_object();
		$category_id = $term->term_id;

		// if this is Category Archive page for Categories (not tags) then order articles by configured order
		if ( ! empty( $term ) && get_class( $term ) == 'WP_Term' && $term->taxonomy == EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) ) {
			$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

			$articles_sequence = $kb_config['articles_display_sequence'];

			if ( $articles_sequence == 'alphabetical-title' ) {

				$query_args = array(
					'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
					'orderby' => 'title',
					'order' => 'ASC',
					'paged'         => $paged, 
					'tax_query' => array(
						array(
							'taxonomy' => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ),
							'terms' => $category_id,
							'include_children' => $include_children,
						)
					)
				);

			} else if ( $articles_sequence == 'user-sequenced' ) {

				// articles with no categories - temporary add one
				if ( isset($articles_seq_data[0]) ) {
					$category_seq_data[0] = array();
				}

				// get category and sub-category ids
				$category_array = isset($category_seq_data[$category_id]) ? $category_seq_data[$category_id] : array();
				$category_ids = array_merge( [ $category_id ], epkb_get_array_keys_multi( $category_array ) );

				// retrieve articles belonging to given (sub) category if any
				$category_article_ids = array();
				foreach( $category_ids as $cat_id ) {
					if ( ! empty($articles_seq_data[$cat_id]) ) {
						foreach( $articles_seq_data[$cat_id] as $key => $value ) {
							if ( $key > 1 ) {
								$category_article_ids[] = $key;
							}
						}
					}
				}

				$query_args = array(
					'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
					'orderby' => 'post__in',
					'post__in' => $category_article_ids,
					'paged'         => $paged, 
					'tax_query' => array(
						array(
							'taxonomy' => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ),
							'terms' => $category_id,
							'include_children' => $include_children,
						)
					)
				);

			// ordered by date
			} else {

				$query_args = array(
					'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
					'orderby' => 'date',
					'order' => 'DESC',
					'paged'         => $paged, 
					'tax_query' => array(
						array(
							'taxonomy' => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ),
							'terms' => $category_id,
							'include_children' => $include_children,
						)
					)
				);
			}

			query_posts( $query_args );
		}

		while ( have_posts() ) {

			the_post();

			$post_id = get_the_ID();

			// Future Options
			$post = get_post( $post_id );
			$post_date = sprintf( '<time class="entry-date" datetime="%1$s">%2$s</time>', esc_attr( get_the_date( DATE_W3C, $post ) ), esc_html(get_the_date( '', $post )) );
			
			// linked articles have their own icon
			$article_title_icon = 'epkbfa-file-text-o';
			if ( has_filter('eckb_single_article_filter' ) ) {
				$article_title_icon = apply_filters( 'eckb_article_icon_filter', $article_title_icon, $post->ID );
				$article_title_icon = empty( $article_title_icon ) ? 'epkbfa-file-text-o' : $article_title_icon;
			}

			$new_tab = '';
			if ( has_filter('eckb_link_newtab_filter' ) ) {
				$new_tab = apply_filters( 'eckb_link_newtab_filter', $post->ID );
			}
			$new_tab = ! empty( $new_tab );

			$article_link = get_permalink( $post_id );
			if ( ! has_filter( 'article_with_seq_no_in_url_enable' ) ) {
				$seq_no       = epkb_get_article_seq_no( $post_id, $category_id, $category_seq_data, $articles_seq_data );
				$article_link = $seq_no == 1 ? $article_link : add_query_arg( 'seq_no', $seq_no, $article_link );
			} ?>

			<article class="eckb-article-container" id="post-<?php the_ID(); ?>">
				<div class="eckb-article-image">
					<?php the_post_thumbnail(); ?>
				</div>
				<div class="eckb-article-header">
					<div class="eckb-article-title">
						<h2><a href="<?php echo esc_url( $article_link ); ?>" <?php echo $new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>><?php the_title(); ?></a></h2>
						<span class="eckb-article-title-icon epkbfa <?php esc_attr_e( $article_title_icon ); ?>"></span>
					</div><?php
                    if ( $meta_date_on == 'on' || $meta_author_on == 'on' || $meta_categories_on == 'on' ) { ?>
                        <div class="eckb-article-metadata">
                            <ul><?php if ( $meta_date_on == 'on' ) { ?>
                                    <li class="eckb-article-posted-on"><span class="eckb-article-meta-name"><?php echo esc_html( $meta_date_text ) . '</span> ' . wp_kses_post( $post_date ); ?></li><?php
                                } if ( $meta_author_on == 'on' ) { ?>
                                    <li class="eckb-article-byline"><span class="eckb-article-meta-name"><?php echo esc_html( $meta_author_text ) . '</span> ' . esc_html( get_the_author() ); ?></li><?php
                                } if ( $meta_categories_on == 'on' ) { ?>
                                    <li class="eckb-article-categories"><span class="eckb-article-meta-name"><?php echo esc_html( $meta_categories_text ) . '</span> ' . wp_kses_post( get_the_category_list(', ' ) ); ?></li><?php
                                } ?>
                            </ul>
                        </div><?php
                    } ?>
				</div>
				<div class="eckb-article-body">					    <?php

					if ( post_password_required() ) {
						//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo get_the_password_form();
					} else {
						$epkb_password_checked = true;

						if ( has_excerpt( $post_id ) ) {
							echo esc_html( get_the_excerpt( $post_id ) );
						} ?>

						<a href="<?php echo esc_url( $article_link ); ?>" class="eckb-article-read-more">
							<div class="eckb-article-read-more-text"><?php echo esc_html( $read_more ); ?></div>
							<div class="eckb-article-read-more-icon <?php echo esc_html( $read_more_icon ); ?>"></div>
						</a>    <?php
					}	    ?>

				</div>
				<div class="eckb-article-footer"></div>
			</article>			    <?php

		}

		the_posts_pagination(
			array(
				'prev_text'          => esc_html__( 'Previous', 'echo-knowledge-base' ),
				'next_text'          => esc_html__( 'Next', 'echo-knowledge-base' ),
				'before_page_number' => '<span>' . esc_html__( 'Page', 'echo-knowledge-base' ) . ' </span>',
			)
		);
			
		wp_reset_postdata();			?>

	</main> <?php
}


 /**
  * Function to flatten array
  * @param array $category_array
  * @return array
  */
 function epkb_get_array_keys_multi( array $category_array ) {
	 $keys = array();

	 foreach ($category_array as $key => $value) {
		 $keys[] = $key;

		 if ( is_array($value) ) {
			 $keys = array_merge($keys, epkb_get_array_keys_multi($value));
		 }
	 }

	 return $keys;
 }

 /**
  * V2 -THIRD display category list
  *
  * @param $args
  *
  * @noinspection PhpUnused*/
 function epkb_display_categories_sidebar($args ) {

	 // for Category Focused Layout show sidebar with list of top-level categories
	 if ( $args['config']['kb_main_page_layout'] != EPKB_Layout::CATEGORIES_LAYOUT ) {
	 	return;
	 }

	// find parent ID
	$parent_category_id = 0;
	$active_id = 0;
	$breadcrumb_tree = EPKB_Templates_Various::get_term_breadcrumb( $args['config'], get_queried_object()->term_id );
	
	$breadcrumb_tree[] = get_queried_object()->term_id;
	
	if ( $args['config']['categories_layout_list_mode'] == 'list_top_categories' ) {
		if ( isset( $breadcrumb_tree[0] ) ) {
			$active_id = $breadcrumb_tree[0];
		}
	} else {		
		$tree_count = count( $breadcrumb_tree );
		if ( $tree_count > 1 ) {
			$parent_category_id = $breadcrumb_tree[$tree_count - 2];
			$active_id = $breadcrumb_tree[$tree_count - 1];
		}
		
		if ( $tree_count == 1 ) {
			$active_id = get_queried_object()->term_id;
		}
	}

	 //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo EPKB_Layout_Category_Sidebar::get_layout_categories_list( $args['id'], $args['config'], $parent_category_id, $active_id );
 }

 /**
  * Output breadcrumb
  * @param $kb_config
  */
 function epkb_archive_category_breadcrumb( $kb_config ) {

	 if ( $kb_config['breadcrumb_enable']  == 'on' ) {

		 $term = get_queried_object();
		 if ( empty($term) || ! $term instanceof WP_Term ) {
			 return;
		 }	?>

		 <div class="eckb-category-archive-breadcrumb">	<?php
			 EPKB_Templates::get_template_part( 'feature', 'breadcrumb', $kb_config, $term ); ?>
		 </div>	<?php
	 }
 }

 /**
  * Output term description
  */
 function epkb_archive_category_description() {

	 $term = get_queried_object();
	 if ( empty($term) || ! $term instanceof WP_Term ) {
		 return;
	 }

	 $term_description = get_term_field( 'description', $term );
	 if ( empty( $term_description ) || is_wp_error( $term_description)  ) {
		 return;
	 }

	 echo '<div class="eckb-category-archive-description">' . wp_kses_post( $term_description ) . '</div>';
 }

 /**
  * Get article sequence no for breadcrumbs based on categories and articles sequence
  *
  * @param $article_id
  * @param $category_id
  * @param $category_seq_data
  * @param $articles_seq_data
  * @param int $seq_no
  * @return int
  */
function epkb_get_article_seq_no( $article_id, $category_id, $category_seq_data, $articles_seq_data, $seq_no = 1 ) {

	// go out from recursion if something went wrong
	if ( $seq_no > 6 ) {
		return 1;
	}

	// check if the data have right format
	if ( ! is_array( $category_seq_data ) ) {
		return $seq_no;
	}

	foreach ( $category_seq_data as $cat_id => $subcategory_seq_data ) {

		if ( $cat_id == $category_id ) {
			return $seq_no;
		}

		if ( ! empty( $articles_seq_data[ $cat_id ] ) && ! empty ( $articles_seq_data[ $cat_id ][ $article_id ] ) ) {
			$seq_no++;
		}

		if ( count( $subcategory_seq_data ) ) {
			$seq_no = epkb_get_article_seq_no( $article_id, $category_id, $subcategory_seq_data, $articles_seq_data, $seq_no );
		}
	}

	return $seq_no;
}