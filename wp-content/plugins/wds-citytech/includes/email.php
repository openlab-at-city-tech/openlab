<?php

/**
 * Mods related to email, including BPGES.
 */

/**
 * Put the group type in email notification subject lines
 * @param type $subject
 * @return type
 */
function openlab_group_type_in_notification_subject( $subject ) {

	if ( ! empty( $groups_template->group->id ) ) {
		$group_id = $groups_template->group->id;
	} elseif ( ! empty( $bp->groups->current_group->id ) ) {
		$group_id = $bp->groups->current_group->id;
	} else {
		return $subject;
	}

	if ( isset( $_COOKIE['wds_bp_group_type'] ) ) {
		$grouptype = $_COOKIE['wds_bp_group_type'];
	} else {
		$grouptype = groups_get_groupmeta( $group_id, 'wds_group_type' );
	}

	return str_replace( 'in the group', 'in the ' . $grouptype, $subject );
}

add_filter( 'ass_clean_subject', 'openlab_group_type_in_notification_subject' );

/**
 * Default subscription level for group emails should be All
 */
function openlab_default_group_subscription( $level ) {
	if ( ! $level ) {
		$level = 'supersub';
	}

	return $level;
}

add_filter( 'ass_default_subscription_level', 'openlab_default_group_subscription' );

/**
 * Load the bp-ass textdomain.
 *
 * We do this because `load_plugin_textdomain()` in `activitysub_textdomain()` doesn't support custom locations.
 */
function openlab_load_bpass_textdomain() {
	load_textdomain( 'bp-ass', WP_LANG_DIR . '/bp-ass-en_US.mo' );
}
add_action( 'init', 'openlab_load_bpass_textdomain', 11 );

/**
 * Use entire text of comment or blog post when sending BPGES notifications.
 *
 * @param string $content Activity content.
 * @param object $activity Activity object.
 */
function openlab_use_full_text_for_blog_related_bpges_notifications( $content, $activity ) {
	if ( 'groups' !== $activity->component ) {
		return $content;
	}

	// @todo new-style blog comments?
	if ( ! in_array( $activity->type, array( 'new_blog_post', 'new_blog_comment' ) ) ) {
		return $content;
	}

	$group_id = $activity->item_id;
	$blog_id  = openlab_get_site_id_by_group_id( $group_id );

	if ( ! $blog_id ) {
		return $content;
	}

	switch_to_blog( $blog_id );

	if ( 'new_blog_post' === $activity->type ) {
		$post    = get_post( $activity->secondary_item_id );
		$content = empty( $post->post_password ) ? $post->post_content : 'This post is password protected.';
	} elseif ( 'new_blog_comment' === $activity->type ) {
		$comment = get_comment( $activity->secondary_item_id );
		$content = $comment->comment_content;
	}

	restore_current_blog();

	return openlab_convert_chars_for_email( $content );
}
add_action( 'bp_ass_activity_notification_content', 'openlab_use_full_text_for_blog_related_bpges_notifications', 10, 2 );

/**
 * Sanitize characters used for blog post notifications.
 *
 * Sometimes things can be mangled in copy-paste from Word, etc.
 */
