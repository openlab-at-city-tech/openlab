<?php

/**
 * Main get function for front-end display and checking
 *
 * It gets the value from our theme global variable which contains all the settings for the current template
 *
 * @param string  $option An option value to get
 * @param string  $part   An option part to get, i.e. if option is an array
 * @return mixed
 * @since  1.0
 */
if ( !function_exists( 'johannes_get' ) ):
    function johannes_get( $option = '', $part = '' ) {

        if ( empty( $option ) ) {
            return false;
        }

        $johannes = get_query_var( 'johannes' );

        if ( empty( $johannes ) ) {
            $johannes = johannes_setup();
        }

        if ( !empty( $part ) ) {

            if ( !isset( $johannes[$option][$part] ) ) {
                return false;
            }

            return $johannes[$option][$part];
        }

        if ( isset( $johannes[$option] ) ) {
            return $johannes[$option];
        }

        return false;
    }
endif;


/**
 * Function to set a specific option/value to our global front-end settings variable
 *
 * @param string  $option name of the option to set
 * @param mixed   $value  option value
 * @return void
 * @since  1.0
 */

if ( !function_exists( 'johannes_set' ) ):
    function johannes_set( $option, $value ) {
        global $wp_query;
        $johannes = get_query_var( 'johannes', array() );
        if ( !empty( $option ) ) {
            $johannes[$option] = $value;
            set_query_var( 'johannes', $johannes );
        }

    }
endif;


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

if ( !function_exists( '__johannes' ) ):
    function __johannes( $string_key ) {

        $translate = johannes_get_translate_options();

        if ( !johannes_get_option( 'enable_translate' ) ) {
            return $translate[$string_key]['text'];
        }

        $translated_string = johannes_get_option( 'tr_' . $string_key );

        if ( isset( $translate[$string_key]['hidden'] ) && trim( $translated_string ) == '' ) {
            return '';
        }

        if ( $translated_string == '-1' ) {
            return '';
        }

        if ( !empty( $translated_string ) ) {
            return  $translated_string;
        }

        return $translate[$string_key]['text'];
    }
endif;


