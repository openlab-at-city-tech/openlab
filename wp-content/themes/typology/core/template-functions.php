<?php

/**
 * Wrapper function for __()
 *
 * It checks if specific text is translated via options panel
 * If option is set, it returns translated text from theme options
 * If option is not set, it returns default translation string (from language file)
 *
 * @param string  $string_key Key name (id) of translation option
 * @return string Returns translated string
 * @since  1.0
 */

if ( !function_exists( '__typology' ) ):
    function __typology( $string_key ) {
        if ( ( $translated_string = typology_get_option( 'tr_' . $string_key ) ) && typology_get_option( 'enable_translate' ) ) {

            if ( $translated_string == '-1' ) {
                return '';
            }

            return wp_kses_post( $translated_string );

        } else {
            $translate = typology_get_translate_options();
            return wp_kses_post( $translate[$string_key]['text'] );
        }
    }
endif;


/**
 * Get meta data
 *
 * Function outputs meta data HTML
 *
 * @param array   $meta_data
 * @return string HTML output of meta data
 * @since  1.0
 */

if ( !function_exists( 'typology_get_meta_data' ) ):
    function typology_get_meta_data( $meta_data = array() ) {

        $output = '';

        if ( empty( $meta_data ) ) {
            return $output;
        }


        foreach ( $meta_data as $mkey ) {


            $meta = '';

            switch ( $mkey ) {

                case 'date':
                    $date = typology_get_option('post_modified_date') ? get_the_modified_date() : get_the_date();
                    $meta = '<span class="updated">' . $date . '</span>';
                    break;

                case 'author':
                    if ( typology_is_co_authors_active() && $coauthors_meta = get_coauthors() ) {
                        $temp = array();
                        foreach ( $coauthors_meta as $key ) {
                            $temp[] = '<span class="vcard author"><span class="fn"><a href="'.esc_url( get_author_posts_url( $key->ID, $key->user_nicename ) ).'">'.$key->display_name.'</a></span></span>';
                        }
                        $temp = implode( ',', $temp );
                        $meta = __typology( 'by' ) . ' <div class="coauthors">'.$temp.'</div>';


                    } else {
                        $author_id = get_post_field( 'post_author', get_the_ID() );
                        $meta = __typology( 'by' ) . ' <span class="vcard author"><span class="fn"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ) . '">' . get_the_author_meta( 'display_name', $author_id ) . '</a></span></span>';
                    }
                    break;

                case 'rtime':
                    $meta = typology_read_time( get_post_field( 'post_content', get_the_ID() ) );
                    if ( !empty( $meta ) ) {
                        $meta .= ' ' . __typology( 'min_read' );
                    }
                    break;

                case 'comments':
                    if ( comments_open() || get_comments_number() ) {
                        ob_start();
                        comments_popup_link( __typology( 'no_comments' ), __typology( 'one_comment' ), __typology( 'multiple_comments' ) );
                        $meta = ob_get_contents();
                        ob_end_clean();
                    } else {
                        $meta = '';
                    }
                    break;

                case 'category':
                    $cats = get_the_category_list( ', ' );
                    if ( !empty( $cats ) ) {
                        $meta = __typology( 'in' ) . ' ' . $cats;
                    }
                    break;

                case 'views':
                    if ( typology_is_wp_post_views_active() ) {
                        $meta = the_views(false);
                    }
                    break;

                default:
                    break;
            }

            if ( !empty( $meta ) ) {
                $output .= '<div class="meta-item meta-' . $mkey . '">' . $meta . '</div>';
            }
        }


        return wp_kses_post( $output );

    }
endif;


/**
 * Get buttons data
 *
 * Function outputs buttons data HTML
 *
 * @param array   $meta_data
 * @return string HTML output of meta data
 * @since  1.0
 */

if ( !function_exists( 'typology_get_buttons_data' ) ):
    function typology_get_buttons_data( $meta_data = array() ) {

        $output = '';

        if ( empty( $meta_data ) ) {
            return $output;
        }

        foreach ( $meta_data as $mkey ) {


            $meta = '';

            switch ( $mkey ) {


            case 'rm':
                $meta = '<a href="' . esc_url( get_permalink() ) . '" class="typology-button">' . __typology( 'read_on' ) . '</a>';
                break;

            case 'rl':
                $meta = '<a href="javascript:void(0);" class="typology-button button-invert typology-rl pocket" data-url="https://getpocket.com/edit?url=' . urlencode( esc_url( get_permalink() ) ) . '"><i class="fa fa-bookmark-o"></i>' . __typology( 'read_later' ) . '</a>';
                break;

            case 'comments':
                if ( comments_open() || get_comments_number() ) {
                    ob_start();
                    comments_popup_link( '<i class="fa fa-comment-o"></i>' . __typology( 'no_comments' ), '<i class="fa fa-comment-o"></i>' . __typology( 'one_comment' ), '<i class="fa fa-comments-o"></i>' . __typology( 'multiple_comments' ), 'typology-button button-invert' );
                    $meta = ob_get_contents();
                    ob_end_clean();
                } else {
                    $meta = '';
                }
                break;

            default:
                break;
            }

            if ( !empty( $meta ) ) {
                $output .= $meta;
            }
        }


        return $output;

    }
