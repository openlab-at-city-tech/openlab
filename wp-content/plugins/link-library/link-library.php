<?php
/*
Plugin Name: Link Library
Plugin URI: http://wordpress.org/extend/plugins/link-library/
Description: Display links on pages with a variety of options
Version: 5.9.13.12
Author: Yannick Lefebvre
Author URI: http://ylefebvre.ca/
Text Domain: link-library

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright 2016 Yannick Lefebvre

Translations:
French Translation courtesy of Luc Capronnier
Danish Translation courtesy of GeorgWP (http://wordpress.blogos.dk)
Italian Translation courtesy of Gianni Diurno
Serbian Translation courtesy of Ogi Djuraskovic (firstsiteguide.com)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNUs General Public License
as published addlinkcatlistoverrideby the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

You can also view a copy of the HTML version of the GNU General Public
License at http://www.gnu.org/copyleft/gpl.html

I, Yannick Lefebvre, can be contacted via e-mail at ylefebvre@gmail.com
*/

require_once(ABSPATH . '/wp-admin/includes/bookmark.php');
require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';
require_once plugin_dir_path( __FILE__ ) . 'rssfeed.php';

global $my_link_library_plugin;
global $my_link_library_plugin_admin;

if ( !get_option( 'link_manager_enabled' ) ) {
    add_filter( 'pre_option_link_manager_enabled', '__return_true' );
}

function link_library_tweak_plugins_http_filter( $response, $r, $url ) {
	if ( stristr( $url, 'api.wordpress.org/plugins/update-check/1.1' ) ) {
		$wpapi_response = json_decode( $response['body'] );
		$wpapi_response->plugins = link_library_modify_http_response( $wpapi_response->plugins );
		$response['body'] = json_encode( $wpapi_response );
	}

	return $response;
}

function link_library_strposX( $haystack, $needle, $number ) {
	if( $number == '1' ){
		return strpos($haystack, $needle);
	} elseif( $number > '1' ){
		return strpos( $haystack, $needle, link_library_strposX( $haystack, $needle, $number - 1 ) + strlen( $needle ) );
	} else {
		return error_log( 'Error: Value for parameter $number is out of range' );
	}
}

function link_library_modify_http_response( $plugins_response ) {

	foreach ( $plugins_response as $response_key => $plugin_response ) {
		if ( plugin_basename(__FILE__) == $plugin_response->plugin ) {
			if ( 3 <= substr_count( $plugin_response->new_version, '.' ) ) {
				$plugin_info = get_plugin_data( __FILE__ );
				$period_position = link_library_strposX( $plugin_info['Version'], '.', 3 );
				if ( false !== $period_position ) {
					$current_version = substr( $plugin_info['Version'], 0, $period_position );
				} else {
					$current_version = $plugin_info['Version'];
				}

				$period_position2 = link_library_strposX( $plugin_response->new_version, '.', 3 );
				if ( false !== $period_position ) {
					$new_version = substr( $plugin_response->new_version, 0, $period_position2 );
				} else {
					$new_version = $plugin_response->new_version;
				}

				$version_diff = version_compare( $current_version, $new_version );

				if ( -1 < $version_diff ) {
					unset( $plugins_response->$response_key );
				}
			}
		}
	}

	return $plugins_response;
}

/*********************************** Link Library Class *****************************************************************************/
class link_library_plugin {

