<?php

namespace ElementsKit_Lite\Libs\Xs_Migration;

class Migration extends Data_Migration {


	/**
	 *
	 * @param $wpOptionKey
	 * @param $existingOption
	 *
	 * @return array
	 */
	public function convert_from_1_4_7_to_1_4_8( $wpOptionKey, $existingOption ) {

		$log = $existingOption['_log'];

		$log[] = '- This is a blank method for testing.';
		$log[] = '- All functionality is checked and updated.';
		$log[] = '- Updating method execution status to executed.';
		$log[] = '- Method execution is finished at ' . date( 'Y-m-d H:i:s' );

		$fn = $existingOption['_func'];

		$fn[ __FUNCTION__ ] = self::STATUS_METHOD_EXECUTED;

		$existingOption['_func'] = $fn;
		$existingOption['_log']  = $log;

		update_option( $wpOptionKey, $existingOption );

		return array(
			'status' => 'success',
			'log'    => $log,
		);
	}


	/**
	 * todo - for future must put the description of what is migrating in this function and try to do it for single issue per method
	 * This way we will be able to not release some migration but the work is done, so we will be able totest it
	 *
	 * This method is for migrating icon values
	 *
	 * @param $wpOptionKey
	 * @param $existingOption
	 *
	 * @return array
	 */
	public function skip__convert_from_1_5_7_to_1_5_8( $wpOptionKey, $existingOption ) {

		$log = $existingOption['_log'];

		$checkList = array();

		if ( empty( $existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'] ) ) {

			$checkList['retrieve_all_post_ids']                                    = false;
			$checkList['post_meta_data_processed']                                 = false;
			$existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'] = $checkList;

		} else {
			$checkList = $existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'];
		}

		if ( $checkList['retrieve_all_post_ids'] != self::SUB_ROUTINE_STATUS_DONE ) {

			$ids = $this->get_all_post_ids_by_meta_key();

			$checkList['_retrieve_log']['ids_processed'] = '';
			$checkList['_retrieve_log']['ids_retrieved'] = 'yes';
			$checkList['_retrieve_log']['ids']           = $ids;
			$checkList['retrieve_all_post_ids']          = self::SUB_ROUTINE_STATUS_DONE;

			$log[] = '- All meta ids of _elementor_data key is retrieved.';

			$existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'] = $checkList;

			$this->update_subroutine_status( __FUNCTION__, $log, $existingOption, $wpOptionKey );

			return array(
				'status' => 'success',
				'log'    => $log,
			);
		}

		if ( $checkList['post_meta_data_processed'] != self::SUB_ROUTINE_STATUS_DONE ) {

			/**
			 * We have retrieved ids of all metas
			 * now we will process the list
			 * but the list could be empty!
			 *
			 */

			if ( ! empty( $checkList['_retrieve_log']['ids'] ) ) {

				$ids             = $checkList['_retrieve_log']['ids'];
				$max_iteration   = $this->getMaxIteration();
				$count_iteration = 0;
				$tmp             = $checkList['_retrieve_log']['ids_processed'];
				$tmp_arr         = $checkList['_retrieve_log']['processed_log'];

				$log[] = '- Processing retrieved ids';

				while ( ! empty( $ids ) ) {
					$count_iteration++;
					$post_id = array_pop( $ids );

					$log[] = '-- Fetching and correcting the entry of post - ' . $post_id;

					$no_need = $this->fetch_and_correct_meta_value( $post_id );

					$tmp                .= $post_id . ', ';
					$tmp_arr[ $post_id ] = $no_need;

					if ( $count_iteration >= $max_iteration ) {
						break;
					}
				}

				$log[] = '- End of current iteration';

				$checkList['_retrieve_log']['ids_processed'] = $tmp;
				$checkList['_retrieve_log']['processed_log'] = $tmp_arr;
				$checkList['_retrieve_log']['ids']           = $ids;

				$existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'] = $checkList;

				$this->update_subroutine_status( __FUNCTION__, $log, $existingOption, $wpOptionKey );

				return array(
					'status' => 'success',
					'log'    => $log,
				);

			}

			/**
			 * the retrieved list is either empty or it is already processed
			 * we will conclude this subroutine here
			 *
			 */

			$checkList['post_meta_data_processed'] = self::SUB_ROUTINE_STATUS_DONE;

			$log[] = '-- Subroutine is finished at ' . date( 'Y-m-d H:i:s' );

			$existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'] = $checkList;

			$this->update_subroutine_status( __FUNCTION__, $log, $existingOption, $wpOptionKey );

			return array(
				'status' => 'success',
				'log'    => $log,
			);
		}

		$log[] = '- All subroutine is processed.';
		$log[] = '- Updating method execution status to executed.';
		$log[] = '- Method execution is finished at ' . date( 'Y-m-d H:i:s' );

		$fn = $existingOption['_func'];

		$fn[ __FUNCTION__ ] = self::STATUS_METHOD_EXECUTED;

		$existingOption['_func'] = $fn;
		$existingOption['_log']  = $log;

		update_option( $wpOptionKey, $existingOption );

		return array(
			'status' => 'success',
			'log'    => $log,
		);
	}


	/**
	 * Language translation migrations
	 *
	 * @since 1.5.9
	 *
	 * @param $wpOptionKey
	 * @param $existingOption
	 *
	 * @return array
	 */
	public function convert_from_1_5_8_to_1_5_9( $wpOptionKey, $existingOption ) {

		/**
		 * Two changes
		 * 1. for loco plugin - duplicate language file : .po, .mo
		 * 2. for wpml plugin - change the database step by step
		 *
		 *
		 * First get the progress log
		 * and we are sure the method execution status is running here...
		 *
		 */
		$log = $existingOption['_log'];

		$checkList = array();

		if ( empty( $existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'] ) ) {

			$checkList['duplicate_icl_translation']                                = false;
			$checkList['retrieve_icl_string_id']                                   = false;
			$existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'] = $checkList;

		} else {
			$checkList = $existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'];
		}

		if ( $checkList['retrieve_icl_string_id'] != self::SUB_ROUTINE_STATUS_DONE ) {

			$ids = $this->get_all_ids();

			$checkList['_icl_log']['ids_retrieved'] = 'yes';
			$checkList['_icl_log']['ids']           = $ids;
			$checkList['retrieve_icl_string_id']    = self::SUB_ROUTINE_STATUS_DONE;

			$log[] = '- All translated strings id is retrieved.';

			$existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'] = $checkList;

			$this->update_subroutine_status( __FUNCTION__, $log, $existingOption, $wpOptionKey );

			return array(
				'status' => 'success',
				'log'    => $log,
			);
		}

		if ( $checkList['duplicate_icl_translation'] != self::SUB_ROUTINE_STATUS_DONE ) {

			/**
			 * We have retrieved ids of all translated string in previous sub routine
			 * now we will process the list
			 * but the list could be empty!
			 *
			 */

			if ( ! empty( $checkList['_icl_log']['ids'] ) ) {

				$ids             = $checkList['_icl_log']['ids'];
				$max_iteration   = $this->getMaxIteration();
				$count_iteration = 0;
				$tmp             = $checkList['_icl_log']['ids_processed'];
				$tmp_arr         = $checkList['_icl_log']['processed_log'];

				$log[] = '- Processing retrieved ids';

				while ( ! empty( $ids ) ) {
					$count_iteration++;
					$dup = array_pop( $ids );

					$log[] = '-- Fetching and duplicating the entry of id - ' . $dup;

					$n_id = $this->fetch_and_duplicate( $dup );

					$tmp            .= $dup . ', ';
					$tmp_arr[ $dup ] = $n_id;

					if ( $count_iteration >= $max_iteration ) {
						break;
					}
				}

				$log[] = '- End of current iteration';

				$checkList['_icl_log']['ids_processed'] = $tmp;
				$checkList['_icl_log']['processed_log'] = $tmp_arr;
				$checkList['_icl_log']['ids']           = $ids;

				$existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'] = $checkList;

				$this->update_subroutine_status( __FUNCTION__, $log, $existingOption, $wpOptionKey );

				return array(
					'status' => 'success',
					'log'    => $log,
				);

			}

			/**
			 * the retrieved list is either empty or it is already processed
			 * we will conclude this subroutine here
			 *
			 */

			$checkList['duplicate_icl_translation'] = self::SUB_ROUTINE_STATUS_DONE;

			$log[] = '-- Subroutine is finished at ' . date( 'Y-m-d H:i:s' );

			$existingOption['exec_plan'][ __FUNCTION__ ]['progress']['check_list'] = $checkList;

			$this->update_subroutine_status( __FUNCTION__, $log, $existingOption, $wpOptionKey );

			return array(
				'status' => 'success',
				'log'    => $log,
			);
		}

		$log[] = '- All subroutine is processed.';
		$log[] = '- Updating method execution status to executed.';
		$log[] = '- Method execution is finished at ' . date( 'Y-m-d H:i:s' );

		$fn = $existingOption['_func'];

		$fn[ __FUNCTION__ ] = self::STATUS_METHOD_EXECUTED;

		$existingOption['_func'] = $fn;
		$existingOption['_log']  = $log;

		update_option( $wpOptionKey, $existingOption );

		return array(
			'status' => 'success',
			'log'    => $log,
		);
	}


	/**
	 *
	 * @since 1.5.9
	 *
	 * @param $id
	 *
	 * @return int|string
	 */
	private function fetch_and_duplicate( $id ) {

		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->prefix . 'icl_strings` AS ics WHERE `id`= %d',  intval( $id ) ), ARRAY_A );

		$str_id = '';

		if ( ! empty( $row ) ) {

			unset( $row['id'] );

			$row['context'] = $this->getNewTextDomain();

			$md5 = md5( $this->getNewTextDomain() . $row['name'] . $row['gettext_context'] );

			$row['domain_name_context_md5'] = $md5;

			$wpdb->insert( $wpdb->prefix . 'icl_strings', $row );

			$str_id = $wpdb->insert_id;

			if ( empty( $str_id ) ) {
				return 0;
			}

			$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->prefix . 'icl_string_translations` AS ics WHERE `string_id`= %d',  intval( $id ) ), ARRAY_A );