/**
 * Get featured image
 *
 * Function gets featured image depending on the size and post id.
 * If image is not set, it gets the default featured image placehloder from theme options.
 *
 * @param string  $size               Image size ID
 * @param bool    $ignore_default_img Wheter to apply default featured image if post doesn't have featured image
 * @param bool    $post_id            If is not provided it will pull the image from the current post
 * @return string Image HTML output
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_featured_image' ) ):
    function johannes_get_featured_image( $size = 'full', $ignore_default_img = false, $post_id = false ) {

        if ( empty( $post_id ) ) {
            $post_id = get_the_ID();
        }

        if ( has_post_thumbnail( $post_id ) ) {

            return get_the_post_thumbnail( $post_id, $size );

        } else if ( !$ignore_default_img && ( $placeholder = johannes_get_option( 'default_fimg', 'image' ) ) ) {

                //If there is no featured image, try to get default placeholder from theme options

                global $placeholder_img, $placeholder_imgs;

                if ( empty( $placeholder_img ) ) {
                    $img_id = johannes_get_image_id_by_url( $placeholder );
                } else {
                    $img_id = $placeholder_img;
                }

                if ( !empty( $img_id ) ) {
                    if ( !isset( $placeholder_imgs[$size] ) ) {
                        $def_img = wp_get_attachment_image( $img_id, $size );
                    } else {
                        $def_img = $placeholder_imgs[$size];
                    }

                    if ( !empty( $def_img ) ) {
                        $placeholder_imgs[$size] = $def_img;

                        return wp_kses_post( $def_img );
                    }
                }

                return wp_kses_post( '<img src="' . esc_attr( $placeholder ) . '" class="size-'.esc_attr( $size ).'" alt="' . esc_attr( get_the_title( $post_id ) ) . '" />' );
            }


        return '';
    }
endif;



/**
 * Get category featured image
 *
 * Function gets category featured image depending on the size
 *
 * @param string  $size   Image size ID
 * @param int     $cat_id
 * @return string Image HTML output
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_category_featured_image' ) ):
    function johannes_get_category_featured_image( $size = 'full', $cat_id = false ) {

        if ( empty( $cat_id ) ) {
            $cat_id = get_queried_object_id();
        }

        $img_html = '';

        $img_url = johannes_get_category_meta( $cat_id, 'image' );

        if ( !empty( $img_url ) ) {
            $img_id = johannes_get_image_id_by_url( $img_url );
            $img_html = wp_get_attachment_image( $img_id, $size );
        }

        return wp_kses_post( $img_html );
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


if ( !function_exists( 'johannes_get_meta_data' ) ):
    function johannes_get_meta_data( $meta_data = array() ) {

        $output = '';

        if ( empty( $meta_data ) ) {
            return $output;
        }

        foreach ( $meta_data as $mkey ) {


            $meta = '';

            switch ( $mkey ) {

            case 'date':
                $meta = '<span class="updated">' . get_the_date() . '</span>';
                break;

            case 'author':
                $author_id = get_post_field( 'post_author', get_the_ID() );
                $meta = '<span class="vcard author">'. __johannes( 'by' ) . ' <a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ) . '">' . get_the_author_meta( 'display_name', $author_id ) . '</a></span>';
                break;

            case 'rtime':
                $meta = johannes_read_time( get_post_field( 'post_content', get_the_ID() ) );
                if ( !empty( $meta ) ) {
                    $meta .= ' ' . __johannes( 'min_read' );
                }
                break;

            case 'comments':
                if ( comments_open() || get_comments_number() ) {
                    ob_start();
                    $scroll_class = is_single() && is_main_query() ? 'johannes-scroll-animate' : '';
                    comments_popup_link( __johannes( 'no_comments' ), __johannes( 'one_comment' ), __johannes( 'multiple_comments' ), $scroll_class, '' );
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
                $output .= '<span class="meta-item meta-' . $mkey . '">' . $meta . '</span>';
            }
        }


        return wp_kses_post( $output );

    }
endif;




/**
 * Get post categories
 *
 * Function outputs category links with HTML
 *
 * @param int     $post_id
 * @return string HTML output of category links
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_category' ) ):
    function johannes_get_category( $post_id = false ) {

        if ( empty( $post_id ) ) {
            $post_id = get_the_ID();
        }

        $terms = array();

        $can_primary_category = !is_single() && johannes_is_yoast_active();
        $primary_category_id = $can_primary_category ? get_post_meta( $post_id, '_yoast_wpseo_primary_category', true ) : false;

        if ( !empty( $primary_category_id ) ) {

            $term = get_term( $primary_category_id );
            if ( !is_wp_error( $term ) && !empty( $term ) ) {
                $terms[0] = $term;
            }

        }

        if ( empty( $terms ) ) {
            $terms = get_the_terms( $post_id, 'category' );
        }

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return '';
        }

        $links = array();

        foreach ( $terms as $term ) {
            $link = get_term_link( $term, 'category' );
            if ( !is_wp_error( $link ) ) {
                $links[] = '<a href="' . esc_url( $link ) . '" rel="tag" class="cat-item cat-'.esc_attr( $term->term_id ).'">' . $term->name . '</a>';
            }
        }

        if ( !empty( $links ) ) {
            return implode( '', $links );
        }

        return '';

    }
endif;


/**
 * Get post format icon
 *
 * Function outputs post format icon HTML
 *
 * @return string HTML output of post format icons
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_format_icon' ) ):
    function johannes_get_format_icon( $format = '' ) {

        $icons = array(
            'video' => 'jf jf-video',
            'audio' => 'jf jf-audio',
            'gallery' => 'jf jf-image'
        );

        $icons = apply_filters( 'johannes_modify_format_icons', $icons );

        if ( !array_key_exists( $format, $icons ) ) {
            return '';
        }

        return '<i class="'.esc_attr( $icons[$format] ).'"></i>';

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

if ( !function_exists( 'johannes_get_excerpt' ) ):
    function johannes_get_excerpt( $limit = 250 ) {

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
                $more = johannes_get_option( 'more_string' );
                $content = wp_strip_all_tags( $content );
                $content = preg_replace( '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $content );
                $content = johannes_trim_chars( $content, $limit, $more );
            }

            return wp_kses_post( wpautop( $content ) );
        }

        return '';

    }
endif;


/**
 * Get previous/next posts
 *
 * @return array Previous and next post ids and labels
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_prev_next_posts' ) ):
    function johannes_get_prev_next_posts() {

        $prev = array();
        $next = array();

        if ( is_single() ) {

            $prev_post = get_adjacent_post( true, '', false, 'category' );
            $next_post = get_adjacent_post( true, '', true, 'category' );

            if ( !empty( $prev_post ) ) {
                $prev['id'] = $prev_post;
                $prev['label'] = __johannes( 'previous_post' );
            }

            if ( !empty( $next_post ) ) {
                $next['id'] = $next_post;
                $next['label'] = __johannes( 'next_post' );
            }

        }

        if ( empty( $prev ) && empty( $next ) ) {
            return array();
        }

        return array( 'prev' => $prev, 'next' => $next );

    }
endif;


/**
 * Get branding
 *
 * Returns HTML of logo or website title based on theme options
 *
 * @param string  $use_mini_logo Whether to use mini logo
 * @return string HTML
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_branding' ) ):
    function johannes_get_branding( $use_mini_logo = false ) {

        //Get all logos
        $logo = johannes_get_option( 'logo', 'image' );
        $logo_retina = johannes_get_option( 'logo_retina', 'image' );
        $logo_mini = johannes_get_option( 'logo_mini', 'image' );
        $logo_mini_retina = johannes_get_option( 'logo_mini_retina', 'image' );
        $logo_text_class = ''; //if there is no image we use textual class

        if ( empty( $logo_mini ) ) {
            $logo_mini = $logo;
        }

        if ( johannes_get( 'logo_is_displayed' ) && johannes_get_option( 'header_sticky_logo' ) == 'mini' ) {
            $use_mini_logo = true;
        }

        if ( $use_mini_logo ) {
            $logo =  $logo_mini;
            $logo_retina =  $logo_mini_retina;
        }

        if ( empty( $logo ) ) {

            $brand =  get_bloginfo( 'name' );
            $logo_text_class = 'logo-img-none';

        } else {
            $brand = '<picture class="johannes-logo">';
            $brand .= '<source media="(min-width: 1050px)" srcset="'.esc_attr( $logo );

            if ( !empty( $logo_retina ) ) {
                $brand .= ', '.esc_attr( $logo_retina ).' 2x';
            }

            $brand .= '">';
            $brand .= '<source srcset="'.esc_attr( $logo_mini );

            if ( !empty( $logo_mini_retina ) ) {
                $brand .= ', '.esc_attr( $logo_mini_retina ).' 2x';
            }

            $brand .= '">';
            $brand .= '<img src="'.esc_attr( $logo ).'" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
            $brand .= '</picture>';
        }

        $element = is_front_page() && !johannes_get( 'logo_is_displayed' ) ? 'h1' : 'span';
        $url = johannes_get_option( 'logo_custom_url' ) ? johannes_get_option( 'logo_custom_url' ) : home_url( '/' );
        $site_desc = !johannes_get( 'logo_is_displayed' ) && johannes_get_option( 'header_site_desc' ) ? '<span class="site-description d-none d-lg-block">' . get_bloginfo( 'description' ) . '</span>' : '';

        $output = '<' . esc_attr( $element ) . ' class="site-title h1 '.esc_attr( $logo_text_class ).'"><a href="' . esc_url( $url ) . '" rel="home">' . $brand . '</a></' . esc_attr( $element ) . '>' . $site_desc;

        johannes_set( 'logo_is_displayed', true );

        return apply_filters( 'johannes_modify_branding', $output );

    }
endif;


/**
 * Breadcrumbs
 *
 * Function provides support for several breadcrumb plugins
 * and gets its content to display on frontend
 *
 * @return string HTML output
 * @since  1.0
 */