	//constructor of class, PHP4 compatible construction for backward compatibility
	function __construct() {

        // Functions to be called when plugin is activated and deactivated
        register_activation_hook( __FILE__, array( $this, 'll_install' ) );
        register_deactivation_hook( __FILE__, array( $this, 'll_uninstall' ) );
	
		$newoptions = get_option( 'LinkLibraryPP1', '' );
		$genoptions = get_option( 'LinkLibraryGeneral', '' );

		if ( empty( $newoptions ) ) {
            global $my_link_library_plugin_admin;

            if ( empty( $my_link_library_plugin_admin ) ) {
                require plugin_dir_path( __FILE__ ) . 'link-library-admin.php';
                $my_link_library_plugin_admin = new link_library_plugin_admin();
            }

			ll_reset_options( 1, 'list', 'return_and_set' );

			if ( empty( $genoptions ) ) {
				ll_reset_gen_settings( 'return_and_set' );
			}
		}
        
		// Add short codes
        add_shortcode( 'link-library', array( $this, 'link_library_func' ) );
		add_shortcode( 'link-library-cats', array( $this, 'link_library_cats_func' ) );
		add_shortcode( 'cats-link-library', array( $this, 'link_library_cats_func' ) );
		add_shortcode( 'link-library-search', array( $this, 'link_library_search_func' ) );
		add_shortcode( 'search-link-library', array( $this, 'link_library_search_func' ) );
		add_shortcode( 'link-library-addlink', array( $this, 'link_library_addlink_func' ) );
		add_shortcode( 'addlink-link-library', array( $this, 'link_library_addlink_func' ) );
		add_shortcode( 'link-library-addlinkcustommsg', array( $this, 'link_library_addlink_func' ) );
		add_shortcode( 'addlinkcustommsg-link-library', array( $this, 'link_library_addlink_func' ) );
		add_shortcode( 'link-library-count', array( $this, 'link_library_count_func' ) );

        // Function to determine if Link Library is used on a page before printing headers
        // the_posts gets triggered before wp_head
        add_filter( 'the_posts', array( $this, 'conditionally_add_scripts_and_styles' ) );

		// Function to print information in page header when plugin present
		add_action( 'wp_head', array( $this, 'll_rss_link' ) );

		add_filter( 'wp_title', array( $this, 'll_title_creator' ) );

		add_action( 'init', array( $this, 'links_rss' ) );

		// Re-write rules filters to allow for custom permalinks
		add_filter( 'rewrite_rules_array', array( $this, 'll_insertMyRewriteRules' ) );
		add_filter( 'query_vars', array( $this, 'll_insertMyRewriteQueryVars' ) );

        add_action( 'template_redirect', array( $this, 'll_template_redirect' ) );
        add_action( 'wp_ajax_link_library_tracker', array( $this, 'link_library_ajax_tracker' ) );
        add_action( 'wp_ajax_nopriv_link_library_tracker', array( $this, 'link_library_ajax_tracker' ) );
        add_action( 'wp_ajax_link_library_ajax_update', array( $this, 'link_library_func') );
        add_action( 'wp_ajax_nopriv_link_library_ajax_update', array( $this, 'link_library_func') );
        add_action( 'wp_ajax_link_library_generate_image', array( $this, 'link_library_generate_image') );
        add_action( 'wp_ajax_nopriv_link_library_generate_image', array( $this, 'link_library_generate_image') );
		add_action( 'wp_ajax_link_library_popup_content', array( $this, 'll_popup_content') );
		add_action( 'wp_ajax_nopriv_link_library_popup_content', array( $this, 'll_popup_content') );

        add_action( 'wp_enqueue_scripts', array( $this, 'll_register_script' ) );

		// Load text domain for translation of admin pages and text strings
		load_plugin_textdomain( 'link-library', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		add_filter( 'kses_allowed_protocols', array( $this, 'll_add_protocols' ) );

		add_filter( 'wp_feed_cache_transient_lifetime' , array( $this, 'feed_cache_filter_handler' ) );

        global $wpdb;

        $wpdb->linkcategorymeta = $wpdb->get_blog_prefix() . 'linkcategorymeta';
	}

	function feed_cache_filter_handler( $seconds ) {
		$genoptions = get_option( 'LinkLibraryGeneral' );
		$genoptions = wp_parse_args( $genoptions, ll_reset_gen_settings( 'return' ) );

		return $genoptions['rsscachedelay'];
	}

	function links_rss() {
		add_feed( 'linklibraryfeed', 'link_library_generate_rss_feed' );
	}

    /************************** Link Library Installation Function **************************/
    function ll_install() {
        global $wpdb;

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
            if ( isset( $_GET['networkwide'] ) && ( $_GET['networkwide'] == 1 ) ) {
                $originalblog = $wpdb->blogid;

                $bloglist = $wpdb->get_col( 'SELECT blog_id FROM ' . $wpdb->blogs );
                foreach ( $bloglist as $blog ) {
                    switch_to_blog( $blog );
                    $this->create_table_and_settings();
                }
                switch_to_blog( $originalblog );
                return;
            }
        }
        $this->create_table_and_settings();
    }

    function new_network_site( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        global $wpdb;

        if ( ! function_exists( 'is_plugin_active_for_network' ) )
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        if ( is_plugin_active_for_network( 'link-library/link-library.php' ) ) {
            $originalblog = $wpdb->blogid;
            switch_to_blog( $blog_id );
            $this->create_table_and_settings();
            switch_to_blog( $originalblog );
        }
    }

    function create_table_and_settings() {
        global $wpdb;

        $wpdb->links_extrainfo = $this->db_prefix() . 'links_extrainfo';

        $creationquery = "CREATE TABLE " . $wpdb->links_extrainfo . " (
				link_id bigint(20) NOT NULL DEFAULT '0',
				link_second_url varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				link_telephone varchar(128) CHARACTER SET utf8 DEFAULT NULL,
				link_email varchar(128) CHARACTER SET utf8 DEFAULT NULL,
				link_visits bigint(20) DEFAULT '0',
				link_reciprocal varchar(255) DEFAULT NULL,
				link_submitter varchar(255) DEFAULT NULL,
				link_submitter_name VARCHAR(128) CHARACTER SET utf8 NULL,
				link_submitter_email VARCHAR(128) NULL,
				link_textfield TEXT CHARACTER SET utf8 NULL,
				link_no_follow VARCHAR(1) NULL,
				link_featured VARCHAR(1) NULL,
				link_manual_updated VARCHAR(1) NULL,
				UNIQUE KEY (link_id)
				)";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $creationquery );

        $wpdb->linkcategorymeta = $this->db_prefix() . 'linkcategorymeta';

