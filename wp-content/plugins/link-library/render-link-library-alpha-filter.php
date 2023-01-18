<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

function RenderLinkLibraryAlphaFilter( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $searchmode ) {

	global $wpdb; // Kept with CPT update

	$generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );
	extract( $generaloptions );

	$libraryoptions = wp_parse_args( $libraryoptions, ll_reset_options( 1, 'list', 'return' ) );
	extract( $libraryoptions );

	$linkcatquery = 'SELECT ';

	$linkcatquery .= 'distinct substring(t.name, 1, 1) as catletter ';
	$linkcatquery .= 'FROM ' . $LLPluginClass->db_prefix() . 'terms t LEFT JOIN ' . $LLPluginClass->db_prefix(). 'term_taxonomy tt ON (t.term_id = tt.term_id)';
	$linkcatquery .= ' LEFT JOIN ' . $LLPluginClass->db_prefix() . 'term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ';
	$linkcatquery .= ' LEFT JOIN ' . $LLPluginClass->db_prefix() . 'posts p on (tr.object_id = p.ID and p.post_status in ("publish"';
	
	if ( $showinvisible ) {
		$linkcatquery .= ', "private"';
	}

	if ( $showuserlinks ) {
		$linkcatquery .= ', "pending"';
	}

	$linkcatquery .= ' )) ';

	$linkcatquery .= 'WHERE tt.taxonomy = "' . $generaloptions['cattaxonomy'] . '"';

	if ( !empty( $categorylist_cpt ) ) {
		$linkcatquery .= ' AND t.term_id in ( ' . $categorylist_cpt . ' )';
	}

	if ( !empty( $excludecategorylist_cpt ) ) {
		$linkcatquery .= ' AND t.term_id not in ( ' . $excludecategorylist_cpt . ' )';
	}

	if ( $hide_if_empty ) {
		$linkcatquery .= ' AND p.post_title != "" ';
	}

	$linkcatquery .= ' ORDER by catletter ASC';

	$catletters = $wpdb->get_col( $linkcatquery );

	$output = '<div class="catalphafilter">';

	$output .= '<div class="catalphafiltertitle">' . $catfilterlabel . '</div>';

	$currentcatletter = '';
	if ( isset( $_GET['catletter'] ) && 'normal' == $searchmode ) {
		if ( isset( $_GET['catletter'] ) && strlen( $_GET['catletter'] ) == 1 ) {
			$currentcatletter = sanitize_text_field( $_GET['catletter'] );
		}
	} elseif ( 'normal' == $searchmode ) {
		if ( $cat_letter_filter_autoselect ) {
			if ( !empty( $catletters ) ) {
				$currentcatletter = $catletters[0];
			}
		}
	}

	if ( isset( $_GET ) ) {
		$incomingget = $_GET;
		unset ( $incomingget['catletter'] );
		unset ( $incomingget['searchll'] );
		unset ( $incomingget['linkresultpage'] );
	}

	global $post;

	foreach ( range('A', 'Z') as $letter ) {
		if ( in_array( $letter, $catletters ) ) {
			$output .= '<div class="';
			if ( $currentcatletter == $letter ) {
				$output .= 'catalphafilterselectedletter';
			} else {
				$output .= 'catalphafilterlinkedletter';
			}

			$argumentarray = array ( 'catletter' => urlencode($letter) );
			$argumentarray = array_merge( $argumentarray, $incomingget );
			$targetaddress = esc_url( add_query_arg( $argumentarray, get_permalink( $post->ID ) ) );

			$output .= '"><a href="' . $targetaddress . '">' . $letter . '</a></div>';
		} else {
			$output .= '<div class="catalphafilteremptyletter">' . $letter . '</div>';
		}
	}

	if ( $cat_letter_filter_showalloption ) {
		$output .= '<div class="';

		if ( empty( $currentcatletter ) ) {
			$output .= 'allcatalphafilterselectedletter catalphafilterselectedletter';
		} else {
			$output .= 'allcatalphafilterlinkedletter catalphafilterlinkedletter';
		}

		$argumentarray = array ( 'catletter' => '' );
		$argumentarray = array_merge( $argumentarray, $incomingget );
		$targetaddress = esc_url( add_query_arg( $argumentarray, get_permalink( $post->ID ) ) );

		$output .= '"><a href="' . $targetaddress . '">' . __( 'ALL', 'link-library' ) . '</a></div>';
	}

	$output .= '</div>';

	$result['output'] = $output;
	$result['currentcatletter'] = $currentcatletter;

	return $result;
}