if ( !function_exists( 'johannes_breadcrumbs' ) ):
    function johannes_breadcrumbs( ) {

        $has_breadcrumbs = johannes_get_option( 'breadcrumbs' );

        if ( $has_breadcrumbs == 'none' ) {
            return '';
        }

        $breadcrumbs = '';

        if ( $has_breadcrumbs == 'yoast' && function_exists( 'yoast_breadcrumb' ) ) {
            $breadcrumbs = yoast_breadcrumb( '<div class="container johannes-breadcrumbs"><div class="row"><div class="col-12 col-lg-8">', '</div></div></div>', false );
        }

        if ( $has_breadcrumbs == 'bcn' && function_exists( 'bcn_display' ) ) {
            $breadcrumbs = '<div class="container johannes-breadcrumbs">'.bcn_display( true ).'</div>';
        }

        return $breadcrumbs;
    }
endif;

/**
 * Get author social links
 *
 * @param int     $author_id ID of an author/user
 * @return string HTML output of social links
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_author_links' ) ):
    function johannes_get_author_links( $author_id, $archive_link = true, $social = true ) {

        $output = '';


        if ( $archive_link ) {

            $output .= '<a href="' . esc_url( get_author_posts_url( $author_id, get_the_author_meta( 'user_nicename', $author_id ) ) ) . '" class="johannes-button johannes-button-secondary johannes-button-medium">' . __johannes( 'view_all' ) . '</a>';
        }

        if ( $social ) {

            if ( $url = get_the_author_meta( 'url', $author_id ) ) {
                $output .= '<a href="' . esc_url( $url ) . '" target="_blank" class="johannes-author-button"><i class="johannes-icon jf jf-website"></i></a>';
            }

            $social = johannes_get_social();

            if ( !empty( $social ) ) {
                foreach ( $social as $id => $name ) {
                    if ( $social_url = get_the_author_meta( $id, $author_id ) ) {

                        if ( $id == 'twitter' ) {
                            $social_url = ( strpos( $social_url, 'http' ) === false ) ? 'https://twitter.com/' . $social_url : $social_url;
                        }

                        $output .= '<a href="' . esc_url( $social_url ) . '" target="_blank" class="johannes-author-button"><i class="fa fa-' . $id . '"></i></a>';
                    }
                }
            }
        }

        return wp_kses_post( $output );
    }
endif;

/**
 * Generate related posts query
 *
 * Depending on post ID generate related posts using theme options
 *
 * @param int     $post_id
 * @return object WP_Query
 * @since  1.0
 */