        $meta_creation_query =
            'CREATE TABLE ' . $wpdb->linkcategorymeta . ' (
        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        linkcategory_id bigint(20) unsigned NOT NULL DEFAULT "0",
        meta_key varchar(255) DEFAULT NULL,
        meta_value longtext,
        UNIQUE KEY (meta_id)
        );';

        dbDelta ( $meta_creation_query );

        $genoptions = get_option( 'LinkLibraryGeneral' );

        if ( !empty( $genoptions ) ) {

            if ( empty( $genoptions['schemaversion'] ) || floatval( $genoptions['schemaversion'] ) < 3.5 ) {
                $genoptions['schemaversion'] = '3.5';
                update_option( 'LinkLibraryGeneral', $genoptions );
            } elseif ( floatval( $genoptions['schemaversion'] ) < '4.6' ) {
                $genoptions['schemaversion'] = '4.6';
                $wpdb->get_results( 'ALTER TABLE ' . $this->db_prefix() . 'links_extrainfo ADD link_submitter_name VARCHAR( 128 ) NULL, ADD link_submitter_email VARCHAR( 128 ) NULL , ADD link_textfield TEXT NULL ;' );

                update_option( 'LinkLibraryGeneral', $genoptions );
            } elseif ( floatval( $genoptions['schemaversion'] ) < '4.7' ) {
                $genoptions['schemaversion'] = '4.7';
                $wpdb->get_results( 'ALTER TABLE ' . $this->db_prefix() . 'links_extrainfo ADD link_no_follow VARCHAR( 1 ) NULL;' );

                update_option( 'LinkLibraryGeneral', $genoptions );
            } elseif ( floatval( $genoptions['schemaversion'] ) < '4.9' ) {
                $genoptions['schemaversion'] = '4.9';
                $wpdb->get_results( 'ALTER TABLE ' . $this->db_prefix() . 'links_extrainfo ADD link_featured VARCHAR( 1 ) NULL;' );

                update_option( 'LinkLibraryGeneral', $genoptions );
            }

            for ( $i = 1; $i <= $genoptions['numberstylesets']; $i++ ) {
                $settingsname = 'LinkLibraryPP' . $i;
                $options = get_option( $settingsname );

                if ( !empty( $options ) ) {
                    if ( empty( $options['showname'] ) ) {
                        $options['showname'] = true;
                    }

                    if ( isset( $options['show_image_and_name'] ) && $options['show_image_and_name'] == true ) {
                        $options['showname'] = true;
                        $options['show_images'] = true;
                    }

                    if ( empty( $options['sourcename'] ) ) {
                        $options['sourcename'] = 'primary';
                    }

                    if ( empty( $options['sourceimage'] ) ) {
                        $options['sourceimage'] = 'primary';
                    }

                    if ( empty( $options['dragndroporder'] ) ) {
                        if ( $options['imagepos'] == 'beforename' ) {
                            $options['dragndroporder'] = '1,2,3,4,5,6,7,8,9,10,11,12';
                        } elseif ( $options['imagepos'] == 'aftername' ) {
                            $options['dragndroporder'] = '2,1,3,4,5,6,7,8,9,10,11,12';
                        } elseif ( $options['imagepos'] == 'afterrssicons' ) {
                            $options['dragndroporder'] = '2,3,4,5,6,1,7,8,9,10,11,12';
                        }
                    } else if ( !empty( $options['dragndroporder'] ) ) {
                        $elementarray = explode( ',', $options['dragndroporder'] );

                        $allelements = array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12' );
                        foreach ( $allelements as $element ) {
                            if ( !in_array( $element, $elementarray ) ) {
                                $elementarray[] = $element;
                                $options['dragndroporder'] = implode( ',', $elementarray );
                            }
                        }
                    }

                    if ( $options['flatlist'] === true ) {
                        $options['flatlist'] = 'unordered';
                    } elseif ( $options['flatlist'] === false ) {
                        $options['flatlist'] = 'table';
                    }
                }

                update_option( $settingsname, $options );
            }
        }

		$genoptions['schemaversion'] = '5.0';
		update_option( 'LinkLibraryGeneral', $genoptions );
    }

    function remove_querystring_var( $url, $key ) {

        $keypos = strpos( $url, $key );
        if ( $keypos ) {
            $ampersandpos = strpos( $url, '&', $keypos );
            $newurl = substr( $url, 0, $keypos - 1 );

            if ( $ampersandpos ) {
                $newurl .= substr($url, $ampersandpos);
            }
        } else {
            $newurl = $url;
        }

        return $newurl;
    }

    /************************** Link Library Uninstall Function **************************/
    function ll_uninstall() {
        $genoptions = get_option( 'LinkLibraryGeneral' );

        if ( !empty( $genoptions ) ) {
            if ( isset( $genoptions['stylesheet'] ) && isset( $genoptions['fullstylesheet'] ) && !empty( $genoptions['stylesheet'] ) && empty( $genoptions['fullstylesheet'] ) ) {
                $stylesheetlocation = plugins_url( $genoptions['stylesheet'], __FILE__ );
                if ( file_exists( $stylesheetlocation ) )
                    $genoptions['fullstylesheet'] = file_get_contents( $stylesheetlocation );

                update_option( 'LinkLibraryGeneral', $genoptions );
            }
        }
    }

    function ll_register_script() {
        wp_register_script( 'form-validator', plugins_url( '/form-validator/jquery.form-validator.min.js' , __FILE__ ), array( 'jquery' ), '1.0.0', true );
    }
    
    function db_prefix() {
		global $wpdb;
		if ( method_exists( $wpdb, 'get_blog_prefix' ) ) {
            return $wpdb->get_blog_prefix();
        } else {
            return $wpdb->prefix;
        }
	}

	function ll_add_protocols( $protocols ) {
		$genoptions = get_option( 'LinkLibraryGeneral' );

		if ( isset( $genoptions['extraprotocols'] ) && !empty( $genoptions['extraprotocols'] ) ) {
			$extra_protocol_array = explode( ',', $genoptions['extraprotocols'] );

			if ( !empty( $extra_protocol_array ) ) {
				foreach( $extra_protocol_array as $extra_protocol ) {
					$protocols[] = $extra_protocol;
				}
			}
		}

		return $protocols;
	}
    
    	/******************************************** Print style data to header *********************************************/

	function ll_rss_link() {
		global $llstylesheet, $rss_settings;
		
		if ( !empty( $rss_settings ) ) {
			$settingsname = 'LinkLibraryPP' . $rss_settings;
			$options = get_option( $settingsname );

			$feedtitle = ( empty( $options['rssfeedtitle'] ) ? __('Link Library Generated Feed', 'link-library') : $options['rssfeedtitle'] );

			$xpath = $this->relativePath( dirname( __FILE__ ), ABSPATH );
			echo '<link rel="alternate" type="application/rss+xml" title="' . esc_html( stripslashes( $feedtitle ) ) . '" href="' . home_url('/feed/linklibraryfeed?settingsset=' . $rss_settings/* . '&xpath=' . $xpath*/) . '" />';
			unset( $xpath );
		}

		if ( $llstylesheet ) {
			$genoptions = get_option( 'LinkLibraryGeneral' );
			
			echo "<style id='LinkLibraryStyle' type='text/css'>\n";
			echo stripslashes( $genoptions['fullstylesheet'] );
			echo "</style>\n";
		}
	}

	/****************************************** Add Link Category name to page title when option is present ********************************/
	function ll_title_creator( $title ) {
		global $wp_query;
		global $wpdb;
        global $llstylesheet;

        if ( $llstylesheet ) {
            $genoptions = get_option( 'LinkLibraryGeneral' );

            $categoryname = ( isset( $wp_query->query_vars['cat_name'] ) ? $wp_query->query_vars['cat_name'] : '' );
            $catid = ( isset( $_GET['cat_id'] ) ? intval($_GET['cat_id']) : '' );

            $linkcatquery = 'SELECT t.name ';
            $linkcatquery .= 'FROM ' . $this->db_prefix() . 'terms t LEFT JOIN ' . $this->db_prefix(). 'term_taxonomy tt ON (t.term_id = tt.term_id) ';
            $linkcatquery .= 'LEFT JOIN ' . $this->db_prefix() . 'term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ';
            $linkcatquery .= 'WHERE tt.taxonomy = "link_category" AND ';

            if ( !empty( $categoryname ) ) {
                    $linkcatquery .= 't.slug = "' . $categoryname . '"';
                    $nicecatname = $wpdb->get_var( $linkcatquery );
                    return $title . $genoptions['pagetitleprefix'] . $nicecatname . $genoptions['pagetitlesuffix'];
            } elseif ( !empty( $catid ) ) {
                    $linkcatquery .= 't.term_id = "' . $catid . '"';
                    $nicecatname = $wpdb->get_var( $linkcatquery );
                    return $title . $genoptions['pagetitleprefix'] . $nicecatname . $genoptions['pagetitlesuffix'];
            }
        }

		return $title;
	}
    
    	/************************************* Function to add to rewrite rules for permalink support **********************************/
	function ll_insertMyRewriteRules($rules)
	{
		$newrules = array();

		$genoptions = get_option('LinkLibraryGeneral');

		if ( !empty( $genoptions ) ) {
			for ( $i = 1; $i <= $genoptions['numberstylesets']; $i++ ) {
				$settingsname = 'LinkLibraryPP' . $i;
				$options = get_option( $settingsname );
				
				if ( $options['enablerewrite'] && !empty( $options['rewritepage'] ) ) {
                    $newrules['(' . $options['rewritepage'] . ')/(.+?)$'] = 'index.php?pagename=$matches[1]&cat_name=$matches[2]';
                }

				if ( $options['publishrssfeed'] ) {
					$xpath = $this->relativePath( dirname( __FILE__ ), ABSPATH );

					if ( !empty( $options['rssfeedaddress'] ) ) {
                        $newrules['(' . $options['rssfeedaddress'] . ')/(.+?)$'] = home_url() . '/feed/linklibraryfeed?settingsset=$matches[1]';
                    }
					unset( $xpath );
				}
			}
		}
		
		return $newrules + $rules;
	}

	// Adding the id var so that WP recognizes it
	function ll_insertMyRewriteQueryVars( $vars ) {
		array_push( $vars, 'cat_name' );
		return $vars;
	}

    function relativePath( $from, $to, $ps = DIRECTORY_SEPARATOR ) {
        $arFrom = explode( $ps, rtrim( $from, $ps ) );
        $arTo = explode( $ps, rtrim( $to, $ps ) );
        while( count( $arFrom ) && count( $arTo ) && ( $arFrom[0] == $arTo[0] ) ) {
            array_shift( $arFrom );
            array_shift( $arTo );
        }
        $return = str_pad( '', count($arFrom) * 3, '..'.$ps ) . implode( $ps, $arTo );

        // Don't disclose anything about the path is it's not needed, i.e. is the standard
        if( $return === '../../../' ) {
            $return = '';
        }

        return $return;
    }

	function CheckReciprocalLink( $RecipCheckAddress = '', $external_link = '' ) {
		$response = wp_remote_get( $external_link );

        if( is_wp_error( $response ) ) {
            $response_code = $response->get_error_code();
            if ( 'http_request_failed' == $response_code ) {
                return 'error_403';
            }
        } elseif ( $response['response']['code'] == '200' ) {
			if ( strpos( $response['body'], $RecipCheckAddress ) === false ) {
				return 'exists_notfound';
			} elseif ( strpos( $response['body'], $RecipCheckAddress ) !== false ) {
				return 'exists_found';
			}
		}

		return 'unreachable';
	}

    function link_library_insert_link( $linkdata, $wp_error = false, $addlinknoaddress = false) {
        global $wpdb;

        $defaults = array( 'link_id' => 0, 'link_name' => '', 'link_url' => '', 'link_rating' => 0 );

        $linkdata = wp_parse_args( $linkdata, $defaults );
        $linkdata = sanitize_bookmark( $linkdata, 'db' );

        extract( stripslashes_deep( $linkdata ), EXTR_SKIP );

        $update = false;

        if ( !empty( $link_id ) )
            $update = true;

        if ( isset( $link_name ) && trim( $link_name ) == '' ) {
            if ( isset( $link_url ) && trim( $link_url ) != '' ) {
                $link_name = $link_url;
            } else {
                return 0;
            }
        }

        if ($addlinknoaddress == false)
        {
            if ( trim( $link_url ) == '' )
                return 0;
        }

        if ( empty( $link_rating ) )
            $link_rating = 0;

        if ( empty( $link_image ) )
            $link_image = '';

        if ( empty( $link_target ) )
            $link_target = '';

        if ( empty( $link_visible ) )
            $link_visible = 'Y';

        if ( empty( $link_owner ) )
            $link_owner = get_current_user_id();

        if ( empty( $link_notes ) )
            $link_notes = '';

        if ( empty( $link_description ) )
            $link_description = '';

        if ( empty( $link_rss ) )
            $link_rss = '';

        if ( empty( $link_rel ) )
            $link_rel = '';

	    if ( empty( $link_updated ) )
		    $link_updated = '';

        // Make sure we set a valid category
        if ( ! isset( $link_category ) || 0 == count( $link_category ) || !is_array( $link_category ) ) {
            $link_category = array( get_option( 'default_link_category' ) );
        }

        if ( $update ) {
            if ( false === $wpdb->update( $wpdb->links, compact('link_url', 'link_name', 'link_image', 'link_target', 'link_description', 'link_visible', 'link_rating', 'link_rel', 'link_notes', 'link_rss', 'link_updated' ), compact('link_id') ) ) {
                if ( $wp_error )
                    return new WP_Error( 'db_update_error', __( 'Could not update link in the database', 'link-library' ), $wpdb->last_error );
                else
                    return 0;
            }
        } else {
            if ( false === $wpdb->insert( $wpdb->links, compact('link_url', 'link_name', 'link_image', 'link_target', 'link_description', 'link_visible', 'link_owner', 'link_rating', 'link_rel', 'link_notes', 'link_rss', 'link_updated' ) ) ) {
                if ( $wp_error )
                    return new WP_Error( 'db_insert_error', __( 'Could not insert link into the database', 'link-library' ), $wpdb->last_error );
                else
                    return 0;
            }
            $link_id = (int) $wpdb->insert_id;
        }

        wp_set_link_cats( $link_id, $link_category );

        if ( $update )
            do_action( 'edit_link', $link_id );
        else
            do_action( 'add_link', $link_id );

        clean_bookmark_cache( $link_id );

        return $link_id;
    }

    /* Output for users trying to directly call Link Library function, as was possible in pre-1.0 versions */

	function LinkLibraryCategories() {
        return __( 'Link Library no longer supports calling this function with individual arguments. Please use the admin panel to configure Link Library and the do_shortcode function to use Link Library output in your code.', 'link-library' );
	}

	function LinkLibrary() {
        return __( 'Link Library no longer supports calling this function with individual arguments. Please use the admin panel to configure Link Library and the do_shortcode function to use Link Library output in your code.', 'link-library' );
	}
	
	/********************************************** Function to Process [link-library-cats] shortcode *********************************************/
	
	function link_library_cats_func( $atts ) {
		$categorylistoverride = '';
		$excludecategoryoverride = '';
		$settings = '';

		extract( shortcode_atts( array (
			'categorylistoverride' => '',
			'excludecategoryoverride' => '',
			'settings' => ''
		), $atts ) );
		
		if ( empty( $settings ) ) {
			$settings = 1;
		}

        $settingsname = 'LinkLibraryPP' . $settings;
        $options = get_option( $settingsname );

        if ( !empty( $categorylistoverride ) ) {
            $options['categorylist'] = $categorylistoverride;
        }

		if ( !empty( $excludecategoryoverride ) ) {
            $options['excludecategorylist'] = $excludecategoryoverride;
        }

		$genoptions = get_option( 'LinkLibraryGeneral' );

        if ( $genoptions['debugmode'] ) {
            $mainoutputstarttime = microtime( true );
            $timeoutputstart = "\n<!-- Start Link Library Cats Time: " . $mainoutputstarttime . "-->\n";
        }

        require_once plugin_dir_path( __FILE__ ) . 'render-link-library-cats-sc.php';

        if ( $genoptions['debugmode'] ) {
            $timeoutput = "\n<!-- [link-library-cats] shortcode execution time: " . ( microtime( true ) - $mainoutputstarttime ) . "-->\n";
        }

		return ( true == $genoptions['debugmode'] ? $timeoutputstart : '' ) . RenderLinkLibraryCategories( $this, $genoptions, $options, $settings )  . ( true == $genoptions['debugmode'] ? $timeoutput : '' );
	}
	
	/********************************************** Function to Process [link-library-search] shortcode *********************************************/

	function link_library_search_func($atts) {
		$settings = '';

		extract(shortcode_atts(array(
			'settings' => ''
		), $atts));
		
		if ( empty( $settings ) ) {
            $options = get_option('LinkLibraryPP1');
        } else {
			$settingsname = 'LinkLibraryPP' . $settings;
			$options = get_option($settingsname);
		}

        require_once plugin_dir_path( __FILE__ ) . 'render-link-library-search-sc.php';
		return RenderLinkLibrarySearchForm( $options );
	}
	
	/********************************************** Function to Process [link-library-add-link] shortcode *********************************************/

	function link_library_addlink_func($atts, $content, $code) {
		$settings = '';
		$categorylistoverride = '';
		$excludecategoryoverride = '';

		extract(shortcode_atts(array(
			'settings' => '',
			'categorylistoverride' => '',
			'excludecategoryoverride' => ''
		), $atts));
                
		if ( empty( $settings ) ) {
            $settings = 1;
        }

		$settingsname = 'LinkLibraryPP' . $settings;
        $options = get_option($settingsname);
                
        $genoptions = get_option('LinkLibraryGeneral');
				
		if ( !empty( $categorylistoverride ) ) {
            $options['categorylist'] = $categorylistoverride;
        } elseif ( !empty( $options['addlinkcatlistoverride'] ) ) {
            $options['categorylist'] = $options['addlinkcatlistoverride'];
        }

		if ( !empty( $excludecategoryoverride ) ) {
            $options['excludecategorylist'] = $excludecategoryoverride;
        }

        require_once plugin_dir_path( __FILE__ ) . 'render-link-library-addlink-sc.php';
        return RenderLinkLibraryAddLinkForm( $this, $genoptions, $options, $settings, $code);
	}

	/********************************************** Function to Process [link-library-count] shortcode ***************************************/

	function link_library_count_func( $atts ) {
		extract( shortcode_atts( array(
			'categorylistoverride' => '',
			'excludecategoryoverride' => '',
			'settings' => ''
		), $atts ) );

		if ( empty( $settings ) ) {
			$settings = 1;
		}

		$settingsname = 'LinkLibraryPP' . $settings;
		$options = get_option( $settingsname );
		$genoptions = get_option( 'LinkLibraryGeneral' );

		if ( !empty( $categorylistoverride ) ) {
			$options['categorylist'] = $categorylistoverride;
		}

		if ( !empty( $excludecategoryoverride ) ) {
			$options['excludecategorylist'] = $excludecategoryoverride;
		}

		require_once plugin_dir_path( __FILE__ ) . 'render-link-library-sc.php';
		return RenderLinkLibrary( $this, $genoptions, $options, $settings, true );
	}
	
	/********************************************** Function to Process [link-library] shortcode *********************************************/

	function link_library_func( $atts ) {

        if ( isset( $_POST['ajaxupdate'] ) ) {
            check_ajax_referer( 'link_library_ajax_refresh' );
        }

		$settings = '';
		$notesoverride = '';
		$descoverride = '';
		$rssoverride = '';
		$categorylistoverride = '';
		$excludecategoryoverride = '';
		$tableoverride = '';
		$singlelinkid = '';

		extract( shortcode_atts( array(
			'categorylistoverride' => '',
			'excludecategoryoverride' => '',
			'notesoverride' => '',
			'descoverride' => '',
			'rssoverride' => '',
			'tableoverride' => '',
			'settings' => '',
			'singlelinkid' => ''
		), $atts ) );

		if ( empty( $settings ) && !isset( $_POST['settings'] ) ) {
			$settings = 1;
		} else if ( isset( $_POST['settings'] ) ) {
            $settings = intval( $_POST['settings'] );
        }

        $settingsname = 'LinkLibraryPP' . $settings;
        $options = get_option( $settingsname );
        $options['AJAXcatid'] = '';
        $options['AJAXpageid'] = '';

        if ( !empty( $notesoverride ) ) {
            $options['shownotes'] = $notesoverride;
        }

		if ( !empty( $descoverride ) ) {
            $options['showdescription'] = $descoverride;
        }

        if ( !empty( $rssoverride ) ) {
            $options['show_rss'] = $rssoverride;
        }

		if ( !empty( $categorylistoverride ) ) {
            $options['categorylist'] = $categorylistoverride;
        }

		if ( !empty( $excludecategoryoverride ) ) {
            $options['excludecategorylist'] = $excludecategoryoverride;
        }

		if ( !empty( $singlelinkid ) ) {
			$options['singlelinkid'] = $singlelinkid;
		}

		if ( !empty( $tableoverride ) ) {
            $options['displayastable'] = $tableoverride;
        }

        if ( isset( $_POST['ajaxupdate'] ) ) {
            if ( isset( $_POST['id'] ) ) {
                $catID = intval( $_POST['id'] );
                $options['AJAXcatid'] = $catID;
            }

            if ( isset( $_POST['linkresultpage'] ) ) {
                $pageID = intval( $_POST['linkresultpage'] );
                $options['AJAXpageid'] = $pageID;
            }
        }

        $genoptions = get_option( 'LinkLibraryGeneral' );
		
		if ( floatval( $genoptions['schemaversion'] ) < '5.0' ) {
			$this->ll_install();
			$genoptions = get_option( 'LinkLibraryGeneral' );
			
			if ( empty( $settings ) ) {
                $options = get_option( 'LinkLibraryPP1' );
            } else {
				$settingsname = 'LinkLibraryPP' . $settings;
				$options = get_option( $settingsname );
			}
		}

        $linklibraryoutput = '';

        if ( $genoptions['debugmode'] ) {
            $linklibraryoutput .= "\n<!-- Library Settings Info:" . print_r( $options, true ) . "-->\n";
            $mainoutputstarttime = microtime( true );
            $linklibraryoutput .= "\n<!-- Start Time: " . $mainoutputstarttime . "-->\n";
        }

        require_once plugin_dir_path( __FILE__ ) . 'render-link-library-sc.php';
        $linklibraryoutput .= RenderLinkLibrary( $this, $genoptions, $options, $settings, false );

        if ( isset( $_POST['ajaxupdate'] ) ) {
            echo $linklibraryoutput;

            if ( $genoptions['debugmode'] ) {
                echo "\n<!-- Execution Time: " . ( microtime( true ) - $mainoutputstarttime ) . "-->\n";
            }
            exit;
        } else {
            if ( $genoptions['debugmode'] ) {
                $timeoutput = "\n<!-- [link-library] shortcode execution time: " . ( microtime( true ) - $mainoutputstarttime ) . "-->\n";
            }
            return $linklibraryoutput . ( true == $genoptions['debugmode'] ? $timeoutput : '' );
        }
	}

	function conditionally_add_scripts_and_styles( $posts ) {
		if ( empty( $posts ) ) {
            return $posts;
        }
		
		global $llstylesheet;
		$load_jquery = false;
		$load_thickbox = false;
		
		if ( $llstylesheet ) {
			$load_style = true;
		} else {
			$load_style = false;
		}
		
		$genoptions = get_option( 'LinkLibraryGeneral' );

		if ( is_admin() ) {
			$load_jquery = false;
			$load_thickbox = false;
			$load_style = false;
		} else {
			foreach ( $posts as $post ) {
				$continuesearch = true;
				$searchpos = 0;
				$settingsetids = array();
				
				while ( $continuesearch ) {
					$linklibrarypos = stripos( $post->post_content, 'link-library ', $searchpos );
					if ( !$linklibrarypos ) {
						$linklibrarypos = stripos( $post->post_content, 'link-library]', $searchpos );
						if ( !$linklibrarypos ) {
                            if ( stripos( $post->post_content, 'link-library-cats' ) || stripos( $post->post_content, 'link-library-addlink' ) ) {
                                $load_style = true;
                            }
                        }
					}

					$continuesearch = $linklibrarypos;

					if ( $continuesearch ) {
						$load_style = true;
						$load_jquery = true;
						$shortcodeend = stripos( $post->post_content, ']', $linklibrarypos );
						if ( $shortcodeend ) {
                            $searchpos = $shortcodeend;
                        } else {
                            $searchpos = $linklibrarypos + 1;
                        }

						if ( $shortcodeend ) {
							$settingconfigpos = stripos( $post->post_content, 'settings=', $linklibrarypos );
							if ( $settingconfigpos && $settingconfigpos < $shortcodeend ) {
								$settingset = substr( $post->post_content, $settingconfigpos + 9, $shortcodeend - $settingconfigpos - 9 );
									
								$settingsetids[] = $settingset;
							} else if ( 0 == count($settingsetids) ) {
								$settingsetids[] = 1;
							}
						}
					}	
				}
			}
			
			if ( $settingsetids ) {
				foreach ( $settingsetids as $settingsetid ) {
					$settingsname = 'LinkLibraryPP' . $settingsetid;
					$options = get_option( $settingsname );
					
					if ( $options['showonecatonly'] ) {
						$load_jquery = true;
					}
			
					if ( $options['rsspreview'] || ( isset( $options['enable_link_popup'] ) && $options['enable_link_popup'] ) ) {
						$load_thickbox = true;
					}

					if ($options['publishrssfeed'] == true) {
						global $rss_settings;
						$rss_settings = $settingsetid;
					}	
				}
			}
				
			if ( !empty( $genoptions['includescriptcss'] ) ) {
				$pagelist = explode ( ',', $genoptions['includescriptcss'] );
                $loadscripts = false;
				foreach( $pagelist as $pageid ) {
                    if ( ( $pageid == 'front' && is_front_page() ) ||
                         ( $pageid == 'category' && is_category() ) ||
                         ( $pageid == 'all') ||
                         ( is_page( $pageid ) ) ) {
                        $load_jquery = true;
						$load_thickbox = true;
						$load_style = true;                        
					}
				}   
			}
		}
		
		if ( $load_style ) {			
			$llstylesheet = true;
		} else {
			$llstylesheet = false;
		}
	 
		if ( $load_jquery ) {
			wp_enqueue_script( 'jquery' );
		}
			
		if ( $load_thickbox ) {
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style ( 'thickbox' );
		}
	 
		return $posts;
	}

	function ll_popup_content() {
		require_once plugin_dir_path( __FILE__ ) . 'linkpopup.php';
		link_library_popup_content( $this );
	}

    function ll_template_redirect( $template ) {
	    if ( !empty( $_POST['link_library_user_link_submission'] ) ) {
            require_once plugin_dir_path( __FILE__ ) . 'usersubmission.php';
            link_library_process_user_submission( $this );
            return '';
        } else if ( !empty( $_GET['link_library_rss_preview'] ) ) {
            require_once plugin_dir_path( __FILE__ ) . 'rsspreview.php';
            link_library_generate_rss_preview( $this );
            return '';
        } else {
            return $template;
        }
    }

    function link_library_ajax_tracker() {
        require_once plugin_dir_path( __FILE__ ) . 'tracker.php';
        link_library_process_ajax_tracker( $this );
    }

    function link_library_generate_image() {
        global $my_link_library_plugin_admin;

        if ( empty( $my_link_library_plugin_admin ) ) {
            require_once plugin_dir_path( __FILE__ ) . 'link-library-admin.php';
            $my_link_library_plugin_admin = new link_library_plugin_admin();
        }

        require_once plugin_dir_path( __FILE__ ) . 'link-library-image-generator.php';
        link_library_ajax_image_generator( $my_link_library_plugin_admin );
    }
}

