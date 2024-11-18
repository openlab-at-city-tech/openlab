<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB Ordering Wizard
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Ordering {

	var $kb_config = array();
	var $feature_specs = array();
	var $kb_id;

	function __construct() {
		add_action( 'epkb-wizard-ordering-page-feature-selection-container', array( $this, 'article_category_ordering' ) );
	}

	/**
	 * Get Wizard page
	 *
	 * @param $kb_config
	 * @return false|string
	 */
	public function show_article_ordering( $kb_config ) {

		$this->kb_config = $kb_config;
		$this->kb_id = $this->kb_config['id'];
		$this->feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $this->kb_config['id'] );
        $HTML = new EPKB_HTML_Forms();

		ob_start();

		// core handles only default KB
		if ( $this->kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! defined( 'E' . 'MKB_PLUGIN_NAME' ) ) {

            return $HTML::notification_box_middle (
                array(
                    'type' => 'error-no-icon',
                    'desc' => 'Ensure that Unlimited KBs add-on is active and refresh this page. '.EPKB_Utilities::contact_us_for_support() ,
                ) ,true );
		}       ?>

		<div id="eckb-wizard-ordering__page" class="eckb-wizard-ordering epkb-config-wizard-content">
			<div class="epkb-config-wizard-inner">

					<!------- Wizard Status Bar ------->
				<div class="epkb-wizard-status-bar">
					<ul>
						<li id="epkb-wsb-step-1" class="epkb-wsb-step epkb-wsb-step--active"><?php esc_html_e( 'Choose Type of Order', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-2" class="epkb-wsb-step"><?php esc_html_e( 'Order Articles and Categories', 'echo-knowledge-base'); ?></li>
					</ul>
				</div>

				<!------- Wizard Content ---------->
				<div class="epkb-wizard-content">
					<?php self::show_loader_html(); ?>
					<?php $this->page_article_category_ordering(); ?>
					<?php $this->page_ordering(); ?>
				</div>

				<!------- Wizard Footer ---------->
				<div class="epkb-wizard-footer">
					<?php $this->wizard_buttons(); ?>
				</div>

				<div id='epkb-ajax-in-progress' style="display:none;">
					<?php esc_html__( 'Saving configuration', 'echo-knowledge-base' ); ?> <img class="epkb-ajax waiting" style="height: 30px;" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif' ); ?>">
				</div>
				<input type="hidden" id="epkb_wizard_kb_id" name="epkb_wizard_kb_id" value="<?php echo esc_attr( $this->kb_id ); ?>"/>
				<input type="hidden" id="use_top_sequence" value="<?php echo ( $this->kb_config['kb_main_page_layout'] == 'Tabs' ) ? 'yes' : 'no'; ?>">
				<input type="hidden" id="original_show_articles_before_categories" value="<?php echo empty( $this->kb_config['show_articles_before_categories'] ) ? '' : esc_attr( $this->kb_config['show_articles_before_categories'] ); ?>">

				<div class="eckb-bottom-notice-message"></div>
			</div>
		</div> <?php

		return ob_get_clean();
	}

	// Wizard: Step 1 - Main Page
	private function page_article_category_ordering() {         ?>

		<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1 epkb-wc-step-panel--active">
			<div class="epkb-wizard-ordering-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-ordering-page-feature-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}
	
	// Wizard: Step 2 - Ordering Page
	private function page_ordering() {         ?>

		<div id="epkb-wsb-step-2-panel" class="epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-wizard-ordering-ordering-preview"><?php // will be filled with ajax ?></div>
		</div>	<?php
	}

	//Wizard: Previous / Next Buttons / Apply Buttons
	public function wizard_buttons() {      ?>

		<div class="epkb-wizard-button-container epkb-wizard-button-container--first-step">
			<div class="epkb-wizard-button-container__inner">
				<button value="0" id="epkb-wizard-button-prev" class="epkb-wizard-button epkb-wizard-button-prev">
					<span class="epkb-wizard-button-prev__icon epkbfa epkbfa-caret-left"></span>
					<span class="epkb-wizard-button-prev__text"><?php esc_html_e( 'Previous', 'echo-knowledge-base' ); ?></span>
				</button>
				<button value="2" id="epkb-wizard-button-next" class="epkb-wizard-button epkb-wizard-button-next">
					<span class="epkb-wizard-button-next__text"><?php esc_html_e( 'Next', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-button-next__icon epkbfa epkbfa-caret-right"></span>
				</button>
				<button value="apply" id="epkb-wizard-button-apply" class="epkb-wizard-button epkb-wizard-button-apply"  data-wizard-type="ordering"><?php esc_html_e( 'Apply', 'echo-knowledge-base' ); ?></button>

				<input type="hidden" id="_wpnonce_epkb_ajax_action" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
			</div>
		</div>	<?php
	}

	/**
	 * Call all hooks for given Wizard section.
	 *
	 * @param $hook - both hook name and div id
	 * @param $args
	 */
	public function wizard_section( $hook, $args ) {
		do_action( $hook, $args );
	}

	/**
	 * Show Wizard page options for article and category ordering
	 *
	 * @param $args
	 */
	public function article_category_ordering( $args ) {
		$kb_id = $args['id'];
		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );

		self::option_group_wizard( $feature_specs, array(
			'option-heading'    => esc_html__( 'I want organize Categories and Articles', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features',
			'inputs_escaped' => array(
				'0' => EPKB_HTML_Elements::radio_buttons_vertical( $feature_specs['categories_display_sequence'] + array(
						'id'        => 'front-end-columns',
						'value'     => $kb_config['categories_display_sequence'],
						'input_group_class' => 'epkb-radio-vertical-group-container',
                        'return_html' => true,
					) ),
				'1' => EPKB_HTML_Elements::radio_buttons_vertical( $feature_specs['articles_display_sequence'] + array(
						'id'        => 'front-end-columns',
						'value'     => $kb_config['articles_display_sequence'],
						'input_group_class' => 'epkb-radio-vertical-group-container' . ( ($kb_config['kb_main_page_layout'] == 'Grid') ? ' epkb-grid-option-hide-show' : ''),
						'return_html' => true,

					) ),
				'2' => EPKB_HTML_Elements::radio_buttons_vertical( $feature_specs['show_articles_before_categories'] + array(
						'value'     => $kb_config['show_articles_before_categories'],
						'input_group_class' => 'epkb-radio-vertical-group-container',
						'return_html' => true,
					) ),	
				)));           
	}

	/**
	 * Display configuration options
	 * @param $feature_specs
	 * @param array $args
	 */
	private static function option_group_wizard( $feature_specs, $args = array() ) {

		$defaults = array(
			'info' => '',
			'option-heading' => '',
			'class' => ' ',
			'addition_info' => '',
		);
		$args = array_merge( $defaults, $args );

		// there might be multiple classes
		$classes = explode(' ', $args['class']);
		$class_string = '';
		foreach( $classes as $class ) {
			$class_string .= $class . '-content ';
		}

		$depends_escaped = '';

		if ( isset($args['depends']) ) {
			$depends_escaped = "data-depends='" . htmlspecialchars( wp_json_encode( $args['depends'] ), ENT_QUOTES, 'UTF-8' ) . "'";
		}		?>

		<div class="<?php echo esc_attr( $class_string ); ?>" <?php echo $depends_escaped; ?>>	        <?php

			if ( $args['option-heading'] ) {    ?>
				<div class="eckb-wizard-option-heading">
					<h4><?php esc_html_e( $args['option-heading'], 'echo-knowledge-base' ); ?>
						<span class="epkbfa epkbfa-caret-right"></span>
						<span class="epkbfa epkbfa-caret-down"></span>
					</h4>
					<span class="ep_font_icon_info option-info-icon"></span>
				</div>            <?php

			} else {     ?>
				<div class="config-option-info">
					<span class="ep_font_icon_info option-info-icon"></span>
				</div>            <?php

			}           ?>

			<div class="option-info-content hidden">
				<h5 class="option-info-title"><?php esc_html_e( 'Help', 'echo-knowledge-base' ); ?></h5>                    <?php
				if ( $feature_specs ) {
					if ( is_array( $args['info']) ) {
						foreach( $args['info'] as $item ) {
							if ( empty($feature_specs[$item]) ) {
								continue;
							}
							echo '<h6>' . esc_html( $feature_specs[$item]['label'] ) . '</h6>';
							echo '<p>' . esc_html( $feature_specs[$item]['info'] ) . '</p>';
						}
					} else {
						echo '<p>' . esc_html( $args['info'] ) . '</p>';
					}
				}		            ?>
			</div>            <?php

			foreach ( $args['inputs_escaped'] as $input ) {
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
				echo $input;
			}

			// Add content after Settings
			if ( ! empty($args['addition_info']) ) {
				echo '<div class="eckb-wizard-default-note">' . esc_html( $args['addition_info'] ) . '</div>';
			}		?>

		</div><!-- config-option-group -->        <?php
	}

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
	 * THis configuration defines fields that are part of this wizard configuration related to search.
	 * All other fields will be excluded when applying changes.
	 * @var array
	 */
	public static $ordering_fields = array(
		'categories_display_sequence',
		'articles_display_sequence',
		'show_articles_before_categories',
		'sidebar_show_articles_before_categories',
	);
}
