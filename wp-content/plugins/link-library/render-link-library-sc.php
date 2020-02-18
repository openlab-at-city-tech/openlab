<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

/* Support functions to render output of link-library shortcode */

function link_library_add_http( $url ) {
	if ( !preg_match( '~^(?:f|ht)tps?://~i', $url ) ) {
		$url = 'http://' . $url;
	}
	return $url;
}

function link_library_highlight_phrase( $str, $phrase, $tag_open = '<strong>', $tag_close = '</strong>' ) {
	if ( empty( $str ) ) {
		return '';
	}

	if ( !empty( $phrase ) ) {
		return preg_replace( '/(' . preg_quote( $phrase, '/') . '(?![^<]*>))/i', $tag_open . "\\1" . $tag_close, $str );
	}

	return $str;
}

function link_library_get_category_path( $slug ) {

	$cat_path = '';

	$term = get_term_by( 'slug', $slug, 'link_library_category' );

	if ( !empty( $term ) && $term->parent != 0 ) {
		$parent_term = get_term_by( 'id', $term->parent, 'link_library_category' );
		if ( !empty( $parent_term ) ) {
			$cat_path .= link_library_get_category_path( $parent_term->slug );
		}
	}

	$cat_path .= '/' . $slug;
	return $cat_path;
}

function link_library_get_breadcrumb_path( $slug, $rewritepage, $level = 0 ) {
	$cat_path = '';

	$term = get_term_by( 'slug', $slug, 'link_library_category' );

	if ( !empty( $term ) ) {
		$parent_term = get_term_by( 'id', $term->parent, 'link_library_category' );
		if ( !empty( $parent_term ) ) {
			$cat_path .= link_library_get_breadcrumb_path( $parent_term->slug, $rewritepage, $level + 1 ) . ' - ';
		}
	}

	$new_link = home_url() . '/' . $rewritepage . link_library_get_category_path( $slug );
	if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
		$new_link = add_query_arg( 'link_tags', $_GET['link_tags'], $new_link );
	}

	if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
		$new_link = add_query_arg( 'link_price', $_GET['link_price'], $new_link );
	}

	$cat_path .= '<a href="' . $new_link . '">' . $term->name . '</a>';

	if ( $level == 0 ) {
		$new_top_link = home_url() . '/' . $rewritepage;

		if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
			$new_top_link = add_query_arg( 'link_tags', $_GET['link_tags'], $new_top_link );
		}
		if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
			$new_top_link = add_query_arg( 'link_price', $_GET['link_price'], $new_top_link );
		}

		$cat_path = '<a href="' . $new_top_link .  '">Home</a> - ' . $cat_path;
	}

	return $cat_path;
}

function link_library_display_pagination( $previouspagenumber, $nextpagenumber, $numberofpages, $pagenumber,
										  $showonecatonly, $showonecatmode, $AJAXcatid, $settings, $pageID, $currentcatletter ) {

	$dotbelow = false;
	$dotabove = false;
	$paginationoutput = '';

	if ( isset( $_GET ) ) {
		$incomingget = $_GET;
		unset ( $incomingget['page_id'] );
		unset ( $incomingget['linkresultpage'] );
		unset ( $incomingget['cat_id'] );
		unset ( $incomingget['catletter'] );
	}

	if ( 1 < $numberofpages ) {
		$paginationoutput = '<div class="pageselector"><!-- Div Pagination -->';

		if ( 1 != $pagenumber ) {
			$paginationoutput .= '<span class="previousnextactive">';

			if ( !$showonecatonly ) {
				if ( 'AJAX' == $showonecatmode ) {
					$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('', '" . $settings . "', " . $previouspagenumber . ");return false;\" >" . __('Previous', 'link-library') . '</a>';
				} else {
					global $page_query;
					$argumentarray = array( 'linkresultpage' => $previouspagenumber );

					if ( ! empty( $currentcatletter ) ) {
						$argumentarray['catletter'] = $currentcatletter;
					}

					$argumentarray = array_merge( $argumentarray, $incomingget );
					$targetaddress = esc_url( add_query_arg( $argumentarray ) );

					$paginationoutput .= '<a href="' . $targetaddress . '">' . __( 'Previous', 'link-library' ) . '</a>';
				}
			} elseif ( $showonecatonly ) {
				if ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) {
					$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('" . $AJAXcatid . "', '" . $settings . "', " . $previouspagenumber . ");return false;\" >" . __('Previous', 'link-library') . '</a>';
				} elseif ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode || 'HTMLGETPERM' == $showonecatmode ) {
					if ( 'HTMLGET' == $showonecatmode ) {
						$argumentarray = array ( 'linkresultpage' => $previouspagenumber, 'cat_id' => $AJAXcatid );
					} elseif ( 'HTMLGETCATNAME' == $showonecatmode ) {
						$argumentarray = array ( 'linkresultpage' => $previouspagenumber, 'catname' => $AJAXcatid );
					} else {
						$argumentarray = array ( 'linkresultpage' => $previouspagenumber, 'cat' => $AJAXcatid );
					}

					$argumentarray = array_merge( $argumentarray, $incomingget );
					$targetaddress = esc_url( add_query_arg( $argumentarray ) );

					$paginationoutput .= '<a href="' . $targetaddress . '" >' . __('Previous', 'link-library') . '</a>';
				}
			}

			$paginationoutput .= '</span>';
		} else {
			$paginationoutput .= '<span class="previousnextinactive">' . __('Previous', 'link-library') . '</span>';
		}

		for ( $counter = 1; $counter <= $numberofpages; $counter++ ) {
			if ( $counter <= 2 || $counter >= $numberofpages - 1 || ( $counter <= $pagenumber + 2 && $counter >= $pagenumber - 2 ) ) {
				if ( $counter != $pagenumber ) {
					$paginationoutput .= '<span class="unselectedpage">';
				} else {
					$paginationoutput .= '<span class="selectedpage">';
				}

				if ( !$showonecatonly ) {
					if ( 'AJAX' == $showonecatmode ) {
						$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('', '" . $settings . "', " . $counter . ");return false;\" >" . $counter . '</a>';
					} else {
						$argumentarray = array( 'linkresultpage' => $counter );

						if ( ! empty( $currentcatletter ) ) {
							$argumentarray['catletter'] = $currentcatletter;
						}

						$argumentarray = array_merge( $argumentarray, $incomingget );
						$targetaddress = esc_url( add_query_arg( $argumentarray ) );

						$paginationoutput .= '<a href="' . $targetaddress . '">' . $counter . '</a>';
					}
				} elseif ( $showonecatonly ) {
					if ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) {
						$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('" . $AJAXcatid . "', '" . $settings . "', " . $counter . ");return false;\" >" . $counter . '</a>';
					} elseif ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode || 'HTMLGETPERM' == $showonecatmode ) {
						if ( 'HTMLGET' == $showonecatmode ) {
							$argumentarray = array ( 'linkresultpage' => $counter, 'cat_id' => $AJAXcatid );
						} elseif ( 'HTMLGETCATNAME' == $showonecatmode ) {
							$argumentarray = array ( 'linkresultpage' => $counter, 'catname' => $AJAXcatid );
						} else {
							$argumentarray = array ( 'linkresultpage' => $counter, 'cat' => $AJAXcatid );
						}

						$argumentarray = array_merge( $argumentarray, $incomingget );
						$targetaddress = esc_url( add_query_arg( $argumentarray ) );

						$paginationoutput .= '<a href="' . $targetaddress . '" >' . $counter . '</a>';
					}
				}

				$paginationoutput .= '</a></span>';
			}

			$dotabove = false;
			$dotbelow = false;

			if ( $counter >= 2 && $counter < $pagenumber - 2 && false == $dotbelow ) {
				$dotbelow = true;
				$paginationoutput .= '...';
			} elseif ( $counter > $pagenumber + 2 && $counter < $numberofpages - 1 && false == $dotabove ) {
				$dotabove = true;
				$paginationoutput .= '...';
			}
		}

		if ( $pagenumber != $numberofpages ) {
			$paginationoutput .= '<span class="previousnextactive">';

			if ( !$showonecatonly ) {
				if ( 'AJAX' == $showonecatmode ) {
					$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('', '" . $settings . "', " . $nextpagenumber . ");return false;\" >" . __('Next', 'link-library') . '</a>';
				} else {
					$argumentarray = array( 'page_id' => $pageID, 'linkresultpage' => $nextpagenumber );

					if ( ! empty( $currentcatletter ) ) {
						$argumentarray['catletter'] = $currentcatletter;
					}

					$argumentarray = array_merge( $argumentarray, $incomingget );
					$targetaddress = esc_url( add_query_arg( $argumentarray ) );

					$paginationoutput .= '<a href="' . $targetaddress . '">' . __( 'Next', 'link-library' ) . '</a>';
				}
			} elseif ( $showonecatonly ) {
				if ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) {
					$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('" . $AJAXcatid . "', '" . $settings . "', " . $nextpagenumber . ");return false;\" >" . __('Next', 'link-library') . '</a>';
				} elseif ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode || 'HTMLGETPERM' == $showonecatmode ) {
					$argumentarray = array ( 'page_id' => $pageID, 'linkresultpage' => $nextpagenumber );
					$argumentarray = array_merge( $argumentarray, $incomingget );
					$targetaddress = esc_url( add_query_arg( $argumentarray ) );

					$paginationoutput .= '<a href="' . $targetaddress . '" >' . __('Next', 'link-library') . '</a>';
				}

			}

			$paginationoutput .= '</span>';
		} else {
			$paginationoutput .= '<span class="previousnextinactive">' . __('Next', 'link-library') . '</span>';
		}

		$paginationoutput .= '</div><!-- Div Pagination -->';
	}

	if ( 'AJAX' == $showonecatmode ) {
		$nonce = wp_create_nonce( 'link_library_ajax_refresh' );

		$paginationoutput .= "<SCRIPT LANGUAGE=\"JavaScript\">\n";
		$paginationoutput .= "var ajaxobject;\n";
		$paginationoutput .= "if(typeof showLinkCat" . $settings . " !== 'function'){\n";
		$paginationoutput .= "window.showLinkCat" . $settings . " = function ( _incomingID, _settingsID, _pagenumber ) {\n";
		$paginationoutput .= "if (typeof(ajaxobject) != \"undefined\") { ajaxobject.abort(); }\n";

		$paginationoutput .= "\tjQuery('#contentLoading" . $settings . "').toggle();" .
		                     "jQuery.ajax( {" .
		                     "    type: 'POST', " .
		                     "    url: '" . admin_url( 'admin-ajax.php' ) . "', " .
		                     "    data: { action: 'link_library_ajax_update', " .
		                     "            _ajax_nonce: '" . $nonce . "', " .
		                     "            id : _incomingID, " .
		                     "            settings : _settingsID, " .
		                     "            ajaxupdate : true, " .
		                     "            linkresultpage: _pagenumber }, " .
		                     "    success: function( data ){ " .
		                     "            jQuery('#linklist" . $settings. "').html( data ); " .
		                     "            jQuery('#contentLoading" . $settings . "').toggle();\n" .
		                     "            } } ); ";
		$paginationoutput .= "}\n";
		$paginationoutput .= "}\n";

		$paginationoutput .= "</SCRIPT>\n\n";
	}

	return $paginationoutput;
}

