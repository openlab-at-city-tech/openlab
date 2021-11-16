<?php
/**
 * Widget Class
 *
 * This file holds the class that extends the WP_Widget class,
 * creating the widget.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */

/**
 * Top Authors Widget Class
 *
 * This creates the widget options in the backend and the widget UI
 * in the front-end.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */
class Top_Authors_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * The widget constructor uses the parent class constructor
     * to add the widget to WordPress, we just provide the basic
     * details
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function __construct() {
        $widget_details = array(
            'classname' => 'top-authors-widget',
            'description' => __( 'A customizable top authors list widget', 'top-authors' )
        );

        parent::__construct( 'top-authors', __( 'Top Authors', 'top-authors' ), $widget_details );

    }


    /**
     * Widget Form
     *
     * The form shown in the admin when building the widget.
     *
     * @param array $instance The widget details
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function form( $instance ) {

        $title = ( !empty( $instance['title'] ) ) ? $instance['title'] : '';
        $count = ( !empty( $instance['count'] ) ) ? $instance['count'] : 5;

        $exclude_roles = ( !empty( $instance['exclude_roles'] ) ) ? $instance['exclude_roles'] : array();
        $include_post_types = ( !empty( $instance['include_post_types'] ) ) ? $instance['include_post_types'] : array( 'post' );

        $preset = ( !isset( $instance['preset'] ) ) ? 'list_count' : $instance['preset'];

        $template = ( !empty( $instance['template'] ) ) ? $instance['template'] : '<li><a href="%linktoposts%">%gravatar% %firstname% %lastname% </a> number of posts: %nrofposts%</li>';

        $before_list = ( !isset( $instance['before_list'] ) ) ? '<ul>' :  $instance['before_list'];

        $after_list = ( !isset( $instance['after_list'] ) ) ? '<ul>' :  $instance['after_list'];

        $archive_specific = ( !empty( $instance['archive_specific'] ) ) ? true : false;

        $custom_id = ( !isset( $instance['custom_id'] ) ) ? '' : $instance['custom_id'];

        $presets = array(
            __( 'Custom Structure', 'top-authors' ) => 'custom',
            __( 'Gravatar Only', 'top-authors' ) => 'gravatars',
            __( 'Gravatar And Name', 'top-authors' ) => 'gravatar_name',
            __( 'List With Post Count', 'top-authors' ) => 'list_count',
            __( 'List With Gravatar And Post Count', 'top-authors' ) => 'gravatar_list_count',
        );

        ?>
        <div class='top-authors'>
            <p>
                <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'top-authors' ) ?> </label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_name( 'count' ); ?>"><?php _e( 'Authors To Show:', 'top-authors' ) ?> </label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
            </p>

            <p>
                <input id="<?php echo $this->get_field_id( 'archive_specific' ); ?>" name="<?php echo $this->get_field_name( 'archive_specific' ); ?>" type="checkbox" <?php checked( $archive_specific, true ) ?> /> <label for="<?php echo $this->get_field_name( 'archive_specific' ); ?>" value='true'>
                    <?php _e( 'Archive specific authors', 'top-authors' ) ?><a href='https://wordpress.org/plugins/top-authors/other_notes/' class='help'><?php _e( 'help', 'top-authors' ) ?></a></label>
            </p>

            <div class="help-content hidden">
            <?php _e( 'Check the box to show most popular authors for the shown term on archive pages. This allows you to create "most popular in category X" widgets', 'top-authors' ) ?>
            </div>



            <p>
                <?php $roles = $this->get_usable_roles(); ?>
                <label for="<?php echo $this->get_field_name( 'exclude_roles' ); ?>"><?php _e( 'Exclude Roles:', 'top-authors' ) ?> </label>
                <select multiple='multiple' class='role-select' id="<?php echo $this->get_field_id( 'exclude_roles' ); ?>" name="<?php echo $this->get_field_name( 'exclude_roles' ); ?>[]">
                <?php
                foreach( $roles as $slug => $name ) {
                    $selected = ( in_array( $slug, $exclude_roles ) ) ? 'selected="selected"' : '';
                    echo '<option ' . $selected . ' value="' . $slug . '">' . $name . '</option>';
                }
                ?>
                </select>
            </p>


            <p>
                <?php $post_types = $this->get_usable_post_types(); ?>
                <label for="<?php echo $this->get_field_name( 'include_post_types' ); ?>"><?php _e( 'Include Post Types:', 'top-authors' ) ?> </label>
                <select multiple='multiple' class='post-type-select' id="<?php echo $this->get_field_id( 'include_post_types' ); ?>" name="<?php echo $this->get_field_name( 'include_post_types' ); ?>[]">
                <?php
                foreach( $post_types as $post_type => $data ) {
                    $selected = ( in_array( $post_type, $include_post_types ) ) ? 'selected="selected"' : '';
                    echo '<option ' . $selected . ' value="' . $post_type . '">' . $data->label . '</option>';
                }
                ?>
                </select>
            </p>

            <p>
                <label for="<?php echo $this->get_field_name( 'preset' ); ?>"><?php _e( 'Preset Display:', 'top-authors' ) ?> </label>
                <select class='widefat preset-selector' id="<?php echo $this->get_field_id( 'preset' ); ?>" name="<?php echo $this->get_field_name( 'preset' ); ?>">
                <?php
                foreach( $presets as $name => $value ) {
                    echo '<option ' . selected( $preset, $value ) . ' value="' . $value . '">' . $name . '</option>';
                }
                ?>
                </select>
            </p>

            <?php $hidden = ( $preset == 'custom' ) ? '' : 'style="display:none"' ?>
            <div class='custom-settings' <?php echo $hidden ?>>

                <p>
                    <label for="<?php echo $this->get_field_name( 'template' ); ?>"><?php _e( 'Display Template:', 'top-authors' ) ?> <a href='https://wordpress.org/plugins/top-authors/other_notes/' class='help'>help</a> </label>
                    <textarea class="widefat" class='display-template' id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" type="text" ><?php echo esc_attr( $template ); ?></textarea>
                </p>

                <div class="help-content hidden">
                    <p><?php _e( 'The following placeholders can be used and will be replaced by actual data from the listed author:', 'top-authors' ) ?></p>
                    <ul>
                        <li><strong>%posts_url%</strong>: <?php _e( "The URL to the user's post archive page", 'top-authors' ) ?></li>
                        <li><strong>%website_url%</strong>: <?php _e( "The URL to the user's website", 'top-authors' ) ?></li>
                        <li><strong>%gravatar_SIZE%</strong>: <?php echo sprintf( __( "The gravatar of the user at the given size. For example, to display a 50px Gravatar your would use %s", 'top-authors'), '%gravatar_50%' ) ?></li>
                        <li><strong>%firstname%</strong>: <?php _e( "The user's first name", 'top-authors' ) ?></li>
                        <li><strong>%lastname%</strong>: <?php _e( "The user's last name", 'top-authors' ) ?></li>
                        <li><strong>%displayname%</strong>: <?php _e( "The user's display name", 'top-authors' ) ?></li>
                        <li><strong>%username%</strong>: <?php _e( "The user's username", 'top-authors' ) ?></li>
                        <li><strong>%post_count%</strong>: <?php _e( "Number of posts", 'top-authors' ) ?></li>
                        <li><strong>%meta_FIELD%</strong>: <?php echo sprintf( __( "Displays the given meta field. If you store a user's Twitter name in the 'twitter' meta field you could use %s to display it.", 'top-authors' ) , '%meta_twitter%') ?></li>

                    </ul>
                </div>

                <p>
                    <label for="<?php echo $this->get_field_name( 'before_list' ); ?>"><?php _e( 'Before List:', 'top-authors' ) ?> </label>
                    <textarea class="widefat" id="<?php echo $this->get_field_id( 'before_list' ); ?>" name="<?php echo $this->get_field_name( 'before_list' ); ?>" type="text" ><?php echo esc_attr( $before_list ); ?></textarea>
                </p>


                <p>
                    <label for="<?php echo $this->get_field_name( 'after_list' ); ?>"><?php _e( 'After List:', 'top-authors' ) ?> </label>
                    <textarea class="widefat" id="<?php echo $this->get_field_id( 'after_list' ); ?>" name="<?php echo $this->get_field_name( 'after_list' ); ?>" type="text" ><?php echo esc_attr( $after_list ); ?></textarea>
                </p>

            </div>

            <p>
                <label for="<?php echo $this->get_field_name( 'custom_id' ); ?>"><?php _e( 'Custom ID:', 'top-authors' ) ?> </label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'custom_id' ); ?>" name="<?php echo $this->get_field_name( 'custom_id' ); ?>" type="text" value="<?php echo esc_attr( $custom_id ); ?>" />
            </p>

        </div>

        <?php


    }


    /**
     * Update Handling
     *
     * Before the instance is returned we retrieve the tweets and
     * store them in a transient.
     *
     * @param array $new_instance The newly saved widget values
     * @param array $old_instance The old widget values
     * @uses retrieve_tweets()
     * @return array The final widget values
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function update( $new_instance, $old_instance ) {
        $new_instance['exclude_roles'] = ( empty( $new_instance['exclude_roles'] ) ) ? array() : $new_instance['exclude_roles'];
        $new_instance['archive_specific'] = ( empty( $new_instance['archive_specific'] ) ) ? false : true;
        return $new_instance;
    }

    /**
     * Front End Output
     *
     * Handles the visitor-facing side of the widget. We enqueue our
     * assets here to make sure they only load when needed. The tweets
     * are retrieved and then displayed according to Twitter's specs.
     *
     * @param array $args The widget area details
     * @param array $instance The widget details
     * @global object $wpdb The WordPress database object
     * @global object $wp_query The WordPress query object
     * @global object $author Author object
     * @link https://dev.twitter.com/overview/terms/display-requirements
     * @uses get_tweets()
     * @uses format_tweet_text()
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function widget( $args, $instance ) {
        global $wpdb;

        $defaults = array(
            'custom_id' => ''
        );

        $instance = wp_parse_args( $instance, $defaults );

        // Get the ID of all the required posts
        $atts = array(
            'post_type' => $instance['include_post_types'],
            'fields' => 'ids',
            'posts_per_page' => -1
        );

        // Narrow posts down to specific categories is needed
        if( ( is_category() || is_tag() || is_tax() ) && $instance['archive_specific']  == true ) {
            global $wp_query;
            if( is_category() ) {
                $atts['cat'] = $wp_query->query_vars['cat'];
            }
            elseif( is_tag() ) {
                $atts['tag_id'] = $wp_query->query_vars['tag_id'];
            }
            elseif( is_tax() ) {
                $atts['tax_query'] = array(
                    'taxonomy' => $wp_query->query_vars['taxonomy'],
                    'field' => 'slug',
                    'terms' => $wp_query->query_vars['term'],
                );
            }
        }

        $atts = apply_filters( 'ta/post_query', $atts, $instance );

        // Grab all posts
        $posts = new WP_Query( $atts );
        $posts = implode( ',', $posts->posts );

        if( !empty( $posts ) ) {
         // Select user ids ordered by the number of posts they've written
         $users = $wpdb->get_results( "SELECT post_author, COUNT(ID) as post_count FROM $wpdb->posts WHERE ID IN ($posts) GROUP BY post_author ORDER BY post_count DESC" );
        }

        if( !empty( $users ) ) {

            // Before widget and title
            echo $args['before_widget'];

            if( !empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
            }

            $user_ids = array();
            foreach( $users as $user ) {
                $user_ids[] = $user->post_author;
            }

            $user_objects = get_users(array(
                'include' => $user_ids
            ));

            $user_array = array();

            foreach( $user_objects as $key => $user ) {

                $intersect = array_intersect( $user->roles, $instance['exclude_roles'] );

                if( empty( $intersect ) ) {
                    $user_array[$user->ID] = $user;
                }
            }

            foreach( $users as $key => $user ) {
                if( empty( $user_array[$user->post_author] ) ) {
                    unset( $users[$key] );
                }
                else {
                    $user->user = $user_array[$user->post_author];
                }
            }

            $users = array_slice( $users, 0, $instance['count'] );

            // Display preset
            if( method_exists( $this, 'display_' . $instance['preset'] ) ) {
                call_user_func( array( $this, 'display_' . $instance['preset'] ), $users, $instance );
            }
            // Display custom list
            else {
                echo '<div class="ta-custom ' . $instance['custom_id'] . '">';
                echo $instance['before_list'];

                foreach( $users as $user ) {
                    global $author;
                    $author = $user->user;
                    $output = $this->replace_all_tags( $instance['template'], $user->post_count );
                    echo $output;
                }

                echo $instance['after_list'];
                echo '</div>';
            }

            echo $args['after_widget'];

        }
    }


    /**
     * Replace Tags
     *
     * Replaces placeholder tags with the actual data they represent. The
     * current list of replacement tags is:
     *
     * %posts_url%: The URL to the user's post archive page
     * %website_url%: The URL to the user's website
     * %gravatar_SIZE%: The gravatar of the user at the given size. For example,
     * to display a 50px Gravatar your would use %gravatar_50%
     * %firstname%: The user's first name
     * %lastname%: The user's last name
     * %displayname%: The user's display name
     * %username%: The user's username
     * %post_count%: Number of posts
     * %nrofposts%: Number of posts (this is available for legacy reasons)
     * %meta_FIELD%: Displays the given meta field. If you store a user's
     * Twitter name in the 'twitter' meta field you could use %meta_twitter% to
     * display it.
     *
     * @param string $template HTML content including template tags
     * @param int $post_count The post count for the user
     * @global object $author The author's object
     * @return string Template HTML content with real data
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function replace_all_tags( $template, $post_count ) {
        global $author;
        $replacements = array(
            '%firstname%' => $author->first_name,
            '%lastname%' => $author->last_name,
            '%displayname%' => $author->display_name,
            '%username%' => $author->user_login,
            '%nrofposts%' => $post_count,
            '%post_count%' => $post_count,
            '%gravatar%' => get_avatar( $author->ID, 24 ),
            '%linktoposts%' => get_author_posts_url( $author->ID ),
            '%posts_url%' => get_author_posts_url( $author->ID ),
            '%website_url%' => $author->user_url,
        );
        $output = str_replace( array_keys( $replacements ), array_values( $replacements ), $template );

        $output = preg_replace_callback( "/%gravatar_(.*?)%/im", array( $this, 'replace_gravatar') , $output );
        $output = preg_replace_callback( "/%meta_(.*?)%/im", array( $this, 'replace_meta') , $output );

        return $output;
    }


    /**
     * Display List Count Preset
     *
     * Displays the "List With Post Count" template that can be selected
     * in the widget
     *
     * @global object $author The author's object
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function display_list_count( $users, $instance ) {
        echo '<ul class="ta-preset ta-list-count ' . $instance['custom_id'] . '">';
        foreach( $users as $user ) {
            global $author;
            $template = "<li><a href='%posts_url%'>%displayname%</a> (%post_count%)</li>";
            $author = get_userdata( $user->post_author );
            $output = $this->replace_all_tags( $template, $user->post_count );
            echo $output;
        }
        echo '</ul>';
    }

    /**
     * Display Gravatar And Post Count List Preset
     *
     * Displays the "List With Gravatar And Post Count" template that can be selected
     * in the widget
     *
     * @global object $author The author's object
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function display_gravatar_list_count( $users, $instance ) {
        wp_enqueue_style( 'ta-preset-gravatar-list-count' );
        echo '<ul class="ta-preset ta-gravatar-list-count ' . $instance['custom_id'] . '">';
        foreach( $users as $user ) {
            global $author;
            $template = "<li><a href='%posts_url%'>%gravatar_32% %displayname%</a> (%post_count%)</li>";
            $author = get_userdata( $user->post_author );
            $output = $this->replace_all_tags( $template, $user->post_count );
            echo $output;
        }
        echo '</ul>';
    }

    /**
     * Display Gravatar And Name Preset
     *
     * Displays the "Gravatar And Name" template that can be selected
     * in the widget
     *
     * @global object $author The author's object
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function display_gravatar_name( $users, $instance ) {
        wp_enqueue_style( 'ta-preset-gravatar-name' );
        echo '<ul class="ta-preset ta-gravatar-name ' . $instance['custom_id'] . '">';
        foreach( $users as $user ) {
            global $author;
            $template = "<li><a href='%posts_url%'>%gravatar_42% %displayname%</a></li>";
            $author = get_userdata( $user->post_author );
            $output = $this->replace_all_tags( $template, $user->post_count );
            echo $output;
        }
        echo '</ul>';
    }

    /**
     * Display Gravatars Preset
     *
     * Displays the "Gravatars" template that can be selected
     * in the widget
     *
     * @global object $author The author's object
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function display_gravatars( $users, $instance ) {
        wp_enqueue_style( 'ta-preset-gravatars' );
        echo '<ul class="ta-preset ta-gravatars ' . $instance['custom_id'] . '">';
        foreach( $users as $user ) {
            global $author;
            $template = "<li><a href='%posts_url%'>%gravatar_60%</a></li>";
            $author = get_userdata( $user->post_author );
            $output = $this->replace_all_tags( $template, $user->post_count );
            echo $output;
        }
        echo '</ul>';
    }

    /**
     * Replace Gravatar
     *
     * Replaces Gravatar placeholders that include a size, like %gravatar_50%
     * with the actual gravatar. This is a callback function for a
     * preg_replace_callback function
     *
     * @param array $matches Regex match array
     * @global object $author The author's object
     * @return string The replacement string for the found match
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function replace_gravatar( $matches ) {
        global $author;
        return get_avatar( $author->ID, $matches[1] );
    }

    /**
     * Replace Meta
     *
     * Replaces meta placeholders, like %meta_twitter% with the actual
     * postmeta field. This is a callback function for a preg_replace_callback
     * function
     *
     * @param array $matches Regex match array
     * @global object $author The author's object
     * @return string The replacement string for the found match
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function replace_meta( $matches ) {
        global $author;
        return get_user_meta( $author->ID, $matches[1], true );
    }

    /**
     * Get Usable Roles
     *
     * Gets roles that have permissions to edit posts
     *
     * @global object $wp_roles WordPress roles object
     * @return array Usable roles
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function get_usable_roles() {
        global $wp_roles;
        $usable_roles = array();
        foreach( $wp_roles->roles as $slug => $data ) {
            if( array_key_exists( 'edit_posts', $data['capabilities'] ) ) {
                $usable_roles[$slug] = $data['name'];
            }
        }

        $usable_roles = apply_filters( 'ta/usable_roles', $usable_roles );

        return $usable_roles;

    }

    /**
     * Get Usable Post Types
     *
     * Gets public post types that have proper authors
     *
     * @return array Usable post types
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function get_usable_post_types() {
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        unset( $post_types['attachment'] );
        $post_types = apply_filters( 'ta/usable_post_types', $post_types );

        return $post_types;
    }

}


?>