if ( !function_exists( 'johannes_get_related' ) ):
    function johannes_get_related( $post_id = false ) {

        if ( empty( $post_id ) ) {
            $post_id = get_the_ID();
        }

        $args['post_type'] = 'post';

        //Exclude current post from query
        $args['post__not_in'] = array( $post_id );

        $num_posts = absint( johannes_get_option( 'related_limit' ) );

        if ( $num_posts > 100 ) {
            $num_posts = 100;
        }

        $args['posts_per_page'] = $num_posts;

        $args['orderby'] = johannes_get_option( 'related_order' );
        $args['order'] = 'DESC';

        if ( $args['orderby'] == 'title' ) {
            $args['order'] = 'ASC';
        }

        $type = johannes_get_option( 'related_type' );

        if ( $type ) {

            switch ( $type ) {

            case 'cat':
                $cats = get_the_category( $post_id );
                $cat_args = array();
                if ( !empty( $cats ) ) {
                    foreach ( $cats as $k => $cat ) {
                        $cat_args[] = $cat->term_id;
                    }
                }
                $args['category__in'] = $cat_args;
                break;

            case 'tag':
                $tags = get_the_tags( $post_id );
                $tag_args = array();
                if ( !empty( $tags ) ) {
                    foreach ( $tags as $tag ) {
                        $tag_args[] = $tag->term_id;
                    }
                }
                $args['tag__in'] = $tag_args;
                break;

            case 'cat_and_tag':
                $cats = get_the_category( $post_id );
                $cat_args = array();
                if ( !empty( $cats ) ) {
                    foreach ( $cats as $k => $cat ) {
                        $cat_args[] = $cat->term_id;
                    }
                }
                $tags = get_the_tags( $post_id );
                $tag_args = array();
                if ( !empty( $tags ) ) {
                    foreach ( $tags as $tag ) {
                        $tag_args[] = $tag->term_id;
                    }
                }
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'id',
                        'terms'    => $cat_args,
                    ),
                    array(
                        'taxonomy' => 'post_tag',
                        'field'    => 'id',
                        'terms'    => $tag_args,
                    )
                );
                break;

            case 'cat_or_tag':
                $cats = get_the_category( $post_id );
                $cat_args = array();
                if ( !empty( $cats ) ) {
                    foreach ( $cats as $k => $cat ) {
                        $cat_args[] = $cat->term_id;
                    }
                }
                $tags = get_the_tags( $post_id );
                $tag_args = array();
                if ( !empty( $tags ) ) {
                    foreach ( $tags as $tag ) {
                        $tag_args[] = $tag->term_id;
                    }
                }
                $args['tax_query'] = array(
                    'relation' => 'OR',
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'id',
                        'terms'    => $cat_args,
                    ),
                    array(
                        'taxonomy' => 'post_tag',
                        'field'    => 'id',
                        'terms'    => $tag_args,
                    )
                );
                break;

            case 'author':
                global $post;
                $author_id = isset( $post->post_author ) ? $post->post_author : 0;
                $args['author'] = $author_id;
                break;

            case 'default':
                break;
            }
        }

        $related_query = new WP_Query( $args );

        return $related_query;
    }
