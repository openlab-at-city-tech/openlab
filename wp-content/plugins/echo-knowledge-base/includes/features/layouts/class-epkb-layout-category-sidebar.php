<?php

/**
 *  Outputs Top Categories Navigation Sidebar on Article Pages and Category Archive Pages.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Layout_Category_Sidebar {

	/**
	 * Top Categories Navigation Sidebar - show a list of top or sibling KB Categories, each with link to the Category Archive page and total article count
	 *
	 * @param $kb_id
	 * @param $kb_config
	 * @param $parent_id
	 * @param $active_id
	 * @return string
	 */
	public static function get_layout_categories_list( $kb_id, $kb_config, $parent_id = 0, $active_id = 0 ) {

		$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$category_seq_data = EPKB_WPML::apply_category_language_filter( $category_seq_data );
		}

		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$articles_seq_data = EPKB_WPML::apply_article_language_filter( $articles_seq_data );
		}

		$top_categories = [];

		// determine what categories will be displayed in the Category Focused Layout list
		if ( empty( $parent_id ) || ( isset( $kb_config['categories_layout_list_mode'] ) && $kb_config['categories_layout_list_mode'] == 'list_top_categories' ) ) {

			foreach ( $category_seq_data as $box_category_id => $box_sub_categories ) {

				if ( empty( $articles_seq_data[$box_category_id] ) ) {
					continue;
				}

				$top_categories[$box_category_id] = $articles_seq_data[$box_category_id][0];
			}

		} else {

			$sub_category_seq_data = self::array_search_key_recursively( $parent_id, $category_seq_data );

			if ( empty( $sub_category_seq_data ) ) {
				foreach ( $category_seq_data as $box_category_id => $box_sub_categories ) {

					if ( empty( $articles_seq_data[$box_category_id] ) ) {
						continue;
					}

					$top_categories[$box_category_id] = $articles_seq_data[$box_category_id][0];
				}

			} else {

				foreach ( $sub_category_seq_data as $box_category_id => $box_sub_categories ) {

					if ( empty( $articles_seq_data[$box_category_id] ) ) {
						continue;
					}

					$top_categories[$box_category_id] = $articles_seq_data[$box_category_id][0];
				}
			}
		}

		if ( empty( $top_categories ) ) {
			return '';
		}

		ob_start();

		self::generate_sidebar_CSS( $kb_config );		?>

		<div class="eckb-article-cat-layout-list eckb-article-cat-layout-list-reset">
			<div class="eckb-article-cat-layout-list__inner">
				<div class="eckb-acll__title"><?php echo esc_html( $kb_config['category_focused_menu_heading_text'] ); ?></div>
				<ul>						<?php

					// display each category in a list
					$tax_name = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
					foreach ( $top_categories as $top_category_id => $top_category_name ) {
						$term_link = EPKB_Utilities::get_term_url( $top_category_id, $tax_name );
						$active = ! empty( $active_id ) && $active_id == $top_category_id;
						$count = EPKB_Categories_DB::get_category_count( $kb_id, $top_category_id ); ?>

						<li class="eckb--acll__cat-item <?php echo ( $active ? 'eckb--acll__cat-item--active' : '' ); ?>">
							<a href="<?php echo esc_url( $term_link ); ?>">
								<div>
									<span class="eckb-acll__cat-item__name">
										<?php echo esc_html( $top_category_name ); ?>
									</span>
								</div>
								<div>
									<span class="eckb-acll__cat-item__count">
										<?php echo esc_html( $count ); ?>
									</span>
								</div>
							</a>
						</li>						<?php
					}	?>

				</ul>
			</div>
		</div>			<?php

		return ob_get_clean();
	}

	private static function generate_sidebar_CSS( $kb_config ) {

		$categories_box_typography_styles = EPKB_Utilities::get_typography_config( $kb_config['categories_box_typography'] );   ?>

		<style>
			.eckb-acll__title {
				color:<?php echo esc_attr( $kb_config['category_box_title_text_color'] ); ?>;
			}
			.eckb-article-cat-layout-list {
				background-color:<?php echo esc_attr( $kb_config['category_box_container_background_color'] ); ?>;
				<?php echo esc_attr( $categories_box_typography_styles ); ?>
			}
			.eckb-article-cat-layout-list a {
				<?php echo esc_attr( $categories_box_typography_styles ); ?>
			}
			body .eckb-acll__cat-item__name {
				color:<?php echo esc_attr( $kb_config['category_box_category_text_color'] ); ?>;
				<?php echo esc_attr( $categories_box_typography_styles ); ?>
			}
			.eckb-acll__cat-item__count {
				color:<?php echo esc_attr( $kb_config['category_box_count_text_color'] ); ?>;
				background-color:<?php echo esc_attr( $kb_config['category_box_count_background_color'] ); ?>;
				border:solid 1px <?php echo esc_attr( $kb_config['category_box_count_border_color'] ); ?>!important;
			}
		</style>    <?php
	}

	/**
	 * Search for value in array by key recursively
	 * @param $needle_key
	 * @param $array
	 * @return array
	 */
	private static function array_search_key_recursively( $needle_key, $array ) {
		foreach ( $array as $key => $value ) {
			if ( $key == $needle_key ) {
				return $value;
			}

			if ( is_array( $value ) ) {
				$result = self::array_search_key_recursively( $needle_key, $value );
				if ( ! empty( $result ) ) {
					return $result;
				}
			}
		}

		return [];
	}
}
