<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

function ll_get_link_image( $url, $name, $mode, $linkid, $cid, $filepath, $filepathtype, $thumbnailsize, $thumbnailgenerator ) {
    $status = false;
    if ( $url != "" && $name != "" ) {
        $protocol = is_ssl() ? 'https://' : 'http://';

        if ( $mode == 'thumb' || $mode == 'thumbonly' ) {
            if ( $thumbnailgenerator == 'robothumb' ) {
                $genthumburl = $protocol . "www.robothumb.com/src/?url=" . esc_html( $url ) . "&size=" . $thumbnailsize;
            } elseif ( $thumbnailgenerator == 'pagepeeker' ) {
                if ( empty( $cid ) ) {
                    $genthumburl = $protocol . "free.pagepeeker.com/v2/thumbs.php?size=" . $thumbnailsize . "&url=" . esc_html( $url );
                } else {
                    $genthumburl = $protocol . "api.pagepeeker.com/v2/thumbs.php?size=" . $thumbnailsize . "&url=" . esc_html( $url );
                }
            } elseif ( $thumbnailgenerator == 'shrinktheweb' ) {
                $genthumburl .= $protocol . "images.shrinktheweb.com/xino.php?stwembed=1&stwaccesskeyid=" . $cid . "&stwsize=" . $thumbnailsize . "&stwurl=" . esc_html( $url );
            } elseif ( $thumbnailgenerator == 'thumbshots' ) {
                if ( !empty ( $cid ) ) {
                    $genthumburl = $protocol . "images.thumbshots.com/image.aspx?cid=" . rawurlencode( $cid ) . "&v1=w=120&url=" . esc_html( $url );
                }
            } elseif ( $thumbnailgenerator == 'wordpressmshots' ) {
                $dimension_array = explode( 'x', $thumbnailsize );
                $genthumburl = $protocol . "s0.wp.com/mshots/v1/" . rtrim( esc_html( $url ), '/' ) . '?w=' . $dimension_array[0]. '&h=' . $dimension_array[1];
            }
        } elseif ( $mode == 'favicon' || $mode == 'favicononly' ) {
            $genthumburl = $protocol . "www.google.com/s2/favicons?domain=" . $url;
        }

        $uploads = wp_upload_dir();

        if ( !file_exists( $uploads['basedir'] ) ) {
            return __( 'Please create a folder called uploads under your Wordpress /wp-content/ directory with write permissions to use this functionality.', 'link-library' );
        } elseif ( !is_writable( $uploads['basedir'] ) ) {
            return __( 'Please make sure that the /wp-content/uploads/ directory has write permissions to use this functionality.', 'link-library' );
        } else {
            if ( !file_exists( $uploads['basedir'] . '/' . $filepath ) ) {
                mkdir( $uploads['basedir'] . '/' . $filepath );
            }
        }

        $img    = $uploads['basedir'] . "/" . $filepath . "/" . $linkid . '.png';

        if ( $thumbnailgenerator != 'google' || $mode == 'favicon' || $mode == 'favicononly' ) {
            $tempfile = download_url( $genthumburl );
            if ( !is_wp_error( $tempfile ) ) {
                copy( $tempfile, $img );
                unlink( $tempfile );
                $status = true;
            }
        } elseif ( $thumbnailgenerator == 'google' && ( $mode == 'thumb' || $mode == 'thumbonly' ) ) {
             $screenshot = file_get_contents('https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed?url=' . esc_html( $url ) );
            $data_whole = json_decode($screenshot);

            if (isset($data_whole->error) || empty($screenshot)) {
                if (!(substr($url, 0, 4) == 'http')) {
                    $url2 = 'https%3A%2F%2F' . $url;
                    $screenshot = file_get_contents('https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed?url=' . $url2 );
                    $data_whole = json_decode($screenshot);
                }
            }
            if (isset($data_whole->error) || empty($screenshot)) {
                if (!(substr($url, 0, 3) == 'www')) {
                    $url3 = 'https%3A%2F%2F' . 'www.' . $url;
                    $screenshot = file_get_contents('https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed?url=' . $url3 );
                    $data_whole = json_decode($screenshot);
                }
            }
            if (isset($data_whole->error)) {
                $status = false;
            } else {
                if (isset($data_whole->lighthouseResult->audits->{'final-screenshot'}->details->data)) {
                    $data = $data_whole->lighthouseResult->audits->{'final-screenshot'}->details->data;
                    $data = str_replace('data:image/jpeg;base64','',$data);

                    $data = str_replace('_', '/', $data);
                    $data = str_replace('-', '+', $data);
                    $base64img = str_replace('data:image/jpeg;base64,', '', $data);

                    $data   		  = base64_decode($data);
                    $upload_dir       = $uploads['basedir'] . '/' . $filepath; // Set upload folder
                    $image_data       = $data; // img data
                    $unique_file_name = wp_unique_filename( $uploads['basedir'] . '/' . $filepath, $linkid . '.png' ); // Generate unique name
                    $filename         = basename( $unique_file_name ); // Create image file name

                    // Create the image  file on the server
                    file_put_contents( $img, $image_data );

                    $exists = file_exists($tmp);
                    $status = true;
                } else {
                    $status = false;
                }

            }

        }

        if ( $status !== false ) {
            if ( $filepathtype == 'absolute' || empty( $filepathtype ) ) {
                $newimagedata = $uploads['baseurl'] . "/" . $filepath . "/" . $linkid . ".png";
            } elseif ( $filepathtype == 'relative' ) {
                $parsedaddress = parse_url( $uploads['baseurl'] );
                $newimagedata  = $parsedaddress['path'] . "/" . $filepath . "/" . $linkid . ".png";
            }

            if ( $mode == 'thumb' || $mode == 'favicon' ) {
                update_post_meta( $linkid, 'link_image', $newimagedata );

                if ( empty( $newimagedata ) ) {
                    delete_post_thumbnail( $linkid );
                } else {
                    $wpFileType = wp_check_filetype( $newimagedata, null);

                    $attachment = array(
                        'post_mime_type' => $wpFileType['type'],  // file type
                        'post_title' => sanitize_file_name( $newimagedata ),  // sanitize and use image name as file name
                        'post_content' => '',  // could use the image description here as the content
                        'post_status' => 'inherit'
                    );

                    // insert and return attachment id
                    $attachmentId = wp_insert_attachment( $attachment, $newimagedata, $linkid );
                    $attachmentData = wp_generate_attachment_metadata( $attachmentId, $newimagedata );
                    wp_update_attachment_metadata( $attachmentId, $attachmentData );
                    set_post_thumbnail( $linkid, $attachmentId );
                }
            }

            return $newimagedata;
        } else {
            return "";
        }
    }

    return 'Parameters are missing';
}

