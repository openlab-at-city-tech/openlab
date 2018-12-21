<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $my_link_library_plugin;

function link_library_generate_rss_feed () {

    require_once plugin_dir_path( __FILE__ ) . 'rss.genesis.php';

    if ( isset( $_GET['settingsset'] ) && !empty( $_GET['settingsset'] ) ) {
        $settingsetid = intval( $_GET['settingsset'] );
    } else {
        $settingsetid = 1;
    }

    $settingsname = 'LinkLibraryPP' . $settingsetid;
    $options = get_option($settingsname);

    $rss = new rssGenesis();

    $feedtitle = ($options['rssfeedtitle'] == "" ? "Link Library Generated Feed" : $options['rssfeedtitle']);
    $feeddescription = ($options['rssfeeddescription'] == "" ? "Link Library Generated Feed Description" : $options['rssfeeddescription']);

    // CHANNEL
    $rss->setChannel (
        $feedtitle, // Title
        home_url () . '/feed/linklibraryfeed?settingsset=' . $settingsetid, // Link
        $feeddescription, // Description
        null, // Language
        null, // Copyright
        null, // Managing Editor
        null, // WebMaster
        null, // Rating
        "auto", // PubDate
        "auto", // Last Build Date
        "Link Library Links", // Category
        null, // Docs
        null, // Time to Live
        null, // Skip Days
        null // Skip Hours
    );

    $link_query_args = array( 'post_type' => 'link_library_links', 'posts_per_page' => $options['numberofrssitems'], 'post_status' => 'publish',
                              'orderby' => 'meta_value_num', 'meta_key' => 'link_updated', 'order' => 'DESC' );

    if ( $options['showinvisible'] == true ) {
        $link_query_args['post_status'] = array( 'publish', 'private' );
    }

    if ( !empty( $options['categorylist_cpt'] ) ) {
	    $link_query_args['tax_query'] = array(
		    array(
			    'taxonomy' => 'link_library_category',
			    'field'    => 'term_id',
			    'terms'    => explode( ',', $options['categorylist_cpt'] ),
			    'operator'    => 'IN',
		    ),
	    );
    }

    if ( !empty( $options['excludecategorylist_cpt'] ) ) {
	    if ( !empty( $options['categorylist_cpt'] ) ) {
		    $link_query_args['tax_query']['relation'] = 'AND';
	    }

	    $link_query_args['tax_query'][] = array(
		    'taxonomy' => 'link_library_category',
		    'field'    => 'term_id',
		    'terms'    => explode( ',', $options['excludecategorylist_cpt'] ),
		    'operator'    => 'NOT IN',
	    );
    }

    $the_link_query = new WP_Query( $link_query_args );

    if ( $the_link_query->have_posts() ) {
        while ( $the_link_query->have_posts() ) {
            $the_link_query->the_post();

            $link_url = get_post_meta( get_the_ID(), 'link_url', true );
            $link_description = get_post_meta( get_the_ID(), 'link_description', true );
            $link_updated = get_post_meta( get_the_ID(), 'link_updated', true );
            $human_date = date( "Y-m-d H:i", $link_updated );

            $link_categories = wp_get_post_terms( get_the_ID(), 'link_library_category' );

            $cat_names = '';
            if ( $link_categories ) {
                $countcats = 0;
                foreach ( $link_categories as $link_category ) {
                    if ( $countcats >= 1 ) {
                        $cat_names .= ', ';
                    }
                    $cat_names .= $link_category->name;
                    $countcats++;
                }
            }

            if ( !empty( $link_url ) ) {
                // ITEM
                $rss->addItem (
                    get_the_title(), // Title
                    $link_url, // Link
                    $link_description, // Description
                    $human_date, //Publication Date
                    $cat_names // Category
                );
            }
        }
    }

    wp_reset_postdata();

	header( 'Content-Type: '. feed_content_type('rss') . '; charset=' . get_option('blog_charset') );
    print( $rss->getFeed() );
    exit;
}