function openlab_convert_chars_for_email( $text ) {
	// UTF-8
	$conv = array(
		"\xC2\xA0" => '&nbsp;',
		"\xC2\xA1" => '&iexcl;',
		"\xC2\xA2" => '&cent;',
		"\xC2\xA3" => '&pound;',
		"\xC2\xA4" => '&curren;',
		"\xC2\xA5" => '&yen;',
		"\xC2\xA6" => '&brvbar;',
		"\xC2\xA7" => '&sect;',
		"\xC2\xA8" => '&uml;',
		"\xC2\xA9" => '&copy;',
		"\xC2\xAA" => '&ordf;',
		"\xC2\xAB" => '&laquo;',
		"\xC2\xAC" => '&not;',
		"\xC2\xAD" => '&shy;',
		"\xC2\xAE" => '&reg;',
		"\xC2\xAF" => '&macr;',
		"\xC2\xB0" => '&deg;',
		"\xC2\xB1" => '&plusmn;',
		"\xC2\xB2" => '&sup2;',
		"\xC2\xB3" => '&sup3;',
		"\xC2\xB4" => '&acute;',
		"\xC2\xB5" => '&micro;',
		"\xC2\xB6" => '&para;',
		"\xC2\xB7" => '&middot;',
		"\xC2\xB8" => '&cedil;',
		"\xC2\xB9" => '&sup1;',
		"\xC2\xBA" => '&ordm;',
		"\xC2\xBB" => '&raquo;',
		"\xC2\xBC" => '&frac14;',
		"\xC2\xBD" => '&frac12;',
		"\xC2\xBE" => '&frac34;',
		"\xC2\xBF" => '&iquest;',
		"\xC3\x80" => '&Agrave;',
		"\xC3\x81" => '&Aacute;',
		"\xC3\x82" => '&Acirc;',
		"\xC3\x83" => '&Atilde;',
		"\xC3\x84" => '&Auml;',
		"\xC3\x85" => '&Aring;',
		"\xC3\x86" => '&AElig;',
		"\xC3\x87" => '&Ccedil;',
		"\xC3\x88" => '&Egrave;',
		"\xC3\x89" => '&Eacute;',
		"\xC3\x8A" => '&Ecirc;',
		"\xC3\x8B" => '&Euml;',
		"\xC3\x8C" => '&Igrave;',
		"\xC3\x8D" => '&Iacute;',
		"\xC3\x8E" => '&Icirc;',
		"\xC3\x8F" => '&Iuml;',
		"\xC3\x90" => '&ETH;',
		"\xC3\x91" => '&Ntilde;',
		"\xC3\x92" => '&Ograve;',
		"\xC3\x93" => '&Oacute;',
		"\xC3\x94" => '&Ocirc;',
		"\xC3\x95" => '&Otilde;',
		"\xC3\x96" => '&Ouml;',
		"\xC3\x97" => '&times;',
		"\xC3\x98" => '&Oslash;',
		"\xC3\x99" => '&Ugrave;',
		"\xC3\x9A" => '&Uacute;',
		"\xC3\x9B" => '&Ucirc;',
		"\xC3\x9C" => '&Uuml;',
		"\xC3\x9D" => '&Yacute;',
		"\xC3\x9E" => '&THORN;',
		"\xC3\x9F" => '&szlig;',
		"\xC3\xA0" => '&agrave;',
		"\xC3\xA1" => '&aacute;',
		"\xC3\xA2" => '&acirc;',
		"\xC3\xA3" => '&atilde;',
		"\xC3\xA4" => '&auml;',
		"\xC3\xA5" => '&aring;',
		"\xC3\xA6" => '&aelig;',
		"\xC3\xA7" => '&ccedil;',
		"\xC3\xA8" => '&egrave;',
		"\xC3\xA9" => '&eacute;',
		"\xC3\xAA" => '&ecirc;',
		"\xC3\xAB" => '&euml;',
		"\xC3\xAC" => '&igrave;',
		"\xC3\xAD" => '&iacute;',
		"\xC3\xAE" => '&icirc;',
		"\xC3\xAF" => '&iuml;',
		"\xC3\xB0" => '&eth;',
		"\xC3\xB1" => '&ntilde;',
		"\xC3\xB2" => '&ograve;',
		"\xC3\xB3" => '&oacute;',
		"\xC3\xB4" => '&ocirc;',
		"\xC3\xB5" => '&otilde;',
		"\xC3\xB6" => '&ouml;',
		"\xC3\xB7" => '&divide;',
		"\xC3\xB8" => '&oslash;',
		"\xC3\xB9" => '&ugrave;',
		"\xC3\xBA" => '&uacute;',
		"\xC3\xBB" => '&ucirc;',
		"\xC3\xBC" => '&uuml;',
		"\xC3\xBD" => '&yacute;',
		"\xC3\xBE" => '&thorn;',
		"\xC3\xBF" => '&yuml;',
		// Latin Extended-A
		"\xC5\x92" => '&OElig;',
		"\xC5\x93" => '&oelig;',
		"\xC5\xA0" => '&Scaron;',
		"\xC5\xA1" => '&scaron;',
		"\xC5\xB8" => '&Yuml;',
		// Spacing Modifier Letters
		"\xCB\x86" => '&circ;',
		"\xCB\x9C" => '&tilde;',
		// General Punctuation
		"\xE2\x80\x82" => '&ensp;',
		"\xE2\x80\x83" => '&emsp;',
		"\xE2\x80\x89" => '&thinsp;',
		"\xE2\x80\x8C" => '&zwnj;',
		"\xE2\x80\x8D" => '&zwj;',
		"\xE2\x80\x8E" => '&lrm;',
		"\xE2\x80\x8F" => '&rlm;',
		"\xE2\x80\x93" => '&ndash;',
		"\xE2\x80\x94" => '&mdash;',
		"\xE2\x80\x98" => '&lsquo;',
		"\xE2\x80\x99" => '&rsquo;',
		"\xE2\x80\x9A" => '&sbquo;',
		"\xE2\x80\x9C" => '&ldquo;',
		"\xE2\x80\x9D" => '&rdquo;',
		"\xE2\x80\x9E" => '&bdquo;',
		"\xE2\x80\xA0" => '&dagger;',
		"\xE2\x80\xA1" => '&Dagger;',
		"\xE2\x80\xB0" => '&permil;',
		"\xE2\x80\xB9" => '&lsaquo;',
		"\xE2\x80\xBA" => '&rsaquo;',
		"\xE2\x82\xAC" => '&euro;',
		// Latin Extended-B
		"\xC6\x92" => '&fnof;',
		// Greek
		"\xCE\x91" => '&Alpha;',
		"\xCE\x92" => '&Beta;',
		"\xCE\x93" => '&Gamma;',
		"\xCE\x94" => '&Delta;',
		"\xCE\x95" => '&Epsilon;',
		"\xCE\x96" => '&Zeta;',
		"\xCE\x97" => '&Eta;',
		"\xCE\x98" => '&Theta;',
		"\xCE\x99" => '&Iota;',
		"\xCE\x9A" => '&Kappa;',
		"\xCE\x9B" => '&Lambda;',
		"\xCE\x9C" => '&Mu;',
		"\xCE\x9D" => '&Nu;',
		"\xCE\x9E" => '&Xi;',
		"\xCE\x9F" => '&Omicron;',
		"\xCE\xA0" => '&Pi;',
		"\xCE\xA1" => '&Rho;',
		"\xCE\xA3" => '&Sigma;',
		"\xCE\xA4" => '&Tau;',
		"\xCE\xA5" => '&Upsilon;',
		"\xCE\xA6" => '&Phi;',
		"\xCE\xA7" => '&Chi;',
		"\xCE\xA8" => '&Psi;',
		"\xCE\xA9" => '&Omega;',
		"\xCE\xB1" => '&alpha;',
		"\xCE\xB2" => '&beta;',
		"\xCE\xB3" => '&gamma;',
		"\xCE\xB4" => '&delta;',
		"\xCE\xB5" => '&epsilon;',
		"\xCE\xB6" => '&zeta;',
		"\xCE\xB7" => '&eta;',
		"\xCE\xB8" => '&theta;',
		"\xCE\xB9" => '&iota;',
		"\xCE\xBA" => '&kappa;',
		"\xCE\xBB" => '&lambda;',
		"\xCE\xBC" => '&mu;',
		"\xCE\xBD" => '&nu;',
		"\xCE\xBE" => '&xi;',
		"\xCE\xBF" => '&omicron;',
		"\xCF\x80" => '&pi;',
		"\xCF\x81" => '&rho;',
		"\xCF\x82" => '&sigmaf;',
		"\xCF\x83" => '&sigma;',
		"\xCF\x84" => '&tau;',
		"\xCF\x85" => '&upsilon;',
		"\xCF\x86" => '&phi;',
		"\xCF\x87" => '&chi;',
		"\xCF\x88" => '&psi;',
		"\xCF\x89" => '&omega;',
		"\xCF\x91" => '&thetasym;',
		"\xCF\x92" => '&upsih;',
		"\xCF\x96" => '&piv;',
		// General Punctuation
		"\xE2\x80\xA2" => '&bull;',
		"\xE2\x80\xA6" => '&hellip;',
		"\xE2\x80\xB2" => '&prime;',
		"\xE2\x80\xB3" => '&Prime;',
		"\xE2\x80\xBE" => '&oline;',
		"\xE2\x81\x84" => '&frasl;',
		// Letterlike Symbols
		"\xE2\x84\x98" => '&weierp;',
		"\xE2\x84\x91" => '&image;',
		"\xE2\x84\x9C" => '&real;',
		"\xE2\x84\xA2" => '&trade;',
		"\xE2\x84\xB5" => '&alefsym;',
		// Arrows
		"\xE2\x86\x90" => '&larr;',
		"\xE2\x86\x91" => '&uarr;',
		"\xE2\x86\x92" => '&rarr;',
		"\xE2\x86\x93" => '&darr;',
		"\xE2\x86\x94" => '&harr;',
		"\xE2\x86\xB5" => '&crarr;',
		"\xE2\x87\x90" => '&lArr;',
		"\xE2\x87\x91" => '&uArr;',
		"\xE2\x87\x92" => '&rArr;',
		"\xE2\x87\x93" => '&dArr;',
		"\xE2\x87\x94" => '&hArr;',
		// Mathematical Operators
		"\xE2\x88\x80" => '&forall;',
		"\xE2\x88\x82" => '&part;',
		"\xE2\x88\x83" => '&exist;',
		"\xE2\x88\x85" => '&empty;',
		"\xE2\x88\x87" => '&nabla;',
		"\xE2\x88\x88" => '&isin;',
		"\xE2\x88\x89" => '&notin;',
		"\xE2\x88\x8B" => '&ni;',
		"\xE2\x88\x8F" => '&prod;',
		"\xE2\x88\x91" => '&sum;',
		"\xE2\x88\x92" => '&minus;',
		"\xE2\x88\x97" => '&lowast;',
		"\xE2\x88\x9A" => '&radic;',
		"\xE2\x88\x9D" => '&prop;',
		"\xE2\x88\x9E" => '&infin;',
		"\xE2\x88\xA0" => '&ang;',
		"\xE2\x88\xA7" => '&and;',
		"\xE2\x88\xA8" => '&or;',
		"\xE2\x88\xA9" => '&cap;',
		"\xE2\x88\xAA" => '&cup;',
		"\xE2\x88\xAB" => '&int;',
		"\xE2\x88\xB4" => '&there4;',
		"\xE2\x88\xBC" => '&sim;',
		"\xE2\x89\x85" => '&cong;',
		"\xE2\x89\x88" => '&asymp;',
		"\xE2\x89\xA0" => '&ne;',
		"\xE2\x89\xA1" => '&equiv;',
		"\xE2\x89\xA4" => '&le;',
		"\xE2\x89\xA5" => '&ge;',
		"\xE2\x8A\x82" => '&sub;',
		"\xE2\x8A\x83" => '&sup;',
		"\xE2\x8A\x84" => '&nsub;',
		"\xE2\x8A\x86" => '&sube;',
		"\xE2\x8A\x87" => '&supe;',
		"\xE2\x8A\x95" => '&oplus;',
		"\xE2\x8A\x97" => '&otimes;',
		"\xE2\x8A\xA5" => '&perp;',
		"\xE2\x8B\x85" => '&sdot;',
		// Miscellaneous Technical
		"\xE2\x8C\x88" => '&lceil;',
		"\xE2\x8C\x89" => '&rceil;',
		"\xE2\x8C\x8A" => '&lfloor;',
		"\xE2\x8C\x8B" => '&rfloor;',
		"\xE2\x8C\xA9" => '&lang;',
		"\xE2\x8C\xAA" => '&rang;',
		// Geometric Shapes
		"\xE2\x97\x8A" => '&loz;',
		// Miscellaneous Symbols
		"\xE2\x99\xA0" => '&spades;',
		"\xE2\x99\xA3" => '&clubs;',
		"\xE2\x99\xA5" => '&hearts;',
		"\xE2\x99\xA6" => '&diams;'
	);

    $string = strtr( $text, $conv );

	// Unicode
	$conv = array(
		chr(128) => "&euro;",
		chr(130) => "&sbquo;",
		chr(131) => "&fnof;",
		chr(132) => "&bdquo;",
		chr(133) => "&hellip;",
		chr(134) => "&dagger;",
		chr(135) => "&Dagger;",
		chr(136) => "&circ;",
		chr(137) => "&permil;",
		chr(138) => "&Scaron;",
		chr(139) => "&lsaquo;",
		chr(140) => "&OElig;",
		chr(145) => "&lsquo;",
		chr(146) => "&rsquo;",
		chr(147) => "&ldquo;",
		chr(148) => "&rdquo;",
		chr(149) => "&bull;",
		chr(150) => "&ndash;",
		chr(151) => "&mdash;",
		chr(152) => "&tilde;",
		chr(153) => "&trade;",
		chr(154) => "&scaron;",
		chr(155) => "&rsaquo;",
		chr(156) => "&oelig;",
		chr(159) => "&yuml;",
		chr(160) => "&nbsp;",
		chr(161) => "&iexcl;",
		chr(162) => "&cent;",
		chr(163) => "&pound;",
		chr(164) => "&curren;",
		chr(165) => "&yen;",
		chr(166) => "&brvbar;",
		chr(167) => "&sect;",
		chr(168) => "&uml;",
		chr(169) => "&copy;",
		chr(170) => "&ordf;",
		chr(171) => "&laquo;",
		chr(172) => "&not;",
		chr(173) => "&shy;",
		chr(174) => "&reg;",
		chr(175) => "&macr;",
		chr(176) => "&deg;",
		chr(177) => "&plusmn;",
		chr(178) => "&sup2;",
		chr(179) => "&sup3;",
		chr(180) => "&acute;",
		chr(181) => "&micro;",
		chr(182) => "&para;",
		chr(183) => "&middot;",
		chr(184) => "&cedil;",
		chr(185) => "&sup1;",
		chr(186) => "&ordm;",
		chr(187) => "&raquo;",
		chr(188) => "&frac14;",
		chr(189) => "&frac12;",
		chr(190) => "&frac34;",
		chr(191) => "&iquest;",
		chr(192) => "&Agrave;",
		chr(193) => "&Aacute;",
		chr(194) => "&Acirc;",
		chr(195) => "&Atilde;",
		chr(196) => "&Auml;",
		chr(197) => "&Aring;",
		chr(198) => "&AElig;",
		chr(199) => "&Ccedil;",
		chr(200) => "&Egrave;",
		chr(201) => "&Eacute;",
		chr(202) => "&Ecirc;",
		chr(203) => "&Euml;",
		chr(204) => "&Igrave;",
		chr(205) => "&Iacute;",
		chr(206) => "&Icirc;",
		chr(207) => "&Iuml;",
		chr(208) => "&ETH;",
		chr(209) => "&Ntilde;",
		chr(210) => "&Ograve;",
		chr(211) => "&Oacute;",
		chr(212) => "&Ocirc;",
		chr(213) => "&Otilde;",
		chr(214) => "&Ouml;",
		chr(215) => "&times;",
		chr(216) => "&Oslash;",
		chr(217) => "&Ugrave;",
		chr(218) => "&Uacute;",
		chr(219) => "&Ucirc;",
		chr(220) => "&Uuml;",
		chr(221) => "&Yacute;",
		chr(222) => "&THORN;",
		chr(223) => "&szlig;",
		chr(224) => "&agrave;",
		chr(225) => "&aacute;",
		chr(226) => "&acirc;",
		chr(227) => "&atilde;",
		chr(228) => "&auml;",
		chr(229) => "&aring;",
		chr(230) => "&aelig;",
		chr(231) => "&ccedil;",
		chr(232) => "&egrave;",
		chr(233) => "&eacute;",
		chr(234) => "&ecirc;",
		chr(235) => "&euml;",
		chr(236) => "&igrave;",
		chr(237) => "&iacute;",
		chr(238) => "&icirc;",
		chr(239) => "&iuml;",
		chr(240) => "&eth;",
		chr(241) => "&ntilde;",
		chr(242) => "&ograve;",
		chr(243) => "&oacute;",
		chr(244) => "&ocirc;",
		chr(245) => "&otilde;",
		chr(246) => "&ouml;",
		chr(247) => "&divide;",
		chr(248) => "&oslash;",
		chr(249) => "&ugrave;",
		chr(250) => "&uacute;",
		chr(251) => "&ucirc;",
		chr(252) => "&uuml;",
		chr(253) => "&yacute;",
		chr(254) => "&thorn;",
		chr(255) => "&yuml;"
	);

	return strtr( $string, $conv );
}

