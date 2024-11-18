<?php

/**
 *  Outputs the tabs theme (tabs used to switch between top categories) for knowledge base main page.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Layout_Tabs extends EPKB_Layout {

	/**
	 * Display Categories and Articles module content for KB Main Page (without KB Search)
	 *
	 * @param $kb_config
	 * @param $category_seq_data
	 * @param $articles_seq_data
	 */
	public function display_categories_and_articles( $kb_config, $category_seq_data, $articles_seq_data ) {
		$this->kb_config = $kb_config;
		$this->category_seq_data = $category_seq_data;
		$this->articles_seq_data = $articles_seq_data;

		$active_cat_id = $this->get_active_category_id(); ?>

		<div id="epkb-ml-tabs-layout" role="main" aria-labelledby="epkb-ml-tabs-layout" class="epkb-layout-container epkb-css-full-reset epkb-tabs-template">
			<div id="epkb-content-container">
				<div class="epkb-section-container">
					<div class="epkb-content-container">

						<!-- Navigation Tabs -->						<?php
						$this->display_navigation_tabs( $active_cat_id ); ?>

						<!-- Main Page Content -->
						<div class="epkb-panel-container">				<?php
							$this->display_main_page_content( $active_cat_id ); ?>
						</div>

					</div>
				</div>
			</div>
		</div>   <?php
	}

    /**
	 * Generate content of the KB main page
	 */
	public function generate_non_modular_kb_main_page() {

		$active_cat_id = $this->get_active_category_id();

		$class2_escaped = $this->get_css_class( '::width' );    ?>

		<div id="epkb-main-page-container" role="main" aria-labelledby="<?php esc_html_e( 'Knowledge Base', 'echo-knowledge-base' ); ?>" class="epkb-css-full-reset epkb-tabs-template <?php echo esc_attr( EPKB_Utilities::get_active_theme_classes() ); ?>">
			<div <?php echo $class2_escaped; ?>>  <?php

				//  KB Search form
				$this->get_search_form();

				//  Knowledge Base Layout
				$style1_escaped = $this->get_inline_style( 'background-color:: background_color' );				?>
				<div id="epkb-content-container" <?php echo $style1_escaped; ?> >

					<!--  Navigation Tabs -->
					<?php $this->display_navigation_tabs( $active_cat_id ); ?>

					<!--  Main Page Content -->
					<div class="epkb-panel-container">
						<?php $this->display_main_page_content( $active_cat_id ); ?>
					</div>

				</div>
			</div>
		</div>   <?php
	}

	/**
	 * Display KB Main page navigation tabs
	 *
	 * @param $active_cat_id
	 */
	private function display_navigation_tabs( $active_cat_id ) {

		$nof_top_categories = count( $this->category_seq_data );

		// show full KB main page if we have fewer categories (otherwise use 'mobile' style menu)

		// loop through LEVEL 1 CATEGORIES
		if ( $nof_top_categories <= 6 ) {

			$class1_escaped = $this->get_css_class( 'epkb-main-nav'. ($this->kb_config[ 'tab_down_pointer' ] == 'on' ? ', epkb-down-pointer' : '') );
			$style1_escaped = $this->get_inline_style( 'typography:: tab_typography, background-color:: tab_nav_background_color' );
			$style2_escaped = $this->get_inline_style( 'background-color:: tab_nav_background_color, border-bottom-color:: tab_nav_border_color, border-bottom-style: solid, border-bottom-width: 1px' ); ?>

			<section <?php echo $class1_escaped . ' ' . $style1_escaped; ?> >

				<ul	class="epkb-nav-tabs epkb-top-categories-list" <?php echo $style2_escaped; ?> >					<?php

					$ix = 0;
					foreach ( $this->category_seq_data as $category_id => $subcategories ) {

						$category_name = isset( $this->articles_seq_data[$category_id][0] ) ?	$this->articles_seq_data[$category_id][0] : '';
						if ( empty( $category_name ) ) {
							continue;
						}

						$category_desc = isset($this->articles_seq_data[$category_id][1]) && $this->kb_config['section_desc_text_on'] == 'on' ? $this->articles_seq_data[$category_id][1] : '';
						$tab_cat_name = sanitize_title_with_dashes( $category_name );

						$active = $active_cat_id == $category_id ? 'active' : '';
						$class1_escaped = $this->get_css_class( $active . ', col-' . $nof_top_categories . ', epkb_top_categories');
						$style2_escaped = $this->get_inline_style( 'color:: tab_nav_font_color' );  ?>

						<li id="epkb_tab_<?php echo esc_attr( ++$ix ); ?>" tabindex="0" <?php echo $class1_escaped; ?> data-cat-name="<?php echo esc_attr( $tab_cat_name ); ?>">
							<div  class="epkb-category-level-1" data-kb-category-id="<?php echo esc_attr( $category_id ); ?>" <?php echo $style2_escaped; ?> >
								<h2 class="epkb-cat-name"><?php echo esc_html( $category_name ); ?></h2>
							</div>							<?php 
							if ( $category_desc ) { ?>
								<p class="epkb-cat-desc" <?php echo $style2_escaped; ?> ><?php echo wp_kses_post( $category_desc ); ?></p>							<?php
							} ?>
						</li> <?php

					} //foreach					?>

				</ul>
			</section> <?php

		} else {   ?>

			<!-- Drop Down Menu -->  <?php

			$class1_escaped = $this->get_css_class( 'main-category-selection-1' );
			$style1_escaped = $this->get_inline_style( 'typography::tab_typography, background-color:: tab_nav_background_color' );
			$style2_escaped = $this->get_inline_style( 'color:: tab_nav_font_color' );  		?>

			<section <?php echo $class1_escaped . ' ' . $style1_escaped; ?> >

				<div class="epkb-category-level-1" <?php echo $style2_escaped; ?>><?php echo esc_html( $this->kb_config['choose_main_topic'] ); ?></div>

				<select id="main-category-selection" class="epkb-top-categories-list"> <?php
						$ix = 0;
						foreach ( $this->category_seq_data as $category_id => $subcategories ) {

							$category_name = isset($this->articles_seq_data[$category_id][0]) ? $this->articles_seq_data[$category_id][0] : '';
							if ( empty($category_name) ) {
								continue;
							}

							$category_desc = isset($this->articles_seq_data[$category_id][1]) && $this->kb_config['section_desc_text_on'] == 'on' ? $this->articles_seq_data[$category_id][1] : '';
							$tab_cat_name = sanitize_title_with_dashes( $category_name );
							$option = $category_name . ( empty($category_desc) ? '' : ' - ' . $category_desc );
							$active = $active_cat_id == $category_id ? 'selected' : ''; ?>
							<option <?php echo esc_attr( $active ); ?> data-kb-category-id="<?php echo esc_attr( $category_id ); ?>" id="epkb_tab_<?php echo esc_attr( ++$ix ); ?>" data-cat-name="<?php echo esc_attr( $tab_cat_name ); ?>"><?php echo esc_html( $option ); ?></option>  <?php
						} 	?>

				</select>

			</section> <?php
		}
	}

	/**
	 * Display KB main page content
	 *
	 * @param $active_cat_id
	 */
	private function display_main_page_content( $active_cat_id ) {

		// show message that articles are coming soon if the current KB does not have any Category
		if ( ! $this->has_kb_categories ) {
			$this->show_categories_missing_message();
			return;
		}

		$class0_escaped = $this->get_css_class('::section_box_shadow, epkb-top-category-box');
		$style0_escaped = $this->get_inline_style(
					'border-radius:: section_border_radius,
					 border-width:: section_border_width,
					 border-color:: section_border_color, ' .
					'background-color:: section_body_background_color, border-style: solid' );

		$class1_escaped = $this->get_css_class('::section_box_shadow, epkb-top-category-box, epkb-tab-top-articles');
		$style1_escaped = $this->get_inline_style(
					'border-radius:: section_border_radius,
					 border-width:: section_border_width,
					 border-color:: section_border_color, ' .
					'background-color:: section_body_background_color, border-style: solid' );

		$class_section_head_escaped= $this->get_css_class( 'section-head' . ( $this->kb_config[ 'section_divider' ] == 'on' ? ', section_divider' : '' ) );
		$style_section_head_escaped = $this->get_inline_style(
					'border-bottom-width:: section_divider_thickness,
					background-color:: section_head_background_color, ' .
					'border-top-left-radius:: section_border_radius,
					border-top-right-radius:: section_border_radius,
					text-align::section_head_alignment,
					border-bottom-color:: section_divider_color,
					padding-top:: section_head_padding_top,
					padding-bottom:: section_head_padding_bottom,
					padding-left:: section_head_padding_left,
					padding-right:: section_head_padding_right'
		);
		$style3_escaped = $this->get_inline_style(
					'color:: section_head_font_color,
					 text-align::section_head_alignment,
					 justify-content::section_head_alignment'
		);
		
		$style31_escaped = $this->get_inline_style(
					'color:: section_head_font_color,
			 		typography:: section_head_typography'
		);
		$style4_escaped = $this->get_inline_style(
					'color:: section_head_description_font_color,
					 text-align::section_head_alignment,
					 typography:: section_head_description_typography'
		);
		$style5 = 'border-bottom-width:: section_border_width,
					padding-top::    section_body_padding_top,
					padding-bottom:: section_body_padding_bottom,
					padding-left::   section_body_padding_left,
					padding-right::  section_body_padding_right,
					';

		if ( $this->kb_config['section_box_height_mode'] == 'section_min_height' ) {
			$style5 .= 'min-height:: section_body_height';
		} else if ( $this->kb_config['section_box_height_mode'] == 'section_fixed_height' ) {
			$style5 .= 'overflow: auto, height:: section_body_height';
		}

		// for each CATEGORY display: a) its articles and b) top-level SUB-CATEGORIES with its articles

		$categories_icons = $this->get_category_icons();

		$header_icon_style_escaped = $this->get_inline_style( 'color:: section_head_category_icon_color, font-size:: section_head_category_icon_size' );
		$header_image_style_escaped = $this->get_inline_style( 'max-height:: section_head_category_icon_size' );

		$icon_location = empty( $this->kb_config['section_head_category_icon_location'] ) ? '' : $this->kb_config['section_head_category_icon_location'];
		
		$top_icon_class = 'epkb-category--' . $icon_location . '-cat-icon';
		
		/** loop through LEVEL 1 CATEGORIES: for the active TOP-CATEGORY display top-level SUB-CATEGORIES with its articles */
		$ix = 0;
		$articles_coming_soon_msg = $this->kb_config['category_empty_msg'];

		foreach ( $this->category_seq_data as $tab_category_id => $box_categories_array ) {

			$level_1_categories = is_array( $box_categories_array ) ? $box_categories_array : array();
			$active = $active_cat_id == $tab_category_id ? 'active' : '';
			$class_list = 'epkb_tab_' . ++$ix . ' epkb_top_panel epkb-tab-panel ';
			if ( ! empty( $this->kb_config['nof_columns'] ) ) {
				$class_list .= 'epkb-' . $this->kb_config['nof_columns'];
			}
			$class_list .= ' eckb-categories-list ' . $active;

			// retrieve level 1 articles
			$articles_level_1_list = array();
			if ( isset( $this->articles_seq_data[ $tab_category_id ] ) ) {
				$articles_level_1_list = $this->articles_seq_data[ $tab_category_id ];
				unset( $articles_level_1_list[0] );
				unset( $articles_level_1_list[1] );
			} ?>

			<div class="<?php echo esc_attr( $class_list ); ?>"> <?php

				$is_modular = $this->kb_config['modular_main_page_toggle'] == 'on';

				// old users do not see hidden articles that were assigned to top tab categories
				if ( ( empty( $articles_level_1_list ) || ! EPKB_Utilities::is_new_user( $this->kb_config, '11.30.0' ) ) && empty( $level_1_categories ) && ! empty( $articles_coming_soon_msg ) ) {  ?>
					<div class="epkb-articles-coming-soon" data-kb-top-category-id="<?php echo esc_attr( $tab_category_id ); ?>" data-kb-type="top-category-no-articles"><?php echo esc_html( $articles_coming_soon_msg ); ?></div> <?php
				}

				/** DISPLAY LEVEL 2 CATEGORY BOX for 1 LEVEL ARTICLES */
				if ( ! empty( $articles_level_1_list ) && EPKB_Utilities::is_new_user( $this->kb_config, '11.30.0' ) ) { ?>
					<section <?php echo $class1_escaped . ' ' . $style1_escaped; ?>>
						<div class="epkb-section-body epkb-ml-articles-list">    <?php

							$columns = $this->get_articles_listed_in_columns( $articles_level_1_list );

							// display the articles in the columns  ?>
							<div class="epkb-ml-articles-list epkb-total-columns-<?php echo esc_attr( $this->get_nof_columns_int() ); ?>">   <?php
								$column_number = 1;
								foreach ( $columns as $articles_in_column ) {			 ?>
									<ul class="epkb-list-column epkb-list-column-<?php echo esc_attr( $column_number ); ?>">   <?php
										foreach ( $articles_in_column as $article ) { ?>
												<li><?php $this->single_article_link( $article['title'], $article['id'], EPKB_Layout::TABS_LAYOUT ); ?></li>  <?php
										} ?>
									</ul>   <?php
									$column_number ++;
								} ?>
							</div>

						</div>
					</section> <?php
				}

				/** DISPLAY LEVEL 2 CATEGORIES (BOX) + ARTICLES + SUB-SUB-CATEGORIES */
				$column_index = 1;
				$loop_index = 1;
				foreach ( $level_1_categories as $box_category_id => $level_2_categories ) {

					$category_name = isset( $this->articles_seq_data[$box_category_id][0] ) ? $this->articles_seq_data[$box_category_id][0] : '';
					if ( empty( $category_name ) ) {
						continue;
					}
					
					$category_icon = EPKB_KB_Config_Category::get_category_icon( $box_category_id, $categories_icons );
					$category_desc = isset($this->articles_seq_data[$box_category_id][1]) && $this->kb_config['section_desc_text_on'] == 'on' ? $this->articles_seq_data[$box_category_id][1] : '';
					$level_2_categories = is_array($level_2_categories) ? $level_2_categories : array();

					// divide categories into rows if modular layout is on
					if ( $is_modular && $column_index == 1 ) { ?>
						<div class="epkb-ml__module-categories-articles__row">  <?php
					}   ?>

					<!-- Section Container ( Category Box ) -->
					<section id="<?php echo esc_attr( 'epkb_cat_' . $box_category_id ); ?>" <?php echo $class0_escaped . ' ' . $style0_escaped; ?> >

						<!-- Section Head -->
						<div <?php echo $class_section_head_escaped . ' ' . $style_section_head_escaped; ?> >

							<!-- Category Name + Icon -->
							<div class="epkb-category-level-2-3 <?php echo esc_attr( $top_icon_class ); ?>" aria-expanded="false" data-kb-top-category-id="<?php echo esc_attr( $tab_category_id ); ?>"
							     data-kb-category-id="<?php echo esc_attr( $box_category_id ); ?>" data-kb-type="sub-category" <?php echo $style3_escaped; ?> role="region">

							<!-- Icon Top / Left -->	                            <?php
							if ( in_array( $icon_location, array('left', 'top') ) ) {

								if ( $category_icon['type'] == 'image' ) { ?>
									<img class="epkb-cat-icon epkb-cat-icon--image "
									     src="<?php echo esc_url( $category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $category_icon['image_alt'] ); ?>"<?php echo $header_image_style_escaped; ?>>								<?php
								} else { ?>
									<span class="epkb-cat-icon epkbfa <?php echo esc_attr( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $category_icon['name'] ); ?>" <?php echo $header_icon_style_escaped; ?>></span>	<?php
								}
							}

							// Get the URL of this category if link is on
							if ( $this->kb_config['section_hyperlink_on'] == 'on' ) {

								// Get the URL of this category
								$category_link = EPKB_Utilities::get_term_url( $box_category_id );     ?>
								<h3 class="epkb-cat-name"><a href="<?php echo esc_url( $category_link ); ?>" <?php echo $style31_escaped; ?>> <?php echo esc_html( $category_name ); ?></a></h3>		<?php
							} else {    ?>
								<h3 class="epkb-cat-name" <?php echo $style31_escaped; ?>><?php echo esc_html( $category_name ); ?></h3>							<?php
							}	        ?>

							<!-- Icon Right -->     <?php
							if ( $icon_location == 'right' ) {

								if ( $category_icon['type'] == 'image' ) { ?>
									<img class="epkb-cat-icon epkb-cat-icon--image "
									     src="<?php echo esc_url( $category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $category_icon['image_alt'] ); ?>"<?php echo $header_image_style_escaped; ?>
									>								<?php
								} else { ?>
									<span class="epkb-cat-icon epkbfa <?php echo esc_attr( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $category_icon['name'] ); ?>" <?php echo $header_icon_style_escaped; ?>></span>	<?php
								}
							}       ?>

							</div>
							
							<!-- Category Description -->						<?php
							if ( $category_desc ) {   ?>
								<p class="epkb-cat-desc" <?php echo $style4_escaped; ?> >
							        <?php echo wp_kses_post( $category_desc ); ?>
						        </p>						<?php
							}       ?>
						</div>

						<!-- Section Body -->
						<div class="epkb-section-body" <?php echo $this->get_inline_style( $style5 ); ?> >   	<?php							
							/** DISPLAY TOP-CATEGORY ARTICLES LIST */
							if (  $this->kb_config['show_articles_before_categories'] != 'off' ) {
								$this->display_articles_list( 2, $box_category_id, ! empty($level_2_categories) );
							}
							
							if ( ! empty($level_2_categories) ) {
								$this->display_level_3_categories( $level_2_categories, 'sub-', 3 );
							}

							/** DISPLAY TOP-CATEGORY ARTICLES LIST */
							if (  $this->kb_config['show_articles_before_categories'] == 'off' ) {
								$this->display_articles_list( 2, $box_category_id, ! empty($level_2_categories) );
							} ?>

						</div><!-- Section Body End -->

					</section><!-- Section End -->  <?php

					if ( $is_modular && ( $column_index == $this->get_nof_columns_int() || $loop_index == count( $level_1_categories ) ) ) {     ?>
						</div>  <?php
						$column_index = 0;
					}

					$column_index ++;
					$loop_index ++;
				} ?>

			</div>  <?php
		}
	}

	/**
	 * Display categories within the Box i.e. sub-sub-categories
	 *
	 * @param $box_sub_category_list
	 * @param string $level_name
	 * @param int $level_num
	 */
	private function display_level_3_categories( $box_sub_category_list, $level_name, $level_num ) {

		$level_name .= 'sub-';
		$body_style1_escaped = $this->get_inline_style( 'padding-left:: article_list_margin' );
        if ( $level_num >= 4 ) {
	        $body_style1_escaped = $this->get_inline_style( 'padding-left:: sub_article_list_margin' );
        }   ?>
		<ul class="epkb-sub-category eckb-sub-category-ordering" <?php echo $body_style1_escaped; ?>> <?php

			/** DISPLAY SUB-SUB-CATEGORIES */
			foreach ( $box_sub_category_list as $box_sub_category_id => $box_sub_sub_category_list ) {
				$category_name = isset( $this->articles_seq_data[$box_sub_category_id][0] ) ?
											$this->articles_seq_data[$box_sub_category_id][0] : _x( 'Category', 'taxonomy singular name' );

				$class1_escaped = $this->get_css_class( '::expand_articles_icon, epkb-category-level-2-3__cat-icon' );
				$style1_escaped = $this->get_inline_style( 'color:: section_category_icon_color' );
				$style2_escaped = $this->get_inline_style( 'color:: section_category_font_color' ); ?>

				<li <?php echo $this->get_inline_style( 'padding-bottom:: article_list_spacing,padding-top::article_list_spacing' ); ?>>
					<div class="epkb-category-level-2-3" aria-expanded="false" data-kb-category-id="<?php echo esc_attr( $box_sub_category_id ); ?>" data-kb-type="<?php echo esc_attr( $level_name . 'category' ); ?>" role="region">
						<span <?php echo $class1_escaped . ' ' . $style1_escaped; ?> ></span>
						<h<?php echo esc_attr( $level_num + 1 ); ?> class="epkb-category-level-2-3__cat-name" tabindex="0" <?php echo $style2_escaped; ?> ><?php echo esc_html( $category_name ); ?></h<?php echo esc_attr( $level_num + 1 ); ?>>
					</div>    <?php

					/** DISPLAY TOP-CATEGORY ARTICLES LIST */
					if (  $this->kb_config['show_articles_before_categories'] != 'off' ) {
						$this->display_articles_list( $level_num, $box_sub_category_id, ! empty($box_sub_sub_category_list), $level_name );
					}

					/** RECURSION DISPLAY SUB-SUB-...-CATEGORIES */
					if ( ! empty($box_sub_sub_category_list) && strlen($level_name) < 20 ) {
						$this->display_level_3_categories( $box_sub_sub_category_list, $level_name, $level_num + 1);
					}
					
					/** DISPLAY TOP-CATEGORY ARTICLES LIST */
					if (  $this->kb_config['show_articles_before_categories'] == 'off' ) {
						$this->display_articles_list( $level_num, $box_sub_category_id, ! empty($box_sub_sub_category_list), $level_name );
					}    ?>
				</li>  <?php
			}           ?>

		</ul> <?php
	}

	/**
	 * Display list of articles that belong to given subcategory
	 *
	 * @param $level
	 * @param $category_id
	 * @param bool $sub_category_exists - if true then we don't want to show "Articles coming soon" if there are no articles because
	 *                                   we have at least categories listed. But sub-category should always have that message if no article present
	 * @param string $level_name
	 */
	private function display_articles_list( $level, $category_id, $sub_category_exists=false, $level_name='' ) {

		// retrieve articles belonging to given (sub) category if any
		$articles_list = array();
		if ( isset($this->articles_seq_data[$category_id]) ) {
			$articles_list = $this->articles_seq_data[$category_id];
			unset($articles_list[0]);
			unset($articles_list[1]);
		}

		// filter top level articles if the will be displayed elsewhere
		if ( isset( $this->category_seq_data[$category_id] ) ) {
			foreach ( $articles_list as $top_article_id => $top_article_title ) {
				foreach ( $this->articles_seq_data as $category_id => $category_article_list ) {
					if ( isset( $category_article_list[ $top_article_id ] ) && ! isset( $this->category_seq_data[ $category_id ] ) ) {
						unset( $articles_list[ $top_article_id ] );
					}
				}
			}
		}

		// return if we have no articles and will not show 'Articles coming soon' message
		$articles_coming_soon_msg = $this->kb_config['category_empty_msg'];
		if ( empty( $articles_list ) && ( $sub_category_exists || empty( $articles_coming_soon_msg ) ) ) {
			return;
		}

		$sub_category_styles = '';
		if ( $level == 1 ) {
			$data_kb_type = 'article';
		} else if ( $level == 2 ) {
			$data_kb_type = 'sub-article';
			$sub_category_styles = is_rtl() ? 'padding-right:: article_list_margin' : 'padding-left:: article_list_margin';
		} else {
			$data_kb_type = empty( $level_name ) ? 'sub-sub-article' : $level_name . 'article';
			$sub_category_styles = is_rtl() ? 'padding-right:: sub_article_list_margin' : 'padding-left:: sub_article_list_margin';
		} ?>

		<ul class="<?php echo esc_attr( ( $level == 2 ? 'epkb-main-category ' : '' ) . 'epkb-articles' ); ?>" <?php echo $this->get_inline_style( $sub_category_styles ); ?> data-list-id="<?php echo esc_attr( $category_id ); ?>"> <?php

			$nof_articles = 0;
			$nof_articles_displayed = $this->kb_config['nof_articles_displayed'];
			foreach ( $articles_list as $article_id => $article_title ) {

				if ( ! EPKB_Utilities::is_article_allowed_for_current_user( $article_id ) ) {
					continue;
				}

				$nof_articles++;
				$hide_class = $nof_articles > $nof_articles_displayed ? 'epkb-hide-elem' : '';

				/** DISPLAY ARTICLE LINK */         ?>
				<li class="epkb-article-level-<?php echo esc_attr( $level . ' ' . $hide_class ); ?>" data-kb-article-id="<?php echo esc_attr( $article_id ); ?>"
				        data-kb-type="<?php echo esc_attr( $data_kb_type ); ?>" <?php echo $this->get_inline_style( 'padding-bottom:: article_list_spacing,padding-top::article_list_spacing' ); ?> >   <?php
								$this->single_article_link( $article_title, $article_id, EPKB_Layout::TABS_LAYOUT ); ?>
				</li> <?php
			}

			if ( $nof_articles == 0 ) {
				echo '<li class="epkb-articles-coming-soon">' . esc_html( $articles_coming_soon_msg ) . '</li>';
			} ?>

		</ul> <?php

		// if article list is longer than initial article list size then show expand/collapse message
		if ( $nof_articles > $nof_articles_displayed ) { ?>
			<button class="epkb-show-all-articles" aria-expanded="false" data-btn-id="<?php echo esc_attr( $category_id ); ?>">
					<span class="epkb-show-text">					<?php 
						echo esc_html( $this->kb_config['show_all_articles_msg'] . ' ( ' . ( $nof_articles - $nof_articles_displayed ) ); ?> )
					</span>
				<span class="epkb-hide-text epkb-hide-elem"><?php echo esc_html( $this->kb_config['collapse_articles_msg'] ); ?></span>
			</button>					<?php
		}
	}

	/**
	 * Determine active tab based on URL or choose first one
	 * @return int|string
	 */
	private function get_active_category_id() {

		$active_tab = EPKB_Utilities::post( 'top-category' );
		$active_cat_id = '0';
		$first_tab = true;
		foreach ( $this->category_seq_data as $category_id => $subcategories ) {

			$category_name = isset( $this->articles_seq_data[ $category_id ][0] ) ? $this->articles_seq_data[ $category_id ][0] : '';
			if ( empty( $category_name ) ) {
				continue;
			}

			// apply the same sanitization that was used on category name output to HTML attribute, then URL decode it to have the same value for comparison
			$tab_cat_name = urldecode( esc_attr( sanitize_title_with_dashes( $category_name ) ) );

			if ( $first_tab ) {
				$active_cat_id = $category_id;
				$first_tab = false;
			}

			if ( $tab_cat_name == $active_tab ) {
				$active_cat_id = $category_id;
				break;
			}
		}

		return $active_cat_id;
	}

	/**
	 * Returns inline styles for Categories & Articles Module
	 *
	 * @param $kb_config
	 *
	 * @return string
	 */
	public static function get_inline_styles( $kb_config ) {

		$output = '';


		/** IF UPDATING THIS CODE, UPDATE BLOCK INLINE STYLE IF APPLICABLE */

		// Container -----------------------------------------/
		if ( ! empty( $kb_config['background_color'] ) ) {
			$output .= '
				#epkb-content-container {
					padding: 20px!important;
					background-color: ' . sanitize_hex_color( $kb_config['background_color'] ) . '!important;
				}';
		}

		// Tabs  ---------------------------------------------/
		$output .= '
			#epkb-content-container .epkb-nav-tabs .active:after {
				border-top-color: ' . $kb_config['tab_nav_active_background_color'] . '!important
			}
			#epkb-content-container .epkb-nav-tabs .active {
				background-color: ' . $kb_config['tab_nav_active_background_color'] . '!important
			}	
			#epkb-content-container .epkb-nav-tabs .active .epkb-category-level-1,
			#epkb-content-container .epkb-nav-tabs .active p {
				color: ' . $kb_config['tab_nav_active_font_color'] . '!important
			}
			#epkb-content-container .epkb-nav-tabs .active:before {
				border-top-color: ' . $kb_config['tab_nav_border_color'] . '!important
			}
		';

		if ( isset( $kb_config['section_typography'] ) ) {
			// Headings  -----------------------------------------/
			$output .= '
			#epkb-content-container .epkb-category-level-2-3 {
			    font-size: ' . ( empty( $kb_config['section_typography']['font-size'] ) ? 'inherit;' : $kb_config['section_typography']['font-size'] . 'px!important;' ) . '
			}';

			// Articles  -----------------------------------------/
			$output .= '
			#epkb-content-container .epkb-category-level-2-3__cat-name, 
			#epkb-content-container .epkb-articles-coming-soon,
			#epkb-content-container .epkb-show-all-articles { ' .
					EPKB_Typography::get_css_string( $kb_config['section_typography'] ) . '
			}';
		}

		if ( isset( $kb_config['article_typography'] ) ) {
			// Articles  -----------------------------------------/
			$output .= '
			#epkb-content-container .epkb-section-body .eckb-article-title { ' .
				EPKB_Typography::get_css_string( $kb_config['article_typography'] ) . '
			}';
		}

		// Top Level Articles  -----------------------------------------/
		$output .= '
		#epkb-ml__module-categories-articles .epkb-list-column li,
		 .epkb-tab-top-articles li {
	        padding-top: ' . $kb_config['article_list_spacing'] . 'px !important;
	        padding-bottom: ' . $kb_config['article_list_spacing'] . 'px !important;
            line-height: 1 !important;
	    }';

		return $output;
	}
}