function link_library_ajax_image_generator ( $my_link_library_plugin_admin ) {

    check_ajax_referer( 'link_library_generate_image' );

    $generaloptions = get_option( 'LinkLibraryGeneral' );
    $generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );
    extract( $generaloptions );

    $name = $_POST['name'];
    $url = $_POST['url'];
    $mode = $_POST['mode'];
    $cid = $_POST['cid'];
    $filepath = $_POST['filepath'];
    $filepathtype = $_POST['filepathtype'];
    $linkid = intval($_POST['linkid']);

    if ( in_array( $generaloptions['thumbnailgenerator'], array( 'robothumb', 'thumbshots', 'google', 'wordpressmshots' ) ) ) {
	    echo ll_get_link_image($url, $name, $mode, $linkid, $cid, $filepath, $filepathtype, $generaloptions['thumbnailsize'], $generaloptions['thumbnailgenerator'] );
    } elseif ( 'pagepeeker' == $generaloptions['thumbnailgenerator'] ) {
	    echo ll_get_link_image($url, $name, $mode, $linkid, $generaloptions['pagepeekerid'], $filepath, $filepathtype, $generaloptions['pagepeekersize'], $generaloptions['thumbnailgenerator'] );
    } elseif ( 'shrinktheweb' == $generaloptions['thumbnailgenerator'] ) {
	    echo ll_get_link_image($url, $name, $mode, $linkid, $generaloptions['shrinkthewebaccesskey'], $filepath, $filepathtype, $generaloptions['stwthumbnailsize'], $generaloptions['thumbnailgenerator'] );
    }

    exit;
}

