<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Glossary admin page
 */
class EPKB_Glossary_Page {

	/**
	 * Register glossary submenu via eckb_add_kb_submenu hook
	 * @param string $parent_slug
	 */
	public static function add_menu_item( $parent_slug ) {

		// Keep the menu hidden when disabled, but still register the page so direct links can open it.
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$current_page = EPKB_Utilities::get( 'page', '' );
		if ( $kb_config['glossary_enable'] !== 'on' && $current_page !== 'epkb-glossary' ) {
			return;
		}

		add_submenu_page( $parent_slug, esc_html__( 'Glossary - Echo Knowledge Base', 'echo-knowledge-base' ), esc_html__( 'Glossary', 'echo-knowledge-base' ),
			EPKB_Admin_UI_Access::get_context_required_capability( array( 'admin_eckb_access_glossary_write' ) ), 'epkb-glossary', array( new self(), 'display_glossary_page' ) );
	}

	/**
	 * Display Glossary page
	 */
	public function display_glossary_page() {

		$admin_page_views = self::get_views_config();

		EPKB_HTML_Admin::admin_page_header(); ?>

		<!-- Admin Page Wrap -->
		<div id="ekb-admin-page-wrap">

			<div id="epkb-kb-glossary-page-container">   <?php

				/**
				 * ADMIN HEADER
				 */
				EPKB_HTML_Admin::admin_header( [], [], 'logo' );

				/**
				 * ADMIN TOOLBAR
				 */
				EPKB_HTML_Admin::admin_primary_tabs( $admin_page_views );

				/**
				 * LIST OF SETTINGS IN TABS
				 */
				EPKB_HTML_Admin::admin_primary_tabs_content( $admin_page_views ); ?>

			</div>

		</div> <?php
	}

