<?php

/**
 *  Outputs the Classic Layout for knowledge base main page.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Layout_Classic extends EPKB_Layout {

	/**
	 * Display Categories and Articles module content for KB Main Page
	 *
	 * @param $kb_config
	 * @param $category_seq_data
	 * @param $articles_seq_data
	 */
	public function display_categories_and_articles( $kb_config, $category_seq_data, $articles_seq_data ) {
		$this->kb_config = $kb_config;
		$this->category_seq_data = $category_seq_data;
		$this->articles_seq_data = $articles_seq_data;

		// show message that articles are coming soon if the current KB does not have any Category
		if ( ! $this->has_kb_categories ) {
			$this->show_categories_missing_message();
			return;
		}

		$categories_per_row = $this->get_nof_columns_int();

		$classes = 'epkb-ml-classic-layout--height-' . ( $this->kb_config['section_box_height_mode'] == 'section_no_height' ? 'variable' : 'fixed' ) . ' ' . 'epkb-ml-classic-layout--' . $categories_per_row . '-col';
		$categories_icons = $this->get_category_icons();	?>

		<div id="epkb-ml-classic-layout" class="epkb-layout-container <?php echo esc_attr( $classes ); ?>">    <?php

			// start with top level categories
			$column_index = 1;
			$loop_index = 1;
			$icon_loc = 'epkb-icon-' . $this->kb_config['section_head_category_icon_location'] . '-row-adj';
			foreach ( $this->category_seq_data as $category_id => $level_2_categories ) {

				$category_name = isset( $this->articles_seq_data[ $category_id ][0] ) ? $this->articles_seq_data[ $category_id ][0] : '';
				if ( empty( $category_name ) ) {
					continue;
				}

				$level_2_categories = is_array( $level_2_categories ) ? $level_2_categories : array();

				// retrieve level 1 articles
				$articles_level_1_list = array();
				if ( isset( $this->articles_seq_data[ $category_id ] ) ) {
					$articles_level_1_list = $this->articles_seq_data[ $category_id ];
					unset( $articles_level_1_list[0] );
					unset( $articles_level_1_list[1] );
				}

				// render opening div tag before first category in the current row
				if ( $column_index == 1 ) { ?>
					<div class="epkb-ml__module-categories-articles__row <?php echo esc_attr( $icon_loc ); ?>">  <?php
				}

				self::display_classic_category( $category_id, $category_name, $articles_level_1_list, $level_2_categories, $categories_icons );

				// render closing div tag after last category in the current row or last category in the loop
				if ( $column_index == $categories_per_row || $loop_index == count( $this->category_seq_data ) ) { ?>
					</div>  <?php
					$column_index = 0;
				}

				$column_index++;
				$loop_index++;
			} ?>
		</div>  <?php
	}

	/**
	 * Display HTML of single Category for Classic layout
	 *
	 * @param $category_id
	 * @param $category_name
	 * @param $level_1_articles
	 * @param $level_2_categories
	 * @param $categories_icons
	 */
	private function display_classic_category( $category_id, $category_name, $level_1_articles, $level_2_categories, $categories_icons ) {

		$category_icon = EPKB_KB_Config_Category::get_category_icon( $category_id, $categories_icons );

		$category_title_tag_escaped = EPKB_Utilities::sanitize_html_tag( $this->kb_config['ml_categories_articles_category_title_html_tag'] );
		$category_desc = isset( $this->articles_seq_data[ $category_id ][1] ) && $this->kb_config['section_desc_text_on'] == 'on' ? $this->articles_seq_data[ $category_id ][1] : '';
		$category_articles_number = 0;
		$use_show_more = $this->kb_config['ml_categories_articles_collapse_categories'] == 'all_collapsed'; ?>

		<!-- Category Section -->
		<section class="epkb-category-section">				<?php

			switch ( $this->kb_config['section_head_category_icon_location'] ) {
				case 'no_icons':            ?>
					<!-- Section Head -->
					<div class="epkb-category-section__head epkb-category-section__head--none-location">

						<!-- Category Name -->
						<div class="epkb-category-section__head_title">
							<<?php echo esc_html( $category_title_tag_escaped ); ?> class="epkb-category-section__head_title__text"><?php echo esc_html( $category_name ); ?></<?php echo esc_html( $category_title_tag_escaped ); ?>>
						</div>

						<!-- Category Description -->
						<div class="epkb-category-section__head_desc">
							<?php echo wp_kses_post( $category_desc ); ?>
						</div>

					</div					<?php
					break;

				case 'top':             ?>
					<div class="epkb-category-section__head epkb-category-section__head--top-location">

						<!-- Icon -->
						<div class="epkb-category-section__head_icon">  <?php

							if ( $category_icon['type'] == 'image' ) { ?>
								<img class="epkb-cat-icon epkb-cat-icon--image "
								     src="<?php echo esc_url( $category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $category_icon['image_alt'] ); ?>">								<?php
							} else { ?>
								<span class="epkb-cat-icon epkb-cat-icon--font epkbfa <?php echo esc_attr( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $category_icon['name'] ); ?>"></span>    <?php
							} ?>
						</div>

						<!-- Category Name -->
						<div class="epkb-category-section__head_title">
							<<?php echo esc_html( $category_title_tag_escaped ); ?> class="epkb-category-section__head_title__text"><?php echo esc_html( $category_name ); ?></<?php echo esc_html( $category_title_tag_escaped ); ?>>
						</div>

						<!-- Category Description -->
						<div class="epkb-category-section__head_desc">
							<?php echo wp_kses_post( $category_desc ); ?>
						</div>

					</div>					<?php
					break;

				case 'left':             ?>
					<div class="epkb-category-section__head epkb-category-section__head--left-location">

						<div class="epkb-category-section__head__icon_title_container">
							<!-- Icon -->
							<div class="epkb-category-section__head_icon">  <?php
								if ( $category_icon['type'] == 'image' ) { ?>
									<img class="epkb-cat-icon epkb-cat-icon--image "
									     src="<?php echo esc_url( $category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $category_icon['image_alt'] ); ?>">								<?php
								} else { ?>
									<span class="epkb-cat-icon epkb-cat-icon--font epkbfa <?php echo esc_attr( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $category_icon['name'] ); ?>"></span>    <?php
								} ?>
							</div>

							<!-- Category Name -->
							<div class="epkb-category-section__head_title">
								<<?php echo esc_html( $category_title_tag_escaped ); ?> class="epkb-category-section__head_title__text"><?php echo esc_html( $category_name ); ?></<?php echo esc_html( $category_title_tag_escaped ); ?>>
							</div>

						</div>

						<!-- Category Description -->
						<div class="epkb-category-section__head_desc">
							<?php echo wp_kses_post( $category_desc ); ?>
						</div>

					</div>					<?php
					break;

				case 'right':               ?>
					<div class="epkb-category-section__head epkb-category-section__head--right-location">

						<div class="epkb-category-section__head__icon_title_container">

							<!-- Category Name -->
							<div class="epkb-category-section__head_title">
								<<?php echo esc_html( $category_title_tag_escaped ); ?> class="epkb-category-section__head_title__text"><?php echo esc_html( $category_name ); ?></<?php echo esc_html( $category_title_tag_escaped ); ?>>
							</div>

							<!-- Icon -->
							<div class="epkb-category-section__head_icon">  <?php
								if ( $category_icon['type'] == 'image' ) { ?>
									<img class="epkb-cat-icon epkb-cat-icon--image "
									     src="<?php echo esc_url( $category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $category_icon['image_alt'] ); ?>">								<?php
								} else { ?>
									<span class="epkb-cat-icon epkb-cat-icon--font epkbfa <?php echo esc_attr( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $category_icon['name'] ); ?>"></span>    <?php
								} ?>
							</div>

						</div>

						<!-- Category Description -->
						<div class="epkb-category-section__head_desc">							<?php
							echo wp_kses_post( $category_desc ); ?>
						</div>

					</div>					<?php
					break;
			} ?>

			<!-- Section Body -->
			<div class="epkb-category-section__body<?php echo $use_show_more ? ' ' . 'epkb-category-section__body--collapsed' : ''; ?>">
				<div class="epkb-main-articles">
					<ul class="epkb-ml-articles-list">    <?php
						foreach ( $level_1_articles as $article_id => $article_title ) {

							if ( ! EPKB_Utilities::is_article_allowed_for_current_user( $article_id ) ) {
								continue;
							}   ?>

							<li><?php $this->single_article_link( $article_title, $article_id, EPKB_Layout::CLASSIC_LAYOUT ); ?></li>    <?php

							$category_articles_number ++;
						} ?>
					</ul>
				</div> <?php

				if ( ! empty( $level_2_categories ) ) { ?>
					<!-- Level 2 Categories -->
					<div class="epkb-ml-2-lvl-categories<?php echo $use_show_more ? ' ' . 'epkb-ml-2-lvl-categories--collapsed' : ''; ?>">   <?php

						foreach ( $level_2_categories as $level_2_cat_id => $level_3_categories ) {

							// level 2 category container start
							echo '<div class="epkb-ml-2-lvl-category-container' . ( $use_show_more ? '' : ' ' . 'epkb-ml-2-lvl-category--active' ) . '">';

							$this->display_classic_sub_category( 2, $level_2_cat_id, $categories_icons, $category_articles_number );

							if ( ! empty( $level_3_categories ) ) { ?>

								<!-- Level 3 Categories -->
								<div class="epkb-ml-3-lvl-categories<?php echo $use_show_more ? ' ' . 'epkb-ml-3-lvl-categories--collapsed' : ''; ?>">  <?php

									foreach ( $level_3_categories as $level_3_cat_id => $level_4_categories ) {

										// level 3 category container start
										echo '<div class="epkb-ml-3-lvl-category-container' . ( $use_show_more ? '' : ' ' . 'epkb-ml-3-lvl-category--active' ) . '">';

										$this->display_classic_sub_category( 3, $level_3_cat_id, $categories_icons, $category_articles_number );

										if ( ! empty( $level_4_categories ) ) { ?>

											<!-- Level 4 Categories -->
											<div class="epkb-ml-4-lvl-categories<?php echo $use_show_more ? ' ' . 'epkb-ml-4-lvl-categories--collapsed' : ''; ?>">  <?php

												foreach ( $level_4_categories as $level_4_cat_id => $level_5_categories ) {

													// level 4 category container start
													echo '<div class="epkb-ml-4-lvl-category-container' . ( $use_show_more ? '' : ' ' . 'epkb-ml-4-lvl-category--active' ) . '">';

													$this->display_classic_sub_category( 4, $level_4_cat_id, $categories_icons, $category_articles_number );

													if ( ! empty( $level_5_categories ) ) { ?>

														<!-- Level 5 Categories -->
														<div class="epkb-ml-5-lvl-categories<?php echo $use_show_more ? ' ' . 'epkb-ml-5-lvl-categories--collapsed' : ''; ?>">  <?php

															foreach ( $level_5_categories as $level_5_cat_id => $level_6_categories ) {

																// level 5 category container start
																echo '<div class="epkb-ml-5-lvl-category-container' . ( $use_show_more ? '' : ' ' . 'epkb-ml-5-lvl-category--active' ) . '">';

																$this->display_classic_sub_category( 5, $level_5_cat_id, $categories_icons, $category_articles_number );

																// level 5 category container end
																echo '</div>';
															} ?>

														</div>  <?php
													}

													// level 4 category container end
													echo '</div>';
												} ?>

											</div>  <?php
										}

										// level 3 category container end
										echo '</div>';
									} ?>

								</div>  <?php
							}

							// level 2 category container end
							echo '</div>';
						} ?>

					</div>  <?php
				} ?>

			</div>  <?php

			if ( $category_articles_number == 0 && empty( $level_2_categories ) ) {
				$articles_coming_soon_msg = $this->kb_config['category_empty_msg']; ?>
				<!-- Section Footer -->
				<div class="epkb-category-section__footer">
					<div class="epkb-ml-articles-coming-soon">
						<?php echo esc_html( $articles_coming_soon_msg ); ?>
					</div>
				</div>          <?php
			} else if ( $use_show_more ) {

				$articles_text = $category_articles_number == 1 ? $this->kb_config['ml_categories_articles_article_text'] : $this->kb_config['ml_categories_articles_articles_text'];			?>
				<!-- Section Footer -->
				<div class="epkb-category-section__footer">
					<div class="epkb-ml-article-count"><span><?php echo esc_html( $category_articles_number . ' ' . $articles_text ); ?></span></div>
					<div class="epkb-ml-articles-show-more">
						<span class="epkbfa epkbfa-plus epkb-ml-articles-show-more__show-more__icon"></span>
					</div>
				</div>          <?php
			}   ?>

		</section>  <?php
	}

	/**
	 * Display Sub Category and its Articles for Classic Layout - used for 2nd, 3rd, 4th, 5th levels
	 *
	 * @param $cat_level
	 * @param $sub_cat_id
	 * @param $categories_icons
	 * @param $category_articles_number
	 */
	private function display_classic_sub_category( $cat_level, $sub_cat_id, $categories_icons, &$category_articles_number ) {

		$sub_category_name = isset( $this->articles_seq_data[ $sub_cat_id ][0] ) ? $this->articles_seq_data[ $sub_cat_id ][0] : '';
		if ( empty( $sub_category_name ) ) {
			return;
		}

		$sub_cat_icon = EPKB_KB_Config_Category::get_category_icon( $sub_cat_id, $categories_icons );
		$use_show_more = $this->kb_config['ml_categories_articles_collapse_categories'] == 'all_collapsed'; ?>

		<div class="epkb-ml-<?php echo esc_attr( $cat_level ); ?>-lvl-category__name">
            <span class="epkb-ml-<?php echo esc_attr( $cat_level ); ?>-lvl-category__icon">   <?php
	            if ( $sub_cat_icon['type'] == 'image' ) { ?>
		            <img class="epkb-cat-icon epkb-cat-icon--image" src="<?php echo esc_url( $sub_cat_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $sub_cat_icon['image_alt'] ); ?>"> <?php
	            } else { ?>
		            <span class="epkb-cat-icon epkbfa <?php echo esc_html( $sub_cat_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $sub_cat_icon['name'] ); ?>"></span>    <?php
	            } ?>
            </span>
			<span class="epkb-ml-<?php echo esc_attr( $cat_level ); ?>-lvl-category__text"><?php echo esc_html( $sub_category_name ); ?></span> <?php
			if ( $use_show_more ) { ?>
				<div class="epkb-ml-<?php echo esc_attr( $cat_level ); ?>-lvl-category__show-more"><span class="epkbfa epkbfa-plus epkb-ml-<?php echo esc_attr( $cat_level ); ?>-lvl-category__show-more__icon"></span></div>   <?php
			}   ?>
		</div>    <?php

		// Retrieve sub articles
		$sub_articles = array();
		if ( isset( $this->articles_seq_data[ $sub_cat_id ] ) ) {
			$sub_articles = $this->articles_seq_data[ $sub_cat_id ];
			unset( $sub_articles[0] );
			unset( $sub_articles[1] );
		} ?>

		<ul class="epkb-ml-<?php echo esc_attr( $cat_level ); ?>-lvl-article-list<?php echo $use_show_more ? ' ' . 'epkb-ml-' . esc_attr( $cat_level ) . '-lvl-article-list--collapsed' : ''; ?>"> <?php

			foreach ( $sub_articles as $article_id => $article_title ) {

				if ( ! EPKB_Utilities::is_article_allowed_for_current_user( $article_id ) ) {
					continue;
				} ?>

				<li><?php $this->single_article_link( $article_title, $article_id, EPKB_Layout::CLASSIC_LAYOUT ); ?></li>    <?php

				$category_articles_number ++;
			}

			$articles_coming_soon_msg = $this->kb_config['category_empty_msg'];
			if ( empty( $sub_articles ) && ! empty( $articles_coming_soon_msg ) ) {
				echo '<li class="epkb-ml-articles-coming-soon">' . esc_html( $articles_coming_soon_msg ) . '</li>';
			}   ?>

		</ul>   <?php
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

		// General -------------------------------------------/
		if ( !empty( $kb_config['background_color'] ) ) {
			$output .= '
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout {
				padding: 20px!important;
				background-color: ' . $kb_config['background_color'] . '!important;
			}';
		}

		// Container -----------------------------------------/
		$border_style = 'none';
		if ( $kb_config['section_border_width'] > 0 ) {
			$border_style = 'solid';
		}
		$output .= '
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-category-section {
				border-color: ' . $kb_config['section_border_color']. ' !important;
				border-width:' . $kb_config['section_border_width']. 'px !important;
				border-radius:' . $kb_config['section_border_radius']. 'px !important;
				border-style: ' . $border_style. ' !important;
			}';

		// Headings  -----------------------------------------/
		if ( $kb_config['section_head_category_icon_location'] == 'top' ) {
			$output .= '
				#epkb-ml__module-categories-articles {
					margin-top:60px;
				}
			';
		}

		if ( $kb_config['ml_categories_articles_top_category_icon_bg_color_toggle'] == 'off' ) {
			$output .= '
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-category-section__head .epkb-cat-icon {
				background-color: transparent !important;
			}';
		}

		$output .= '
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-category-section__head .epkb-cat-icon--font {
		        font-size: ' . $kb_config['section_head_category_icon_size'] . 'px;
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-category-section__head .epkb-cat-icon--image {
			    background-color: ' . $kb_config['ml_categories_articles_top_category_icon_bg_color'] . ';
			    width: ' . ( $kb_config['section_head_category_icon_size'] + 20 ) . 'px;
			    max-width: ' . ( $kb_config['section_head_category_icon_size'] + 20 ) . 'px;
			    height: ' . ( $kb_config['section_head_category_icon_size'] + 20 ) . 'px;
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-category-section__head_title__text {
			    color: ' . $kb_config['section_head_font_color'] . ';
			}';

		$output .= '
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-category-section .epkb-category-section__head_icon .epkb-cat-icon--font {
			    color: ' . $kb_config['section_head_category_icon_color'] . ';
			    background-color: ' . $kb_config['ml_categories_articles_top_category_icon_bg_color'] . '; 
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-category-section__head_desc {
			    color: ' . $kb_config['section_head_description_font_color'] . ';
			}
			';

		// Articles  -----------------------------------------/

		// Variable - No CSS required
		// Minimum  - Min Height of Article List
		// Maximum  - Max Height of Article List with Overflow and scroll bar
		if ( $kb_config['section_box_height_mode'] == 'section_fixed_height' )  {
			$output .= '
			#epkb-ml__module-categories-articles .epkb-ml-classic-layout--height-fixed .epkb-category-section__body{
				height: ' . $kb_config['section_body_height'] . 'px;
				overflow:auto;
			}';
		}
		if ( $kb_config['section_box_height_mode'] == 'section_min_height' )  {
			$output .= '
			#epkb-ml__module-categories-articles .epkb-ml-classic-layout--height-fixed .epkb-category-section {
				min-height: ' . $kb_config['section_body_height'] . 'px;
			}';
		}
		if ( $kb_config['ml_categories_articles_collapse_categories'] == 'all_collapsed' ) {
			$output .= '
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-ml-articles-show-more {
			    border-color: ' . $kb_config['section_head_category_icon_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-ml-articles-show-more:hover {
			    color: #fff;
			    background-color: ' . $kb_config['section_head_category_icon_color'] . ';
			}';
		}

		$output .= '
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-article-inner .epkb-article__icon {
			    color: ' . $kb_config['article_icon_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-article-inner .epkb-article__text {
			    color: ' . $kb_config['article_font_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-ml-2-lvl-category__icon .epkb-cat-icon {
			    color: ' . $kb_config['section_category_icon_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-ml-2-lvl-category__text {
			    color: ' . $kb_config['section_category_font_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-ml-3-lvl-category__icon .epkb-cat-icon {
			    color: ' . $kb_config['section_category_icon_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-ml-3-lvl-category__text {
			    color: ' . $kb_config['section_category_font_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-ml-4-lvl-category__icon .epkb-cat-icon {
			    color: ' . $kb_config['section_category_icon_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-ml-4-lvl-category__text {
			    color: ' . $kb_config['section_category_font_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-ml-5-lvl-category__icon .epkb-cat-icon {
			    color: ' . $kb_config['section_category_icon_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-ml-5-lvl-category__text {
			    color: ' . $kb_config['section_category_font_color'] . ';
			}';

		$output .= '
			    #epkb-ml__module-categories-articles #epkb-ml-classic-layout .epkb-category-section__body li {
			        padding-top: ' . $kb_config['article_list_spacing'] . 'px !important;
			        padding-bottom: ' . $kb_config['article_list_spacing'] . 'px !important;
		            line-height: 1 !important;
			    }';

		return $output;
	}

	public function generate_non_modular_kb_main_page() {
		// for compatibility reasons
	}
}