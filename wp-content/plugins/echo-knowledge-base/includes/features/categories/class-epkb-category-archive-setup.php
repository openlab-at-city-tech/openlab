<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * V3 Template for KB Category Archive Page front-end setup
 */
class EPKB_Category_Archive_Setup {

    /**
     * Generate Category Archive Page based on selected preset.
     */
	public static function get_category_archive_page_v3( $kb_config ) {

		// setup archive structure
		self::setup_archive_hooks( $kb_config );

		// define container classes
		$archive_page_container_classes = apply_filters( 'eckb-archive-page-container-classes', array(), $kb_config['id'], $kb_config );  // used for old Widgets KB Sidebar
		$archive_page_container_classes = isset( $archive_page_container_classes ) && is_array( $archive_page_container_classes ) ? $archive_page_container_classes : array();
		if ( ! empty( $kb_config['theme_name'] ) ) {
			$archive_page_container_classes[] = 'eckb-theme-' . $kb_config['theme_name'];
		}

		// add theme name to Div for specific targeting
		$activeWPTheme = EPKB_Utilities::get_active_theme_classes( 'cp' );

		$mobile_breakpoint = '768';
		if ( is_numeric( $mobile_breakpoint ) && ! empty( $_REQUEST['epkb-editor-page-loaded'] ) ) {
			$mobile_breakpoint -= 400;
		} ?>

		<div id="eckb-archive-page-container" class="<?php echo esc_attr( implode( " ", $archive_page_container_classes ) . ' ' . $activeWPTheme ); ?> eckb-archive-page-design-1" data-mobile_breakpoint="<?php echo esc_attr( $mobile_breakpoint ); ?>">    <?php

		   self::archive_section( 'eckb-archive-header', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?>

			<div id="eckb-archive-body">  <?php

		        self::archive_section( 'eckb-archive-left-sidebar', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?>

		        <div id="eckb-archive-content">                        <?php

					self::archive_section( 'eckb-archive-content-header', array( 'id' => $kb_config['id'], 'config' => $kb_config ) );
					self::archive_section( 'eckb-archive-content-body', array( 'id' => $kb_config['id'], 'config' => $kb_config ) );
					self::archive_section( 'eckb-archive-content-footer', array( 'id' => $kb_config['id'], 'config' => $kb_config ) );                        ?>

		        </div><!-- /#eckb-archive-content -->     <?php

		        self::archive_section( 'eckb-archive-right-sidebar', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?>

			</div><!-- /#eckb-archive-body -->              <?php

			self::archive_section( 'eckb-archive-footer', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?>

		</div><!-- /#eckb-archive-page-container -->    <?php
	}

	/**
	 * REGISTER all archive hooks we need
	 *
	 * @param $kb_config
	 */
	private static function setup_archive_hooks( $kb_config ) {

		// A. ARCHIVE PAGE HEADER
		add_action( 'eckb-archive-header', array( 'EPKB_Category_Archive_Setup', 'search_box' ) );

		// B. ARCHIVE CONTENT HEADER
		add_action( 'eckb-archive-content-header', array( 'EPKB_Category_Archive_Setup', 'breadcrumb' ), 9 );
		add_action( 'eckb-archive-content-header', array( 'EPKB_Category_Archive_Setup', 'category_header' ), 9 );

		// C. SIDEBARS + ARCHIVE CONTENT BODY
		add_action( 'eckb-archive-content-body', array( 'EPKB_Category_Archive_Setup', 'archive_content_body' ), 10 );

		// Sidebar
		add_action( 'eckb-archive-left-sidebar', array( 'EPKB_Category_Archive_Setup', 'display_nav_sidebar_left' ), 10 );
		add_action( 'eckb-archive-right-sidebar', array( 'EPKB_Category_Archive_Setup', 'display_nav_sidebar_right' ), 10 );

		// D. ARCHIVE CONTENT FOOTER
		//add_action( 'eckb-archive-content-footer', array('EPKB_Category_Archive_Setup', 'prev_next_navigation'), 99 );
	}


	/***********************   A. ARCHIVE PAGE HEADER   *********************/

	/**
	 * Search Box
	 *
	 * @param $args
	 */
	public static function search_box( $args ) {

		// SEARCH BOX OFF: no search box if Archive Page search is off
		if ( $args['config']['archive_search_toggle'] == 'off' ) {
			return;
		}

		EPKB_Modular_Main_Page::search_module( $args['config'] );
	}


	/***********************   B. ARCHIVE CONTENT HEADER  *********************/

	public static function category_header( $args ) {

		$term = EPKB_Utilities::get_current_term();
		if ( empty( $term ) ) {
			return;
		}

		$category_title = single_cat_title( '', false );
		$category_title = empty( $category_title ) ? '' : $category_title;
		$categories_icons = EPKB_KB_Config_Category::get_category_data_option( $args['config']['id'] );
		$category_icon = EPKB_KB_Config_Category::get_category_icon( $term->term_id, $categories_icons );     ?>

		<header class="eckb-category-archive-header">

			<div class="eckb-category-archive-title-container">
				<h1 class="eckb-category-archive-title">
					<span class="eckb-category-archive-title-icon"> <?php
						if ( $category_icon['type'] == 'image' ) { ?>
							<img class="eckb-category-archive-title-icon--image" src="<?php echo esc_url( $category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $category_icon['image_alt'] ); ?>">  <?php
						} else { ?>
							<span class="eckb-category-archive-title-icon--font epkbfa <?php echo esc_attr( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $category_icon['name'] ); ?>"></span>	<?php
						} ?>
					</span>

					<span class="eckb-category-archive-title-name"><?php echo esc_html( $args['config']['archive_category_name_prefix'] ) . ' ' . esc_html( $category_title ); ?></span>
				</h1>
			</div>            <?php

			if ( $args['config']['archive_category_desc_toggle'] == 'on' ) {
				$term_description = get_term_field( 'description', $term );
				if ( ! is_wp_error( $term_description ) && ! empty( $term_description ) ) {
					echo '<div class="eckb-category-archive-description">' . wp_kses_post( $term_description ) . '</div>';
				}
			}   ?>

		</header>   <?php
	}

	public static function breadcrumb( $args ) {

		if ( $args['config']['breadcrumb_enable']  != 'on' ) {
			return;
		}

		$term = EPKB_Utilities::get_current_term();
		if ( empty( $term ) ) {
			return;
		}

		echo '<div id="eckb-archive-content-breadcrumb-container">';
		EPKB_Templates::get_template_part( 'feature', 'breadcrumb', $args['config'], $term );
		echo '</div>';
	}

	/***********************   C. ARCHIVE CONTENT BODY   *********************/

	public static function archive_content_body( $args ) {


		$kb_config = $args['config'];

		// category and article sequence
		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		$category_seq_data = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$category_seq_data = EPKB_WPML::apply_category_language_filter( $category_seq_data );
			$articles_seq_data = EPKB_WPML::apply_article_language_filter( $articles_seq_data );
		}

		$term = EPKB_Utilities::get_current_term();
		if ( empty( $term ) ) {
			return;
		}

		$articles_list = array();
		if ( isset( $articles_seq_data[$term->term_id] ) ) {
			$articles_list = $articles_seq_data[$term->term_id];
			unset( $articles_list[0] );
			unset( $articles_list[1] );
		}
		$nof_articles_displayed = $kb_config['archive_content_articles_nof_articles_displayed'];
		$total_articles = count( $articles_list );      ?>

		<main class="eckb-category-archive-main">   <?php

			// show list of articles if any
			if ( $total_articles > 0 ) {

				// Show title for the list of articles
				$archive_content_articles_list_title = $kb_config['archive_content_articles_list_title'];
				if ( ! empty( $archive_content_articles_list_title ) ) {
					echo '<h2 class="eckb-category-archive-articles-list-title">' . esc_html( $archive_content_articles_list_title ) . '</h2>';
				}

				$nof_columns = $kb_config['archive_content_articles_nof_columns'];

				// Remove the borders of the last row of articles
				$article_list_separator_classes = '';
				if (  $kb_config['archive_content_articles_separator_toggle'] === 'on' ) {
					$article_list_separator_classes .= 'eckb-article-list--separator';
				}   ?>

				<div class="eckb-article-list-container eckb-article-list-container-columns-<?php echo esc_attr( $nof_columns . ' ' . $article_list_separator_classes ); ?>"> <?php

					self::display_category_articles( $kb_config, $articles_list, $kb_config['archive_content_articles_nof_articles_displayed'] );

					if ( $total_articles > $nof_articles_displayed ) { ?>
						<div class="eckb-article-list-show-more-container">
							<div class="eckb-article-list-article-count">+ <?php echo esc_html( $total_articles - $nof_articles_displayed ) . ' ' . esc_html__( 'Articles', 'echo-knowledge-base' ); ?> </div>
							<div class="eckb-article-list-show-all-link"><?php echo esc_html( $kb_config['show_all_articles_msg'] ); ?></div>
						</div>  <?php
					} ?>

				</div> <?php
			}

			if ( $kb_config['archive_content_sub_categories_toggle'] == 'on' ) {

				$sub_categories = EPKB_Categories_DB::get_sub_categories( $category_seq_data, $term->term_id  );
				if ( empty( $sub_categories ) ) {

					if ( $total_articles == 0 ) {
						echo '<div class="epkb-articles-coming-soon">' . esc_html( $kb_config['category_empty_msg'] ) . '</div>';
					}

					return;
				}

				// Show title for the Sub Category list of articles
				if ( $total_articles > 0 && ! empty( $kb_config['archive_content_sub_categories_title'] ) && count( $sub_categories ) > 0 ) {
					echo '<h2 class="eckb-category-archive-sub-category-list-title">' . esc_html( $kb_config['archive_content_sub_categories_title'] ) . '</h2>';
				} ?>

				<div class="eckb-sub-category-list-container">  <?php
					self::show_sub_categories( $kb_config, $articles_seq_data, $sub_categories ); ?>
				</div>  <?php
			}

			wp_reset_postdata();			?>

		</main> <?php
	}

	/**
	 * Display category articles
	 */
	private static function display_category_articles( $kb_config, $articles_list, $nof_articles_displayed ) {

		$nof_articles = 0;
		foreach ( $articles_list as $article_id => $article_title ) {

			if ( ! EPKB_Utilities::is_article_allowed_for_current_user( $article_id ) ) {
				continue;
			}

			$nof_articles++;
			$hide_class = $nof_articles > $nof_articles_displayed ? ' epkb-hide-elem' : '';

			self::display_article( $kb_config, $article_id, $hide_class );
		}
	}

	private static function display_article( $kb_config, $article_id, $article_class ) {

		$article = get_post( $article_id );
		$inline_style_escaped = EPKB_Utilities::get_inline_style( 'padding-bottom:: article_list_spacing,padding-top::article_list_spacing', $kb_config ); ?>

		<div class="eckb-article-container<?php echo esc_attr( $article_class ); ?>" id="post-<?php echo esc_attr( $article_id ); ?>">

			<div class="eckb-article-header" <?php echo $inline_style_escaped; ?> >   <?php
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				EPKB_Utilities::get_single_article_link( $kb_config, $article->post_title, $article_id, 'Category_Archive_Page' ) ?>
			</div> <?php

			$articles_display_mode = empty( $kb_config['archive_content_articles_display_mode'] ) ? 'title' : $kb_config['archive_content_articles_display_mode'];
			$articles_display_mode = $articles_display_mode == 'title_excerpt' && ! has_excerpt( $article_id ) ? 'title_content' : $articles_display_mode;

			if ( $articles_display_mode !== 'title' ) {     ?>
				<div class="eckb-article-body"> <?php
					if ( $articles_display_mode == 'title_excerpt' ) {
						echo esc_html( wp_strip_all_tags( get_the_excerpt( $article ) ) );
					} else if ( $articles_display_mode == 'title_content' ) {
						echo esc_html( wp_strip_all_tags( wp_trim_excerpt( '', $article ) ) );
					}   ?>
				</div>
				<div class="eckb-article-footer"></div>	<?php
			}   ?>

		</div>	<?php
	}

	private static function show_sub_categories( $kb_config, $articles_seq_data, $sub_categories ) {

		$nof_columns = $kb_config['archive_content_sub_categories_nof_columns'];
		$rows_number = (int)ceil( count( $sub_categories ) / $nof_columns );
		$categories_icons = EPKB_KB_Config_Category::get_category_data_option( $kb_config['id'] );

		// show sub-categories in rows and columns
		for ( $i = 0; $i < $rows_number; $i ++ ) {

			$sub_categories_in_row = array_slice( $sub_categories, $i * $nof_columns, $nof_columns, true );    ?>
			<div class="eckb-sub-category-row eckb-sub-category-row-columns-<?php echo esc_attr( $nof_columns ); ?>"> <?php

				foreach ( $sub_categories_in_row as $sub_category_id => $child_categories ) {

					$sub_category_name = isset( $articles_seq_data[ $sub_category_id ][0] ) ? $articles_seq_data[ $sub_category_id ][0] : '';
					$sub_category_desc = isset( $articles_seq_data[ $sub_category_id ][1] ) && $kb_config['archive_category_desc_toggle'] == 'on' ? $articles_seq_data[ $sub_category_id ][1] : '';
					$category_link = EPKB_Utilities::get_term_url( $sub_category_id );
					$category_icon = EPKB_KB_Config_Category::get_category_icon( $sub_category_id, $categories_icons );
                    $sub_categories_articles_count = 0;

					$articles_list = array();
					if ( isset( $articles_seq_data[$sub_category_id] ) ) {
						$articles_list = $articles_seq_data[$sub_category_id];
						unset( $articles_list[0] );
						unset( $articles_list[1] );
					}

					$show_count = count( $articles_list ) - $kb_config['archive_content_sub_categories_nof_articles_displayed'];

                    if ( count( $child_categories ) > 0 ) {
                        foreach ( $child_categories as $term_id => $sub_category ) {
                            $term = get_term( $term_id );
                            if ( ! is_wp_error(  $term ) && ! empty( $term ) && $term->count > 0 ) {
                                $sub_categories_articles_count += $term->count;
                            }
                        }
                    }

					// show both sub-categories AND articles
					if ( $kb_config['archive_content_sub_categories_with_articles_toggle'] == 'on' ) {    ?>
						<div id="eckb-sub-category-id-<?php echo esc_attr( $sub_category_id ); ?>" class="eckb-sub-category-container">

							<div class="eckb-sub-category-header">
								<div class="epkb-sub-category-inner">				<?php
									if ( $kb_config['archive_content_sub_categories_icon_toggle'] == 'on' ) { ?>
										<span class="eckb-sub-category-icon"> <?php
											if ( $category_icon['type'] == 'image' ) { ?>
												<img class="eckb-sub-category-icon--image" src="<?php echo esc_url( $category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $category_icon['image_alt'] ); ?>">  <?php
											} else { ?>
												<span class="eckb-sub-category-icon--font epkbfa <?php echo esc_attr( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $category_icon['name'] ); ?>"></span>	<?php
											} ?>
										</span>							<?php
									} ?>
									<a class="eckb-sub-category-title" href="<?php echo esc_url( $category_link ); ?>"><?php echo esc_html( $sub_category_name ); ?></a>
								</div>
							</div>

							<div class="eckb-sub-category-body"> <?php
								if ( ! empty( $sub_category_desc ) ) { ?>
									<div class="eckb-sub-category-description"><?php echo wp_kses_post( $sub_category_desc ); ?></div>  <?php
								}

								if ( count( $articles_list ) > 0 ) { ?>
								    <div class="eckb-sub-category-article-list-container">   <?php
										self::display_category_articles( $kb_config, $articles_list, $kb_config['archive_content_sub_categories_nof_articles_displayed'] ); ?>
								    </div>  <?php
								} else if ( count( $child_categories ) == 0 ) {    ?>
									<div class="epkb-articles-coming-soon"><?php echo esc_html( $kb_config['category_empty_msg'] ); ?></div> <?php
								}	?>
							</div>                            <?php

							if ( $show_count > 0 || $sub_categories_articles_count > 0 ) { ?>
                                <div class="eckb-sub-category-footer">                                    <?php
	                                if ( $show_count > 0 ) { ?>
                                        <div class="eckb-sub-category-article-count">+ <?php echo esc_html( $show_count ) . ' ' . esc_html__( 'Articles', 'echo-knowledge-base' ) ?> </div>                                    <?php
									} ?>
                                    <a class="eckb-sub-category-show-all-link" href="<?php echo esc_url( $category_link ); ?>"><?php echo esc_html(  ( $show_count > 0 ) ? $kb_config['show_all_articles_msg'] : $kb_config['sidebar_show_sub_category_articles_msg'] ); ?></a>
                                </div>                            <?php
							} ?>
						</div>							<?php

					// show only sub-categories
					} else {    ?>
						<a class="eckb-sub-category-container" href="<?php echo esc_url( $category_link ); ?>">
							<span class="eckb-sub-category-header">
								<span class="epkb-sub-category-inner">				<?php
									if ( $kb_config['archive_content_sub_categories_icon_toggle'] == 'on' ) { ?>
										<span class="eckb-sub-category-icon"> <?php
											if ( $category_icon['type'] == 'image' ) { ?>
												<img class="eckb-sub-category-icon--image" src="<?php echo esc_url( $category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $category_icon['image_alt'] ); ?>">  <?php
											} else { ?>
												<span class="eckb-sub-category-icon--font epkbfa <?php echo esc_attr( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $category_icon['name'] ); ?>"></span>	<?php
											} ?>
										</span>							<?php
									} ?>
									<span class="eckb-sub-category-title"><?php echo esc_html( $sub_category_name ); ?></span>
								</span>
								<span class="eckb-category-archive-arrow epkbfa epkbfa-arrow-right"></span>
							</span> <?php

							if ( ! empty( $sub_category_desc ) ) { ?>
								<span class="eckb-sub-category-body">
									<span class="eckb-sub-category-description"><?php echo wp_kses_post( $sub_category_desc ); ?></span>
								</span> <?php
							} ?>
						</a>    <?php
					}
				}  ?>

			</div> <?php
		}
	}

	/******************************************************************************
	 *
	 *  SIDEBARS
	 *
	 ******************************************************************************/

	private static function is_left_sidebar_on( $kb_config ) {
		return $kb_config['archive_left_sidebar_toggle'] != 'off';
	}

	private static function is_right_sidebar_on( $kb_config ) {
		return $kb_config['archive_right_sidebar_toggle'] != 'off';
	}

	/**
	 * Display LEFT navigation Sidebar
	 * @param $args
	 */
	public static function display_nav_sidebar_left( $args ) {

		$kb_config = $args['config'];

		// TODO: new feature - 3 positions where user can choose Navigation, Recent or Popular Articles

		// Position 1
		if ( $kb_config['archive-left-sidebar-position-1'] != 'none' ) {
			if ( $kb_config['archive-left-sidebar-position-1'] == 'navigation' ) {
				self::get_navigation( $args );
			}
		}

		// Position 2
		/* if ( $kb_config['archive-left-sidebar-position-2'] != 'none' ) {
		} */

		// Position 3
		/* if ( $kb_config['archive-left-sidebar-position-3'] != 'none' ) {
		} */
	}

	/**
	 * Display RIGHT navigation Sidebar
	 * @param $args
	 */
	public static function display_nav_sidebar_right( $args ) {
		$kb_config = $args['config'];

		// TODO: new feature - 3 positions where user can choose Navigation, Recent or Popular Articles

		// Position 1
		if ( $kb_config['archive-right-sidebar-position-1'] != 'none' ) {
			if ( $kb_config['archive-right-sidebar-position-1'] == 'navigation' ) {
				self::get_navigation( $args );
			}
		}

		// Position 2
		/* if ( $kb_config['archive-right-sidebar-position-2'] != 'none' ) {
		} */

		// Position 3
		/* if ( $kb_config['archive-right-sidebar-position-3'] != 'none' ) {
		} */
	}

	private static function get_navigation( $args ) {

		$navigation_type = $args['config']['archive_sidebar_navigation_type'];

		// Categories Focused Layout navigation
		if ( $navigation_type == 'navigation-categories' ) {
			self::display_categories_sidebar( $args['config'] );

		// Current category and everything below
		} else if ( $navigation_type == 'navigation-current-category' ) {

			$core_nav_sidebar = new EPKB_Layout_Article_Sidebar();
			$core_nav_sidebar->display_article_sidebar( $args['config'], true );

		// Elegant Layouts Article-like Navigation
		} else if ( $navigation_type == 'navigation-all-categories' && EPKB_Utilities::is_elegant_layouts_enabled() ) {
			do_action( 'eckb-article-v2-elay_sidebar', $args );

		// core Article-like Navigation
		} else if ( $navigation_type == 'navigation-all-categories' ) {
			$core_nav_sidebar = new EPKB_Layout_Article_Sidebar();
			$core_nav_sidebar->display_article_sidebar( $args['config'] );
		}
	}

	/**
	 * For Category Focused Layout show top level or sibling categories in the left sidebar
	 *
	 * @param $kb_config
	 */
	private static function display_categories_sidebar( $kb_config ) {

		$term = EPKB_Utilities::get_current_term();
		if ( empty( $term ) ) {
			return;
		}

		// find parent ID
		$parent_category_id = 0;
		$active_id = 0;
		$breadcrumb_tree = EPKB_Templates_Various::get_term_breadcrumb( $kb_config, $term->term_id );
		$breadcrumb_tree[] = $term->term_id;

		if ( $kb_config['categories_layout_list_mode'] == 'list_top_categories' ) {
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
				$active_id = $term->term_id;
			}
		}

		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo EPKB_Layout_Category_Sidebar::get_layout_categories_list( $kb_config['id'], $kb_config, $parent_category_id, $active_id );
	}


	/******************************************************************************
	 *
	 *  OTHER UTILITIES
	 *
	 ******************************************************************************/

	/**
	 * Output section container + trigger hook to output the section content.
	 *
	 * @param $hook - both hook name and div id
	 * @param $args
	 */
	public static function archive_section( $hook, $args ) {

	   echo '<div id="' . esc_attr( $hook ) . '">';

		if ( self::is_hook_enabled( $args['config'], $hook ) ) {
			do_action( $hook, $args );
		}

		echo '</div>';
	}

	/**
	 * Hooks in Sidebar belong to either left or right sidebar. If sidebar is disabled then it is not invoked.
	 *
	 * @param $kb_config
	 * @param $hook
	 * @return bool
	 */
	private static function is_hook_enabled( $kb_config, $hook ) {

		// do not output left and/or right sidebar if not configured
		if ( $hook == 'eckb-archive-left-sidebar' && ! self::is_left_sidebar_on( $kb_config ) ) {
			return false;
		}
		if ( $hook == 'eckb-archive-right-sidebar' && ! self::is_right_sidebar_on( $kb_config ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Generate archive styles from configuration
	 *
	 * @param $kb_config
	 * @return string
	 */
	private static function generate_archive_structure_css( $kb_config ) {

		// Left Sidebar Settings
		$archive_sidebar_bg_color                       = $kb_config['archive_sidebar_background_color'];
		$archive_left_sidebar_padding_top               = $kb_config['article-left-sidebar-padding-v2_top'];
		$archive_left_sidebar_padding_right             = $kb_config['article-left-sidebar-padding-v2_right'];
		$archive_left_sidebar_padding_bottom            = $kb_config['article-left-sidebar-padding-v2_bottom'];
		$archive_left_sidebar_padding_left              = $kb_config['article-left-sidebar-padding-v2_left'];

		// Content Settings
		$archive_content_bg_color                       = $kb_config['archive_content_background_color'];

		// Right Sidebar Settings
		$archive_right_sidebar_padding_top              = $kb_config['article-right-sidebar-padding-v2_top'];
		$archive_right_sidebar_padding_right            = $kb_config['article-right-sidebar-padding-v2_right'];
		$archive_right_sidebar_padding_bottom           = $kb_config['article-right-sidebar-padding-v2_bottom'];
		$archive_right_sidebar_padding_left             = $kb_config['article-right-sidebar-padding-v2_left'];

		// WIDTH
		$archive_header_desktop_width           = $kb_config['archive_header_desktop_width'];
		$archive_header_desktop_width_units     = $kb_config['archive_header_desktop_width_units'];
		$archive_content_desktop_width          = $kb_config['archive_content_desktop_width'];
		$archive_content_desktop_width_units    = $kb_config['archive_content_desktop_width_units'];
		$archive_left_sidebar_desktop_width     = $kb_config['archive_left_sidebar_desktop_width'];
		$archive_right_sidebar_desktop_width    = $kb_config['archive_right_sidebar_desktop_width'];

		// auto-determine whether we need sidebar or let user override it to be displayed
		$is_left_sidebar_on = self::is_left_sidebar_on( $kb_config );
		$is_right_sidebar_on = self::is_right_sidebar_on( $kb_config );

		/**
		 *  Grid Columns start at lines.
		 *
		 *  Left Sidebar Grid Start:    1 - 2;
		 *  Content Grid Start:         2 - 3;
		 *  Right Sidebar Grid Start:    3 - 4;
		 *
		 *  LEFT   Content  Right
		 *  1 - 2   2 - 3   3 - 4
		 */

		$output = self::archive_media_structure( array(
				'is_left_sidebar_on'                    => $is_left_sidebar_on,
				'is_right_sidebar_on'                   => $is_right_sidebar_on,
				'archive_header_desktop_width'          => $archive_header_desktop_width,
				'archive_header_desktop_width_units'    => $archive_header_desktop_width_units,
				'archive_content_desktop_width'         => $archive_content_desktop_width,
				'archive_content_desktop_width_units'   => $archive_content_desktop_width_units,
				'archive_left_sidebar_desktop_width'    => $archive_left_sidebar_desktop_width,
				'archive_right_sidebar_desktop_width'   => $archive_right_sidebar_desktop_width,
		) );

		/* SHARED */
		$output .= '
			#eckb-archive-page-container #eckb-archive-content {
				background-color: ' . $archive_content_bg_color . ';
			}
			#eckb-archive-page-container #eckb-archive-left-sidebar {
				background-color: ' . $archive_sidebar_bg_color .';
				padding: ' . $archive_left_sidebar_padding_top . 'px ' . $archive_left_sidebar_padding_right . 'px ' . $archive_left_sidebar_padding_bottom . 'px ' . $archive_left_sidebar_padding_left . 'px;
			}
			#eckb-archive-page-container #eckb-archive-right-sidebar {
				padding: ' . $archive_right_sidebar_padding_top . 'px ' . $archive_right_sidebar_padding_right . 'px ' . $archive_right_sidebar_padding_bottom . 'px ' . $archive_right_sidebar_padding_left . 'px;
				background-color: ' . $archive_sidebar_bg_color . ';
			}';

		return $output;
	}

	/**
	 * Output style for either desktop or tablet
	 * @param array $settings
	 */
	public static function archive_media_structure( $settings = array() ) {

		$defaults = array(
			'is_left_sidebar_on'                    => '',
			'is_right_sidebar_on'                   => '',
			'archive_header_desktop_width'          => '',
			'archive_header_desktop_width_units'    => '',
			'archive_content_desktop_width'         => '',
			'archive_content_desktop_width_units'   => '',
			'archive_left_sidebar_desktop_width'    => '',
			'archive_right_sidebar_desktop_width'   => '',
		);
		$args = array_merge( $defaults, $settings );

		// Header ( Currently contains search )
		$output =
			'#eckb-archive-page-container #eckb-archive-header  {
				width: ' . $args[ 'archive_header_desktop_width' ] . $args[ 'archive_header_desktop_width_units'] . ';
			}';

		// Content ( Sidebars , Article Content )
		$output .=
			'#eckb-archive-page-container #eckb-archive-body,
			#eckb-archive-page-container #eckb-archive-footer {
				width: ' . $args[ 'archive_content_desktop_width' ] . $args[ 'archive_content_desktop_width_units'] . ';
			}';

		/**
		 * If No Left Sidebar AND Right Sidebar active
		 *  - Expend the Archive Content 1 - 3
		 *  - Make Layout 2 Columns only and use the Two remaining values
		 */
		if ( ! $args[ 'is_left_sidebar_on' ] && $args[ 'is_right_sidebar_on' ]  ) {

			$archive_content_width = 100 - $args[ 'archive_right_sidebar_desktop_width' ];

			$output .= '
		        /* NO LEFT SIDEBAR */
				#eckb-archive-page-container #eckb-archive-body {
					grid-template-columns:  0 ' . $archive_content_width . '% ' . $args[ 'archive_right_sidebar_desktop_width' ] . '%;
				}
				#eckb-archive-page-container #eckb-archive-left-sidebar {
					display:none;
				}
				#eckb-archive-page-container #eckb-archive-content {
					grid-column-start: 1;
					grid-column-end: 3;
				}';
		}

		/**
		 * If No Right Sidebar AND Left Sidebar active
		 *  - Expend the Archive Content 2 - 4
		 *  - Make Layout 2 Columns only and use the Two remaining values
		 */
		if ( ! $args[ 'is_right_sidebar_on' ] && $args[ 'is_left_sidebar_on' ] ) {

			$archive_content_width = 100 - $args[ 'archive_left_sidebar_desktop_width' ];

			$output .= '
				/* NO RIGHT SIDEBAR */
				#eckb-archive-page-container #eckb-archive-body {
					grid-template-columns: ' . $args[ 'archive_left_sidebar_desktop_width' ] . '% ' . $archive_content_width . '% 0;
				}
				#eckb-archive-page-container #eckb-archive-right-sidebar {
					display:none;
				}
				#eckb-archive-page-container #eckb-archive-content {
					grid-column-start: 2;
					grid-column-end: 4;
				}';
		}

		// If No Sidebars Expand the Archive Content 1 - 4
		if ( ! $args[ 'is_left_sidebar_on']  && ! $args[ 'is_right_sidebar_on' ] ) {
			$output .= '
				#eckb-archive-page-container #eckb-archive-body {
					grid-template-columns: 0 100% 0;
				}
				#eckb-archive-page-container #eckb-archive-left-sidebar,
				#eckb-archive-page-container #eckb-archive-right-sidebar {
					display:none;
				}
				#eckb-archive-page-container #eckb-archive-content {
					grid-column-start: 1;
					grid-column-end: 4;
				}';
		}

		/**
		 * If Both Sidebars are active
		 *  - Make Layout 3 Columns and divide their sizes according to the user settings
		 */
		if ( $args[ 'is_left_sidebar_on' ]  && $args[ 'is_right_sidebar_on' ] ) {
			$archive_content_width = 100 - $args[ 'archive_left_sidebar_desktop_width' ] - $args[ 'archive_right_sidebar_desktop_width' ];
			$output .= '
				#eckb-archive-page-container #eckb-archive-body {
					grid-template-columns: ' . $args[ 'archive_left_sidebar_desktop_width' ] . '% ' . $archive_content_width . '% ' . $args[ 'archive_right_sidebar_desktop_width' ] . '%;
				}';
		}

		return $output;
	}

	/**
	 * Returns inline styles for Archive Page
	 *
	 * @param $kb_config
	 *
	 * @return string
	 */
	public static function get_all_inline_styles( $kb_config ) {

		$output = '
		/* CSS for Archive Page V3
		-----------------------------------------------------------------------*/';

		// General Typography ----------------------------------------------/
		if ( ! empty( $kb_config['general_typography']['font-family'] ) ) {
			$output .= '
			#eckb-archive-page-container,
			#eckb-archive-page-container .eckb-category-archive-title-name,
			#eckb-archive-page-container .eckb-category-archive-articles-list-title,
			#eckb-archive-page-container .eckb-category-archive-sub-category-list-title, 
			#eckb-archive-page-container #epkb-sidebar-container-v2 .epkb-sidebar__heading__inner__cat-name,
			#eckb-archive-page-container #epkb-sidebar-container-v2 .epkb-category-level-2-3__cat-name,
			#eckb-archive-page-container #epkb-sidebar-container-v2 .eckb-article-title__text,
			#eckb-archive-page-container #elay-sidebar-container-v2 .elay-sidebar__heading__inner__cat-name,
			#eckb-archive-page-container #elay-sidebar-container-v2 .elay-category-level-2-3__cat-name,
			#eckb-archive-page-container #elay-sidebar-container-v2 .elay-article-title__text,
			#eckb-archive-page-container .eckb-acll__title,
			#eckb-archive-page-container .eckb-acll__cat-item__name,
			#eckb-archive-page-container .eckb-breadcrumb-nav
			{
			    ' . EPKB_Utilities::get_font_css( $kb_config, 'general_typography', 'font-family' ) . '
			}';
		}
		// Category Name
		$output .= '
		    #eckb-archive-page-container .eckb-category-archive-title-container {
		        ' . ( empty( $kb_config['article_title_typography']['font-size'] ) ? 'font-size: 30px' : EPKB_Utilities::get_font_css( $kb_config, 'article_title_typography', 'font-size' ) ) . ';
		        color: ' .  ( empty( $kb_config['section_head_font_color'] ) ? '#000000;' : $kb_config['section_head_font_color'] ). ';
		    }
		    .eckb-category-archive-title-icon--image {
		        width: ' . ( empty( $kb_config['article_title_typography']['font-size'] ) ? '30px' : ( intval( $kb_config['article_title_typography']['font-size']) + 10 ) . 'px' ) . ' !important;
		    }';
		// Category Desc
		$output .= '
		    #eckb-archive-page-container .eckb-category-archive-description {
		        color: ' . $kb_config['section_head_description_font_color'] . ';
		    }';

		// Category Icon
		$output .= '
		    #eckb-archive-page-container .eckb-category-archive-title-icon {
		        color: ' . $kb_config['section_head_category_icon_color'] . ';
		    }';

		// Sub Titles
		$output .= '
		    #eckb-archive-page-container .eckb-category-archive-articles-list-title,
		    #eckb-archive-page-container .eckb-category-archive-sub-category-list-title {
		        color: ' . $kb_config['section_category_font_color'] . ';
		        font-size: ' . ( intval( $kb_config['article_typography']['font-size'] ) + 6 ) . 'px;
		    }';

		// Main Category Articles
		$output .= '
		    #eckb-archive-page-container .eckb-article-list-container .eckb-article-container .epkb-article-inner { 
				font-size: ' . ( intval( $kb_config['article_typography']['font-size'] ) + 2 ) . 'px;
		    }';
		$output .= '
		    #eckb-archive-page-container .eckb-article-list-container .eckb-article-container .epkb-article__icon { 
		        font-size: ' . ( intval( $kb_config['article_typography']['font-size'] ) + 6 ) . 'px;
		    }';

		// Sub Categories
		$output .= '
		    #eckb-archive-page-container .eckb-sub-category-container {
		        background-color: ' . $kb_config['archive_content_sub_categories_background_color'] . ';
		    }';
		if ( $kb_config['archive_content_sub_categories_border_toggle'] == 'on' ) {
			$output .= '
		    #eckb-archive-page-container .eckb-sub-category-container {
		        border: solid 1px #dfe5eb;
		        padding: 30px 20px;
		        border-radius: 10px;
		    }';
		} else {
			$output .= '
		    #eckb-archive-page-container .eckb-sub-category-row {
		       gap:40px !important;
		    }';
		}

		$output .= '
		    #eckb-archive-page-container .eckb-sub-category-title {
		        color: ' . $kb_config['section_category_font_color'] . ';
		    }';

		$output .= '
	    #eckb-archive-page-container .eckb-sub-category-description {
	        color: ' . $kb_config['section_head_description_font_color'] . ';
	    }';
		// Sub Category Articles
		$output .= '
		    #eckb-archive-page-container .eckb-sub-category-list-container .eckb-article-container .epkb-article-inner { 
				font-size: ' .  $kb_config['article_typography']['font-size'] . 'px;
		    }';
		$output .= '
		    #eckb-archive-page-container .eckb-sub-category-list-container .eckb-article-container .epkb-article__icon { 
		        font-size: ' . ( intval( $kb_config['article_typography']['font-size'] ) + 4 ) . 'px;
		    }';

		// Arrows
		$output .= '
		    #eckb-archive-page-container .eckb-category-archive-arrow {
		        color: ' . $kb_config['article_icon_color'] . ';
		    }';

		$output .= self::generate_archive_structure_css( $kb_config );

		// Search ----------------------------------------------------------/
		$output .= EPKB_ML_Search::get_inline_styles( $kb_config );

		return $output;
	}
}
