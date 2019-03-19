<?php

// Check WP constant
if (!defined('ABSPATH')) {
	die;
}

// Check dependencies
if (!class_exists('WP_List_Table')) {
	require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Scans Results class
 *
 * @package WP Link Status
 * @subpackage Views
 */
class WPLNST_Views_Scans_Results extends WP_List_Table {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * External data
	 */
	protected $results;



	/**
	 * Base URL for filters
	 */
	protected $base_url;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($results) {

		// Dependencies
		wplnst_require('core', 'util-math');
		wplnst_require('core', 'util-string');

		// Parent constructor
		parent::__construct();

		// Copy results
		$this->results = $results;

		// Base link for filters
		$this->base_url = esc_url(WPLNST_Core_Plugin::get_url_scans_results($this->results->scan->id));
	}



	// Prepare columns and items
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Prepare columns and data
	 */
	function prepare_items() {

		// Columns
		$this->setup_columns();

		// Data items
		$this->setup_items();

		// Pagination
		$this->setup_pagination();
	}



	/**
	 * Setup columns
	 */
	private function setup_columns() {

		// Initialize
		$hidden = array();
		$sortable = array();

		// Column headers
		$this->_column_headers = array($this->get_columns(), $hidden, $sortable);
	}



	/**
	 * Columns array
	 */
	public function get_columns(){

		// Prepare
		$columns = array(
			'cb'				=> '<input type="checkbox" />',
			'wplnst-url' 		=> 'URL',
			'wplnst-status'		=> __('Status', 		'wplnst'),
			'wplnst-anchor'		=> __('Anchor text', 	'wplnst'),
			'wplnst-content'	=> __('Content', 		'wplnst'),
		);

		// Exception
		if ($this->results->isolated) {
			unset($columns['cb']);
			unset($columns['wplnst-content']);
		} elseif (!$this->get_columns_cb()) {
			unset($columns['cb']);
		}

		// Done
		return $columns;
	}



	/**
	 * Check if needed a cb column
	 */
	protected function get_columns_cb() {
		return false;
	}



	/**
	 * Setup data items
	 */
	private function setup_items() {

		// Dependencies
		wplnst_require('core', 'types-curl');

		// Initialize
		$status_levels = WPLNST_Core_Types::get_status_levels();
		$status_codes  = WPLNST_Core_Types::get_status_codes_raw();

		// Populate data
		$this->items = array();
		foreach ($this->results->rows as $row) {


			// Normalize identifier
			$item = array(
				'ID' 				=> $row->url_id,
				'url' 				=> $row->url,
				'url_id' 			=> $row->url_id,
				'loc_id' 			=> $row->loc_id,
				'status_level'		=> $row->status_level,
				'redirect_url'		=> $row->redirect_url,
				'redirect_url_id' 	=> $row->redirect_url_id,
				'object_id' 		=> $row->object_id,
				'object_type' 		=> $row->object_type,
				'object_field' 		=> $row->object_field,
				'link_type' 		=> $row->link_type,
				'ignored' 			=> $row->ignored,
				'unlinked' 			=> $row->unlinked,
				'nofollow'			=> $row->nofollow,
				'anchored'			=> $row->anchored,
				'attributed'		=> $row->attributed,
			);


			/* First column: URL */


			// Add unlinked flag
			$url = '<span id="wplnst-results-url-unlinked-'.$row->loc_id.'" class="wplnst-results-mark wplnst-results-mark-unlinked'.($row->unlinked? '' : ' wplnst-display-none').'">'.__('Unlinked', 'wplnst').'</span>';

			// Check link
			if ('http' == $row->scheme || 'https' == $row->scheme || 'ftp' == $row->scheme) {

				// Link to an browser resource
				$url .= '<strong><a href="'.esc_url($row->url).'" target="_blank" id="wplnst-results-url-loc-'.$row->loc_id.'" title="'.esc_url($row->url).'">'.esc_html($row->raw_url).'</a></strong>';

			// No link
			} else {

				// Unsupported linkable protocol
				$url .= '<strong><span id="wplnst-results-url-loc-'.$row->loc_id.'">'.esc_html($row->raw_url).'</span></strong>';
			}


			// Redirection
			$class_redirection = ('3' == $row->status_level && $row->redirect_url_id > 0 && !empty($row->redirect_url))? '' : 'wplnst-display-none';
			$url .= '<span id="wplnst-results-url-redir-'.$row->loc_id.'" class="'.$class_redirection.'"><br />&rarr;&nbsp;<a id="wplnst-results-url-redir-href-'.$row->loc_id.'" href="'.esc_url($row->redirect_url).'" target="_blank">'.esc_html($row->redirect_url).'</a></span>';


			// Check error
			$class_error = (!empty($row->status_code) || empty($row->curl_errno))? 'wplnst-display-none' : '';
			$url .= '<div id="wplnst-results-url-error-'.$row->loc_id.'" class="'.$class_error.'">';
			if (empty($row->status_code) && !empty($row->curl_errno)) {

				// Retrieve error type
				$curl_error = WPLNST_Core_Types_CURL::get_code_info($row->curl_errno);

				// Unknown
				if (empty($curl_error)) {
					$url .= '<strong id="wplnst-results-url-error-title-'.$row->loc_id.'">'.__('Error code ', 'wplnst').$row->curl_errno.'</strong> <span id="wplnst-results-url-error-code-'.$row->loc_id.'" class="wplnst-results-url-error-code"></span><br /><span id="wplnst-results-url-error-desc-'.$row->loc_id.'"></span>';

				// Knowed error
				} else {
					$url .= '<strong id="wplnst-results-url-error-title-'.$row->loc_id.'">'.esc_html($curl_error['title']).'</strong> <span id="wplnst-results-url-error-code-'.$row->loc_id.'" class="wplnst-results-url-error-code">'.esc_html(sprintf(__('error code %d', 'wplnst'), $row->curl_errno)).'</span><br /><span id="wplnst-results-url-error-desc-'.$row->loc_id.'">'.esc_html($curl_error['desc']).'</span>';
				}

			} else {
				$url .= '<strong id="wplnst-results-url-error-title-'.$row->loc_id.'"></strong> <span id="wplnst-results-url-error-code-'.$row->loc_id.'" class="wplnst-results-url-error-code"></span><br /><span id="wplnst-results-url-error-desc-'.$row->loc_id.'"></span>';
			}

			$url .= '</div>';


			// Check redirection error
			$error_redir = !empty($row->redirect_url_id) && empty($row->redirect_url_status) && !empty($row->redirect_curl_errno);
			$class_error_redir = $error_redir? '' : 'wplnst-display-none';
			$url .= '<div id="wplnst-results-url-error-redir-'.$row->loc_id.'" class="'.$class_error_redir.'">&rarr;&nbsp;';
			if ($error_redir) {

				// Retrieve error type
				$curl_error = WPLNST_Core_Types_CURL::get_code_info($row->redirect_curl_errno);

				// Unknown
				if (empty($curl_error)) {
					$url .= '<strong id="wplnst-results-url-error-redir-title-'.$row->loc_id.'">'.__('Error code ', 'wplnst').$row->curl_errno.'</strong> <span id="wplnst-results-url-error-redir-code-'.$row->loc_id.'" class="wplnst-results-url-error-code"></span><br /><span id="wplnst-results-url-error-redir-desc-'.$row->loc_id.'"></span>';

				// Knowed error
				} else {
					$url .= '<strong id="wplnst-results-url-error-redir-title-'.$row->loc_id.'">'.esc_html($curl_error['title']).'</strong> <span id="wplnst-results-url-error-redir-code-'.$row->loc_id.'" class="wplnst-results-url-error-code">'.esc_html(sprintf(__('error code %d', 'wplnst'), $row->redirect_curl_errno)).'</span><br /><span id="wplnst-results-url-error-redir-desc-'.$row->loc_id.'">'.esc_html($curl_error['desc']).'</span>';
				}

			} else {
				$url .= '<strong id="wplnst-results-url-error-redir-title-'.$row->loc_id.'"></strong>  <span id="wplnst-results-url-error-redir-code-'.$row->loc_id.'" class="wplnst-results-url-error-code"></span><br /><span id="wplnst-results-url-error-redir-desc-'.$row->loc_id.'"></span>';
			}

			$url .= '</div>';

			// Check relative or absolute
			$url .= '<div id="wplnst-results-url-full-'.$row->loc_id.'" class="wplnst-results-url-full'.(($row->relative || $row->absolute)? '' : ' wplnst-display-none').'">'.esc_html($row->url).'</div>';


			// Prepare data for https mark
			$is_https = ('https' == $row->scheme);

			// Prepare data for redirections
			$redirs_count = 0;
			if ($redirs = ('3' == $row->status_level && $row->redirect_url_id > 0 && !empty($row->redirect_url))) {
				$redirs_steps = @json_decode($row->redirect_steps, true);
				if (!empty($redirs_steps) && is_array($redirs_steps)) {
					$redirs_count = count($redirs_steps);
				}
			}

			// Prepare row of marks
			$mark_modified 	= '<span class="wplnst-results-mark wplnst-results-mark-modified' .($row->modified?  '' : ' wplnst-display-none').'">'.__('Modified',  'wplnst').'</span>';
			$mark_nofollow 	= '<span class="wplnst-results-mark wplnst-results-mark-nofollow' .($row->nofollow?  '' : ' wplnst-display-none').'">nofollow</span>';
			$mark_relative 	= '<span class="wplnst-results-mark wplnst-results-mark-relative' .($row->relative?  '' : ' wplnst-display-none').'">'.__('Relative',  'wplnst').'</span>';
			$mark_absolute 	= '<span class="wplnst-results-mark wplnst-results-mark-absolute' .($row->absolute?  '' : ' wplnst-display-none').'">'.__('Absolute',  'wplnst').'</span>';
			$mark_spaced 	= '<span class="wplnst-results-mark wplnst-results-mark-spaced'   .($row->spaced? 	 '' : ' wplnst-display-none').'">'.__('Spaced',    'wplnst').'</span>';
			$mark_malformed = '<span class="wplnst-results-mark wplnst-results-mark-malformed'.($row->malformed? '' : ' wplnst-display-none').'">'.__('Malformed', 'wplnst').'</span>';
			$mark_https 	= '<span class="wplnst-results-mark wplnst-results-mark-https' 	  .($is_https?  	 '' : ' wplnst-display-none').'">HTTPS</span>';
			$mark_protorel 	= '<span class="wplnst-results-mark wplnst-results-mark-protorel' .($row->protorel?  '' : ' wplnst-display-none').'">'.__('Protocol relative',  'wplnst').'</span>';
			$mark_ignored 	= '<span class="wplnst-results-mark wplnst-results-mark-ignored'  .($row->ignored? 	 '' : ' wplnst-display-none').'">'.__('Ignored',    'wplnst').'</span>';
			$mark_redirs	= '<span class="wplnst-results-mark wplnst-results-mark-redirs'	  .($redirs? 	 	 '' : ' wplnst-display-none').'">'.((!$redirs || empty($redirs_count) || 1 == $redirs_count)? '1 redirect' : $redirs_count.' redirects').'</span>';

			// Add new row checking visibility
			$mark_visible = ($row->modified || $row->nofollow || $row->relative || $row->absolute || $row->spaced || $row->malformed || $is_https || $row->protorel || $row->ignored || $redirs);
			$url .= '<div id="wplnst-results-url-marks-'.$row->loc_id.'" class="wplnst-results-url-marks'.($mark_visible? '' : ' wplnst-display-none').'">'.$mark_modified.$mark_nofollow.$mark_relative.$mark_absolute.$mark_spaced.$mark_malformed.$mark_https.$mark_protorel.$mark_ignored.$mark_redirs.'</div>';


			// Done
			$item['wplnst-url'] = '<div class="wplnst-row-url">'.$url.'</div>';



			/* Second column: status, redirection status, and time/size info */

			// Start container
			$item['wplnst-status'] = '<div class="wplnst-url-status-code">';

			// Prepare status classes
			$class_status_error = empty($row->curl_errno)?  ' wplnst-display-none' : '';
			$class_status_code  = empty($row->status_code)? ' wplnst-display-none' : '';

			// Prepare status Code
			$status_code_label = isset($status_codes[$row->status_code])? ' '.$status_codes[$row->status_code] : '';

			// Prepare rechecked mark
			$mark_rechecked = ' &nbsp; <span id="wplnst-url-status-recheck-mark-'.$row->loc_id.'" class="wplnst-results-mark wplnst-results-mark-rechecked'.($row->rechecked? '' : ' wplnst-display-none').'">Rechecked</span>';

			// Status code result
			$item['wplnst-status'] .= '<div class="wplnst-url-status-code-result"><span id="wplnst-url-status-code-0-loc-'.$row->loc_id.'" class="wplnst-url-status-code-0'.$class_status_error.'">'.__('Request error', 'wplnst').'</span><span id="wplnst-url-status-code-loc-'.$row->loc_id.'" class="wplnst-url-status-code-'.esc_attr($row->status_level).$class_status_code.'">'.esc_html($row->status_code.$status_code_label).'</span>'.$mark_rechecked.'</div>';

			// Prepare redirections status
			$redir_status_level = $redir_status_label = '';
			$status_redir = (!empty($row->redirect_url_id) && (!empty($row->redirect_url_status) || !empty($row->redirect_curl_errno)));
			if ($status_redir && !empty($row->redirect_url_status)) {
				$redir_status_level = mb_substr($row->redirect_url_status, 0, 1);
				$redir_status_label = $row->redirect_url_status.(isset($status_codes[$row->redirect_url_status])? ' '.$status_codes[$row->redirect_url_status] : '');
			}

			// Prepare redirection classes
			$class_status_redir = $status_redir? '' : ' wplnst-display-none';
			$class_status_redir_error = ($status_redir && !empty($row->redirect_curl_errno))? '' : ' wplnst-display-none';
			$class_status_redir_code  = ($status_redir && !empty($row->redirect_url_status))? '' : ' wplnst-display-none';

			// Redirection status code
			$item['wplnst-status'] .= '<div id="wplnst-url-status-code-redir-'.$row->loc_id.'" class="wplnst-url-status-code-redir'.$class_status_redir.'"><span class="wplnst-url-status-code-redir-arrow">&rarr;&nbsp;</span><span id="wplnst-url-status-code-redir-error-'.$row->loc_id.'" class="wplnst-url-status-code-0'.$class_status_redir_error.'">'.__('Request error', 'wplnst').'</span><span id="wplnst-url-status-code-redir-status-'.$row->loc_id.'" class="wplnst-url-status-code-'.esc_attr($redir_status_level).$class_status_redir_code.'">'.esc_html($redir_status_label).'</span></div>';

			// End status container
			$item['wplnst-status'] .= '</div>';

			// Time and size
			$item['wplnst-status'] .= '<div class="wplnst-url-status-info"><span id="wplnst-url-status-info-time-'.$row->loc_id.'">'.number_format_i18n($row->total_time, 3).' s</span>'.(($row->total_bytes > 0)? '<span id="wplnst-url-status-info-split-'.$row->loc_id.'" class="wplnst-url-status-info-split"> | </span><span id="wplnst-url-status-info-size-'.$row->loc_id.'">'.wplnst_format_bytes($row->total_bytes).'</span>' : '<span id="wplnst-url-status-info-split-'.$row->loc_id.'" class="wplnst-url-status-info-split wplnst-display-none"> | </span><span id="wplnst-url-status-info-size-'.$row->loc_id.'" class="wplnst-display-none"></span>').'</div>';



			/* Third column: anchor text */

			// Set text anchor
			if ('links' == $row->link_type) {

				// Prepare modified mark
				$mark_anchored = '<div id="wplnst-results-anchor-mod-'.$row->loc_id.'" class="wplnst-results-anchor-mod'.($row->anchored? '' : ' wplnst-display-none').'"><span class="wplnst-results-mark wplnst-results-mark-modified">'.__('Modified', 'wplnst').'</span></div>';

				// Anchor text
				$item['wplnst-anchor'] = '<div class="wplnst-anchor-link"><span id="wplnst-results-anchor-loc-'.$row->loc_id.'">'.esc_html($row->anchor).'</span>'.$mark_anchored.'</div>';

			// Is an image
			} elseif ('images' == $row->link_type) {

				// Image info
				$item['wplnst-anchor'] = '<div class="wplnst-anchor-image wplnst-row-dashicon"><span>'.esc_html(__('Image', 'wplnst')).'</span></div>';
			}



			/* Fourth column: Content host */

			// Check no isolated
			if (!$this->results->isolated) {

				// Default content link
				$item['wplnst-content'] = $row->object_type.' '.$row->object_id;

				// Extract identifier
				$object_id = (int) $row->object_id;

				// Column content for posts
				if ('posts' == $row->object_type) {

					// Retrieve post
					$post = get_post($object_id);

					// Check post object
					if (!empty($post) && is_object($post) && 'WP_Post' == get_class($post)) {

						// Copy object
						$item['post'] = $post;
						$item['can_edit'] = current_user_can('edit_post', $post->ID);

						// Prepare title
						$title = _draft_or_post_title($post);

						// Check edit post link
						if ($item['can_edit'] && 'trash' != $post->post_status) {

							// Copy edit post link
							$item['edit_post_link'] = get_edit_post_link($post->ID);

							// Editable post link
							$post_row = '<strong><a href="'.$item['edit_post_link'].'" title="'.esc_attr(sprintf(__( 'Edit &#8220;%s&#8221;'), $title)).'" target="_blank">'.$title.'</a></strong>';

						// Post without link
						} else {

							// Only title
							$post_row = '<strong>'.$title.'</strong>';
						}

					// Not found
					} else {

						// Not found item
						$post_row = sprintf(__('Entry %d not found', 'wplnst'), $object_id);
					}

					// Set column value
					$item['wplnst-content'] = '<div class="wplnst-content-post wplnst-row-dashicon"><span>'.$post_row.'</span></div>';

				// Column content for comments
				} elseif ('comments' == $row->object_type) {

					// Retrieve comment
					$object_id = (int) $row->object_id;
					$comment = get_comment($object_id);

					// Check comment object
					if (!empty($comment) && is_object($comment)) {

						// Copy object
						$item['comment'] = $comment;
						$item['can_edit'] = current_user_can('edit_comment', $comment->comment_ID);

						// Isolate comment author
						$comment_author = ('' === $comment->comment_author)? '' : '<strong>'.esc_html($comment->comment_author).'</strong>  &#8212; ';

						// First comment chars
						$comment_text = wplnst_crop_text($comment->comment_content, 50);

						// Check editable comment
						if ($item['can_edit']) {

							// Submitted on link
							$comment_row = '<a href="'.admin_url('comment.php?action=editcomment&c='.$comment->comment_ID).'">'.$comment_author.$comment_text.'</a>';

						// Check approved comment
						} elseif ('approved' == wp_get_comment_status($comment->comment_ID)) {

							// View comment
							$comment_row = '<a href="'.esc_url(get_comment_link($comment->comment_ID)).'" target="_blank">'.$comment_author.$comment_text.'</a>';

						// No link
						} else {

							// Only text
							$comment_row = $comment_author.$comment_text;
						}

					// Not found
					} else {

						// Not found item
						$comment_row = sprintf(__('Comment %d not found', 'wplnst'), $object_id);
					}

					// Set column value
					$item['wplnst-content'] = '<div class="wplnst-content-comment wplnst-row-dashicon"><span>'.$comment_row.'</span></div>';

				// Column content for links
				} elseif ('blogroll' == $row->object_type) {

					// Retrieve link
					$object_id = (int) $row->object_id;
					$bookmark = get_bookmark($object_id);

					// Check link object
					if (!empty($bookmark) && is_object($bookmark)) {

						// Copy object
						$item['bookmark'] = $bookmark;
						$item['can_edit'] = current_user_can('manage_links', $bookmark->link_id);

						// Prepare visible URL
						$link_url = esc_html(url_shorten($bookmark->link_url));

						// Check editable comment
						if ($item['can_edit']) {

							// Submitted on link
							$bookmark_row = '<a href="'.admin_url('link.php?action=edit&link_id='.((int) $bookmark->link_id)).'" target="_blank">'.$link_url.'</a>';

						// No link
						} else {

							// Only text
							$bookmark_row = '<a href="'.esc_url($link->link_url).'" target="_blank" target="_blank">'.$link_url.'</a>';
						}

					// Not found
					} else {

						// Not found item
						$bookmark_row = sprintf(__('Bookmark %d not found', 'wplnst'), $object_id);
					}

					// Set column value
					$item['wplnst-content'] = '<div class="wplnst-content-bookmark wplnst-row-dashicon"><span>'.$bookmark_row.'</span></div>';
				}
			}

			// Add row
			$this->items[] = $item;
		}
	}



	// Column actions
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array();
	}



	/**
	 * Handles the checkbox column output.
	 */
	protected function column_cb($item) {
		if (!$this->results->isolated) {
			$disabled = !(isset($item['post']) || isset($item['comment']) || isset($item['bookmark']));
			return sprintf('<input type="checkbox"'.($disabled? ' disabled' : ' class="wplnst-ck-loc-id"').' value="%s" />', $item['loc_id']);
		}
    }



	/**
	 * Default method to display a column
	 *
	 * @param array  $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	protected function column_default($item, $column_name) {

		// Actions for URL column
		if ('wplnst-url' == $column_name) {

			// URL actions row
			$actions = $this->column_actions_url($item);
			if (!empty($actions) && is_array($actions)) {
				return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions, $item['loc_id']));
			}

		// Actions for status column
		} elseif ('wplnst-status' == $column_name) {

			// Status actions row
			$actions = $this->column_actions_status($item);
			if (!empty($actions) && is_array($actions)) {
				return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions, $item['loc_id']));
			}

		// Actions for the link anchor
		} elseif ('wplnst-anchor' == $column_name) {

			// Check links type and available anchor
			if ('links' == $item['link_type'] && false === strpos($item['object_field'], 'custom_field_url_')) {

				// Anchor actions row
				$actions = $this->column_actions_anchor($item);
				if (!empty($actions) && is_array($actions)) {
					return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions, $item['loc_id']));
				}
			}

		// Actions for content column
		} elseif ('wplnst-content' == $column_name) {

			// Posts actions
			if ('posts' == $item['object_type']) {

				// Content post actions row
				$actions = $this->column_actions_content_posts($item);
				if (!empty($actions) && is_array($actions)) {
					return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions, $item['loc_id']));
				}

			// Comments actions
			} elseif ('comments' == $item['object_type']) {

				// Content comments actions row
				$actions = $this->column_actions_content_comments($item);
				if (!empty($actions) && is_array($actions)) {
					return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions, $item['loc_id']));
				}

			// Blogroll actions
			} elseif ('blogroll' == $item['object_type']) {

				// Content bookmarks actions row
				$actions = $this->column_actions_content_blogroll($item);
				if (!empty($actions) && is_array($actions)) {
					return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions, $item['loc_id']));
				}
			}
		}

		// Default column
		return $item[$column_name];
	}



	/**
	 * Column URL row actions
	 */
	private function column_actions_url($item) {

		// Initialize
		$actions = array();

		// For not isolated
		if (!$this->results->isolated) {

			// Check object
			if (isset($item['can_edit']) && $item['can_edit']) {

				// Add results actions
				$actions = apply_filters('wplnst_results_actions_url', $actions, $item);
			}

			// Filter by URL (for future versions)
			// $actions['wplnst-action-filter'] = '<a href="'.esc_url(WPLNST_Core_Plugin::get_url_scans_locations($this->results->scan->id, $item['url_id'])).'" class="wplnst-results-action">'.__('Filter by URL', 'wplnst').'</a>';
		}

		// Done
		return $actions;
	}



	/**
	 * Column Status row actions
	 */
	private function column_actions_status($item) {

		// Initialize
		$actions = array();

		// For not isolated
		if (!$this->results->isolated) {

			// Add results actions
			$actions = apply_filters('wplnst_results_actions_status', $actions, $item);
		}

		// Done
		return $actions;
	}



	/**
	 * Column Anchor row actions
	 */
	private function column_actions_anchor($item) {

		// Initialize
		$actions = array();

		// For not isolated
		if (!$this->results->isolated) {

			// Check editable object
			if (isset($item['can_edit']) && $item['can_edit']) {

				// Add results actions
				$actions = apply_filters('wplnst_results_actions_anchor', $actions, $item);
			}
		}

		// Done
		return $actions;
	}



	/**
	 * Column content row actions for posts
	 */
	private function column_actions_content_posts($item) {

		// Check object
		if (!isset($item['post']))
			return array();

		// Initialize
		$actions = array();
		$post = $item['post'];

		// Permissions
		$can_edit_post = $item['can_edit'];
		$post_type_object = get_post_type_object($post->post_type);

		// Check edit post action
		if ($can_edit_post && 'trash' != $post->post_status) {
			$actions['edit'] = '<a href="'.$item['edit_post_link'].'" title="'.esc_attr__('Edit this item').'" target="_blank">'.__( 'Edit' ).'</a>';
		}

		// Check delete post action
		if (current_user_can('delete_post', $post->ID)) {

			// Post in trash
			if ('trash' == $post->post_status) {
				$actions['untrash'] = "<a title='" . esc_attr__( 'Restore this item from the Trash' ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore' ) . "</a>";

			// Post to trash
			} elseif (EMPTY_TRASH_DAYS) {
				$actions['trash'] = "<a class='submitdelete' title='".esc_attr__('Move this item to the Trash')."' href='".get_delete_post_link($post->ID)."'>".__('Trash')."</a>";
			}

			// Remove Permanently
			if ('trash' == $post->post_status || !EMPTY_TRASH_DAYS) {
				$actions['delete'] = "<a class='submitdelete wplnst-remove-entry' title='".esc_attr__('Delete this item permanently')."' href='".get_delete_post_link($post->ID, '', true)."'>".__('Delete Permanently')."</a>";
			}
		}

		// Check view post action
		if ($post_type_object->public) {

			// Post title
			$title = _draft_or_post_title();

			// Not published
			if (in_array($post->post_status, array('pending', 'draft', 'future'))) {
				if ($can_edit_post) {
					$preview_link = set_url_scheme(get_permalink($post->ID));
					/** This filter is documented in wp-admin/includes/meta-boxes.php */
					$preview_link = apply_filters( 'preview_post_link', add_query_arg('preview', 'true', $preview_link ), $post );
					$actions['view'] = '<a href="' . esc_url( $preview_link ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink" target="_blank">' . __( 'Preview' ) . '</a>';
				}

			// Not in trash
			} elseif ( 'trash' != $post->post_status ) {
				$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink" target="_blank">' . __( 'View' ) . '</a>';
			}
		}

		// Done
		return $actions;
	}



	/**
	 * Column content row actions for comments
	 */
	private function column_actions_content_comments($item) {

		// Check object
		if (!isset($item['comment']))
			return array();

		// Initialize
		$actions = array();
		$comment = $item['comment'];

		// Check editable comment
		if ($item['can_edit']) {

			// Real comment status
			$the_comment_status = wp_get_comment_status($comment->comment_ID);

			// Remove and approve nonces
			$del_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "delete-comment_$comment->comment_ID" ) );
			$approve_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "approve-comment_$comment->comment_ID" ) );

			// Base URL
			$url = "comment.php?c=$comment->comment_ID";

			// All URLs
			$approve_url = 		esc_url( $url . "&action=approvecomment&$approve_nonce" );
			$unapprove_url = 	esc_url( $url . "&action=unapprovecomment&$approve_nonce" );
			$spam_url = 		esc_url( $url . "&action=spamcomment&$del_nonce" );
			$unspam_url = 		esc_url( $url . "&action=unspamcomment&$del_nonce" );
			$trash_url = 		esc_url( $url . "&action=trashcomment&$del_nonce" );
			$untrash_url = 		esc_url( $url . "&action=untrashcomment&$del_nonce" );
			$delete_url = 		esc_url( $url . "&action=deletecomment&$del_nonce" );

			// Preorder it: Edit | Approve | Spam | Trash.
			$actions = array(
				'edit' => '',
				'approvecomment' => '', 'unapprove' => '',
				'spam' => '', 'unspam' => '',
				'trash' => '', 'untrash' => '', 'delete' => ''
			);

			if ( 'approved' == $the_comment_status ) {
				$actions['unapprove'] = "<a href='$unapprove_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID:e7e7d3:action=dim-comment&amp;new=unapproved' class='vim-u vim-destructive' title='" . esc_attr__( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a>';
			} elseif ( 'unapproved' == $the_comment_status ) {
				$actions['approvecomment'] = "<a href='$approve_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID:e7e7d3:action=dim-comment&amp;new=approved' class='vim-a vim-destructive' title='" . esc_attr__( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a>';
			}

			if ( 'spam' != $the_comment_status ) {
				$actions['spam'] = "<a href='$spam_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID::spam=1' class='vim-s vim-destructive' title='" . esc_attr__( 'Mark this comment as spam' ) . "'>" . /* translators: mark as spam link */ _x( 'Spam', 'verb' ) . '</a>';
			} elseif ( 'spam' == $the_comment_status ) {
				$actions['unspam'] = "<a href='$unspam_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID:66cc66:unspam=1' class='vim-z vim-destructive'>" . _x( 'Not Spam', 'comment' ) . '</a>';
			}

			if ( 'trash' == $the_comment_status ) {
				$actions['untrash'] = "<a href='$untrash_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID:66cc66:untrash=1' class='vim-z vim-destructive'>" . __( 'Restore' ) . '</a>';
			}

			if ( 'spam' == $the_comment_status || 'trash' == $the_comment_status || !EMPTY_TRASH_DAYS ) {
				$actions['delete'] = "<a href='$delete_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID::delete=1' class='delete vim-d vim-destructive wplnst-remove-comment'>" . __( 'Delete Permanently' ) . '</a>';
			} else {
				$actions['trash'] = "<a href='$trash_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID::trash=1' class='delete vim-d vim-destructive' title='" . esc_attr__( 'Move this comment to the trash' ) . "'>" . _x( 'Trash', 'verb' ) . '</a>';
			}

			if ('spam' != $the_comment_status && 'trash' != $the_comment_status) {
				$actions['edit'] = "<a href='comment.php?action=editcomment&amp;c={$comment->comment_ID}' title='" . esc_attr__( 'Edit comment' ) . "'>". __( 'Edit' ) . '</a>';
			}
		}

		// Check approved comment to see it
		if ('approved' == wp_get_comment_status($comment->comment_ID)) {
			$actions['view'] = '<a href="'.esc_url(get_comment_link($comment->comment_ID)).'" target="_blank">'.__('View').'</a>';
		}

		/** This filter is documented in wp-admin/includes/dashboard.php */
		$actions = apply_filters('comment_row_actions', array_filter($actions), $comment);

		// Done
		return $actions;
	}



	/**
	 * Column content row actions for blogroll
	 */
	private function column_actions_content_blogroll($item) {

		// Check object
		if (!isset($item['bookmark'])) {
			return array();
		}

		// Initialize
		$actions = array();
		$bookmark = $item['bookmark'];

		// Check editable comment
		if ($item['can_edit']) {

			// Cast identifier
			$bookmark_id = (int) $bookmark->link_id;

			// Edit or remove link
			$actions['edit']  = '<a href="'.admin_url('link.php?action=edit&link_id='.$bookmark_id).'" target="_blank">'.__('Edit').'</a>';
			$actions['trash'] = '<a class="wplnst-remove-bookmark" href="'.wp_nonce_url('link.php?action=delete&link_id='.$bookmark_id, 'delete-bookmark_'.$bookmark_id).'" target="_blank">'.__('Delete').'</a>';
		}

		// Visit action
		$actions['visit'] = '<a href="'.esc_url($bookmark->link_url).'" target="_blank">'.__('Visit', 'wplnst').'</a>';

		// Done
		return $actions;
	}



	/**
	 * Generate row actions div
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param array $actions The list of actions
	 * @param bool $always_visible Whether the actions should be always visible
	 * @return string
	 */
	protected function row_actions( $actions, $loc_id = 0 ) {

		$action_count = count( $actions );
		$i = 0;

		if ( !$action_count ) {
			return '';
		}

		$out = '<div class="row-actions wplnst-row-actions wplnst-row-actions-'.$loc_id.'">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$out .= "<span class='$action'>$link$sep</span>";
		}
		$out .= '</div>';

		return $out;
	}



	// Display and pagination
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Set pagination arguments
	 */
	private function setup_pagination() {
		$this->set_pagination_args(array(
			'per_page' 		=> $this->results->isolated? 0 : $this->results->per_page,
			'total_items' 	=> $this->results->total_rows,
			'total_pages'	=> $this->results->isolated? 0 : $this->results->total_pages,
		));
	}



	/**
	 * Display the table
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display() {

		// Wrapper form
		echo '<form method="get" action="'.esc_url(remove_query_arg('paged', set_url_scheme('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))).'" id="wplnst-results" data-nonce="'.esc_attr(wp_create_nonce('wplnst-results-'.$this->results->scan->hash)).'" data-nonce-advanced-display="'.esc_attr(wp_create_nonce('wplnst-results-advanced-display')).'" data-confirm-delete-entry="'.esc_attr__('Please, confirm you want to remove this entry pressing the Ok button', 'wplnst').'" data-confirm-delete-comment="'.esc_attr__('Please, confirm you want to remove this comment pressing the Ok button', 'wplnst').'" data-confirm-delete-bookmark="'.esc_attr__('Please, confirm you want to remove this bookmark pressing the Ok button', 'wplnst').'" data-label-action-url-redir="'.esc_attr__('Apply Redirection', 'wplnst').'" data-label-server-comm-error="'.esc_attr(WPLNST_Core_Text::get_text('server_comm_error')).'" data-label-unknown-error="'.esc_attr(WPLNST_Core_Text::get_text('unknown_error')).'" data-label-select-any="'.esc_attr__('Please, select any result to proceed', 'wplnst').'" data-label-error-code="'.esc_attr__('error code', 'wplnst').'">';

		// Hidden fields
		echo '<input type="hidden" name="page" value="'.esc_attr($_GET['page']).'" />';
		echo '<input type="hidden" name="scan_id" value="'.esc_attr($this->results->scan->id).'" />';
		echo '<input type="hidden" name="context" value="results" />';
		if (isset($this->results->status_level)) echo '<input type="hidden" name="status" value="'.esc_attr($this->results->status_level).'" />';

		// Raise event at this point
		do_action('wplnst_scans_results_view_display');

		// Check isolated classes
		$extra_classes = array();
		if ($this->results->isolated) {
			$extra_classes[] = 'wplnst-isolated-table';
		}

		// Show levels menu
		$this->menu();

		// Check isolated display
		if (!$this->results->isolated) {
			$this->display_tablenav('top');
		}

		?><table class="wp-list-table <?php echo implode(' ', array_merge($this->get_table_classes(), $extra_classes)); ?>">
			<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
			<?php if (!$this->results->isolated) : ?>
			<tfoot>
				<tr>
					<?php $this->print_column_headers( false ); ?>
				</tr>
			</tfoot>
			<?php endif; ?>
			</table><?php

		// Check isolated display
		if (!$this->results->isolated) {
			$this->display_tablenav('bottom');
		}

		// Close form
		echo '</form>';
	}



	/**
	 * Generate the table navigation above or below the table
	 */
	protected function display_tablenav($which) {
		?><div class="tablenav <?php echo esc_attr($which); ?>">
			<?php if ('top' == $which) $this->filters(); ?>
			<?php $this->pagination($which); ?>
			<br class="clear" />
		</div><?php
	}



	// Menu and filters
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Display a menu based on status levels
	 */
	private function menu() {

		// Initialize
		$status_levels = WPLNST_Core_Types::get_status_levels();

		// Enum summary elements
		$levels = array();
		foreach ($this->results->scan->summary as $key => $value) {

			// Status level record
			if (0 === strpos($key, 'status_level_')) {
				$key = explode('status_level_', $key);
				if (2 == count($key)) {
					$key = $key[1];
					if ('0' == $key) {
						$levels[$key] = (isset($levels[$key])? $levels[$key] : 0) + (int) $value;
					} elseif (isset($status_levels[$key])) {
						$levels[$key] = $value;
					}
				}
			}
		}

		// Status levels menu
		$menu_levels = array();
		foreach ($status_levels as $key => $label) {
			if (in_array($key, array_keys($levels))) {
				$menu_levels[$key] = $levels[$key];
			}
		}

		// Check stored total
		$total = empty($this->results->scan->summary['status_total'])? 0 : (int) $this->results->scan->summary['status_total'];

		// Menu levels
		$menu_links = array();

		// All results
		$menu_links[] = '<a href="'.$this->base_url.'"'.($total > 0 && $this->results->is_all_results? ' class="current"' : '').'>'.__('All results', 'wplnst').' </a><span class="count">('.($this->results->is_all_results? number_format_i18n($this->results->total_rows) : number_format_i18n($total)).')</span>';

		// Request error
		if (!empty($levels['0'])) {
			$menu_links[] = '<a href="'.$this->base_url.'&status=0"'.(('0' === $this->results->status_level && !$this->results->is_search)? ' class="current"' : '').'>'.__('Request error', 'wplnst').' </a><span class="count">('.number_format_i18n($levels['0']).')</span>';
		}

		// Level results
		foreach ($menu_levels as $key => $total) {
			$menu_links[] = '<a href="'.$this->base_url.'&status='.$key.'"'.(($key == $this->results->status_level && !$this->results->is_search)? ' class="current"' : '').'>'.esc_html($key.'00s '.$status_levels[$key]).' </a><span class="count">('.number_format_i18n($total).')</span>';
		}

		// Show menu
		echo '<div id="wplnst-levels-menu" class="wplnst-clearfix'.($this->results->isolated? ' wplnst-levels-menu-isolated' : '').'"><ul class="subsubsub"><li>'.implode(' | </li><li>', $menu_links).'</li></ul>'.(('end' != $this->results->scan->status)? '<div class="alignright wplnst-aproximate-total">'.__('(counters in progress)', 'wplnst').'</div>' : '').'</div>';
	}



	/**
	 * Display a set of filters
	 */
	protected function filters() {

		// Check filters
		$fields = $this->filters_fields();
		if (empty($fields)) {
			return;
		}

		// Show menu, actions, etc.
		echo '<div id="wplnst-results-filters" class="alignleft actions'.$this->filters_classes().'">';

		// Display fields
		foreach ($fields as $key => $field) {
			echo '<select id="wplnst-filter-'.$key.'"><option value="">'.esc_html($field['title']).'</option>'.$field['options'].'</select>';
		}

		// Button and end of div
		echo '&nbsp;<input id="wplnst-filter-button" data-fields="'.implode(',', array_keys($fields)).'" data-href="'.esc_attr($this->base_url).'" class="button" type="button" value="'.__('Filter', 'wplnst').'" /></div>';
	}



	/**
	 * Additional classes
	 */
	protected function filters_classes() {
		return '';
	}



	/**
	 * Retrieve fields for basic filters
	 */
	protected function filters_fields() {

		// Basic fields
		$fields = array();


		/* Status codes filter */

		// Initialize
		$objects_types = WPLNST_Core_Types::get_objects_types();
		$status_levels = WPLNST_Core_Types::get_status_levels();
		$status_codes_raw = WPLNST_Core_Types::get_status_codes_raw();

		// Enum summary elements
		$levels = $codes = $objects = array();
		foreach ($this->results->scan->summary as $key => $value) {

			// Status codes
			if (0 === strpos($key, 'status_code_')) {
				$key = explode('status_code_', $key);
				if (2 == count($key)) {
					$key = $key[1];
					if ('0' == $key) {
						$levels['0'] = true;
					} elseif (3 == strlen($key) && isset($status_codes_raw[$key])) {
						$level = substr($key, 0, 1);
						if (isset($status_levels[$level])) {
							$levels[$level] = isset($levels[$level])? $levels[$level] + 1 : 1;
						}
						$codes[$key] = $value;
					}
				}

			// Objects match
			} elseif (0 === strpos($key, 'objects_match_')) {
				$key = explode('objects_match_', $key);
				if (2 == count($key) && in_array($key[1], array_keys($objects_types))) {
					$objects[$key[1]] = $value;
				}
			}
		}


		/* Status codes options */

		// Collect options
		$options_codes = empty($levels['0'])? '' : '<option '.((isset($this->results->status_level) && '0' === $this->results->status_level)? 'selected' : '').' value="0">'.__('Request error', 'wplnst').'</option>';
		$options_levels = array();
		foreach ($status_codes_raw as $key => $label) {
			if (isset($codes[$key])) {
				$level = substr($key, 0, 1);
				if (isset($levels[$level]) && $levels[$level] > 1 && !in_array($level, $options_levels)) {
					$options_levels[] = $level;
					$options_codes .= '<option '.((!empty($this->results->status_level) && $this->results->status_level == $level)? 'selected' : '').' value="'.$level.'">'.$level.'xx'.' '.$status_levels[$level].'</option>';
				}
				$options_codes .= '<option '.((!empty($this->results->status_code) && $this->results->status_code == $key)? 'selected' : '').' value="'.$key.'">'.$key.' '.$label.'</option>';
			}
		}

		// Add filter
		$fields['status'] = array(
			'type' => 'select',
			'title' => __('All status codes', 'wplnst'),
			'options' => $options_codes,
		);


		/* Objects options */

		// Collect custom post types
		$options_post_types = '';
		if (in_array('posts', array_keys($objects))) {
			$post_types = WPLNST_Core_Types::get_post_types();
			foreach ($post_types as $type => $name) {
				if (in_array($type, $this->results->scan->post_types)) {
					$options_post_types .= '<option '.((!empty($this->results->object_post_type) && $this->results->object_post_type == $type)? 'selected' : '').' value="posts_'.$type.'">&mdash;'.esc_html($name).'</option>';
				}
			}
		}

		// Collect options
		$objects_codes = '';
		foreach ($objects_types as $key => $value) {
			if (in_array($key, array_keys($objects))) {
				$objects_codes .= '<option '.((!empty($this->results->object_type) && $this->results->object_type == $key)? 'selected' : '').' value="'.$key.'">'.$value.'</option>';
				if ('posts' == $key) {
					$objects_codes .= $options_post_types;
				}
			}
		}

		// Check filter
		$fields['otype'] = array(
			'type' => 'select',
			'title' => __('All content', 'wplnst'),
			'options' => $objects_codes,
		);


		/* Options for link types */

		$options_ltypes = '';
		$link_types = WPLNST_Core_Types::get_link_types();
		foreach ($link_types as $link_type => $link_type_name) {
			$options_ltypes .= '<option '.((!empty($this->results->link_type) && $this->results->link_type == $link_type)? 'selected' : '').' value="'.esc_attr($link_type).'">'.esc_html($link_type_name).'</option>';
		}

		// Check filter
		$fields['ltype'] = array(
			'type' => 'select',
			'title' => __('All link types', 'wplnst'),
			'options' => $options_ltypes,
		);


		// Done
		return $fields;
	}



}