endif;


/**
 * Get post layout options
 * Return post layout params based on theme options
 *
 * @return string
 * @since  1.0
 */
if ( !function_exists( 'johannes_get_post_layout_options' ) ):
    function johannes_get_post_layout_options( $layout ) {
        $args = array();

        $args['category'] = johannes_get_option( 'layout_' . $layout . '_cat' );
        $args['format'] = johannes_get_option( 'layout_' . $layout . '_format' ) && johannes_get_post_format() ? johannes_get_post_format() : '';
        $args['meta'] = johannes_get_option( 'layout_' . $layout . '_meta' );
        $args['excerpt'] = johannes_get_option( 'layout_' . $layout . '_excerpt' ) ? johannes_get_option( 'layout_' . $layout . '_excerpt_limit' ) : false;
        $args['excerpt_type'] = $args['excerpt'] ? johannes_get_option( 'layout_' . $layout . '_excerpt_type' ) : 'auto';
        $args['rm'] = johannes_get_option( 'layout_' . $layout . '_rm' );
        $args['width'] = johannes_get_option( 'layout_' . $layout . '_width' );

        $args = apply_filters( 'johannes_modify_post_layout_' . $layout . '_options', $args );

        return $args;
    }
endif;

/**
 * Check if is sidebar enabled
 *
 * @param string  $position_to_check sidebar position to check
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'johannes_has_sidebar' ) ):
    function johannes_has_sidebar( $position_to_check = 'right' ) {

        $sidebar = johannes_get( 'sidebar' );

        if ( empty( $sidebar ) ) {
            return false;
        }

        if ( $sidebar['position'] != $position_to_check ) {
            return false;
        }

        return true;
    }
endif;


/**
 * Get boostrap wrapper col class depending
 * on what layout is used
 *
 * @return string
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_loop_col_class' ) ):
    function johannes_get_loop_col_class( $layout_id = 1 ) {

        $params = johannes_get_layouts_map();

        if ( !array_key_exists( $layout_id, $params ) ) {
            return '';
        }

        if ( isset( $params[$layout_id]['col'] ) && $params[$layout_id]['col'] ) {
            return $params[$layout_id]['col'];
        }

        return '';

    }
endif;

/**
 * Get class for featred area layout wrapper
 *
 * @return string
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_fa_class' ) ):
    function johannes_get_fa_class( $layout_id = 1 ) {

        $params = johannes_get_featured_layouts_map();

        if ( !array_key_exists( $layout_id, $params ) ) {
            return '';
        }

        $classes = array();

        if ( isset( $params[$layout_id]['slider'] ) ) {
            $classes[] = 'johannes-slider has-arrows';
        }

        if ( isset( $params[$layout_id]['carousel'] ) ) {
            $classes[] = 'johannes-carousel';
        }

        if ( isset( $params[$layout_id]['center'] ) ) {
            $classes[] = 'slider-center';
        }

        if ( isset( $params[$layout_id]['classes'] ) ) {
            $classes[] = $params[$layout_id]['classes'];
        }

        return implode( ' ', $classes );
    }
endif;


/**
 * Get loop layout params
 *
 * @param int     $layout_id
 * @param int     $loop_index current post in the loop
 * @return array set of parameters
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_loop_params' ) ):
    function johannes_get_loop_params( $layout_id = 1, $loop_index = 0, $type = 'default' ) {

        if ( $type == 'fa' ) {
            $params = johannes_get_featured_layouts_map();
        } else {
            $params = johannes_get_layouts_map();
        }

        if ( array_key_exists( $layout_id, $params ) ) {

            $layout = $params[$layout_id]['loop'];

            if ( count( $layout ) > $loop_index && !is_paged() ) {
                return $layout[$loop_index];
            }

            return $layout[count( $layout ) - 1];
        }

        return false;

    }
endif;

/**
 * Check if specified loop layout can display sidebar
 *
 * @param int     $layout_id
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'johannes_loop_has_sidebar' ) ):
    function johannes_loop_has_sidebar( $layout_id = 1 ) {

        $params = johannes_get_layouts_map();

        if ( !array_key_exists( $layout_id, $params ) ) {
            return false;
        }

        if ( isset( $params[$layout_id]['sidebar'] ) && $params[$layout_id]['sidebar'] ) {
            return true;
        }

        return false;

    }
endif;


/**
 * Check if single post has avatar and return offset class
 *
 * @return string
 * @since  1.0
 */