// Don't allow BPGES to convert links in HTML email.
add_filter(
	'ass_clean_content',
	function( $content ) {
		remove_filter( 'ass_clean_content', 'ass_convert_links', 6 );
		return $content;
	},
	0
);

/**
 * Respect 'Hidden' site setting when BPGES sends blog-related notifications.
 *
 * @param bool   $send_it
 * @param object $activity
 * @param int    $user_id
 */
function openlab_respect_hidden_site_setting_for_bpges_notifications( $send_it, $activity, $user_id ) {
	if ( ! $send_it ) {
		return $send_it;
	}

	if ( ! in_array( $activity->type, array( 'new_blog_post', 'new_blog_comment' ) ) ) {
		return $send_it;
	}

	$group_id = $activity->item_id;

	$site_id = openlab_get_site_id_by_group_id( $group_id );
	if ( ! $site_id ) {
		return $send_it;
	}

	$site_privacy = get_blog_option( $site_id, 'blog_public' );
	if ( '-3' !== $site_privacy ) {
		return $send_it;
	}

	// Email notifications should only go to site admins.
	if ( ! is_super_admin( $user_id ) ) {
		$send_it = false;
	}

	return $send_it;
}
add_filter( 'bp_ass_send_activity_notification_for_user', 'openlab_respect_hidden_site_setting_for_bpges_notifications', 10, 3 );

