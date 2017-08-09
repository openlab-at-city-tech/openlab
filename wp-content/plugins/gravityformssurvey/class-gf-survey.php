<?php

//------------------------------------------

GFForms::include_addon_framework();

class GFSurvey extends GFAddOn {

	protected $_version = GF_SURVEY_VERSION;
	protected $_min_gravityforms_version = '2.0';
	protected $_slug = 'gravityformssurvey';
	protected $_path = 'gravityformssurvey/survey.php';
	protected $_full_path = __FILE__;
	protected $_url = 'http://www.gravityforms.com';
	protected $_title = 'Gravity Forms Survey Add-On';
	protected $_short_title = 'Survey';
	protected $_enable_rg_autoupgrade = true;

	/**
	 * Members plugin integration
	 */
	protected $_capabilities = array( 'gravityforms_survey', 'gravityforms_survey_uninstall', 'gravityforms_survey_results' );

	/**
	 * Permissions
	 */
	protected $_capabilities_settings_page = 'gravityforms_survey';
	protected $_capabilities_form_settings = 'gravityforms_survey';
	protected $_capabilities_uninstall = 'gravityforms_survey_uninstall';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFSurvey
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFSurvey();
		}

		return self::$_instance;
	}

	private function __clone() {
	} /* do nothing */

	/**
	 * Handles anything which requires early initialization.
	 */
	public function pre_init() {
		parent::pre_init();

		if ( $this->is_gravityforms_supported() && class_exists( 'GF_Field' ) ) {
			require_once( 'includes/class-gf-field-survey.php' );
			require_once( 'includes/class-gf-field-likert.php' );
			require_once( 'includes/class-gf-field-rank.php' );
			require_once( 'includes/class-gf-field-rating.php' );

			add_filter( 'gform_export_field_value', array( $this, 'export_field_value' ), 10, 4 );
		}
	}

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {

		// Integration with the feed add-ons as of GF 1.9.15.12; for add-ons which don't override get_field_value().
		add_filter( 'gform_addon_field_value', array( $this, 'addon_field_value' ), 10, 5 );

		// AWeber 2.3 and newer use the gform_addon_field_value hook, only use the gform_aweber_field_value hook with older versions.
		if ( defined( 'GF_AWEBER_VERSION' ) && version_compare( 'GF_AWEBER_VERSION', '2.3', '<' ) ) {
			add_filter( 'gform_aweber_field_value', array( $this, 'legacy_addon_field_value' ), 10, 4 );
		}

		// Campaign Monitor Add-On integration
		add_filter( 'gform_campaignmonitor_field_value', array( $this, 'legacy_addon_field_value' ), 10, 4 );

		// Mailchimp Add-On integration
		add_filter( 'gform_mailchimp_field_value', array( $this, 'legacy_addon_field_value' ), 10, 4 );

		// Zapier Add-On integration
		add_filter( 'gform_zapier_field_value', array( $this, 'zapier_field_value' ), 10, 4 );

		// merge tags
		add_filter( 'gform_replace_merge_tags', array( $this, 'replace_merge_tags' ), 10, 7 );

		// add a special class to likert fields so we can identify them later
		add_action( 'gform_field_css_class', array( $this, 'add_custom_class' ), 10, 3 );

		// display survey results on entry detail
		add_filter( 'gform_entry_field_value', array( $this, 'entry_field_value' ), 10, 4 );

		// conditional logic filters
		add_filter( 'gform_entry_meta_conditional_logic_confirmations', array( $this, 'conditional_logic_filters' ), 10, 3 );
		add_filter( 'gform_entry_meta_conditional_logic_notifications', array( $this, 'conditional_logic_filters' ), 10, 3 );

		parent::init();

	}

	/**
	 * Initialize the admin specific hooks.
	 */
	public function init_admin() {

		// form editor
		add_action( 'gform_field_standard_settings', array( $this, 'survey_field_settings' ), 10, 2 );
		add_filter( 'gform_tooltips', array( $this, 'add_survey_tooltips' ) );

		// merge tags
		add_filter( 'gform_admin_pre_render', array( $this, 'add_merge_tags' ) );

		// display results on entry list
		add_filter( 'gform_entries_field_value', array( $this, 'export_field_value' ), 10, 4 );

		// declare arrays on form import
		add_filter( 'gform_import_form_xml_options', array( $this, 'import_file_options' ) );

		// contacts
		add_filter( 'gform_contacts_tabs_contact_detail', array( $this, 'add_tab_to_contact_detail' ), 10, 2 );
		add_action( 'gform_contacts_tab_survey', array( $this, 'contacts_tab' ) );

		parent::init_admin();

	}

	/**
	 * The Survey add-on does not support logging.
	 *
	 * @param array $plugins The plugins which support logging.
	 *
	 * @return array
	 */
	public function set_logging_supported( $plugins ) {

		return $plugins;

	}


	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
		$gsurvey_js_deps = array( 'jquery', 'jquery-ui-sortable' );
		if ( wp_is_mobile() ) {
			$gsurvey_js_deps[] = 'jquery-touch-punch';
		}

		$scripts = array(
			array(
				'handle'   => 'gsurvey_form_editor_js',
				'src'      => $this->get_base_url() . '/js/gsurvey_form_editor.js',
				'version'  => $this->_version,
				'deps'     => array( 'jquery' ),
				'callback' => array( $this, 'localize_scripts' ),
				'enqueue'  => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
			),
			array(
				'handle'  => 'gsurvey_js',
				'src'     => $this->get_base_url() . '/js/gsurvey.js',
				'version' => $this->_version,
				'deps'    => $gsurvey_js_deps,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor', 'results', 'entry_view', 'entry_detail', 'entry_edit' ) ),
					array( 'field_types' => array( 'survey' ) ),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {

		$styles = array(
			array(
				'handle'  => 'gsurvey_form_editor_css',
				'src'     => $this->get_base_url() . '/css/gsurvey_form_editor.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
			),
			array(
				'handle'  => 'gsurvey_css',
				'src'     => $this->get_base_url() . '/css/gsurvey.css',
				'version' => $this->_version,
				'media'   => 'screen',
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor', 'results', 'entry_view', 'entry_detail', 'entry_edit' ) ),
					array(
						'field_types' => array( 'survey' )
					),
				),
			),
		);

		return array_merge( parent::styles(), $styles );
	}

	/**
	 * Localize the strings used by the scripts.
	 */
	public function localize_scripts() {

		// Get current page protocol
		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		// Output admin-ajax.php URL with same protocol as current page
		$params = array(
			'ajaxurl'   => admin_url( 'admin-ajax.php', $protocol ),
			'imagesUrl' => $this->get_base_url() . '/images',
			'strings'   => array(
				'untitledSurveyField' => esc_html__( 'Untitled Survey Field', 'gravityformssurvey' ),
			),
		);
		wp_localize_script( 'gsurvey_form_editor_js', 'gsurveyVars', $params );

		//localize strings for the js file
		$strings = array(
			'firstChoice'      => esc_html__( 'First row', 'gravityformssurvey' ),
			'secondChoice'     => esc_html__( 'Second row', 'gravityformssurvey' ),
			'thirdChoice'      => esc_html__( 'Third row', 'gravityformssurvey' ),
			'fourthChoice'     => esc_html__( 'Fourth row', 'gravityformssurvey' ),
			'fifthChoice'      => esc_html__( 'Fifth row', 'gravityformssurvey' ),
			'dragToReOrder'    => esc_html__( 'Drag to re-order', 'gravityformssurvey' ),
			'addAnotherRow'    => esc_html__( 'Add another row', 'gravityformssurvey' ),
			'removeThisRow'    => esc_html__( 'Remove this row', 'gravityformssurvey' ),
			'addAnotherColumn' => esc_html__( 'Add another column', 'gravityformssurvey' ),
			'removeThisColumn' => esc_html__( 'Remove this column', 'gravityformssurvey' ),
			'columnLabel1'     => esc_html__( 'Strongly disagree', 'gravityformssurvey' ),
			'columnLabel2'     => esc_html__( 'Disagree', 'gravityformssurvey' ),
			'columnLabel3'     => esc_html__( 'Neutral', 'gravityformssurvey' ),
			'columnLabel4'     => esc_html__( 'Agree', 'gravityformssurvey' ),
			'columnLabel5'     => esc_html__( 'Strongly agree', 'gravityformssurvey' ),

		);
		wp_localize_script( 'gsurvey_form_editor_js', 'gsurveyLikertStrings', $strings );

		//localize strings for the rank field
		$rank_strings = array(
			'firstChoice'  => esc_html__( 'First Choice', 'gravityformssurvey' ),
			'secondChoice' => esc_html__( 'Second Choice', 'gravityformssurvey' ),
			'thirdChoice'  => esc_html__( 'Third Choice', 'gravityformssurvey' ),
			'fourthChoice' => esc_html__( 'Fourth Choice', 'gravityformssurvey' ),
			'fifthChoice'  => esc_html__( 'Fifth Choice', 'gravityformssurvey' ),
		);
		wp_localize_script( 'gsurvey_form_editor_js', 'gsurveyRankStrings', $rank_strings );

		//localize strings for the ratings field
		$rating_strings = array(
			'firstChoice'  => esc_html__( 'Terrible', 'gravityformssurvey' ),
			'secondChoice' => esc_html__( 'Not so great', 'gravityformssurvey' ),
			'thirdChoice'  => esc_html__( 'Neutral', 'gravityformssurvey' ),
			'fourthChoice' => esc_html__( 'Pretty good', 'gravityformssurvey' ),
			'fifthChoice'  => esc_html__( 'Excellent', 'gravityformssurvey' ),
		);
		wp_localize_script( 'gsurvey_form_editor_js', 'gsurveyRatingStrings', $rating_strings );

	}

	public function localize_results_scripts() {

		$filter_fields    = rgget( 'f' );
		$filter_types     = rgget( 't' );
		$filter_operators = rgget( 'o' );
		$filter_values    = rgget( 'v' );

		// Get current page protocol
		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		// Output admin-ajax.php URL with same protocol as current page

		$vars = array(
			'ajaxurl'         => admin_url( 'admin-ajax.php', $protocol ),
			'imagesUrl'       => $this->get_base_url() . '/images',
			'filterFields'    => $filter_fields,
			'filterTypes'     => $filter_types,
			'filterOperators' => $filter_operators,
			'filterValues'    => $filter_values,
		);

		wp_localize_script( 'gsurvey_results_js', 'gresultsVars', $vars );

		$strings = array(
			'noFilters'         => esc_html__( 'No filters', 'gravityformspolls' ),
			'addFieldFilter'    => esc_html__( 'Add a field filter', 'gravityformspolls' ),
			'removeFieldFilter' => esc_html__( 'Remove a field filter', 'gravityformspolls' ),
			'ajaxError'         => esc_html__( 'Error retrieving results. Please contact support.', 'gravityformspolls' ),
		);

		wp_localize_script( 'gsurvey_results_js', 'gresultsStrings', $strings );

	}


	// # RESULTS & SCORING ----------------------------------------------------------------------------------------------

	/**
	 * Configure the survey results page.
	 *
	 * @return array
	 */
	public function get_results_page_config() {
		return array(
			'title'        => esc_html__( 'Survey Results', 'gravityformssurvey' ),
			'capabilities' => array( 'gravityforms_survey_results' ),
			'callbacks'    => array(
				'fields'  => array( $this, 'results_fields' ),
				'filters' => array( $this, 'results_filters' ),
			),
		);
	}

	/**
	 * Remove the score from the results page filters if scoring is not enabled on a Likert field.
	 *
	 * @param array $filters
	 * @param array $form
	 *
	 * @return array
	 */
	public function results_filters( $filters, $form ) {
		$fields = $form['fields'];
		foreach ( $fields as $field ) {
			if ( $field->get_input_type() == 'likert' && $field->gsurveyLikertEnableScoring ) {
				return $filters;
			}
		}

		foreach ( $filters as $key => $filter ) {
			if ( $filter['key'] == 'gsurvey_score' ) {
				unset( $filters[ $key ] );
			}
		}

		return $filters;
	}

	/**
	 * Get all the survey fields for the current form.
	 *
	 * @param array $form The current form object.
	 *
	 * @return GF_Field[]
	 */
	public function results_fields( $form ) {
		return GFAPI::get_fields_by_type( $form, array( 'survey' ) );
	}

	/**
	 * Helper to check if scoring is enabled on at least one of the forms Likert fields.
	 *
	 * @param array $form The current form object.
	 *
	 * @return bool
	 */
	private function scoring_enabled( $form ) {
		$survey_fields = $this->results_fields( $form );

		foreach ( $survey_fields as $field ) {
			if ( $field->get_input_type() == 'likert' && $field->gsurveyLikertEnableScoring ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the total score of all likert fields with scoring enabled.
	 *
	 * @param array $form The current form object.
	 * @param array $entry The current entry object.
	 *
	 * @return float|int
	 */
	public function get_survey_score( $form, $entry ) {
		$survey_fields = $this->results_fields( $form );
		$score         = 0;
		foreach ( $survey_fields as $field ) {
			if ( $field->get_input_type() == 'likert' && $field->gsurveyLikertEnableScoring ) {
				$score += $this->get_field_score( $field, $entry );
			}
		}

		return $score;
	}

	/**
	 * Returns the total score of all likert fields with scoring enabled.
	 *
	 * @param array $form The current form object.
	 * @param array $entry The current entry object.
	 *
	 * @return float|int
	 */
	public function get_total_score( $form, $entry ) {

		return $this->get_survey_score( $form, $entry );
	}

	/**
	 * Returns the score for the specified field.
	 *
	 * Called statically by GFResults.
	 *
	 * @param GF_Field_Likert $field The current field.
	 * @param array $entry The current entry object.
	 *
	 * @return float|int
	 */
	public static function get_field_score( $field, $entry ) {
		$score = 0;
		if ( $field->gsurveyLikertEnableMultipleRows ) {
			// cycle through the entry values in case the the number of choices has changed since the entry was submitted
			foreach ( $entry as $key => $value ) {
				if ( intval( $key ) != $field->id ) {
					continue;
				}

				if ( false === strpos( $value, ':' ) ) {
					continue;
				}

				list( $row_val, $col_val ) = explode( ':', $value, 2 );

				foreach ( $field->gsurveyLikertRows as $row ) {
					if ( $row['value'] == $row_val ) {
						foreach ( $field->choices as $choice ) {
							if ( $choice['value'] == $col_val ) {
								$score += floatval( rgar( $choice, 'score' ) );
							}
						}
					}
				}
			}
		} else {
			$value = rgar( $entry, $field->id );
			if ( ! empty( $value ) ) {
				foreach ( $field->choices as $choice ) {
					if ( $choice['value'] == $value ) {
						$score += floatval( rgar( $choice, 'score' ) );
					}
				}
			}
		}

		return $score;
	}

	/**
	 * Returns the score for the specified row.
	 *
	 * @param string $target_row_val The unique id (value) for the likert row.
	 * @param GF_Field_Likert $field The current field.
	 * @param array $entry The current entry.
	 *
	 * @return float|int
	 */
	public static function get_likert_row_score( $target_row_val, $field, $entry ) {
		$score = 0;

		if ( $field->gsurveyLikertEnableMultipleRows ) {

			foreach ( $entry as $key => $value ) {
				if ( intval( $key ) != $field->id ) {
					continue;
				}

				if ( false === strpos( $value, ':' ) ) {
					continue;
				}

				list( $row_val, $col_val ) = explode( ':', $value, 2 );

				foreach ( $field->gsurveyLikertRows as $row ) {
					if ( $row['value'] == $row_val && $target_row_val == $row_val ) {
						foreach ( $field->choices as $choice ) {
							if ( $choice['value'] == $col_val ) {
								$score = floatval( rgar( $choice, 'score' ) );

								return $score;
							}
						}
					}
				}
			}
		} else {
			$score = self::get_field_score( $field, $entry );
		}

		return $score;
	}

	// # MERGE TAGS -----------------------------------------------------------------------------------------------------

	/**
	 * Add the score merge tags to the merge tag drop downs in the admin.
	 *
	 * @param array $form The current form.
	 *
	 * @return array
	 */
	public function add_merge_tags( $form ) {
		if ( ! $this->is_form_settings() ) {
			return $form;
		}

		$survey_fields = GFAPI::get_fields_by_type( $form, array( 'survey' ) );
		if ( empty( $survey_fields ) ) {
			return $form;
		}

		$scoring_enabled = false;
		$merge_tags      = array();
		foreach ( $form['fields'] as $field ) {
			if ( $field->get_input_type() == 'likert' && $field->gsurveyLikertEnableScoring ) {
				$scoring_enabled = true;
				$field_id        = $field->id;
				$field_label     = $field->label;
				$group           = $field->isRequired ? 'required' : 'optional';
				$merge_tags[]    = array( 'group' => $group, 'label' => esc_html__( 'Survey Field Score: ', 'gravityformssurvey' ) . $field_label, 'tag' => "{score:id={$field_id}}" );
			}
		}
		if ( $scoring_enabled ) {
			$merge_tags[] = array( 'group' => 'other', 'label' => esc_html__( 'Survey Total Score', 'gravityformssurvey' ), 'tag' => '{survey_total_score}' );
		}
		?>
		<script type="text/javascript">
			if (window.gform)
				gform.addFilter("gform_merge_tags", "gsurvey_add_merge_tags");
			function gsurvey_add_merge_tags(mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option) {
				if (isPrepop)
					return mergeTags;
				var customMergeTags = <?php echo json_encode( $merge_tags ); ?>;
				jQuery.each(customMergeTags, function (i, customMergeTag) {
					mergeTags[customMergeTag.group].tags.push({ tag: customMergeTag.tag, label: customMergeTag.label });
				});

				return mergeTags;
			}
		</script>
		<?php
		//return the form object from the php hook
		return $form;
	}

	/**
	 * Replace the score merge tags.
	 *
	 * @param string $text The current text in which merge tags are being replaced.
	 * @param array $form The current form object.
	 * @param array $entry The current entry object.
	 * @param bool $url_encode Whether or not to encode any URLs found in the replaced value.
	 * @param bool $esc_html Whether or not to encode HTML found in the replaced value.
	 * @param bool $nl2br Whether or not to convert newlines to break tags.
	 * @param string $format The format requested for the location the merge is being used. Possible values: html, text or url.
	 *
	 * @return string
	 */
	public function replace_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {

		if ( empty( $entry ) || empty( $form ) ) {
			return $text;
		}

		$survey_fields = GFAPI::get_fields_by_type( $form, array( 'survey' ) );
		if ( empty( $survey_fields ) ) {
			return $text;
		}

		$total_merge_tag = '{survey_total_score}';

		if ( false !== strpos( $text, $total_merge_tag ) ) {
			$score_total = $this->get_total_score( $form, $entry );
			$text        = str_replace( $total_merge_tag, $score_total, $text );
		}

		preg_match_all( "/\{score:(.*?)\}/", $text, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {

			$full_tag       = $match[0];
			$options_string = isset( $match[1] ) ? $match[1] : '';
			$options        = shortcode_parse_atts( $options_string );
			extract(
				shortcode_atts(
					array(
						'id' => 0,
					), $options
				)
			);
			if ( 0 == $id ) {
				continue;
			}

			$field = GFFormsModel::get_field( $form, $id );
			if ( ! is_object( $field ) || $field->get_input_type() != 'likert' ) {
				continue;
			}

			if ( $id == intval( $id ) ) {
				$score = $this->get_field_score( $field, $entry );
			} else {
				$score = $this->get_likert_row_score( $field->get_row_id( $id ), $field, $entry );
			}

			$text = str_replace( $full_tag, $url_encode ? urlencode( $score ) : $score, $text );
		}

		return $text;
	}


	// # ENTRY RELATED --------------------------------------------------------------------------------------------------

	/**
	 * Add the Survey Total Score entry meta property.
	 *
	 * @param array $entry_meta An array of entry meta already registered with the gform_entry_meta filter.
	 * @param int $form_id The form id
	 *
	 * @return array The filtered entry meta array.
	 */
	public function get_entry_meta( $entry_meta, $form_id ) {
		if ( empty( $form_id ) ) {
			return $entry_meta;
		}
		$form          = RGFormsModel::get_form_meta( $form_id );
		$survey_fields = GFAPI::get_fields_by_type( $form, array( 'survey' ) );
		if ( false === empty( $survey_fields ) && $this->scoring_enabled( $form ) ) {

			$entry_meta['gsurvey_score'] = array(
				'label'                      => esc_html__( 'Survey Total Score', 'gravityformssurvey' ),
				'is_numeric'                 => true,
				'is_default_column'          => false,
				'update_entry_meta_callback' => array( $this, 'update_entry_meta' ),
				'filter'                     => array(
					'operators' => array( 'is', 'isnot', '>', '<' ),
				),
			);

		}

		return $entry_meta;
	}

	/**
	 * Used to update the Survey Total Score entry meta property
	 *
	 * @param string $key The key of the property to be updated.
	 * @param array $entry The current entry object.
	 * @param array $form The current form object.
	 *
	 * @return mixed
	 */
	public function update_entry_meta( $key, $entry, $form ) {
		$value = '';

		if ( $key == 'gsurvey_score' ) {
			$value = $this->get_survey_score( $form, $entry );
		}


		return $value;
	}

	/**
	 * Remove the survey score from the entry meta conditional logic filters on the notifications/confirmations pages.
	 *
	 * @param array $filters The array of filters.
	 * @param array $form The current form object.
	 * @param string $id The ID of the notification/confirmation being edited.
	 *
	 * @return mixed
	 */
	public function conditional_logic_filters( $filters, $form, $id ) {
		$survey_fields = GFAPI::get_fields_by_type( $form, array( 'survey' ) );
		if ( empty( $survey_fields ) ) {
			return $filters;
		}

		if ( false === $this->scoring_enabled( $form ) ) {
			unset( $filters['gsurvey_score'] );
		}

		return $filters;
	}

	/**
	 * Format the Survey field values for entry exports and the entry list page so they use the choice text instead of values.
	 *
	 * @param string|array $value The field value.
	 * @param int $form_id The ID of the form currently being processed.
	 * @param string $field_id The ID of the field currently being processed.
	 * @param array $entry The entry object currently being processed.
	 *
	 * @return string|array
	 */
	public function export_field_value( $value, $form_id, $field_id, $entry ) {
		if ( ! rgblank( $value ) ) {
			$form_meta = RGFormsModel::get_form_meta( $form_id );
			$field     = RGFormsModel::get_field( $form_meta, $field_id );

			return $this->maybe_format_field_values( $value, $field );
		}

		return $value;
	}

	/**
	 * Format the Survey field values on the entry detail page so they use the choice text instead of values.
	 *
	 * @param string|array $value The field value.
	 * @param GF_Field $field The field currently being processed.
	 * @param array $entry The entry object currently being processed.
	 * @param array $form The form object currently being processed.
	 *
	 * @return string|array
	 */
	public function entry_field_value( $value, $field, $entry, $form ) {

		return ! rgblank( $value ) ? $this->maybe_format_field_values( $value, $field ) : $value;
	}

	/**
	 * Format the Survey field values so they use the choice text instead of values before being passed to the third-party.
	 *
	 * @param string $value The field value.
	 * @param array $form The form currently being processed.
	 * @param array $entry The entry object currently being processed.
	 * @param string $field_id The ID of the field currently being processed.
	 *
	 * @return string
	 */
	public function addon_field_value( $value, $form, $entry, $field_id, $slug ) {
		if ( ! rgblank( $value ) ) {
			$field = RGFormsModel::get_field( $form, $field_id );

			return $this->maybe_format_field_values( $value, $field );
		}

		return $value;
	}

	/**
	 * If the field is a Survey type radio, select or checkbox then replace the choice value with the choice text.
	 *
	 * @param string $value The field value.
	 * @param GF_Field|null $field The field object being processed or null.
	 *
	 * @return string
	 */
	public function maybe_format_field_values( $value, $field ) {

		if ( is_object( $field ) && $field->type == 'survey' ) {
			switch ( $field->inputType ) {
				case 'radio' :
				case 'select' :
					return RGFormsModel::get_choice_text( $field, $value );

				case 'checkbox' :
					if ( is_array( $value ) ) {
						foreach ( $value as &$choice ) {
							if ( ! empty( $choice ) ) {
								$choice = RGFormsModel::get_choice_text( $field, $choice );
							}
						}
					} else {
						foreach ( $field->choices as $choice ) {
							$val   = rgar( $choice, 'value' );
							$text  = rgar( $choice, 'text' );
							$value = str_replace( $val, $text, $value );
						}
					}
			}
		}

		return $value;
	}

	/**
	 * Format the Survey field values so they use the choice text instead of values before being passed to the third-party.
	 *
	 * @param string $value The field value.
	 * @param int $form_id The ID of the form currently being processed.
	 * @param string $field_id The ID of the field currently being processed.
	 * @param array $entry The entry object currently being processed.
	 *
	 * @return string
	 */
	public function legacy_addon_field_value( $value, $form_id, $field_id, $entry ) {
		if ( ! rgblank( $value ) ) {
			$form_meta = RGFormsModel::get_form_meta( $form_id );
			$field     = RGFormsModel::get_field( $form_meta, $field_id );

			if ( is_object( $field ) && $field->type == 'survey' ) {
				return $field->get_value_export( $entry, $field_id, true );
			}
		}

		return $value;
	}

	/**
	 * Format the Survey field values so they use the choice text instead of values before they are sent to Zapier.
	 *
	 * @param string|array $value The field value.
	 * @param int $form_id The ID of the form currently being processed.
	 * @param string $field_id The ID of the field currently being processed.
	 * @param array $entry The entry object currently being processed.
	 *
	 * @return string|array
	 */
	public function zapier_field_value( $value, $form_id, $field_id, $entry ) {
		if ( ! rgblank( $value ) ) {
			$form_meta = RGFormsModel::get_form_meta( $form_id );
			$field     = RGFormsModel::get_field( $form_meta, $field_id );

			if ( is_object( $field ) && $field->type == 'survey' ) {
				switch ( $field->inputType ) {
					case 'likert' :
						if ( is_array( $value ) ) {
							foreach ( $value as $key => &$row ) {
								if ( ! empty( $row ) ) {
									$row = $field->get_column_text( $value, false, $key );
								}
							}
						} else {

							return $field->get_column_text( $value );
						}

						break;

					case 'rank' :
					case 'rating' :
					case 'radio' :
					case 'select' :
						return $field->get_value_export( $entry, $field_id, true );

					case 'checkbox' :
						foreach ( $value as &$choice ) {
							if ( ! empty( $choice ) ) {
								$choice = RGFormsModel::get_choice_text( $field, $choice );
							}
						}
				}
			}
		}

		return $value;
	}


	// # FIELD SETTINGS -------------------------------------------------------------------------------------------------

	/**
	 * Add the gsurvey-field class to the Survey field.
	 *
	 * @param string $classes The CSS classes to be filtered, separated by empty spaces.
	 * @param GF_Field $field The field currently being processed.
	 * @param array $form The form currently being processed.
	 *
	 * @return string
	 */
	public function add_custom_class( $classes, $field, $form ) {
		if ( $field->type == 'survey' ) {
			$classes .= ' gsurvey-survey-field ';
		}

		return $classes;
	}

	/**
	 * Add the tooltips for the Survey field.
	 *
	 * @param array $tooltips An associative array of tooltips where the key is the tooltip name and the value is the tooltip.
	 *
	 * @return array
	 */
	public function add_survey_tooltips( $tooltips ) {
		$tooltips['gsurvey_question']                    = '<h6>' . esc_html__( 'Survey Question', 'gravityformssurvey' ) . '</h6>' . esc_html__( 'Enter the question you would like to ask the user.', 'gravityformssurvey' );
		$tooltips['gsurvey_field_type']                  = '<h6>' . esc_html__( 'Survey Field Type', 'gravityformssurvey' ) . '</h6>' . esc_html__( 'Select the type of field that will be used for the survey.', 'gravityformssurvey' );
		$tooltips['gsurvey_likert_columns']              = '<h6>' . esc_html__( 'Likert Columns', 'gravityformssurvey' ) . '</h6>' . esc_html__( 'Edit the choices for this likert field.', 'gravityformssurvey' );
		$tooltips['gsurvey_likert_enable_multiple_rows'] = '<h6>' . esc_html__( 'Enable Multiple Rows', 'gravityformssurvey' ) . '</h6>' . esc_html__( 'Select to add multiple rows to the likert field.', 'gravityformssurvey' );
		$tooltips['gsurvey_likert_rows']                 = '<h6>' . esc_html__( 'Likert Rows', 'gravityformssurvey' ) . '</h6>' . esc_html__( 'Edit the texts that will appear to the left of each row of choices.', 'gravityformssurvey' );
		$tooltips['gsurvey_likert_enable_scoring']       = '<h6>' . esc_html__( 'Enable Scoring', 'gravityformssurvey' ) . '</h6>' . esc_html__( 'Scoring allows different scores for each column. Aggregate scores are displayed in the results page and can be used in merge tags.', 'gravityformssurvey' );

		return $tooltips;
	}

	/**
	 * Add the custom settings for the Survey fields to the fields general tab.
	 *
	 * @param int $position The position the settings should be located at.
	 * @param int $form_id The ID of the form currently being edited.
	 */
	public function survey_field_settings( $position, $form_id ) {
		if ( $position == 25 ) {
			?>
			<li class="gsurvey-setting-question field_setting">
				<label for="gsurvey-question">
					<?php esc_html_e( 'Survey Question', 'gravityformssurvey' ); ?>
					<?php gform_tooltip( 'gsurvey_question' ); ?>
				</label>
				<textarea id="gsurvey-question" class="fieldwidth-3 fieldheight-2"
						  onkeyup="SetFieldLabel(this.value)"
						  size="35"></textarea>
			</li>
			<li class="gsurvey-setting-field-type field_setting">
				<label for="gsurvey-field-type">
					<?php esc_html_e( 'Survey Field Type', 'gravityformssurvey' ); ?>
					<?php gform_tooltip( 'gsurvey_field_type' ); ?>
				</label>
				<select id="gsurvey-field-type"
						onchange="if(jQuery(this).val() == '') return; jQuery('#field_settings').slideUp(function(){StartChangeSurveyType(jQuery('#gsurvey-field-type').val());});">
					<option value="likert"><?php esc_html_e( 'Likert', 'gravityformssurvey' ); ?></option>
					<option value="rank"><?php esc_html_e( 'Rank', 'gravityformssurvey' ); ?></option>
					<option value="rating"><?php esc_html_e( 'Rating', 'gravityformssurvey' ); ?></option>
					<option value="radio"><?php esc_html_e( 'Radio Buttons', 'gravityformssurvey' ); ?></option>
					<option value="checkbox"><?php esc_html_e( 'Checkboxes', 'gravityformssurvey' ); ?></option>
					<option value="text"><?php esc_html_e( 'Single Line Text', 'gravityformssurvey' ); ?></option>
					<option value="textarea"><?php esc_html_e( 'Paragraph Text', 'gravityformssurvey' ); ?></option>
					<option value="select"><?php esc_html_e( 'Drop Down', 'gravityformssurvey' ); ?></option>
				</select>
			</li>
		<?php
		} elseif ( $position == 1362 ) {
			?>
			<li class="gsurvey-likert-setting-columns field_setting">

				<div style="float:right;">
					<input id="gsurvey-likert-enable-scoring" type="checkbox"
						   onclick="SetFieldProperty('gsurveyLikertEnableScoring', this.checked); jQuery('#gsurvey-likert-columns-container').toggleClass('gsurvey-likert-scoring-enabled');">
					<label class="inline gfield_value_label" for="gsurvey-likert-enable-scoring"><?php esc_html_e( 'Enable Scoring', 'gravityformssurvey' ); ?></label> <?php gform_tooltip( 'gsurvey_likert_enable_scoring' ) ?>
				</div>
				<label for="gsurvey-likert-columns">
					<?php esc_html_e( 'Columns', 'gravityformssurvey' ); ?>
					<?php gform_tooltip( 'gsurvey_likert_columns' ); ?>
				</label>

				<div id="gsurvey-likert-columns-container">
					<ul id="gsurvey-likert-columns">
					</ul>
				</div>
			</li>
			<li class="gsurvey-likert-setting-enable-multiple-rows field_setting">
				<input type="checkbox" id="gsurvey-likert-enable-multiple-rows"
					   onclick="field = GetSelectedField(); var value = jQuery(this).is(':checked'); SetFieldProperty('gsurveyLikertEnableMultipleRows', value); gsurveyLikertUpdateInputs(field); gsurveyLikertUpdatePreview(); jQuery('.gsurvey-likert-setting-rows').toggle('slow');" />
				<label for="gsurvey-likert-enable-multiple-rows" class="inline">
					<?php esc_html_e( 'Enable Multiple Rows', 'gravityformssurvey' ); ?>
					<?php gform_tooltip( 'gsurvey_likert_enable_multiple_rows' ) ?>
				</label>

			</li>
			<li class="gsurvey-likert-setting-rows field_setting">
				<?php esc_html_e( 'Rows', 'gravityformssurvey' ); ?>
				<?php gform_tooltip( 'gsurvey_likert_rows' ) ?>
				<div id="gsurvey-likert-rows-container">
					<ul id="gsurvey-likert-rows"></ul>
				</div>
			</li>
		<?php
		}
	}


	// # CONTACTS INTEGRATION -------------------------------------------------------------------------------------------

	public function add_tab_to_contact_detail( $tabs, $contact_id ) {
		if ( $contact_id > 0 ) {
			$tabs[] = array( 'name' => 'survey', 'label' => esc_html__( 'Survey Entries', 'gravityformssurvey' ) );
		}

		return $tabs;
	}

	public function contacts_tab( $contact_id ) {

		if ( false === empty( $contact_id ) ) :
			$search_criteria['status'] = 'active';
			$search_criteria['field_filters'][] = array(
				'type'  => 'meta',
				'key'   => 'gcontacts_contact_id',
				'value' => $contact_id,
			);
			$form_ids                           = array();
			$forms                              = GFFormsModel::get_forms( true );
			foreach ( $forms as $form ) {
				$form_meta     = GFFormsModel::get_form_meta( $form->id );
				$survey_fields = GFCommon::get_fields_by_type( $form_meta, array( 'survey' ) );
				if ( ! empty( $survey_fields ) ) {
					$form_ids[] = $form->id;
				}
			}

			if ( empty( $form_ids ) ) {
				return;
			}
			$entries                   = GFAPI::get_entries( $form_ids, $search_criteria );

			if ( empty( $entries ) ) :
				esc_html_e( 'This contact has not submitted any survey entries yet.', 'gravityformssurvey' );
			else :
				?>
				<h3><span><?php esc_html_e( 'Survey Entries', 'gravityformssurvey' ) ?></span></h3>
				<div>
					<table id="gcontacts-entry-list" class="widefat">
						<tr class="gcontacts-entries-header">
							<td>
								<?php esc_html_e( 'Entry ID', 'gravityformssurvey' ) ?>
							</td>
							<td>
								<?php esc_html_e( 'Date', 'gravityformssurvey' ) ?>
							</td>
							<td>
								<?php esc_html_e( 'Form', 'gravityformssurvey' ) ?>
							</td>
						</tr>
						<?php


						foreach ( $entries as $entry ) {
							$form_id    = $entry['form_id'];
							$form       = GFFormsModel::get_form_meta( $form_id );
							$form_title = rgar( $form, 'title' );
							$entry_id   = $entry['id'];
							$entry_date = GFCommon::format_date( rgar( $entry, 'date_created' ), false );
							$entry_url  = admin_url( "admin.php?page=gf_entries&view=entry&id={$form_id}&lid={$entry_id}" );

							?>
							<tr>
								<td>
									<a href="<?php echo $entry_url; ?>"><?php echo $entry_id; ?></a>
								</td>
								<td>
									<?php echo $entry_date; ?>
								</td>
								<td>
									<?php echo $form_title; ?>
								</td>


							</tr>
							<?php
						}
						?>
					</table>
				</div>
				<?php
			endif;
		endif;

	}


	// # DEPRECATED -----------------------------------------------------------------------------------------------------

	/**
	 * Returns the Likert column text.
	 *
	 * @deprecated
	 * @param GF_Field_Likert $field The field being processed.
	 * @param string $value The field value.
	 *
	 * @return string
	 */
	public function get_likert_column_text( $field, $value ) {
		_deprecated_function( __FUNCTION__, '3.0', '$field->get_column_text( $value, $entry = false, $field_id = false, $include_row_text = false )' );

		if ( $field->gsurveyLikertEnableMultipleRows ) {
			if ( false === strpos( $value, ':' ) ) {
				return '';
			}
			list( $row_val, $col_val ) = explode( ':', $value, 2 );

			foreach ( $field->gsurveyLikertRows as $row ) {
				if ( $row['value'] == $row_val ) {
					foreach ( $field->choices as $choice ) {
						if ( $choice['value'] == $col_val ) {
							return $choice['text'];
						}
					}
				}
			}
		} else {
			foreach ( $field->choices as $choice ) {
				if ( $choice['value'] == $value ) {
					return $choice['text'];
				}
			}
		}

	}

	/**
	 * Returns the label for the row at the specified index in the gsurveyLikertRows array.
	 *
	 * @param GF_Field_Likert $field The field being processed.
	 * @param integer $index The row index.
	 *
	 * @return string
	 */
	public function get_likert_row_text( $field, $index ) {

		return $field->gsurveyLikertEnableMultipleRows ? $field->gsurveyLikertRows[ $index ]['text'] : '';
	}

	/**
	 * Support importing forms using the old XML format.
	 *
	 * @param array $options Array of options for the XML import.
	 *
	 * @return array
	 */
	public function import_file_options( $options ) {
		$options['gsurveyLikertRow'] = array( 'unserialize_as_array' => true );

		return $options;
	}

} // end class