	/**
	 * Get configuration array for views
	 * @return array
	 */
	private static function get_views_config() {

		$views_config = [];

		// Introduction tab
		$views_config[] = array(
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( array( 'admin_eckb_access_glossary_write' ) ),
			'list_key'   => 'glossary-introduction',
			'label_text' => esc_html__( 'Introduction', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-home',
			'boxes_list' => array(
				array(
					'html' => self::introduction_tab(),
				)
			),
		);

		// Glossary Terms tab
		$views_config[] = array(
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( array( 'admin_eckb_access_glossary_write' ) ),
			'list_key'   => 'glossary-terms',
			'label_text' => esc_html__( 'Glossary Terms', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-book',
			'boxes_list' => array(
				array(
					'html' => self::glossary_terms_tab(),
				)
			),
		);

		// Settings tab
		$views_config[] = array(
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_admin_capability(),
			'list_key'   => 'glossary-settings',
			'label_text' => esc_html__( 'Settings', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-cog',
			'boxes_list' => array(
				array(
					'html' => self::settings_tab(),
				)
			),
		);

		$views_config = apply_filters( 'epkb_glossary_page_views', $views_config );

		// Show AI Generate tab with ad if AI Features PRO is not active, or with configure message if AI is not configured
		if ( ! EPKB_Utilities::is_ai_features_pro_enabled() ) {
			$ai_generate_html = self::ai_generate_ad_tab();
		} else if ( ! EPKB_AI_Utilities::is_ai_configured() ) {
			$ai_generate_html = self::ai_generate_configure_tab();
		}

		if ( ! empty( $ai_generate_html ) ) {
			$views_config[] = array(
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( array( 'admin_eckb_access_glossary_write' ) ),
				'list_key'   => 'glossary-ai-generate',
				'label_text' => esc_html__( 'AI Generate Terms', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-magic',
				'boxes_list' => array(
					array(
						'html' => $ai_generate_html,
					)
				),
			);
		}

		return $views_config;
	}

	/**
	 * Show HTML content for Introduction tab
	 * @return false|string
	 */
	private static function introduction_tab() {

		ob_start(); ?>

		<!-- How It Works -->
		<div class="epkb-admin-info-box">
			<div class="epkb-admin-info-box__header">
				<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-info-circle"></div>
				<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'How It Works', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-admin-info-box__body">

				<!-- Live Example -->
				<div class="epkb-glossary-intro-example">
					<div class="epkb-glossary-intro-example__label"><?php esc_html_e( 'Example', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-glossary-intro-example__text">
						<?php echo wp_kses( sprintf(
							/* translators: %1$s: opening glossary term tag, %2$s: closing glossary term tag */
							__( 'A %1$sKnowledge Base%2$s helps your team organize documentation and share information with customers.', 'echo-knowledge-base' ),
							'<span class="epkb-glossary-intro-term">',
							'<span class="epkb-glossary-intro-tooltip"><span class="epkb-glossary-intro-tooltip__term">' . esc_html__( 'Knowledge Base', 'echo-knowledge-base' ) .
								'</span><span class="epkb-glossary-intro-tooltip__definition">' . esc_html__( 'A self-serve library of information about a product, service, or topic.', 'echo-knowledge-base' ) .
								'</span></span></span>'
						), array( 'span' => array( 'class' => array() ) ) ); ?>
					</div>
				</div>

				<ul>
					<li><?php esc_html_e( 'The Glossary is shared across all your Knowledge Bases.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Create terms with definitions in the Glossary Terms tab.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Published glossary terms are automatically highlighted in your KB articles.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'When readers hover over a highlighted term, a tooltip displays its definition.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Only the first occurrence of each term in an article is highlighted.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Terms inside headings, links, and code blocks are not highlighted.', 'echo-knowledge-base' ); ?></li>
				</ul>
			</div>
		</div>

		<!-- How to Set It Up -->
		<div class="epkb-admin-info-box">
			<div class="epkb-admin-info-box__header">
				<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-cog"></div>
				<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'How to Set It Up', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-admin-info-box__body">
				<ul>
					<li><?php esc_html_e( 'Enable the Glossary feature in each KB where you want it in the Settings tab.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Add glossary terms and definitions in the Glossary Terms tab.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Set each term status to Published to make it appear in articles.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Customize highlight and tooltip colors in the Settings tab.', 'echo-knowledge-base' ); ?></li>
				</ul>
			</div>
		</div>

		<!-- Best Practices -->
		<div class="epkb-admin-info-box">
			<div class="epkb-admin-info-box__header">
				<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-lightbulb-o"></div>
				<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'Best Practices', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-admin-info-box__body">
				<ul>
					<li><?php esc_html_e( 'Keep definitions concise and clear - they appear in small tooltips.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Focus on technical terms, abbreviations, and jargon that readers may not know.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Use Draft status to prepare terms before making them visible.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Avoid creating glossary terms for common words to prevent over-highlighting.', 'echo-knowledge-base' ); ?></li>
				</ul>
			</div>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Show HTML content for Glossary Terms tab
	 * @return false|string
	 */
	private static function glossary_terms_tab() {

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		if ( $kb_config['glossary_enable'] !== 'on' ) {
			ob_start(); ?>
			<div class="epkb-admin-info-box">
				<div class="epkb-admin-info-box__header">
					<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-info-circle"></div>
					<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'Glossary is Disabled', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-admin-info-box__body">
					<p><?php esc_html_e( 'Enable the Glossary feature to manage terms. Go to the Settings tab to enable it.', 'echo-knowledge-base' ); ?></p>
				</div>
			</div> <?php
			return ob_get_clean();
		}

		$terms = get_terms( array(
			'taxonomy'   => EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		) );

		if ( is_wp_error( $terms ) ) {
			$terms = [];
		}

		$lang = EPKB_Language_Utilities::detect_current_language();
		$is_cjk = in_array( $lang['code'], array( 'ja', 'zh', 'ko' ), true );

		ob_start(); ?>

		<!-- Add/Edit Form -->
		<div id="epkb-glossary-form" style="display:none;">
			<div class="epkb-glossary-form-head">
				<div class="epkb-glossary-form-head__title"><?php esc_html_e( 'Term', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-glossary-form-body">
				<input type="hidden" id="epkb-glossary-term-id" value="0">
				<div class="epkb-glossary-form-field">
					<label for="epkb-glossary-term-name"><?php esc_html_e( 'Term Name', 'echo-knowledge-base' ); ?></label>
					<input type="text" id="epkb-glossary-term-name" maxlength="100" placeholder="<?php esc_attr_e( 'Enter term name...', 'echo-knowledge-base' ); ?>">
					<div class="epkb-characters_left"><span class="epkb-characters_left-counter">100</span>/100</div>
				</div>	<?php
				if ( $is_cjk ) { ?>
				<div class="epkb-glossary-form-field">
					<label for="epkb-glossary-term-sort-key"><?php esc_html_e( 'Sort Key (Reading)', 'echo-knowledge-base' ); ?></label>
					<input type="text" id="epkb-glossary-term-sort-key" maxlength="100" placeholder="<?php esc_attr_e( 'Enter reading (e.g. furigana, pinyin)...', 'echo-knowledge-base' ); ?>">
					<div class="epkb-characters_left"><span class="epkb-characters_left-counter">100</span>/100</div>
				</div>	<?php
				} ?>
				<div class="epkb-glossary-form-field">
					<label for="epkb-glossary-term-definition"><?php esc_html_e( 'Definition', 'echo-knowledge-base' ); ?></label>
					<textarea id="epkb-glossary-term-definition" maxlength="500" rows="4" placeholder="<?php esc_attr_e( 'Enter definition...', 'echo-knowledge-base' ); ?>"></textarea>
					<div class="epkb-characters_left"><span class="epkb-characters_left-counter">500</span>/500</div>
				</div>
				<div class="epkb-glossary-form-field">
					<label><?php esc_html_e( 'Status', 'echo-knowledge-base' ); ?></label>
					<div class="epkb-glossary-status-toggle" id="epkb-glossary-term-status" data-value="publish">
						<button type="button" class="epkb-glossary-status-toggle__btn epkb-glossary-status-toggle__btn--publish epkb-glossary-status-toggle__btn--active" data-status="publish"><?php esc_html_e( 'Published', 'echo-knowledge-base' ); ?></button>
						<button type="button" class="epkb-glossary-status-toggle__btn epkb-glossary-status-toggle__btn--draft" data-status="draft"><?php esc_html_e( 'Draft', 'echo-knowledge-base' ); ?></button>
					</div>
				</div>
			</div>
		</div>

		<!-- Buttons -->
		<div id="epkb-glossary-top-buttons-container">
			<button type="button" id="epkb-glossary-create-term" class="epkb-btn epkb-success-btn">
				<span class="epkb-btn-icon epkbfa epkbfa-plus-circle"></span>
				<span class="epkb-btn-text"><?php esc_html_e( 'Add Term', 'echo-knowledge-base' ); ?></span>
			</button>
			<button type="button" id="epkb-glossary-bulk-delete" class="epkb-btn epkb-error-btn" style="display:none;">
				<span class="epkb-btn-icon epkbfa epkbfa-trash"></span>
				<span class="epkb-btn-text"><?php esc_html_e( 'Delete Terms', 'echo-knowledge-base' ); ?></span>
			</button>
			<div class="epkb-glossary-form-buttons" style="display:none;">
				<button class="epkb-glossary-form__save epkb-success-btn"><?php esc_html_e( 'Save', 'echo-knowledge-base' ); ?></button>
				<button class="epkb-glossary-form__cancel epkb-primary-btn"><?php esc_html_e( 'Cancel', 'echo-knowledge-base' ); ?></button>
			</div>
		</div>

		<!-- Search -->
		<div id="epkb-glossary-search-container">
			<input type="text" id="epkb-glossary-search-input" placeholder="<?php esc_attr_e( 'Search terms...', 'echo-knowledge-base' ); ?>">
			<span class="epkbfa epkbfa-search"></span>
		</div>

		<!-- Toolbar: Filter Buttons + Bulk Actions -->
		<div id="epkb-glossary-toolbar">
			<div class="epkb-glossary-filter-buttons">
				<button type="button" class="epkb-glossary-filter-btn epkb-glossary-filter-btn--active" data-filter="all"><?php esc_html_e( 'All', 'echo-knowledge-base' ); ?></button>
				<button type="button" class="epkb-glossary-filter-btn" data-filter="publish"><?php esc_html_e( 'Published', 'echo-knowledge-base' ); ?></button>
				<button type="button" class="epkb-glossary-filter-btn" data-filter="draft"><?php esc_html_e( 'Draft', 'echo-knowledge-base' ); ?></button>
			</div>
			<div class="epkb-glossary-bulk-actions" style="display:none;">
				<button type="button" id="epkb-glossary-bulk-publish" class="epkb-success-btn"><?php esc_html_e( 'Publish Selected', 'echo-knowledge-base' ); ?></button>
			</div>
		</div>

		<!-- Terms List -->
		<div id="epkb-glossary-terms-list" data-is-cjk="<?php echo esc_attr( $is_cjk ? '1' : '0' ); ?>">
			<table class="epkb-glossary-terms-table">
				<thead>
					<tr>
						<th class="epkb-glossary-col-checkbox"><input type="checkbox" id="epkb-glossary-select-all"></th>
						<th><?php esc_html_e( 'Term', 'echo-knowledge-base' ); ?></th>
						<th><?php esc_html_e( 'Definition', 'echo-knowledge-base' ); ?></th>
						<th><?php esc_html_e( 'Status', 'echo-knowledge-base' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'echo-knowledge-base' ); ?></th>
					</tr>
				</thead>
				<tbody>  <?php
					if ( empty( $terms ) ) { ?>
						<tr class="epkb-glossary-empty-row">
							<td colspan="5"><?php esc_html_e( 'No glossary terms found. Click "Add Term" to create one.', 'echo-knowledge-base' ); ?></td>
						</tr> <?php
					}

					foreach ( $terms as $term ) {
						$status = get_term_meta( $term->term_id, 'epkb_glossary_status', true );
						if ( empty( $status ) ) {
							$status = 'publish';
						}
						$sort_key = get_term_meta( $term->term_id, 'epkb_glossary_sort_key', true );
						self::display_term_row( $term->term_id, $term->name, $term->description, $status, false, $sort_key );
					} ?>
				</tbody>
			</table>
		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Show HTML content for Settings tab
	 * @return false|string
	 */
	private static function settings_tab() {

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );

		ob_start(); ?>

		<input id="epkb-list-of-kbs" type="hidden" value="<?php echo esc_attr( EPKB_KB_Config_DB::DEFAULT_KB_ID ); ?>">

		<div class="epkb-admin__form">
			<div class="epkb-admin__form__save_button">
				<button class="epkb-success-btn epkb-admin__kb__form-save__button"><?php esc_html_e( 'Save Settings', 'echo-knowledge-base' ); ?></button>
			</div>
			<div class="epkb-admin__form__body">  <?php

				EPKB_HTML_Elements::checkbox_toggle( [
					'id'                => 'glossary_enable',
					'name'              => 'glossary_enable',
					'text'              => esc_html__( 'Glossary', 'echo-knowledge-base' ),
					'checked'           => $kb_config['glossary_enable'] === 'on',
					'input_group_class' => 'eckb-conditional-setting-input ',
				] );

				EPKB_HTML_Elements::custom_dropdown( [
					'name'              => 'glossary_highlight_style',
					'label'             => esc_html__( 'Highlight Style', 'echo-knowledge-base' ),
					'value'             => isset( $kb_config['glossary_highlight_style'] ) ? $kb_config['glossary_highlight_style'] : 'style_1',
					'options'           => array(
						'style_1' => esc_html__( 'Style 1', 'echo-knowledge-base' ),
						'style_2' => esc_html__( 'Style 2', 'echo-knowledge-base' ),
						'style_3' => esc_html__( 'Style 3', 'echo-knowledge-base' ),
					),
					'input_group_class' => 'eckb-condition-depend__glossary_enable ',
					'group_data'        => [ 'dependency-ids' => 'glossary_enable', 'enable-on-values' => 'on' ],
				] );

				EPKB_HTML_Elements::color( [
					'name'              => 'glossary_highlight_color',
					'label'             => esc_html__( 'Glossary Highlight', 'echo-knowledge-base' ),
					'value'             => $kb_config['glossary_highlight_color'],
					'input_group_class' => 'eckb-condition-depend__glossary_enable ',
					'group_data'        => [ 'dependency-ids' => 'glossary_enable', 'enable-on-values' => 'on' ],
				] );

				EPKB_HTML_Elements::color( [
					'name'              => 'glossary_tooltip_text_color',
					'label'             => esc_html__( 'Tooltip Text', 'echo-knowledge-base' ),
					'value'             => $kb_config['glossary_tooltip_text_color'],
					'input_group_class' => 'eckb-condition-depend__glossary_enable ',
					'group_data'        => [ 'dependency-ids' => 'glossary_enable', 'enable-on-values' => 'on' ],
				] );

				EPKB_HTML_Elements::color( [
					'name'              => 'glossary_tooltip_background_color',
					'label'             => esc_html__( 'Tooltip Background', 'echo-knowledge-base' ),
					'value'             => $kb_config['glossary_tooltip_background_color'],
					'input_group_class' => 'eckb-condition-depend__glossary_enable ',
					'group_data'        => [ 'dependency-ids' => 'glossary_enable', 'enable-on-values' => 'on' ],
				] ); ?>

			</div>
		</div>  <?php

		return ob_get_clean();
	}

	/**
	 * Show HTML content for AI Generate ad tab (when AI Features PRO is not active)
	 * @return false|string
	 */
	private static function ai_generate_ad_tab() {
		return EPKB_HTML_Forms::pro_feature_ad_box( array(
			'return_html'  => true,
			'icon'         => 'epkbfa epkbfa-magic',
			'title'        => esc_html__( 'AI Glossary Term Generator', 'echo-knowledge-base' ),
			'desc'         => esc_html__( 'Let AI scan your Knowledge Base articles and automatically suggest glossary terms with definitions.', 'echo-knowledge-base' ),
			'list'         => array(
				esc_html__( 'AI discovers glossary-worthy terms from your articles', 'echo-knowledge-base' ),
				esc_html__( 'Review, edit, and approve suggested terms before adding', 'echo-knowledge-base' ),
				esc_html__( 'Customize AI guidance with optional prompts', 'echo-knowledge-base' ),
			),
			'btn_text'     => esc_html__( 'Upgrade to PRO', 'echo-knowledge-base' ),
			'btn_url'      => 'https://www.echoknowledgebase.com/wordpress-plugin/ai-features/',
			'discount_coupon' => EPKB_AI_PRO_Features_Tab::get_discount_coupon(),
		) );
	}

	/**
	 * Show HTML content for AI Generate tab when AI Features PRO is active but AI is not configured
	 * @return false|string
	 */
	private static function ai_generate_configure_tab() {

		ob_start(); ?>
		<div class="epkb-admin-info-box epkb-admin-info-box--ai-required">
			<div class="epkb-admin-info-box__header">
				<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-exclamation-triangle"></div>
				<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'AI Features Required', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-admin-info-box__body">
				<p><?php esc_html_e( 'To use AI features, please configure your API key and accept the data privacy agreement in General Settings, then enable AI Search or AI Chat.', 'echo-knowledge-base' ); ?></p>
			</div>
		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Display a single term row in the table
	 * @param int $term_id
	 * @param string $name
	 * @param string $definition
	 * @param string $status
	 * @param bool $return_html
	 * @param string $sort_key
	 * @return string|void
	 */
	public static function display_term_row( $term_id, $name, $definition, $status, $return_html = false, $sort_key = '' ) {

		if ( $return_html ) {
			ob_start();
		}

		$status_label = $status === 'publish' ? esc_html__( 'Published', 'echo-knowledge-base' ) : esc_html__( 'Draft', 'echo-knowledge-base' );
		$status_class = $status === 'publish' ? 'epkb-glossary-status--publish' : 'epkb-glossary-status--draft'; ?>

		<tr class="epkb-glossary-term-row" data-term-id="<?php echo esc_attr( $term_id ); ?>" data-status="<?php echo esc_attr( $status ); ?>" data-sort-key="<?php echo esc_attr( $sort_key ); ?>">
			<td class="epkb-glossary-term-row__checkbox"><input type="checkbox" class="epkb-glossary-term-select"></td>
			<td class="epkb-glossary-term-row__name"><?php echo esc_html( $name ); ?></td>
			<td class="epkb-glossary-term-row__definition"><?php echo esc_html( $definition ); ?></td>
			<td class="epkb-glossary-term-row__status"><span class="<?php echo esc_attr( $status_class ); ?>" title="<?php echo esc_attr( $status_label ); ?>"></span></td>
			<td class="epkb-glossary-term-row__actions">
				<button class="epkb-glossary-edit-btn epkb-primary-btn" title="<?php esc_attr_e( 'Edit', 'echo-knowledge-base' ); ?>">
					<span class="epkbfa epkbfa-edit"></span>
				</button>
				<button class="epkb-glossary-delete-btn epkb-error-btn" title="<?php esc_attr_e( 'Delete', 'echo-knowledge-base' ); ?>">
					<span class="epkbfa epkbfa-trash"></span>
				</button>
			</td>
		</tr> <?php

		if ( $return_html ) {
			return ob_get_clean();
		}
	}
}