/**
 * Force most kinds of content to go to weekly digests.
 */
add_filter(
	'bp_ges_add_to_digest_queue_for_user',
	function( $add, $activity, $user_id, $subscription_type ) {
		if ( 'sum' !== $subscription_type ) {
			return $add;
		}

		if ( $add ) {
			return $add;
		}

		$force = [
			'added_group_document'  => 1,
			'bbp_reply_create'      => 1, // topic creation already whitelisted
			'bp_doc_comment'        => 1,
			'bp_doc_created'        => 1,
			'bp_doc_edited'         => 1,
			'edited_group_document' => 1,
			'new_blog_comment'      => 1,
			'new_blog_post'         => 1,
		];

		if ( isset( $force[ $activity->type ] ) ) {
			$add = true;
		}

		return $add;
	},
	10,
	4
);

/**
 * Change the content type to "text/html" of the comment notification emails sent by WP.
 */
function ol_comment_notification_headers( $message_headers, $comment_id ) {
	$comment = get_comment( $comment_id );

	if ( empty( $comment ) || empty( $comment->comment_post_ID ) ) {
		return false;
	}

	$message_headers = 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\n";

	return $message_headers;
}
add_filter( 'comment_notification_headers', 'ol_comment_notification_headers', 10, 2 );
add_filter( 'comment_moderation_headers', 'ol_comment_notification_headers', 10, 2 );

