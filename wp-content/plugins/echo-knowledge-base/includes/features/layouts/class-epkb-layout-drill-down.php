<?php

/**
 *  Outputs the Drill-Down Layout for knowledge base main page.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Layout_Drill_Down extends EPKB_Layout {

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

		$col_class = 'epkb-ml-top-categories-button--' . $this->get_nof_columns_int() . '-col' ;

		$categories_icons = $this->get_category_icons();				?>

		<div id="epkb-ml-drill-down-layout" class="epkb-layout-container">

			<!-- Top Level Categories -->
			<div class="epkb-ml-drill-down-layout-categories-container">

				<!-- Top Level Categories Button -->
				<div class="epkb-ml-top-categories-button-container <?php echo esc_html( $col_class ); ?>">                    <?php
					foreach ( $this->category_seq_data as $category_id => $level_2_categories ) {

						$category_name = isset( $this->articles_seq_data[ $category_id ][0] ) ? $this->articles_seq_data[ $category_id ][0] : '';
						if ( empty( $category_name ) ) {
							continue;
						}

						$this->display_drill_down_category_button_lvl_1( $category_id, $categories_icons, $category_name );
					} ?>
				</div>

				<!-- All Categories content -->
				<div class="epkb-ml-all-categories-content-container">

					<button class="epkb-back-button">
						<span class="epkb-back-button__icon epkbfa epkbfa-arrow-left"></span>
						<span class="epkb-back-button__text"><?php echo esc_html( $this->kb_config['ml_categories_articles_back_button_text'] ); ?></span>
					</button>   <?php

					foreach ( $this->category_seq_data as $category_id => $level_2_categories ) {
						$this->display_drill_down_top_category_content( $category_id, $categories_icons, $level_2_categories );
					}   ?>

				</div>

			</div>

		</div>  <?php
	}

	/**
	 * Display button of top category for Drill Down Layout
	 *
	 * @param $category_id
	 * @param $categories_icons
	 * @param $category_name
	 */
	private function display_drill_down_category_button_lvl_1( $category_id, $categories_icons, $category_name ) {

		$category_icon = EPKB_KB_Config_Category::get_category_icon( $category_id, $categories_icons );
		$category_title_tag_escaped = EPKB_Utilities::sanitize_html_tag( $this->kb_config['ml_categories_articles_category_title_html_tag'] );

		switch ( $this->kb_config['section_head_category_icon_location'] ) {
			case 'no_icons':            ?>
				<section id="epkb-1-lvl-id-<?php echo esc_attr( $category_id ); ?>" data-cat-level="1" data-cat-id="<?php echo esc_attr( $category_id ); ?>" class="epkb-ml-top__cat-container epkb-ml-top__cat-container--none-location">					<?php
					echo '<' . esc_html( $category_title_tag_escaped ) . ' ' . 'class="epkb-ml-top__cat-title"' . '>' . esc_html( $category_name ) . '</' . esc_html( $category_title_tag_escaped ) . '>'; ?>
				</section>				<?php
				break;

			case 'top':
			case 'left':                ?>
				<section id="epkb-1-lvl-id-<?php echo esc_attr( $category_id ); ?>" data-cat-level="1" data-cat-id="<?php echo esc_attr( $category_id ); ?>" class="epkb-ml-top__cat-container epkb-ml-top__cat-container--<?php echo esc_attr( $this->kb_config['section_head_category_icon_location'] ); ?>-location">

					<!-- Icon / Image -->					<?php
					if ( $category_icon['type'] == 'image' ) { ?>
						<img class="epkb-ml-top__cat-icon epkb-ml-top__cat-icon--image" src="<?php echo esc_url( $category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $category_icon['image_alt'] ); ?>">  <?php
					} else { ?>
						<div class="epkb-ml-top__cat-icon epkb-ml-top__cat-icon--font epkbfa <?php echo esc_attr( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $category_icon['name'] ); ?>"></div>	<?php
					} ?>

					<!-- Category Name -->					<?php
					echo '<' . esc_html( $category_title_tag_escaped ) . ' ' . 'class="epkb-ml-top__cat-title"' . '>' . esc_html( $category_name ) . '</' . esc_html( $category_title_tag_escaped ) . '>'; ?>

				</section>				<?php
				break;

			case 'right':               ?>
				<section id="epkb-1-lvl-id-<?php echo esc_attr( $category_id ); ?>" data-cat-level="1" data-cat-id="<?php echo esc_attr( $category_id ); ?>" class="epkb-ml-top__cat-container epkb-ml-top__cat-container--right-location">

					<!-- Category Name -->					<?php
					echo '<' . esc_html( $category_title_tag_escaped ) . ' ' . 'class="epkb-ml-top__cat-title"' . '>' . esc_html( $category_name ) . '</' . esc_html( $category_title_tag_escaped ) . '>'; ?>

					<!-- Icon / Image -->					<?php
					if ( $category_icon['type'] == 'image' ) { ?>
						<img class="epkb-ml-top__cat-icon epkb-ml-top__cat-icon--image" src="<?php echo esc_url( $category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $category_icon['image_alt'] ); ?>">  <?php
					} else { ?>
						<div class="epkb-ml-top__cat-icon epkb-ml-top__cat-icon--font epkbfa <?php echo esc_attr( $category_icon['name'] ); ?>" data-kb-category-icon="<?php echo esc_attr( $category_icon['name'] ); ?>"></div>	<?php
					} ?>

				</section>				<?php
				break;
		}
	}

	/**
	 * Display content of top category for Drill Down Layout
	 *
	 * @param $category_id
	 * @param $categories_icons
	 * @param $level_2_categories
	 */
	private function display_drill_down_top_category_content( $category_id, $categories_icons, $level_2_categories ) {

		// lvl 1 categories: header, description, articles, and child lvl 2 categories
		$this->display_drill_down_category_container( 0, $category_id, $categories_icons, $level_2_categories, 1 );
		if ( empty( $level_2_categories ) ) {
			return;
		}

		// lvl 2 categories: header, description, articles, and child lvl 3 categories
		foreach ( $level_2_categories as $level_2_category_id => $level_3_categories ) {
			$this->display_drill_down_category_container( $category_id, $level_2_category_id, $categories_icons, $level_3_categories, 2 );
			if ( empty( $level_3_categories ) ) {
				continue;
			}

			// lvl 3 categories: header, description, articles, and child lvl 4 categories
			foreach ( $level_3_categories as $level_3_category_id => $level_4_categories ) {
				$this->display_drill_down_category_container( $level_2_category_id, $level_3_category_id, $categories_icons, $level_4_categories, 3 );
				if ( empty( $level_4_categories ) ) {
					continue;
				}

				// lvl 4 categories: header, description, articles, and child lvl 5 categories
				foreach ( $level_4_categories as $level_4_category_id => $level_5_categories ) {
					$this->display_drill_down_category_container( $level_3_category_id, $level_4_category_id, $categories_icons, $level_5_categories, 4 );
					if ( empty( $level_5_categories ) ) {
						continue;
					}

					// lvl 5 categories: header, description, articles - no support for child categories lvl 6 and higher
					foreach ( $level_5_categories as $level_5_category_id => $unused_level_6_categories ) {
						$this->display_drill_down_category_container( $level_4_category_id, $level_5_category_id, $categories_icons, [], 5 );
					}
				}
			}
		}
	}

	/**
	 * Display sub categories list
	 *
	 * @param $parent_category_id
	 * @param $category_id
	 * @param $categories_icons
	 * @param $sub_categories
	 * @param $category_lvl
	 */
	private function display_drill_down_category_container( $parent_category_id, $category_id, $categories_icons, $sub_categories, $category_lvl ) {

		/**
		 * Category description and articles
		 */
		$category_desc = isset( $this->articles_seq_data[ $category_id ][1] ) && $this->kb_config['section_desc_text_on'] == 'on' ? $this->articles_seq_data[ $category_id ][1] : '';

		// retrieve level 1 articles
		$articles_list = array();
		if ( isset( $this->articles_seq_data[ $category_id ] ) ) {
			$articles_list = $this->articles_seq_data[ $category_id ];
			unset( $articles_list[0] );
			unset( $articles_list[1] );
		}

		$no_articles_class = '';

		// If no articles then add class for articles coming soon message.
		if ( empty( $articles_list ) && empty( $sub_categories ) ) {
			$no_articles_class = 'epkb-ml__cat-content--no-articles';
		}   ?>

		<!-- Category Description and Articles -->
		<div class="epkb-ml__cat-content epkb-ml-<?php echo esc_attr( $category_lvl ); ?>-lvl__cat-content <?php echo esc_attr( $no_articles_class ); ?>" data-cat-level="<?php echo esc_attr( $category_lvl ); ?>" data-cat-id="<?php echo esc_attr( $category_id ); ?>" data-parent-cat-id="<?php echo esc_attr( $parent_category_id ); ?>">   <?php

			if ( ! empty( $category_desc ) || ! empty( $articles_list ) ) {

				// If no articles exist, add Class to adjust Category Desc width.
				$desc_articles_no_articles_class = empty( $articles_list ) ? 'epkb-ml-' . $category_lvl . '-lvl-desc-articles--no-articles' : '';

				// If no description exist, add Class to adjust Category Desc width.
				$desc_articles_no_desc_class = empty( $category_desc ) ? 'epkb-ml-' . $category_lvl . '-lvl-desc-articles--no-desc' : '';   ?>

				<div class="epkb-ml-<?php echo esc_attr( $category_lvl ); ?>-lvl-desc-articles <?php echo esc_attr( $desc_articles_no_articles_class . ' ' . $desc_articles_no_desc_class ); ?>">    <?php

					if ( ! empty( $category_desc ) ) {   ?>
						<div class="epkb-ml-<?php echo esc_attr( $category_lvl ); ?>-lvl__desc"><?php echo wp_kses_post( $category_desc ); ?></div> <?php
					}

					if ( ! empty( $articles_list ) ) {  ?>
						<div class="epkb-ml-<?php echo esc_attr( $category_lvl ); ?>-lvl__articles">

							<div class="epkb-ml-articles-list">    <?php

								// limit number of article columns
								$columns = $this->get_articles_listed_in_columns( $articles_list );

								// display the articles in the columns  ?>
								<div class="epkb-ml-articles-list epkb-total-columns-<?php echo esc_attr( count( $columns ) ); ?>">   <?php
									$column_number = 1;
									foreach ( $columns as $articles_in_column ) {			 ?>
										<ul class="epkb-list-column epkb-list-column-<?php echo esc_attr( $column_number ); ?>">   <?php
											foreach ( $articles_in_column as $article ) { ?>
												<li><?php $this->single_article_link( $article['title'], $article['id'], EPKB_Layout::DRILL_DOWN_LAYOUT ); ?></li>  <?php
											} ?>
										</ul>   <?php
										$column_number ++;
									} ?>
								</div>
							</div>
						</div>  <?php
					}   ?>
				</div>            <?php
			}

			// The Category and it's children are completely empty display articles coming soon message.
			if ( empty( $articles_list ) && empty( $sub_categories ) ) {
				$articles_coming_soon_msg = $this->kb_config['category_empty_msg']; ?>
				<div class="epkb-ml-articles-coming-soon"><?php echo esc_html( $articles_coming_soon_msg ); ?></div>            <?php
			}   ?>

		</div>  <?php

		/**
		 * Sub Categories list
		 */
		if ( ! empty( $sub_categories ) ) {
			$sub_category_lvl = $category_lvl + 1;    ?>
			<!-- Sub Categories List -->
			<div class="epkb-ml-categories-button-container epkb-ml-<?php echo esc_attr( $sub_category_lvl ); ?>-lvl-categories-button-container" data-cat-level="<?php echo esc_attr( $category_lvl ); ?>" data-cat-id="<?php echo esc_attr( $category_id ); ?>">  <?php
				foreach ( $sub_categories as $sub_category_id => $level_3_categories ) {

					$sub_category_name = isset( $this->articles_seq_data[ $sub_category_id ][0] ) ? $this->articles_seq_data[ $sub_category_id ][0] : '';
					if ( empty( $sub_category_name ) ) {
						continue;
					}

					$sub_category_icon = EPKB_KB_Config_Category::get_category_icon( $sub_category_id, $categories_icons ); ?>

					<section id="epkb-ml-<?php echo esc_attr( $sub_category_lvl ); ?>-lvl-<?php echo esc_attr( $sub_category_id ); ?>" class="epkb-ml__cat-container epkb-ml-<?php echo esc_attr( $sub_category_lvl ); ?>-lvl__cat-container"
					         data-cat-level="<?php echo esc_attr( $sub_category_lvl ); ?>" data-cat-id="<?php echo esc_attr( $sub_category_id ); ?>" data-parent-cat-id="<?php echo esc_attr( $category_id ); ?>">  <?php
						if ( $sub_category_icon['type'] == 'image' ) { ?>
							<img class="epkb-ml-<?php echo esc_attr( $sub_category_lvl ); ?>-lvl__cat-icon epkb-ml-<?php echo esc_attr( $sub_category_lvl ); ?>-lvl__cat-icon--image"
							     src="<?php echo esc_url( $sub_category_icon['image_thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $sub_category_icon['image_alt'] ); ?>">  <?php
						} else { ?>
							<div class="epkb-ml-<?php echo esc_attr( $sub_category_lvl ); ?>-lvl__cat-icon epkb-ml-<?php echo esc_attr( $sub_category_lvl ); ?>-lvl__cat-icon--font epkbfa <?php echo esc_attr( $sub_category_icon['name'] ); ?>"
							     data-kb-category-icon="<?php echo esc_attr( $sub_category_icon['name'] ); ?>"></div>    <?php
						} ?>
						<div class="epkb-ml-<?php echo esc_attr( $sub_category_lvl ); ?>-lvl__cat-title"><?php echo esc_html( $sub_category_name ); ?></div>
					</section>  <?php
				}   ?>
			</div>        <?php
		}
	}

	/**
	 * Returns inline styles for Categories & Articles Module
	 *
	 * @param $kb_config
	 *
	 * @return string
	 */
	public static function get_inline_styles( $kb_config ) {


		$output = '
		/* CSS for Categories & Articles Module
		-----------------------------------------------------------------------*/';

		// Drill Down Layout ----------------------------------------------------------------------------/
		if ( $kb_config['ml_categories_articles_top_category_icon_bg_color_toggle'] == 'off' ) {
			$output .= '
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-top-categories-button-container .epkb-ml-top__cat-icon {
				background-color: transparent !important;
			}';
		}

		// Add General Typography
		if ( ! empty( $kb_config['general_typography']['font-family'] ) ) {
			$output .= '
			#epkb-ml-drill-down-layout .epkb-ml-top__cat-title,
			#epkb-ml-drill-down-layout .epkb-back-button__text {
				    ' . 'font-family:' . $kb_config['general_typography']['font-family'] . ' !important;' . '
				}';
		}

		$border_style = 'none';
		if ( $kb_config['section_border_width'] > 0 ) {
			$border_style = 'solid';
		}
		$output .= '
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-top__cat-container {
				border-color: ' . $kb_config['section_border_color'] . ' !important;
				border-width:' . $kb_config['section_border_width'] . 'px !important;
				border-radius:' . $kb_config['section_border_radius'] . 'px !important;
				border-style: ' . $border_style. ' !important;
			}';

		$output .= '
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-top-categories-button-container .epkb-ml-top__cat-icon--font {
			    font-size: ' . $kb_config['section_head_category_icon_size'] . 'px;
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-top-categories-button-container .epkb-ml-top__cat-title {
			    color: ' . $kb_config['section_head_font_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-top-categories-button-container .epkb-ml-top__cat-icon {
			    color: ' . $kb_config['section_head_category_icon_color'] . ';
			    background-color: ' . $kb_config['ml_categories_articles_top_category_icon_bg_color'] . ';
			    width: ' . ( $kb_config['section_head_category_icon_size'] + 40 ) . 'px;
			    height: ' . ( $kb_config['section_head_category_icon_size'] + 40 ) . 'px;
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-article-inner .epkb-article__icon {
			    color: ' . $kb_config['article_icon_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-article-inner .epkb-article__text {
			    color: ' . $kb_config['article_font_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-all-categories-content-container,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-2-lvl__cat-content {
			    background-color: ' . $kb_config['ml_categories_articles_article_bg_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-1-lvl__cat-content .epkb-ml-1-lvl-desc-articles .epkb-ml-1-lvl__desc,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-2-lvl__cat-content .epkb-ml-2-lvl-desc-articles .epkb-ml-2-lvl__desc,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-3-lvl__cat-content .epkb-ml-3-lvl-desc-articles .epkb-ml-3-lvl__desc,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-4-lvl__cat-content .epkb-ml-4-lvl-desc-articles .epkb-ml-4-lvl__desc,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-5-lvl__cat-content .epkb-ml-5-lvl-desc-articles .epkb-ml-5-lvl__desc,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-1-lvl__cat-content .epkb-ml-articles-coming-soon,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-2-lvl__cat-content .epkb-ml-articles-coming-soon,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-3-lvl__cat-content .epkb-ml-articles-coming-soon,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-4-lvl__cat-content .epkb-ml-articles-coming-soon,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-5-lvl__cat-content .epkb-ml-articles-coming-soon {
			    color: ' . $kb_config['section_head_description_font_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-back-button {
			    background-color: ' . $kb_config['ml_categories_articles_back_button_bg_color'] . '!important;
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-back-button:hover {
			    background-color: ' . EPKB_Utilities::darken_hex_color( $kb_config['ml_categories_articles_back_button_bg_color'], 0.2 )  . '!important;
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-top-categories-button-container .epkb-ml-top__cat-container,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-top-categories-button-container .epkb-ml-top__cat-container:hover {
			    border-color: ' . $kb_config['section_border_color'] . ' !important;
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-top-categories-button-container .epkb-ml-top__cat-container--active,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-top-categories-button-container .epkb-ml-top__cat-container--active:hover {
			    box-shadow: 0 0 0 4px ' . $kb_config['section_border_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-2-lvl-categories-button-container .epkb-ml-2-lvl__cat-container,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-2-lvl-categories-button-container .epkb-ml-2-lvl__cat-container:hover,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-3-lvl-categories-button-container .epkb-ml-3-lvl__cat-container,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-3-lvl-categories-button-container .epkb-ml-3-lvl__cat-container:hover,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-4-lvl-categories-button-container .epkb-ml-4-lvl__cat-container,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-4-lvl-categories-button-container .epkb-ml-4-lvl__cat-container:hover,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-5-lvl-categories-button-container .epkb-ml-5-lvl__cat-container,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-5-lvl-categories-button-container .epkb-ml-5-lvl__cat-container:hover {
			    border-color: ' . $kb_config['section_border_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-1-lvl-categories-button-container .epkb-ml__cat-container--active,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-2-lvl-categories-button-container .epkb-ml__cat-container--active,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-3-lvl-categories-button-container .epkb-ml__cat-container--active,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-4-lvl-categories-button-container .epkb-ml__cat-container--active,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-5-lvl-categories-button-container .epkb-ml__cat-container--active,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-1-lvl-categories-button-container .epkb-ml__cat-container--active:hover,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-2-lvl-categories-button-container .epkb-ml__cat-container--active:hover,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-3-lvl-categories-button-container .epkb-ml__cat-container--active:hover,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-4-lvl-categories-button-container .epkb-ml__cat-container--active:hover,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-5-lvl-categories-button-container .epkb-ml__cat-container--active:hover{
			    box-shadow: 0px 1px 0 0px ' . $kb_config['section_category_icon_color'] . ';
			    border-color: ' . $kb_config['section_category_icon_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-2-lvl-categories-button-container .epkb-ml-2-lvl__cat-container .epkb-ml-2-lvl__cat-icon,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-3-lvl-categories-button-container .epkb-ml-3-lvl__cat-container .epkb-ml-3-lvl__cat-icon,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-4-lvl-categories-button-container .epkb-ml-4-lvl__cat-container .epkb-ml-4-lvl__cat-icon,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-5-lvl-categories-button-container .epkb-ml-5-lvl__cat-container .epkb-ml-5-lvl__cat-icon {
			    color: ' . $kb_config['section_category_icon_color'] . ';
			}
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-2-lvl-categories-button-container .epkb-ml-2-lvl__cat-container .epkb-ml-2-lvl__cat-title,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-3-lvl-categories-button-container .epkb-ml-3-lvl__cat-container .epkb-ml-3-lvl__cat-title,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-4-lvl-categories-button-container .epkb-ml-4-lvl__cat-container .epkb-ml-4-lvl__cat-title,
			#epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-5-lvl-categories-button-container .epkb-ml-5-lvl__cat-container .epkb-ml-5-lvl__cat-title {
			    color: ' . $kb_config['section_category_font_color'] . ';
			}';

		$output .= '
		    #epkb-ml__module-categories-articles #epkb-ml-drill-down-layout .epkb-ml-articles-list li {
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