			foreach ( $rows as $entry ) {

				unset( $entry['id'] );

				$entry['string_id'] = $str_id;

				$wpdb->insert( $wpdb->prefix . 'icl_string_translations', $entry );
			}
		}

		return $str_id;
	}


	/**
	 *
	 * @since 1.5.9
	 *
	 * @param $func
	 * @param $log
	 * @param $existingOption
	 * @param $wpOptionKey
	 *
	 * @return array
	 */
	private function update_subroutine_status( $func, $log, $existingOption, $wpOptionKey ) {

		$log[] = '- Entering into paused phase.';

		$fn = $existingOption['_func'];

		$fn[ $func ] = self::STATUS_METHOD_PAUSED;

		$existingOption['_func'] = $fn;
		$existingOption['_log']  = $log;

		update_option( $wpOptionKey, $existingOption );

		return $log;
	}


	/**
	 *
	 *
	 * @since 1.5.9
	 *
	 * @param string $context
	 *
	 * @return array
	 */
	private function get_all_ids( $context = 'elementskit-lite' ) {

		global $wpdb;

		$ret = array();
		$tbl = $wpdb->prefix . 'icl_string_translations';

		/**
		 * Lets check first if the user has has this plugin installed!
		 * by checking if table exists
		 */

		if ( $wpdb->get_var( $wpdb->prepare(  "SHOW TABLES LIKE %s", $tbl  ) ) == $tbl ) {

			$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT ict.id, ict.string_id, ict.language, ict.status, ics.context FROM `' . $wpdb->prefix . 'icl_string_translations` AS ict LEFT JOIN `' . $wpdb->prefix . 'icl_strings` AS ics ON ict.string_id = ics.id WHERE ics.context = %s', $context  ) );

			foreach ( $rows as $row ) {

				$ret[ $row->string_id ] = $row->string_id;
			}
		}

		return $ret;
	}


	/**
	 *
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	private function get_all_post_ids_by_meta_key( $key = '_elementor_data' ) {

		global $wpdb;

		$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT meta_id, meta_key, post_id FROM `' . $wpdb->prefix . 'postmeta` WHERE meta_key = %s',  $key) );
		$ret  = array();

		foreach ( $rows as $row ) {

			$ret[ $row->post_id ] = $row->post_id;
		}

		return $ret;
	}


	private function get_post_meta_by_post_id( $post_id, $key = '_elementor_data' ) {

		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->prefix . 'postmeta` WHERE post_id = %d AND meta_key = %s', intval( $post_id ), $key) );

		return $row;
	}


	private function update_post_meta_by_meta_id( $meta_id, $value ) {

		global $wpdb;

		$tbl = $wpdb->prefix . 'postmeta';

		//$qry = 'UPDATE `'.$tbl.'` SET `meta_value` = \''.$value.'\'  WHERE `meta_id`=\''.intval($meta_id).'\';';

		return $wpdb->update( $tbl, array( 'meta_value' => $value ), array( 'meta_id' => intval( $meta_id ) ), array( '%s' ), array( '%d' ) );
	}


	/**
	 * Icon value replacing from meta value
	 *
	 *
	 * @param $post_id
	 * @param string $meta_key
	 *
	 * @return mixed
	 */
	private function fetch_and_correct_meta_value( $post_id, $meta_key = '_elementor_data' ) {

		$row = $this->get_post_meta_by_post_id( $post_id, $meta_key );

		$check_arr   = $this->get_original_string_array();
		$replace_arr = $this->get_replace_string_array();

		$modified = str_replace( $check_arr, $replace_arr, $row->meta_value );

		return $this->update_post_meta_by_meta_id( $row->meta_id, $modified );

		//get_post_meta($post_id, $meta_key, true);
		//update_post_meta($post_id, $meta_key, $modified);
	}


	private function get_original_string_array() {

		$original = array(
			'{"value":"icon icon-home","library":"ekiticons"}',
			'{"value":"icon icon-apartment1","library":"ekiticons"}',
			'{"value":"icon icon-pencil","library":"ekiticons"}',
			'{"value":"icon icon-magic-wand","library":"ekiticons"}',
			'{"value":"icon icon-drop","library":"ekiticons"}',
			'{"value":"icon icon-lighter","library":"ekiticons"}',
			'{"value":"icon icon-poop","library":"ekiticons"}',
			'{"value":"icon icon-sun","library":"ekiticons"}',
			'{"value":"icon icon-moon","library":"ekiticons"}',
			'{"value":"icon icon-cloud1","library":"ekiticons"}',
			'{"value":"icon icon-cloud-upload","library":"ekiticons"}',
			'{"value":"icon icon-cloud-download","library":"ekiticons"}',
			'{"value":"icon icon-cloud-sync","library":"ekiticons"}',
			'{"value":"icon icon-cloud-check","library":"ekiticons"}',
			'{"value":"icon icon-database1","library":"ekiticons"}',
			'{"value":"icon icon-lock","library":"ekiticons"}',
			'{"value":"icon icon-cog","library":"ekiticons"}',
			'{"value":"icon icon-trash","library":"ekiticons"}',
			'{"value":"icon icon-dice","library":"ekiticons"}',
			'{"value":"icon icon-heart1","library":"ekiticons"}',
			'{"value":"icon icon-star1","library":"ekiticons"}',
			'{"value":"icon icon-star-half","library":"ekiticons"}',
			'{"value":"icon icon-star-empty","library":"ekiticons"}',
			'{"value":"icon icon-flag","library":"ekiticons"}',
			'{"value":"icon icon-envelope1","library":"ekiticons"}',
			'{"value":"icon icon-paperclip","library":"ekiticons"}',
			'{"value":"icon icon-inbox","library":"ekiticons"}',
			'{"value":"icon icon-eye","library":"ekiticons"}',
			'{"value":"icon icon-printer","library":"ekiticons"}',
			'{"value":"icon icon-file-empty","library":"ekiticons"}',
			'{"value":"icon icon-file-add","library":"ekiticons"}',
			'{"value":"icon icon-enter","library":"ekiticons"}',
			'{"value":"icon icon-exit","library":"ekiticons"}',
			'{"value":"icon icon-graduation-hat","library":"ekiticons"}',
			'{"value":"icon icon-license","library":"ekiticons"}',
			'{"value":"icon icon-music-note","library":"ekiticons"}',
			'{"value":"icon icon-film-play","library":"ekiticons"}',
			'{"value":"icon icon-camera-video","library":"ekiticons"}',
			'{"value":"icon icon-camera","library":"ekiticons"}',
			'{"value":"icon icon-picture","library":"ekiticons"}',
			'{"value":"icon icon-book","library":"ekiticons"}',
			'{"value":"icon icon-bookmark","library":"ekiticons"}',
			'{"value":"icon icon-user","library":"ekiticons"}',
			'{"value":"icon icon-users","library":"ekiticons"}',
			'{"value":"icon icon-shirt","library":"ekiticons"}',
			'{"value":"icon icon-store","library":"ekiticons"}',
			'{"value":"icon icon-cart2","library":"ekiticons"}',
			'{"value":"icon icon-tag","library":"ekiticons"}',
			'{"value":"icon icon-phone-handset","library":"ekiticons"}',
			'{"value":"icon icon-phone","library":"ekiticons"}',
			'{"value":"icon icon-pushpin","library":"ekiticons"}',
			'{"value":"icon icon-map-marker","library":"ekiticons"}',
			'{"value":"icon icon-map","library":"ekiticons"}',
			'{"value":"icon icon-location","library":"ekiticons"}',
			'{"value":"icon icon-calendar-full","library":"ekiticons"}',
			'{"value":"icon icon-keyboard","library":"ekiticons"}',
			'{"value":"icon icon-spell-check","library":"ekiticons"}',
			'{"value":"icon icon-screen","library":"ekiticons"}',
			'{"value":"icon icon-smartphone","library":"ekiticons"}',
			'{"value":"icon icon-tablet","library":"ekiticons"}',
			'{"value":"icon icon-laptop","library":"ekiticons"}',
			'{"value":"icon icon-laptop-phone","library":"ekiticons"}',
			'{"value":"icon icon-power-switch","library":"ekiticons"}',
			'{"value":"icon icon-bubble","library":"ekiticons"}',
			'{"value":"icon icon-heart-pulse","library":"ekiticons"}',
			'{"value":"icon icon-construction","library":"ekiticons"}',
			'{"value":"icon icon-pie-chart","library":"ekiticons"}',
			'{"value":"icon icon-chart-bars","library":"ekiticons"}',
			'{"value":"icon icon-gift1","library":"ekiticons"}',
			'{"value":"icon icon-diamond1","library":"ekiticons"}',
			'{"value":"icon icon-dinner","library":"ekiticons"}',
			'{"value":"icon icon-coffee-cup","library":"ekiticons"}',
			'{"value":"icon icon-leaf","library":"ekiticons"}',
			'{"value":"icon icon-paw","library":"ekiticons"}',
			'{"value":"icon icon-rocket","library":"ekiticons"}',
			'{"value":"icon icon-briefcase","library":"ekiticons"}',
			'{"value":"icon icon-bus","library":"ekiticons"}',
			'{"value":"icon icon-car1","library":"ekiticons"}',
			'{"value":"icon icon-train","library":"ekiticons"}',
			'{"value":"icon icon-bicycle","library":"ekiticons"}',
			'{"value":"icon icon-wheelchair","library":"ekiticons"}',
			'{"value":"icon icon-select","library":"ekiticons"}',
			'{"value":"icon icon-earth","library":"ekiticons"}',
			'{"value":"icon icon-smile","library":"ekiticons"}',
			'{"value":"icon icon-sad","library":"ekiticons"}',
			'{"value":"icon icon-neutral","library":"ekiticons"}',
			'{"value":"icon icon-mustache","library":"ekiticons"}',
			'{"value":"icon icon-alarm","library":"ekiticons"}',
			'{"value":"icon icon-bullhorn","library":"ekiticons"}',
			'{"value":"icon icon-volume-high","library":"ekiticons"}',
			'{"value":"icon icon-volume-medium","library":"ekiticons"}',
			'{"value":"icon icon-volume-low","library":"ekiticons"}',
			'{"value":"icon icon-volume","library":"ekiticons"}',
			'{"value":"icon icon-mic","library":"ekiticons"}',
			'{"value":"icon icon-hourglass","library":"ekiticons"}',
			'{"value":"icon icon-undo","library":"ekiticons"}',
			'{"value":"icon icon-redo","library":"ekiticons"}',
			'{"value":"icon icon-sync","library":"ekiticons"}',
			'{"value":"icon icon-history","library":"ekiticons"}',
			'{"value":"icon icon-clock1","library":"ekiticons"}',
			'{"value":"icon icon-download","library":"ekiticons"}',
			'{"value":"icon icon-upload","library":"ekiticons"}',
			'{"value":"icon icon-enter-down","library":"ekiticons"}',
			'{"value":"icon icon-exit-up","library":"ekiticons"}',
			'{"value":"icon icon-bug","library":"ekiticons"}',
			'{"value":"icon icon-code","library":"ekiticons"}',
			'{"value":"icon icon-link","library":"ekiticons"}',
			'{"value":"icon icon-unlink","library":"ekiticons"}',
			'{"value":"icon icon-thumbs-up","library":"ekiticons"}',
			'{"value":"icon icon-thumbs-down","library":"ekiticons"}',
			'{"value":"icon icon-magnifier","library":"ekiticons"}',
			'{"value":"icon icon-cross","library":"ekiticons"}',
			'{"value":"icon icon-chevron-up","library":"ekiticons"}',
			'{"value":"icon icon-chevron-down","library":"ekiticons"}',
			'{"value":"icon icon-chevron-left","library":"ekiticons"}',
			'{"value":"icon icon-chevron-right","library":"ekiticons"}',
			'{"value":"icon icon-arrow-up","library":"ekiticons"}',
			'{"value":"icon icon-arrow-down","library":"ekiticons"}',
			'{"value":"icon icon-arrow-left","library":"ekiticons"}',
			'{"value":"icon icon-arrow-right","library":"ekiticons"}',
			'{"value":"icon icon-right-arrow","library":"ekiticons"}',
			'{"value":"icon icon-left-arrow","library":"ekiticons"}',
			'{"value":"icon icon-download-arrow","library":"ekiticons"}',
			'{"value":"icon icon-up-arrow","library":"ekiticons"}',
			'{"value":"icon icon-arrows","library":"ekiticons"}',
			'{"value":"icon icon-double-angle-pointing-to-right","library":"ekiticons"}',
			'{"value":"icon icon-double-left-chevron","library":"ekiticons"}',
			'{"value":"icon icon-left-arrow2","library":"ekiticons"}',
			'{"value":"icon icon-right-arrow2","library":"ekiticons"}',
			'{"value":"icon icon-warning","library":"ekiticons"}',
			'{"value":"icon icon-down-arrow1","library":"ekiticons"}',
			'{"value":"icon icon-up-arrow1","library":"ekiticons"}',
			'{"value":"icon icon-right-arrow1","library":"ekiticons"}',
			'{"value":"icon icon-left-arrows","library":"ekiticons"}',
			'{"value":"icon icon-question-circle","library":"ekiticons"}',
			'{"value":"icon icon-menu-circle","library":"ekiticons"}',
			'{"value":"icon icon-checkmark-circle","library":"ekiticons"}',
			'{"value":"icon icon-cross-circle","library":"ekiticons"}',
			'{"value":"icon icon-plus-circle","library":"ekiticons"}',
			'{"value":"icon icon-move","library":"ekiticons"}',
			'{"value":"icon icon-circle-minus","library":"ekiticons"}',
			'{"value":"icon icon-arrow-up-circle","library":"ekiticons"}',
			'{"value":"icon icon-arrow-down-circle","library":"ekiticons"}',
			'{"value":"icon icon-arrow-left-circle","library":"ekiticons"}',
			'{"value":"icon icon-arrow-right-circle","library":"ekiticons"}',
			'{"value":"icon icon-chevron-up-circle","library":"ekiticons"}',
			'{"value":"icon icon-chevron-down-circle","library":"ekiticons"}',
			'{"value":"icon icon-chevron-left-circle","library":"ekiticons"}',
			'{"value":"icon icon-chevron-right-circle","library":"ekiticons"}',
			'{"value":"icon icon-crop","library":"ekiticons"}',
			'{"value":"icon icon-frame-expand","library":"ekiticons"}',
			'{"value":"icon icon-frame-contract","library":"ekiticons"}',
			'{"value":"icon icon-layers","library":"ekiticons"}',
			'{"value":"icon icon-funnel","library":"ekiticons"}',
			'{"value":"icon icon-text-format","library":"ekiticons"}',
			'{"value":"icon icon-text-size","library":"ekiticons"}',
			'{"value":"icon icon-bold","library":"ekiticons"}',
			'{"value":"icon icon-italic","library":"ekiticons"}',
			'{"value":"icon icon-underline","library":"ekiticons"}',
			'{"value":"icon icon-strikethrough","library":"ekiticons"}',
			'{"value":"icon icon-highlight","library":"ekiticons"}',
			'{"value":"icon icon-text-align-left","library":"ekiticons"}',
			'{"value":"icon icon-text-align-center","library":"ekiticons"}',
			'{"value":"icon icon-text-align-right","library":"ekiticons"}',
			'{"value":"icon icon-text-align-justify","library":"ekiticons"}',
			'{"value":"icon icon-line-spacing","library":"ekiticons"}',
			'{"value":"icon icon-indent-increase","library":"ekiticons"}',
			'{"value":"icon icon-indent-decrease","library":"ekiticons"}',
			'{"value":"icon icon-page-break","library":"ekiticons"}',
			'{"value":"icon icon-hand","library":"ekiticons"}',
			'{"value":"icon icon-pointer-up","library":"ekiticons"}',
			'{"value":"icon icon-pointer-right","library":"ekiticons"}',
			'{"value":"icon icon-pointer-down","library":"ekiticons"}',
			'{"value":"icon icon-pointer-left","library":"ekiticons"}',
			'{"value":"icon icon-burger","library":"ekiticons"}',
			'{"value":"icon icon-cakes","library":"ekiticons"}',
			'{"value":"icon icon-cheese","library":"ekiticons"}',
			'{"value":"icon icon-drink-glass","library":"ekiticons"}',
			'{"value":"icon icon-pizza","library":"ekiticons"}',
			'{"value":"icon icon-vplay","library":"ekiticons"}',
			'{"value":"icon icon-newsletter","library":"ekiticons"}',
			'{"value":"icon icon-coins-2","library":"ekiticons"}',
			'{"value":"icon icon-commerce-2","library":"ekiticons"}',
			'{"value":"icon icon-monitor","library":"ekiticons"}',
			'{"value":"icon icon-business","library":"ekiticons"}',
			'{"value":"icon icon-graphic-2","library":"ekiticons"}',
			'{"value":"icon icon-commerce-1","library":"ekiticons"}',
			'{"value":"icon icon-hammer","library":"ekiticons"}',
			'{"value":"icon icon-justice-1","library":"ekiticons"}',
			'{"value":"icon icon-line","library":"ekiticons"}',
			'{"value":"icon icon-money-3","library":"ekiticons"}',
			'{"value":"icon icon-commerce","library":"ekiticons"}',
			'{"value":"icon icon-agenda","library":"ekiticons"}',
			'{"value":"icon icon-justice","library":"ekiticons"}',
			'{"value":"icon icon-technology","library":"ekiticons"}',
			'{"value":"icon icon-coins-1","library":"ekiticons"}',
			'{"value":"icon icon-bank","library":"ekiticons"}',
			'{"value":"icon icon-calculator","library":"ekiticons"}',
			'{"value":"icon icon-soundcloud","library":"ekiticons"}',
			'{"value":"icon icon-chart2","library":"ekiticons"}',
			'{"value":"icon icon-checked","library":"ekiticons"}',
			'{"value":"icon icon-clock11","library":"ekiticons"}',
			'{"value":"icon icon-comment2","library":"ekiticons"}',
			'{"value":"icon icon-comments","library":"ekiticons"}',
			'{"value":"icon icon-consult","library":"ekiticons"}',
			'{"value":"icon icon-consut2","library":"ekiticons"}',
			'{"value":"icon icon-deal","library":"ekiticons"}',
			'{"value":"icon icon-envelope11","library":"ekiticons"}',
			'{"value":"icon icon-folder","library":"ekiticons"}',
			'{"value":"icon icon-folder2","library":"ekiticons"}',
			'{"value":"icon icon-invest","library":"ekiticons"}',
			'{"value":"icon icon-loan","library":"ekiticons"}',
			'{"value":"icon icon-menu1","library":"ekiticons"}',
			'{"value":"icon icon-list1","library":"ekiticons"}',
			'{"value":"icon icon-map-marker1","library":"ekiticons"}',
			'{"value":"icon icon-mutual-fund","library":"ekiticons"}',
			'{"value":"icon icon-google-plus","library":"ekiticons"}',
			'{"value":"icon icon-phone1","library":"ekiticons"}',
			'{"value":"icon icon-pie-chart1","library":"ekiticons"}',
			'{"value":"icon icon-play","library":"ekiticons"}',
			'{"value":"icon icon-savings","library":"ekiticons"}',
			'{"value":"icon icon-search2","library":"ekiticons"}',
			'{"value":"icon icon-tag1","library":"ekiticons"}',
			'{"value":"icon icon-tags","library":"ekiticons"}',
			'{"value":"icon icon-instagram1","library":"ekiticons"}',
			'{"value":"icon icon-quote","library":"ekiticons"}',
			'{"value":"icon icon-arrow-point-to-down","library":"ekiticons"}',
			'{"value":"icon icon-play-button","library":"ekiticons"}',
			'{"value":"icon icon-minus","library":"ekiticons"}',
			'{"value":"icon icon-plus","library":"ekiticons"}',
			'{"value":"icon icon-tick","library":"ekiticons"}',
			'{"value":"icon icon-check","library":"ekiticons"}',
			'{"value":"icon icon-edit","library":"ekiticons"}',
			'{"value":"icon icon-reply","library":"ekiticons"}',
			'{"value":"icon icon-cogwheel-outline","library":"ekiticons"}',
			'{"value":"icon icon-abacus","library":"ekiticons"}',
			'{"value":"icon icon-abacus1","library":"ekiticons"}',
			'{"value":"icon icon-agenda1","library":"ekiticons"}',
			'{"value":"icon icon-shopping-basket","library":"ekiticons"}',
			'{"value":"icon icon-users1","library":"ekiticons"}',
			'{"value":"icon icon-man","library":"ekiticons"}',
			'{"value":"icon icon-support1","library":"ekiticons"}',
			'{"value":"icon icon-favorites","library":"ekiticons"}',
			'{"value":"icon icon-calendar","library":"ekiticons"}',
			'{"value":"icon icon-paper-plane","library":"ekiticons"}',
			'{"value":"icon icon-placeholder","library":"ekiticons"}',
			'{"value":"icon icon-phone-call","library":"ekiticons"}',
			'{"value":"icon icon-contact","library":"ekiticons"}',
			'{"value":"icon icon-email","library":"ekiticons"}',
			'{"value":"icon icon-internet","library":"ekiticons"}',
			'{"value":"icon icon-quote1","library":"ekiticons"}',
			'{"value":"icon icon-medical","library":"ekiticons"}',
			'{"value":"icon icon-eye1","library":"ekiticons"}',
			'{"value":"icon icon-full-screen","library":"ekiticons"}',
			'{"value":"icon icon-tools","library":"ekiticons"}',
			'{"value":"icon icon-pie-chart2","library":"ekiticons"}',
			'{"value":"icon icon-diamond11","library":"ekiticons"}',
			'{"value":"icon icon-valentines-heart","library":"ekiticons"}',
			'{"value":"icon icon-like","library":"ekiticons"}',
			'{"value":"icon icon-team","library":"ekiticons"}',
			'{"value":"icon icon-tshirt","library":"ekiticons"}',
			'{"value":"icon icon-cancel","library":"ekiticons"}',
			'{"value":"icon icon-drink","library":"ekiticons"}',
			'{"value":"icon icon-home1","library":"ekiticons"}',
			'{"value":"icon icon-music","library":"ekiticons"}',
			'{"value":"icon icon-rich","library":"ekiticons"}',
			'{"value":"icon icon-brush","library":"ekiticons"}',
			'{"value":"icon icon-opposite-way","library":"ekiticons"}',
			'{"value":"icon icon-cloud-computing1","library":"ekiticons"}',
			'{"value":"icon icon-technology-1","library":"ekiticons"}',
			'{"value":"icon icon-rotate","library":"ekiticons"}',
			'{"value":"icon icon-medical1","library":"ekiticons"}',
			'{"value":"icon icon-flash-1","library":"ekiticons"}',
			'{"value":"icon icon-flash","library":"ekiticons"}',
			'{"value":"icon icon-uturn","library":"ekiticons"}',
			'{"value":"icon icon-down-arrow","library":"ekiticons"}',
			'{"value":"icon icon-hours-support","library":"ekiticons"}',
			'{"value":"icon icon-bag","library":"ekiticons"}',
			'{"value":"icon icon-photo-camera","library":"ekiticons"}',
			'{"value":"icon icon-school","library":"ekiticons"}',
			'{"value":"icon icon-settings","library":"ekiticons"}',
			'{"value":"icon icon-smartphone1","library":"ekiticons"}',
			'{"value":"icon icon-technology-11","library":"ekiticons"}',
			'{"value":"icon icon-tool","library":"ekiticons"}',
			'{"value":"icon icon-business1","library":"ekiticons"}',
			'{"value":"icon icon-shuffle-arrow","library":"ekiticons"}',
			'{"value":"icon icon-van-1","library":"ekiticons"}',
			'{"value":"icon icon-van","library":"ekiticons"}',
			'{"value":"icon icon-vegetables","library":"ekiticons"}',
			'{"value":"icon icon-women","library":"ekiticons"}',
			'{"value":"icon icon-vintage","library":"ekiticons"}',
			'{"value":"icon icon-team-1","library":"ekiticons"}',
			'{"value":"icon icon-team1","library":"ekiticons"}',
			'{"value":"icon icon-apple","library":"ekiticons"}',
			'{"value":"icon icon-watch","library":"ekiticons"}',
			'{"value":"icon icon-cogwheel","library":"ekiticons"}',
			'{"value":"icon icon-light-bulb","library":"ekiticons"}',
			'{"value":"icon icon-light-bulb-1","library":"ekiticons"}',
			'{"value":"icon icon-heart-shape-outline","library":"ekiticons"}',
			'{"value":"icon icon-online-shopping-cart","library":"ekiticons"}',
			'{"value":"icon icon-shopping-cart1","library":"ekiticons"}',
			'{"value":"icon icon-star2","library":"ekiticons"}',
			'{"value":"icon icon-star-1","library":"ekiticons"}',
			'{"value":"icon icon-favorite1","library":"ekiticons"}',
			'{"value":"icon icon-agenda2","library":"ekiticons"}',
			'{"value":"icon icon-agenda-1","library":"ekiticons"}',
			'{"value":"icon icon-alarm-clock","library":"ekiticons"}',
			'{"value":"icon icon-alarm-clock1","library":"ekiticons"}',
			'{"value":"icon icon-atomic","library":"ekiticons"}',
			'{"value":"icon icon-auction","library":"ekiticons"}',
			'{"value":"icon icon-balance","library":"ekiticons"}',
			'{"value":"icon icon-balance1","library":"ekiticons"}',
			'{"value":"icon icon-bank1","library":"ekiticons"}',
			'{"value":"icon icon-bar-chart","library":"ekiticons"}',
			'{"value":"icon icon-barrier","library":"ekiticons"}',
			'{"value":"icon icon-battery","library":"ekiticons"}',
			'{"value":"icon icon-battery-1","library":"ekiticons"}',
			'{"value":"icon icon-bell","library":"ekiticons"}',
			'{"value":"icon icon-bluetooth","library":"ekiticons"}',
			'{"value":"icon icon-book1","library":"ekiticons"}',
			'{"value":"icon icon-briefcase1","library":"ekiticons"}',
			'{"value":"icon icon-briefcase-1","library":"ekiticons"}',
			'{"value":"icon icon-briefcase-2","library":"ekiticons"}',
			'{"value":"icon icon-calculator1","library":"ekiticons"}',
			'{"value":"icon icon-calculator2","library":"ekiticons"}',
			'{"value":"icon icon-calculator-1","library":"ekiticons"}',
			'{"value":"icon icon-calendar1","library":"ekiticons"}',
			'{"value":"icon icon-calendar2","library":"ekiticons"}',
			'{"value":"icon icon-calendar-1","library":"ekiticons"}',
			'{"value":"icon icon-calendar-page-empty","library":"ekiticons"}',
			'{"value":"icon icon-calendar3","library":"ekiticons"}',
			'{"value":"icon icon-car11","library":"ekiticons"}',
			'{"value":"icon icon-carrier","library":"ekiticons"}',
			'{"value":"icon icon-cash","library":"ekiticons"}',
			'{"value":"icon icon-chat","library":"ekiticons"}',
			'{"value":"icon icon-chat-1","library":"ekiticons"}',
			'{"value":"icon icon-checked1","library":"ekiticons"}',
			'{"value":"icon icon-clip","library":"ekiticons"}',
			'{"value":"icon icon-clip1","library":"ekiticons"}',
			'{"value":"icon icon-clipboard1","library":"ekiticons"}',
			'{"value":"icon icon-clipboard11","library":"ekiticons"}',
			'{"value":"icon icon-clock2","library":"ekiticons"}',
			'{"value":"icon icon-clock-1","library":"ekiticons"}',
			'{"value":"icon icon-cloud11","library":"ekiticons"}',
			'{"value":"icon icon-cloud-computing11","library":"ekiticons"}',
			'{"value":"icon icon-cloud-computing-1","library":"ekiticons"}',
			'{"value":"icon icon-cogwheel1","library":"ekiticons"}',
			'{"value":"icon icon-coins1","library":"ekiticons"}',
			'{"value":"icon icon-compass","library":"ekiticons"}',
			'{"value":"icon icon-contract","library":"ekiticons"}',
			'{"value":"icon icon-conversation","library":"ekiticons"}',
			'{"value":"icon icon-crane1","library":"ekiticons"}',
			'{"value":"icon icon-crane-2","library":"ekiticons"}',
			'{"value":"icon icon-credit-card","library":"ekiticons"}',
			'{"value":"icon icon-credit-card1","library":"ekiticons"}',
			'{"value":"icon icon-cursor","library":"ekiticons"}',
			'{"value":"icon icon-customer-service","library":"ekiticons"}',
			'{"value":"icon icon-cutlery","library":"ekiticons"}',
			'{"value":"icon icon-dart-board","library":"ekiticons"}',
			'{"value":"icon icon-decision-making","library":"ekiticons"}',
			'{"value":"icon icon-desk-chair","library":"ekiticons"}',
			'{"value":"icon icon-desk-lamp","library":"ekiticons"}',
			'{"value":"icon icon-diamond2","library":"ekiticons"}',
			'{"value":"icon icon-direction","library":"ekiticons"}',
			'{"value":"icon icon-document","library":"ekiticons"}',
			'{"value":"icon icon-dollar-bill","library":"ekiticons"}',
			'{"value":"icon icon-download1","library":"ekiticons"}',
			'{"value":"icon icon-edit1","library":"ekiticons"}',
			'{"value":"icon icon-email1","library":"ekiticons"}',
			'{"value":"icon icon-envelope2","library":"ekiticons"}',
			'{"value":"icon icon-envelope3","library":"ekiticons"}',
			'{"value":"icon icon-eraser","library":"ekiticons"}',
			'{"value":"icon icon-eye2","library":"ekiticons"}',
			'{"value":"icon icon-factory","library":"ekiticons"}',
			'{"value":"icon icon-fast-forward","library":"ekiticons"}',
			'{"value":"icon icon-favorites1","library":"ekiticons"}',
			'{"value":"icon icon-file","library":"ekiticons"}',
			'{"value":"icon icon-file-1","library":"ekiticons"}',
			'{"value":"icon icon-file-2","library":"ekiticons"}',
			'{"value":"icon icon-file-3","library":"ekiticons"}',
			'{"value":"icon icon-filter","library":"ekiticons"}',
			'{"value":"icon icon-finance-book","library":"ekiticons"}',
			'{"value":"icon icon-flag1","library":"ekiticons"}',
			'{"value":"icon icon-folder1","library":"ekiticons"}',
			'{"value":"icon icon-folder-1","library":"ekiticons"}',
			'{"value":"icon icon-folders","library":"ekiticons"}',
			'{"value":"icon icon-folders1","library":"ekiticons"}',
			'{"value":"icon icon-gamepad","library":"ekiticons"}',
			'{"value":"icon icon-gift11","library":"ekiticons"}',
			'{"value":"icon icon-growth","library":"ekiticons"}',
			'{"value":"icon icon-heart11","library":"ekiticons"}',
			'{"value":"icon icon-home2","library":"ekiticons"}',
			'{"value":"icon icon-house","library":"ekiticons"}',
			'{"value":"icon icon-house-1","library":"ekiticons"}',
			'{"value":"icon icon-house-2","library":"ekiticons"}',
			'{"value":"icon icon-id-card","library":"ekiticons"}',
			'{"value":"icon icon-id-card1","library":"ekiticons"}',
			'{"value":"icon icon-id-card-1","library":"ekiticons"}',
			'{"value":"icon icon-idea1","library":"ekiticons"}',
			'{"value":"icon icon-image","library":"ekiticons"}',
			'{"value":"icon icon-improvement","library":"ekiticons"}',
			'{"value":"icon icon-inbox1","library":"ekiticons"}',
			'{"value":"icon icon-information","library":"ekiticons"}',
			'{"value":"icon icon-key","library":"ekiticons"}',
			'{"value":"icon icon-key1","library":"ekiticons"}',
			'{"value":"icon icon-laptop1","library":"ekiticons"}',
			'{"value":"icon icon-layers1","library":"ekiticons"}',
			'{"value":"icon icon-light-bulb1","library":"ekiticons"}',
			'{"value":"icon icon-like1","library":"ekiticons"}',
			'{"value":"icon icon-line-chart1","library":"ekiticons"}',
			'{"value":"icon icon-mail","library":"ekiticons"}',
			'{"value":"icon icon-manager","library":"ekiticons"}',
			'{"value":"icon icon-map1","library":"ekiticons"}',
			'{"value":"icon icon-medal1","library":"ekiticons"}',
			'{"value":"icon icon-megaphone","library":"ekiticons"}',
			'{"value":"icon icon-megaphone1","library":"ekiticons"}',
			'{"value":"icon icon-message","library":"ekiticons"}',
			'{"value":"icon icon-message-1","library":"ekiticons"}',
			'{"value":"icon icon-message-2","library":"ekiticons"}',
			'{"value":"icon icon-microphone","library":"ekiticons"}',
			'{"value":"icon icon-money1","library":"ekiticons"}',
			'{"value":"icon icon-money-bag1","library":"ekiticons"}',
			'{"value":"icon icon-monitor1","library":"ekiticons"}',
			'{"value":"icon icon-music1","library":"ekiticons"}',
			'{"value":"icon icon-next","library":"ekiticons"}',
			'{"value":"icon icon-open-book1","library":"ekiticons"}',
			'{"value":"icon icon-padlock","library":"ekiticons"}',
			'{"value":"icon icon-padlock-1","library":"ekiticons"}',
			'{"value":"icon icon-paint-brush","library":"ekiticons"}',
			'{"value":"icon icon-pause","library":"ekiticons"}',
			'{"value":"icon icon-pen","library":"ekiticons"}',
			'{"value":"icon icon-pencil1","library":"ekiticons"}',
			'{"value":"icon icon-percentage","library":"ekiticons"}',
			'{"value":"icon icon-phone-call1","library":"ekiticons"}',
			'{"value":"icon icon-phone-call2","library":"ekiticons"}',
			'{"value":"icon icon-photo-camera1","library":"ekiticons"}',
			'{"value":"icon icon-pie-chart3","library":"ekiticons"}',
			'{"value":"icon icon-pipe","library":"ekiticons"}',
			'{"value":"icon icon-placeholder1","library":"ekiticons"}',
			'{"value":"icon icon-placeholder2","library":"ekiticons"}',
			'{"value":"icon icon-planet-earth","library":"ekiticons"}',
			'{"value":"icon icon-play-button1","library":"ekiticons"}',
			'{"value":"icon icon-power-button","library":"ekiticons"}',
			'{"value":"icon icon-presentation","library":"ekiticons"}',
			'{"value":"icon icon-presentation1","library":"ekiticons"}',
			'{"value":"icon icon-printer1","library":"ekiticons"}',
			'{"value":"icon icon-push-pin","library":"ekiticons"}',
			'{"value":"icon icon-push-pin1","library":"ekiticons"}',
			'{"value":"icon icon-refresh","library":"ekiticons"}',
			'{"value":"icon icon-reload","library":"ekiticons"}',
			'{"value":"icon icon-return","library":"ekiticons"}',
			'{"value":"icon icon-rocket-ship","library":"ekiticons"}',
			'{"value":"icon icon-rss1","library":"ekiticons"}',
			'{"value":"icon icon-safebox","library":"ekiticons"}',
			'{"value":"icon icon-safebox1","library":"ekiticons"}',
			'{"value":"icon icon-settings1","library":"ekiticons"}',
			'{"value":"icon icon-settings-2","library":"ekiticons"}',
			'{"value":"icon icon-sewing-machine","library":"ekiticons"}',
			'{"value":"icon icon-share2","library":"ekiticons"}',
			'{"value":"icon icon-shield1","library":"ekiticons"}',
			'{"value":"icon icon-shield11","library":"ekiticons"}',
			'{"value":"icon icon-shopping","library":"ekiticons"}',
			'{"value":"icon icon-shopping-bag","library":"ekiticons"}',
			'{"value":"icon icon-shopping-bag-1","library":"ekiticons"}',
			'{"value":"icon icon-shopping-bag-2","library":"ekiticons"}',
			'{"value":"icon icon-shopping-cart11","library":"ekiticons"}',
			'{"value":"icon icon-shopping-cart2","library":"ekiticons"}',
			'{"value":"icon icon-shopping-cart-1","library":"ekiticons"}',
			'{"value":"icon icon-shopping-cart-2","library":"ekiticons"}',
			'{"value":"icon icon-shopping-cart-3","library":"ekiticons"}',
			'{"value":"icon icon-smartphone2","library":"ekiticons"}',
			'{"value":"icon icon-speaker","library":"ekiticons"}',
			'{"value":"icon icon-speakers","library":"ekiticons"}',
			'{"value":"icon icon-stats","library":"ekiticons"}',
			'{"value":"icon icon-stats-1","library":"ekiticons"}',
			'{"value":"icon icon-stats-2","library":"ekiticons"}',
			'{"value":"icon icon-stats-3","library":"ekiticons"}',
			'{"value":"icon icon-stats-4","library":"ekiticons"}',
			'{"value":"icon icon-stats-5","library":"ekiticons"}',
			'{"value":"icon icon-stats-6","library":"ekiticons"}',
			'{"value":"icon icon-sticky-note","library":"ekiticons"}',
			'{"value":"icon icon-store1","library":"ekiticons"}',
			'{"value":"icon icon-store-1","library":"ekiticons"}',
			'{"value":"icon icon-suitcase","library":"ekiticons"}',
			'{"value":"icon icon-suitcase-1","library":"ekiticons"}',
			'{"value":"icon icon-tag2","library":"ekiticons"}',
			'{"value":"icon icon-target","library":"ekiticons"}',
			'{"value":"icon icon-team2","library":"ekiticons"}',
			'{"value":"icon icon-tie","library":"ekiticons"}',
			'{"value":"icon icon-trash1","library":"ekiticons"}',
			'{"value":"icon icon-trolley","library":"ekiticons"}',
			'{"value":"icon icon-trolley-1","library":"ekiticons"}',
			'{"value":"icon icon-trolley-2","library":"ekiticons"}',
			'{"value":"icon icon-trophy1","library":"ekiticons"}',
			'{"value":"icon icon-truck1","library":"ekiticons"}',
			'{"value":"icon icon-truck-1","library":"ekiticons"}',
			'{"value":"icon icon-truck-2","library":"ekiticons"}',
			'{"value":"icon icon-umbrella","library":"ekiticons"}',
			'{"value":"icon icon-upload1","library":"ekiticons"}',
			'{"value":"icon icon-user1","library":"ekiticons"}',
			'{"value":"icon icon-user-1","library":"ekiticons"}',
			'{"value":"icon icon-user-2","library":"ekiticons"}',
			'{"value":"icon icon-user-3","library":"ekiticons"}',
			'{"value":"icon icon-users2","library":"ekiticons"}',
			'{"value":"icon icon-video-camera","library":"ekiticons"}',
			'{"value":"icon icon-voucher","library":"ekiticons"}',
			'{"value":"icon icon-voucher-1","library":"ekiticons"}',
			'{"value":"icon icon-voucher-2","library":"ekiticons"}',
			'{"value":"icon icon-voucher-3","library":"ekiticons"}',
			'{"value":"icon icon-voucher-4","library":"ekiticons"}',
			'{"value":"icon icon-wallet","library":"ekiticons"}',
			'{"value":"icon icon-wallet1","library":"ekiticons"}',
			'{"value":"icon icon-wifi","library":"ekiticons"}',
			'{"value":"icon icon-worker","library":"ekiticons"}',
			'{"value":"icon icon-zoom-in","library":"ekiticons"}',
			'{"value":"icon icon-zoom-out","library":"ekiticons"}',
			'{"value":"icon icon-burger-menu","library":"ekiticons"}',
			'{"value":"icon icon-squares","library":"ekiticons"}',
			'{"value":"icon icon-options","library":"ekiticons"}',
			'{"value":"icon icon-apps","library":"ekiticons"}',
			'{"value":"icon icon-menu-11","library":"ekiticons"}',
			'{"value":"icon icon-menu11","library":"ekiticons"}',
			'{"value":"icon icon-back_up","library":"ekiticons"}',
			'{"value":"icon icon-cart11","library":"ekiticons"}',
			'{"value":"icon icon-checkmark","library":"ekiticons"}',
			'{"value":"icon icon-dollar","library":"ekiticons"}',
			'{"value":"icon icon-domian","library":"ekiticons"}',
			'{"value":"icon icon-hosting1","library":"ekiticons"}',
			'{"value":"icon icon-key2","library":"ekiticons"}',
			'{"value":"icon icon-migration","library":"ekiticons"}',
			'{"value":"icon icon-play1","library":"ekiticons"}',
			'{"value":"icon icon-quote2","library":"ekiticons"}',
			'{"value":"icon icon-api_setup","library":"ekiticons"}',
			'{"value":"icon icon-coin","library":"ekiticons"}',
			'{"value":"icon icon-hand_shake","library":"ekiticons"}',
			'{"value":"icon icon-idea_generate","library":"ekiticons"}',
			'{"value":"icon icon-page_search","library":"ekiticons"}',
			'{"value":"icon icon-pen_shape","library":"ekiticons"}',
			'{"value":"icon icon-pencil_art","library":"ekiticons"}',
			'{"value":"icon icon-review","library":"ekiticons"}',
			'{"value":"icon icon-star","library":"ekiticons"}',
			'{"value":"icon icon-timing","library":"ekiticons"}',
			'{"value":"icon icon-trophy","library":"ekiticons"}',
			'{"value":"icon icon-communication","library":"ekiticons"}',
			'{"value":"icon icon-money-bag2","library":"ekiticons"}',
			'{"value":"icon icon-dentist","library":"ekiticons"}',
			'{"value":"icon icon-bill","library":"ekiticons"}',
			'{"value":"icon icon-label","library":"ekiticons"}',
			'{"value":"icon icon-money","library":"ekiticons"}',
			'{"value":"icon icon-shield","library":"ekiticons"}',
			'{"value":"icon icon-support","library":"ekiticons"}',
			'{"value":"icon icon-one","library":"ekiticons"}',
			'{"value":"icon icon-clock","library":"ekiticons"}',
			'{"value":"icon icon-cart","library":"ekiticons"}',
			'{"value":"icon icon-globe","library":"ekiticons"}',
			'{"value":"icon icon-tooth","library":"ekiticons"}',
			'{"value":"icon icon-tooth-1","library":"ekiticons"}',
			'{"value":"icon icon-tooth-2","library":"ekiticons"}',
			'{"value":"icon icon-brain","library":"ekiticons"}',
			'{"value":"icon icon-view","library":"ekiticons"}',
			'{"value":"icon icon-doctor","library":"ekiticons"}',
			'{"value":"icon icon-heart","library":"ekiticons"}',
			'{"value":"icon icon-medicine","library":"ekiticons"}',
			'{"value":"icon icon-stethoscope","library":"ekiticons"}',
			'{"value":"icon icon-hospital","library":"ekiticons"}',
			'{"value":"icon icon-clipboard","library":"ekiticons"}',
			'{"value":"icon icon-medicine-1","library":"ekiticons"}',
			'{"value":"icon icon-hospital-1","library":"ekiticons"}',
			'{"value":"icon icon-customer-support","library":"ekiticons"}',
			'{"value":"icon icon-brickwall","library":"ekiticons"}',
			'{"value":"icon icon-crane2","library":"ekiticons"}',
			'{"value":"icon icon-valve","library":"ekiticons"}',
			'{"value":"icon icon-safety","library":"ekiticons"}',
			'{"value":"icon icon-energy-saving","library":"ekiticons"}',
			'{"value":"icon icon-paint-roller","library":"ekiticons"}',
			'{"value":"icon icon-paint-brushes","library":"ekiticons"}',
			'{"value":"icon icon-construction-tool-vehicle-with-crane-lifting-materials","library":"ekiticons"}',
			'{"value":"icon icon-trowel","library":"ekiticons"}',
			'{"value":"icon icon-bucket","library":"ekiticons"}',
			'{"value":"icon icon-smart","library":"ekiticons"}',
			'{"value":"icon icon-repair","library":"ekiticons"}',
			'{"value":"icon icon-saw","library":"ekiticons"}',
			'{"value":"icon icon-cutter","library":"ekiticons"}',
			'{"value":"icon icon-plier","library":"ekiticons"}',
			'{"value":"icon icon-drill","library":"ekiticons"}',
			'{"value":"icon icon-save-money","library":"ekiticons"}',
			'{"value":"icon icon-planting","library":"ekiticons"}',
			'{"value":"icon icon-line-chart","library":"ekiticons"}',
			'{"value":"icon icon-open-book","library":"ekiticons"}',
			'{"value":"icon icon-money-bag3","library":"ekiticons"}',
			'{"value":"icon icon-server","library":"ekiticons"}',
			'{"value":"icon icon-server-1","library":"ekiticons"}',
			'{"value":"icon icon-server-2","library":"ekiticons"}',
			'{"value":"icon icon-cloud-computing","library":"ekiticons"}',
			'{"value":"icon icon-cloud","library":"ekiticons"}',
			'{"value":"icon icon-database","library":"ekiticons"}',
			'{"value":"icon icon-computer","library":"ekiticons"}',
			'{"value":"icon icon-server-3","library":"ekiticons"}',
			'{"value":"icon icon-server-4","library":"ekiticons"}',
			'{"value":"icon icon-server-5","library":"ekiticons"}',
			'{"value":"icon icon-server-6","library":"ekiticons"}',
			'{"value":"icon icon-server-7","library":"ekiticons"}',
			'{"value":"icon icon-cloud-1","library":"ekiticons"}',
			'{"value":"icon icon-server-8","library":"ekiticons"}',
			'{"value":"icon icon-business-and-finance","library":"ekiticons"}',
			'{"value":"icon icon-cloud-2","library":"ekiticons"}',
			'{"value":"icon icon-server-9","library":"ekiticons"}',
			'{"value":"icon icon-hosting","library":"ekiticons"}',
			'{"value":"icon icon-car","library":"ekiticons"}',
			'{"value":"icon icon-car-frontal-view","library":"ekiticons"}',
			'{"value":"icon icon-car-1","library":"ekiticons"}',
			'{"value":"icon icon-racing","library":"ekiticons"}',
			'{"value":"icon icon-car-wheel","library":"ekiticons"}',
			'{"value":"icon icon-steering-wheel","library":"ekiticons"}',
			'{"value":"icon icon-frontal-taxi-cab","library":"ekiticons"}',
			'{"value":"icon icon-taxi","library":"ekiticons"}',
			'{"value":"icon icon-cosmetics","library":"ekiticons"}',
			'{"value":"icon icon-flower","library":"ekiticons"}',
			'{"value":"icon icon-mirror","library":"ekiticons"}',
			'{"value":"icon icon-itunes","library":"ekiticons"}',
			'{"value":"icon icon-salon","library":"ekiticons"}',
			'{"value":"icon icon-hair-dryer","library":"ekiticons"}',
			'{"value":"icon icon-shampoo","library":"ekiticons"}',
			'{"value":"icon icon-download-button","library":"ekiticons"}',
			'{"value":"icon icon-list","library":"ekiticons"}',
			'{"value":"icon icon-loupe","library":"ekiticons"}',
			'{"value":"icon icon-search","library":"ekiticons"}',
			'{"value":"icon icon-search-1","library":"ekiticons"}',
			'{"value":"icon icon-shopping-cart","library":"ekiticons"}',
			'{"value":"icon icon-menu","library":"ekiticons"}',
			'{"value":"icon icon-menu-1","library":"ekiticons"}',
			'{"value":"icon icon-menu-button-of-three-horizontal-lines","library":"ekiticons"}',
			'{"value":"icon icon-menu-2","library":"ekiticons"}',
			'{"value":"icon icon-menu-3","library":"ekiticons"}',
			'{"value":"icon icon-menu-5","library":"ekiticons"}',
			'{"value":"icon icon-menu-button","library":"ekiticons"}',
			'{"value":"icon icon-list-1","library":"ekiticons"}',
			'{"value":"icon icon-menu-6","library":"ekiticons"}',
			'{"value":"icon icon-menu-7","library":"ekiticons"}',
			'{"value":"icon icon-menu-8","library":"ekiticons"}',
			'{"value":"icon icon-list-2","library":"ekiticons"}',
			'{"value":"icon icon-dot","library":"ekiticons"}',
			'{"value":"icon icon-menu-9","library":"ekiticons"}',
			'{"value":"icon icon-search11","library":"ekiticons"}',
			'{"value":"icon icon-search-minus","library":"ekiticons"}',
			'{"value":"icon icon-search-11","library":"ekiticons"}',
			'{"value":"icon icon-search-2","library":"ekiticons"}',
			'{"value":"icon icon-search-3","library":"ekiticons"}',
			'{"value":"icon icon-magnifying-glass-search","library":"ekiticons"}',
			'{"value":"icon icon-loupe1","library":"ekiticons"}',
			'{"value":"icon icon-speed","library":"ekiticons"}',
			'{"value":"icon icon-search21","library":"ekiticons"}',
			'{"value":"icon icon-search-4","library":"ekiticons"}',
			'{"value":"icon icon-search-5","library":"ekiticons"}',
			'{"value":"icon icon-detective","library":"ekiticons"}',
			'{"value":"icon icon-cart1","library":"ekiticons"}',
			'{"value":"icon icon-buying-on-smartphone","library":"ekiticons"}',
			'{"value":"icon icon-badge","library":"ekiticons"}',
			'{"value":"icon icon-basket1","library":"ekiticons"}',
			'{"value":"icon icon-commerce-and-shopping","library":"ekiticons"}',
			'{"value":"icon icon-comment","library":"ekiticons"}',
			'{"value":"icon icon-comment-1","library":"ekiticons"}',
			'{"value":"icon icon-share","library":"ekiticons"}',
			'{"value":"icon icon-share-1","library":"ekiticons"}',
			'{"value":"icon icon-share-2","library":"ekiticons"}',
			'{"value":"icon icon-share-3","library":"ekiticons"}',
			'{"value":"icon icon-comment1","library":"ekiticons"}',
			'{"value":"icon icon-favorite","library":"ekiticons"}',
			'{"value":"icon icon-retweet","library":"ekiticons"}',
			'{"value":"icon icon-share1","library":"ekiticons"}',
			'{"value":"icon icon-facebook","library":"ekiticons"}',
			'{"value":"icon icon-twitter","library":"ekiticons"}',
			'{"value":"icon icon-linkedin","library":"ekiticons"}',
			'{"value":"icon icon-whatsapp-1","library":"ekiticons"}',
			'{"value":"icon icon-dribbble","library":"ekiticons"}',
			'{"value":"icon icon-facebook-2","library":"ekiticons"}',
			'{"value":"icon icon-twitter1","library":"ekiticons"}',
			'{"value":"icon icon-vk","library":"ekiticons"}',
			'{"value":"icon icon-youtube-v","library":"ekiticons"}',
			'{"value":"icon icon-vimeo","library":"ekiticons"}',
			'{"value":"icon icon-youtube","library":"ekiticons"}',
			'{"value":"icon icon-snapchat-1","library":"ekiticons"}',
			'{"value":"icon icon-behance","library":"ekiticons"}',
			'{"value":"icon icon-github","library":"ekiticons"}',
			'{"value":"icon icon-pinterest","library":"ekiticons"}',
			'{"value":"icon icon-spotify","library":"ekiticons"}',
			'{"value":"icon icon-soundcloud-1","library":"ekiticons"}',
			'{"value":"icon icon-skype-1","library":"ekiticons"}',
			'{"value":"icon icon-rss","library":"ekiticons"}',
			'{"value":"icon icon-reddit-1","library":"ekiticons"}',
			'{"value":"icon icon-dribbble-1","library":"ekiticons"}',
			'{"value":"icon icon-wordpress-1","library":"ekiticons"}',
			'{"value":"icon icon-logo","library":"ekiticons"}',
			'{"value":"icon icon-dropbox-1","library":"ekiticons"}',
			'{"value":"icon icon-blogger-1","library":"ekiticons"}',
			'{"value":"icon icon-photo","library":"ekiticons"}',
			'{"value":"icon icon-hangouts","library":"ekiticons"}',
			'{"value":"icon icon-xing","library":"ekiticons"}',
			'{"value":"icon icon-myspace","library":"ekiticons"}',
			'{"value":"icon icon-flickr-1","library":"ekiticons"}',
			'{"value":"icon icon-envato","library":"ekiticons"}',
			'{"value":"icon icon-picasa-1","library":"ekiticons"}',
			'{"value":"icon icon-wattpad","library":"ekiticons"}',
			'{"value":"icon icon-emoji","library":"ekiticons"}',
			'{"value":"icon icon-deviantart-1","library":"ekiticons"}',
			'{"value":"icon icon-yahoo-1","library":"ekiticons"}',
			'{"value":"icon icon-vine-1","library":"ekiticons"}',
			'{"value":"icon icon-delicious","library":"ekiticons"}',
			'{"value":"icon icon-kickstarter-1","library":"ekiticons"}',
			'{"value":"icon icon-stumbleupon-1","library":"ekiticons"}',
			'{"value":"icon icon-brands-and-logotypes","library":"ekiticons"}',
			'{"value":"icon icon-instagram-1","library":"ekiticons"}',
			'{"value":"icon icon-facebook-1","library":"ekiticons"}',
			'{"value":"icon icon-instagram-2","library":"ekiticons"}',
			'{"value":"icon icon-twitter-1","library":"ekiticons"}',
			'{"value":"icon icon-whatsapp-2","library":"ekiticons"}',
			'{"value":"icon icon-youtube-1","library":"ekiticons"}',
			'{"value":"icon icon-linkedin-1","library":"ekiticons"}',
			'{"value":"icon icon-telegram","library":"ekiticons"}',
			'{"value":"icon icon-github-1","library":"ekiticons"}',
			'{"value":"icon icon-vk-1","library":"ekiticons"}',
			'{"value":"icon icon-pinterest-1","library":"ekiticons"}',
			'{"value":"icon icon-rss-1","library":"ekiticons"}',
			'{"value":"icon icon-twitch","library":"ekiticons"}',
			'{"value":"icon icon-snapchat-2","library":"ekiticons"}',
			'{"value":"icon icon-skype-2","library":"ekiticons"}',
			'{"value":"icon icon-behance-2","library":"ekiticons"}',
			'{"value":"icon icon-spotify-1","library":"ekiticons"}',
			'{"value":"icon icon-periscope","library":"ekiticons"}',
			'{"value":"icon icon-dribbble-2","library":"ekiticons"}',
			'{"value":"icon icon-tumblr-1","library":"ekiticons"}',
			'{"value":"icon icon-soundcloud-2","library":"ekiticons"}',
			'{"value":"icon icon-google-drive-1","library":"ekiticons"}',
			'{"value":"icon icon-dropbox-2","library":"ekiticons"}',
			'{"value":"icon icon-reddit-2","library":"ekiticons"}',
			'{"value":"icon icon-html","library":"ekiticons"}',
			'{"value":"icon icon-vimeo-1","library":"ekiticons"}',
			'{"value":"icon icon-hangout","library":"ekiticons"}',
			'{"value":"icon icon-blogger-2","library":"ekiticons"}',
			'{"value":"icon icon-yahoo-2","library":"ekiticons"}',
			'{"value":"icon icon-path","library":"ekiticons"}',
			'{"value":"icon icon-yelp-1","library":"ekiticons"}',
			'{"value":"icon icon-slideshare","library":"ekiticons"}',
			'{"value":"icon icon-picasa-2","library":"ekiticons"}',
			'{"value":"icon icon-myspace-1","library":"ekiticons"}',
			'{"value":"icon icon-flickr-2","library":"ekiticons"}',
			'{"value":"icon icon-xing-1","library":"ekiticons"}',
			'{"value":"icon icon-envato-1","library":"ekiticons"}',
			'{"value":"icon icon-swarm","library":"ekiticons"}',
			'{"value":"icon icon-wattpad-1","library":"ekiticons"}',
			'{"value":"icon icon-foursquare","library":"ekiticons"}',
			'{"value":"icon icon-deviantart-2","library":"ekiticons"}',
			'{"value":"icon icon-kickstarter-2","library":"ekiticons"}',
			'{"value":"icon icon-delicious-1","library":"ekiticons"}',
			'{"value":"icon icon-vine-2","library":"ekiticons"}',
			'{"value":"icon icon-digg","library":"ekiticons"}',
			'{"value":"icon icon-bebo","library":"ekiticons"}',
			'{"value":"icon icon-stumbleupon-2","library":"ekiticons"}',
			'{"value":"icon icon-forrst","library":"ekiticons"}',
			'{"value":"icon icon-eye3","library":"ekiticons"}',
			'{"value":"icon icon-microscope","library":"ekiticons"}',
			'{"value":"icon icon-Anti-Lock","library":"ekiticons"}',
			'{"value":"icon icon-apartment","library":"ekiticons"}',
			'{"value":"icon icon-app","library":"ekiticons"}',
			'{"value":"icon icon-Aroma","library":"ekiticons"}',
			'{"value":"icon icon-bamboo-Leaf","library":"ekiticons"}',
			'{"value":"icon icon-basket","library":"ekiticons"}',
			'{"value":"icon icon-Battery","library":"ekiticons"}',
			'{"value":"icon icon-Bettery","library":"ekiticons"}',
			'{"value":"icon icon-building","library":"ekiticons"}',
			'{"value":"icon icon-car-2","library":"ekiticons"}',
			'{"value":"icon icon-Car","library":"ekiticons"}',
			'{"value":"icon icon-Child","library":"ekiticons"}',
			'{"value":"icon icon-cityscape","library":"ekiticons"}',
			'{"value":"icon icon-cleaner","library":"ekiticons"}',
			'{"value":"icon icon-Coffee-cup","library":"ekiticons"}',
			'{"value":"icon icon-coins","library":"ekiticons"}',
			'{"value":"icon icon-Computer","library":"ekiticons"}',
			'{"value":"icon icon-Consultancy","library":"ekiticons"}',
			'{"value":"icon icon-cottage","library":"ekiticons"}',
			'{"value":"icon icon-crane","library":"ekiticons"}',
			'{"value":"icon icon-Custom-api","library":"ekiticons"}',
			'{"value":"icon icon-customer-support-2","library":"ekiticons"}',
			'{"value":"icon icon-Design-2","library":"ekiticons"}',
			'{"value":"icon icon-Design-3","library":"ekiticons"}',
			'{"value":"icon icon-design","library":"ekiticons"}',
			'{"value":"icon icon-diamond","library":"ekiticons"}',
			'{"value":"icon icon-diploma","library":"ekiticons"}',
			'{"value":"icon icon-Document-Search","library":"ekiticons"}',
			'{"value":"icon icon-Download","library":"ekiticons"}',
			'{"value":"icon icon-drilling","library":"ekiticons"}',
			'{"value":"icon icon-engine","library":"ekiticons"}',
			'{"value":"icon icon-engineer","library":"ekiticons"}',
			'{"value":"icon icon-envelope","library":"ekiticons"}',
			'{"value":"icon icon-Family","library":"ekiticons"}',
			'{"value":"icon icon-friendship","library":"ekiticons"}',
			'{"value":"icon icon-gift","library":"ekiticons"}',
			'{"value":"icon icon-graph-2","library":"ekiticons"}',
			'{"value":"icon icon-graph","library":"ekiticons"}',
			'{"value":"icon icon-hamburger-2","library":"ekiticons"}',
			'{"value":"icon icon-handshake","library":"ekiticons"}',
			'{"value":"icon icon-Helmet","library":"ekiticons"}',
			'{"value":"icon icon-hot-Stone-2","library":"ekiticons"}',
			'{"value":"icon icon-hot-stone","library":"ekiticons"}',
			'{"value":"icon icon-idea","library":"ekiticons"}',
			'{"value":"icon icon-Leaf","library":"ekiticons"}',
			'{"value":"icon icon-management","library":"ekiticons"}',
			'{"value":"icon icon-Massage-table","library":"ekiticons"}',
			'{"value":"icon icon-Mechanic","library":"ekiticons"}',
			'{"value":"icon icon-Money-2","library":"ekiticons"}',
			'{"value":"icon icon-money-bag","library":"ekiticons"}',
			'{"value":"icon icon-Money","library":"ekiticons"}',
			'{"value":"icon icon-oil-bottle","library":"ekiticons"}',
			'{"value":"icon icon-Physiotherapy","library":"ekiticons"}',
			'{"value":"icon icon-Profile","library":"ekiticons"}',
			'{"value":"icon icon-Rating","library":"ekiticons"}',
			'{"value":"icon icon-right-mark","library":"ekiticons"}',
			'{"value":"icon icon-rings","library":"ekiticons"}',
			'{"value":"icon icon-Safe-house","library":"ekiticons"}',
			'{"value":"icon icon-Scan","library":"ekiticons"}',
			'{"value":"icon icon-social-care","library":"ekiticons"}',
			'{"value":"icon icon-Speed-Clock","library":"ekiticons"}',
			'{"value":"icon icon-stopwatch","library":"ekiticons"}',
			'{"value":"icon icon-Support-2","library":"ekiticons"}',
			'{"value":"icon icon-target-2","library":"ekiticons"}',
			'{"value":"icon icon-Target","library":"ekiticons"}',
			'{"value":"icon icon-tripod","library":"ekiticons"}',
			'{"value":"icon icon-truck","library":"ekiticons"}',
			'{"value":"icon icon-university","library":"ekiticons"}',
			'{"value":"icon icon-User","library":"ekiticons"}',
			'{"value":"icon icon-Web-Portals","library":"ekiticons"}',
			'{"value":"icon icon-window","library":"ekiticons"}',
			'{"value":"icon icon-ek_line_icon","library":"ekiticons"}',
			'{"value":"icon icon-ek_stroke_icon","library":"ekiticons"}',
			'{"value":"icon icon-ekit","library":"ekiticons"}',
			'{"value":"icon icon-elements-kit-logo","library":"ekiticons"}',
			'{"value":"icon icon-degree-image","library":"ekiticons"}',
			'{"value":"icon icon-accordion","library":"ekiticons"}',
			'{"value":"icon icon-animated-flip-box","library":"ekiticons"}',
			'{"value":"icon icon-animated-text","library":"ekiticons"}',
			'{"value":"icon icon-brands","library":"ekiticons"}',
			'{"value":"icon icon-business-hour","library":"ekiticons"}',
			'{"value":"icon icon-button","library":"ekiticons"}',
			'{"value":"icon icon-carousel","library":"ekiticons"}',
			'{"value":"icon icon-Circle-progress","library":"ekiticons"}',
			'{"value":"icon icon-contact-form","library":"ekiticons"}',
			'{"value":"icon icon-countdown-timer","library":"ekiticons"}',
			'{"value":"icon icon-dropbar","library":"ekiticons"}',
			'{"value":"icon icon-faq","library":"ekiticons"}',
			'{"value":"icon icon-full-width-scroll","library":"ekiticons"}',
			'{"value":"icon icon-google-map","library":"ekiticons"}',
			'{"value":"icon icon-heading-style","library":"ekiticons"}',
			'{"value":"icon icon-help-desk","library":"ekiticons"}',
			'{"value":"icon icon-horizontal-timeline","library":"ekiticons"}',
			'{"value":"icon icon-iframe","library":"ekiticons"}',
			'{"value":"icon icon-image-comparison","library":"ekiticons"}',
			'{"value":"icon icon-image-gallery","library":"ekiticons"}',
			'{"value":"icon icon-image-justify","library":"ekiticons"}',
			'{"value":"icon icon-image-magnifier","library":"ekiticons"}',
			'{"value":"icon icon-image-masonry","library":"ekiticons"}',
			'{"value":"icon icon-inline-svg","library":"ekiticons"}',
			'{"value":"icon icon-instagram","library":"ekiticons"}',
			'{"value":"icon icon-listing","library":"ekiticons"}',
			'{"value":"icon icon-music-player","library":"ekiticons"}',
			'{"value":"icon icon-news-ticker","library":"ekiticons"}',
			'{"value":"icon icon-off-canvus-menu","library":"ekiticons"}',
			'{"value":"icon icon-parallax","library":"ekiticons"}',
			'{"value":"icon icon-portfolio","library":"ekiticons"}',
			'{"value":"icon icon-post-banner","library":"ekiticons"}',
			'{"value":"icon icon-post-carousel","library":"ekiticons"}',
			'{"value":"icon icon-post-grid","library":"ekiticons"}',
			'{"value":"icon icon-post-slider","library":"ekiticons"}',
			'{"value":"icon icon-pricing-list","library":"ekiticons"}',
			'{"value":"icon icon-pricing-table","library":"ekiticons"}',
			'{"value":"icon icon-product-featured","library":"ekiticons"}',
			'{"value":"icon icon-product-image","library":"ekiticons"}',
			'{"value":"icon icon-product-recent","library":"ekiticons"}',
			'{"value":"icon icon-product-sale","library":"ekiticons"}',
			'{"value":"icon icon-product-top-rated","library":"ekiticons"}',
			'{"value":"icon icon-product-top-seller","library":"ekiticons"}',
			'{"value":"icon icon-progress-bar","library":"ekiticons"}',
			'{"value":"icon icon-protected-content-v2","library":"ekiticons"}',
			'{"value":"icon icon-protected-content-v3","library":"ekiticons"}',
			'{"value":"icon icon-protected-content","library":"ekiticons"}',
			'{"value":"icon icon-qr_code","library":"ekiticons"}',
			'{"value":"icon icon-scroll-button","library":"ekiticons"}',
			'{"value":"icon icon-search1","library":"ekiticons"}',
			'{"value":"icon icon-service","library":"ekiticons"}',
			'{"value":"icon icon-slider-image","library":"ekiticons"}',
			'{"value":"icon icon-social-share","library":"ekiticons"}',
			'{"value":"icon icon-subscribe","library":"ekiticons"}',
			'{"value":"icon icon-tab","library":"ekiticons"}',
			'{"value":"icon icon-table","library":"ekiticons"}',
			'{"value":"icon icon-team-join","library":"ekiticons"}',
			'{"value":"icon icon-team-member","library":"ekiticons"}',
			'{"value":"icon icon-testimonial-carousel","library":"ekiticons"}',
			'{"value":"icon icon-testimonial-grid","library":"ekiticons"}',
			'{"value":"icon icon-testimonial-quote","library":"ekiticons"}',
			'{"value":"icon icon-testimonial-slider","library":"ekiticons"}',
			'{"value":"icon icon-toggle","library":"ekiticons"}',
			'{"value":"icon icon-user-login","library":"ekiticons"}',
			'{"value":"icon icon-user-registration","library":"ekiticons"}',
			'{"value":"icon icon-vertical-timeline","library":"ekiticons"}',
			'{"value":"icon icon-video-player","library":"ekiticons"}',
			'{"value":"icon icon-weather","library":"ekiticons"}',
		);

		return $original;
	}


	private function get_replace_string_array() {

		$replace = array(
			'{"value":"ekiticon ekiticon-home","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-apartment1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pencil","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-magic-wand","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-drop","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-lighter","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-poop","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-sun","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-moon","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud-upload","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud-download","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud-sync","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud-check","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-database1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-lock","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cog","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-trash","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dice","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-heart1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-star1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-star-half","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-star-empty","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-flag","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-envelope1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-paperclip","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-inbox","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-eye","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-printer","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-file-empty","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-file-add","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-enter","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-exit","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-graduation-hat","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-license","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-music-note","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-film-play","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-camera-video","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-camera","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-picture","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-book","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bookmark","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-user","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-users","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shirt","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-store","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cart2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tag","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-phone-handset","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-phone","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pushpin","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-map-marker","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-map","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-location","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calendar-full","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-keyboard","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-spell-check","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-screen","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-smartphone","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tablet","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-laptop","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-laptop-phone","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-power-switch","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bubble","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-heart-pulse","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-construction","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pie-chart","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chart-bars","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-gift1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-diamond1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dinner","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-coffee-cup","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-leaf","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-paw","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-rocket","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-briefcase","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bus","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-car1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-train","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bicycle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-wheelchair","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-select","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-earth","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-smile","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-sad","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-neutral","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-mustache","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-alarm","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bullhorn","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-volume-high","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-volume-medium","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-volume-low","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-volume","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-mic","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hourglass","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-undo","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-redo","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-sync","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-history","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-clock1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-download","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-upload","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-enter-down","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-exit-up","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bug","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-code","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-link","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-unlink","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-thumbs-up","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-thumbs-down","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-magnifier","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cross","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chevron-up","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chevron-down","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chevron-left","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chevron-right","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-arrow-up","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-arrow-down","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-arrow-left","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-arrow-right","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-right-arrow","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-left-arrow","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-download-arrow","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-up-arrow","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-arrows","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-double-angle-pointing-to-right","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-double-left-chevron","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-left-arrow2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-right-arrow2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-warning","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-down-arrow1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-up-arrow1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-right-arrow1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-left-arrows","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-question-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-checkmark-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cross-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-plus-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-move","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-circle-minus","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-arrow-up-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-arrow-down-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-arrow-left-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-arrow-right-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chevron-up-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chevron-down-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chevron-left-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chevron-right-circle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-crop","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-frame-expand","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-frame-contract","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-layers","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-funnel","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-text-format","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-text-size","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bold","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-italic","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-underline","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-strikethrough","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-highlight","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-text-align-left","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-text-align-center","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-text-align-right","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-text-align-justify","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-line-spacing","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-indent-increase","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-indent-decrease","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-page-break","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hand","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pointer-up","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pointer-right","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pointer-down","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pointer-left","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-burger","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cakes","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cheese","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-drink-glass","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pizza","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-vplay","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-newsletter","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-coins-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-commerce-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-monitor","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-business","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-graphic-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-commerce-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hammer","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-justice-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-line","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-money-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-commerce","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-agenda","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-justice","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-technology","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-coins-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bank","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calculator","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-soundcloud","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chart2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-checked","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-clock11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-comment2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-comments","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-consult","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-consut2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-deal","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-envelope11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-folder","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-folder2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-invest","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-loan","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-list1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-map-marker1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-mutual-fund","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-google-plus","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-phone1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pie-chart1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-play","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-savings","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tag1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tags","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-instagram1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-quote","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-arrow-point-to-down","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-play-button","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-minus","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-plus","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tick","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-check","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-edit","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-reply","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cogwheel-outline","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-abacus","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-abacus1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-agenda1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-basket","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-users1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-man","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-support1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-favorites","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calendar","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-paper-plane","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-placeholder","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-phone-call","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-contact","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-email","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-internet","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-quote1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-medical","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-eye1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-full-screen","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tools","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pie-chart2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-diamond11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-valentines-heart","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-like","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-team","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tshirt","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cancel","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-drink","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-home1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-music","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-rich","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-brush","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-opposite-way","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud-computing1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-technology-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-rotate","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-medical1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-flash-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-flash","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-uturn","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-down-arrow","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hours-support","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bag","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-photo-camera","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-school","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-settings","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-smartphone1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-technology-11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tool","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-business1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shuffle-arrow","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-van-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-van","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-vegetables","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-women","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-vintage","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-team-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-team1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-apple","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-watch","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cogwheel","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-light-bulb","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-light-bulb-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-heart-shape-outline","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-online-shopping-cart","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-cart1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-star2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-star-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-favorite1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-agenda2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-agenda-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-alarm-clock","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-alarm-clock1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-atomic","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-auction","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-balance","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-balance1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bank1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bar-chart","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-barrier","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-battery","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-battery-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bell","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bluetooth","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-book1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-briefcase1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-briefcase-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-briefcase-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calculator1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calculator2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calculator-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calendar1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calendar2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calendar-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calendar-page-empty","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-calendar3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-car11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-carrier","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cash","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chat","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-chat-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-checked1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-clip","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-clip1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-clipboard1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-clipboard11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-clock2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-clock-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud-computing11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud-computing-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cogwheel1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-coins1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-compass","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-contract","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-conversation","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-crane1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-crane-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-credit-card","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-credit-card1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cursor","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-customer-service","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cutlery","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dart-board","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-decision-making","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-desk-chair","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-desk-lamp","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-diamond2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-direction","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-document","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dollar-bill","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-download1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-edit1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-email1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-envelope2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-envelope3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-eraser","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-eye2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-factory","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-fast-forward","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-favorites1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-file","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-file-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-file-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-file-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-filter","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-finance-book","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-flag1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-folder1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-folder-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-folders","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-folders1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-gamepad","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-gift11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-growth","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-heart11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-home2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-house","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-house-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-house-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-id-card","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-id-card1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-id-card-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-idea1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-image","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-improvement","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-inbox1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-information","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-key","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-key1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-laptop1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-layers1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-light-bulb1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-like1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-line-chart1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-mail","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-manager","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-map1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-medal1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-megaphone","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-megaphone1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-message","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-message-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-message-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-microphone","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-money1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-money-bag1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-monitor1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-music1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-next","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-open-book1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-padlock","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-padlock-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-paint-brush","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pause","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pen","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pencil1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-percentage","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-phone-call1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-phone-call2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-photo-camera1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pie-chart3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pipe","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-placeholder1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-placeholder2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-planet-earth","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-play-button1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-power-button","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-presentation","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-presentation1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-printer1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-push-pin","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-push-pin1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-refresh","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-reload","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-return","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-rocket-ship","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-rss1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-safebox","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-safebox1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-settings1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-settings-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-sewing-machine","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-share2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shield1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shield11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-bag","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-bag-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-bag-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-cart11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-cart2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-cart-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-cart-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-cart-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-smartphone2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-speaker","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-speakers","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stats","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stats-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stats-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stats-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stats-4","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stats-5","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stats-6","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-sticky-note","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-store1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-store-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-suitcase","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-suitcase-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tag2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-target","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-team2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tie","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-trash1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-trolley","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-trolley-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-trolley-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-trophy1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-truck1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-truck-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-truck-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-umbrella","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-upload1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-user1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-user-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-user-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-user-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-users2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-video-camera","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-voucher","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-voucher-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-voucher-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-voucher-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-voucher-4","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-wallet","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-wallet1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-wifi","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-worker","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-zoom-in","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-zoom-out","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-burger-menu","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-squares","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-options","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-apps","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-back_up","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cart11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-checkmark","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dollar","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-domian","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hosting1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-key2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-migration","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-play1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-quote2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-api_setup","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-coin","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hand_shake","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-idea_generate","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-page_search","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pen_shape","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pencil_art","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-review","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-star","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-timing","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-trophy","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-communication","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-money-bag2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dentist","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bill","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-label","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-money","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shield","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-support","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-one","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-clock","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cart","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-globe","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tooth","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tooth-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tooth-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-brain","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-view","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-doctor","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-heart","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-medicine","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stethoscope","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hospital","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-clipboard","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-medicine-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hospital-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-customer-support","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-brickwall","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-crane2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-valve","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-safety","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-energy-saving","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-paint-roller","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-paint-brushes","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-construction-tool-vehicle-with-crane-lifting-materials","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-trowel","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bucket","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-smart","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-repair","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-saw","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cutter","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-plier","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-drill","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-save-money","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-planting","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-line-chart","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-open-book","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-money-bag3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-server","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-server-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-server-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud-computing","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-database","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-computer","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-server-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-server-4","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-server-5","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-server-6","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-server-7","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-server-8","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-business-and-finance","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cloud-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-server-9","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hosting","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-car","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-car-frontal-view","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-car-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-racing","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-car-wheel","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-steering-wheel","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-frontal-taxi-cab","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-taxi","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cosmetics","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-flower","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-mirror","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-itunes","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-salon","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hair-dryer","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shampoo","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-download-button","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-list","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-loupe","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-shopping-cart","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-button-of-three-horizontal-lines","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-5","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-button","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-list-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-6","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-7","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-8","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-list-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dot","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-menu-9","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search-minus","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search-11","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-magnifying-glass-search","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-loupe1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-speed","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search21","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search-4","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search-5","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-detective","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cart1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-buying-on-smartphone","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-badge","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-basket1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-commerce-and-shopping","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-comment","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-comment-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-share","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-share-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-share-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-share-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-comment1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-favorite","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-retweet","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-share1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-facebook","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-twitter","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-linkedin","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-whatsapp-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dribbble","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-facebook-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-twitter1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-vk","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-youtube-v","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-vimeo","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-youtube","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-snapchat-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-behance","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-github","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pinterest","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-spotify","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-soundcloud-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-skype-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-rss","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-reddit-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dribbble-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-wordpress-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-logo","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dropbox-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-blogger-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-photo","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hangouts","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-xing","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-myspace","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-flickr-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-envato","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-picasa-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-wattpad","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-emoji","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-deviantart-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-yahoo-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-vine-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-delicious","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-kickstarter-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stumbleupon-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-brands-and-logotypes","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-instagram-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-facebook-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-instagram-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-twitter-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-whatsapp-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-youtube-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-linkedin-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-telegram","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-github-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-vk-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pinterest-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-rss-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-twitch","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-snapchat-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-skype-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-behance-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-spotify-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-periscope","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dribbble-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tumblr-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-soundcloud-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-google-drive-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dropbox-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-reddit-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-html","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-vimeo-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hangout","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-blogger-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-yahoo-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-path","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-yelp-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-slideshare","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-picasa-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-myspace-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-flickr-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-xing-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-envato-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-swarm","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-wattpad-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-foursquare","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-deviantart-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-kickstarter-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-delicious-1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-vine-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-digg","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bebo","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stumbleupon-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-forrst","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-eye3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-microscope","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Anti-Lock","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-apartment","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-app","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Aroma","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-bamboo-Leaf","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-basket","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Battery","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Bettery","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-building","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-car-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Car","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Child","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cityscape","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cleaner","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Coffee-cup","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-coins","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Computer","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Consultancy","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-cottage","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-crane","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Custom-api","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-customer-support-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Design-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Design-3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-design","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-diamond","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-diploma","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Document-Search","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Download","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-drilling","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-engine","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-engineer","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-envelope","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Family","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-friendship","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-gift","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-graph-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-graph","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hamburger-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-handshake","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Helmet","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hot-Stone-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-hot-stone","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-idea","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Leaf","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-management","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Massage-table","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Mechanic","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Money-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-money-bag","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Money","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-oil-bottle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Physiotherapy","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Profile","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Rating","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-right-mark","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-rings","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Safe-house","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Scan","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-social-care","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Speed-Clock","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-stopwatch","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Support-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-target-2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Target","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tripod","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-truck","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-university","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-User","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Web-Portals","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-window","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-ek_line_icon","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-ek_stroke_icon","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-ekit","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-elements-kit-logo","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-degree-image","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-accordion","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-animated-flip-box","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-animated-text","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-brands","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-business-hour","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-button","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-carousel","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-Circle-progress","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-contact-form","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-countdown-timer","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-dropbar","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-faq","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-full-width-scroll","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-google-map","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-heading-style","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-help-desk","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-horizontal-timeline","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-iframe","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-image-comparison","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-image-gallery","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-image-justify","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-image-magnifier","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-image-masonry","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-inline-svg","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-instagram","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-listing","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-music-player","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-news-ticker","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-off-canvus-menu","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-parallax","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-portfolio","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-post-banner","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-post-carousel","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-post-grid","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-post-slider","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pricing-list","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-pricing-table","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-product-featured","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-product-image","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-product-recent","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-product-sale","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-product-top-rated","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-product-top-seller","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-progress-bar","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-protected-content-v2","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-protected-content-v3","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-protected-content","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-qr_code","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-scroll-button","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-search1","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-service","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-slider-image","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-social-share","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-subscribe","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-tab","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-table","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-team-join","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-team-member","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-testimonial-carousel","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-testimonial-grid","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-testimonial-quote","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-testimonial-slider","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-toggle","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-user-login","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-user-registration","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-vertical-timeline","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-video-player","library":"ekiticons"}',
			'{"value":"ekiticon ekiticon-weather","library":"ekiticons"}',
		);

		return $replace;
	}

}
