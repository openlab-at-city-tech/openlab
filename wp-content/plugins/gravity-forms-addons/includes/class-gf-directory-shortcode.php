<?php
/**
 * Loads the content of [directory] shortcode.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes
 */

class GFDirectory_Shortcode extends GFDirectory {

	/**
	 * Instance of this class.
	 *
	 * @since    4.2
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since      4.2
	 *
	 * @return     void
	 */
	public function __construct() {
		add_shortcode( 'directory', array( $this, 'make_directory' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     4.2
	 *
	 * @return    Object    A single instance of this class.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Include gravity forms files if not found loaded before.
	 *
	 * @since     4.2
	 *
	 */
	public static function include_gf_files() {
		if ( ! class_exists( 'GFEntryDetail' ) ) {
			@require_once( GFCommon::get_base_path() . '/entry_detail.php' );
		}
		if ( ! class_exists( 'GFCommon' ) ) {
			@require_once( GFCommon::get_base_path() . '/common.php' );
		}
		if ( ! class_exists( 'RGFormsModel' ) ) {
			@require_once( GFCommon::get_base_path() . '/forms_model.php' );
		}
		if ( ! class_exists( 'GFEntryList' ) ) {
			require_once( GFCommon::get_base_path() . '/entry_list.php' );
		}
	}

	/**
	 * Render [directory] shortcode content.
	 *
	 * @since     4.2
	 *
	 * @param  array  $atts    Array of attributes passed with the shortcode.
	 * @return string $content HTML content.
	 */
	public static function make_directory( $atts ) {
		global $wpdb, $wp_rewrite, $post, $wpdb, $directory_shown, $kws_gf_scripts, $kws_gf_styles;

		self::include_gf_files();

		//quit if version of wp is not supported
		if ( ! class_exists( 'GFCommon' ) || ! GFCommon::ensure_wp_version() ) {
			return;
		}

		// Already showed edit directory form and there are more than one forms on the page.
		if ( did_action( 'kws_gf_directory_post_after_edit_lead_form' ) ) {
			return;
		}

		ob_start(); // Using ob_start() allows us to use echo instead of $output .=

		foreach ( $atts as $key => $att ) {
			if ( 'false' == strtolower( $att ) ) {
				$atts[ $key ] = false;
			} elseif ( 'true' == strtolower( $att ) ) {
				$atts[ $key ] = true;
			}
		}

		$atts['approved'] = isset( $atts['approved'] ) ? $atts['approved'] : -1;

		if ( ! empty( $atts['lightboxsettings'] ) && is_string( $atts['lightboxsettings'] ) ) {
			$atts['lightboxsettings'] = explode( ',', $atts['lightboxsettings'] );
		}

		$options = GFDirectory::directory_defaults( $atts );

		// Make sure everything is on the same page.
		if ( is_array( $options['lightboxsettings'] ) ) {
			foreach ( $options['lightboxsettings'] as $key => $value ) {
				if ( is_numeric( $key ) ) {
					$options['lightboxsettings'][ "{$value}" ] = $value;
					unset( $options['lightboxsettings'][ "{$key}" ] );
				}
			}
		}

		extract( $options );

		$form_id = $form;
		$form    = GFAPI::get_form( $form_id );

		if ( ! $form ) {
			return;
		}

		$get_fields = wp_unslash( $_GET );

		$sort_field     = empty( $get_fields['sort'] ) ? $sort : $get_fields['sort'];
		$sort_direction = empty( $get_fields['dir'] ) ? $dir : $get_fields['dir'];
		$search_query   = isset( $get_fields['gf_search'] ) ? $get_fields['gf_search'] : null;

		$start_date = ! empty( $get_fields['start_date'] ) ? $get_fields['start_date'] : $start_date;
		$end_date   = ! empty( $get_fields['end_date'] ) ? $get_fields['end_date'] : $end_date;

		$page_index       = empty( $get_fields['pagenum'] ) ? $startpage - 1 : intval( $get_fields['pagenum'] ) - 1;
		$star             = ( isset( $get_fields['star'] ) && is_numeric( $get_fields['star'] ) ) ? intval( $get_fields['star'] ) : null;
		$read             = ( isset( $get_fields['read'] ) && is_numeric( $get_fields['read'] ) ) ? intval( $get_fields['read'] ) : null;
		$first_item_index = $page_index * $page_size;
		$link_params      = array();
		if ( ! empty( $page_index ) ) {
			$link_params['pagenum'] = $page_index;
		}

		$formaction = esc_url_raw(
			remove_query_arg(
				array(
					'gf_search',
					'sort',
					'dir',
					'pagenum',
					'edit',
				),
				add_query_arg( $link_params )
			)
		);
		$tableclass     .= ! empty( $jstable ) ? sprintf( ' tablesorter tablesorter-%s', apply_filters( 'kws_gf_tablesorter_theme', 'blue', $form ) ) : '';
		$title           = $form['title'];
		$sort_field_meta = RGFormsModel::get_field( $form, $sort_field );
		$is_numeric      = ( $sort_field_meta && 'number' === $sort_field_meta->type );

		$columns = self::get_grid_columns( $form_id, true );

		$approvedcolumn        = null;
		$smartapproval         = ! empty( $smartapproval );
		$enable_smart_approval = false;

		// Approved is not enabled, and smart approval is enabled
		if ( - 1 === $approved && $smartapproval ) {
			$enable_smart_approval = true;
			$approved              = true;
		}

		if ( true === $approved ) {
			$approvedcolumn = self::get_approved_column( $form );
		}

		if ( $approved || ( ! empty( $smartapproval ) && -1 === $approved ) && ! empty( $approvedcolumn ) ) {
			$approved = true; // If there is an approved column, turn on approval
		} else {
			$approved = false; // Otherwise, show entries as normal.
		}

		$entrylinkcolumns = self::get_entrylink_column( $form, $entry );
		$adminonlycolumns = self::get_admin_only( $form );

		// Show only a single entry.
		$detail = self::process_lead_detail( true, $entryback, $showadminonly, $adminonlycolumns, $approvedcolumn, $options, $entryonly );

		if ( ! empty( $entry ) && ! empty( $detail ) ) {

			// Once again, checking to make sure this hasn't been shown already with multiple shortcodes on one page.
			if ( ! did_action( 'kws_gf_after_directory' ) ) {
				echo $detail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			if ( ! empty( $entryonly ) ) {
				do_action( 'kws_gf_after_directory', do_action( 'kws_gf_after_directory_form_' . $form_id, $form, compact( 'approved', 'sort_field', 'sort_direction', 'search_query', 'first_item_index', 'page_size', 'star', 'read', 'is_numeric', 'start_date', 'end_date' ) ) );

				$content = ob_get_clean(); // Get the output and clear the buffer

				// If the form is form #2, two filters are applied: `kws_gf_directory_output_2` and `kws_gf_directory_output`
				$content = apply_filters( 'kws_gf_directory_output', apply_filters( 'kws_gf_directory_output_' . $form_id, self::html_display_type_filter( $content, $directoryview ) ) );

				return $content;
			}
		}

		// since 3.5 - remove columns of the fields not allowed to be shown
		$columns = self::remove_hidden_fields( $columns, $adminonlycolumns, $approvedcolumn, false, false, $showadminonly, $form );

		// hook for external selection of columns
		$columns = apply_filters( 'kws_gf_directory_filter_columns', $columns );

		//since 3.5 search criteria
		$show_search_filters = self::get_search_filters( $form );
		$show_search_filters = apply_filters( 'kws_gf_directory_search_filters', $show_search_filters, $form );
		$search_criteria     = array();

		foreach ( $show_search_filters as $key ) {
			if ( '' !== rgget( 'filter_' . $key ) ) {
				$search_criteria['field_filters'][] = array(
					'key' => $key,
					'value' => rgget( 'filter_' . $key ),
				);
			}
		}

		// 2.3 supports $smartapproval out of the box
		if ( $smartapproval && $enable_smart_approval && self::use_gf_23_db() ) {

			$search_criteria['field_filters'][] = array(
				'key'      => 'is_approved',
				'operator' => 'isnot',
				'value'    => '',
			);

			$search_criteria['field_filters']['mode'] = 'all';

		}

		$total_count = 0;

		// Or start to generate the directory.
		$leads = GFDirectory::get_leads( $form_id, $sort_field, $sort_direction, $search_query, $first_item_index, $page_size, $star, $read, $is_numeric, $start_date, $end_date, 'active', $approvedcolumn, $limituser, $search_criteria, $total_count );

		// Allow lightbox to determine whether showadminonly is valid without passing a query string in URL
		if ( true === $entry && ! empty( $lightboxsettings['entry'] ) ) {
			if ( get_site_transient( 'gf_form_' . $form_id . '_post_' . $post->ID . '_showadminonly' ) != $showadminonly ) {
				set_site_transient( 'gf_form_' . $form_id . '_post_' . $post->ID . '_showadminonly', $showadminonly, HOUR_IN_SECONDS );
			}
		} else {
			delete_site_transient( 'gf_form_' . $form_id . '_post_' . $post->ID . '_showadminonly' );
		}

		// Get a list of query args for the pagination links
		if ( ! empty( $search_query ) ) {
			$args['gf_search'] = urlencode( $search_query );
		}
		if ( ! empty( $sort_field ) ) {
			$args['sort'] = $sort_field;
		}
		if ( ! empty( $sort_direction ) ) {
			$args['dir'] = $sort_direction;
		}
		if ( ! empty( $star ) ) {
			$args['star'] = $star;
		}

		if ( $page_size > 0 ) {

			// $leads contains all the entries according to request, since 3.5, to allow multisort.
			if ( apply_filters( 'kws_gf_directory_want_multisort', false ) ) {
				$lead_count = count( $leads );
				$leads      = array_slice( $leads, $first_item_index, $page_size );
			} else {
				$lead_count = $total_count;
			}

			$page_links = array(
				'base'      => esc_url_raw( @add_query_arg( 'pagenum', '%#%' ) ), // get_permalink().'%_%',
				'format'    => '&pagenum=%#%',
				'add_args'  => $args,
				'prev_text' => $prev_text,
				'next_text' => $next_text,
				'total'     => ceil( $lead_count / $page_size ),
				'current'   => $page_index + 1,
				'show_all'  => $pagelinksshowall,
			);
			$page_links = apply_filters( 'kws_gf_results_pagination', $page_links );
			$page_links = paginate_links( $page_links );
		} else {
			// Showing all results.
			$page_links = false;
			$lead_count = count( $leads );
		}

		include_once( GF_DIRECTORY_PATH . 'includes/views/html-gf-directory-shortcode.php' );

		$content = ob_get_contents(); // Get the output.
		ob_end_clean(); // Clear the cache.

		// If the form is form #2, two filters are applied: `kws_gf_directory_output_2` and `kws_gf_directory_output`
		$content = apply_filters( 'kws_gf_directory_output', apply_filters( 'kws_gf_directory_output_' . $form_id, self::html_display_type_filter( $content, $directoryview ) ) );

		return $content; // Return it!
	}

	/**
	 * Get grid columns.
	 *
	 * @since     4.2
	 *
	 * @param  int   $form_id          Gravity form ID.
	 * @param  bool  $input_label_only Input label only or not.
	 * @return array $columns          Array of grid columns.
	 */
	public static function get_grid_columns( $form_id, $input_label_only = false ) {
		$form      = GFFormsModel::get_form_meta( $form_id );
		$field_ids = self::get_grid_column_meta( $form_id );

		if ( ! is_array( $field_ids ) ) {
			$field_ids = array();
			for ( $i = 0, $count = count( $form['fields'] ); $i < $count && $i < 5; $i ++ ) {
				$field = $form['fields'][ $i ];

				if ( $field->displayOnly ) {
					continue;
				}

				if ( isset( $field->inputs ) && is_array( $field->inputs ) ) {
					$field_ids[] = $field->id;
					if ( 'name' === $field->type ) {
						$field_ids[] = $field->id . '.3'; //adding first name.
						$field_ids[] = $field->id . '.6'; //adding last name.
					} else if ( isset( $field->inputs[0] ) ) {
						$field_ids[] = $field->inputs[0]['id']; //getting first input.
					}
				} else {
					$field_ids[] = $field->id;
				}
			}
			//adding default entry meta columns.
			$entry_metas = GFFormsModel::get_entry_meta( $form_id );
			foreach ( $entry_metas as $key => $entry_meta ) {
				if ( rgar( $entry_meta, 'is_default_column' ) ) {
					$field_ids[] = $key;
				}
			}
		}

		$columns    = array();
		$entry_meta = GFFormsModel::get_entry_meta( $form_id );
		foreach ( $field_ids as $field_id ) {

			switch ( $field_id ) {
				case 'id':
					$columns[ $field_id ] = array(
						'label' => 'Entry Id',
						'type'  => 'id',
					);
					break;
				case 'ip':
					$columns[ $field_id ] = array(
						'label' => 'User IP',
						'type'  => 'ip',
					);
					break;
				case 'date_created':
					$columns[ $field_id ] = array(
						'label' => 'Entry Date',
						'type'  => 'date_created',
					);
					break;
				case 'source_url':
					$columns[ $field_id ] = array(
						'label' => 'Source Url',
						'type'  => 'source_url',
					);
					break;
				case 'payment_status':
					$columns[ $field_id ] = array(
						'label' => 'Payment Status',
						'type'  => 'payment_status',
					);
					break;
				case 'transaction_id':
					$columns[ $field_id ] = array(
						'label' => 'Transaction Id',
						'type'  => 'transaction_id',
					);
					break;
				case 'payment_date':
					$columns[ $field_id ] = array(
						'label' => 'Payment Date',
						'type'  => 'payment_date',
					);
					break;
				case 'payment_amount':
					$columns[ $field_id ] = array(
						'label' => 'Payment Amount',
						'type'  => 'payment_amount',
					);
					break;
				case 'created_by':
					$columns[ $field_id ] = array(
						'label' => 'User',
						'type'  => 'created_by',
					);
					break;
				case ( ( is_string( $field_id ) || is_int( $field_id ) ) && array_key_exists( $field_id, $entry_meta ) ):
					$columns[ $field_id ] = array(
						'label' => $entry_meta[ $field_id ]['label'],
						'type'  => $field_id,
					);
					break;
				default:
					$field = GFFormsModel::get_field( $form, $field_id );
					if ( $field ) {
						$columns[ strval( $field_id ) ] = array(
							'label'     => self::get_label( $field, $field_id, $input_label_only ),
							'type'      => rgobj( $field, 'type' ),
							'inputType' => rgobj( $field, 'inputType' ),
						);
					}
			}
		}

		return $columns;
	}

	/**
	 * Get entrylink columns.
	 *
	 * @since     4.2
	 *
	 * @param  array $form    Gravity form ID.
	 * @param  array $entry   Entry.
	 * @return array $columns Array of columns.
	 */
	public static function get_entrylink_column( $form, $entry = false ) {
		if ( ! is_array( $form ) ) {
			return false;
		}

		$columns = empty( $entry ) ? array() : array( 'id' => 'id' );
		foreach ( @$form['fields'] as $key => $col ) {
			if ( ! empty( $col['useAsEntryLink'] ) ) {
				$columns[ $col['id'] ] = $col['useAsEntryLink'];
			}
		}

		return empty( $columns ) ? false : $columns;
	}

}
