<?php

/**
 * Category Page Visual Helper - add a Visual Helper to the KB Category Archive Page
 *
 */
class EPKB_Category_Page_Visual_Helper extends EPKB_Visual_Helper {

	/**
	 * Constructor - add actions for Visual Helper functionality
	 */
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'epkb_article_page_generate_page_content' ) );
	}

	/**
	 * Display Visual Helper on KB Category Archive Page
	 */
	public function epkb_article_page_generate_page_content() {
		global $post;

		if (  empty( $post ) || empty( $post->post_type ) || ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) || is_single() ) {
			return;
		}

		if ( ! is_user_logged_in() || ! EPKB_Admin_UI_Access::is_user_admin_editor() ) {
			return;
		}

		$kb_id = EPKB_Utilities::get_eckb_kb_id();

		$visual_helper_state = epkb_get_instance()->kb_config_obj->get_value( $kb_id, 'visual_helper_switch_visibility_toggle' );
		if ( $visual_helper_state === 'off' ) {
			return;
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		$settings_side_menu = array(
			array(
				'box_title'         => esc_html__( 'Issues with the page layout, header, or menu?', 'echo-knowledge-base' ),
				'box_content'       => $this->epkb_article_page_side_menu_issues_box( $kb_config ),
				'details_button_id' => 'epkb-vshelp-switch-template'
			),
			array(
				'box_title'         => esc_html__( 'Is this page or search box too narrow?', 'echo-knowledge-base' ),
				'box_content'       => $this->epkb_article_page_side_menu_narrow_box( $kb_config ),
			)
		);

		$settings_info_icons = array(
			// search
			'kb-category-page-search' => array(
				'connected_selectors' => '#epkb-ml-search-form',
				'modalTitle' => esc_html__( 'Search Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Search Box Width', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Adjust the whole search box width or width of the input box.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__archive-page__archive-page__search-options-archive' ) )
					),
					array(
						'title' => esc_html__( 'Labels for Search Title, Search Button Text, and Other', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Search labels are controlled by the KB Main Page settings.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__labels____search-labels-mp' ) )
					),
					array(
						'title' => esc_html__( 'Colors, Padding, Title, and More', 'echo-knowledge-base' ),
						'content' => esc_html__( 'These settings are controlled by the KB Main Page settings.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--search__search-options-mp' ) )
					),
				)
			),
			'kb-category-page-advanced-search' => array(
				'connected_selectors' => '#asea-doc-search-box-container',
				'modalTitle' => esc_html__( 'Search Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => 'Search Box Width',
						'content' => esc_html__( 'Adjust the whole search box width or width of the input box.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__archive-page__archive-page__search-options-archive' ) )
					),
					array(
						'title' => esc_html__( 'Labels for Search Title, Search Button Text, and Other', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Customize text for the search box title, search button, and other elements.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__labels____search-labels-mp' ) )
					),
					array(
						'title' => esc_html__( 'Colors, Padding, Title, and More', 'echo-knowledge-base' ),
						'content' => esc_html__( 'The search box also has settings for colors, padding, title, and more.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__main-page__module--search__search-options-mp--search-style-mp' ) )
					),
				)
			),
			'kb-category-left-sidebar' => array(
				'connected_selectors' => '#eckb-archive-body',
				'modalTitle' => esc_html__( 'Left Sidebar Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Left Sidebar', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Adjust the whole search box width or width of the input box.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__archive-page__archive-page__left-sidebar-archive' ) )
					)
				)
			),
			'kb-category-right-sidebar' => array(
				'connected_selectors' => '#eckb-archive-body',
				'modalTitle' => esc_html__( 'Right Sidebar Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Right Sidebar', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Adjust the whole search box width or width of the input box.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__archive-page__archive-page__right-sidebar-archive' ) )
					),
				)
			),
			'kb-category-page-article-list' => array(
				'connected_selectors' => '.eckb-article-list-container',
				'modalTitle' => esc_html__( 'Archive Article List Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Article List', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Choose a preset design, then customize the number of columns and other settings.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__archive-page__archive-page__list-of-articles-archive' ) )
					)
				)
			),
			'kb-category-page-subcategory-list' => array(
				'connected_selectors' => '.eckb-sub-category-list-container',
				'modalTitle' => esc_html__( 'Archive Subcategories List Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Sub-Categories List', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Choose a preset design, then customize the number of columns and other settings.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__archive-page__archive-page__list-of-sub-categories-archive' ) )
					)
				)
			),
		);

		$this->epkb_generate_page_content( $settings_info_icons , $kb_config, $settings_side_menu );
	}

	/**
	 * Returns side menu issues box content
	 * @param $kb_config
	 * @return false|string
	 */
	public function epkb_article_page_side_menu_issues_box( $kb_config ) {
		$current_page_template  = $kb_config[ 'template_for_archive_page' ];
		$kb_id                  = $kb_config['id'];

		ob_start(); ?>
		<p> <?php
			echo sprintf( esc_html__( 'The Knowledge Base offers two template options: %sKB Template%s and %sCurrent Theme Template%s.', 'echo-knowledge-base' ), '<strong>', '</strong>', '<strong>', '</strong>' ) . ' ' .
				'<a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank" rel="nofollow">' . esc_html__(  'Learn More', 'echo-knowledge-base' ) . '</a> <span class="epkbfa epkbfa-external-link"></span>';  ?>
		</p>
		<div class="epkb-vshelp-accordion-body__divider"></div>
		<p><?php echo esc_html__( 'If you\'re experiencing layout issues or want to see a different look, try switching the template', 'echo-knowledge-base' ) . ':'; ?></p>
			<div class="epkb-vshelp-accordion-body__template-toggle epkb-settings-control">
				<label class="epkb-settings-control-circle-radio">
					<input type="radio" name="template_for_archive_page" value="kb_templates" data-kbid="<?php echo esc_attr( $kb_id ); ?>" class="epkb-settings-control-circle-radio__radio" <?php echo esc_attr( $current_page_template === 'kb_templates' ? 'checked="checked"' : '' ); ?>>
					<span><?php esc_html_e( 'KB Template', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-settings-control-circle-radio__checkmark"></span>
				</label>
				<label class="epkb-settings-control-circle-radio">
					<input type="radio" name="template_for_archive_page" value="current_theme_templates" data-kbid="<?php echo esc_attr( $kb_id ); ?>" class="epkb-settings-control-circle-radio__radio" <?php echo esc_attr( $current_page_template === 'current_theme_templates' ? 'checked="checked"' : '' ); ?>>
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
	public function epkb_article_page_side_menu_narrow_box( $kb_config ) {

		$category_search_toggle = $kb_config['archive_search_toggle'];
		$category_search_width  = $kb_config['archive_header_desktop_width'];
		$category_search_width_units  = $kb_config['archive_header_desktop_width_units'];
		$category_content_width = $kb_config['archive_content_desktop_width'];
		$category_content_width_units = $kb_config['archive_content_desktop_width_units'];

		$current_page_template  = $kb_config[ 'template_for_archive_page' ];
        $kb_id                  = $kb_config['id'];


		ob_start();
		if ( $current_page_template === 'current_theme_templates' ) { ?>
			<h5 class="epkb-vshelp-accordion-body-content__title">
				<strong><?php echo esc_html__( 'Current Theme Settings', 'echo-knowledge-base' ); ?></strong>
			</h5>
			<p><?php echo esc_html__( 'Since you currently have the "Current Theme" option selected, the HTML and CSS output is entirely controlled by your active WordPress theme.', 'echo-knowledge-base' ); ?></p>
			<p><?php echo esc_html__( 'The Knowledge Base (KB) no longer handles any visual display or styling functionality. To customize this archive page, you\'ll need to review and adjust your theme settings.', 'echo-knowledge-base' ); ?></p>
			<p><?php echo esc_html__( 'However, if your theme lacks features for customizing this type of page, we recommend using the KB template option, which allows you to make adjustments through the KB settings.', 'echo-knowledge-base' ); ?></p>
			<p>
				<?php echo esc_html__( 'For more details, please refer to this article on the differences between the KB template and the Current Theme template:', 'echo-knowledge-base' ); ?>
				<a href="<?php echo esc_url( 'https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/' ); ?>"  target="_blank" rel="nofollow"><?php echo esc_html__( 'Current Theme Template vs KB Template', 'echo-knowledge-base' ); ?></a>
			</p>		<?php 
		} else {
			if ( $category_search_toggle ) { ?>

				<h5 class="epkb-vshelp-accordion-body-content__title"><?php echo esc_html__( 'Page width', 'echo-knowledge-base' ) . ':'; ?> <span class='js-epkb-cp-width'>-</span></h5>

				<h5 class="epkb-vshelp-accordion-body-content__title"><strong><?php echo esc_html__( 'Search Box', 'echo-knowledge-base' ); ?></strong></h5>

				<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
					<?php echo esc_html__( 'Actual width', 'echo-knowledge-base' ) . ': '; ?><span class="js-epkb-cp-search-width">-</span>
				</div>

				<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
				<?php echo esc_html__( 'KB setting for Search Width', 'echo-knowledge-base' ) . ': ' . esc_attr( $category_search_width . $category_search_width_units ) .
								( $category_search_width_units == '%' ? ' ' . esc_html__( 'of the page.', 'echo-knowledge-base' ) : '' ); ?>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__archive-page__archive-page__search-options-archive' ) ); ?>" target="_blank" class="epkb-vshelp-accordion-body-content__note"><span class="epkbfa epkbfa-external-link"></span></a>
				</div>

				<div class="epkb-vshelp-accordion-body-content__spacer"></div><?php
			} ?>
			<h5 class="epkb-vshelp-accordion-body-content__title"><strong><?php echo esc_html__( 'Width of left sidebar + content + right sidebar', 'echo-knowledge-base' ); ?></strong></h5>

			<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
				<?php echo esc_html__( 'Actual width', 'echo-knowledge-base' ) . ': '; ?><span class="js-epkb-cp-width-container">-</span>
			</div>

			<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
			<?php echo esc_html__( 'KB setting for Archive Width', 'echo-knowledge-base' ) . ': ' . esc_attr( $category_content_width . $category_content_width_units ) .
							( $category_content_width_units == '%' ? ' ' . esc_html__( 'of the total page width.', 'echo-knowledge-base' ) : '' ); ?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__archive-page__archive-page__content-archive' ) ); ?>" target="_blank" class="epkb-vshelp-accordion-body-content__note"><span class="epkbfa epkbfa-external-link"></span></a>
			</div>

			<div class="epkb-vshelp-accordion-body-content__spacer"></div>

			<h5><strong><?php esc_html_e( 'Troubleshooting', 'echo-knowledge-base' ); ?></strong></h5>
			<p><?php echo esc_html__( 'If the value you set in the KB settings does not match the actual value, it may be because your theme or page
									builder is limiting the overall width. In such cases, the KB settings cannot exceed the maximum width allowed
									by your theme or page builder. Try the following', 'echo-knowledge-base' ) . ':'; ?></p>
			<ul>
				<li><?php echo sprintf( esc_html__( 'Category Archive Page width is controlled by search box width, sidebar widths and other settings. %s', 'echo-knowledge-base' ),
						'<a href="https://www.echoknowledgebase.com/documentation/category-archive-page/" target="_blank" rel="nofollow">' . esc_html__( 'Learn more', 'echo-knowledge-base' ) . '</a> <span class="epkbfa epkbfa-external-link"> </span>' ); ?></li>  <?php

				if ( $current_page_template == 'current_theme_templates' ) { ?>
					<li><?php echo sprintf( esc_html__( 'You are currently using the Current theme template. Check your theme settings or switch to the KB template. %s', 'echo-knowledge-base' ),
							'<a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank" rel="nofollow">' . esc_html__( 'Learn more', 'echo-knowledge-base' ) . '</a> <span class="epkbfa epkbfa-external-link"></span>' ); ?></li>								<?php
				}								?>
			</ul>		<?php
		}

		return ob_get_clean();
	}
}