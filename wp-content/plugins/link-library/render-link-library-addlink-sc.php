<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

/**
 *
 * Render the output of the link-library-search shortcode
 *
 * @param $LLPluginClass    Link Library main plugin class
 * @param $generaloptions   General Plugin Settings
 * @param $libraryoptions   Selected library settings array
 * @param $settings         Settings ID
 * @param $code             Shortcode used in text that called this function
 * @return                  List of categories output for browser
 */

function RenderLinkLibraryAddLinkForm( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $code ) {

    wp_enqueue_script( 'form-validator' );
    global $wpdb;
    $output = '';

    $generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );
    extract( $generaloptions );

    $libraryoptions = wp_parse_args( $libraryoptions, ll_reset_options( 1, 'list', 'return' ) );
    extract( $libraryoptions );

    if ( $libraryoptions['showaddlinkrss'] === false ) {
        $libraryoptions['showaddlinkrss'] = 'hide';
    } elseif ( $libraryoptions['showaddlinkrss'] === true ) {
        $libraryoptions['showaddlinkrss'] = 'show';
    }

    if ( $libraryoptions['showaddlinkdesc'] === false ) {
        $libraryoptions['showaddlinkdesc'] = 'hide';
    } elseif ( $libraryoptions['showaddlinkdesc'] === true ) {
        $libraryoptions['showaddlinkdesc'] = 'show';
    }

    if ( $libraryoptions['showaddlinkcat'] === false ) {
        $libraryoptions['showaddlinkcat'] = 'hide';
    } elseif ( $libraryoptions['showaddlinkcat'] === true ) {
        $libraryoptions['showaddlinkcat'] = 'show';
    }

    if ( $libraryoptions['showaddlinknotes'] === false ) {
        $libraryoptions['showaddlinknotes'] = 'hide';
    } elseif ( $libraryoptions['showaddlinknotes'] === true ) {
        $libraryoptions['showaddlinknotes'] = 'show';
    }

    if ( $libraryoptions['addlinkcustomcat'] === false ) {
        $libraryoptions['addlinkcustomcat'] = 'hide';
    } elseif ( $libraryoptions['addlinkcustomcat'] === true ) {
        $libraryoptions['addlinkcustomcat'] = 'show';
    }

    if ( $libraryoptions['showaddlinkreciprocal'] === false ) {
        $libraryoptions['showaddlinkreciprocal'] = 'hide';
    } elseif ( $libraryoptions['showaddlinkreciprocal'] === true ) {
        $libraryoptions['showaddlinkreciprocal'] = 'show';
    }

    if ( $libraryoptions['showaddlinksecondurl'] === false ) {
        $libraryoptions['showaddlinksecondurl'] = 'hide';
    } elseif ( $libraryoptions['showaddlinksecondurl'] === true ) {
        $libraryoptions['showaddlinksecondurl'] = 'show';
    }

    if ( $libraryoptions['showaddlinktelephone'] === false ) {
        $libraryoptions['showaddlinktelephone'] = 'hide';
    } elseif ( $libraryoptions['showaddlinktelephone'] === true ) {
        $libraryoptions['showaddlinktelephone'] = 'show';
    }

    if ( $libraryoptions['showaddlinkemail'] === false ) {
        $libraryoptions['showaddlinkemail'] = 'hide';
    } elseif ( $libraryoptions['showaddlinkemail'] === true ) {
        $libraryoptions['showaddlinkemail'] = 'show';
    }

    if ( $libraryoptions['showlinksubmittername'] === false ) {
        $libraryoptions['showlinksubmittername'] = 'hide';
    } elseif ( $libraryoptions['showlinksubmittername'] === true ) {
        $libraryoptions['showlinksubmittername'] = 'show';
    }

    if ( $libraryoptions['showaddlinksubmitteremail'] === false ) {
        $libraryoptions['showaddlinksubmitteremail'] = 'hide';
    } elseif ( $libraryoptions['showaddlinksubmitteremail'] === true ) {
        $libraryoptions['showaddlinksubmitteremail'] = 'show';
    }

    if ( $libraryoptions['showlinksubmittercomment'] === false ) {
        $libraryoptions['showlinksubmittercomment'] = 'hide';
    } elseif ( $libraryoptions['showlinksubmittercomment'] === true ) {
        $libraryoptions['showlinksubmittercomment'] = 'show';
    }

    if ( $libraryoptions['showcustomcaptcha'] === false ) {
        $libraryoptions['showcustomcaptcha'] = 'hide';
    } elseif ( $libraryoptions['showcustomcaptcha'] === true ) {
        $libraryoptions['showcustomcaptcha'] = 'show';
    }

    if ( $libraryoptions['showuserlargedescription'] === false ) {
        $libraryoptions['showuserlargedescription'] = 'hide';
    } elseif ( $libraryoptions['showuserlargedescription'] === true ) {
        $libraryoptions['showuserlargedescription'] = 'show';
    }

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

    if ( 'link-library-addlink' == $code || 'addlink-link-library' == $code || 'link-library-addlinkcustommsg' == $code || 'addlinkcustommsg-link-library' == $code ) {
        if ( isset( $_GET['addlinkmessage'] ) ) {
            if ( 1 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('Confirm code not given', 'link-library') . '.</div>';
            } elseif ( 2 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('Captcha code is wrong', 'link-library') . '.</div>';
            } elseif ( 3 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('Captcha code is only valid for 5 minutes', 'link-library') . '.</div>';
            } elseif ( 4 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('No captcha cookie given. Make sure cookies are enabled', 'link-library') . '.</div>';
            } elseif ( 5 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('Captcha answer was not provided.', 'link-library') . '</div>';
            } elseif ( 6 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('Captcha answer is incorrect', 'link-library') . '.</div>';
            } elseif ( 7 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('User Category was not provided correctly. Link insertion failed.', 'link-library') . '</div>';
            } elseif ( 8 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $newlinkmsg;
                if ( !$showuserlinks ) {
                    $output .= ' ' . $moderatemsg;
                }
                $output .= '</div>';
            } elseif ( 9 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('Error: Link does not have an address.', 'link-library') . '</div>';
            } elseif ( 10 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('Error: Link already exists.', 'link-library') . '</div>';
            } elseif ( 11 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linkrsslabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 12 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linkdesclabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 13 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linknoteslabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 14 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linkreciprocallabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 15 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linksecondurllabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 16 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linktelephonelabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 17 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linkemaillabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 18 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linksubmitternamelabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 19 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linksubmitteremaillabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 20 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linksubmittercommentlabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 21 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . $libraryoptions['linklargedesclabel'] . __(' is a required field', 'link-library') . '</div>';
            } elseif ( 22 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('Link submission error', 'link-library') . '</div>';
            } elseif ( 23 == $_GET['addlinkmessage'] ) {
                $output .= '<div class="llmessage">' . __('Link rejected. There is already a site with this reciprocal link.', 'link-library') . '</div>';
            }
        }
    }

    if ( ( 'link-library-addlink' == $code || 'addlink-link-library' == $code ) && ( ( $addlinkreqlogin && current_user_can( 'read' ) ) || !$addlinkreqlogin ) ) {
        $output .= '<form method="post" id="lladdlink" action="">';

        $output .= wp_nonce_field( 'LL_ADDLINK_FORM', '_wpnonce', true, false );
        $output .= '<input type="hidden" name="thankyouurl" value="' . $linksubmissionthankyouurl . '" />';
        $output .= '<input type="hidden" name="link_library_user_link_submission" value="1" />';

        global $wp_query;
        $thePostID = $wp_query->post->ID;
        $output .= '<input type="hidden" name="pageid" value="' . $thePostID . '" />';
        $output .= '<input type="hidden" name="settingsid" value="' . $settings . '" />';

        $xpath = $LLPluginClass->relativePath( dirname( __FILE__ ), ABSPATH );
        $output .= '<input type="hidden" name="xpath" value="' . esc_attr( $xpath ) . '" />';
        unset( $xpath );

        $output .= "<div class='lladdlink'>\n";

        if ( empty( $addnewlinkmsg ) ) {
            $addnewlinkmsg = __( 'Add new link', 'link-library' );
        }

        $output .= '<div id="lladdlinktitle">' . $addnewlinkmsg . "</div>\n";

        $output .= "<table>\n";

        if ( empty( $linknamelabel ) ) {
            $linknamelabel = __( 'Link name', 'link-library' );
        }

        $output .= '<tr><th>' . $linknamelabel . '</th><td><input data-validation="required length" data-validation-length="max255" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" type="text" name="link_name" id="link_name" value="' . ( isset( $_GET['addlinkname'] ) ? esc_html( stripslashes( $_GET['addlinkname'] ), '1' ) : '') . "\" /></td></tr>\n";

        if ( empty( $linkaddrlabel ) ) {
            $linkaddrlabel = __( 'Link address', 'link-library' );
        }

        $output .= '<tr><th>' . $linkaddrlabel . '</th><td><input ';
        if ( !$addlinknoaddress ) {
            $output .= 'data-validation="required url length" data-validation-length="max255" data-validation-error-msg-required="' . __( 'Required field, URL', 'link-library' ) . '" ';
        }

        $output .= 'type="text" name="link_url" id="link_url" value="' . ( isset( $_GET['addlinkurl'] ) ? esc_html( stripslashes( $_GET['addlinkurl'] ), '1') : 'http://' ) . "\" /></td></tr>\n";

        if ( 'show' == $showaddlinkrss || 'required' == $showaddlinkrss) {
            if ( empty( $linkrsslabel ) ) {
                $linkrsslabel = __( 'Link RSS', 'link-library' );
            }

            $output .= '<tr><th>' . $linkrsslabel . '</th><td><input ';

            if ( 'required' == $showaddlinkrss ) {
                $requiredtext = ' required';
            } else {
                $requiredtext = '';
            }

            $output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max255" data-validation-error-msg-required="' . __( 'Required field, 1-255 chars', 'link-library' ) . '" ';

            $output .= ' type="text" name="link_rss" id="link_rss" value="' . ( isset( $_GET['addlinkrss'] ) ? esc_html( stripslashes( $_GET['addlinkrss'] ), '1') : '' ) . "\" /></td></tr>\n";
        }

        $linkcatquery = 'SELECT distinct t.name, t.term_id, t.slug as category_nicename, tt.description as category_description ';
        $linkcatquery .= 'FROM ' . $LLPluginClass->db_prefix() . 'terms t ';
        $linkcatquery .= 'LEFT JOIN ' . $LLPluginClass->db_prefix() . 'term_taxonomy tt ON (t.term_id = tt.term_id) ';
        $linkcatquery .= 'LEFT JOIN ' . $LLPluginClass->db_prefix() . 'term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ';

        $linkcatquery .= 'WHERE tt.taxonomy = "link_category" ';

        if ( !empty( $categorylist ) ) {
            $linkcatquery .= ' AND t.term_id in (' . $categorylist. ')';
        }

        if ( !empty( $excludecategorylist ) ) {
            $linkcatquery .= ' AND t.term_id not in (' . $excludecategorylist . ')';
        }

        $linkcatquery .= ' ORDER by t.name ASC';

        $linkcats = $wpdb->get_results( $linkcatquery );

        if ( $debugmode ) {
            $output .= "\n<!-- Category query for add link form:" . print_r($linkcatquery, TRUE) . "-->\n\n";
            $output .= "\n<!-- Results of Category query for add link form:" . print_r($linkcats, TRUE) . "-->\n";
        }

        if ( $linkcats ) {
            if ( 'show' == $showaddlinkcat || 'required' == $showaddlinkcat ) {
                if ( empty( $linkcatlabel ) ) {
                    $linkcatlabel = __( 'Link category', 'link-library' );
                }

                $output .= '<tr><th>' . $linkcatlabel . '</th><td>';
                $output .= '<SELECT data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" name="link_category" id="link_category">';

                if ( 'nodefaultcat' == $addlinkdefaultcat ) {
                    $output .= '<option value="">' . __( 'Select a category', 'link-category' ) . '</option>';
                }

                if ( empty( $linkcustomcatlistentry ) ) {
                    $linkcustomcatlistentry = __( 'User-submitted category (define below)', 'link-library' );
                }

                foreach ( $linkcats as $linkcat ) {
                    $output .= '<OPTION VALUE="' . $linkcat->term_id . '" ';
                    if ( isset( $_GET['addlinkcat'] ) && $_GET['addlinkcat'] == $linkcat->term_id ) {
                        $output .= "selected";
                    } elseif ( 'nodefaultcat' != $addlinkdefaultcat && $linkcat->term_id == intval( $addlinkdefaultcat ) ) {
                        $output .= "selected";
                    }
                    $output .= '>' . $linkcat->name;
                }

                if ( 'show' == $addlinkcustomcat ) {
                    $output .= '<OPTION VALUE="new">' . stripslashes( $linkcustomcatlistentry ) . "\n";
                }

                $output .= "</SELECT></td></tr>\n";
            } else {
                $output .= '<input type="hidden" name="link_category" id="link_category" value="';
                if ( 'nodefaultcat' == $addlinkdefaultcat ) {
                    $output .= $linkcats[0]->term_id;
                } else {
                    $output .= intval( $addlinkdefaultcat );
                }
                $output .= '">';
            }

            if ( 'show' == $addlinkcustomcat ) {
                $output .= '<tr class="customcatrow" style="display:none"><th>' .  $linkcustomcatlabel . '</th><td><input type="text" name="link_user_category" id="link_user_category" value="' . ( isset( $_GET['addlinkusercat'] ) ? esc_html( stripslashes( $_GET['addlinkusercat'] ), '1' ) : '') . "\" /></td></tr>\n";
            }
        }

        if ( 'show' == $showaddlinkdesc || 'required' == $showaddlinkdesc ) {
            if ( empty( $linkdesclabel ) ) {
                $linkdesclabel = __( 'Link description', 'link-library' );
            }

            $output .= '<tr><th>' . $linkdesclabel . '</th><td><input ';

            if ( 'required' == $showaddlinkrss ) {
                $requiredtext = ' required';
            } else {
                $requiredtext = '';
            }

            $output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max255" data-validation-error-msg-required="' . __( 'Required field, 1-255 chars', 'link-library' ) . '" ';

            $output .= ' type="text" name="link_description" id="link_description" value="' . ( isset( $_GET['addlinkdesc'] ) ? esc_html( stripslashes( $_GET['addlinkdesc'] ), '1' ) : '' ) . "\" /></td></tr>\n";
        }

        if ( 'show' == $showuserlargedescription || 'required' == $showuserlargedescription ) {
            if ( empty( $linklargedesclabel ) ) {
                $linklargedesclabel = __( 'Large description', 'link-library' );
            }

            $output .= '<tr><th style="vertical-align: top">' . $linklargedesclabel . '</th><td><textarea ';

            if ( 'required' == $showuserlargedescription ) {
                $output .= 'data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
            }

            $output .= ' name="link_textfield" id="link_textfield" cols="66">' . ( isset( $_GET['addlinktextfield'] ) ? esc_html( stripslashes( $_GET['addlinktextfield'] ), '1' ) : '' ) . "</textarea></td></tr>\n";
        }

        if ( 'show' == $showaddlinknotes || 'required' == $showaddlinknotes) {
            if ( empty( $linknoteslabel ) ) {
                $linknoteslabel = __( 'Link notes', 'link-library' );
            }

            $output .= '<tr><th>' . $linknoteslabel . '</th><td>';

            if ( !$usetextareaforusersubmitnotes || empty( $usetextareaforusersubmitnotes ) ) {
                $output .= '<input ';

                if ( 'required' == $showaddlinknotes ) {
                    $output .= 'data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
                }

                $output .= 'type="text" name="link_notes" id="link_notes" value="';
            } elseif ( $usetextareaforusersubmitnotes ) {
                $output .= '<textarea ';

                if ( 'required' == $showaddlinknotes ) {
                    $output .= 'data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
                }

                $output .= 'name="link_notes" id="link_notes">';
            }

            $output .= ( isset( $_GET['addlinknotes'] ) ? esc_html( stripslashes( $_GET['addlinknotes'] ), '1' ) : '' );

            if ( !$usetextareaforusersubmitnotes || empty( $usetextareaforusersubmitnotes ) ) {
                $output .= '" />';
            } elseif ( $usetextareaforusersubmitnotes ) {
                $output .= '</textarea>';
            }

            $output .= "</td></tr>\n";
        }

        if ( 'show' == $showaddlinkreciprocal || 'required' == $showaddlinkreciprocal) {
            if ( empty( $linkreciprocallabel ) ) {
                $linkreciprocallabel = __( 'Reciprocal Link', 'link-library' );
            }

            $output .= '<tr><th>' . $linkreciprocallabel . '</th><td><input ';

            if ( 'required' == $showaddlinkrss ) {
                $requiredtext = ' required';
            } else {
                $requiredtext = '';
            }

            $output .= 'data-validation="url length' . $requiredtext . '" data-validation-length="max255" data-validation-error-msg-required="' . __( 'Required field, 1-255 chars', 'link-library' ) . '" ';

            $output .= 'type="text" name="ll_reciprocal" id="ll_reciprocal" value="' . ( isset( $_GET['addlinkreciprocal'] ) ? esc_html(stripslashes($_GET['addlinkreciprocal']), '1') : 'http://' ) . "\" /></td></tr>\n";
        }

        if ( 'show' == $showaddlinksecondurl || 'required' == $showaddlinksecondurl) {
            if ( empty( $linksecondurllabel ) ) {
                $linksecondurllabel = __( 'Secondary Address', 'link-library' );
            }

            $output .= '<tr><th>' . $linksecondurllabel . '</th><td><input ';

            if ( 'required' == $showaddlinkrss ) {
                $requiredtext = ' required';
            } else {
                $requiredtext = '';
            }

            $output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max255" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';

            $output .= 'type="text" name="ll_secondwebaddr" id="ll_secondwebaddr" value="' . ( isset( $_GET['addlinksecondurl'] ) ? esc_html( stripslashes( $_GET['addlinksecondurl'] ), '1' ) : 'http://' ) . "\" /></td></tr>\n";
        }

        if ( 'show' == $showaddlinktelephone || 'required' == $showaddlinktelephone) {
            if ( empty( $linktelephonelabel ) ) {
                $linktelephonelabel = __( 'Telephone', 'link-library' );
            }

            $output .= '<tr><th>' . $linktelephonelabel . '</th><td><input ';

            if ( 'required' == $showaddlinkrss ) {
                $requiredtext = ' required';
            } else {
                $requiredtext = '';
            }

            $output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max128" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';

            $output .= 'type="text" name="ll_telephone" id="ll_telephone" value="' . ( isset( $_GET['addlinktelephone'] ) ? esc_html( stripslashes( $_GET['addlinktelephone'] ), '1' ) : '' ) . "\" /></td></tr>\n";
        }

        if ( 'show' == $showaddlinkemail || 'required' == $showaddlinkemail ) {
            if ( empty( $linkemaillabel ) ) {
                $linkemaillabel = __( 'E-mail', 'link-library' );
            }

            $output .= '<tr><th>' . $linkemaillabel . '</th><td><input ';

            if ( 'required' == $showaddlinkrss ) {
                $requiredtext = ' required';
            } else {
                $requiredtext = '';
            }

            $output .= 'data-validation="email length' . $requiredtext . '" data-validation-length="max128" data-validation-error-msg-required="' . __( 'Required field, proper e-mail, 1-128 chars', 'link-library' ) . '" ';

            $output .= 'type="text" name="ll_email" id="ll_email" value="' . ( isset( $_GET['addlinkemail'] ) ? esc_html( stripslashes( $_GET['addlinkemail'] ), '1' ) : '' ) . "\" /></td></tr>\n";
        }

        if ( 'show' == $showlinksubmittername || 'required' == $showlinksubmittername || is_user_logged_in() ) {
            if ( empty( $linksubmitternamelabel ) ) {
                $linksubmitternamelabel = __( 'Submitter Name', 'link-library' );
            }

	        $name_field_value = '';
	        if ( isset( $_GET['ll_submittername'] ) ) {
		        $name_field_value = esc_html( stripslashes( $_GET['ll_submittername'] ) );
	        } elseif ( is_user_logged_in() ) {
		        $user_data = wp_get_current_user();
		        $name_field_value = $user_data->display_name;
	        }

            $output .= '<tr';

	        if ( 'show' != $showlinksubmittername && 'required' != $showlinksubmittername ) {
		        $output .= ' style="display:none"';
	        }
	        $output .= '><th>' . $linksubmitternamelabel . '</th><td><input ';

            if ( 'required' == $showaddlinkrss ) {
                $requiredtext = ' required';
            } else {
                $requiredtext = '';
            }

            $output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max128" data-validation-error-msg-required="' . __( 'Required field, 1-128 chars', 'link-library' ) . '" ';

            $output .= 'type="text" name="ll_submittername" id="ll_submittername" value="' . $name_field_value . "\" /></td></tr>\n";
        }

        if ( 'show' == $showaddlinksubmitteremail || 'required' == $showaddlinksubmitteremail || is_user_logged_in()) {
            if ( empty( $linksubmitteremaillabel ) ) {
                $linksubmitteremaillabel = __( 'Submitter E-mail', 'link-library' );
            }

	        $email_field_value = '';
	        if ( isset( $_GET['ll_submitteremail'] ) ) {
		        $email_field_value = esc_html( stripslashes( $_GET['ll_submitteremail'] ) );
	        } elseif ( is_user_logged_in() ) {
		        $user_data = wp_get_current_user();
		        $email_field_value = $user_data->user_email;
	        }

	        $output .= '<tr';

	        if ( 'show' != $showaddlinksubmitteremail && 'required' != $showaddlinksubmitteremail ) {
		        $output .= ' style="display:none"';
	        }
	        $output .= '><th>' . $linksubmitteremaillabel . '</th><td><input ';

            if ( 'required' == $showaddlinkrss ) {
                $requiredtext = ' required';
            } else {
                $requiredtext = '';
            }

            $output .= 'data-validation="email length' . $requiredtext . '" data-validation-length="max128" data-validation-error-msg-required="' . __( 'Required field, 1-128 chars', 'link-library' ) . '" ';

            $output .= 'type="text" name="ll_submitteremail" id="ll_submitteremail" value="' . $email_field_value . "\" /></td></tr>\n";
        }

        if ( 'show' == $showlinksubmittercomment || 'required' == $showlinksubmittercomment) {
            if ( empty( $linksubmittercommentlabel ) ) {
                $linksubmittercommentlabel = __( 'Submitter Comment', 'link-library' );
            }

            $output .= '<tr><th style="vertical-align: top;">' . $linksubmittercommentlabel . '</th><td><textarea ';

            if ( 'required' == $showlinksubmittercomment ) {
                $output .= 'data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
            }

            $output .= 'name="ll_submittercomment" id="ll_submittercomment" cols="38">' . ( isset( $_GET['addlinksubmitcomment'] ) ? esc_html( stripslashes( $_GET['addlinksubmitcomment']), '1' ) : '' ) . "</textarea></td></tr>\n";
        }

        if ( $showcaptcha ) {
            $output .= apply_filters( 'link_library_generate_captcha', '' );
        }

        if ( 'show' == $showcustomcaptcha ) {
            if ( empty( $customcaptchaquestion ) ) {
                $customcaptchaquestion = __( 'Is boiling water hot or cold?', 'link-library' );
            }

            $output .= '<tr><th style="vertical-align: top;">' . $customcaptchaquestion . '</th><td><input data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . ' type="text" name="ll_customcaptchaanswer" id="ll_customcaptchaanswer" value="' . (isset( $_GET['ll_customcaptchaanswer'] ) ? esc_html( stripslashes( $_GET['ll_customcaptchaanswer'] ), '1' ) : '' ) . "\" /></td></tr>\n";
        }

        $output .= "</table>\n";

        if ( empty( $addlinkbtnlabel ) ) {
            $addlinkbtnlabel = __( 'Add link', 'link-library' );
        }

        $output .= '<span style="border:0;" class="LLUserLinkSubmit"><input type="submit" name="submit" value="' . $addlinkbtnlabel . '" /></span>';

        $output .= "</div>\n";
        $output .= "</form>\n\n";

        $output .= "<script type='text/javascript'>\n";
        $output .= "\tjQuery( document ).ready( function() {\n";

        $output .= "\tvar LinkLibraryValidationLanguage = {\n";
        $output .= "\t\terrorTitle: '" . addslashes( __( 'Form submission failed!', 'link-library' ) ) . "',\n";
        $output .= "\t\trequiredFields: '" . addslashes( __( 'You have not answered all required fields', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadTime: '" . addslashes( __( 'You have not given a correct time', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadEmail: '" . addslashes( __( 'You have not given a correct e-mail address', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadTelephone: '" . addslashes( __( 'You have not given a correct phone number', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadSecurityAnswer: '" . addslashes( __( 'You have not given a correct answer to the security question', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadDate: '" . addslashes( __( 'You have not given a correct date', 'link-library' ) ) . "',\n";
        $output .= "\t\tlengthBadStart: '" . addslashes( __( 'The input value must be between ', 'link-library' ) ) . "',\n";
        $output .= "\t\tlengthBadEnd: '" . addslashes( __( 'characters', 'link-library' ) ) . "',\n";
        $output .= "\t\tlengthTooLongStart: '" . addslashes( __( 'The input value is longer than ', 'link-library' ) ) . "',\n";
        $output .= "\t\tlengthTooShortStart: '" . addslashes( __( 'The input value is shorter than ', 'link-library' ) ). "',\n";
        $output .= "\t\tnotConfirmed: '" . addslashes( __( 'Input values could not be confirmed', 'link-library' ) ). "',\n";
        $output .= "\t\tbadDomain: '" . addslashes( __( 'Incorrect domain value', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadUrl: '" . addslashes( __( 'The input value is not a correct URL. Requires http://.', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadCustomVal: '" . addslashes( __( 'The input value is incorrect', 'link-library' ) ) . "',\n";
        $output .= "\t\tandSpaces: '" . addslashes( __( ' and spaces ', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadInt: '" . addslashes( __( 'The input value was not a correct number', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadSecurityNumber: '" . addslashes( __( 'Your social security number was incorrect', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadUKVatAnswer: '" . addslashes( __( 'Incorrect UK VAT Number', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadStrength: '" . addslashes( __( 'The password is not strong enough', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadNumberOfSelectedOptionsStart: '" . addslashes( __( 'You have to choose at least ', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadNumberOfSelectedOptionsEnd: '" . addslashes( __( ' answers', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadAlphaNumeric: '" . addslashes( __( 'The input value can only contain alphanumeric characters ', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadAlphaNumericExtra: '" . addslashes( __( ' and ', 'link-library' ) ) . "',\n";
        $output .= "\t\twrongFileSize: '" . addslashes( __( 'The file you are trying to upload is too large (max %s)', 'link-library' ) ) . "',\n";
        $output .= "\t\twrongFileType: '" . addslashes( __( 'Only files of type %s is allowed', 'link-library' ) ) . "',\n";
        $output .= "\t\tgroupCheckedRangeStart: '" . addslashes( __( 'Please choose between ', 'link-library' ) ) . "',\n";
        $output .= "\t\tgroupCheckedTooFewStart: '" . addslashes( __( 'Please choose at least ', 'link-library' ) ) . "',\n";
        $output .= "\t\tgroupCheckedTooManyStart: '" . addslashes( __( 'Please choose a maximum of ', 'link-library' ) ) . "',\n";
        $output .= "\t\tgroupCheckedEnd: '" . addslashes( __( ' item(s)', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadCreditCard: '" . addslashes( __( 'The credit card number is not correct', 'link-library' ) ) . "',\n";
        $output .= "\t\tbadCVV: '" . addslashes( __( 'The CVV number was not correct', 'link-library' ) ) . "',\n";
        $output .= "\t\twrongFileDim : '" . addslashes( __( 'Incorrect image dimensions,', 'link-library' ) ) . "',\n";
        $output .= "\t\timageTooTall : '" . addslashes( __( 'the image can not be taller than', 'link-library' ) ) . "',\n";
        $output .= "\t\timageTooWide : '" . addslashes( __( 'the image can not be wider than', 'link-library' ) ) . "',\n";
        $output .= "\t\timageTooSmall : '" . addslashes( __( 'the image was too small', 'link-library' ) ) . "',\n";
        $output .= "\t\tmin : '" . addslashes( __( 'min', 'link-library' ) ) . "',\n";
        $output .= "\t\tmax : '" . addslashes( __( 'max', 'link-library' ) ) . "',\n";
        $output .= "\t\timageRatioNotAccepted : '" . addslashes( __( 'Image ratio is not accepted', 'link-library' ) ) . "'\n";
        $output .= "\t};\n";

        $output .= "\t\tjQuery.validate({\n";
        $output .= "\t\t\tmodules : 'location, date, security, file',\n";
        $output .= "\t\t\tlanguage : LinkLibraryValidationLanguage,\n";
        $output .= "\t\t});\n";
        $output .= "\t\tjQuery('#link_category').change(function() {\n";
        $output .= "\t\t\tif ( jQuery('#link_category').val() == 'new' ) {\n";
        $output .= "\t\t\t\tjQuery('.customcatrow').show();\n";
        $output .= "\t\t\t} else {\n";
        $output .= "\t\t\t\tjQuery('.customcatrow').hide();\n";
        $output .= "\t\t\t};\n";
        $output .= "\t\t});\n";
        $output .= "\t});\n";
        $output .= "</script>\n";
    }

    return $output;
}

function link_library_generate_captcha() {
    $captcha = '<tr><td></td><td><span id="captchaimage"><img src="' . plugins_url( 'captcha/easycaptcha.php', __FILE__ ) . "\" /></span></td></tr>\n";
	$captcha .= '<tr><th>' . __('Enter code from above image', 'link-library') . "</th><td><input type='text' name='confirm_code' /></td></tr>\n";
    return $captcha;
}

add_filter( 'link_library_generate_captcha', 'link_library_generate_captcha' );