/**
 * Change "<br />|" with "<br />" in the comment notification email content,
 * and use "<br />" for new line, instead of paragraphs due to the spacing between the lines.
 */
function ol_comment_notification_text( $notify_message, $comment_id ) {
	$comment = get_comment( $comment_id );

	if ( empty( $comment ) || empty( $comment->comment_post_ID ) ) {
		return false;
	}

	$post   = get_post( $comment->comment_post_ID );
	$comment_content = wp_specialchars_decode( $comment->comment_content );

	$comment_author_domain = '';
	if ( WP_Http::is_ip_address( $comment->comment_author_IP ) ) {
		$comment_author_domain = gethostbyaddr( $comment->comment_author_IP );
	}

	switch ( $comment->comment_type ) {
		case 'trackback':
			/* translators: %s: Post title. */
			$notify_message = sprintf( __( 'New trackback on your post "%s"' ), $post->post_title ) . "<br />";
			/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
			$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";
			/* translators: %s: Comment text. */
			$notify_message .= sprintf( __( 'Comment: %s' ), "<br />" . $comment_content ) . "<br /><br />";
			$notify_message .= __( 'You can see all trackbacks on this post here:' ) . "<br />";
			break;

		case 'pingback':
			/* translators: %s: Post title. */
			$notify_message = sprintf( __( 'New pingback on your post "%s"' ), $post->post_title ) . "<br />";
			/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
			$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";
			/* translators: %s: Comment text. */
			$notify_message .= sprintf( __( 'Comment: %s' ), "<br />" . $comment_content ) . "<br /><br />";
			$notify_message .= __( 'You can see all pingbacks on this post here:' ) . "<br />";
			break;

		default: // Comments.
			/* translators: %s: Post title. */
			$notify_message = sprintf( __( 'New comment on your post "%s"' ), $post->post_title ) . "<br />";
			/* translators: 1: Comment author's name, 2: Comment author's IP address, 3: Comment author's hostname. */
			$notify_message .= sprintf( __( 'Author: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Comment author email. */
			$notify_message .= sprintf( __( 'Email: %s' ), $comment->comment_author_email ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";

			if ( $comment->comment_parent && user_can( $post->post_author, 'edit_comment', $comment->comment_parent ) ) {
				/* translators: Comment moderation. %s: Parent comment edit URL. */
				$notify_message .= sprintf( __( 'In reply to: %s' ), admin_url( "comment.php?action=editcomment&c={$comment->comment_parent}#wpbody-content" ) ) . "<br />";
			}

			/* translators: %s: Comment text. */
			$notify_message .= sprintf( __( 'Comment: %s' ), "<br />" . $comment_content ) . "<br /><br />";
			$notify_message .= __( 'You can see all comments on this post here:' ) . "<br />";
			break;
	}

	$comments_url    = get_permalink( $comment->comment_post_ID ) . "#comments";
	$notify_message .= '<a href="' . $comments_url . '">' . $comments_url . '</a>' . "<br /><br />";

	$comment_url = get_comment_link( $comment );
	/* translators: %s: Comment URL. */
	$notify_message .= sprintf( __( 'Permalink: %s' ), '<a href="' . $comment_url . '">' . $comment_url . '</a>' ) . "<br />";

	if ( user_can( $post->post_author, 'edit_comment', $comment->comment_ID ) ) {
		if ( EMPTY_TRASH_DAYS ) {
			$trash_url = admin_url( "comment.php?action=trash&c={$comment_id}#wpbody-content" );

			/* translators: Comment moderation. %s: Comment action URL. */
			$notify_message .= sprintf( __( 'Trash it: %s' ), '<a href="' . $trash_url . '">' . $trash_url . '</a>' ) . "<br />";
			/* translators: Comment moderation. %s: Comment action URL. */
		} else {
			$delete_url = admin_url( "comment.php?action=delete&c={$comment_id}#wpbody-content" );

			/* translators: Comment moderation. %s: Comment action URL. */
			$notify_message .= sprintf( __( 'Delete it: %s' ), '<a href="' . $delete_url . '">' . $delete_url . '</a>' ) . "<br />";
		}

		$spam_url = admin_url( "comment.php?action=spam&c={$comment_id}#wpbody-content" );
		/* translators: Comment moderation. %s: Comment action URL. */
		$notify_message .= sprintf( __( 'Spam it: %s' ), '<a href="' . $spam_url . '">' . $spam_url . '</a>' ) . "<br />";
	}

	// Remove <p> from the message
	$notify_message = str_replace( '<p>', '', $notify_message);
	// Replace </p> with <br />
	$notify_message = str_replace( '</p>', '<br />', $notify_message);

	return $notify_message;
}
add_filter( 'comment_notification_text', 'ol_comment_notification_text', 10, 2 );

/**
 * Change "<br />|" with "<br />" in the comment held for moderation notification email content,
 * and use "<br />" for new line, instead of paragraphs due to the spacing between the lines.
 */
function ol_comment_moderation_text( $notify_message, $comment_id ) {
	global $wpdb;
	$comment = get_comment( $comment_id );

	if ( empty( $comment ) || empty( $comment->comment_post_ID ) ) {
		return false;
	}

	$post    = get_post( $comment->comment_post_ID );

	$comment_author_domain = '';
	if ( WP_Http::is_ip_address( $comment->comment_author_IP ) ) {
		$comment_author_domain = gethostbyaddr( $comment->comment_author_IP );
	}

	$comments_waiting = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'" );
	$comment_content = wp_specialchars_decode( $comment->comment_content );

	switch ( $comment->comment_type ) {
		case 'trackback':
			/* translators: %s: Post title. */
			$notify_message  = sprintf( __( 'A new trackback on the post "%s" is waiting for your approval' ), $post->post_title ) . "<br />";
			$notify_message .= get_permalink( $comment->comment_post_ID ) . "<br /><br />";
			/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
			$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";
			$notify_message .= __( 'Trackback excerpt: ' ) . "<br />" . $comment_content . "<br /><br />";
			break;

		case 'pingback':
			/* translators: %s: Post title. */
			$notify_message  = sprintf( __( 'A new pingback on the post "%s" is waiting for your approval' ), $post->post_title ) . "<br />";
			$notify_message .= get_permalink( $comment->comment_post_ID ) . "<br /><br />";
			/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
			$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";
			$notify_message .= __( 'Pingback excerpt: ' ) . "<br />" . $comment_content . "<br /><br />";
			break;

		default: // Comments.
			/* translators: %s: Post title. */
			$notify_message  = sprintf( __( 'A new comment on the post "%s" is waiting for your approval' ), $post->post_title ) . "<br />";
			$notify_message .= get_permalink( $comment->comment_post_ID ) . "<br /><br />";
			/* translators: 1: Comment author's name, 2: Comment author's IP address, 3: Comment author's hostname. */
			$notify_message .= sprintf( __( 'Author: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "<br />";
			/* translators: %s: Comment author email. */
			$notify_message .= sprintf( __( 'Email: %s' ), $comment->comment_author_email ) . "<br />";
			/* translators: %s: Trackback/pingback/comment author URL. */
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "<br />";

			if ( $comment->comment_parent ) {
				/* translators: Comment moderation. %s: Parent comment edit URL. */
				$notify_message .= sprintf( __( 'In reply to: %s' ), admin_url( "comment.php?action=editcomment&c={$comment->comment_parent}#wpbody-content" ) ) . "<br />";
			}

			/* translators: %s: Comment text. */
			$notify_message .= sprintf( __( 'Comment: %s' ), "<br />" . $comment_content ) . "<br /><br />";
			break;
	}

	$approve_url     = admin_url( "comment.php?action=approve&c={$comment_id}#wpbody-content" );
	$notify_message .= sprintf( __( 'Approve it: %s' ), '<a href="' . $approve_url . '">' . $approve_url . '</a>' ) . "<br />";

	if ( EMPTY_TRASH_DAYS ) {
		$trash_url = admin_url( "comment.php?action=trash&c={$comment_id}#wpbody-content" );

		/* translators: Comment moderation. %s: Comment action URL. */
		$notify_message .= sprintf( __( 'Trash it: %s' ), '<a href="' . $trash_url . '">' . $trash_url . '</a>' ) . "<br />";
	} else {
		$delete_url = admin_url( "comment.php?action=delete&c={$comment_id}#wpbody-content" );

		/* translators: Comment moderation. %s: Comment action URL. */
		$notify_message .= sprintf( __( 'Delete it: %s' ), '<a href="' . $delete_url . '">' . $delete_url . '</a>' ) . "<br />";
	}

	$spam_url = admin_url( "comment.php?action=spam&c={$comment_id}#wpbody-content" );
	/* translators: Comment moderation. %s: Comment action URL. */
	$notify_message .= sprintf( __( 'Spam it: %s' ), '<a href="' . $spam_url . '">' . $spam_url . '</a>' ) . "<br />";

	$notify_message .= sprintf(
		/* translators: Comment moderation. %s: Number of comments awaiting approval. */
		_n(
			'Currently %s comment is waiting for approval. Please visit the moderation panel:',
			'Currently %s comments are waiting for approval. Please visit the moderation panel:',
			$comments_waiting
		),
		number_format_i18n( $comments_waiting )
	) . "<br />";

	$moderate_url    = admin_url( 'edit-comments.php?comment_status=moderated#wpbody-content' );
	$notify_message .= '<a href="' . $moderate_url . '">' . $moderate_url . '</a>' . "<br />";

	// Remove <p> from the message
	$notify_message = str_replace( '<p>', '', $notify_message);
	// Replace </p> with <br />
	$notify_message = str_replace( '</p>', '<br />', $notify_message);

	return $notify_message;
}
add_filter( 'comment_moderation_text', 'ol_comment_moderation_text', 10, 2 );

/**
 * Adds 'Hello' and footer 'note' to comment-related emails.
 */
function openlab_comment_email_boilerplate( $content ) {
	return sprintf(
		'Hello,' . "<br /><br />" .
		'%s' .  "<br /><br />" .
		'Please note: You are receiving this message because you are an administrator or author.',
		$content

	);
}
add_filter( 'comment_moderation_text', 'openlab_comment_email_boilerplate', 20 );
add_filter( 'comment_notification_text', 'openlab_comment_email_boilerplate', 20 );

/**
 * Adds custom OL tokens to outgoing emails.
 */
add_filter(
	'bp_after_send_email_parse_args',
	function( $args ) {
		$read_reply_link = '';
		if ( ! empty( $args['tokens']['thread.url'] ) ) {
			$read_reply_link_text = '<a href="%s">Go to the post</a> to read or reply.';

			if ( ! empty( $args['activity'] ) ) {
				switch ( $args['activity']->type ) {
					case 'bp_doc_comment' :
					case 'bp_doc_created' :
					case 'bp_doc_edited' :
						$read_reply_link_text = '<a href="%s">Go to the Doc</a> to read, edit, or comment.';
					break;

					case 'added_group_document' :
						$document = new BP_Group_Documents( (string) $args['activity']->secondary_item_id );

						$args['tokens']['thread.url'] = $document->get_url( false );
					break;
				}
			}

			// Special cases where the text should not appear.
			if ( ! empty( $args['activity'] ) && in_array( $args['activity']->type, [ 'added_group_document', 'edited_group_document', 'deleted_group_document' ], true ) ) {
				$read_reply_link_text = '';
			}

			if ( $read_reply_link_text ) {
				$read_reply_link = sprintf(
					$read_reply_link_text,
					$args['tokens']['thread.url']
				);
			}
		}

		$args['tokens']['openlab.read-reply-link'] = $read_reply_link;

		return $args;
	}
);


/**
 * Filters the subject line of comment notification emails.
 */
add_filter(
	'comment_notification_subject',
	function( $subject, $comment_id ) {
	   $group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	   if ( ! $group_id ) {
		   return $subject;
	   }

	   $group = groups_get_group( $group_id );

	   $comment = get_comment( $comment_id );

	   if ( $comment->user_id ) {
		   $comment_author = bp_core_get_user_displayname( $comment->user_id );
	   } else {
		   $comment_author = $comment->comment_author;
	   }

	   $post = get_post( $comment->comment_post_ID );

	   switch ( $comment->comment_type ) {
		   case 'trackback' :
			   $base = 'A new trackback from %s on %s in %s';
		   break;

		   case 'pingback' :
			   $base = 'A new pingback from %s on %s in %s';
		   break;

		   case 'comment' :
		   default :
			   $base = 'A new comment from %s on %s in %s';
		   break;
	   }

	   return sprintf(
		   $base,
		   $comment_author,
		   $post->post_title,
		   $group->name
	   );
	},
	10,
	2
);

/**
 * Filters the subject line of comment moderation emails.
 */
add_filter(
   'comment_moderation_subject',
   function( $subject, $comment_id ) {
	   $group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	   if ( ! $group_id ) {
		   return $subject;
	   }

	   $group = groups_get_group( $group_id );

	   $comment = get_comment( $comment_id );

	   $post = get_post( $comment->comment_post_ID );

	   switch ( $comment->comment_type ) {
		   case 'trackback' :
			   $base = 'Please moderate a trackback on %s in %s';
		   break;

		   case 'pingback' :
			   $base = 'Please moderate a pingback on %s in %s';
		   break;

		   case 'comment' :
		   default :
			   $base = 'Please moderate a comment on %s in %s';
		   break;
	   }

	   return sprintf(
		   $base,
		   $post->post_title,
		   $group->name
	   );
   },
   10,
   2
);

/**
 * Appends OL footer to outgoing emails.
 */
function openlab_add_footer_to_outgoing_emails( $phpmailer ) {
   // Do nothing to HTML emails.
   if ( $phpmailer->isHTML() ) {
	   return;
   }

   // Previous check may not have worked.
   $body = $phpmailer->Body;
   if ( 0 === strpos( $body, '<' ) ) {
	   return;
   }

   $footer = '<br />' . '---------------' . '<br /><br />' .

'The OpenLab at City Tech: A place to work, learn, and share!<br />
<a href="https://openlab.citytech.cuny.edu">https://openlab.citytech.cuny.edu</a><br /><br />

Help: <a href="https://openlab.citytech.cuny.edu/blog/help/openlab-help/">https://openlab.citytech.cuny.edu/blog/help/openlab-help/</a><br />
About: <a href="https://openlab.citytech.cuny.edu/about/">https://openlab.citytech.cuny.edu/about/</a>';

   $body .= $footer;

   $phpmailer->Body = $body;
}
add_action( 'phpmailer_init', 'openlab_add_footer_to_outgoing_emails' );

function openlab_convert_email_line_breaks_to_br_tags( $phpmailer ) {
	// Ignore this with emails that are natively HTML.
	if ( $phpmailer->isHTML() ) {
		return;
	}

	// Another HTML email check.
	$body = $phpmailer->Body;
	if ( 0 === strpos( $body, '<' ) ) {
		return;
	}

	// We only need to do this if the ContentType is 'text/html'.
	if ( 'text/html' !== $phpmailer->ContentType ) {
		return;
	}

	// Bail if the text already contains line breaks.
	if ( preg_match( '/<br[ \/]*>/', $body ) ) {
		return;
	}

	$body = preg_replace( '/\n/', '<br />' . "\n", $body );
	$phpmailer->Body = $body;
}
add_action( 'phpmailer_init', 'openlab_convert_email_line_breaks_to_br_tags', 5 );

/**
 * Ensure that the summary is added to weekly as well as daily digests.
 */
add_filter( 'bpges_add_summary_to_digest', '__return_true' );

/**
 * Name on outgoing emails should never be 'WordPress'.
 */
add_filter(
	'wp_mail_from_name',
	function( $name ) {
		if ( 'WordPress' === $name ) {
			$name = 'City Tech OpenLab';
		}

		return $name;
	}
);
