<?php

/**
 * Main Page Visual Helper - add a Visual Helper to the KB Main Page
 *
 */
class EPKB_Main_Page_Visual_Helper extends EPKB_Visual_Helper {

	/**
	 * Constructor - add actions for Visual Helper functionality
	 */
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'epkb_main_page_generate_page_content' ) );
	}

	/**
	 * Display Visual Helper on KB Main Page
	 */
	public function epkb_main_page_generate_page_content() {

		if ( ! EPKB_Utilities::is_kb_main_page() || EPKB_Utilities::get( 'kbsearch' ) ) {
			return;
		}

		if ( ! is_user_logged_in() || ! EPKB_Admin_UI_Access::is_user_admin_editor() ) {
			return;
		}

		$kb_id = EPKB_Utilities::get_eckb_kb_id();

		// TODO for now Visual Helper is disabled for blocks
		if ( EPKB_Block_Utilities::current_post_has_kb_layout_blocks() ) {
			return;
		}

		$visual_helper_state = epkb_get_instance()->kb_config_obj->get_value( $kb_id, 'visual_helper_switch_visibility_toggle' );
		if ( $visual_helper_state === 'off' ) {
			return;
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		$settings_side_menu = array(
			array(
				'box_title'         => esc_html__( 'Issues with the page layout, header, or menu?', 'echo-knowledge-base' ),
				'box_content'       => $this->epkb_main_page_side_menu_issues_box( $kb_config ),
				'details_button_id' => 'epkb-vshelp-switch-template'
			),
			array(
				'box_title'         => esc_html__( 'Is this page or search box too narrow?', 'echo-knowledge-base' ),
				'box_content'       => $this->epkb_main_page_side_menu_narrow_box( $kb_config ),
			)
		);

		$settings_info_icons = array(
			// search
			'kb-main-page-search' => array(
				'connected_selectors' => '#epkb-ml-search-form',
				'modalTitle' => esc_html__( 'Search Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => 'Search Box Width',
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Search Box', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Adjust the whole search box width or width of the input box.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/main-page-width/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--search__module-settings' ) )
					),
					array(
						'title' => esc_html__( 'Labels for Search Title, Search Button Text, and Other', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Labels', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Customize text for the search box title, search button, and other elements.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__labels____search-labels-mp' ) )
					),
					array(
						'title' => esc_html__( 'Colors, Padding, Title, and More', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Search Box', 'echo-knowledge-base' ),
						'content' => esc_html__( 'The search box also has settings for colors, padding, title, and more.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--search__search-options-mp' ) )
					),
				)
			),
			// advanced search
			'kb-main-page-advanced-search' => array(
				'connected_selectors' => '#asea-doc-search-box-container',
				'modalTitle' => esc_html__( 'Search Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => 'Search Box Width',
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Search Box', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Adjust the whole search box width or width of the input box.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/main-page-width/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--search__module-settings' ) )
					),
					array(
						'title' => esc_html__( 'Labels for Search Title, Search Button Text, and Other', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Labels', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Customize text for the search box title, search button, and other elements.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__labels____search-labels-mp' ) )
					),
					array(
						'title' => esc_html__( 'Colors, Padding, Title, and More', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Search Box', 'echo-knowledge-base' ),
						'content' => esc_html__( 'The search box also has settings for colors, padding, title, and more.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--search__search-options-mp--search-style-mp' ) )
					),
				)
			),
			'kb-main-page-category-box' => array(
				'connected_selectors' => '
				.epkb-top-category-box, 
				.epkb-category-section, 
				.epkb-ml-top-categories-button-container,
				#epkb-ml-grid-layout .eckb-categories-list a
				',
				'modalTitle' => esc_html__( 'Category Box Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => 'Category and Articles Width',
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Search Box', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Adjust the width of the categories and articles section on the webpage.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/main-page-width/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--categories_articles__module-settings' ) )
					),
					array(
						'title' => esc_html__( 'Category Icons', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'KB Main Page', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Categories & Articles', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Choose category icons from our font icon library or upload your own.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit-tags.php?taxonomy=epkb_post_type_' . $kb_id  . '_category&post_type=epkb_post_type_' . $kb_id ) )
					),
					array(
						'title' => esc_html__( 'Colors, Box Height, Alignment, and More', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'KB Main Page', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Categories & Articles', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Customize the appearance and behavior of category boxes with additional settings for colors, box height, alignment, and more.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--categories_articles' ) )
					),
					array(
						'title' => esc_html__( 'Labels for Category', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Customize text for the Empty Category Notice.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__labels____labels-category-body' ) )
					),
				)
			),
			'kb-main-page-featured-articles' => array(
				'connected_selectors' => '#epkb-ml-popular-articles',
				'modalTitle' => esc_html__( 'Featured Articles', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Configuration', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'KB Main Page', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Categories & Articles', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Customize the number of articles listed and which articles to list.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--articles_list__module-settings' ) )
					),
					array(
						'title' => esc_html__( 'Labels for Featured Articles Titles', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Customize text for featured articles section and box titles.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__labels____labels_articles_list_feature' ) )
					),
				)
			),
			'kb-main-page-faq' => array(
				'connected_selectors' => '#epkb-ml__module-faqs',
				'modalTitle' => esc_html__( 'FAQs', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'FAQs Management', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Add and update questions and answers for your FAQs.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-faqs/' ) )
					),
					array(
						'title' => esc_html__( 'Choose style, colors, format, and More', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'KB Main Page', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Categories & Articles', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Personalize the look and feel of your FAQs with additional settings for colors, predefined styles, animations, and more.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--faqs__module-settings' ) )
					),
					array(
						'title' => esc_html__( 'Labels for FAQs', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Customize text for the FAQs title, and Empty FAQs Notice.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__labels____labels_faqs_feature' ) )
					),
				)
			),
			'kb-main-page-resource-links' => array(
				'connected_selectors' => '.elay-ml__module-resource-links__title span',
				'modalTitle' => esc_html__( 'Resource Links', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Definition of Links and Buttons For Site Resources', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'KB Main Page', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Categories & Articles', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Define the destination and behavior of each link and button. Also, specify their colors and styles.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--resource_links__resource-link-individual-settings' ) )
					),
				)
			)
		);

		$this->epkb_generate_page_content( $settings_info_icons , $kb_config, $settings_side_menu );
	}

	/**
	 * Returns side menu issues box content
	 * @param $kb_config
	 * @return false|string
	 */
	public function epkb_main_page_side_menu_issues_box( $kb_config ) {

		$current_page_template  = $kb_config[ 'templates_for_kb' ];
		$kb_id                  = $kb_config['id'];

		ob_start(); ?>
			<p> <?php
				echo sprintf( esc_html__( 'The Knowledge Base offers two template options for both Main and Article Pages: %sKB Template%s and %sCurrent Theme Template%s.', 'echo-knowledge-base' ), '<strong>', '</strong>', '<strong>', '</strong>' ) . ' ' .
					'<a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank" rel="nofollow">' . esc_html__(  'Learn More', 'echo-knowledge-base' ) . '</a> <span class="epkbfa epkbfa-external-link"></span>';  ?>
			</p>
			<div class="epkb-vshelp-accordion-body__divider"></div>
			<p><?php echo esc_html__( 'If you\'re experiencing layout issues or want to see a different look, try switching the template', 'echo-knowledge-base' ) . ':'; ?></p>
			<div class="epkb-vshelp-accordion-body__template-toggle epkb-settings-control">
				<label class="epkb-settings-control-circle-radio">
					<input type="radio" name="templates_for_kb" value="kb_templates" data-kbid="<?php echo esc_attr( $kb_id ); ?>" class="epkb-settings-control-circle-radio__radio" <?php echo esc_attr( $current_page_template === 'kb_templates' ? 'checked="checked"' : '' ); ?>>
					<span><?php esc_html_e( 'KB Template', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-settings-control-circle-radio__checkmark"></span>
				</label>
				<label class="epkb-settings-control-circle-radio">
					<input type="radio" name="templates_for_kb" value="current_theme_templates" data-kbid="<?php echo esc_attr( $kb_id ); ?>" class="epkb-settings-control-circle-radio__radio" <?php echo esc_attr( $current_page_template === 'current_theme_templates' ? 'checked="checked"' : '' ); ?>>
					<span><?php esc_html_e( 'Current Theme Template', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-settings-control-circle-radio__checkmark"></span>
				</label>
			</div>  <?php

		return ob_get_clean();
	}

	/**
	 * Returns side menu narrow box content
	 * @param $kb_config
	 * @return false|string
	 */
	public function epkb_main_page_side_menu_narrow_box( $kb_config ) {

		$search_row_width_key = '';
		$category_row_width_key = '';
        $kb_id = $kb_config['id'];

		for ( $i = 1; $i <= 5; $i++ ) {
			if ( $kb_config['ml_row_' . $i . '_module'] === 'categories_articles' ) {
				$category_row_width_key = 'ml_row_' . $i . '_desktop_width';
				continue;
			}
			if ( $kb_config['ml_row_' . $i . '_module'] === 'search' ) {
				$search_row_width_key = 'ml_row_' . $i . '_desktop_width';
			}
		}

		ob_start();
		if ( ! empty( $search_row_width_key ) ) { ?>

			<h5 class="epkb-vshelp-accordion-body-content__title"><?php echo esc_html__( 'Page width', 'echo-knowledge-base' ) . ': '; ?><span class='js-epkb-mp-width'>-</span></h5>

			<h5 class="epkb-vshelp-accordion-body-content__title"><strong><?php echo esc_html__( 'Search Box', 'echo-knowledge-base' ); ?></strong></h5>

			<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
				<?php echo esc_html__( 'Actual width', 'echo-knowledge-base' ) . ': '; ?><span class="js-epkb-mp-search-width">-</span>
			</div>

			<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
				<?php echo esc_html__( 'KB setting for Search Width', 'echo-knowledge-base' ) . ': ' . esc_attr( $kb_config[ $search_row_width_key ] . $kb_config[ $search_row_width_key . '_units' ] ) .
							( $kb_config[ $search_row_width_key . '_units' ] == '%' ? ' ' . esc_html__( 'of the page.', 'echo-knowledge-base' ) : '' ); ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--search__module-settings' ) ); ?>" target="_blank" class="epkb-vshelp-accordion-body-content__note"><span class="epkbfa epkbfa-external-link"></span></a>
			</div>	<?php
			/* if ( $search_row_width_units == '%' ) { ?>
				<div class="epkb-vshelp-accordion-body-content__note"><?php
					esc_html_e( 'Note: The px value for the width should be the configured percentage.', 'echo-knowledge-base' ); ?>
				</div>								<?php
			} */ ?>
			<div class="epkb-vshelp-accordion-body-content__spacer"></div>	<?php
		}
		if ( ! empty( $category_row_width_key ) ) {	?>
			<h5 class="epkb-vshelp-accordion-body-content__title"><strong><?php echo esc_html__( 'Categories and Articles Width', 'echo-knowledge-base' ); ?></strong></h5>


			<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
				<?php echo esc_html__( 'Actual width', 'echo-knowledge-base' ) . ': '; ?><span class="js-epkb-mp-width-container">-</span>
			</div>

			<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
				<?php echo esc_html__( 'KB setting for categories list width', 'echo-knowledge-base' ); echo ': ' . esc_attr( $kb_config[ $category_row_width_key ] . $kb_config[ $category_row_width_key . '_units' ] ) .
							( $kb_config[ $category_row_width_key . '_units' ] == '%' ? ' ' . esc_html__( 'of the total page width.', 'echo-knowledge-base' ) : '' ); ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--categories_articles__module-settings' ) ); ?>" target="_blank" class="epkb-vshelp-accordion-body-content__note"><span class="epkbfa epkbfa-external-link"></span></a>
					   <?php
			/* if ( $category_row_width_units == '%' ) { ?>
				<div class="epkb-vshelp-accordion-body-content__note"><?php
					esc_html_e( 'Note: The detected px value should be a percentage of your page width.', 'echo-knowledge-base' ); ?>
				</div>								<?php
			} */ ?>
			</div>
			<div class="epkb-vshelp-accordion-body-content__spacer"></div>  <?php
		}	?>

		<h5><strong><?php esc_html_e( 'Troubleshooting', 'echo-knowledge-base' ); ?></strong></h5>
		<p><?php echo esc_html__( 'If the value you set in the KB settings does not match the actual value, it may be because your theme or page
								builder is limiting the overall width. In such cases, the KB settings cannot exceed the maximum width allowed
								by your theme or page builder. Try the following', 'echo-knowledge-base' ) . ':'; ?></p>
		<ul>
			<li><?php echo sprintf( esc_html__( 'If the KB Shortcode is inserted inside your page builder then you will need to check the section width of that page builder. %s', 'echo-knowledge-base' ),
						'<a href="https://www.echoknowledgebase.com/documentation/main-page-width-and-page-builders/" target="_blank" rel="nofollow">' . esc_html__( 'Learn more', 'echo-knowledge-base' ) . '</a> <span class="epkbfa epkbfa-external-link"> </span>' ); ?></li><?php

			if ( $kb_config['templates_for_kb'] == 'current_theme_templates' ) { ?>
				<li><?php echo sprintf( esc_html__( 'You are currently using the Current theme template. Check your theme settings or switch to the KB template. %s', 'echo-knowledge-base' ),
						'<a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank" rel="nofollow">' . esc_html__( 'Learn more', 'echo-knowledge-base' ) . '</a> <span class="epkbfa epkbfa-external-link"></span>' ); ?></li>								<?php
			}								?>
		</ul>		<?php

		return ob_get_clean();
	}
}