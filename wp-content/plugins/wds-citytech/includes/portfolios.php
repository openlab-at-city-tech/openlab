<?php

/**
 * Functionality related to (e)Portfolio
 *
 * Overview:
 *  - 'portfolio' is a group type, alongside 'course', 'project', and 'club'
 *  - Portfolios must have associated sites
 *  - In templates, the word 'portfolio' should always be used for faculty/staff, 'eportfolio' for
 *    students
 *  - One portfolio per user
 */

/////////////////////////
//  PORTFOLIO DETAILS  //
/////////////////////////

/**
 * Get a user's portfolio site id
 *
 * @param int $user_id Defaults to displayed user, then to current member loop user
 * @return bool
 */
function openlab_get_user_portfolio_id( $user_id = 0 ) {
	if ( !$user_id ) {
		$user_id = openlab_fallback_user();
	}

	$site_id = bp_get_user_meta( $user_id, 'portfolio_site_id', true );

	return (int) $site_id;
}

/**
 * Does a given user have a portfolio?
 *
 * @param int $user_id Defaults to displayed user, then to current member loop user
 * @return bool
 */
function openlab_user_has_portfolio( $user_id = 0 ) {
	return (bool) openlab_get_user_portfolio_id( $user_id );
}

/**
 * Echo a user's portfolio URL
 */
function openlab_user_portfolio_url( $user_id = 0 ) {
	echo openlab_get_user_portfolio_url( $user_id );
}
	/**
	 * Get a user's portfolio URL
	 *
	 * @param int $user_id Defaults to displayed user, then to current member loop user
	 * @return string URL of the portfolio
	 */
	function openlab_get_user_portfolio_url( $user_id = 0 ) {
		$site_id = openlab_get_user_portfolio_id( $user_id );

		return get_blog_option( $site_id, 'blogurl' );
	}

/**
 * Echoes the output of openlab_get_portfolio_label()
 */
function openlab_portfolio_label( $args = array() ) {
	echo openlab_get_portfolio_label( $args );
}
	/**
	 * Get the portfolio label for the given user
	 *
	 * Portfolio labels are: 'portfolio' for fac/staff, 'ePortfolio' for students
	 *
	 * @param array $args The argument array:
	 *   - 'user_id'   The numeric id of the user. If you don't provide this value, it'll fall
	 *                 back on the displayed user, and then on the current user in a member loop
	 *   - 'user_type' The type of user ('faculty', 'staff', 'student'). If you don't provide
	 *                 this value, it'll be auto-detected based on the user_id. Note that this
	 *                 value will always take precedence, even if it's wrong - so don't pass in
	 *                 a value (and let the function detect it automatically) if you're not
	 *                 totally sure what to put here.
	 *   - 'case'      'lower' or 'upper'. Note that this refers to the first letter only, which
	 *                 only matters in the case of 'portfolio'. That is, we'll return
	 *                 'ePortfolio' for upper *or* lower case.
	 *   - 'leading_a' Because 'ePortfolio' starts with a vowel and 'portfolio' does not, the
	 *                 indefinite article varies between them. If you need to use this article,
	 *                 set 'leading_a' to true, and you'll get back 'an ePortfolo' or 'a
	 *                 portfolio', as appropriate. Note that 'case' has no effect on the
	 *                 article: it's always lowercase.
	 */
	function openlab_get_portfolio_label( $args = array() ) {
		$default_user_id   = openlab_fallback_user();

		$defaults = array(
			'user_id'      => $default_user_id,
			'user_type'    => '',
			'case'         => 'lower',
			'leading_a'    => false
		);

		$r = wp_parse_args( $args, $defaults );

		if ( empty( $r['user_type'] ) ) {
			$r['user_type'] = xprofile_get_field_data( 'Account Type', $r['user_id'] );
		}

		// Sanitize
		if ( !in_array( strtolower( $r['user_type'] ), array( 'student', 'staff', 'faculty' ) ) ) {
			$r['user_type'] = 'student';
		}

		if ( 'student' == $r['user_type'] ) {
			$label = 'ePortfolio';

			if ( (bool) $r['leading_a'] ) {
				$label = 'an ' . $label;
			}
		} else {
			$label = 'upper' == $r['case'] ? 'Portfolio' : 'portfolio';

			if ( (bool) $r['leading_a'] ) {
				$label = 'a ' . $label;
			}
		}

		return $label;
	}

/////////////////////////
//    MISCELLANEOUS    //
/////////////////////////

/**
 * Echoes the URL for the portfolio creation page
 */
function openlab_portfolio_creation_url() {
	echo openlab_get_portfolio_creation_url();
}
	/**
	 * Returns the URL for the portfolio creation page
	 */
	function openlab_get_portfolio_creation_url() {
		return bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details/?type=portfolio&new=true';
	}

/**
 * Remove BPGES settings from portfolio group admin and creation screens
 */
function openlab_remove_bpges_settings_for_portfolios() {
	if ( openlab_is_portfolio() || ( bp_is_group_create() && isset( $_GET['type'] ) && 'portfolio' == $_GET['type'] ) ) {
		remove_action( 'bp_after_group_settings_admin' ,'ass_default_subscription_settings_form' );
		remove_action( 'bp_after_group_settings_creation_step' ,'ass_default_subscription_settings_form' );
	}
}
add_action( 'bp_actions', 'openlab_remove_bpges_settings_for_portfolios', 1 );

?>