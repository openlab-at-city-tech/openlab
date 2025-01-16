<?php

/**
 *
 * BASE THEME class that every theme should extend
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
abstract class EPKB_Layout {

	const BASIC_LAYOUT = 'Basic';
	const TABS_LAYOUT = 'Tabs';
	const CATEGORIES_LAYOUT = 'Categories';
	const CLASSIC_LAYOUT = 'Classic';
	const DRILL_DOWN_LAYOUT = 'Drill-Down';
	const SIDEBAR_LAYOUT = 'Sidebar';
	const GRID_LAYOUT = 'Grid';

	protected $kb_config;
	protected $kb_id;
	protected $category_seq_data;
	protected $articles_seq_data;
	protected $has_kb_categories = true;
	protected $active_theme = 'unknown';
	protected $displayed_article_ids = array();

	/**
	 * Show the KB Main page with list of categories and articles
	 *
	 * @param $kb_config
	 * @param bool $is_ordering_wizard_on
	 * @param array $article_seq
	 * @param array $categories_seq
	 */
	public function display_non_modular_kb_main_page( $kb_config, $is_ordering_wizard_on=false, $article_seq=array(), $categories_seq=array() ) {

		// set initial data
		$this->kb_config = $kb_config;
		$this->kb_id = $kb_config['id'];

		// set category and article sequence
		if ( $is_ordering_wizard_on && ! empty( $article_seq ) && ! empty( $categories_seq ) ) {
			$this->articles_seq_data = $article_seq;
			$this->category_seq_data = $categories_seq;
		} else {
			$this->category_seq_data = EPKB_Utilities::get_kb_option( $this->kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
			$this->articles_seq_data = EPKB_Utilities::get_kb_option( $this->kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		}

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$this->category_seq_data = EPKB_WPML::apply_category_language_filter( $this->category_seq_data );
			$this->articles_seq_data = EPKB_WPML::apply_article_language_filter( $this->articles_seq_data );
		}

		// check we have categories defined
		$this->has_kb_categories = $this->kb_has_categories();

		// articles with no categories - temporary add one
		if ( isset( $this->articles_seq_data[0] ) ) {
			$this->category_seq_data[0] = array();
		}

		$this->generate_non_modular_kb_main_page();
	}

	/**
	 * Generate content of the KB main page
	 */
	protected abstract function generate_non_modular_kb_main_page();

	/**
	 * Display a link to a KB article.
	 *
	 * @param $title
	 * @param $article_id
	 * @param string $layout
	 */
	public function single_article_link( $title, $article_id, $layout='' ) {

		if ( empty( $article_id ) ) {
			return;
		}

		$this->displayed_article_ids[$article_id] = isset( $this->displayed_article_ids[$article_id] ) ? $this->displayed_article_ids[$article_id] + 1 : 1;
		$seq_no = $this->displayed_article_ids[$article_id];

		EPKB_Utilities::get_single_article_link( $this->kb_config, $title, $article_id, $layout, $seq_no );
	}

	/**
	 * Display a search form for core layouts for non-modular Main Page
	 */
	public function get_search_form() {
		EPKB_KB_Search::get_search_form( $this->kb_config );
	}

	/**
	 * Output inline CSS style based on configuration.
	 *
	 * @param string $styles  A list of Configuration Setting styles
	 * @return string
	 */
	public function get_inline_style( $styles ) {
		return EPKB_Utilities::get_inline_style( $styles, $this->kb_config );
	}

	/**
	 * Output CSS classes based on configuration.
	 *
	 * @param $classes
	 * @return string
	 */
	public function get_css_class( $classes ) {
		return EPKB_Utilities::get_css_class( $classes, $this->kb_config );
	}

	/**
	 * Retrieve category icons.
	 * @return array
	 */
	protected function get_category_icons() {

		// handle Visual Editor with theme preset selection
		if ( EPKB_Utilities::get( 'epkb-editor-page-loaded' ) == '1' && isset( $this->kb_config['theme_presets'] ) && $this->kb_config['theme_presets'] !== 'current' ) {
			$category_icons = EPKB_Core_Utilities::get_or_update_new_category_icons( $this->kb_config, $this->kb_config['theme_presets'] );
			if ( ! empty( $category_icons ) ) {
				return $category_icons;
			}
		}

		return EPKB_KB_Config_Category::get_category_data_option( $this->kb_config['id'] );
	}

	/**
	 * Detect whether the current KB has any category
	 *
	 * @return bool
	 */
	protected function kb_has_categories() {

		// if non-empty categories sequence in DB then nothing to do
		if ( ! empty( $this->category_seq_data ) && is_array( $this->category_seq_data ) ) {
			return true;
		}

		// if no categories in the sequence then query DB directly; return if error
		$category_seq_data = EPKB_KB_Handler::get_refreshed_kb_categories( $this->kb_id, $this->category_seq_data );
		if ( ! is_array( $category_seq_data ) ) {
			return true;
		}

		// re-populate the class
		$this->category_seq_data = $category_seq_data;
		$this->articles_seq_data = EPKB_Utilities::get_kb_option( $this->kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $this->kb_config ) ) {
			$this->category_seq_data = EPKB_WPML::apply_category_language_filter( $this->category_seq_data );
			$this->articles_seq_data = EPKB_WPML::apply_article_language_filter( $this->articles_seq_data );
		}

		return ! empty( $this->category_seq_data );
	}

	/**
	 * Show message that KB does not have any categories
	 */
	public function show_categories_missing_message() {
		
		$kb_post_type = EPKB_KB_Handler::get_post_type( $this->kb_id );
		$kb_category_taxonomy_name = EPKB_KB_Handler::get_category_taxonomy_name( $this->kb_id );
		$manage_articles_url = admin_url( 'edit-tags.php?taxonomy=' . $kb_category_taxonomy_name . '&post_type=' . $kb_post_type );
		$import_url = EPKB_Utilities::is_export_import_enabled() ?
								admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_id ) . '&page=ep'.'kb-kb-configuration#tools__import' )
								: 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/';     ?>

		<section class="eckb-kb-no-content">   <?php

			// when in Ordering Wizard if user has multi-language site and did not select particular language (categories and articles do not show) ask them to switch to a particular language
			if ( EPKB_Utilities::is_wpml_enabled( $this->kb_config ) && ! empty( $_POST['action'] ) && $_POST['action'] == 'epkb_wizard_update_order_view' ) {

				EPKB_HTML_Forms::notification_box_middle( array(
					'type'      => 'error',
					'desc'      => esc_html__( 'No KB categories found. This ordering action must be performed for each language separately. ' .
						'Only one language can be selected for this page in the top admin menu.', 'echo-knowledge-base' ),
				) );
				
			// for users with at least Author access
			} else if ( current_user_can( EPKB_Admin_UI_Access::get_author_capability() ) ) {

				$is_block_main_page = EPKB_Block_Utilities::current_post_has_kb_layout_blocks();
				$is_editor_on = EPKB_Utilities::get( 'action' ) == 'edit' || EPKB_Utilities::get( 'context' ) == 'edit';     ?>
				<h2 class="eckb-kb-no-content-title"><?php $is_block_main_page ?
						printf( esc_html__( 'KB %s Layout Block', 'echo-knowledge-base' ) . '<br>' . esc_html__( 'You do not have any KB categories.', 'echo-knowledge-base' ), $this->kb_config['kb_main_page_layout'] )
						: esc_html_e( 'You do not have any KB categories. What would you like to do?', 'echo-knowledge-base' ); ?></h2>  <?php

				// for users with at least Editor access - if WPML enabled, then show action buttons only for original KB Main Page
				if ( EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) && EPKB_PLL::is_original_language_page( $this->kb_config ) ) {

					if ( $is_block_main_page && $is_editor_on ) {   ?>
						<div class='eckb-kb-no-content-body'>
							<span><?php printf( esc_html__( 'To fix this issue, go to the page frontend %s', 'echo-knowledge-base' ),
							'<a href="' . EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config ). '" target="_blank">' . esc_html__( 'here', 'echo-knowledge-base' ) ); ?></a></span>
						</div><?php
					} else {			?>
						<div class="eckb-kb-no-content-body">
							<p><a id="eckb-kb-create-demo-data" class="eckb-kb-no-content-btn" href="#" data-id="<?php echo esc_attr( $this->kb_id ); ?>"><?php esc_html_e( 'Generate Demo Categories and Articles', 'echo-knowledge-base' ); ?></a></p>
							<p><a class="eckb-kb-no-content-btn" href="<?php echo esc_url( $manage_articles_url ); ?>" target="_blank"><?php esc_html_e( 'Create Categories', 'echo-knowledge-base' ); ?></a></p>
							<p><a class="eckb-kb-no-content-btn" href="<?php echo esc_url( $import_url ); ?>" target="_blank"><?php esc_html_e( 'Import Articles and Categories', 'echo-knowledge-base' ); ?></a></p>
						</div><?php
					}

					EPKB_HTML_Forms::dialog_confirm_action( array(
						'id'                => 'epkb-created-kb-content',
						'title'             => esc_html__( 'Notice', 'echo-knowledge-base' ),
						'body'              => esc_html__( 'Demo categories and articles have been created. The page will reload.', 'echo-knowledge-base' ),
						'accept_label'      => esc_html__( 'Ok', 'echo-knowledge-base' ),
						'accept_type'       => 'primary',
						'show_cancel_btn'   => 'no',
						'show_close_btn'    => 'no',
					) );

				}   ?>

				<div class="eckb-kb-no-content-footer">
					<p><?php echo $is_block_main_page && $is_editor_on ? '' : esc_html__( 'Ensure all articles are assigned to categories.', 'echo-knowledge-base' ); ?></p>
					<p>
						<span><?php esc_html_e( 'If you need help, please contact us', 'echo-knowledge-base' ); ?></span>
						<a href="https://www.echoknowledgebase.com/technical-support/" target="_blank"> <?php esc_html_e( 'here', 'echo-knowledge-base' ); ?></a>
					</p>
				</div>  <?php

			// for other users
			} else {    ?>
				<h2 class="eckb-kb-no-content-title"><?php echo esc_html( $this->kb_config['category_empty_msg'] ); ?></h2>     <?php
			}   ?>

		</section>      <?php
	}

	protected function get_nof_columns_int() {
		switch ( $this->kb_config['nof_columns'] ) {
			case 'one-col':
				$nof_columns = 1;
				break;
			case 'two-col':
				$nof_columns = 2;
				break;
			case 'three-col':
			default:
				$nof_columns = 3;
				break;
			case 'four-col':
				$nof_columns = 4;
				break;
		}

		return $nof_columns;
	}

	/**
	 * Arrange articles in columns based on the number of columns defined in the KB configuration.
	 * @param $articles_list
	 * @param $max_columns
	 * @return array
	 */
	protected function get_articles_listed_in_columns( $articles_list, $max_columns=3 ) {

		// exclude private and inaccessible articles
		$nof_articles = 0;
		foreach ( $articles_list as $article_id => $article_title ) {
			if ( ! EPKB_Utilities::is_article_allowed_for_current_user( $article_id ) ) {
				continue;
			}
			$nof_articles++;
		}

		$nof_columns_int = $this->get_nof_columns_int();
		$nof_columns_int = $nof_columns_int > $max_columns ? $max_columns : $nof_columns_int;
		$articles_per_column = (int) ceil( $nof_articles / $nof_columns_int );

		// create a nested array of articles for each column
		$column_count = 0;
		$column_articles_count = 0;
		$columns = array_fill( 0, $nof_columns_int, [] );
		foreach ( $articles_list as $article_id => $article_title ) {
			if ( ! EPKB_Utilities::is_article_allowed_for_current_user( $article_id ) ) {
				continue;
			}

			$columns[ $column_count ][] = [
				'title'  => $article_title,
				'id'     => $article_id,
			];
			$column_articles_count ++;

			if ( $column_articles_count >= $articles_per_column ) {
				$column_count ++;
				$column_articles_count = 0;
			}
		}

		return $columns;
	}
}