global $my_link_library_plugin;
$my_link_library_plugin = new link_library_plugin();

if ( is_admin() ) {

	/* Determine update method selected by user under General Settings or under Network Settings */
	$updatechannel = 'standard';

	if ( ( function_exists( 'is_multisite' ) && !is_multisite() ) || !function_exists( 'is_multisite' ) ) {
		$genoptions = get_option( 'LinkLibraryGeneral' );
		$genoptions = wp_parse_args( $genoptions, ll_reset_gen_settings( 'return' ) );

		if ( !empty( $genoptions['updatechannel'] ) ) {
			$updatechannel = $genoptions['updatechannel'];
		}
	} else if ( function_exists( 'is_multisite' ) && function_exists( 'is_network_admin' ) && is_multisite() && is_network_admin() ) {
		$networkoptions = get_site_option( 'LinkLibraryNetworkOptions' );

		if ( isset( $networkoptions ) && !empty( $networkoptions['updatechannel'] ) ) {
			$updatechannel = $networkoptions['updatechannel'];
		}
	}

	/* Install filter is user selected monthly updates to filter out dot dot dot minor releases (e.g. 5.8.8.x) */
	if ( 'monthly' == $updatechannel ) {
		add_filter( 'http_response', 'link_library_tweak_plugins_http_filter', 10, 3 );
	}

	if ( empty( $my_link_library_plugin_admin ) ) {
		global $my_link_library_plugin_admin;
		require plugin_dir_path( __FILE__ ) . 'link-library-admin.php';
		$my_link_library_plugin_admin = new link_library_plugin_admin();
	}
}