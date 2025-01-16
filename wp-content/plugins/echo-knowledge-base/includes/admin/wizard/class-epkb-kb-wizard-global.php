<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB configuration Wizard
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Global {

	var $kb_config = array();
	private $article_path_matches = false;
	private $main_page_slugs = [];
	private $kb_main_pages = [];
	private $main_page_id = 0;

	function __construct( $kb_config ) {

		$this->kb_config = $kb_config;

		// get a list of the pages
		// with WMPL we want to show just the main language URLs
		if ( class_exists('SitePress') ) {
			global $sitepress;

			// get pages that are only for the default language
			foreach ( $this->kb_config['kb_main_pages'] as $post_id => $title ) {
				$post_language_information = apply_filters( 'wpml_post_language_details', NULL, $post_id );

				if ( ! empty($post_language_information['language_code']) && $post_language_information['language_code'] == $sitepress->get_default_language() ) {
					$this->kb_main_pages[$post_id] = $title;
				}
			}

		} else {
			$this->kb_main_pages = $this->kb_config['kb_main_pages'];
		}

		// get a list of the pages
		// with WMPL we want to show just the main language URLs
		$this->main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $this->kb_config );
		foreach ( $this->kb_main_pages as $post_id => $title ) {
			$this->main_page_slugs[$post_id] = EPKB_Core_Utilities::get_main_page_slug( $post_id );
			$this->article_path_matches = $this->article_path_matches || $this->main_page_slugs[$post_id] == $this->kb_config['kb_articles_common_path'];
		}
	}

	/**
	 * Get Wizard page
	 *
	 * @return false|string
	 */
	public function show_kb_urls() {

        $html = new EPKB_HTML_Forms();

		ob_start();

		// core handles only default KB
		if ( $this->kb_config['id'] != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! EPKB_Utilities::is_multiple_kbs_enabled() ) {

            return $html::notification_box_middle (
                array(
                    'type' => 'error-no-icon',
                    'desc' => 'Ensure that Unlimited KBs add-on is active and refresh this page.' . EPKB_Utilities::contact_us_for_support() ,
                ) ,true );

		}       ?>

		<div id="eckb-wizard-global__page" class="eckb-wizard-global-page epkb-config-wizard-content" data-kb-main-page-id="<?php echo esc_attr( $this->main_page_id ); ?>">
			<div class="epkb-config-wizard-inner"> <?php

				if ( ! $this->article_path_matches ) {

                    $html::notification_box_middle (
                        array(
                            'type' => 'error-no-icon',
                            'desc' => 'We detected that your KB URL has changed. When ready, please update your Articles URL below to match the KB URL:' ,
                        ) );

				}   ?>

				<!------- Wizard Content ---------->
				<div class="epkb-wizard-content">
					<?php self::show_loader_html(); ?>
					<?php $this->slug_options(); ?>
				</div>

				<div id='epkb-ajax-in-progress' style="display:none;">
					<?php esc_html__( 'Saving configuration', 'echo-knowledge-base' ); ?> <img class="epkb-ajax waiting" style="height: 30px;" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif' ); ?>">
				</div>
				<input type="hidden" id="epkb_wizard_kb_id" name="epkb_wizard_kb_id" value="<?php echo esc_attr( $this->kb_config['id'] ); ?>"/>

				<div class="eckb-bottom-notice-message"></div>
			</div>
		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Wizard: Slug Options
	 */
	private function slug_options() {

		$site_url = site_url();

		$input_index = 0;     ?>

		<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1 epkb-wc-step-panel--active">

			<!-- CURRENT KNOWLEDGE BASE URL section -->      <?php
			$main_page_slug = isset( $this->main_page_slugs[$this->main_page_id] ) ? $this->main_page_slugs[$this->main_page_id] : '';      ?>
			<h3 class="epkb-wso__options-title"><?php esc_html_e( 'Current Knowledge Base URL', 'echo-knowledge-base' ); ?></h3>

			<!-- Current KB Articles URL -->
			<div class="epkb-wso__options-cotent">
				<div class="epkb-wso__option-row epkb-wso__option-row--with-category">  <?php
					$current_url = $this->article_path_matches ? $main_page_slug : $this->kb_config['kb_articles_common_path'];
					$this->url_options_for_kb_main_page( $current_url, $site_url, $input_index, $this->main_page_id );   ?>
					<div class="epkb-wso__option__edit-button">
						<a class="epkb-kb__wizard-link" href="<?php echo esc_url( get_edit_post_link( $this->main_page_id ) ); ?>" target="_blank"><?php esc_html_e( 'Edit Page', 'echo-knowledge-base' ); ?></a>
					</div>
				</div>
			</div>      <?php

			// If multiple KB Main Pages found, then list all of them which are not used
			if ( count( $this->kb_main_pages ) > 1 || ! $this->article_path_matches ) {  ?>

				<!--  SWITCH TO A DIFFERENT URL section -->
				<h3 class="epkb-wso__options-title"><?php esc_html_e( 'Switch to a Different URL', 'echo-knowledge-base' ); ?></h3>
				<div class="epkb-wso__options-cotent">
					<div class="epkb-wso__options-description"><?php echo ( count( $this->kb_main_pages ) > 1 ? esc_html__( 'Choose one of the other pages with KB shortcode:', 'echo-knowledge-base' ) : '' ); ?></div>    <?php

						foreach ( $this->kb_main_pages as $post_id => $title ) {
							$input_index++;

							$kb_main_page_slug = empty( $this->main_page_slugs[$post_id] ) ? '' : $this->main_page_slugs[$post_id];
							if ( empty( $kb_main_page_slug ) ) {
								continue;
							}

							// Do not show currently active KB Main Page if its URL was not changed
							if ( $post_id == $this->main_page_id && $this->article_path_matches ) {
								continue;
							}

							$is_new_url = $post_id == $this->main_page_id && ! $this->article_path_matches;     ?>

							<!-- Articles URL Structure -->
							<div class="epkb-wso__option-row epkb-wso__option-row--with-category">  <?php
								$this->url_options_for_kb_main_page( $this->main_page_slugs[$post_id], $site_url, $input_index, $post_id, $is_new_url );    ?>
							</div>  <?php
						}   ?>

				</div>  <?php
			}       ?>

			<input type="hidden" name="categories_in_url_enabled" id="categories_in_url_enabled" value="<?php echo esc_attr( $this->kb_config['categories_in_url_enabled'] ); ?>">
			<input type="hidden" name="kb_articles_common_path" id="kb_articles_common_path" value="<?php echo esc_attr( $this->kb_config['kb_articles_common_path'] ); ?>">

		</div>	<?php
	}

	/**
	 * Show option rows for a single KB Main Page
	 *
	 * @param $page_slug
	 * @param $site_url
	 * @param $input_index
	 * @param $current_page_id
	 * @param bool $is_new_url
	 */
	private function url_options_for_kb_main_page( $page_slug, $site_url, $input_index, $current_page_id, $is_new_url=false ) {  ?>

		<div class="epkb-wso__option-container">
			<input id="q<?php echo esc_attr( $input_index ); ?>" type="radio" data-path="<?php echo esc_attr( $page_slug ); ?>" data-kb-main-page-id="<?php echo esc_attr( $current_page_id ); ?>" class="eckb_slug" name="eckb_slug">
			<label for="q<?php echo esc_attr( $input_index ); ?>" class="epkb-global-wizard-slug-label">

				<!-- Site URL -->
				<span class="epkb-wso-with-category__site-url">
					<span class="epkb-wso-with-category__slug"><?php echo esc_html( $site_url ); ?></span>
				</span>

				<!-- KB slug -->
				<span class="epkb-wso-with-category__main-page-slug">
					<span class="epkb-wso-with-category__divider"> / </span>
					<span class="epkb-wso-with-category__slug"><?php echo esc_html( $page_slug ); ?></span>
				</span>

				<!-- Category slug -->
				<span class="epkb-wso-with-category__category<?php echo $this->kb_config['categories_in_url_enabled'] == 'on' ? '' : ' epkb-wso-with-category__category--off'; ?>">
					<span class="epkb-wso-with-category__divider"> / </span>
					<span class="epkb-wso-with-category__slug"><?php esc_html_e( 'kb-category', 'echo-knowledge-base' ); ?></span>
				</span>

				<!-- Article slug -->
				<span class="epkb-wso-with-category__article">
					<span class="epkb-wso-with-category__divider"> / </span>
					<span class="epkb-wso-with-category__slug"><?php esc_html_e( 'kb-article', 'echo-knowledge-base' ); ?></span>
				</span>     <?php

				if ( $is_new_url ) {   ?>
					<span class="epkb-wso-with-category__new-url">[<?php esc_html_e( 'This is a new URL', 'echo-knowledge-base' ); ?>]</span>     <?php
				}   ?>

			</label>
		</div>      <?php
	}

	/**
	 * THis configuration defines fields that are part of this wizard configuration related to text.
	 * All other fields will be excluded when applying changes.
	 * @var array
	 */
	public static $global_fields = array(
		'kb_articles_common_path',
		'categories_in_url_enabled'
	);

	/**
	 * Wizard: Previous / Next Buttons / Apply Buttons
	 * @return false|string
	 */
	public function show_apply_button() {

		if ( empty( $this->kb_config['kb_main_pages'] ) ) {
			return '';
		}

		ob_start();	?>

		<div class="epkb-wizard-button-container__inner">
			<button value="apply" id="epkb-wizard-button-apply" class="epkb-wizard-button epkb-wizard-button-apply"  data-wizard-type="global"><?php esc_html_e( 'Apply', 'echo-knowledge-base' ); ?></button>
			<input type="hidden" id="_wpnonce_epkb_ajax_action" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>">
		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Show HTML for Loader
	 */
	public static function show_loader_html() { ?>
		<div class="epkb-admin-dialog-box-loading">
			<div class="epkb-admin-dbl__header">
				<div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>
				<div class="epkb-admin-text"><?php esc_html_e( 'Loading...', 'echo-knowledge-base' ); ?></div>
			</div>
		</div>
		<div class="epkb-admin-dialog-box-overlay"></div> <?php
	}

	/**
	 * Show Category Slug in KB URL Toggle
	 * @return false|string
	 */
	public function show_category_slug_toggle() {
		return EPKB_HTML_Elements::checkbox_toggle( array(
			'id'            => 'categories_in_url_enabled__toggle',
			'textLoc'       => 'baseline',
			'data'          => 'on',
			'toggleOnText'  => esc_html__( 'yes', 'echo-knowledge-base' ),
			'toggleOffText' => esc_html__( 'no', 'echo-knowledge-base' ),
			'checked'       => $this->kb_config['categories_in_url_enabled'] == 'on',
			'return_html'   => true,
			'topDesc'       => esc_html__( 'Should article URLs contain the slug of their categories?', 'echo-knowledge-base' ),
		) );
	}
}
