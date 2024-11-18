<?php

/**
 * Article Page Visual Helper - add a Visual Helper to the KB Article Page
 *
 */
class EPKB_Article_Page_Visual_Helper extends EPKB_Visual_Helper {

	/**
	 * Constructor - add actions for Visual Helper functionality
	 */
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'epkb_article_page_generate_page_content' ) );
	}

	/**
	 * Display Visual Helper on KB Article Page
	 */
	public function epkb_article_page_generate_page_content() {
		global $post;

		if ( empty( $post ) || empty( $post->post_type ) || ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) || ! is_single() ) {
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
			'kb-article-page-title' => array(
				'connected_selectors' => '.eckb-article-title',
				'modalTitle' => esc_html__( 'Article Title Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Article Title', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Change the article title.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/article-title/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( get_edit_post_link( $post->ID ) ),
					),
					array(
						'title' => esc_html__( 'Article Title Visibility', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Change the article title visibility.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-settings__article_features_top' ) ),
					)
				)
			),
			'kb-article-page-breadcrumb' => array(
				'connected_selectors' => '#eckb-article-content-breadcrumb-container',
				'modalTitle' => esc_html__( 'Article Breadcrumb Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Article Breadcrumb', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Change the article breadcrumb.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/article-breadcrumb/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-settings__breadcrumb' ) ),
					)
				)
			),
			'kb-article-page-metadata' => array(
				'connected_selectors' => '#eckb-article-content-header-row-3',
				'modalTitle' => esc_html__( 'Article Metadata Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Article Metadata', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Change the article metadata.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-settings__article_features_top' ) ),
					),
					array(
						'title' => esc_html__( 'Article View Counter', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Change the article counter.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/article-views-counter/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-settings__article_features_top' ) ),
					),
					array(
						'title' => esc_html__( 'Article Print Button', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Change the article counter.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/print-button/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-settings__article_features_top' ) ),
					)
				)
			),
			'kb-article-page-search' => array(
				'connected_selectors' => '#epkb-ml-search-form',
				'modalTitle' => esc_html__( 'Search Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Search Box Width', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Adjust the whole search box width or width of the input box.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/article-page-width/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-search-box__search-settings-ap' ) )
					),
					array(
						'title' => esc_html__( 'Labels for Search Title, Search Button Text, and Other', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Labels', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Customize text for the search box title, search button, and other elements.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__labels____search-labels-ap' ) )
					),
					array(
						'title' => esc_html__( 'Colors, Padding, Title, and More', 'echo-knowledge-base' ),
						//'location' => esc_html__( 'KB Config', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Settings', 'echo-knowledge-base' ) . ' ⮞ ' . esc_html__( 'Search Box', 'echo-knowledge-base' ),
						'content' => esc_html__( 'The search box also has settings for colors, padding, title, and more.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-search-box__search-options-ap' ) )
					),
				)
			),
			'kb-article-page-advanced-search' => array(
				'connected_selectors' => '#asea-doc-search-box-container',
				'modalTitle' => esc_html__( 'Search Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Search Box Width', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Adjust the whole search box width or width of the input box.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/article-page-width/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-search-box__search-settings-ap' ) )
					),
					array(
						'title' => esc_html__( 'Labels for Search Title, Search Button Text, and Other', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Customize text for the search box title, search button, and other elements.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__labels____search-labels-ap' ) )
					),
					array(
						'title' => esc_html__( 'Colors, Padding, Title, and More', 'echo-knowledge-base' ),
						'content' => esc_html__( 'The search box also has settings for colors, padding, title, and more.', 'echo-knowledge-base' ),
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-search-box__search-options-ap--search-style-ap' ) )
					),
				)
			),
            'kb-article-page-content' => array(
                'connected_selectors' => '#eckb-article-content-body',
                'modalTitle' => esc_html__( 'Article Content', 'echo-knowledge-base' ),
                'modalSections' => array(
                    array(
                        'title' => esc_html__( 'Adding PDFs to Knowledge Base', 'echo-knowledge-base' ),
                        'content' => esc_html__( 'PDF documents can be linked to or embedded in articles.', 'echo-knowledge-base' ) .
                            ' <a href="https://www.echoknowledgebase.com/documentation/pdf-with-knowledge-base/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
                    ),
                    array(
                        'title' => esc_html__( 'Embedding Google Documents', 'echo-knowledge-base' ),
                        'content' => esc_html__( 'Embedding Google Documents in Articles', 'echo-knowledge-base' ) .
                            ' <a href="https://www.echoknowledgebase.com/documentation/embeding-google-documents-in-articles/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
                    ),
                    array(
	                    'title' => esc_html__( 'Article Comments', 'echo-knowledge-base' ),
	                    'content' => esc_html__( 'Adjust the whole search box width or width of the input box.', 'echo-knowledge-base' ) .
		                    ' <a href="https://www.echoknowledgebase.com/documentation/wordpress-article-comments/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
	                    'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-settings__article_features_bottom' ) )
                    ),
	                array(
		                'title' => esc_html__( 'Article Page Customizations', 'echo-knowledge-base' ),
		                'content' => esc_html__( 'Adding custom section to articles using hooks and custom styling etc.', 'echo-knowledge-base' ) .
			                ' <a href="https://www.echoknowledgebase.com/documentation/category/knowledge-base-plugin-core/kb-article-pages/advanced-kb-article-pages//" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
	                ),
                )
            ),
			'kb-article-left-sidebar' => array(
				'connected_selectors' => '#eckb-article-body',
				'modalTitle' => esc_html__( 'Left Sidebar Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Left Sidebar', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Adjust the whole search box width or width of the input box.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/article-sidebars/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-sidebar__left_sidebar' ) )
					)
				)
			),
			'kb-article-right-sidebar' => array(
				'connected_selectors' => '#eckb-article-body',
				'modalTitle' => esc_html__( 'Right Sidebar Settings', 'echo-knowledge-base' ),
				'modalSections' => array(
					array(
						'title' => esc_html__( 'Right Sidebar', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Configure right sidebar content.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/article-sidebars/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-sidebar__right_sidebar' ) )
					),
					array(
						'title' => esc_html__( 'Table of Contents (TOC)', 'echo-knowledge-base' ),
						'content' => esc_html__( 'Adjust TOC colors and behavior.', 'echo-knowledge-base' ) .
							' <a href="https://www.echoknowledgebase.com/documentation/table-of-content/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>',
						'link' => esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-toc__toc-options' ) )
					)
				)
			)
		);

		$this->epkb_generate_page_content( $settings_info_icons, $kb_config, $settings_side_menu );
	}

	/**
	 * Returns side menu issues box content
	 * @param $kb_config
	 * @return false|string
	 */
	public function epkb_article_page_side_menu_issues_box( $kb_config ) {

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
	public function epkb_article_page_side_menu_narrow_box( $kb_config ) {

		$article_search_toggle = $kb_config['article_search_toggle'];
		$article_search_width  = $kb_config['article-container-desktop-width-v2'];
		$article_search_width_units  = $kb_config['article-container-desktop-width-units-v2'];
		$article_content_width = $kb_config['article-body-desktop-width-v2'];
		$article_content_width_units = $kb_config['article-body-desktop-width-units-v2'];

		$current_page_template  = $kb_config[ 'templates_for_kb' ];
        $kb_id                  = $kb_config['id'];


		ob_start();
			if ( $article_search_toggle ) { ?>

				<h5 class="epkb-vshelp-accordion-body-content__title"><?php echo esc_html__( 'Page width', 'echo-knowledge-base' ) . ':'; ?> <span class='js-epkb-ap-width'>-</span></h5>

				<h5 class="epkb-vshelp-accordion-body-content__title"><strong><?php echo esc_html__( 'Search Box', 'echo-knowledge-base' ); ?></strong></h5>

				<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
					<?php echo esc_html__( 'Actual width', 'echo-knowledge-base' ) . ': '; ?><span class="js-epkb-ap-search-width">-</span>
				</div>
				<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
				<?php echo esc_html__( 'KB setting for Search Width', 'echo-knowledge-base' ) . ': ' . esc_attr( $article_search_width . $article_search_width_units ) .
								( $article_search_width_units == '%' ? ' ' . esc_html__( 'of the page.', 'echo-knowledge-base' ) : '' ); ?>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-search-box__search-settings-ap' ) ); ?>" target="_blank" class="epkb-vshelp-accordion-body-content__note"><span class="epkbfa epkbfa-external-link"></span></a>
				</div>

				<div class="epkb-vshelp-accordion-body-content__spacer"></div><?php
			} ?>
				<h5 class="epkb-vshelp-accordion-body-content__title"><strong><?php echo esc_html__( 'Width of Left Sidebar + Content + Right Sidebar', 'echo-knowledge-base' ); ?></strong></h5>

				<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
					<?php echo esc_html__( 'Actual width', 'echo-knowledge-base' ) . ': '; ?><span class="js-epkb-ap-width-container">-</span>
				</div>

				<div class="epkb-vshelp-accordion-body-content__item"><span class="epkb-vshelp-accordion-body-content__item_icon">⮞</span>
					<?php echo esc_html__( 'KB setting for Article Width', 'echo-knowledge-base' ) . ': ' . esc_attr( $article_content_width . $article_content_width_units ) .
								( $article_content_width_units == '%' ? ' ' . esc_html__( 'of the total page width.', 'echo-knowledge-base' ) : '' ); ?>
                    <a href="<?php echo esc_url(admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_id . '&page=epkb-kb-configuration#settings__article-page__article-page-settings__article_content' ) ); ?>" target="_blank" class="epkb-vshelp-accordion-body-content__note"><span class="epkbfa epkbfa-external-link"></span></a>
				</div>

				<div class="epkb-vshelp-accordion-body-content__spacer"></div>

			<h5><strong><?php esc_html_e( 'Troubleshooting', 'echo-knowledge-base' ); ?></strong></h5>
			<p><?php echo esc_html__( 'If the value you set in the KB settings does not match the actual value, it may be because your theme or page
									builder is limiting the overall width. In such cases, the KB settings cannot exceed the maximum width allowed
									by your theme or page builder. Try the following', 'echo-knowledge-base' ) . ':'; ?></p>
			<ul>
				<li><?php echo sprintf( esc_html__( 'Article Page width is controlled by search box width, sidebar widths and other settings. %s', 'echo-knowledge-base' ),
						'<a href="https://www.echoknowledgebase.com/documentation/article-page-width/" target="_blank" rel="nofollow">' . esc_html__( 'Learn more', 'echo-knowledge-base' ) . '</a> <span class="epkbfa epkbfa-external-link"> </span>' ); ?></li>  <?php

				if ( $current_page_template == 'current_theme_templates' ) { ?>
					<li><?php echo sprintf( esc_html__( 'You are currently using the Current theme template. Check your theme settings or switch to the KB template. %s', 'echo-knowledge-base' ),
							'<a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank" rel="nofollow">' . esc_html__( 'Learn more', 'echo-knowledge-base' ) . '</a> <span class="epkbfa epkbfa-external-link"></span>' ); ?></li>								<?php
				}								?>
			</ul>		<?php

		return ob_get_clean();
	}
}