if ( !function_exists( 'johannes_single_content_offset' ) ):
    function johannes_single_content_offset() {

        if ( johannes_has_sidebar( 'none' ) ) {
            return '';
        }

        if ( !johannes_get( 'avatar' ) && !johannes_has_sidebar( 'none' ) ) {
            return '';
        }

        if ( johannes_get_option( 'single_width' ) == '8' ) {
            return '';
        }

        if ( johannes_get_option( 'single_width' ) == '6' ) {
            return 'offset-lg-2';
        }

        if ( johannes_get_option( 'single_width' ) == '7' ) {
            return 'offset-lg-1';
        }



        return 'offset-lg-2';

    }
endif;

/**
 * Check if specified layout is indented
 *
 * @param int     $layout_id
 * @return bool
 * @since  1.0
 */

if ( !function_exists( 'johannes_layout_is_indented' ) ):
    function johannes_layout_is_indented( $layout_id = 1 ) {

        return in_array( $layout_id, array( 1, 9, 10, 15, 16, 17, 18, 21, 22, 28, 29, 32, 33, 34, 35 ) );

    }
endif;



/**
 * Get archive content
 *
 * Function gets parts of the archive content like ttitle, description, post count, etc...
 *
 * @return array Args
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_archive_content' ) ):
    function johannes_get_archive_content( $part = false ) {

        global $wp_query;

        if ( ( is_home() && is_front_page() ) || ( is_home() && !$wp_query->is_posts_page ) ) {
            return false;
        }

        $defaults = array(
            'avatar' => '',
            'title' => '',
            'meta' => johannes_get_archive_posts_count(),
            'description' => '',
            'subnav' => '',
        );

        $args = array();

        $title_prefix = '';

        if ( is_category() ) {

            $title_prefix = __johannes( 'category' );
            $args['title'] =  single_cat_title( '', false );
            $args['description'] = category_description();

        } else if ( is_tag() ) {

                $title_prefix = __johannes( 'tag' );
                $args['title'] =  single_tag_title( '', false );
                $args['description'] = tag_description();

            } else if ( is_author() ) {
                $title_prefix =  __johannes( 'author' );
                $args['title'] = get_the_author();
                $args['description'] = get_the_author_meta( 'description' );
                $args['avatar'] =  get_avatar( get_the_author_meta( 'ID' ), 100 );
                $args['subnav'] =  johannes_get_author_links( get_the_author_meta( 'ID' ), false );

            } else if ( is_tax() ) {
                $title_prefix =  __johannes( 'archive' );
                $args['title'] = single_term_title( '', false );

            } else if ( is_search() ) {
                $args['description'] =
                    __johannes( 'search_results_for' ). '
                    <form class="search-form search-alt" action="'.esc_url( home_url( '/' ) ).'" method="get">
                    <input name="s" type="text" value="" placeholder="'.esc_attr( get_search_query() ).'" />';

                if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
                    $args['description'] .= '<input type="hidden" name="lang" value="'.esc_attr( ICL_LANGUAGE_CODE ).'">';
                }

                $args['description'] .= '<button type="submit">'.esc_attr( __johannes( 'search_again' ) ).'</button></form>';

                $args['meta'] = '';

            } else if ( is_day() ) {
                $title_prefix =  __johannes( 'archive' );
                $args['title'] = get_the_date();

            } else if ( is_month() ) {
                $title_prefix =  __johannes( 'archive' );
                $args['title'] = get_the_date( 'F Y' );

            } else if ( is_year() ) {
                $title_prefix =  __johannes( 'archive' );
                $args['title'] = get_the_date( 'Y' );

            } else if ( $wp_query->is_posts_page ) {
                $posts_page = get_option( 'page_for_posts' );
                $args['title'] = get_the_title( $posts_page );

            } else if ( is_archive() ) {

                $args['title'] = __johannes( 'archive' );
            }

        if ( $title_prefix ) {
            $args['title'] = '<span>' . $title_prefix . '</span>' . $args['title'];
        }

        $args = apply_filters( 'johannes_modify_archive_content', wp_parse_args( $args, $defaults ) );

        if ( $part && isset( $args[$part] ) ) {
            return $args[$part];
        }

        return $args;

    }
endif;


/**
 * Get media
 *
 * Function gets featured image, video block or gallery depending on post format
 *
 * @since  1.1
 */

if ( !function_exists( 'johannes_get_media' ) ):

    function johannes_get_media( $format = false,  $before = '', $after = '' ) {

        $output = '';

        switch ( $format ) {

        case 'video':
            $output = hybrid_media_grabber( array( 'type' => 'video', 'split_media' => true ) );
            break;

        case 'gallery':
            $output = hybrid_media_grabber( array( 'type' => 'gallery', 'split_media' => true ) );
            break;

        default:

            if ( johannes_get( 'fimg' ) && $fimg = johannes_get_featured_image( 'johannes-single-'. johannes_get( 'layout' ) , true ) ) {
                $output = wp_kses_post( $fimg );

                if ( johannes_get( 'fimg_cap' ) && $caption = get_post( get_post_thumbnail_id() )->post_excerpt ) {
                    $output .= '<figure class="wp-caption-text">'.wp_kses_post( $caption ).'</figure>';
                }
            }
            break;
        }

        if ( empty( $output ) ) {
            return '';
        }

        return $before . $output . $after;

    }

endif;

?>