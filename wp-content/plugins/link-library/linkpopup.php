<?php

function link_library_popup_content( $my_link_library_plugin ) {
    if ( isset( $_GET['linkid'] ) && isset( $_GET['settings']) ) {
        $link_id = intval( $_GET['linkid'] );
        $settings_id = intval( $_GET['settings'] );
    } else {
        wp_die();
    }

    $linkquery = "SELECT distinct *, l.link_id as proper_link_id, UNIX_TIMESTAMP(l.link_updated) as link_date, ";
    $linkquery .= "IF (DATE_ADD(l.link_updated, INTERVAL 120 MINUTE) >= NOW(), 1,0) as recently_updated ";
    $linkquery .= "FROM " . $my_link_library_plugin->db_prefix() . "terms t ";
    $linkquery .= "LEFT JOIN " . $my_link_library_plugin->db_prefix() . "term_taxonomy tt ON (t.term_id = tt.term_id) ";
    $linkquery .= "LEFT JOIN " . $my_link_library_plugin->db_prefix() . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";
    $linkquery .= "LEFT JOIN " . $my_link_library_plugin->db_prefix() . "links l ON (tr.object_id = l.link_id) ";
    $linkquery .= "LEFT JOIN " . $my_link_library_plugin->db_prefix() . "links_extrainfo le ON (l.link_id = le.link_id) ";
    $linkquery .= "WHERE tt.taxonomy = 'link_category' ";

    $linkquery .= "AND l.link_id = " . $link_id;

    global $wpdb;

    $linkitem = $wpdb->get_row($linkquery, ARRAY_A);

    $the_link = '#';
    if ( !empty( $linkitem['link_url'] ) )
        $the_link = esc_html($linkitem['link_url']);

    $the_second_link = '#';
    if (!empty($linkitem['link_second_url']) )
        $the_second_link = esc_html($linkitem['link_second_url']);

    $cleanname = esc_html($linkitem['link_name'], ENT_QUOTES);

    $name = $cleanname;

    $alt = ' alt="' . $cleanname . '"';

    $title = esc_html($linkitem['link_description'], ENT_QUOTES);

    if ('' != $title)
        $title = ' title="' . $title . '"';

    $options = get_option( 'LinkLibraryPP' . $settings_id );

    $rel = $linkitem['link_rel'];
    if ('' != $rel and !$options['nofollow'] and !$linkitem['link_no_follow'])
        $rel = ' rel="' . $rel . '"';
    else if ('' != $rel and ($options['nofollow'] or $linkitem['link_no_follow']))
        $rel = ' rel="' . $rel . ' nofollow"';
    else if ('' == $rel and ($options['nofollow'] or $linkitem['link_no_follow']))
        $rel = ' rel="nofollow"';

    $target = $linkitem['link_target'];
    if ( !empty( $target ) ) {
        $target = ' target="' . $target . '"';
    } else {
        $target = $options['linktarget'];
        if ( !empty( $target ) )
            $target = ' target="' . $target . '"';
    }

    $popup_text = ( !empty( $options['link_popup_text'] ) ? stripslashes($options['link_popup_text']) : __( '%link_image%<br />Click through to visit %link_name%.', 'link-library') );

    if ( ( strpos( $popup_text, '%link_image%' ) !== false ) && !empty( $linkitem['link_image'] ) ) {
        $imageoutput = '<a href="';

        if ($options['sourceimage'] == 'primary' || $options['sourceimage'] == '')
            $imageoutput .= $the_link;
        elseif ($options['sourceimage'] == 'secondary')
            $imageoutput .= $the_second_link;

        $imageoutput .= '" id="link-' . $linkitem['proper_link_id'] . '" class="track_this_link ' . ( $linkitem['link_featured'] ? 'featured' : '' ). '" ' . $rel . $title . $target. '>';

        if ($options['usethumbshotsforimages'])
        {
            if ($options['thumbshotscid'] == '')
                $imageoutput .= '<img src="http://open.thumbshots.org/image.aspx?url=' . $the_link . '"';
            elseif ($options['thumbshotscid'] != '')
                $imageoutput .= '<img src="http://images.thumbshots.com/image.aspx?cid=' . $options['thumbshotscid'] .
                    '&v=1&w=120&h=90&url=' . $the_link . '"';
        }
        elseif ( strpos($linkitem['link_image'], 'http') !== false )
            $imageoutput .= '<img src="' . $linkitem['link_image'] . '"';
        else // If it's a relative path
            $imageoutput .= '<img src="' . get_option('siteurl') . $linkitem['link_image'] . '"';

        $imageoutput .= $alt . $title;

        if ($options['imageclass'] != '')
            $imageoutput .= ' class="' . $options['imageclass'] . '" ';

        $imageoutput .= "/>";

        $imageoutput .= '</a>';

        $popup_text = str_replace( '%link_image%', $imageoutput, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_image%' ) !== false )  && empty( $linkitem['link_image'] ) ) {
        $popup_text = str_replace( '%link_image%', '', $popup_text );
    }


    if ( ( strpos( $popup_text, '%link_name%' ) !== false ) && !empty( $name ) ) {
        if ( ( $options['sourcename'] == 'primary' && $the_link != '#') || ($options['sourcename'] == 'secondary' && $the_second_link != '#')) {
            $nameoutput = '<a href="';

            if ( isset( $options['sourcename'] ) && ( $options['sourcename'] == 'primary' || $options['sourcename'] == '' ) ) {
                $nameoutput .= $the_link;
            }
            elseif ( isset( $options['sourcename'] ) && $options['sourcename'] == 'secondary' )
                $nameoutput .= $the_second_link;

            $nameoutput .= '" id="link-' . $linkitem['proper_link_id'] . '" class="' . ( ( isset( $enablelinkpopup ) && $enablelinkpopup ) ? 'thickbox' : 'track_this_link' ) . ( $linkitem['link_featured'] ? ' featured' : '' ). '" ' . $rel . $title . $target. '>';
        }

        $nameoutput .= $name;

        if (($options['sourcename'] == 'primary' && $the_link != '#') || ($options['sourcename'] == 'secondary' && $the_second_link != '#'))
            $nameoutput .= '</a>';

        $popup_text = str_replace( '%link_name%', $nameoutput, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_name%' ) !== false ) && empty( $name ) ) {
        $popup_text = str_replace( '%link_name%', '', $popup_text );
    }

    if ( ( strpos( $popup_text, '%link_cat_name%' ) !== false ) && !empty( $linkitem['name'] ) ) {            $popup_text = str_replace( '%link_cat_name%', $linkitem['name'], $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_cat_name%' ) !== false ) && empty( $linkitem['name'] ) ) {
        $popup_text = str_replace( '%link_cat_name%', '', $popup_text );
    }

    if ( ( strpos( $popup_text, '%link_cat_desc%' ) !== false ) && !empty( $linkitem['description'] ) ) {
        $cleandesc = str_replace('[', '<', $linkitem['description']);
        $cleandesc = str_replace(']', '>', $cleandesc);

        $popup_text = str_replace( '%link_cat_desc%', $cleandesc, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_cat_desc%' ) !== false ) && empty( $linkitem['description'] ) ) {
        $popup_text = str_replace( '%link_cat_desc%', '', $popup_text );
    }

    if ( ( strpos ( $popup_text, '%link_desc%' ) !== false ) && !empty( $linkitem['link_description'] ) ) {
        $linkdesc = $linkitem['link_description'];
        $linkdesc = str_replace('[', '<', $linkdesc);
        $linkdesc = str_replace(']', '>', $linkdesc);

        $popup_text = str_replace( '%link_desc%', $linkdesc, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_desc%' ) !== false ) && empty( $linkitem['link_description'] ) ) {
        $popup_text = str_replace( '%link_desc%', '', $popup_text );
    }

    if ( ( strpos ( $popup_text, '%link_large_desc%' ) !== false ) && !empty( $linkitem['link_textfield'] ) ) {
        $linklargedesc = stripslashes( $linkitem['link_textfield'] );
        $linklargedesc = str_replace('[', '<', $linklargedesc);
        $linklargedesc = str_replace(']', '>', $linklargedesc);

        $popup_text = str_replace( '%link_large_desc%', $linklargedesc, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_large_desc%' ) !== false ) && empty( $linkitem['link_textfield'] ) ) {
        $popup_text = str_replace( '%link_large_desc%', '', $popup_text );
    }

    if ( ( strpos ( $popup_text, '%link_telephone%' ) !== false ) && !empty( $linkitem['link_telephone'] ) ) {
        $linktelephone = stripslashes( $linkitem['link_telephone'] );

        $popup_text = str_replace( '%link_telephone%', $linktelephone, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_telephone%' ) !== false ) && empty( $linkitem['link_telephone'] ) ) {
        $popup_text = str_replace( '%link_telephone%', '', $popup_text );
    }

    if ( ( strpos ( $popup_text, '%link_email%' ) !== false ) && !empty( $linkitem['link_email'] ) ) {
        $linkemail = stripslashes( $linkitem['link_email'] );

        $popup_text = str_replace( '%link_email%', $linkemail, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_email%' ) !== false ) && empty( $linkitem['link_email'] ) ) {
        $popup_text = str_replace( '%link_email%', '', $popup_text );
    }

    if ( ( strpos ( $popup_text, '%link_alt_web%' ) !== false ) && !empty( $linkitem['link_second_url'] ) ) {
        $linkalturl = stripslashes( esc_html( $linkitem['link_second_url'] ) );

        $popup_text = str_replace( '%link_alt_web%', $linkalturl, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_alt_web%' ) !== false ) && empty( $linkitem['link_second_url'] ) ) {
        $popup_text = str_replace( '%link_alt_web%', '', $popup_text );
    }

    if ( ( strpos ( $popup_text, '%link_num_views%' ) !== false ) && !empty( $linkitem['link_visits'] ) ) {
        $linkvisits = stripslashes( $linkitem['link_visits'] );

        $popup_text = str_replace( '%link_num_views%', $linkvisits, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_num_views%' ) !== false ) && empty( $linkitem['link_visits'] ) ) {
        $popup_text = str_replace( '%link_num_views%', '', $popup_text );
    }

    if ( ( strpos ( $popup_text, '%link_submitter_name%' ) !== false ) && !empty( $linkitem['link_submitter_name'] ) ) {
        $linksubmitter = stripslashes( $linkitem['link_submitter_name'] );

        $popup_text = str_replace( '%link_submitter_name%', $linksubmitter, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_submitter_name%' ) !== false ) && empty( $linkitem['link_submitter_name'] ) ) {
        $popup_text = str_replace( '%link_submitter_name%', '', $popup_text );
    }

    if ( ( strpos ( $popup_text, '%link_rating%' ) !== false ) && !empty( $linkitem['link_rating'] ) ) {
        $linksubmitter = stripslashes( $linkitem['link_rating'] );

        $popup_text = str_replace( '%link_rating%', $linksubmitter, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_rating%' ) !== false ) && empty( $linkitem['link_rating'] ) ) {
        $popup_text = str_replace( '%link_rating%', '', $popup_text );
    }

    if ( ( strpos ( $popup_text, '%link_rss%' ) !== false ) && !empty( $linkitem['link_rss'] ) ) {
        $linksubmitter = stripslashes( $linkitem['link_rss'] );

        $popup_text = str_replace( '%link_rss%', $linksubmitter, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_rss%' ) !== false ) && empty( $linkitem['link_rss'] ) ) {
        $popup_text = str_replace( '%link_rss%', '', $popup_text );
    }

    echo '<div class="linkpopup">' . $popup_text . '</div>';

    $xpath = $my_link_library_plugin->relativePath( dirname( __FILE__ ), ABSPATH );

    $track_code = "<script type='text/javascript'>\n";
    $track_code .= "jQuery(document).ready(function()\n";
    $track_code .= "{\n";
    $track_code .= "jQuery('a.track_this_link').click(function() {\n";
    $track_code .= "linkid = this.id;\n";
    $track_code .= "linkid = linkid.substring(5);";
    $track_code .= "path = '" . $xpath . "';";
    $track_code .= "jQuery.post('" . WP_PLUGIN_URL . "/link-library/tracker.php', {id:linkid, xpath:path});\n";
    $track_code .= "return true;\n";
    $track_code .= "});\n";
    $track_code .= "});\n";
    $track_code .= "</script>";

    echo $track_code;
    exit;
}

