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
        $paginationoutput = '<div class="pageselector">';

        if ( 1 != $pagenumber ) {
            $paginationoutput .= '<span class="previousnextactive">';

            if ( !$showonecatonly ) {
                $argumentarray = array ( 'page_id' => get_the_ID(), 'linkresultpage' => $previouspagenumber );

	            if ( !empty( $currentcatletter ) ) {
		            $argumentarray['catletter'] = $currentcatletter;
	            }

                $argumentarray = array_merge( $argumentarray, $incomingget );
                $targetaddress = esc_url( add_query_arg( $argumentarray ) );

                $paginationoutput .= '<a href="' . $targetaddress . '">' . __('Previous', 'link-library') . '</a>';
            } elseif ( $showonecatonly ) {
                if ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) {
                    $paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('" . $AJAXcatid . "', '" . $settings . "', " . $previouspagenumber . ");return false;\" >" . __('Previous', 'link-library') . '</a>';
                } elseif ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode ) {
                    if ( 'HTMLGET' == $showonecatmode ) {
                        $argumentarray = array ( 'page_id' => $pageID, 'linkresultpage' => $previouspagenumber, 'cat_id' => $AJAXcatid );
                    } elseif ( 'HTMLGETCATNAME' == $showonecatmode ) {
						$argumentarray = array ( 'page_id' => $pageID, 'linkresultpage' => $previouspagenumber, 'catname' => $AJAXcatid );
					} else {
                        $argumentarray = array ( 'page_id' => $pageID, 'linkresultpage' => $previouspagenumber, 'cat' => $AJAXcatid );
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
                    $argumentarray = array ( 'page_id' => $pageID, 'linkresultpage' => $counter );

	                if ( !empty( $currentcatletter ) ) {
		                $argumentarray['catletter'] = $currentcatletter;
	                }

                    $argumentarray = array_merge( $argumentarray, $incomingget );
                    $targetaddress = esc_url( add_query_arg( $argumentarray ) );

                    $paginationoutput .= '<a href="' . $targetaddress . '">' . $counter . '</a>';
                } elseif ( $showonecatonly ) {
                    if ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) {
                        $paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('" . $AJAXcatid . "', '" . $settings . "', " . $counter . ");return false;\" >" . $counter . '</a>';
                    } elseif ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode ) {
                        if ( 'HTMLGET' == $showonecatmode ) {
                            $argumentarray = array ( 'page_id' => $pageID, 'linkresultpage' => $counter, 'cat_id' => $AJAXcatid );
                        } elseif ( 'HTMLGETCATNAME' == $showonecatmode ) {
							$argumentarray = array ( 'page_id' => $pageID, 'linkresultpage' => $counter, 'catname' => $AJAXcatid );
						} else {
                            $argumentarray = array ( 'page_id' => $pageID, 'linkresultpage' => $counter, 'cat' => $AJAXcatid );
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
                $argumentarray = array ( 'page_id' => $pageID, 'linkresultpage' => $nextpagenumber );

	            if ( !empty( $currentcatletter ) ) {
		            $argumentarray['catletter'] = $currentcatletter;
	            }

                $argumentarray = array_merge( $argumentarray, $incomingget );
                $targetaddress = esc_url( add_query_arg( $argumentarray ) );

                $paginationoutput .= '<a href="' . $targetaddress . '">' . __('Next', 'link-library') . '</a>';
            } elseif ( $showonecatonly ) {
                if ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) {
                    $paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('" . $AJAXcatid . "', '" . $settings . "', " . $nextpagenumber . ");return false;\" >" . __('Next', 'link-library') . '</a>';
                } elseif ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode ) {
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

        $paginationoutput .= '</div>';
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

function RenderLinkLibrary( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $onlycount = 'false' ) {

    global $wpdb;
    $showonecatonly = '';
    $showonecatmode = '';

    $generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );
    extract( $generaloptions );

    $libraryoptions = wp_parse_args( $libraryoptions, ll_reset_options( 1, 'list', 'return' ) );
    extract( $libraryoptions );

    /* This case will only happen if the user entered bad data in the admin page or if someone is trying to inject bad data in SQL query */
    if ( !empty( $categorylist ) ) {
        $categorylistarray = explode( ',', $categorylist );

        if ( true === array_filter( $categorylistarray, 'is_int' ) ) {
            return 'List of requested categories is invalid. Please go back to Link Library admin panel to correct.';
        }
    }

    if ( !empty( $excludecategorylist ) ) {
        $excludecategorylistarray = explode( ',', $excludecategorylist );

        if ( true === array_filter( $excludecategorylistarray, 'is_int' ) ) {
            return 'List of requested excluded categories is invalid. Please go back to Link Library admin panel to correct.';
        }
    }

    $validdirections = array( 'ASC', 'DESC' );

	$linkeditoruser = current_user_can( 'manage_links' );

    $output = "\n<!-- Beginning of Link Library Output -->\n\n";

    $currentcategory = 1;
    $categoryname = '';
	$mode = 'normal';

    if ( $showonecatonly && 'AJAX' == $showonecatmode && isset( $AJAXcatid ) && empty( $AJAXcatid ) ) {
        $AJAXnocatset = true;
    } else {
        $AJAXnocatset = false;
    }

    if ( $showonecatonly && 'AJAX' == $showonecatmode && isset( $AJAXcatid ) && !empty( $AJAXcatid ) && ( !isset( $_GET['searchll'] ) || empty( $_GET['searchll'] ) ) ) {
        $categorylist = $AJAXcatid;
    } elseif ($showonecatonly && 'HTMLGET' == $showonecatmode && isset( $_GET['cat_id'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
        $categorylist = intval( $_GET['cat_id'] );
        $AJAXcatid = $categorylist;
    } elseif ($showonecatonly && 'HTMLGETSLUG' == $showonecatmode && isset( $_GET['cat'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
        $categorysluglist = $_GET['cat'];
    } elseif ($showonecatonly && 'HTMLGETCATNAME' == $showonecatmode && isset( $_GET['catname'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
        $categorynamelist = $_GET['catname'];
    }elseif ( $showonecatonly && 'HTMLGETPERM' == $showonecatmode && empty( $_GET['searchll'] ) ) {
        global $wp_query;

        $categoryname = $wp_query->query_vars['cat_name'];
        $AJAXcatid = $categoryname;
    } elseif ( $showonecatonly && ( !isset( $AJAXcatid ) || empty( $AJAXcatid ) ) && !empty( $defaultsinglecat ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
        $categorylist = $defaultsinglecat;
        $AJAXcatid = $categorylist;
    } elseif ( $showonecatonly && ( !isset( $AJAXcatid ) || empty( $AJAXcatid ) ) && empty( $defaultsinglecat ) && empty( $_GET['searchll'] ) ) {
        $catquery = 'SELECT distinct t.name, t.term_id ';
        $catquery .= 'FROM ' . $LLPluginClass->db_prefix() . 'terms t ';
        $catquery .= 'LEFT JOIN ' . $LLPluginClass->db_prefix() . 'term_taxonomy tt ON (t.term_id = tt.term_id) ';
        $catquery .= 'LEFT JOIN ' . $LLPluginClass->db_prefix() . 'term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ';
        $catquery .= 'LEFT JOIN ' . $LLPluginClass->db_prefix() . 'links l ON (tr.object_id = l.link_id) ';
        $catquery .= 'LEFT JOIN ' . $LLPluginClass->db_prefix() . 'links_extrainfo le ON (l.link_id = le.link_id) ';
        $catquery .= 'WHERE tt.taxonomy = "link_category" ';

	    if ( $hide_if_empty ) {
            $catquery .= 'AND l.link_id is not NULL AND l.link_description not like "%LinkLibrary:AwaitingModeration:RemoveTextToApprove%" ';
        }

        if ( !empty( $categorylist ) ) {
            $catquery .= ' AND t.term_id in (' . $categorylist. ')';
        }

        if ( !empty( $excludecategorylist ) ) {
            $catquery .= ' AND t.term_id not in (' . $excludecategorylist . ')';
        }

        if ( false == $showinvisible && ( false == $showinvisibleadmin || ( true == $showinvisibleadmin && ! $linkeditoruser ) ) ) {
            $catquery .= ' AND l.link_visible != "N"';
        }

        if ( true == $current_user_links && is_user_logged_in() ) {
            $user_data = wp_get_current_user();
            $name_field_value = $user_data->display_name;

            $catquery .= 'AND le.link_submitter = "' . $name_field_value . '" ';
        }

        $mode = 'normal';

        $catquery .= ' ORDER by ';

        if ( !$combineresults ) {
            if ( 'name' == $order ) {
                $catquery .= ' name ' . ( in_array( $direction, $validdirections ) ? $direction : 'ASC' );
            } elseif ( 'id' == $order ) {
                $catquery .= ' t.term_id ' . ( in_array( $direction, $validdirections ) ? $direction : 'ASC' );
            } elseif ( 'order' == $order ) {
                $catquery .= ' t.term_order ' . ( in_array( $direction, $validdirections ) ? $direction : 'ASC' );
            } elseif ( 'catlist' == $order ) {
                $catquery .= ' FIELD(t.term_id,' . $categorylist . ') ';
            }

            $catquery .= ', ';
        }

	    if ( true == $featuredfirst ) {
		    $catquery .= ' le.link_featured DESC, ';
	    }

	    if ( 'name' == $linkorder ) {
            $catquery .= 'link_name ' . ( in_array( $linkdirection, $validdirections ) ? $direction : 'ASC' );
        } elseif ( 'id' == $linkorder ) {
            $catquery .= 'link_id ' . ( in_array( $linkdirection, $validdirections ) ? $direction : 'ASC' );
        } elseif ( 'order' == $linkorder ) {
            $catquery .= 'link_order ' . ( in_array( $linkdirection, $validdirections ) ? $direction : 'ASC' );
        } elseif ( 'date' == $linkorder ) {
            $catquery .= 'link_updated ' . ( in_array( $linkdirection, $validdirections ) ? $direction : 'ASC' );
        }

        $catitems = $wpdb->get_results( $catquery );

        if ( $debugmode ) {
            $output .= "\n<!-- AJAX Default Category Query: " . print_r( $catquery, TRUE ) . "-->\n\n";
            $output .= "\n<!-- AJAX Default Category Results: " . print_r( $catitems, TRUE ) . "-->\n\n";
        }

        if ( $catitems ) {
            $categorylist = $catitems[0]->term_id;
            $AJAXcatid = $categorylist;
        }
    }

	$searchterms = '';

	if ( isset($_GET['searchll'] ) && !empty( $_GET['searchll'] ) && empty( $singlelinkid ) ) {
		$searchterms  = array();
		$searchstring = $_GET['searchll'];

		$offset = 0;
		while ( false !== strpos( $searchstring, '"', $offset ) ) {
			if ( 0 == $offset ) {
				$offset = strpos( $searchstring, '"' );
			} else {
				$endpos        = strpos( $searchstring, '"', $offset + 1 );
				$searchterms[] = substr( $searchstring, $offset + 1, $endpos - $offset - 2 );
				$strlength     = ( $endpos + 1 ) - ( $offset + 1 );
				$searchstring  = substr_replace( $searchstring, '', $offset - 1, $endpos + 2 - ( $offset ) );
				$offset        = 0;
			}
		}

		if ( ! empty( $searchstring ) ) {
			$searchterms = array_merge( $searchterms, explode( " ", $searchstring ) );
		}

		if ( !empty( $searchterms ) ) {
			$mode = 'search';
		}
	}

	$currentcatletter = '';

	if ( $cat_letter_filter != 'no' ) {
		require_once plugin_dir_path( __FILE__ ) . 'render-link-library-alpha-filter.php';
		$result = RenderLinkLibraryAlphaFilter( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $mode );

		$currentcatletter = $result['currentcatletter'];

		if ( 'beforelinks' == $cat_letter_filter || 'beforecatsandlinks' == $cat_letter_filter ) {
			$output .= $result['output'];
		}
	}

    $linkquery = 'SELECT distinct *, l.link_id as proper_link_id, UNIX_TIMESTAMP(l.link_updated) as link_date, ';
    $linkquery .= 'IF (DATE_ADD(l.link_updated, INTERVAL 120 MINUTE) >= NOW(), 1,0) as recently_updated ';
    $linkquery .= 'FROM ' . $LLPluginClass->db_prefix() . 'terms t ';
    $linkquery .= 'LEFT JOIN ' . $LLPluginClass->db_prefix() . 'term_taxonomy tt ON (t.term_id = tt.term_id) ';
    $linkquery .= 'LEFT JOIN ' . $LLPluginClass->db_prefix() . 'term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ';
    $linkquery .= 'LEFT JOIN ' . $LLPluginClass->db_prefix() . 'links l ON (tr.object_id = l.link_id) ';
    $linkquery .= 'LEFT JOIN ' . $LLPluginClass->db_prefix() . 'links_extrainfo le ON (l.link_id = le.link_id) ';
    $linkquery .= 'WHERE tt.taxonomy = "link_category" ';

    if ( $hide_if_empty ) {
        $linkquery .= 'AND l.link_id is not NULL AND l.link_description not like "%LinkLibrary:AwaitingModeration:RemoveTextToApprove%" ';
    }

	if ( !empty( $currentcatletter ) && $cat_letter_filter != 'no' ) {
		$linkquery .= ' AND substring(t.name, 1, 1) = "' . $currentcatletter . '" ';
	}

    if ( ( !empty( $categorylist ) || isset( $_GET['cat_id'] ) ) && empty( $singlelinkid ) ) {
        $linkquery .= ' AND t.term_id in (' . $categorylist. ')';
    }

    if ( ( !empty( $categorysluglist ) || isset( $_GET['cat'] ) ) && empty( $singlelinkid ) ) {
        $linkquery .= ' AND t.slug in ("' . $categorysluglist. '")';
    }
	
	if ( ( !empty( $categorynamelist ) || isset( $_GET['catname'] ) ) && empty( $singlelinkid ) ) {
        $linkquery .= ' AND t.name in ("' . urldecode( $categorynamelist ). '")';
    }

    if ( isset( $categoryname ) && !empty( $categoryname ) && 'HTMLGETPERM' == $showonecatmode && empty( $singlelinkid ) ) {
        $linkquery .= ' AND t.slug = "' . $categoryname. '"';
    }

    if ( !empty( $excludecategorylist ) && empty( $singlelinkid ) ) {
        $linkquery .= ' AND t.term_id not in (' . $excludecategorylist . ')';
    }

	if ( !empty( $singlelinkid ) && intval( $singlelinkid ) ) {
		$linkquery .= ' AND l.link_id = ' . $singlelinkid . ' ';
	}

	if ( false == $showinvisible && ( false == $showinvisibleadmin || ( true == $showinvisibleadmin && ! $linkeditoruser ) ) ) {
        $linkquery .= ' AND l.link_visible != "N" ';
    }

    if ( true == $current_user_links && is_user_logged_in() ) {
        $user_data = wp_get_current_user();
        $name_field_value = $user_data->display_name;

        $linkquery .= 'AND le.link_submitter = "' . $name_field_value . '" ';
    }

    if ( !empty( $searchterms ) ) {
        $mode = 'search';
        $termnb = 1;

        foreach( $searchterms as $searchterm ) {
            if ( !empty( $searchterm ) ) {
                $searchterm = str_replace( '--', '', $searchterm );
                $searchterm = str_replace( ';', '', $searchterm );
                $searchterm = esc_html( stripslashes( $searchterm ) );
                if ( true == $searchterm ) {
                    if ( 1 == $termnb ) {
                        $linkquery .= ' AND (link_name like "%' . $searchterm . '%" ';
                        $termnb++;
                    } else {
                        $linkquery .= ' OR link_name like "%' . $searchterm . '%" ';
                    }

                    if ( false == $hidecategorynames ) {
                        $linkquery .= ' OR name like "%' . $searchterm . '%" ';
                    }

                    if ( $shownotes ) {
                        $linkquery .= ' OR link_notes like "%' . $searchterm . '%" ';
                    }

                    if ( $showdescription ) {
                        $linkquery .= ' OR link_description like "%' . $searchterm . '%" ';
                    }

                    if ( $showlargedescription ) {
                        $linkquery .= ' OR link_textfield like "%' . $searchterm . '%" ';
                    }
                }
            }
        }

        $linkquery .= ')';
    }

    $linkquery .= ' ORDER by ';

    if ( !$combineresults ) {
        if ( 'name' == $order ) {
            $linkquery .= ' name ' . ( in_array( $direction, $validdirections ) ? $direction : 'ASC' );
        } elseif ( 'id' == $order ) {
            $linkquery .= ' t.term_id ' . ( in_array( $direction, $validdirections ) ? $direction : 'ASC' );
        } elseif ( 'order' == $order ) {
            $linkquery .= ' t.term_order ' . ( in_array( $direction, $validdirections ) ? $direction : 'ASC' );
        } elseif ( 'catlist' == $order ) {
            $linkquery .= ' FIELD(t.term_id,' . $categorylist . ') ';
        }

        $linkquery .= ', ';
    }

	if ( $featuredfirst ) {
		$linkquery .= ' link_featured DESC, ';
	}

    if ( 'name' == $linkorder || 'random' == $linkorder ) {
        $linkquery .= 'l.link_name ' . ( in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC' );
    } elseif ( 'id' == $linkorder ) {
        $linkquery .= 'l.link_id ' . ( in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC' );
    } elseif ( 'order' == $linkorder ) {
        $linkquery .= 'l.link_order '. ( in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC' );
    } elseif ( 'date' == $linkorder ) {
        $linkquery .= 'l.link_updated '. ( in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC' );
    }

    if ( $pagination && 'search' != $mode ) {

        $linkitemsforcount = $wpdb->get_results( $linkquery );

        $numberoflinks = count( $linkitemsforcount );

        $quantity = $linksperpage + 1;

        if ( isset( $_POST['linkresultpage'] ) || isset( $_GET['linkresultpage'] ) ) {

            if ( isset( $_POST['linkresultpage'] ) ) {
                $pagenumber = $_POST['linkresultpage'];
            } elseif ( isset( $_GET['linkresultpage'] ) ) {
                $pagenumber = $_GET['linkresultpage'];
            }

            $startingitem = ($pagenumber - 1) * $linksperpage;
            $linkquery .= ' LIMIT ' . $startingitem . ', ' . $quantity;
        } else {
            $pagenumber = 1;
            $linkquery .= ' LIMIT 0, ' . $quantity;
        }
    }

    if ( !empty( $singlelinkid ) && intval( $singlelinkid ) ) {
        $linkquery .= ' LIMIT 0, 1';
    }

    if ( true == $debugmode ) {
        $linkquerystarttime = microtime ( true );
    }

    $linkitems = $wpdb->get_results( $linkquery, ARRAY_A );

    if ( $debugmode ) {
        $output .= "\n<!-- Link Query: " . print_r( $linkquery, TRUE ) . "-->\n\n";
        $output .= "\n<!-- Link Results: " . print_r( $linkitems, TRUE ) . "-->\n\n";
        $output .= "\n<!-- Link Query Execution Time: " . ( microtime( true ) - $linkquerystarttime ) . "-->\n\n";
    }

    if ( $pagination ) {
        if ($linksperpage == 0 || empty( $linksperpage ) ) {
            $linksperpage = 5;
        }

        if ( count( $linkitems ) > $linksperpage ) {
            array_pop( $linkitems );
            $nextpage = true;
        } else {
            $nextpage = false;
        }

        if( isset( $numberoflinks ) ) {
            $preroundpages = $numberoflinks / $linksperpage;
            $numberofpages = ceil( $preroundpages * 1 ) / 1;
        }
    }

    if ( 'random' == $linkorder ) {
        shuffle($linkitems);
    }

    if ( !empty( $maxlinks ) ) {
        if ( is_numeric( $maxlinks ) ) {
            array_splice( $linkitems, $maxlinks );
        }
    }

    if ( $debugmode ) {
        echo '<!-- showonecatmode: ' . $showonecatonly . ', AJAXnocatset: ' . $AJAXnocatset . ', nocatonstartup: ' . $nocatonstartup . '-->';
    }

    // Display links
    if ( ( $linkitems && $showonecatonly && $AJAXnocatset && $nocatonstartup && !isset( $_GET['searchll'] ) ) || ( empty( $linkitems ) && $nocatonstartup && empty( $_GET['searchll'] ) ) ) {
        $output .= "<div id='linklist" . $settings . "' class='linklist'>\n";
        $output .= '</div>';
    } elseif ( $linkitems && $onlycount ) {
	    return count( $linkitems );
    } elseif ( $linkitems && !$onlycount ) {
        $output .= "<div id='linklist" . $settings . "' class='linklist'>\n";

        if ( $pagination && $mode != "search" && 'BEFORE' == $paginationposition ) {
            $previouspagenumber = $pagenumber - 1;
            $nextpagenumber = $pagenumber + 1;
            $pageID = get_the_ID();
            if ( empty( $AJAXcatid ) && !empty( $categorysluglist ) ) {
                $AJAXcatid = $categorysluglist;
            }
			if ( empty( $AJAXcatid ) && !empty( $categorynamelist ) ) {
                $AJAXcatid = $categorynamelist;
            }

            $output .= link_library_display_pagination( $previouspagenumber, $nextpagenumber, $numberofpages, $pagenumber, $showonecatonly, $showonecatmode, $AJAXcatid, $settings, $pageID, $currentcatletter );
        }

        if ( 'search' == $mode ) {
            $output .= '<div class="resulttitle">' . __('Search Results for', 'link-library') . ' "' . esc_html( stripslashes( $_GET['searchll'] ) ) . '"</div>';
        }

        $currentcategoryid = -1;

        $xpath = $LLPluginClass->relativePath( dirname( __FILE__ ), ABSPATH );


        $linkcount = 0;

        foreach ( $linkitems as $linkitem ) {

            if ( true == $debugmode ) {
                $linkstarttime = microtime ( true );
            }

            if ( $currentcategoryid != $linkitem['term_id'] ) {
                if ( -1 != $currentcategoryid && $showonecatonly && empty( $_GET['searchll'] ) && ! $combineresults ) {
                    break;
                }

                if ( -1 != $currentcategoryid && ! $combineresults) {
                    // Close the last category
                    if ( $displayastable ) {
                        $output .= "\t</table>\n";
                    } else {
                        $output .= "\t</ul>\n";
                    }

                    if ( !empty( $catlistwrappers ) ) {
                        $output .= '</div>';
                    }

	                if ( !empty( $beforefirstlink ) ) {
		                $output .= stripslashes( $afterlastlink );
	                }

                    if ( $showlinksonclick ) {
                        $output .= '</div>';
                    }

                    $output .= '</div>';

                    $currentcategory = $currentcategory + 1;
                }

                if ( ! $combineresults ) {
                    $currentcategoryid = $linkitem['term_id'];
                    $output .= '<div class="LinkLibraryCat LinkLibraryCat' . $currentcategoryid . '">';
                    $linkcount = 0;
                    $catlink = '';
                    $cattext = '';
                    $catenddiv = '';

                    if ( 1 == $catlistwrappers ) {
                        $output .= '<div class="' . $beforecatlist1 . '">';
                    } else if ( $catlistwrappers == 2 ) {
                        $remainder = $currentcategory % $catlistwrappers;
                        switch ( $remainder ) {

                            case 0:
                                $output .= '<div class="' . $beforecatlist2 . '">';
                                break;

                            case 1:
                                $output .= '<div class="' . $beforecatlist1 . '">';
                                break;
                        }
                    } else if ( 3 == $catlistwrappers ) {
                        $remainder = $currentcategory % $catlistwrappers;
                        switch ( $remainder ) {

                            case 0:
                                $output .= '<div class="' . $beforecatlist3 . '">';
                                break;

                            case 2:
                                $output .= '<div class="' . $beforecatlist2 . '">';
                                break;

                            case 1:
                                $output .= '<div class="' . $beforecatlist1 . '">';
                                break;
                        }
                    }

                    // Display the category name
                    if ( !$hidecategorynames || empty( $hidecategorynames ) ) {
                        $caturl = get_metadata( 'linkcategory', $linkitem['term_id'], 'linkcaturl', true );

                        if ( $catanchor ) {
                            $cattext = '<div id="' . $linkitem['slug'] . '">';
                        } else {
                            $cattext = '';
                        }

                        if ( !$divorheader ) {
                            if ( 'search' == $mode ) {
                                foreach ( $searchterms as $searchterm ) {
                                    $linkitem['name'] = link_library_highlight_phrase( $linkitem['name'], $searchterm, '<span class="highlight_word">', '</span>' );
                                }
                            }

                            $catlink = '<div class="' . $catnameoutput . '">';

                            if ( 'right' == $catdescpos || empty( $catdescpos ) ) {
                                if ( !empty( $caturl ) ) {
                                    $catlink .= '<a href="' . link_library_add_http( $caturl ) . '" ';

                                    if ( !empty( $linktarget ) )
                                        $catlink .= ' target="' . $linktarget . '"';

                                    $catlink .= '>';
                                }
                                $catlink .= $linkitem['name'];
                                if ( !empty( $caturl ) ) {
                                    $catlink .= '</a>';
                                }
                            }

                            if ( $showcategorydesclinks ) {
                                $catlink .= '<span class="linklistcatnamedesc">';
                                $linkitem['description'] = str_replace( '[', '<', $linkitem['description'] );
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
                                $catlink .= $linkitem['name'];
                                if ( !empty( $caturl ) ) {
                                    $catlink .= '</a>';
                                }
                            }

                            if ( $showlinksonclick ) {
                                $catlink .= '<span class="expandlinks" id="LinksInCat' . $linkitem['term_id'] . '">';
                                $catlink .= '<img src="';

                                if ( !empty( $expandiconpath ) ) {
                                    $catlink .= $expandiconpath;
                                } else {
                                    $catlink .= plugins_url( 'icons/expand-32.png', __FILE__ );
                                }

                                $catlink .= '" />';

                                $catlink .= '</span>';
                            }

                            $catlink .= '</div>';
                        } else if ( $divorheader ) {
                            if ( 'search' == $mode ) {
                                foreach ( $searchterms as $searchterm ) {
                                    $linkitem['name'] = link_library_highlight_phrase( $linkitem['name'], $searchterm, '<span class="highlight_word">', '</span>' );
                                }
                            }

                            $catlink = '<'. $catnameoutput . '>';

                            if ( 'right' == $catdescpos || empty( $catdescpos ) ) {
                                if ( !empty( $caturl ) ) {
                                    $catlink .= '<a href="' . link_library_add_http( $caturl ). '" ';

                                    if ( !empty( $linktarget ) )
                                        $catlink .= ' target="' . $linktarget . '"';

                                    $catlink .= '>';
                                }
                                $catlink .= $linkitem['name'];
                                if ( !empty( $caturl ) ) {
                                    $catlink .= '</a>';
                                }
                            }

                            if ( $showcategorydesclinks ) {
                                $catlink .= '<span class="linklistcatnamedesc">';
                                $linkitem['description'] = str_replace( '[', '<', $linkitem['description'] );
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
                                $catlink .= $linkitem['name'];
                                if ( !empty( $caturl ) ) {
                                    $catlink .= '</a>';
                                }
                            }

                            if ( $showlinksonclick ) {
                                $catlink .= '<span class="expandlinks" id="LinksInCat' . $linkitem['term_id'] . '">';
                                $catlink .= '<img src="';

                                if ( !empty( $expandiconpath ) ) {
                                    $catlink .= $expandiconpath;
                                } else {
                                    $catlink .= plugins_url( 'icons/expand-32.png', __FILE__ );
                                }

                                $catlink .= '" />';
                                $catlink .= '</span>';
                            }

                            $catlink .= '</' . $catnameoutput . '>';
                        }

                        if ($catanchor) {
                            $catenddiv = '</div>';
                        } else {
                            $catenddiv = '';
                        }
                    }

                    $output .= $cattext . $catlink . $catenddiv;

                    if ( $showlinksonclick ) {
                        $output .= '<div class="LinksInCat' . $currentcategoryid . ' LinksInCat">';
                    }
                }

                if ( $displayastable && ( ! $combineresults || ( $combineresults && $linkcount == 0 ) ) ) {
                    $catstartlist = "\n\t<table class='linklisttable'>\n";
                    if ( $showcolumnheaders ) {
                        $catstartlist .= '<div class="linklisttableheaders"><tr>';

                        if ( !empty( $linkheader ) ) {
                            $catstartlist .= '<th><div class="linklistcolumnheader">' . $linkheader . '</div></th>';
                        }

                        if ( !empty( $descheader ) ) {
                            $catstartlist .= '<th><div class="linklistcolumnheader">' . $descheader . '</div></th>';
                        }

                        if ( !empty( $notesheader ) ) {
                            $catstartlist .= '<th><div class="linklistcolumnheader">' . $notesheader . '</div></th>';
                        }

                        $catstartlist .= "</tr></div>\n";
                    } else {
                        $catstartlist .= '';
                    }
                } elseif ( ! $combineresults || ( $combineresults && $linkcount == 0 ) ) {
                    $catstartlist = "\n\t<ul>\n";
                } else {
                    $catstartlist = '';
                }

                $output .= $catstartlist;

                if ( !empty( $beforefirstlink ) ) {
                    $output .= stripslashes( $beforefirstlink );
                }
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
                    $output .= "\n<!-- Time to render RSS Feed section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerssfeed ) . " --> \n";
                }
            }

            if ( $showuserlinks || strpos( $linkitem['link_description'], 'LinkLibrary:AwaitingModeration:RemoveTextToApprove' ) == false ) {
                $linkcount = $linkcount + 1;

                if ( $linkaddfrequency > 0 ) {
                    if ( ( $linkcount - 1 ) % $linkaddfrequency == 0 ) {
                        $output .= stripslashes( $addbeforelink );
                    }
                }

                if ( !isset( $linkitem['recently_updated'] ) ) {
                    $linkitem['recently_updated'] = false;
                }

                $output .= stripslashes( $beforeitem );

                if ( $showupdated && $linkitem['recently_updated'] ) {
                    $output .= get_option( 'links_recently_updated_prepend' );
                }

                $the_link = '#';
                if ( !empty( $linkitem['link_url'] ) ) {
                    $the_link = esc_html( $linkitem['link_url'] );
                }

                $the_second_link = '#';
                if ( !empty( $linkitem['link_second_url'] ) ) {
                    $the_second_link = esc_html( stripslashes( $linkitem['link_second_url'] ) );
                }

                $rel = $linkitem['link_rel'];
                if ( !empty( $rel ) && !$nofollow && !$linkitem['link_no_follow'] ) {
                    $rel = ' rel="' . $rel . '"';
                } elseif ( !empty( $rel ) && ( $nofollow || $linkitem['link_no_follow'] ) ) {
                    $rel = ' rel="' . $rel . ' nofollow"';
                } elseif ( empty( $rel ) && ( $nofollow || $linkitem['link_no_follow'] ) ) {
                    $rel = ' rel="nofollow"';
                }

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

                if ( $showupdated ) {
                    if ( substr( $linkitem['link_updated'], 0, 2 ) != '00' ) {
                        $title .= ' ('.__('Last updated', 'link-library') . '  ' . date_i18n(get_option('links_updated_date_format'), strtotime( $linkitem['link_updated'] ) ) .')';
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
                    $dragndroporder = '1,2,3,4,5,6,7,8,9,10,11,12,13,14';
                }

                $dragndroparray = explode( ',', $dragndroporder );

                if ( !in_array( '13', $dragndroparray ) ) {
                    $dragndroparray[] = '13';
                }

	            if ( !in_array( '14', $dragndroparray ) ) {
		            $dragndroparray[] = '14';
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

										$imageoutput .= '" id="link-' . $linkitem['proper_link_id'] . '" class="' . ( $enable_link_popup ? 'thickbox' : 'track_this_link' ) . ' ' . ( $linkitem['link_featured'] ? 'featured' : '' ). '" ' . $rel . $title . $target. '>';

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

											$imageoutput .= '/>';

											$imageoutput .= '</a>';
										}

										if ( true == $debugmode ) {
											$output .= '<!-- Time to render image section of link id ' . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimeimage ) . " --> \n";
										}

									}

									$imageoutput .= stripslashes( $afterimage );

									if ( ( !empty( $imageoutput ) || ( $usethumbshotsforimages && !empty( $thumbshotscid ) ) ) && $show_images ) {
										$output .= $imageoutput;
									}

									break;
								}

                            case 2: 	//------------------ Name Output --------------------
                                if ( ( $showname && 2 == $arrayelements && ( !$nooutputempty || ( $nooutputempty && !empty( $name ) ) ) ) ||
                                     ( $show_images && $shownameifnoimage && empty( $linkitem['link_image'] ) && !$usethumbshotsforimages && 1 == $arrayelements && ( !$nooutputempty || ( $nooutputempty && !empty( $name ) ) ) ) ) {
                                    if ( true == $debugmode ) {
                                        $starttimename = microtime ( true );
                                    }

                                    $output .= stripslashes( $beforelink );

                                    if ( ( 'primary' == $sourcename && $the_link != '#') || ( 'secondary' == $sourcename && $the_second_link != '#' ) ) {
                                        $output .= '<a href="';

                                        if ( !$enable_link_popup ) {
                                            if ( 'primary' == $sourcename || empty( $sourcename ) ) {
                                                $output .= $the_link;
                                            } elseif ( 'secondary' == $sourcename ) {
                                                $output .= $the_second_link;
                                            }
                                        } else {
                                            $output .= admin_url( 'admin-ajax.php' . '?action=link_library_popup_content&linkid=' . $linkitem['proper_link_id'] . '&settings=' . $settings . '&height=' . ( empty( $popup_height ) ? 300 : $popup_height ) . '&width=' . ( empty( $popup_width ) ? 400 : $popup_width ) . '&xpath=' . $xpath );
                                        }

                                        if ( 'description' == $tooltipname && !empty( $desc ) ) {
	                                        $title = ' title="' . $desc . '"';
                                        }

                                        $output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="' . ( $enable_link_popup ? 'thickbox' : 'track_this_link' ) . ' ' . ( $linkitem['link_featured'] ? ' featured' : '' ). '" ' . $rel . $title . $target. '>';
                                    }

                                    $output .= $name;

                                    if ( ( 'primary' == $sourcename && $the_link != '#') || ( 'secondary' == $sourcename && $the_second_link != '#' ) ) {
                                        $output .= '</a>';
                                    }

                                    if ( $showadmineditlinks && $linkeditoruser ) {
                                        $output .= $between . '<span class="editlink"><a href="' . esc_url( add_query_arg( array(
                                                'action' => 'edit', 'link_id' => $linkitem['proper_link_id'] ),
                                                admin_url( 'link.php' ) ) ) . '">(' . __('Edit', 'link-library') . ')</a></span>';
                                    }

                                    if ( $showupdated && $linkitem['recently_updated'] ) {
                                        $output .= get_option( 'links_recently_updated_append' );
                                    }

                                    $output .= stripslashes( $afterlink );

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render name section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimename ) . " --> \n";
                                    }
                                }

                                break;

                            case 3: 	//------------------ Date Output --------------------

                                if ( $showdate && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_date'] ) ) ) ) {
                                    if ( true == $debugmode ) {
                                        $starttimedate = microtime ( true );
                                    }

                                    $formatteddate = date_i18n( get_option( 'links_updated_date_format' ), $linkitem['link_date'] );

                                    $output .= $between . stripslashes( $beforedate ) . $formatteddate . stripslashes( $afterdate );

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render date section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedate ) . " --> \n";
                                    }
                                }

                                break;

                            case 4: 	//------------------ Description Output --------------------

                                if ( $showdescription && ( !$nooutputempty || ( $nooutputempty && !empty( $desc ) ) ) ) {
                                    if ( true == $debugmode ) {
                                        $starttimedesc = microtime ( true );
                                    }

                                    $output .= $between . stripslashes( $beforedesc ) . $desc . stripslashes( $afterdesc );

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
                                    }
                                }

                                break;

                            case 5: 	//------------------ Notes Output --------------------

                                if ( $shownotes && ( !$nooutputempty || ( $nooutputempty && !empty( $descnotes ) ) ) ) {
                                    if ( true == $debugmode ) {
                                        $starttimenotes = microtime ( true );
                                    }

                                    $output .= $between . stripslashes( $beforenote ) . $descnotes . stripslashes( $afternote );

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render notes section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimenotes ) . " --> \n";
                                    }
                                }

                                break;

                            case 6: 	//------------------ RSS Icons Output --------------------

                                if ( ( $show_rss || $show_rss_icon || $rsspreview ) && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_rss'] ) ) ) ) {
                                    if ( true == $debugmode ) {
                                        $starttimerssicon = microtime ( true );
                                    }

                                    $output .= stripslashes( $beforerss ) . '<div class="rsselements">';

                                    if ( $show_rss && !empty( $linkitem['link_rss'] ) ) {
                                        $output .= $between . '<a class="rss" href="' . $linkitem['link_rss'] . '">RSS</a>';
                                    }

                                    if ( $show_rss_icon && !empty( $linkitem['link_rss'] ) ) {
                                        $output .= $between . '<a class="rssicon" href="' . $linkitem['link_rss'] . '"><img src="' . plugins_url( 'icons/feed-icon-14x14.png', __FILE__ ) . '" /></a>';
                                    }

                                    if ( $rsspreview && !empty( $linkitem['link_rss'] ) ) {
                                        $output .= $between . '<a href="' . home_url() . '/?link_library_rss_preview=1&keepThis=true&linkid=' . $linkitem['proper_link_id'] . '&previewcount=' . $rsspreviewcount . 'height=' . ( empty( $rsspreviewwidth ) ?  900 : $rsspreviewwidth ) . '&width=' . ( empty( $rsspreviewheight ) ? 700 : $rsspreviewheight ) . '&xpath=' . urlencode( $xpath ) . '" title="' . __('Preview of RSS feed for', 'link-library') . ' ' . $cleanname . '" class="thickbox"><img src="' . plugins_url( 'icons/preview-16x16.png', __FILE__ ) . '" /></a>';
                                    }

                                    $output .= '</div>' . stripslashes( $afterrss );

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render RSS Icon section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerssicon ) . " --> \n";
                                    }
                                }

                                if ( $rssfeedinline && $linkitem['link_rss'] ) {
                                    if ( $rss_items ) {
                                        $output .= '<div id="ll_rss_results">';
	                                    $date_format_string = get_option( 'date_format' );

                                        foreach ( $rss_items as $item ) {
                                            $output .= '<div class="chunk" style="padding:0 5px 5px;">';
	                                        $item_timestamp = strtotime( $item->get_date( 'j F Y | g:i a' ) );

	                                        $formatted_date = date_i18n( $date_format_string, $item_timestamp );
                                            $output .= '<div class="rsstitle"><a target="feedwindow" href="' . $item->get_permalink() . '">' . $item->get_title() . '</a> - ' . $formatted_date . '</div>';

                                            if ( $rssfeedinlinecontent ) {
                                                $output .= '<div class="rsscontent">' . $item->get_description() . '</div>';
                                            }

                                            $output .= '</div>';
                                            $output .= '<br />';
                                        }

                                        $output .= '</div>';
                                    }
                                }
                                break;
                            case 7: 	//------------------ Web Link Output --------------------

                                if ( 'false' != $displayweblink &&
                                     ( !$nooutputempty ||
                                       ( $nooutputempty && !empty( $the_link ) && 'address' == $displayweblink && 'primary' == $sourceweblink ) ||
                                       ( $nooutputempty && !empty( $the_second_link ) && 'address' == $displayweblink && 'secondary' == $sourceweblink ) ||
                                       ( $nooutputempty && !empty( $weblinklabel ) && 'label' == $displayweblink )
                                     ) ) {
                                    if ( true == $debugmode ) {
                                        $starttimerweblink = microtime ( true );
                                    }

                                    if ( 'addressonly' == $displayweblink ) {
                                        $output .= $between . stripslashes( $beforeweblink );
                                        $output .= $the_link;
                                        $output .= stripslashes( $afterweblink );
                                    } else {
                                        $output .= $between . stripslashes( $beforeweblink ) . '<a href="';

                                        if ( 'primary' == $sourceweblink || empty( $sourceweblink ) ) {
                                            $output .= $the_link;
                                        } elseif ( 'secondary' == $sourceweblink ) {
                                            $output .= $the_second_link;
                                        }

                                        $output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="track_this_link" ' . $target . '>';

                                        if ( 'address' == $displayweblink ) {
                                            if ( ( 'primary' == $sourceweblink || empty( $sourceweblink ) ) && ! empty( $the_link ) ) {
                                                $output .= $the_link;
                                            } elseif ( 'secondary' == $sourceweblink && ! empty( $the_second_link ) ) {
                                                $output .= $the_second_link;
                                            }
                                        } elseif ( 'label' == $displayweblink && ! empty( $weblinklabel ) ) {
                                            $output .= $weblinklabel;
                                        }

                                        $output .= '</a>' . stripslashes( $afterweblink );
                                    }

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render web link section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerweblink ) . " --> \n";
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

                                    $output .= $between . stripslashes( $beforetelephone );

                                    if ( 'plain' != $showtelephone ) {
                                        $output .= '<a href="';

                                        if ( ( 'primary' == $sourcetelephone || empty( $sourcetelephone ) ) && !empty( $the_link ) ) {
                                            $output .= $the_link;
                                        } elseif ( 'secondary' == $sourcetelephone && !empty( $the_second_link ) ) {
                                            $output .= $the_second_link;
                                        }

                                        $output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="track_this_link" >';
                                    }

                                    if ( 'link' == $showtelephone || 'plain' == $showtelephone ) {
                                        $output .= $linkitem['link_telephone'];
                                    } elseif ( 'label' == $showtelephone ) {
                                        $output .= $telephonelabel;
                                    }

                                    if ( 'plain' != $showtelephone ) {
                                        $output .= '</a>';
                                    }

                                    $output .= stripslashes( $aftertelephone );

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render telephone section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimertelephone ) . " --> \n";
                                    }
                                }
                                break;
                            case 9: 	//------------------ E-mail Output --------------------

                                if ( 'false' != $showemail && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_email'] ) ) ) ) {
                                    if ( true == $debugmode ) {
                                        $starttimeremail = microtime ( true );
                                    }

                                    $output .= $between . stripslashes( $beforeemail );

                                    if ( 'plain' != $showemail ) {
                                        $output .= '<a href="';

                                        if ( 'mailto' == $showemail || 'mailtolabel' == $showemail ) {
                                            if ( false === strpos( $linkitem['link_email'], '@' ) ) {
                                                $output .= esc_url( $linkitem['link_email'] );
                                            } else {
                                                $output .= 'mailto:' . $linkitem['link_email'];
                                            }
                                        } elseif ( 'command' == $showemail || 'commandlabel' == $showemail ) {
                                            $newcommand = str_replace( '#email', $linkitem['link_email'], $emailcommand );
                                            $cleanlinkname = str_replace( ' ', '%20', $linkitem['link_name'] );
                                            $newcommand = str_replace( '#company', $cleanlinkname, $newcommand );
                                            $output .= $newcommand;
                                        }

                                        $output .= '">';
                                    }

                                    if ( 'plain' == $showemail || 'mailto' == $showemail || 'command' == $showemail ) {
                                        $output .= $linkitem['link_email'];
                                    } elseif ( 'mailtolabel' == $showemail || 'commandlabel' == $showemail ) {
                                        $output .= $emaillabel;
                                    }

                                    if ( 'plain' != $showemail ) {
                                        $output .= '</a>';
                                    }

                                    $output .= stripslashes( $afteremail );

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render e-mail section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimeremail ) . " --> \n";
                                    }
                                }

                                break;
                            case 10: 	//------------------ Link Hits Output --------------------

                                if ( $showlinkhits && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_visits'] ) ) ) ) {
                                    if ( true == $debugmode ) {
                                        $starttimerhits = microtime ( true );
                                    }

                                    $output .= $between . stripslashes( $beforelinkhits );
                                    $output .= $linkitem['link_visits'];
                                    $output .= stripslashes( $afterlinkhits );

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render link hits section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerhits ) . " --> \n";
                                    }
                                }

                                break;

                            case 11: 	//------------------ Link Rating Output --------------------

                                if ( $showrating && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_rating'] ) ) ) ) {
                                    if ( true == $debugmode ) {
                                        $starttimerrating = microtime ( true );
                                    }

                                    $output .= $between . stripslashes( $beforelinkrating );
                                    $output .= $linkitem['link_rating'];
                                    $output .= stripslashes( $afterlinkrating );

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render link rating section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerrating ) . " --> \n";
                                    }
                                }

                                break;

                            case 12: 	//------------------ Link Large Description Output --------------------

                            if ( $showlargedescription && ( !$nooutputempty || ( $nooutputempty && !empty( $textfield ) ) ) ) {
                                if ( true == $debugmode ) {
                                    $starttimerlargedesc = microtime ( true );
                                }

                                $output .= $between . stripslashes( $beforelargedescription );
                                $output .= $textfield;
                                $output .= stripslashes( $afterlargedescription );

                                if ( true == $debugmode ) {
                                    $output .= "\n<!-- Time to render link large description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerlargedesc ) . " --> \n";
                                }
                            }

                            break;

                            case 13: 	//------------------ Link Large Description Output --------------------

                                if ( $showsubmittername && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_submitter_name'] ) ) ) ) {
                                    if ( true == $debugmode ) {
                                        $starttimersubmittername = microtime ( true );
                                    }

                                    $output .= $between . stripslashes( $beforesubmittername );
                                    $output .= $linkitem['link_submitter_name'];
                                    $output .= stripslashes( $aftersubmittername );

                                    if ( true == $debugmode ) {
                                        $output .= "\n<!-- Time to render link large description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimersubmittername ) . " --> \n";
                                    }
                                }

                                break;

	                        case 14: 	//------------------ Category Description Output --------------------

		                        $linkitem['description'] = str_replace( '[', '<', $linkitem['description'] );
		                        $linkitem['description'] = str_replace( ']', '>', $linkitem['description'] );

		                        if ( $showcatdesc && ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['description'] ) ) ) ) {
			                        if ( true == $debugmode ) {
				                        $starttimedesc = microtime ( true );
			                        }

			                        $output .= $between . stripslashes( $beforecatdesc ) . $linkitem['description'] . stripslashes( $aftercatdesc );

			                        if ( true == $debugmode ) {
				                        $output .= "\n<!-- Time to render description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
			                        }
		                        }

		                        break;
                        }
                    }
                }

                $output .= stripslashes( $afteritem ) . "\n";

                if ( $linkaddfrequency > 0 ) {
                    if ( 0 == $linkcount % $linkaddfrequency ) {
                        $output .= stripslashes( $addafterlink );
                    }
                }
            }

            if ( true == $debugmode ) {
                $output .= '<!-- Time to render link id ' . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $linkstarttime ) . " --> \n";
            }

        } // end while

        // Close the last category
        if ( $displayastable ) {
            $output .= "\t</table>\n";
        } else {
            $output .= "\t</ul>\n";
        }

        if ( !empty( $catlistwrappers ) && ! $combineresults ) {
            $output .= '</div>';
        }

        if ( $usethumbshotsforimages ) {
            if ( $thumbnailgenerator == 'robothumb' ) {
                $output .= '<div class="llthumbshotsnotice"><a href="http://www.robothumb.com">Screenshots par Robothumb</a></div>';
            } elseif ( $thumbnailgenerator == 'thumbshots' ) {
                $output .= '<div class="llthumbshotsnotice"><a href="http://www.thumbshots.com" target="_blank" title="Thumbnails Screenshots by Thumbshots">Thumbnail Screenshots by Thumbshots</a></div>';
            }
        }

        if ( $showlinksonclick ) {
            $output .= '</div>';
        }

        if ( ! $combineresults ) {
            $output .= '</div>';
        }

        if ( $pagination && 'search' != $mode && ( 'AFTER' == $paginationposition || empty( $pagination ) ) ) {
            $previouspagenumber = $pagenumber - 1;
            $nextpagenumber = $pagenumber + 1;
            $pageID = get_the_ID();

            $output .= link_library_display_pagination( $previouspagenumber, $nextpagenumber, $numberofpages, $pagenumber, $showonecatonly, $showonecatmode, $AJAXcatid, $settings, $pageID, $currentcatletter );
        }

        $xpath = $LLPluginClass->relativePath( dirname( __FILE__ ), ABSPATH );
        $nonce = wp_create_nonce( 'll_tracker' );

        $output .= "<script type='text/javascript'>\n";
        $output .= "jQuery(document).ready(function()\n";
        $output .= "{\n";
        $output .= "jQuery('#linklist" . $settings . " a.track_this_link').click(function() {\n";
        $output .= "linkid = this.id;\n";
        $output .= "linkid = linkid.substring(5);";
        $output .= "path = '" . $xpath . "';";
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
        $currentcategory = $currentcategory + 1;

        $output .= "</div>\n";

    } else if ( isset( $_GET['searchll'] ) ) {
        $output .= "<div id='linklist" . $settings . "' class='linklist'>\n";
        $output .= $searchnoresultstext . "\n";
        $output .= "</div>";
    } else {
        $output .= "<div id='linklist" . $settings . "' class='linklist'>\n";
        $output .= __('No links found', 'link-library') . ".\n";
        $output .= "</div>";
    }

    $output .= "\n<!-- End of Link Library Output -->\n\n";

    return do_shortcode( $output );
}
