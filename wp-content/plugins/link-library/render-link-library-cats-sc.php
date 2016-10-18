<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

/**
 *
 * Render the output of the link-library-cats shortcode
 *
 * @param $LLPluginClass    Link Library main plugin class
 * @param $generaloptions   General Plugin Settings
 * @param $libraryoptions   Selected library settings array
 * @param $settings         Settings ID
 * @return                  List of categories output for browser
 */

function RenderLinkLibraryCategories( $LLPluginClass, $generaloptions, $libraryoptions, $settings ) {

    global $wpdb;

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

    $output = '';

    $categoryid = '';

    if ( isset($_GET['cat_id'] ) ) {
        $categoryid = intval( $_GET['cat_id'] );
    } elseif ( isset( $_GET['catname'] ) ) {
        $categoryterm = get_term_by( 'name', urldecode( $_GET['catname'] ), 'link_category' );
	    $categoryid = $categoryterm->term_id;
    } elseif ( $showonecatonly ) {
	    $categoryid = $defaultsinglecat;
    }

    if ( !isset( $_GET['searchll'] ) || true == $showcatonsearchresults ) {
        $countcat = 0;

        $order = strtolower( $order );

        $output .= "<!-- Link Library Categories Output -->\n\n";

        if ( $showonecatonly && ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) ) {
            $nonce = wp_create_nonce( 'link_library_ajax_refresh' );

            $output .= "<SCRIPT LANGUAGE=\"JavaScript\">\n";
            $output .= "var ajaxobject;\n";
            $output .= "function showLinkCat" . $settings . " ( _incomingID, _settingsID, _pagenumber ) {\n";
            $output .= "if (typeof(ajaxobject) != \"undefined\") { ajaxobject.abort(); }\n";

            $output .= "\tjQuery('#contentLoading" . $settings . "').toggle();" .
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
            $output .= "}\n";

            $output .= "</SCRIPT>\n\n";
        }

        // Handle link category sorting
        $direction = 'ASC';
        if ( '_' == substr( $order, 0, 1 ) ) {
            $direction = 'DESC';
            $order = substr( $order, 1 );
        }

	    $currentcatletter = '';

	    if ( $cat_letter_filter != 'no' ) {
		    require_once plugin_dir_path( __FILE__ ) . 'render-link-library-alpha-filter.php';
		    $result = RenderLinkLibraryAlphaFilter( $LLPluginClass, $generaloptions, $libraryoptions, $settings, 'normal' );

		    $currentcatletter = $result['currentcatletter'];

		    if ( 'beforecats' == $cat_letter_filter || 'beforecatsandlinks' == $cat_letter_filter ) {
			    $output .= $result['output'];
		    }
	    }

        $linkcatquery = 'SELECT ';

        if ( $showcatlinkcount || $pagination ) {
            $linkcatquery .= 'count(l.link_name) as linkcount, ';
        }

        $linkcatquery .= 't.name, t.term_id, t.slug as category_nicename, tt.description as category_description ';
        $linkcatquery .= 'FROM ' . $LLPluginClass->db_prefix() . 'terms t LEFT JOIN ' . $LLPluginClass->db_prefix(). 'term_taxonomy tt ON (t.term_id = tt.term_id)';
        $linkcatquery .= ' LEFT JOIN ' . $LLPluginClass->db_prefix() . 'term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ';
        $linkcatquery .= ' LEFT JOIN ' . $LLPluginClass->db_prefix() . 'links l on (tr.object_id = l.link_id';

        if ( false == $showinvisible ) {
            $linkcatquery .= ' AND l.link_visible != "N" ';
        }

        if ( !$showuserlinks ) {
            $linkcatquery .= ' AND l.link_description not like \'%LinkLibrary:AwaitingModeration:RemoveTextToApprove%\' ';
        }

        $linkcatquery .= ' ) ';

        $linkcatquery .= 'WHERE tt.taxonomy = "link_category"';

	    if ( !empty( $currentcatletter ) ) {
		    $linkcatquery .= ' AND substring(t.name, 1, 1) = "' . $currentcatletter . '"';
	    }

        if ( !empty( $categorylist ) ) {
            $linkcatquery .= ' AND t.term_id in ( ' . $categorylist . ' )';
        }

        if ( !empty( $excludecategorylist ) ) {
            $linkcatquery .= ' AND t.term_id not in ( ' . $excludecategorylist . ' )';
        }

        if ( $hide_if_empty ) {
            $linkcatquery .= ' AND l.link_name != "" ';
        }

        $linkcatquery .= ' GROUP BY t.name ';

        $validdirections = array( 'ASC', 'DESC' );

        if ( 'name' == $order ) {
            $linkcatquery .= ' ORDER by t.name ' . ( in_array( $direction, $validdirections ) ? $direction : 'ASC' );
        } elseif ( 'id' == $order ) {
            $linkcatquery .= ' ORDER by t.term_id ' . ( in_array( $direction, $validdirections ) ? $direction : 'ASC' );
        } elseif ( 'order' == $order ) {
            $linkcatquery .= ' ORDER by t.term_order ' . ( in_array( $direction, $validdirections ) ? $direction : 'ASC' );
        } elseif ( 'catlist' == $order ) {
            $linkcatquery .= ' ORDER by FIELD(t.term_id, ' . $categorylist . ' ) ';
        }

        $catnames = $wpdb->get_results( $linkcatquery );

        if ( $debugmode ) {
            $output .= "\n<!-- Category Query: " . print_r( $linkcatquery, TRUE ) . "-->\n\n";
            $output .= "\n<!-- Category Results: " . print_r( $catnames, TRUE ) . "-->\n\n";
        }

        // Display each category

        if ( $catnames ) {

            $output .=  '<div id="linktable" class="linktable">';

            if ( 'table' == $flatlist ) {
                $output .= "<table width=\"" . $table_width . "%\">\n";
            } elseif ( 'unordered' == $flatlist ) {
                $output .= "<ul class='menu'>\n";
            } elseif ( 'dropdown' == $flatlist || 'dropdowndirect' == $flatlist ) {
                $output .= "<form name='catselect'><select ";
	            if ( 'dropdowndirect' == $flatlist ) {
		            $output .= "onchange='showcategory()' ";
	            }
	            $output .= "name='catdropdown' class='catdropdown'>";
            }

            $linkcount = 0;

            foreach ( (array) $catnames as $catname ) {

                $catfront = '';
                $cattext = '';
                $catitem = '';

                // Display the category name
                $countcat += 1;
                if ( $num_columns > 0 && 'table' == $flatlist && ( ( 1 == $countcat % $num_columns ) || ( 1 == $num_columns ) ) ) {
                    $output .= "<tr>\n";
                }

                if ( 'table' == $flatlist ) {
                    $catfront = "\t<td>";
                } elseif ( 'unordered' == $flatlist ) {
                    $catfront = "\t<li>";
                } elseif ( 'dropdown' == $flatlist || 'dropdowndirect' == $flatlist ) {
                    $catfront = "\t<option ";
                    if ( !empty( $categoryid ) && $categoryid == $catname->term_id ) {
                        $catfront .= 'selected="selected" ';
                    }
                    $catfront .= 'value="';
                }

                if ( $showonecatonly ) {
                    if ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) {
                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext = "<a href='#' onClick=\"showLinkCat" . $settings . "('" . $catname->term_id. "', '" . $settings . "', 1);return false;\" >";
                        } elseif ( 'dropdown' == $flatlist || 'dropdowndirect' == $flatlist ) {
                            $cattext = $catname->term_id;
                        }
                    } elseif ( 'HTMLGET' == $showonecatmode ) {
                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext = "<a href='";
                        }

                        if ( !empty( $cattargetaddress ) && strpos( $cattargetaddress, '?' ) != false ) {
                            $cattext .= $cattargetaddress . '&cat_id=';
                        } elseif ( !empty( $cattargetaddress ) && strpos( $cattargetaddress, '?' ) == false ) {
                            $cattext .= $cattargetaddress . '?cat_id=';
                        } elseif ( empty( $cattargetaddress ) ) {
                            $cattext .= '?cat_id=';
                        }

                        $cattext .= $catname->term_id;

                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext .= "'>";
                        }
                    } elseif ( 'HTMLGETSLUG' == $showonecatmode ) {
                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext = "<a href='";
                        }

                        if ( !empty( $cattargetaddress ) && strpos( $cattargetaddress, '?' ) != false ) {
                            $cattext .= $cattargetaddress . '&cat=';
                        } elseif ( !empty( $cattargetaddress ) && strpos( $cattargetaddress, '?' ) == false ) {
                            $cattext .= $cattargetaddress . '?cat=';
                        } elseif ( empty( $cattargetaddress ) ) {
                            $cattext .= '?cat=';
                        }

                        $cattext .= $catname->category_nicename;

                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext .= "'>";
                        }
                    }  elseif ( 'HTMLGETCATNAME' == $showonecatmode ) {
                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext = "<a href='";
                        }

                        /* if ( !empty( $cattargetaddress ) && strpos( $cattargetaddress, '?' ) != false ) {
                            $cattext .= $cattargetaddress . '&catname=';
                        } elseif ( !empty( $cattargetaddress ) && strpos( $cattargetaddress, '?' ) == false ) {
                            $cattext .= $cattargetaddress . '?catname=';
                        } elseif ( empty( $cattargetaddress ) ) {
                            $cattext .= '?catname=';
                        } */

	                    if ( !empty( $_GET ) ) {
		                    $get_array = $_GET;
	                    } else {
		                    $get_array = array();
	                    }

	                    $get_array['catname'] = urlencode( $catname->name );
	                    $get_query = add_query_arg( $get_array, $cattargetaddress );

	                    $cattext .= $get_query;

                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext .= "'>";
                        }
                    } elseif ( 'HTMLGETPERM' == $showonecatmode ) {
                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext = "<a href='";
                        }

                        $cattext .= '/' . $rewritepage . '/' . $catname->category_nicename;

                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext .= "'>";
                        }
                    }
                } else if ( $catanchor ) {
                    if ( !$pagination ) {
                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext = '<a href="';
                        }

                        $cattext .= '#' . $catname->category_nicename;

                        if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                            $cattext .= '">';
                        }
                    } elseif ( $pagination ) {
                        if ( 0 == $linksperpage || empty( $linksperpage ) ) {
                            $linksperpage = 5;
                        }

                        $pageposition = ( $linkcount + 1 ) / $linksperpage;
                        $ceilpageposition = ceil( $pageposition );
                        if ( 0 == $ceilpageposition && !isset( $_GET['linkresultpage'] ) ) {
                            if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                                $cattext = '<a href="';
                            }

                            $cattext .= get_permalink() . '#' . $catname->category_nicename;

                            if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                                $cattext .= '">';
                            }
                        } else {
                            if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                                $cattext = '<a href="';
                            }

                            $cattext .= '?linkresultpage=' . ( $ceilpageposition == 0 ? 1 : $ceilpageposition ) . '#' . $catname->category_nicename;

                            if ( 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                                $cattext .= '">';
                            }
                        }

                        $linkcount = $linkcount + $catname->linkcount;
                    }
                } else {
                    $cattext = '';
                }

                if ( 'dropdown' == $flatlist || 'dropdowndirect' == $flatlist ) {
                    $cattext .= '">';
                }

                if ( 'right' == $catlistdescpos || empty( $catlistdescpos ) ) {
                    $catitem .= '<div class="linkcatname">' . $catname->name;
                    if ($showcatlinkcount) {
                        $catitem .= '<span class="linkcatcount"> (' . $catname->linkcount . ')</span>';
                    }
					$catitem .= '</div>';
                }

                if ( $showcategorydescheaders ) {
                    $catname->category_description = esc_html( $catname->category_description );
                    $catname->category_description = str_replace( '[', '<', $catname->category_description );
                    $catname->category_description = str_replace( ']', '>', $catname->category_description );
                    $catname->category_description = str_replace( "&quot;", '"', $catname->category_description );
                    $catitem .= '<span class="linkcatdesc">' . $catname->category_description . '</span>';
                }

                if ( 'left' == $catlistdescpos ) {
                    $catitem .= '<div class="linkcatname">' . $catname->name;
                    if ( $showcatlinkcount ) {
                        $catitem .= '<span class="linkcatcount"> (' . $catname->linkcount . ')</span>';
                    }
					$catitem .= '</div>';
                }

                if ( ( $catanchor || $showonecatonly ) && 'dropdown' != $flatlist && 'dropdowndirect' != $flatlist ) {
                    $catitem .= '</a>';
                }

                $output .= ( $catfront . $cattext . $catitem );

                if ( 'table' == $flatlist ) {
                    $catterminator = "</td>\n";
                } elseif ( 'unordered' == $flatlist ) {
                    $catterminator = "</li>\n";
                } elseif ( 'dropdown' == $flatlist || 'dropdowndirect' == $flatlist ) {
                    $catterminator = "</option>\n";
                }

                $output .= ( $catterminator );

                if ( $num_columns > 0 && 'table' == $flatlist and ( 0 == $countcat % $num_columns ) ) {
                    $output .= "</tr>\n";
                }
            }

            if ( $num_columns > 0 && 'table' == $flatlist and ( 3 == $countcat % $num_columns ) ) {
                $output .= "</tr>\n";
            }

            if ( 'table' == $flatlist && $catnames ) {
                $output .= "</table>\n";
            } elseif ( 'unordered' == $flatlist && $catnames ) {
                $output .= "</ul>\n";
            } elseif ( ( 'dropdown' == $flatlist || 'dropdowndirect' == $flatlist ) && $catnames ) {
                $output .= "</select>\n";
	            if ( 'dropdown' == $flatlist ) {
		            $output .= "<button type='button' onclick='showcategory()'>" . __('Go!', 'link-library') . "</button>";
	            }
                $output .= '</form>';
            }

            $output .= "</div>\n";

            if ( $showonecatonly && ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) ) {
                if ( empty( $loadingicon ) ) {
                    $loadingicon = '/icons/Ajax-loader.gif';
                }

                $output .= "<div class='contentLoading' id='contentLoading" . $settings . "' style='display: none;'><img src='" . plugins_url( $loadingicon, __FILE__ ) . "' alt='Loading data, please wait...'></div>\n";
            }

            if ( 'dropdown' == $flatlist || 'dropdowndirect' == $flatlist ) {
                $output .= "<SCRIPT TYPE='text/javascript'>\n";
                $output .= "\tfunction showcategory(){\n";

                if ( $showonecatonly && ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) ) {
                    $output .= 'catidvar = document.catselect.catdropdown.options[document.catselect.catdropdown.selectedIndex].value;';
                    $output .= "showLinkCat" . $settings . "(catidvar, '" . $settings . "', 1);return false; }";
                } else {
                    $output .= "\t\tlocation=\n";
                    $output .= "document.catselect.catdropdown.options[document.catselect.catdropdown.selectedIndex].value }\n";
                }
                $output .= "</SCRIPT>\n";
            }
        } else {
            $output .= '<div>' . __('No categories found', 'link-library') . '.</div>';
        }

        $output .= "\n<!-- End of Link Library Categories Output -->\n\n";
    }
    return $output;
}