<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 



/**
 * Returns total number of accounts.
 *
 * @param object  $wpdb  WordPress database object
 *
 * @return int
 */
function zotpress_get_total_accounts($wpdb=false)
{
	if ( $wpdb === false) global $wpdb;

	$count = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress");

    return $wpdb->num_rows;
}



/**
 * Returns accounts. Can be in the form of a <select> element.
 *
 * @param obj		$wpdb				WordPress database object
 * @param boolean	$use_select		Set true if display as <select>
 * @param boolean	$select_req		Set true if <select> is required
 * @param boolean	$select_id			Set an ID for <select>
 * @param boolean	$select_name	Set a name for <select>
 * @param boolean	$select_default	Set default API User ID for <select>
 *
 * @return int
 */
function zotpress_get_accounts($wpdb=false, $use_select=false, $select_req=false, $select_id=false, $select_name=false, $select_default=false )
{
	if ( $wpdb === false) global $wpdb;

	$accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");

	if ( $accounts ):

		if ( ! $use_select )
		{
			return $accounts;
		}
		else // Display as <select>
		{
			$output = "";
			if ( ! $select_id ) $select_id = "zp-FilterByAccount";
			if ( ! $select_name ) $select_name = "zp-FilterByAccount";

			$output .= '<label for="'.$select_id.'">'.__('Account','zotpress');
			if ( $select_req ) $output .= '<span class="req">*</span>';
			$output .= '</label>';
			$output .= '<select id="'.$select_id.'" name="'.$select_name.'">';

			foreach ( $accounts as $zp_account )
			{
				// DISPLAY ACCOUNTS IN DROPDOWN
				$output .= "<option ";

				// Default, if exists
				if ( $select_default 
						&& $select_default == $zp_account->api_user_id )
					$output .= "selected='selected' ";

				// Value and option name
				$output .= "rel='".$zp_account->api_user_id."' value='".$zp_account->api_user_id."'>";
				if ( $zp_account->nickname ) $output .= $zp_account->nickname; else $output .= $zp_account->api_user_id;
				$output .= esc_html__("'s Library", "zotpress");

				$output .= "</option>\n";
			}

			return $output . "</select>\n";
		}

	endif; // if $accounts
}

?>