endif;

/**
 * Get post excerpt
 *
 * Function outputs post excerpt for specific layout
 *
 * @param int     $limit Number of characters to limit excerpt
 * @return string HTML output of category links
 * @since  1.0
 */

if ( !function_exists( 'typology_get_excerpt' ) ):
    function typology_get_excerpt( $limit = 250 ) {

        $manual_excerpt = false;

        if ( has_excerpt() ) {
            $content = get_the_excerpt();
            $manual_excerpt = true;
        } else {
            $text = get_the_content( '' );
            $text = strip_shortcodes( $text );
            $text = apply_filters( 'the_content', $text );
            $content = str_replace( ']]>', ']]&gt;', $text );
        }

        if ( !empty( $content ) ) {
            if ( !empty( $limit ) || !$manual_excerpt ) {
                $more = typology_get_option( 'more_string' );
                $content = wp_strip_all_tags( $content );
                $content = preg_replace( '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $content );
                $content = typology_trim_chars( $content, $limit, $more );
            }
            return wp_kses_post( wpautop( $content ) );
        }

        return '';

    }
endif;

/**
 * Get first letter
 *
 * Function gets first letter of a string,
 * if string is not provided it gets first letter from current post title
 *
 * @param string  $string String from which to get letter
 * @return string First letter of a string
 * @since  1.0
 */

if ( !function_exists( 'typology_get_letter' ) ):
    function typology_get_letter( $string = '' ) {

        $string = empty( $string ) ? wp_strip_all_tags( get_the_title() ) : $string;

        if ( empty( $string ) ) {
            return '';
        }

        return typology_trim_chars( $string, 2, '' );

    }
endif;


/**
 * Get archive heading
 *
 * Function gets title and description for current archive template
 *
 * @return array Args
 * @since  1.0
 */

if ( !function_exists( 'typology_get_archive_heading' ) ):
    function typology_get_archive_heading() {

        $defaults = array(
            'pre' => '',
            'title' => '',
            'desc' => '',
            'avatar' => ''
        );

        $args = array();

        if ( is_category() ) {

            $obj = get_queried_object();
            $args['pre'] = __typology( 'category' );
            $args['title'] = single_cat_title( '', false );
            $args['desc'] = typology_get_option( 'archive_description' ) ? category_description() : '' ;

        } else if ( is_author() ) {

                $obj = get_queried_object();

                if ( empty( $obj ) ) {
                    global $author;
                    $obj = isset( $_GET['author_name'] ) ? get_user_by( 'slug', $author_name ) : get_userdata( intval( $author ) );
                }

                $args['pre'] = __typology( 'author' );
                $args['title'] = $obj->display_name;
                $args['desc'] = typology_get_option( 'archive_description' ) ? get_the_author_meta( 'description', $obj->ID ) : '';
                $args['avatar'] = typology_get_option( 'use_author_image' ) ? get_avatar( $obj->ID, 100 ) : '';


            } else if ( is_tax() ) {

                $args['title'] = single_term_title( '', false );

            } else if ( is_home() && ( $posts_page = get_option( 'page_for_posts' ) ) && !is_page_template( 'template-home.php' ) ) {

                $args['title'] = get_the_title( $posts_page );

            } else if ( is_search() ) {

                $args['pre'] = __typology( 'search_results_for' );
                $args['title'] = get_search_query();

            } else if ( is_tag() ) {

                $args['pre'] = __typology( 'tag' );
                $args['title'] = single_tag_title( '', false );
                $args['desc'] = typology_get_option( 'archive_description' ) ? tag_description() : '';

            } else if ( is_day() ) {

                $args['pre'] = __typology( 'archive' );
                $args['title'] = get_the_date();

            } else if ( is_month() ) {
                $args['pre'] = __typology( 'archive' );
                $args['title'] = get_the_date( 'F Y' );
            } else if ( is_year() ) {
                $args['pre'] = __typology( 'archive' );
                $args['title'] = get_the_date( 'Y' );
            } else if ( is_home() ) {
                $args['title'] = __typology( 'latest_stories' );
            }

        return wp_parse_args( $args, $defaults );
    }
endif;


/**
 * Get author social links
 *
 * @param int     $author_id ID of an author/user
 * @return string HTML output of social links
 * @since  1.0
 */

if ( !function_exists( 'typology_get_author_links' ) ):
    function typology_get_author_links( $author_id ) {

        $output = '';

        if ( is_singular() ) {
            $url = typology_is_co_authors_active() ? get_author_posts_url( $author_id, get_the_author_meta( 'user_nicename', $author_id ) ) : get_author_posts_url( get_the_author_meta( 'ID', $author_id ) );
            $output .= '<a class="typology-button-social hover-on" href="' . esc_url( $url ) . '">' . __typology( 'view_all' ) . '</a>';
        }

        $url = typology_is_co_authors_active() ? get_the_author_meta( 'url', $author_id ) : get_the_author_meta( 'url', $author_id );

        if ( $url ) {
            $output .= '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener" class="typology-icon-social hover-on fa fa-link"></a>';
        }

        $social = typology_get_social();

        if ( !empty( $social ) ) {
            foreach ( $social as $id => $name ) {
                if ( $social_url = get_the_author_meta( $id, $author_id ) ) {

                    if ( $id == 'twitter' ) {
                        $social_url = ( strpos( $social_url, 'http' ) === false ) ? 'https://twitter.com/' . $social_url : $social_url;
                    }

                    $output .= '<a href="' . esc_url( $social_url ) . '" target="_blank" rel="noopener" class="typology-icon-social hover-on fa fa-' . esc_attr( $id ) . '"></a>';
                }
            }
        }

        return wp_kses_post( $output );
    }
endif;

/**
 * Header display element
 *
 * Checks is specific header element should be displayed based on theme options
 *
 * @param string  $element ID of an element to check
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'typology_header_display' ) ):
    function typology_header_display( $element ) {

        $elements = typology_get_option( 'header_elements' );

        if ( isset( $elements[$element] ) && $elements[$element] ) {
            return true;
        }

        return false;

    }
endif;

/**
 * Get active header elements
 *
 * Get header active header elements.
 * Elements keys from $elements array are actually file names in template-parts/header/elements and they will be dynamically included.
 *
 * @return array|bool
 * @since  1.2
 */

if ( !function_exists( 'typology_get_header_elements' ) ):
    function typology_get_header_elements() {
        if ( $elements = typology_get_option( 'header_elements' ) ) {
            $elements['sidebar-button'] = 1;
            return array_intersect( array_keys( array_reverse( array_filter( $elements ) ) ), array( 'sidebar-button', 'search-dropdown', 'social-menu-dropdown' ) );
        }

        return false;
    }
endif;

/**
 * Meta display
 *
 * Checks what meta elements to display based on specific Layout
 *
 * @param string  $Layout ID
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'typology_meta_display' ) ):
    function typology_meta_display( $layout ) {

        $meta = array_keys( array_filter( typology_get_option( 'layout_' . $layout . '_meta' ) ) );
        return $meta;

    }
endif;

/**
 * Action buttons display
 *
 * Checks what action button elements to display based on specific Layout
 *
 * @param string  $Layout ID
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'typology_buttons_display' ) ):
    function typology_buttons_display( $layout ) {

        $meta = array_keys( array_filter( typology_get_option( 'layout_' . $layout . '_buttons' ) ) );
        return $meta;

    }
endif;


/**
 * Get branding
 *
 * Returns HTML of logo or website title based on theme options
 *
 * @param string  $element ID of an element to check
 * @return string HTML
 * @since  1.0
 */

if ( !function_exists( 'typology_get_branding' ) ):
    function typology_get_branding() {

        global $typology_h1_used;

        $logo = typology_get_option( 'logo' );
        $brand = !empty( $logo ) ? '<img class="typology-logo" src="' . esc_url( $logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" >' : get_bloginfo( 'name' );
        $element = is_front_page() && empty( $typology_h1_used ) ? 'h1' : 'span';
        $url = typology_get_option( 'logo_custom_url' ) ? typology_get_option( 'logo_custom_url' ) : home_url( '/' );

        $output = '<' . $element . ' class="site-title h4"><a href="' . esc_url( $url ) . '" rel="home">' . wp_kses_post( $brand ) . '</a></' . esc_attr( $element ) . '>';

        $typology_h1_used = true;

        return $output;

    }
endif;

/**
 * Display section head
 *
 * Outputs section heading HTML based on passed arguments
 *
 * @param array   $args
 * @return string HTML
 * @since  1.0
 */

if ( !function_exists( 'typology_section_heading' ) ):
    function typology_section_heading( $args ) {

        $defaults = array(
            'title' => '',
            'pre' => '',
            'element' => 'h3',
            'avatar' => '',
            'desc' => ''
        );

        $args = typology_parse_args( $args, $defaults );

        $output = '';

        if ( !empty( $args['title'] ) ) {

            $output .= '<div class="section-head">';
            $output .= !empty( $args['avatar'] ) ? '<div class="section-avatar">' . $args['avatar'] . '</div>' : '';
            $output .= '<' . esc_attr( $args['element'] ) . ' class="section-title h6">';
            $output .= !empty( $args['pre'] ) ? '<span class="typology-archive-title">' . wp_kses_post( $args['pre'] ) . '</span>' : '';
            $output .= esc_html( $args['title'] ) . '</' . esc_attr( $args['element'] ) . '>';
            $output .= !empty( $args['desc'] ) ? '<div class="section-content archive-desc">' .wp_kses_post( $args['desc'] ) . '</div>' : '';
            $output .= '</div>';

        }


        echo wp_kses_post( $output );

    }
endif;


/**
 * Check if image or video is selected to be displayed in cover
 *
 * @return array
 * @since  1.2
 */

if ( !function_exists( 'typology_cover_media' ) ):
    function typology_cover_media( $object = '' ) {

        $media = array();

        if ( typology_get_option( 'cover_bg_media' ) == 'image' && typology_get_option( 'cover_bg_img' ) ) {
            $media = array( 'src' => typology_get_option( 'cover_bg_img' ), 'type' => 'image' );
        } else if ( typology_get_option( 'cover_bg_media' ) == 'video' && typology_get_option( 'cover_bg_video' ) ) {
                $media = array( 'src' => typology_get_option( 'cover_bg_video' ), 'type' => 'video' );
            }

        if ( is_single() ) {
            $meta = typology_get_post_meta();
            if ( $meta['fimg'] == 'cover' && has_post_thumbnail() ) {
                $media = array( 'src' => get_the_post_thumbnail_url( get_the_ID(), 'typology-cover' ), 'type' => 'image' );
            }
        }

        if ( !is_front_page() && is_page() ) {
            $meta = typology_get_page_meta();
            if ( $meta['fimg'] == 'cover' && has_post_thumbnail() ) {
                $media = array( 'src' => get_the_post_thumbnail_url( get_the_ID(), 'typology-cover' ), 'type' => 'image' );
            }
        }

        return $media;
    }
endif;

/**
 * Output image or video
 *
 * @return string HTML
 * @since  1.2
 */

if ( !function_exists( 'typology_display_media' ) ):
    function typology_display_media( $args = array() ) {

        $defaults = array( 'type' => 'image', 'src' => '' );
        $args = wp_parse_args( $args, $defaults );

        $cover_video_fallback_image = typology_get_option( 'cover_bg_video_image' );
        $cover_video_fallback_image = !empty( $cover_video_fallback_image ) ? '<img class="typology-fallback-video-img" src="'.esc_url( $cover_video_fallback_image ).'" />' : '';

        $output = '';
        if ( $args['type'] == 'image' ) {
            $output = '<img src="' . esc_url( $args['src'] ) . '"/>';
        } else if ( $args['type'] == 'video' ) {
                $output = '<video autoplay loop muted>
                        <source src="' . esc_url( $args['src'] ) . '">
                       </video>'. $cover_video_fallback_image;
            }
        echo do_shortcode( $output );
    }
endif;

/**
 * Display ads
 *
 * @since  1.5.1
 *
 * @return boolean
 */
if ( !function_exists( 'typology_can_display_ads' ) ):
    function typology_can_display_ads() {
        if ( is_404() && typology_get_option( 'ad_exclude_404' ) ) {
            return false;
        }

        $exclude_ids_option = typology_get_option( 'ad_exclude_from_pages' );
        $exclude_ids = !empty( $exclude_ids_option ) ? $exclude_ids_option : array();

        if ( is_page() && in_array( get_queried_object_id(), $exclude_ids ) ) {
            return false;
        }

        return true;
    }
endif;


/**
 * Display ads
 *
 * @since  1.5.2
 *
 * @return boolean
 */
if ( !function_exists( 'typology_has_ad_between' ) ):
    function typology_has_ad_between( $current_post ) {

        if ( !typology_can_display_ads() ) {
            return false;
        }

        $ad_between_posts = typology_get_option( 'ad_between_posts' );
        $ads_between_posts_position = absint( typology_get_option( 'ad_between_posts_position' ) ) - 1;

        if( $ad_between_posts && $ads_between_posts_position == $current_post ){
            return true;
        }

        return false;
    }
endif;

/**
 * Display header
 *
 * @since  1.6.3
 *
 * @return boolean
 */
if ( !function_exists( 'typology_display_header' ) ):
    function typology_display_header( ) {

        if ( ! is_page_template( 'template-blank.php' ) ) {
            return true;
        }

        return false;
    }
endif;

/**
 * Display footer
 *
 * @since  1.6.3
 *
 * @return boolean
 */
if ( !function_exists( 'typology_display_footer' ) ):
    function typology_display_footer( ) {
        
        if ( ! is_page_template( 'template-blank.php' ) ) {
            return true;
        }
        return false;
    }
endif;