function link_library_image_generator( $LLPluginClass, $options = array(), $autogen = false ) {

    $genoptions = get_option( 'LinkLibraryGeneral' );
	$genoptions = wp_parse_args( $genoptions, ll_reset_gen_settings( 'return' ) );

    if ( isset( $_GET['genthumbs'] ) ) {
        check_admin_referer( 'llgenthumbs' );
    }

    if ( isset( $_GET['genfavicons'] ) ) {
        check_admin_referer( 'llgenfavicons' );
    }

    if ( isset( $_GET['genthumbs'] ) || isset( $_GET['genthumbsingle'] ) || $autogen ) {
        $filepath = "link-library-images";
    } elseif ( isset( $_GET['genfavicons'] ) || isset( $_GET['genfaviconsingle'] ) ) {
        $filepath = "link-library-favicons";
    }

    $uploads = wp_upload_dir();

    if ( !file_exists( $uploads['basedir'] ) ) {
        echo "<div id='message' class='updated fade'><p><strong>" . __( 'Please create a folder called uploads under your Wordpress /wp-content/ directory with write permissions to use this functionality.', 'link-library' ) . "</strong></p></div>";
    } elseif ( !is_writable( $uploads['basedir'] ) ) {
        echo "<div id='message' class='updated fade'><p><strong>" . __( 'Please make sure that the /wp-content/uploads/ directory has write permissions to use this functionality.', 'link-library' ) . "</strong></p></div>";
    } else {
        if ( !file_exists( $uploads['basedir'] . '/' . $filepath ) ) {
            mkdir( $uploads['basedir'] . '/' . $filepath );
        }

        if ( isset( $_GET['genthumbs'] ) || isset( $_GET['genthumbsingle'] ) || $autogen ) {
            $genmode = 'thumb';
        } elseif ( isset( $_GET['genfavicons'] ) || isset( $_GET['genfaviconsingle'] ) ) {
            $genmode = 'favicon';
        }

        $link_query_args = array( 'post_type' => 'link_library_links', 'posts_per_page' => -1, 'post_status' => array( 'publish', 'pending', 'draft', 'future', 'private' ) );

        if ( $options['categorylist_cpt'] != "" && !isset( $_GET['genthumbsingle'] ) && !isset( $_GET['genfaviconsingle'] ) ) {
            $link_query_args['tax_query'] = array(
                                                array( 'taxonomy' => $genoptions['cattaxonomy'],
                                                        'field' => 'term-id',
                                                        'terms' => $options['categorylist_cpt'],
                                                        'operator' => 'IN' )
                                                );
        } else if ( isset( $_GET['genthumbsingle'] ) || isset( $_GET['genfaviconsingle'] ) ) {
            $link_query_args['p'] = intval( $_GET['linkid'] );
        }

        $the_link_query = new WP_Query( $link_query_args );

        if ( $the_link_query->have_posts() ) {
            $filescreated = 0;
            $totallinks   = $the_link_query->found_posts;

            while ( $the_link_query->have_posts() ) {
                $the_link_query->the_post();

                $link_url = get_post_meta( get_the_ID(), 'link_url', true );
                $link_image = get_post_meta( get_the_ID(), 'link_image', true );

                if ( !$options['uselocalimagesoverthumbshots'] || ( $options['uselocalimagesoverthumbshots'] && empty( $link_image ) ) ) {
                    if ( in_array( $genoptions['thumbnailgenerator'], array( 'robothumb', 'thumbshots', 'wordpressmshots', 'google' ) ) ) {
                        ll_get_link_image( $link_url, get_the_title(), $genmode, get_the_ID(), $genoptions['thumbshotscid'], $filepath, $genoptions['imagefilepath'], $genoptions['thumbnailsize'], $genoptions['thumbnailgenerator'] );
                    } elseif ( 'pagepeeker' == $genoptions['thumbnailgenerator'] ) {
                        ll_get_link_image( $link_url, get_the_title(), $genmode, get_the_ID(), $genoptions['pagepeekerid'], $filepath, $genoptions['imagefilepath'], $genoptions['pagepeekersize'], $genoptions['thumbnailgenerator'] );
                    } elseif ( 'shrinktheweb' == $genoptions['thumbnailgenerator'] ) {
                        ll_get_link_image( $link_url, get_the_title(), $genmode, get_the_ID(), $genoptions['shrinkthewebaccesskey'], $filepath, $genoptions['imagefilepath'], $genoptions['stwthumbnailsize'], $genoptions['thumbnailgenerator'] );
                    }
                }
                $linkname = get_the_title();
            }

            wp_reset_postdata();

            if ( isset( $_GET['genthumbs'] ) ) {
                echo "<div id='message' class='updated fade'><p><strong>" . __( 'Thumbnails successfully generated!', 'link-library' ) . "</strong></p></div>";
            } elseif ( isset( $_GET['genfavicons'] ) ) {
                echo "<div id='message' class='updated fade'><p><strong>" . __( 'Favicons successfully generated!', 'link-library' ) . "</strong></p></div>";
            } elseif ( isset( $_GET['genthumbsingle'] ) ) {
                echo "<div id='message' class='updated fade'><p><strong>" . __( 'Thumbnail successfully generated for', 'link-library' ) . " " . $linkname . ".</strong></p></div>";
            } elseif ( isset( $_GET['genfaviconsingle'] ) ) {
                echo "<div id='message' class='updated fade'><p><strong>" . __( 'Favicon successfully generated for', 'link-library' ) . " " . $linkname . ".</strong></p></div>";
            }
        }
    }
}