/**
 *
 * Render the output of the link-library shortcode
 *
 * @param $LLPluginClass    Link Library main plugin class
 * @param $generaloptions   General Plugin Settings
 * @param $libraryoptions   Selected library settings array
 * @param $settings         Settings ID
 * @return                  List of categories output for browser
 */

function RenderLinkLibrary( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $onlycount = 'false', $parent_cat_id = 0, $level = 0, $display_children = true, $hide_children_cat_links = false, &$linkcount ) {

	$showonecatonly = '';
	$showonecatmode = '';
	$AJAXcatid = '';

	$generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );
	extract( $generaloptions );

	$libraryoptions = wp_parse_args( $libraryoptions, ll_reset_options( 1, 'list', 'return' ) );
	extract( $libraryoptions );

	remove_filter('posts_request', 'relevanssi_prevent_default_request');
	remove_filter('the_posts', 'relevanssi_query', 99);

	global $wp_query;

	if ( $level == 0 && ( ( isset( $_GET['cat_name'] ) && !empty( $_GET['cat_name'] ) ) || ( isset( $wp_query->query_vars['cat_name'] ) && !empty( $wp_query->query_vars['cat_name'] ) ) ) ) {
		if ( !empty( $_GET['cat_name'] ) ) {
			$category_entry = get_term_by( 'slug', $_GET['cat_name'], 'link_library_category', OBJECT );
		} elseif ( !empty( $wp_query->query_vars['cat_name'] ) ) {
			$last_slash_pos = strripos( $wp_query->query_vars['cat_name'], '/' );
			if ( $last_slash_pos != 0 ) {
				$cat_string = substr( $wp_query->query_vars['cat_name'], $last_slash_pos );
			} else {
				$cat_string = $wp_query->query_vars['cat_name'];
			}

			$category_entry = get_term_by( 'slug', $cat_string, 'link_library_category', OBJECT );
		}

		if ( !empty( $category_entry ) ) {
			$AJAXcatid = $category_entry->term_id;
			$parent_cat_id = $AJAXcatid;
		}
	}

	if ( 0 == $parent_cat_id && $hidechildcatlinks ) {
		$hide_children_cat_links = $hidechildcatlinks;
	}

	if ( 0 == $parent_cat_id && $hidechildcattop ) {
		$display_children = false;
	}

	/* This case will only happen if the user entered bad data in the admin page or if someone is trying to inject bad data in SQL query */
	if ( !empty( $categorylist_cpt ) ) {
		$categorylistarray = explode( ',', $categorylist_cpt );

		if ( true === array_filter( $categorylistarray, 'is_int' ) ) {
			return 'List of requested categories is invalid. Please go back to Link Library admin panel to correct.';
		}
	}

	if ( !empty( $excludecategorylist_cpt ) ) {
		$excludecategorylistarray = explode( ',', $excludecategorylist_cpt );

		if ( true === array_filter( $excludecategorylistarray, 'is_int' ) ) {
			return 'List of requested excluded categories is invalid. Please go back to Link Library admin panel to correct.';
		}
	}

	$validdirections = array( 'ASC', 'DESC' );

	$linkeditoruser = current_user_can( 'manage_options' );

	if ( $level == 0 ) {
		$output = "\n<!-- Beginning of Link Library Output -->\n\n";
	} else {
		$output = '';
	}

	$currentcategory = 1;
	$pagenumber = 1;
	$currentcatletter = '';
	$number_of_pages = 1;
	$categoryname = '';
	$mode = 'normal';

	$AJAXnocatset = false;
	if ( $showonecatonly && 'AJAX' == $showonecatmode && isset( $AJAXcatid ) && empty( $AJAXcatid ) ) {
		$AJAXnocatset = true;
	}

	$GETnocatset = false;
	if ( $showonecatonly && ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode ) ) {
		if ( 'HTMLGET' == $showonecatmode && ( !isset( $_GET['cat_id'] ) || ( isset( $_GET['cat_id'] ) && empty( $_GET['cat_id'] ) ) ) ) {
			$GETnocatset = true;
		} elseif ( 'HTMLGETSLUG' == $showonecatmode && ( !isset( $_GET['cat'] ) || ( isset( $_GET['cat'] ) && empty( $_GET['cat'] ) ) ) ) {
			$GETnocatset = true;
		} elseif ( 'HTMLGETCATNAME' == $showonecatmode && ( !isset( $_GET['catname'] ) || ( isset( $_GET['catname'] ) && empty( $_GET['catname'] ) ) ) ) {
			$GETnocatset = true;
		}

	}

	if ( $showonecatonly && 'AJAX' == $showonecatmode && isset( $AJAXcatid ) && !empty( $AJAXcatid ) && ( !isset( $_GET['searchll'] ) || empty( $_GET['searchll'] ) ) ) {
		$categorylist_cpt = $AJAXcatid;
	} elseif ($showonecatonly && 'HTMLGET' == $showonecatmode && isset( $_GET['cat_id'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
		$categorylist_cpt = intval( $_GET['cat_id'] );
		$AJAXcatid = $categorylist_cpt;
	} elseif ($showonecatonly && 'HTMLGETSLUG' == $showonecatmode && isset( $_GET['cat'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
		$categorysluglist = $_GET['cat'];
	} elseif ($showonecatonly && 'HTMLGETCATNAME' == $showonecatmode && isset( $_GET['catname'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
		$categorynamelist = $_GET['catname'];
	} elseif ( $showonecatonly && 'HTMLGETPERM' == $showonecatmode && empty( $_GET['searchll'] ) ) {
		global $wp_query;

		$categoryname = $wp_query->query_vars['cat_name'];
		$AJAXcatid = $categoryname;
		$categorysluglist = '';
		if ( isset( $_GET['cat'] ) ) {
			$categorysluglist = $_GET['cat'];
		}
	} elseif ( $showonecatonly && ( !isset( $AJAXcatid ) || empty( $AJAXcatid ) ) && !empty( $defaultsinglecat_cpt ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
		$categorylist_cpt = $defaultsinglecat_cpt;
		$AJAXcatid = $categorylist_cpt;
	} elseif ( $showonecatonly && ( !isset( $AJAXcatid ) || empty( $AJAXcatid ) ) && isset( $_GET['cat_id'] ) && !empty( $_GET['cat_id'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
		$categorylist_cpt = intval( $_GET['cat_id'] );
		$AJAXcatid = $categorylist_cpt;
		$defaultsinglecat = $AJAXcatid;
	} elseif ( $showonecatonly && ( !isset( $AJAXcatid ) || empty( $AJAXcatid ) ) && empty( $defaultsinglecat_cpt ) && empty( $_GET['searchll'] ) ) {

		$show_one_cat_query_args = array( );

		if ( $hide_if_empty ) {
			$show_one_cat_query_args['hide_empty'] = true;
		} else {
			$show_one_cat_query_args['hide_empty'] = false;
		}

		if ( !$showuserlinks && !$showinvisible && !$showinvisibleadmin ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_only_publish', 10, 3 );
		} elseif ( $showuserlinks && !$showinvisible && !$showinvisibleadmin ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_pending', 10, 3 );
		} elseif ( !$showuserlinks && ( $showinvisible || ( $showinvisibleadmin && $linkeditoruser ) ) ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft', 10, 3 );
		} elseif ( $showuserlinks && ( $showinvisible || ( $showinvisibleadmin && $linkeditoruser ) ) ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft_pending', 10, 3 );
		}

		if ( !empty( $categorylist_cpt ) ) {
			$show_one_cat_query_args['include'] = explode( ',', $categorylist_cpt );
		}

		if ( !empty( $excludecategorylist_cpt ) ) {
			$show_one_cat_query_args['exclude'] = explode( ',', $excludecategorylist_cpt );
		}

		if ( ( !empty( $categorysluglist ) || isset( $_GET['cat'] ) ) && empty( $singlelinkid ) ) {
			if ( !empty( $categorysluglist ) ) {
				$show_one_cat_query_args['slug'] = explode( ',', $categorysluglist );
			} elseif ( isset( $_GET['cat'] ) ) {
				$show_one_cat_query_args['slug'] = isset( $_GET['cat'] );
			}

		}

		if ( isset( $categoryname ) && !empty( $categoryname ) && 'HTMLGETPERM' == $showonecatmode && empty( $singlelinkid ) ) {
			$show_one_cat_query_args['slug'] = $categoryname;
		}

		if ( ( !empty( $categorynamelist ) || isset( $_GET['catname'] ) ) && empty( $singlelinkid ) ) {
			$show_one_cat_query_args['name'] = explode( ',', urldecode( $categorynamelist ) );
		}

		if ( 'name' == $order ) {
			$show_one_cat_query_args['orderby'] = 'name';
			$show_one_cat_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';
		} elseif ( 'id' == $order ) {
			$show_one_cat_query_args['orderby'] = 'id';
			$show_one_cat_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';
		}

		$show_one_cat_query_args['taxonomy'] = 'link_library_category';

		$show_one_cat_link_categories = get_terms( $show_one_cat_query_args );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_only_publish' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_pending' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft_pending' );

		//var_dump( $show_one_cat_link_categories );

		$mode = 'normal';

		if ( $debugmode ) {
			$output .= "\n<!-- AJAX Default Category Query: " . print_r( $show_one_cat_query_args, TRUE ) . "-->\n\n";
			$output .= "\n<!-- AJAX Default Category Results: " . print_r( $show_one_cat_link_categories, TRUE ) . "-->\n\n";
		}

		if ( $show_one_cat_link_categories ) {
			$categorylist_cpt = $show_one_cat_link_categories[0]->term_id;
			$AJAXcatid = $categorylist_cpt;
		}
	}

	$searchterms = '';

	if ( isset($_GET['searchll'] ) && !empty( $_GET['searchll'] ) && empty( $singlelinkid ) ) {
		$searchstring = $_GET['searchll'];		
		$searchstringcopy = $searchstring;
		$searchterms  = array();

		$offset = 0;
		while ( false !== strpos( $searchstringcopy, '"', $offset ) ) {
			if ( 0 == $offset ) {
				$offset = strpos( $searchstringcopy, '"' );
			} else {
				$endpos        = strpos( $searchstringcopy, '"', $offset + 1 );
				$searchterms[] = substr( $searchstringcopy, $offset + 1, $endpos - $offset - 2 );
				$strlength     = ( $endpos + 1 ) - ( $offset + 1 );
				$searchstringcopy  = substr_replace( $searchstringcopy, '', $offset - 1, $endpos + 2 - ( $offset ) );
				$offset        = 0;
			}
		}

		if ( ! empty( $searchstringcopy ) ) {
			$searchterms = array_merge( $searchterms, explode( " ", $searchstringcopy ) );
		}
		
		if ( !empty( $searchstring ) ) {
			$mode = 'search';
			$showlinksonclick = false;
		}
	}

	$link_count = wp_count_posts( 'link_library_links' );

	if ( isset( $link_count ) && !empty( $link_count ) && ( $link_count->publish > 0 || ( $showinvisible && $link_count->private > 0 ) || ( $showuserlinks && $link_count->pending ) ) ) {
		$currentcatletter = '';

		if ( $level == 0 && $cat_letter_filter != 'no' ) {
			require_once plugin_dir_path( __FILE__ ) . 'render-link-library-alpha-filter.php';
			$result = RenderLinkLibraryAlphaFilter( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $mode );

			$currentcatletter = $result['currentcatletter'];

			if ( 'beforelinks' == $cat_letter_filter || 'beforecatsandlinks' == $cat_letter_filter ) {
				$output .= $result['output'];
			}
		}

		$link_categories_query_args = array( );

		if ( $hide_if_empty ) {
			$link_categories_query_args['hide_empty'] = true;
		} else {
			$link_categories_query_args['hide_empty'] = false;
		}

		if ( !$showuserlinks && !$showinvisible && !$showinvisibleadmin ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_only_publish', 10, 3 );
		} elseif ( $showuserlinks && !$showinvisible && !$showinvisibleadmin ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_pending', 10, 3 );
		} elseif ( !$showuserlinks && ( $showinvisible || ( $showinvisibleadmin && $linkeditoruser ) ) ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft', 10, 3 );
		} elseif ( $showuserlinks && ( $showinvisible || ( $showinvisibleadmin && $linkeditoruser ) ) ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft_pending', 10, 3 );
		}

		if ( ( !empty( $categorylist_cpt ) || isset( $_GET['cat_id'] ) ) && empty( $singlelinkid ) && $level == 0 ) {
			$link_categories_query_args['include'] = explode( ',', $categorylist_cpt );
		}

		if ( !empty( $excludecategorylist_cpt ) && empty( $singlelinkid ) ) {
			$link_categories_query_args['exclude'] = explode( ',', $excludecategorylist_cpt );
		}

		if ( ( !empty( $categorysluglist ) || isset( $_GET['cat'] ) ) && empty( $singlelinkid ) ) {
			if ( !empty( $categorysluglist ) ) {
				$link_categories_query_args['slug'] = explode( ',', $categorysluglist );
			} elseif ( isset( $_GET['cat'] ) ) {
				$link_categories_query_args['slug'] = $_GET['cat'];
			}
			$link_categories_query_args['include'] = array();
			$link_categories_query_args['exclude'] = array();
		}

		if ( isset( $categoryname ) && !empty( $categoryname ) && 'HTMLGETPERM' == $showonecatmode && empty( $singlelinkid ) ) {
			$link_categories_query_args['slug'] = $categoryname;
		}

		if ( ( !empty( $categorynamelist ) || isset( $_GET['catname'] ) ) && empty( $singlelinkid ) ) {
			$link_categories_query_args['name'] = explode( ',', urldecode( $categorynamelist ) );
		}

		if ( 'name' == $order ) {
			$link_categories_query_args['orderby'] = 'name';
			$link_categories_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';
		} elseif ( 'id' == $order ) {
			$link_categories_query_args['orderby'] = 'id';
			$link_categories_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';
		} elseif ( 'slug' == $order ) {
			$link_categories_query_args['orderby'] = 'slug';
			$link_categories_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';
		}

		if ( isset( $AJAXcatid ) && !empty( $AJAXcatid ) ) {
			$link_categories_query_args['include'] = $AJAXcatid;
		} elseif ( empty( $link_categories_query_args['slug'] ) ) {
			$no_sub_cat = true;
			if ( !empty( $link_categories_query_args['include'] ) ) {
				foreach ( $link_categories_query_args['include'] as $include_cat ) {
					$cat_term = get_term_by( 'id', $include_cat, 'link_library_category' );
					if ( !empty( $cat_term ) ) {
						if ( $cat_term->parent != 0 && $level == 0 ) {
							$no_sub_cat = false;
						}
					}
				}
			}
			if ( $no_sub_cat ) {
				$link_categories_query_args['parent'] = $parent_cat_id;
			}
		}

		$link_categories = get_terms( 'link_library_category', $link_categories_query_args );

		remove_filter( 'get_terms', 'link_library_get_terms_filter_only_publish' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_pending' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft_pending' );

		if ( 'catlist' == $order && is_array( $link_categories ) && !empty( $link_categories_query_args['include'] ) ) {
			$temp_link_categories = $link_categories;
			$link_categories = array();
			$exploded_include_list = explode( ',', $categorylist_cpt );
			foreach ( $exploded_include_list as $sort_link_category_id ) {
				foreach ( $temp_link_categories as $temp_link_cat ) {
					if ( $sort_link_category_id == $temp_link_cat->term_id ) {
						$link_categories[] = $temp_link_cat;
						continue;
					}
				}
			}
		}

		if ( !empty( $currentcatletter ) && $cat_letter_filter != 'no' ) {
			foreach ( $link_categories as $index => $link_category ) {
				if ( substr( $link_category->name, 0, 1) != $currentcatletter ) {
					unset( $link_categories[$index] );
				}
			}
		}

		if ( $pagination && 'search' != $mode ) {
			if ($linksperpage == 0 || empty( $linksperpage ) ) {
				$linksperpage = 5;
			}

			$number_of_links = 0;
			foreach ( $link_categories as $link_category ) {
				$number_of_links += $link_category->count;
			}

			if ( $number_of_links > $linksperpage ) {
				$nextpage = true;
			} else {
				$nextpage = false;
			}

			if ( isset( $number_of_links ) ) {
				$preroundpages = $number_of_links / $linksperpage;
				$number_of_pages = ceil( $preroundpages * 1 ) / 1;
			}

			if ( isset( $_POST['linkresultpage'] ) || isset( $_GET['linkresultpage'] ) ) {

				if ( isset( $_POST['linkresultpage'] ) ) {
					$pagenumber = $_POST['linkresultpage'];
				} elseif ( isset( $_GET['linkresultpage'] ) ) {
					$pagenumber = $_GET['linkresultpage'];
				}
				$startingitem = ( $pagenumber - 1 ) * $linksperpage + 1;
			} else {
				$pagenumber = 1;
				$startingitem = 1;
			}
		}

		if ( $level == 0 ) {
			$output .= "<div id='linklist" . $settings . "' class='linklist'><!-- Div Linklist -->\n";
		}

		if ( $level == 0 && $pagination && $mode != "search" && 'BEFORE' == $paginationposition ) {
			$previouspagenumber = $pagenumber - 1;
			$nextpagenumber = $pagenumber + 1;

			$pageID = get_queried_object_id();

			if ( empty( $AJAXcatid ) && !empty( $categorysluglist ) ) {
				$AJAXcatid = $categorysluglist;
			}
			if ( empty( $AJAXcatid ) && !empty( $categorynamelist ) ) {
				$AJAXcatid = $categorynamelist;
			}

			$output .= link_library_display_pagination( $previouspagenumber, $nextpagenumber, $number_of_pages, $pagenumber, $showonecatonly, $showonecatmode, $AJAXcatid, $settings, $pageID, $currentcatletter );
		}

		if ( $level == 0 && 'search' == $mode ) {
			$output .= '<div class="resulttitle">' . __('Search Results for', 'link-library') . ' "' . esc_html( stripslashes( $_GET['searchll'] ) ) . '"</div><!-- Div search results title -->';
		}

		if ( $enablerewrite && !empty( $toppagetext ) && $parent_cat_id == 0 ) {
			$output .= '<div class="toppagetext">' . nl2br( $toppagetext ) . '</div>';
		}

		$xpath = $LLPluginClass->relativePath( dirname( __FILE__ ), ABSPATH );

		if ( !empty( $link_categories ) ) {
			foreach ( $link_categories as $link_category ) {
				if ( !empty( $maxlinks ) && is_numeric( $maxlinks ) && 0 < $maxlinks && $linkcount > $maxlinks ) {
					break;
				}

				if ( $enablerewrite && $showbreadcrumbspermalinks && $parent_cat_id != 0 && $level == 0) {
					$breadcrumb = '<div class="breadcrumb">' . link_library_get_breadcrumb_path( $link_category->slug, $rewritepage ) . '</div>';
					$output .= $breadcrumb;
				}

				if ( $pagination && 'search' != $mode && !$combineresults ) {
					if ( $linkcount + $link_category->count - 1 < $startingitem || $linkcount > $startingitem + $linksperpage - 1 ) {
						$linkcount = $linkcount + $link_category->count;
						continue;
					}
				}

				if ( !empty( $singlelinkid ) && intval( $singlelinkid ) && $linkcount > 1 ) {
					break;
				}

				$link_query_args = array( 'post_type' => 'link_library_links', 'posts_per_page' => -1 );

				if ( !$combineresults ) {
					$link_query_args['tax_query'][] =
						array(
							'taxonomy' => 'link_library_category',
							'field'    => 'term_id',
							'terms'    => $link_category->term_id,
							'include_children' => false

						);
					if ( sizeof( $link_query_args['tax_query'] ) > 1 ) {
						$link_query_args['tax_query']['relation'] = 'AND';
					}
				}

				if ( !empty( $taglistoverride ) || ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) ) {

					$tag_array = array();

					if ( ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) ) {
						$tag_array = explode( '.', $_GET['link_tags'] );
					} elseif( !empty( $taglistoverride ) ) {
						$tag_array = explode( ',', $taglistoverride );
					}

					// YL: Make this an option
					if ( !empty( $tag_array ) ) {
						$showlinksonclick = false;
					}

					if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
						$link_query_args['tax_query'][] = array(
							'taxonomy' => 'link_library_tags',
							'field' => 'slug',
							'terms' => $tag_array,
						);
					} elseif ( !empty( $taglistoverride ) ) {
						$link_query_args['tax_query'][] = array(
							'taxonomy' => 'link_library_tags',
							'field' => 'id',
							'terms' => $tag_array,
						);
					}

					if ( sizeof( $link_query_args['tax_query'] ) > 1 ) {
						$link_query_args['tax_query']['relation'] = 'AND';
					}
				}

				if ( !empty( $singlelinkid ) && intval( $singlelinkid ) ) {
					$link_query_args['p'] = $singlelinkid;
				}

				$link_query_args['post_status'] = array( 'publish' );

				if ( $showuserlinks ) {
					$link_query_args['post_status'][] = 'pending';
				}

				if ( $showinvisible || ( $showinvisibleadmin && $linkeditoruser ) ) {
					$link_query_args['post_status'][] = 'draft';
				}

				if ( $showscheduledlinks ) {
					$link_query_args['post_status'][] = 'future';
				}

				if ( !empty( $searchstring ) ) {
					add_filter( 'posts_search', 'll_expand_posts_search', 10, 2 );
					$link_query_args['s'] = $searchstring;
				}

				if ( isset( $_GET['linkname'] ) && in_array( $_GET['linkname'], array( 'ASC', 'DESC' ) ) ) {
					$linkorder = 'name';
					$linkdirection = $_GET['linkname'];
				} elseif ( isset( $_GET['linkprice'] ) && in_array( $_GET['linkprice'], array( 'ASC', 'DESC' ) ) ) {
					$linkorder = 'price';
					$linkdirection = $_GET['linkprice'];
				}

				$link_query_args['meta_query']['relation'] = 'AND';

				if ( $featuredfirst && 'random' != $linkorder ) {
					$link_query_args['meta_query']['link_featured_clause'] = array( 'key' => 'link_featured' );
					$link_query_args['orderby']['link_featured_clause'] = 'DESC';
				}

				if ( 'name' == $linkorder ) {
					$link_query_args['orderby']['title'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'id' == $linkorder ) {
					$link_query_args['orderby']['ID'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'date' == $linkorder ) {
					$link_query_args['meta_query']['link_updated_clause'] = array( 'key' => 'link_updated' );
					$link_query_args['orderby']['link_updated_clause'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'price' == $linkorder ) {
					$link_query_args['meta_query']['link_price_clause'] = array( 'key' => 'link_price' );
					$link_query_args['orderby']['link_price_clause'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'random' == $linkorder ) {
					$link_query_args['orderby'] = 'rand';
				} elseif ( 'hits' == $linkorder ) {
					$link_query_args['meta_query']['link_visits_clause'] = array( 'key' => 'link_visits', 'type' => 'numeric' );
					$link_query_args['orderby']['link_visits_clause'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'scpo' == $linkorder ) {
					$link_query_args['orderby']['menu_order'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				}

				if ( $current_user_links ) {
					$user_data = wp_get_current_user();
					$name_field_value = $user_data->display_name;

					$link_query_args['meta_query']['link_submitter_clause'] =
						array(
							'key'     => 'link_submitter',
							'value'   => $name_field_value,
							'compare' => '=',
						);
				}

				if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
					$link_query_args['meta_query'][] =
						array(
							'key'     => 'link_price',
							'value'   => floatval( 0.0 ),
							'compare' => '=',
						);
				}

				if ( isset( $_GET['link_letter'] ) && !empty( $_GET['link_letter'] ) ) {
					$link_query_args['link_starts_with'] = $_GET['link_letter'];
				}

				if ( true == $debugmode ) {
					$linkquerystarttime = microtime ( true );
				}

				if ( $combineresults && !empty( $maxlinks ) && 0 < intval( $maxlinks ) ) {
					$link_query_args['posts_per_page'] = intval ( $maxlinks );
				} elseif ( !empty( $maxlinkspercat ) && 0 < intval( $maxlinkspercat ) ) {
					$link_query_args['posts_per_page'] = intval ( $maxlinkspercat );
				}

				$the_link_query = new WP_Query( $link_query_args );

				if ( $debugmode ) {
					$output .= "\n<!-- Link Query: " . print_r( $link_query_args, TRUE ) . "-->\n\n";
					$output .= "\n<!-- Link Results: " . print_r( $the_link_query, TRUE ) . "-->\n\n";
					$output .= "\n<!-- Link Query Execution Time: " . ( microtime( true ) - $linkquerystarttime ) . "-->\n\n";
				}

				if ( $debugmode ) {
					$output .= '<!-- showonecatmode: ' . $showonecatonly . ', AJAXnocatset: ' . $AJAXnocatset . ', nocatonstartup: ' . $nocatonstartup . '-->';
				}

				$child_cat_params = array( 'taxonomy' => 'link_library_category', 'child_of' => $link_category->term_id );

				if ( $hide_if_empty ) {
					$child_cat_params['hide_empty'] = true;
				} else {
					$child_cat_params['hide_empty'] = false;
				}

				$childcategories = get_terms( $child_cat_params );

				$cat_has_children = false;
				if ( !is_wp_error( $childcategories ) && !empty( $childcategories ) ) {
					$cat_has_children = true;

					$children_have_links = false;
				}

				// Display links
				if ( ( $the_link_query->found_posts && $showonecatonly && ( ( 'AJAX' == $showonecatmode && $AJAXnocatset ) || ( 'AJAX' != $showonecatmode && $GETnocatset ) ) && $nocatonstartup && !isset( $_GET['searchll'] ) ) || ( 0 == $the_link_query->found_posts && $nocatonstartup && empty( $_GET['searchll'] ) ) ) {
					$output .= "<div id='linklist" . $settings . "' class='linklist'>\n";
					$output .= '</div><!-- Div empty list -->';
				} elseif ( ( $the_link_query->found_posts || !$hide_if_empty || $cat_has_children ) ) {
					if ( ( $the_link_query->have_posts() || !$hide_if_empty || $cat_has_children ) && ( empty( $maxlinks ) || 0 == $maxlinks | $linkcount <= $maxlinks ) ) {
						$current_cat_output = '';
						$start_link_count = $linkcount;
						if ( ! $combineresults ) {
							$currentcategoryid = $link_category->term_id;
							$current_cat_output .= '<div class="LinkLibraryCat LinkLibraryCat' . $currentcategoryid . ( $level == 0 ? '' : ' childlevel'). ' level' . $level .'"><!-- Div Category -->';

							$catlink = '';
							$cattext = '';
							$catenddiv = '';

							if ( 1 == $catlistwrappers && !empty( $beforecastlist1 ) ) {
								$current_cat_output .= '<div class="' . $beforecatlist1 . '">';
							} else if ( $catlistwrappers == 2 && !empty( $beforecatlist2 ) && !empty( $beforecatlist1 ) ) {
								$remainder = $currentcategory % $catlistwrappers;
								switch ( $remainder ) {

									case 0:
										$current_cat_output .= '<div class="' . $beforecatlist2 . '">';
										break;

									case 1:
										$current_cat_output .= '<div class="' . $beforecatlist1 . '">';
										break;
								}
							} else if ( 3 == $catlistwrappers && !empty( $beforecatlist3 ) && !empty( $beforecatlist2 ) && !empty( $beforecatlist1 )) {
								$remainder = $currentcategory % $catlistwrappers;
								switch ( $remainder ) {

									case 0:
										$current_cat_output .= '<div class="' . $beforecatlist3 . '">';
										break;

									case 2:
										$current_cat_output .= '<div class="' . $beforecatlist2 . '">';
										break;

									case 1:
										$current_cat_output .= '<div class="' . $beforecatlist1 . '">';
										break;
								}
							}

							// Display the category name
							if ( !$hidecategorynames || empty( $hidecategorynames ) ) {
								$caturl = get_term_meta( $link_category->term_id, 'linkcaturl', true );

								if ( $catanchor ) {
									$cattext = '<div id="' . $link_category->slug . '"><!-- Div Category Name -->';
								} else {
									$cattext = '';
								}

								if ( !$divorheader ) {
									if ( 'search' == $mode ) {
										foreach ( $searchterms as $searchterm ) {
											$link_category->name = link_library_highlight_phrase( $link_category->name, $searchterm, '<span class="highlight_word">', '</span>' );
										}
									}

									$catlink = '<div class="' . $catnameoutput . '"><!-- Div Cat Name -->';

									if ( 'right' == $catdescpos || 'aftercatname' == $catdescpos || 'aftertoplevelcatname' == $catdescpos || empty( $catdescpos ) ) {
										if ( !empty( $caturl ) ) {
											$catlink .= '<a href="' . link_library_add_http( $caturl ) . '" ';

											if ( !empty( $linktarget ) )
												$catlink .= ' target="' . $linktarget . '"';

											$catlink .= '>';
										} /* elseif ( $catlinkspermalinksmode ) {
											var_dump( 'Generating cat link' );
										} */
										$catlink .= $link_category->name;
										if ( !empty( $caturl ) ) {
											$catlink .= '</a>';
										}
									}

									if ( $showcategorydesclinks ) {
										$catlink .= '<span class="linklistcatnamedesc">';
										$linkitem['description'] = str_replace( '[', '<', $link_category->description );
										$linkitem['description'] = str_replace( ']', '>', $linkitem['description'] );
										$catlink .= $linkitem['description'];
										$catlink .= '</span>';
									}

									if ( 'left' == $catdescpos ) {
										if ( !empty( $caturl ) ) {
											$catlink .= '<a href="' . link_library_add_http( $caturl ) . '" ';

											if ( !empty( $linktarget ) )
												$catlink .= ' target="' . $linktarget . '"';

											$catlink .= '>';
										}
										$catlink .= $link_category->name;
										if ( !empty( $caturl ) ) {
											$catlink .= '</a>';
										}
									}

									if ( $showlinksonclick && $the_link_query->found_posts > 0 ) {
										$catlink .= '<span class="expandlinks" id="LinksInCat' . $link_category->term_id . '">';
										$catlink .= '<img class="arrow-down" src="';

										if ( !empty( $expandiconpath ) ) {
											$catlink .= $expandiconpath;
										} else {
											$catlink .= plugins_url( 'icons/expand-32.png', __FILE__ );
										}

										$catlink .= '" />';
										$catlink .= '<img class="arrow-up" src="';

										if ( !empty( $expandiconpath ) ) {
											$catlink .= $expandiconpath;
										} else {
											$catlink .= plugins_url( 'icons/collapse-32.png', __FILE__ );
										}

										$catlink .= '" />';
										$catlink .= '</span>';
									}

									$catlink .= '</div><!-- DivOrHeader -->';
								} else if ( $divorheader ) {
									if ( 'search' == $mode ) {
										foreach ( $searchterms as $searchterm ) {
											$link_category->name = link_library_highlight_phrase( $link_category->name, $searchterm, '<span class="highlight_word">', '</span>' );
										}
									}

									$catlink = '<'. $catnameoutput . '>';

									if ( 'right' == $catdescpos || 'aftercatname' == $catdescpos || 'aftertoplevelcatname' == $catdescpos || empty( $catdescpos ) ) {
										if ( !empty( $caturl ) ) {
											$catlink .= '<a href="' . link_library_add_http( $caturl ). '" ';

											if ( !empty( $linktarget ) )
												$catlink .= ' target="' . $linktarget . '"';

											$catlink .= '>';
										} elseif ( $catlinkspermalinksmode && !empty( $rewritepage ) ) {
											$cat_path = link_library_get_category_path( $link_category->slug );

											if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
												$cat_path = add_query_arg( 'link_tags', $_GET['link_tags'], $cat_path );
											}

											if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
												$cat_path = add_query_arg( 'link_price', $_GET['link_price'], $cat_path );
											}

											$catlink .= '<a href="' . site_url() . '/' . $rewritepage . $cat_path . '">';
										}
										$catlink .= $link_category->name;
										if ( !empty( $caturl ) || ( $catlinkspermalinksmode && !empty( $rewritepage ) ) ) {
											$catlink .= '</a>';
										}
									}

									if ( $showcategorydesclinks && ( 'left' == $catdescpos || 'right' == $catdescpos ) ) {
										$catlink .= '<span class="linklistcatnamedesc">';
										$linkitem['description'] = str_replace( '[', '<', $link_category->description );
										$linkitem['description'] = str_replace(']', '>', $linkitem['description'] );
										$catlink .= $linkitem['description'];
										$catlink .= '</span>';
									}

									if ( 'left' == $catdescpos ) {
										if ( !empty( $caturl ) ) {
											$catlink .= '<a href="' . link_library_add_http( $caturl ) . '" ';

											if ( !empty( $linktarget ) )
												$catlink .= ' target="' . $linktarget . '"';

											$catlink .= '>';
										}
										$catlink .= $link_category->name;
										if ( !empty( $caturl ) ) {
											$catlink .= '</a>';
										}
									}

									if ( $showlinksonclick && $the_link_query->found_posts > 0 ) {
										$catlink .= '<span class="expandlinks" id="LinksInCat' . $link_category->term_id . '">';
										$catlink .= '<img class="arrow-down" src="';

										if ( !empty( $expandiconpath ) ) {
											$catlink .= $expandiconpath;
										} else {
											$catlink .= plugins_url( 'icons/expand-32.png', __FILE__ );
										}

										$catlink .= '" />';

										$catlink .= '<img class="arrow-up" src="';

										if ( !empty( $expandiconpath ) ) {
											$catlink .= $expandiconpath;
										} else {
											$catlink .= plugins_url( 'icons/collapse-32.png', __FILE__ );
										}

										$catlink .= '" />';
										$catlink .= '</span>';
									}

									$catlink .= '</' . $catnameoutput . '>';
								}

								if ($catanchor) {
									$catenddiv = '</div><!-- Div Category Name -->';
								} else {
									$catenddiv = '';
								}
							}

							$current_cat_output .= $cattext . $catlink . $catenddiv;

							// YL: Add option to control this
							//		if ( 0 != $parent_cat_id ) {
							if ( $showcategorydesclinks && ( 'aftercatname' == $catdescpos || ( 'aftertoplevelcatname' == $catdescpos && $level == 0 ) ) ) {
								$current_cat_output .= '<div class="parentcatdesc">' . nl2br( $link_category->description ) . '</div>';
							}
							//	}

							if ( $showlinksonclick ) {
								$current_cat_output .= '<div class="LinksInCat' . $currentcategoryid . ' LinksInCat"><!-- Div show links on click -->';
							}
						}

						if ( !empty( $beforefirstlink ) && $the_link_query->found_posts > 0 ) {
							$current_cat_output .= stripslashes( $beforefirstlink );
						}

						$display_as_table = 'false';

						if ( is_bool( $displayastable ) && $displayastable ) {
							$display_as_table = 'true';
						} elseif( is_bool( $displayastable ) && !$displayastable ) {
							$display_as_table = 'false';
						} elseif ( in_array( $displayastable, array( 'true', 'false', 'nosurroundingtags' ) ) ) {
							$display_as_table = $displayastable;
						}

						if ( $display_as_table === 'true' && ( ! $combineresults || ( $combineresults && $linkcount > 0 ) ) ) {
							$catstartlist = "\n\t<table class='linklisttable'>\n";
							if ( $showcolumnheaders ) {
								if ( !empty( $columnheaderoverride ) && !$allowcolumnsorting ) {
									$catstartlist .= '<div class="linklisttableheaders"><tr>';

									$columnheaderarray = explode( ',', $columnheaderoverride );
									foreach( $columnheaderarray as $columnheader ) {
										if ( !empty( $columnheader ) ) {
											$catstartlist .= '<th><div class="linklistcolumnheader">' . $columnheader . '</div></th>';
										}
									}

									$catstartlist .= "</tr></div>\n";
								} elseif ( $allowcolumnsorting ) {
									$sorting_labels = array( 2 => 'linkname', 16 => 'linkprice' );
									$settings_sort_label = array( 2 => 'name' );
									$activation_variables = array( 1 => 'show_images', 2 => 'showname', 3 => 'showdate', 4 => 'showdescription',
									                               5 => 'shownotes', 6 => 'show_rss', 7 => 'displayweblink', 8 => 'showtelephone',
									                               9 => 'showemail', 10 => 'showlinkhits', 11 => 'showrating', 12 => 'showlargedescription',
									                               13 => 'showsubmittername', 14 => 'showcatdesc', 15 => 'showlinktags', 16 => 'showlinkprice',
																   17 => 'showcatname' );

									$default_labels = array( 1 => __( 'Image', 'link-library' ), 2 => __( 'Name', 'link-library' ),
									                         3 => __( 'Date', 'link-library' ), 4 => __( 'Description', 'link-library'),
									                         5 => __( 'Notes', 'link-library'), 6 => __( 'RSS', 'link-library' ),
									                         7 => __( 'Web Link', 'link-library' ), 8 => __( 'Telephone', 'link-library'),
									                         9 => __( 'E-mail', 'link-library' ), 10 => __( 'Hits', 'link-library' ),
									                         11 => __( 'Rating', 'link-library' ), 12 => __( 'Large Description', 'link-library' ),
									                         13 => __( 'Submitter Name', 'link-library' ), 14 => __( 'Category Description', 'link-library' ),
									                         15 => __( 'Tags', 'link-library' ), 16 => __( 'Price', 'link-library'),
															 17 => __( 'Category Name', 'link-library' ) );

									if ( empty( $dragndroporder ) ) {
										$dragndroporder = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17';
									}

									$dragndroparray = explode( ',', $dragndroporder );

									$new_entries = array( '13', '14', '15', '16', '17' );

									foreach ( $new_entries as $new_entry ) {
										if ( !in_array( $new_entry, $dragndroparray ) ) {
											$dragndroparray[] = $new_entry;
										}
									}

									$catstartlist .= '<div class="linklisttableheaders"><tr>';
									$columnheaderarray = explode( ',', $columnheaderoverride );

									$actual_column = 0;

									foreach ( $dragndroparray as $index => $display_item ) {
										$can_sort = false;
										$sort_label = '';
										$column_label = '';
										$show_column_header = false;

										if ( isset( $columnheaderarray[$actual_column] ) ) {
											$column_label = $columnheaderarray[$actual_column];
										}

										if ( isset( $sorting_labels[$display_item] ) ) {
											$can_sort = true;
											$sort_label = $sorting_labels[$display_item];
										}

										$act_var_name = $activation_variables[$display_item];

										if ( isset( $activation_variables[$display_item] ) && $$act_var_name ) {
											if ( !in_array( $display_item, array( 7, 8, 9 ) ) || ( in_array( $display_item, array( 7, 8, 9 ) ) && $$act_var_name != 'false' ) ) {
												if ( empty( $column_label ) ) {
													$column_label = $default_labels[$display_item];
												}

												$show_column_header = true;
												$actual_column++;
											}
										}

										if ( $show_column_header ) {
											$catstartlist .= '<th><div class="linklistcolumnheader">';
											if ( $can_sort ) {
												$sort_direction = 'ASC';
												if ( ( isset( $_GET[$sorting_labels[$display_item]] ) && 'ASC' == $_GET[$sorting_labels[$display_item]] ) ||
												     ( isset( $settings_sort_label[$display_item] ) && $linkorder = $settings_sort_label[$display_item] && $linkdirection == 'ASC' ) ) {
													$sort_direction = 'DESC';
												}
												$sort_url = add_query_arg( $sorting_labels[$display_item], $sort_direction, '' );
												if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
													$sort_url = add_query_arg( 'link_tags', $_GET['link_tags'], $sort_url );
												}

												if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
													$sort_url = add_query_arg( 'link_price', $_GET['link_price'], $sort_url );
												}

												$catstartlist .= '<a href="' . $sort_url . '">';
											}

											$catstartlist .= $column_label;
											if ( $can_sort ) {
												$catstartlist .= '</a>';
											}
											$catstartlist .= '</div></th>';
										}
									}

									$catstartlist .= "</tr></div>\n";
								}
							} else {
								$catstartlist .= '';
							}
						} elseif ( $display_as_table === 'false' && ( ! $combineresults || ( $combineresults && $linkcount > 0 ) ) ) {
							$catstartlist = "\n\t<ul>\n";
						} else {
							$catstartlist = '';
						}

						if ( 0 == $the_link_query->found_posts && !$cat_has_children && !$hide_children_cat_links ) {
							$current_cat_output .= __('No links found', 'link-library');
						} elseif ( !$hide_children_cat_links ) {
							if ( $the_link_query->found_posts > 0 ) {
								$current_cat_output .= $catstartlist;
							}

							while ( $the_link_query->have_posts() ) {
								$the_link_query->the_post();

								if ( !empty( $maxlinks ) && is_numeric( $maxlinks ) && 0 < $maxlinks && $linkcount > $maxlinks ) {
									break;
								}

								if ( $pagination && 'search' != $mode ) {
									if ( $linkcount > $pagenumber * $linksperpage || $linkcount < $startingitem ) {
										$linkcount++;
										continue;
									}
								}

								$linkitem['term_id'] = $link_category->term_id;
								$linkitem['link_name'] = get_the_title();
								$linkitem['link_permalink'] = get_the_permalink( get_the_ID() );
								$link_meta = get_metadata( 'post', get_the_ID() );

								$linkitem['category_description'] = $link_category->description;

								$linkitem['category_name'] = '';
								if ( $combineresults ) {
									$link_terms = wp_get_post_terms( get_the_ID(), 'link_library_category' );
									if ( !empty( $link_terms ) ) {
										$link_term_array = array();
										foreach( $link_terms as $link_term ) {
											$link_term_array[] = $link_term->name;
										}

										if ( !empty( $link_term_array ) ) {
											$link_term_string = implode( ', ', $link_term_array );
											$linkitem['category_name'] = $link_term_string;
										}
									}
								} else {
									$linkitem['category_name'] = $link_category->name;
								}


								if ( isset( $link_meta['link_url'] ) ) {
									$linkitem['link_url'] = esc_html ( $link_meta['link_url'][0] );
								} else {
									$linkitem['link_url'] = '';
								}

								$linkitem['proper_link_id'] = get_the_ID();

								if ( isset( $link_meta['link_description'] ) ) {
									$linkitem['link_description'] = esc_html( $link_meta['link_description'][0] );
								} else {
									$linkitem['link_description'] = '';
								}

								if ( isset( $link_meta['link_notes'] ) ) {
									$linkitem['link_notes'] = esc_html( $link_meta['link_notes'][0] );
								} else {
									$linkitem['link_notes'] = '';
								}

								if ( isset( $link_meta['link_second_url'] ) ) {
									$linkitem['link_second_url'] = esc_url( $link_meta['link_second_url'][0] );
								} else {
									$linkitem['link_second_url'] = '';
								}

								if ( isset( $link_meta['link_no_follow'] ) ) {
									$linkitem['link_no_follow'] = esc_html( $link_meta['link_no_follow'][0] );
								} else {
									$linkitem['link_no_follow'] = '';
								}

								if ( isset( $link_meta['link_textfield'] ) ) {
									$linkitem['link_textfield'] = $link_meta['link_textfield'][0];
								} else {
									$linkitem['link_textfield'] = '';
								}

								if ( isset( $link_meta['link_target'] ) ) {
									$linkitem['link_target'] = esc_html( $link_meta['link_target'][0] );
								} else {
									$linkitem['link_target'] = '';
								}

								if ( isset( $link_meta['link_image'] ) ) {
									$linkitem['link_image'] = esc_url( $link_meta['link_image'][0] );
								} else {
									$linkitem['link_image'] = '';
								}

								if ( isset( $link_meta['link_featured'] ) ) {
									$linkitem['link_featured'] = esc_html( $link_meta['link_featured'][0] );
								} else {
									$linkitem['link_featured'] = '';
								}

								if ( isset( $link_meta['link_rss'] ) ) {
									$linkitem['link_rss'] = esc_url( $link_meta['link_rss'][0] );
								} else {
									$linkitem['link_rss'] = '';
								}

								if ( isset( $link_meta['link_telephone'] ) ) {
									$linkitem['link_telephone'] = esc_html( $link_meta['link_telephone'][0] );
								} else {
									$linkitem['link_telephone'] = '';
								}

								if ( isset( $link_meta['link_email'] ) ) {
									$linkitem['link_email'] = esc_html( $link_meta['link_email'][0] );
								} else {
									$linkitem['link_email'] = '';
								}

								if ( isset( $link_meta['link_reciprocal'] ) ) {
									$linkitem['link_reciprocal'] = esc_url( $link_meta['link_reciprocal'][0] );
								} else {
									$linkitem['link_reciprocal'] = '';
								}

								if ( isset( $link_meta['link_rel'] ) ) {
									$linkitem['link_rel'] = esc_html( $link_meta['link_rel'][0] );
								} else {
									$linkitem['link_rel'] = '';
								}

								if ( isset( $link_meta['link_submitter'][0] ) ) {
									$linkitem['link_submitter'] = esc_html( $link_meta['link_submitter'][0] );
								} else {
									$linkitem['link_submitter'] = '';
								}

								if ( isset( $link_meta['link_submitter_name'][0] ) ) {
									$linkitem['link_submitter_name'] = esc_html( $link_meta['link_submitter_name'][0] );
								} else {
									$linkitem['link_submitter_name'] = '';
								}

								if ( isset( $link_meta['link_submitter_email'][0] ) ) {
									$linkitem['link_submitter_email'] = esc_html( $link_meta['link_submitter_email'][0] );
								} else {
									$linkitem['link_submitter_email'] = '';
								}

								$linkitem['link_price'] = floatval( get_post_meta( get_the_ID(), 'link_price', true ) );

								if ( isset( $link_meta['link_visits'][0] ) ) {
									$linkitem['link_visits'] = esc_html( $link_meta['link_visits'][0] );
								} else {
									$linkitem['link_visits'] = '';
								}

								if ( isset( $link_meta['link_rating'][0] ) ) {
									$linkitem['link_rating'] = esc_html( $link_meta['link_rating'][0] );
								} else {
									$linkitem['link_rating'] = '';
								}

								$date_diff = time() - intval( $link_meta['link_updated'][0] );

								if ( $date_diff < 604800 ) {
									$linkitem['recently_updated'] = true;
								} else {
									$linkitem['recently_updated'] = false;
								}

								$linkitem['link_updated'] = $link_meta['link_updated'][0];

								if ( true == $debugmode ) {
									$linkstarttime = microtime ( true );
								}

								$between = "\n";

								if ( $rssfeedinline ) {
									include_once( ABSPATH . WPINC . '/feed.php' );

									if ( true == $debugmode ) {
										$starttimerssfeed = microtime ( true );
									}

									$rss = fetch_feed( $linkitem['link_rss'] );
									if ( !is_wp_error( $rss ) ) {
										$maxitems = $rss->get_item_quantity( $rssfeedinlinecount );

										$rss_items = $rss->get_items( 0, $maxitems );

										if ( $rss_items && !empty( $rssfeedinlinedayspublished ) && $rssfeedinlinedayspublished != 0 ) {
											foreach ( $rss_items as $index => $item ) {
												$diff_published = current_time( 'timestamp' ) - strtotime( $item->get_date( 'j F o' ) );
												if ( $diff_published > 60 * 60 * 24 * intval( $rssfeedinlinedayspublished ) ) {
													unset( $rss_items[$index] );
												}
											}

											if ( empty( $rss_items ) && $rssfeedinlineskipempty ) {
												continue;
											}
										}
									}

									if ( true == $debugmode ) {
										$current_cat_output .= "\n<!-- Time to render RSS Feed section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerssfeed ) . " --> \n";
									}
								}

								if ( $linkaddfrequency > 0 ) {
									if ( $the_link_query->current_post == 0 || $the_link_query->current_post % $linkaddfrequency == 0 ) {
										$current_cat_output .= stripslashes( $addbeforelink );
									}
								}

								if ( !isset( $linkitem['recently_updated'] ) ) {
									$linkitem['recently_updated'] = false;
								}

								$current_cat_output .= stripslashes( $beforeitem );

								if ( $showupdated && $linkitem['recently_updated'] && 'before' == $showupdatedpos ) {
									$current_cat_output .= '<span class="recently_updated">' . $updatedlabel . '</span>';
								}

								$the_link = '#';
								if ( !empty( $linkitem['link_url'] ) ) {
									$the_link = esc_html( $linkitem['link_url'] );
								}

								if ( !empty( $extraquerystring ) ) {
									parse_str( $extraquerystring, $expanded_query_string );
									if ( !empty( $expanded_query_string ) ) {
										$the_link = add_query_arg( $expanded_query_string, $the_link );
									}
								}

								$cat_extra_query_string = get_metadata( 'linkcategory', $linkitem['term_id'], 'linkextraquerystring', true );
								if ( !empty( $cat_extra_query_string ) ) {
									parse_str( $cat_extra_query_string, $cat_expanded_query_string );
									if ( !empty( $cat_expanded_query_string ) ) {
										$the_link = add_query_arg( $cat_expanded_query_string, $the_link );
									}
								}

								$the_second_link = '#';
								if ( !empty( $linkitem['link_second_url'] ) ) {
									$the_second_link = esc_html( stripslashes( $linkitem['link_second_url'] ) );
								}

								$the_permalink = '#';
								if ( !empty( $linkitem['link_permalink'] ) ) {
									$the_permalink = $linkitem['link_permalink'];
								}

								if ( !$suppressnoreferrer ) {
									$rel_list = array( 'noopener', 'noreferrer' );
								} else {
									$rel_list = array();
								}

								if ( $nofollow ) {
									$rel_list[] = 'nofollow';
								}

								if ( !empty( $linkitem['link_rel'] ) ) {
									$rel_list[] = $linkitem['link_rel'];
								}

								if ( !empty( $rel_list ) ) {
									$linkitem['link_rel'] = trim( ' rel="' . implode( ' ', $rel_list ) . '"' );
								}

								$linkitem['link_textfield'] = do_shortcode( $linkitem['link_textfield'] );

								if ( $use_html_tags ) {
									$descnotes = $linkitem['link_notes'];
									$descnotes = str_replace( '[', '<', $descnotes );
									$descnotes = str_replace( ']', '>', $descnotes );
									$desc = $linkitem['link_description'];
									$desc = str_replace("[", "<", $desc);
									$desc = str_replace("]", ">", $desc);
									$textfield = stripslashes( $linkitem['link_textfield'] );
									$textfield = str_replace( '[', '<', $textfield );
									$textfield = str_replace( ']', '>', $textfield );
								} else {
									$descnotes = esc_html( $linkitem['link_notes'], ENT_QUOTES );
									$desc = esc_html($linkitem['link_description'], ENT_QUOTES);
									$textfield = stripslashes( $linkitem['link_textfield'] );
								}

								$cleandesc = $desc;
								$cleanname = esc_html( $linkitem['link_name'], ENT_QUOTES );

								if ( 'search' == $mode ) {
									foreach ( $searchterms as $searchterm ) {
										$descnotes = link_library_highlight_phrase( $descnotes, $searchterm, '<span class="highlight_word">', '</span>' );
										$desc = link_library_highlight_phrase( $desc, $searchterm, '<span class="highlight_word">', '</span>' );
										$name = link_library_highlight_phrase( $linkitem['link_name'], $searchterm, '<span class="highlight_word">', '</span>' );
										$textfield = link_library_highlight_phrase( $textfield, $searchterm, '<span class="highlight_word">', '</span>' );
									}
								} else {
									$name = $cleanname;
								}

								if ( 'linkname' == $linktitlecontent ) {
									$title = $cleanname;
								} elseif ( 'linkdesc' == $linktitlecontent ) {
									$title = $cleandesc;
								}

								if ( $showupdatedtooltip ) {
									$date_format_string = get_option( 'date_format' );
									$cleandate = date_i18n( $date_format_string, intval( $linkitem['link_updated'] ) );
									if ( substr( $cleandate, 0, 2 ) != '00' ) {
										$title .= ' ('.__('Last updated', 'link-library') . '  ' . date_i18n(get_option('links_updated_date_format'), intval( $linkitem['link_updated'] ) ) .')';
									}
								}

								if ( !empty( $title ) ) {
									$title = ' title="' . $title . '"';
								}

								$alt = ' alt="' . $cleanname . '"';

								$target = $linkitem['link_target'];
								if ( !empty( $target ) ) {
									$target = ' target="' . $target . '"';
								} else {
									$target = $linktarget;
									if ( !empty( $target ) ) {
										$target = ' target="' . $target . '"';
									}
								}

								if ( empty( $dragndroporder ) ) {
									$dragndroporder = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17';
								}

								$dragndroparray = explode( ',', $dragndroporder );

								$new_entries = array( '13', '14', '15', '16', '17' );

								foreach ( $new_entries as $new_entry ) {
									if ( !in_array( $new_entry, $dragndroparray ) ) {
										$dragndroparray[] = $new_entry;
									}
								}

								if ( $dragndroparray ) {
									foreach ( $dragndroparray as $arrayelements ) {
										switch ( $arrayelements ) {

											case 1: 	//------------------ Image Output --------------------
												$imageoutput = '';

												if ( ( $show_images && !$shownameifnoimage && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_image'] ) ) ) ) || ( $show_images && $shownameifnoimage && !empty( $linkitem['link_image'] ) && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_image'] ) ) ) || $usethumbshotsforimages ) ) {
													$imageoutput .= stripslashes( $beforeimage );

													if ( !empty( $linkitem['link_image'] ) || $usethumbshotsforimages ) {
														if ( true == $debugmode ) {
															$starttimeimage = microtime ( true );
														}

														$imageoutput .= '<a href="';

														if ( !$enable_link_popup ) {
															if ( 'primary' == $sourceimage || empty( $sourceimage ) ) {
																$imageoutput .= $the_link;
															} elseif ( 'secondary' == $sourceimage ) {
																$imageoutput .= $the_second_link;
															}
														} else {
															$imageoutput .= admin_url( 'admin-ajax.php' . '?action=link_library_popup_content&linkid=' . $linkitem['proper_link_id'] . '&settings=' . $settings . '&height=' . ( empty( $popup_height ) ? 300 : $popup_height ) . '&width=' . ( empty( $popup_width ) ? 400 : $popup_width ) . '&xpath=' . $xpath );
														}

														$imageoutput .= '" id="link-' . $linkitem['proper_link_id'] . '" class="' . ( $enable_link_popup ? 'thickbox' : 'track_this_link' ) . ' ' . ( $linkitem['link_featured'] ? 'featured' : '' ). '" ' . $linkitem['link_rel'] . $title . $target. '>';

														if ( $usethumbshotsforimages && ( !$uselocalimagesoverthumbshots || empty( $uselocalimagesoverthumbshots ) || ( $uselocalimagesoverthumbshots && empty( $linkitem['link_image'] ) ) ) ) {
															if ( $thumbnailgenerator == 'robothumb' ) {
																$imageoutput .= '<img src="http://www.robothumb.com/src/?url=' . $the_link . '&size=' . $generaloptions['thumbnailsize'] . '"';
															} elseif ( $thumbnailgenerator == 'thumbshots' ) {
																if ( !empty( $thumbshotscid ) ) {
																	$imageoutput .= '<img src="http://images.thumbshots.com/image.aspx?cid=' . rawurlencode( $thumbshotscid ) . '&v=1&w=120&url=' . $the_link . '"';
																}
															}
														} else if ( !$usethumbshotsforimages || ( $usethumbshotsforimages && $uselocalimagesoverthumbshots && !empty( $linkitem['link_image'] ) ) ) {
															if ( strpos( $linkitem['link_image'], 'http' ) !== false ) {
																$imageoutput .= '<img src="' . $linkitem['link_image'] . '"';
															} else {
																// If it's a relative path
																$imageoutput .= '<img src="' . get_option( 'siteurl' ) . $linkitem['link_image'] . '"';
															}
														}

														if ( !$usethumbshotsforimages || ($usethumbshotsforimages && !empty( $thumbshotscid ) ) || ( $usethumbshotsforimages && $uselocalimagesoverthumbshots && !empty( $linkitem['link_image'] ) ) ) {

															$imageoutput .= $alt . $title;

															if ( !empty( $imageclass ) ) {
																$imageoutput .= ' class="' . $imageclass . '" ';
															}
														}
														$imageoutput .= '/>';
														$imageoutput .= '</a>';

														if ( true == $debugmode ) {
															$current_cat_output .= '<!-- Time to render image section of link id ' . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimeimage ) . " --> \n";
														}

													}

													$imageoutput .= stripslashes( $afterimage );

													if ( ( !empty( $imageoutput ) || ( $usethumbshotsforimages && !empty( $thumbshotscid ) ) ) && $show_images ) {
														$current_cat_output .= $imageoutput;
													}

													break;
												}

											case 2: 	//------------------ Name Output --------------------
												if ( ( $showname && 2 == $arrayelements && ( !$nooutputempty || ( $nooutputempty && !empty( $name ) ) ) ) ||
												     ( $show_images && $shownameifnoimage && empty( $linkitem['link_image'] ) && !$usethumbshotsforimages && 1 == $arrayelements && ( !$nooutputempty || ( $nooutputempty && !empty( $name ) ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimename = microtime ( true );
													}

													$current_cat_output .= stripslashes( $beforelink );

													if ( ( 'primary' == $sourcename && $the_link != '#') || ( 'secondary' == $sourcename && $the_second_link != '#' ) || ( 'permalink' == $sourcename && $the_permalink != '#' ) ) {
														$current_cat_output .= '<a href="';

														if ( !$enable_link_popup ) {
															if ( 'primary' == $sourcename || empty( $sourcename ) ) {
																$current_cat_output .= $the_link;
															} elseif ( 'secondary' == $sourcename ) {
																$current_cat_output .= $the_second_link;
															} elseif ( 'permalink' == $sourcename ) {
																$current_cat_output .= $the_permalink;
															}
														} else {
															$current_cat_output .= admin_url( 'admin-ajax.php' . '?action=link_library_popup_content&linkid=' . $linkitem['proper_link_id'] . '&settings=' . $settings . '&height=' . ( empty( $popup_height ) ? 300 : $popup_height ) . '&width=' . ( empty( $popup_width ) ? 400 : $popup_width ) . '&xpath=' . $xpath );
														}

														if ( 'description' == $tooltipname && !empty( $desc ) ) {
															$title = ' title="' . $desc . '"';
														}

														$current_cat_output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="' . ( $enable_link_popup ? 'thickbox' : 'track_this_link' ) . ' ' . ( $linkitem['link_featured'] ? ' featured' : '' ). '" ' . $linkitem['link_rel'] . $title . $target. '>';
													}

													$current_cat_output .= $name;

													if ( ( 'primary' == $sourcename && $the_link != '#') || ( 'secondary' == $sourcename && $the_second_link != '#' ) ) {
														$current_cat_output .= '</a>';
													}

													if ( $showadmineditlinks && $linkeditoruser ) {
														$current_cat_output .= $between . '<span class="editlink"><a href="' . esc_url( add_query_arg( array(
																'action' => 'edit', 'post' => $linkitem['proper_link_id'] ),
																admin_url( 'post.php' ) ) ) . '">(' . __('Edit', 'link-library') . ')</a></span>';
													}

													if ( $showupdated && $linkitem['recently_updated'] && 'after' == $showupdatedpos ) {
														$current_cat_output .= '<span class="recently_updated">' . $updatedlabel . '</span>';
													}

													$current_cat_output .= stripslashes( $afterlink );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render name section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimename ) . " --> \n";
													}
												}

												break;

											case 3: 	//------------------ Date Output --------------------

												if ( $showdate && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_updated'] ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimedate = microtime ( true );
													}

													$formatteddate = date_i18n( get_option( 'links_updated_date_format' ), intval( $linkitem['link_updated'] ) );

													$current_cat_output .= $between . stripslashes( $beforedate ) . $formatteddate . stripslashes( $afterdate );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render date section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedate ) . " --> \n";
													}
												}

												break;

											case 4: 	//------------------ Description Output --------------------

												if ( $showdescription && ( !$nooutputempty || ( $nooutputempty && !empty( $desc ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimedesc = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforedesc ) . $desc . stripslashes( $afterdesc );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
													}
												}

												break;

											case 5: 	//------------------ Notes Output --------------------

												if ( $shownotes && ( !$nooutputempty || ( $nooutputempty && !empty( $descnotes ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimenotes = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforenote ) . $descnotes . stripslashes( $afternote );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render notes section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimenotes ) . " --> \n";
													}
												}

												break;

											case 6: 	//------------------ RSS Icons Output --------------------

												if ( ( $show_rss || $show_rss_icon || $rsspreview ) && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_rss'] ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimerssicon = microtime ( true );
													}

													$current_cat_output .= stripslashes( $beforerss ) . '<div class="rsselements">';

													if ( $show_rss && !empty( $linkitem['link_rss'] ) ) {
														$current_cat_output .= $between . '<a class="rss" href="' . $linkitem['link_rss'] . '">RSS</a>';
													}

													if ( $show_rss_icon && !empty( $linkitem['link_rss'] ) ) {
														$current_cat_output .= $between . '<a class="rssicon" href="' . $linkitem['link_rss'] . '"><img src="' . plugins_url( 'icons/feed-icon-14x14.png', __FILE__ ) . '" /></a>';
													}

													if ( $rsspreview && !empty( $linkitem['link_rss'] ) ) {
														$current_cat_output .= $between . '<a href="' . home_url() . '/?link_library_rss_preview=1&keepThis=true&linkid=' . $linkitem['proper_link_id'] . '&previewcount=' . $rsspreviewcount . 'height=' . ( empty( $rsspreviewwidth ) ?  900 : $rsspreviewwidth ) . '&width=' . ( empty( $rsspreviewheight ) ? 700 : $rsspreviewheight ) . '&xpath=' . urlencode( $xpath ) . '" title="' . __('Preview of RSS feed for', 'link-library') . ' ' . $cleanname . '" class="thickbox"><img src="' . plugins_url( 'icons/preview-16x16.png', __FILE__ ) . '" /></a>';
													}

													$current_cat_output .= '</div><!-- Div RSS -->' . stripslashes( $afterrss );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render RSS Icon section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerssicon ) . " --> \n";
													}
												}

												if ( $rssfeedinline && $linkitem['link_rss'] ) {
													if ( $rss_items ) {
														$current_cat_output .= '<div id="ll_rss_results">';
														$date_format_string = get_option( 'date_format' );

														foreach ( $rss_items as $item ) {
															$current_cat_output .= '<div class="chunk" style="padding:0 5px 5px;">';
															$item_timestamp = strtotime( $item->get_date( 'j F Y | g:i a' ) );

															$formatted_date = date_i18n( $date_format_string, $item_timestamp );
															$current_cat_output .= '<div class="rsstitle"><a target="feedwindow" href="' . $item->get_permalink() . '">' . $item->get_title() . '</a><span class="rsstimestamp"> - ' . $formatted_date . '</span></div><!-- RSS Feed title -->';

															if ( $rssfeedinlinecontent ) {
																$current_cat_output .= '<div class="rsscontent">' . $item->get_description() . '</div><!-- RSS Content -->';
															}

															$current_cat_output .= '</div><!-- RSS Chunk -->';
															$current_cat_output .= '<br />';
														}

														$current_cat_output .= '</div><!-- RSS Results -->';
													}
												}
												break;
											case 7: 	//------------------ Web Link Output --------------------

												if ( 'false' != $displayweblink &&
												     ( !$nooutputempty ||
												       ( $nooutputempty && !empty( $the_link ) && 'label' != $displayweblink && '#' != $the_link && 'primary' == $sourceweblink ) ||
												       ( $nooutputempty && !empty( $the_second_link ) && 'label' != $displayweblink && '#' != $the_second_link && 'secondary' == $sourceweblink ) ||
												       ( $nooutputempty && !empty( $weblinklabel ) && 'label' == $displayweblink && !empty( $the_link ) && '#' != $the_link && 'primary' == $sourceweblink ) ||
												       ( $nooutputempty && !empty( $weblinklabel ) && 'label' == $displayweblink && !empty( $the_second_link ) && '#' != $the_second_link && 'secondary' == $sourceweblink )
												     ) ) {
													if ( true == $debugmode ) {
														$starttimerweblink = microtime ( true );
													}

													if ( 'addressonly' == $displayweblink ) {
														$current_cat_output .= $between . stripslashes( $beforeweblink );
														$current_cat_output .= $the_link;
														$current_cat_output .= stripslashes( $afterweblink );
													} else {
														$current_cat_output .= $between . stripslashes( $beforeweblink ) . '<a href="';

														if ( 'primary' == $sourceweblink || empty( $sourceweblink ) ) {
															$current_cat_output .= $the_link;
														} elseif ( 'secondary' == $sourceweblink ) {
															$current_cat_output .= $the_second_link;
														}

														if ( !empty( $target ) && !empty( $weblinktarget ) ) {
															$new_target_string = '="' . $weblinktarget . ' ';
															$weblinktarget = str_replace( '="', $new_target_string, $target );
														} elseif ( empty( $target ) && !empty( $weblinktarget ) ) {
															$weblinktarget = ' target="' . $weblinktarget . '"';
														}

														$current_cat_output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="track_this_link" ' . $weblinktarget . '>';

														if ( 'address' == $displayweblink ) {
															if ( ( 'primary' == $sourceweblink || empty( $sourceweblink ) ) && !empty( $the_link ) ) {
																$current_cat_output .= $the_link;
															} elseif ( 'secondary' == $sourceweblink && !empty( $the_second_link ) ) {
																$current_cat_output .= $the_second_link;
															}
														} elseif ( 'label' == $displayweblink && !empty( $weblinklabel ) ) {
															$current_cat_output .= stripslashes( $weblinklabel );
														}

														$current_cat_output .= '</a>' . stripslashes( $afterweblink );
													}

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render web link section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerweblink ) . " --> \n";
													}
												}

												break;
											case 8: 	//------------------ Telephone Output --------------------

												if ( 'false' != $showtelephone &&
												     ( !$nooutputempty ||
												       ( $nooutputempty && !empty( $linkitem['link_telephone'] ) && ( 'link' == $showtelephone || 'plain' == $showtelephone ) ) ||
												       ( $nooutputempty && !empty( $telephonelabel ) && 'label' == $showtelephone )
												     )
												) {
													if ( true == $debugmode ) {
														$starttimertelephone = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforetelephone );

													if ( 'plain' != $showtelephone ) {
														$current_cat_output .= '<a href="';

														if ( ( 'primary' == $sourcetelephone || empty( $sourcetelephone ) ) && !empty( $the_link ) ) {
															$current_cat_output .= $the_link;
														} elseif ( 'secondary' == $sourcetelephone && !empty( $the_second_link ) ) {
															$current_cat_output .= $the_second_link;
														} elseif ( 'phone' == $sourcetelephone && !empty( $the_second_link ) ) {
															$current_cat_output .= 'tel:' . $linkitem['link_telephone'];
														}

														$current_cat_output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="track_this_link" >';
													}

													if ( 'link' == $showtelephone || 'plain' == $showtelephone ) {
														$current_cat_output .= $linkitem['link_telephone'];
													} elseif ( 'label' == $showtelephone ) {
														$current_cat_output .= $telephonelabel;
													}

													if ( 'plain' != $showtelephone ) {
														$current_cat_output .= '</a>';
													}

													$current_cat_output .= stripslashes( $aftertelephone );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render telephone section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimertelephone ) . " --> \n";
													}
												}
												break;
											case 9: 	//------------------ E-mail Output --------------------

												if ( 'false' != $showemail && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_email'] ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimeremail = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforeemail );

													if ( 'plain' != $showemail ) {
														$current_cat_output .= '<a href="';

														if ( 'mailto' == $showemail || 'mailtolabel' == $showemail ) {
															if ( false === strpos( $linkitem['link_email'], '@' ) ) {
																$current_cat_output .= esc_url( $linkitem['link_email'] );
															} else {
																$current_cat_output .= 'mailto:' . $linkitem['link_email'];
															}
														} elseif ( 'command' == $showemail || 'commandlabel' == $showemail ) {
															$newcommand = str_replace( '#email', $linkitem['link_email'], $emailcommand );
															$cleanlinkname = str_replace( ' ', '%20', $linkitem['link_name'] );
															$newcommand = str_replace( '#company', $cleanlinkname, $newcommand );
															$current_cat_output .= $newcommand;
														}

														$current_cat_output .= '">';
													}

													if ( 'plain' == $showemail || 'mailto' == $showemail || 'command' == $showemail ) {
														$current_cat_output .= $linkitem['link_email'];
													} elseif ( 'mailtolabel' == $showemail || 'commandlabel' == $showemail ) {
														$current_cat_output .= $emaillabel;
													}

													if ( 'plain' != $showemail ) {
														$current_cat_output .= '</a>';
													}

													$current_cat_output .= stripslashes( $afteremail );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render e-mail section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimeremail ) . " --> \n";
													}
												}

												break;
											case 10: 	//------------------ Link Hits Output --------------------

												if ( $showlinkhits && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_visits'] ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimerhits = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforelinkhits );
													$current_cat_output .= $linkitem['link_visits'];
													$current_cat_output .= stripslashes( $afterlinkhits );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render link hits section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerhits ) . " --> \n";
													}
												}

												break;

											case 11: 	//------------------ Link Rating Output --------------------

												if ( $showrating && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_rating'] ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimerrating = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforelinkrating );
													$current_cat_output .= $linkitem['link_rating'];
													$current_cat_output .= stripslashes( $afterlinkrating );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render link rating section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerrating ) . " --> \n";
													}
												}

												break;

											case 12: 	//------------------ Link Large Description Output --------------------

												if ( $showlargedescription && ( !$nooutputempty || ( $nooutputempty && !empty( $textfield ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimerlargedesc = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforelargedescription );
													$current_cat_output .= $textfield;
													$current_cat_output .= stripslashes( $afterlargedescription );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render link large description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerlargedesc ) . " --> \n";
													}
												}

												break;

											case 13: 	//------------------ Submitter Name Output --------------------

												if ( $showsubmittername && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_submitter_name'] ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimersubmittername = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforesubmittername );
													$current_cat_output .= $linkitem['link_submitter_name'];
													$current_cat_output .= stripslashes( $aftersubmittername );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render link large description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimersubmittername ) . " --> \n";
													}
												}

												break;

											case 14: 	//------------------ Category Description Output --------------------

												if ( isset( $linkitem['category_description'] ) ) {
													$linkitem['category_description'] = str_replace( '[', '<', $linkitem['category_description'] );
													$linkitem['category_description'] = str_replace( ']', '>', $linkitem['category_description'] );
												} else {
													$linkitem['category_description'] = '';
												}

												if ( $showcatdesc && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['category_description'] ) ) ) ) {

													if ( true == $debugmode ) {
														$starttimedesc = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforecatdesc ) . $linkitem['category_description'] . stripslashes( $aftercatdesc );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render category description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
													}
												}

												break;

											case 15: 	//------------------ Link Tags Output --------------------

												$link_tags = wp_get_post_terms( $linkitem['proper_link_id'], 'link_library_tags' );

												if ( $showlinktags && ( !$nooutputempty || ( $nooutputempty && !empty( $link_tags ) ) ) ) {

													if ( true == $debugmode ) {
														$starttimedesc = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforelinktags );

													$link_tags_array = array();
													foreach ( $link_tags as $link_tag ) {
														$link_tags_array[] = $link_tag->name;
													}
													$current_cat_output .= implode( ',', $link_tags_array );
													$current_cat_output .= stripslashes( $afterlinktags );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render category description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
													}
												}

												break;
											case 16: 	//------------------ Link Price Output --------------------

												if ( $showlinkprice && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_price'] ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimersubmittername = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforelinkprice );
													if ( 'before' == $linkcurrencyplacement && !empty( $linkcurrency ) && $linkitem['link_price'] > 0 ) {
														$current_cat_output .= $linkcurrency;
													}

													$value = number_format((float)$linkitem['link_price'], 2, '.', '');
													if ( $value == 0 ) {
														$value = __( 'Free', 'link-library' );
													}
													$current_cat_output .= $value;

													if ( 'after' == $linkcurrencyplacement && !empty( $linkcurrency ) && $linkitem['link_price'] > 0 ) {
														$current_cat_output .= $linkcurrency;
													}
													$current_cat_output .= stripslashes( $afterlinkprice );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render link large description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimersubmittername ) . " --> \n";
													}
												}

												break;

											case 17: 	//------------------ Category Name Output --------------------

												if ( $showcatname && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['category_name'] ) ) ) ) {
													if ( true == $debugmode ) {
														$starttimedesc = microtime ( true );
													}

													$current_cat_output .= $between . stripslashes( $beforecatname ) . $linkitem['category_name'] . stripslashes( $aftercatname );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render category name section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
													}
												}

												break;
										}
									}
								}

								$current_cat_output .= stripslashes( $afteritem ) . "\n";

								if ( $linkaddfrequency > 0 ) {
									if ( ( $the_link_query->current_post + 1 ) % $linkaddfrequency === 0 || $the_link_query->current_post + 1 == $the_link_query->found_posts ) {
										$current_cat_output .= stripslashes( $addafterlink );
									}
								}

								if ( true == $debugmode ) {
									$current_cat_output .= '<!-- Time to render link id ' . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $linkstarttime ) . " --> \n";
								}

								$linkcount++;



							}

							// Close the category
							if ( $the_link_query->found_posts > 0 ) {
								if ( 'true' == $display_as_table ) {
									$current_cat_output .= "\t</table>\n";
								} elseif ( 'false' == $display_as_table ) {
									$current_cat_output .= "\t</ul>\n";
								}
							}
						}

						if ( !empty( $catlistwrappers ) && !empty( $beforecastlist1 ) ) {
							$current_cat_output .= '</div><!-- Div cat list wrappers -->';
						}

						if ( !empty( $afterlastlink ) && $the_link_query->found_posts > 0 ) {
							$current_cat_output .= stripslashes( $afterlastlink );
						}

						if ( $showlinksonclick ) {
							$current_cat_output .= '</div><!-- Div Show Links on click -->';
						}

						$currentcategory = $currentcategory + 1;

						if ( $display_children && $cat_has_children && !$showonecatonly ) {
							$current_cat_output .= RenderLinkLibrary( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $onlycount, $link_category->term_id, $level + 1, $display_children, $hidechildcatlinks, $linkcount );
						}

						if ( $combineresults ) {
							if ( $start_link_count != $linkcount ) {
								$output .= $current_cat_output;
							}
							break;
						} else {
							$current_cat_output .= "</div><!-- Div End Category -->\n";
						}
					}

					if ( $start_link_count != $linkcount ) {
						$output .= $current_cat_output;
					}
				} else {
					$output .= '<span class="nolinksfoundincat">' . __( 'No links found', 'link-library' ) . '</span>';
				}
			}
		} else {
			$output .= '<span class="nolinkstodisplay">' . __( 'All of your links must be assigned at least to one category to be displayed', 'link-library') . '</span>';
		}
	} else {
		$output .= '<span class="nolinksfoundallcats">' . __( 'No links found', 'link-library' ) . '</span>';
	}

	if ( isset( $_GET['searchll'] ) && $linkcount == 1 && $level == 0 ) {
		$output .= $searchnoresultstext . "\n";
	}

	if ( $usethumbshotsforimages && $level == 0 ) {
		if ( $thumbnailgenerator == 'robothumb' ) {
			$output .= '<div class="llthumbshotsnotice"><a href="http://www.robothumb.com" target="_blank">' . __( 'Screenshots by Robothumb', 'link-library' ) . '</a></div>';
		} elseif ( $thumbnailgenerator == 'thumbshots' ) {
			$output .= '<div class="llthumbshotsnotice"><a href="http://www.thumbshots.com" target="_blank" title="Thumbnails Screenshots by Thumbshots">' . __( 'Thumbnail Screenshots by Thumbshots', 'link-library' ) . '</a></div>';
		}
	}

	if ( $level == 0 && $pagination && 'search' != $mode && ( 'AFTER' == $paginationposition || empty( $pagination ) ) ) {
		$previouspagenumber = $pagenumber - 1;
		$nextpagenumber = $pagenumber + 1;
		$pageID = get_queried_object_id();

		$output .= link_library_display_pagination( $previouspagenumber, $nextpagenumber, $number_of_pages, $pagenumber, $showonecatonly, $showonecatmode, $AJAXcatid, $settings, $pageID, $currentcatletter );
	}

	if ( $level == 0 ) {
		$xpath = $LLPluginClass->relativePath( dirname( __FILE__ ), ABSPATH );
		$nonce = wp_create_nonce( 'll_tracker' );

		$output .= "<script type='text/javascript'>\n";
		$output .= "jQuery(document).ready(function()\n";
		$output .= "{\n";
		$output .= "jQuery('.arrow-up').hide();\n";
		$output .= "jQuery('#linklist" . $settings . " a.track_this_link').click(function() {\n";
		$output .= "linkid = this.id;\n";
		$output .= "linkid = linkid.substring(5);\n";
		$output .= "path = '" . $xpath . "';\n";
		$output .= "jQuery.ajax( {" .
				   "    type: 'POST'," .
				   "    url: '" . admin_url( 'admin-ajax.php' ) . "', " .
				   "    data: { action: 'link_library_tracker', " .
				   "            _ajax_nonce: '" . $nonce . "', " .
				   "            id:linkid, xpath:path } " .
				   "    });\n";
		$output .= "return true;\n";
		$output .= "});\n";
		$output .= "jQuery('#linklist" . $settings . " .expandlinks').click(function() {\n";
		$output .= "target = '.' + jQuery(this).attr('id');\n";
		$output .= "if ( jQuery( target ).is(':visible') ) {\n";
		$output .= "jQuery(target).slideUp();\n";
		$output .= "jQuery(this).children('img').attr('src', '";

		if ( !empty( $expandiconpath ) ) {
			$output .= $expandiconpath;
		} else {
			$output .= plugins_url( 'icons/expand-32.png', __FILE__ );
		}

		$output .= "');\n";
		$output .= "} else {\n";
		$output .= "jQuery(target).slideDown();\n";
		$output .= "jQuery(this).children('img').attr('src', '";

		if ( !empty( $collapseiconpath ) ) {
			$output .= $collapseiconpath;
		} else {
			$output .= plugins_url( 'icons/collapse-32.png', __FILE__ );
		}

		$output .= "');\n";
		$output .= "}\n";
		$output .= "});\n";
		$output .= "});\n";
		$output .= "</script>";
		unset( $xpath );
	}

	$currentcategory = $currentcategory + 1;

	if ( $level == 0 ) {
		$output .= '</div><!-- Div Linklist -->';

		$output .= "\n<!-- End of Link Library Output -->\n\n";
	}

	remove_filter( 'posts_search', 'll_expand_posts_search', 10 );

	wp_reset_postdata();

	return do_shortcode( $output );
}
