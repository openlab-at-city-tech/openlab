<?php
/**
 * Theme widgets.
 *
 * @package ePortfolio
 */

if (!function_exists('eportfolio_load_widgets')) :
    /**
     * Load widgets.
     *
     * @since 1.0.0
     */
    function eportfolio_load_widgets()
    {

        // Slider Post widget.
        register_widget('Eportfolio_slider_post_widget');

        // Recent Post widget.
        register_widget('Eportfolio_sidebar_widget');
     
        // Social widget.
        register_widget('Eportfolio_Social_widget');

        // Bio widget.
        register_widget('Eportfolio_Bio_Post_widget');

    }
endif;
add_action('widgets_init', 'eportfolio_load_widgets');


/*Slider Post widget*/
if (!class_exists('Eportfolio_slider_post_widget')) :

    /**
     * Slider Post widget Class.
     *
     * @since 1.0.0
     */
    class Eportfolio_slider_post_widget extends Eportfolio_Widget_Base
    {

        /**
         * Sets up a new widget instance.
         *
         * @since 1.0.0
         */
        function __construct()
        {
            $opts = array(
                'classname' => 'eportfolio_slider_post_widget',
                'description' => __('Displays post form selected category as slider in any sidebars.', 'eportfolio'),
                'customize_selective_refresh' => true,
            );
            $fields = array(
                'post_category' => array(
                    'label' => __('Select Category:', 'eportfolio'),
                    'type' => 'dropdown-taxonomies',
                    'show_option_all' => __('All Categories', 'eportfolio'),
                ),
                'post_number' => array(
                    'label' => __('Number of Posts:', 'eportfolio'),
                    'type' => 'number',
                    'default' => 5,
                    'css' => 'max-width:60px;',
                    'min' => 1,
                    'max' => 6,
                ),
            );

            parent::__construct('eportfolio-slider-post-sidebar-layout', __('ePortfolio :- Slider Post', 'eportfolio'), $opts, array(), $fields);
        }

        /**
         * Outputs the content for the current widget instance.
         *
         * @since 1.0.0
         *
         * @param array $args Display arguments.
         * @param array $instance Settings for the current widget instance.
         */
        function widget($args, $instance)
        {

            $params = $this->get_params($instance);

            echo $args['before_widget'];

            if (!empty($params['title'])) {
                echo $args['before_title'] . $params['title'] . $args['after_title'];
            }

            $qargs = array(
                'posts_per_page' => esc_attr($params['post_number']),
                'no_found_rows' => true,
            );
            if (absint($params['post_category']) > 0) {
                $qargs['category'] = absint($params['post_category']);
            }
            $all_posts = get_posts($qargs);
            ?>
            <?php global $post; 
            ?>
            <?php if (!empty($all_posts)) : ?>
            <?php $twp_rtl_class = 'false';
            if(is_rtl()){ 
                $twp_rtl_class = 'true';
            }?>
            <div class="twp-widget-slider twp-post-with-bg-image" data-slick='{"rtl": <?php echo esc_attr($twp_rtl_class); ?>}'>
            <?php foreach ($all_posts as $key => $post) : ?>
                <?php setup_postdata($post); ?>
                    <div class="twp-widget-slider-wrapper">
                        <div class="twp-gallery-post">
                            <?php if (has_post_thumbnail()) {
                                $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'medium-large' );
                                $url = $thumb['0'];
                                } else {
                                    $url = '';
                            }
                            ?>
                            <a href="<?php the_permalink(); ?>" class="post-thumbnail data-bg" data-background="<?php echo esc_url($url); ?>">
                            </a>
                            <div class="twp-desc twp-overlay-black">
                                <div class="twp-categories">
                                    <?php eportfolio_post_categories(); ?>
                                </div>
                                <h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                <div class="twp-author-meta twp-author-meta-primary">
                                    <?php eportfolio_post_date(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                    endforeach;
                ?>
            </div>

            <?php wp_reset_postdata(); ?>

        <?php endif; ?>
            <?php echo $args['after_widget'];
        }
    }
endif;


/*Recent Post widget*/
if (!class_exists('Eportfolio_sidebar_widget')) :

    /**
     * Recent/Popular widget Class.
     *
     * @since 1.0.0
     */
    class Eportfolio_sidebar_widget extends Eportfolio_Widget_Base
    {

        /**
         * Sets up a new widget instance.
         *
         * @since 1.0.0
         */
        function __construct()
        {
            $opts = array(
                'classname' => 'eportfolio_popular_post_widget',
                'description' => __('Displays post form selected category specific for popular post in sidebars.', 'eportfolio'),
                'customize_selective_refresh' => true,
            );
            $fields = array(
                'title' => array(
                    'label' => __('Title:', 'eportfolio'),
                    'type' => 'text',
                    'class' => 'widefat',
                ),
                'post_category' => array(
                    'label' => __('Select Category:', 'eportfolio'),
                    'type' => 'dropdown-taxonomies',
                    'show_option_all' => __('All Categories', 'eportfolio'),
                ),
                'post_number' => array(
                    'label' => __('Number of Posts:', 'eportfolio'),
                    'type' => 'number',
                    'default' => 5,
                    'css' => 'max-width:60px;',
                    'min' => 1,
                    'max' => 6,
                ),
            );

            parent::__construct('eportfolio-popular-sidebar-layout', __('ePortfolio :- Recent Post', 'eportfolio'), $opts, array(), $fields);
        }

        /**
         * Outputs the content for the current widget instance.
         *
         * @since 1.0.0
         *
         * @param array $args Display arguments.
         * @param array $instance Settings for the current widget instance.
         */
        function widget($args, $instance)
        {

            $params = $this->get_params($instance);

            echo $args['before_widget'];

            if (!empty($params['title'])) {
                echo $args['before_title'] . $params['title'] . $args['after_title'];
            }

            $qargs = array(
                'posts_per_page' => esc_attr($params['post_number']),
                'no_found_rows' => true,
            );
            if (absint($params['post_category']) > 0) {
                $qargs['category'] = absint($params['post_category']);
            }
            $all_posts = get_posts($qargs);
            ?>
            <?php global $post; 
            ?>
            <?php if (!empty($all_posts)) : ?>
            <ul class="twp-list-post-list">
            <?php foreach ($all_posts as $key => $post) : ?>
                <?php setup_postdata($post); ?>
                    <li class="twp-list-post twp-d-flex">
                        <div class="twp-image-section">
                            <?php if (has_post_thumbnail()) {
                                $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'medium' );
                                $url = $thumb['0'];
                                } else {
                                    $url = '';
                            }
                            ?>
                            <a  href="<?php the_permalink(); ?>" class="data-bg twp-image-hover" data-background="<?php echo esc_url($url); ?>">
                            </a>
                        </div>
                        <div class="twp-desc">
                            <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                            <div class="twp-author-meta twp-author-meta-primary">
                                <?php eportfolio_post_date(); ?>
                            </div>
                        </div>
                    </li>
                <?php 
                    endforeach;
                ?>
            </ul>

            <?php wp_reset_postdata(); ?>

        <?php endif; ?>
            <?php echo $args['after_widget'];
        }
    }
endif;


/*Social widget*/
if (!class_exists('Eportfolio_Social_widget')) :

    /**
     * Social widget Class.
     *
     * @since 1.0.0
     */
    class Eportfolio_Social_widget extends Eportfolio_Widget_Base
    {

        /**
         * Sets up a new widget instance.
         *
         * @since 1.0.0
         */
        function __construct()
        {
            $opts = array(
                'classname' => 'eportfolio_social_widget',
                'description' => __('Displays Social share.', 'eportfolio'),
                'customize_selective_refresh' => true,
            );
            $fields = array(
                'title' => array(
                    'label' => __('Title:', 'eportfolio'),
                    'type' => 'text',
                    'class' => 'widefat',
                ),
            );

            parent::__construct('eportfolio-social-layout', __('ePortfolio :- Social Widget', 'eportfolio'), $opts, array(), $fields);
        }

        /**
         * Outputs the content for the current widget instance.
         *
         * @since 1.0.0
         *
         * @param array $args Display arguments.
         * @param array $instance Settings for the current widget instance.
         */
        function widget($args, $instance)
        {

            $params = $this->get_params($instance);

            echo $args['before_widget'];

            if ( ! empty( $params['title'] ) ) {
                echo $args['before_title'] . $params['title'] . $args['after_title'];
            } ?>

            <div class="twp-social-widget-section">
                <?php
                    wp_nav_menu(
                        array('theme_location' => 'social-nav',
                            'link_before' => '<span>',
                            'link_after' => '</span>',
                            'menu_id' => 'social-menu',
                            'fallback_cb' => false,
                            'menu_class' => 'twp-social-icons-rounded twp-social-widget'
                        )); ?>
                <?php if ( ! has_nav_menu( 'social-nav' ) ) : ?>
                    <p>
                        <?php esc_html_e( 'Social menu is not set. You need to create menu and assign it to Social Menu on Menu Settings.', 'eportfolio' ); ?>
                    </p>
                <?php endif; ?>
            </div>
            <?php echo $args['after_widget'];
        }
    }
endif;



/*Bio widget*/
if (!class_exists('Eportfolio_Bio_Post_widget')) :

    /**
     * Bio widget Class.
     *
     * @since 1.0.0
     */
    class Eportfolio_Bio_Post_widget extends Eportfolio_Widget_Base
    {

        /**
         * Sets up a new widget instance.
         *
         * @since 1.0.0
         */
        function __construct()
        {
            $opts = array(
                'classname' => 'eportfolio_bio_widget',
                'description' => __('Displays bio details in post.', 'eportfolio'),
                'customize_selective_refresh' => true,
            );
            $fields = array(
                'title' => array(
                    'label' => __('Title:', 'eportfolio'),
                    'type' => 'text',
                    'class' => 'widefat',
                ),
                'bio-name' => array(
                    'label' => __('Name:', 'eportfolio'),
                    'type' => 'text',
                    'class' => 'widefat',
                ),
                'bio-sub-title' => array(
                    'label' => __('Position/Sub Title:', 'eportfolio'),
                    'type' => 'text',
                    'class' => 'widefat',
                ),
                'quote' => array(
                    'label' => __('Quotation:', 'eportfolio'),
                    'type'  => 'textarea',
                    'class' => 'widget-content widefat'
                ),
                'image_url' => array(
                    'label' => __('Bio Image:', 'eportfolio'),
                    'type'  => 'image',
                ),
                'url-fb' => array(
                   'label' => __('Facebook URL:', 'eportfolio'),
                   'type' => 'url',
                   'class' => 'widefat',
                    ),
                'url-tw' => array(
                   'label' => __('Twitter URL:', 'eportfolio'),
                   'type' => 'url',
                   'class' => 'widefat',
                    ),
                'url-lt' => array(
                   'label' => __('Linkedin URL:', 'eportfolio'),
                   'type' => 'url',
                   'class' => 'widefat',
                    ),
                'url-ig' => array(
                   'label' => __('Instagram URL:', 'eportfolio'),
                   'type' => 'url',
                   'class' => 'widefat',
                    ),
            );

            parent::__construct('eportfolio-bio-layout', __('ePortfolio :- Bio Widget', 'eportfolio'), $opts, array(), $fields);
        }

        /**
         * Outputs the content for the current widget instance.
         *
         * @since 1.0.0
         *
         * @param array $args Display arguments.
         * @param array $instance Settings for the current widget instance.
         */
        function widget($args, $instance)
        {

            $params = $this->get_params($instance);

            echo $args['before_widget'];

            if ( ! empty( $params['title'] ) ) {
                echo $args['before_title'] . $params['title'] . $args['after_title'];
            } ?>

            <!--cut from here-->
            <div class="twp-bio-widget">
                <div class="twp-basic-info twp-d-flex">
                    <?php if ( ! empty( $params['image_url'] ) ) { ?>
                        <div class="twp-image-section">
                            <div class="twp-wrapper data-bg twp-image-hover twp-overlay-image-hover" data-background="<?php echo esc_url( $params['image_url'] ); ?>">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="twp-title-with-social-icon">
                        <?php if ( ! empty( $params['bio-name'] ) ) { ?>
                            <h2><?php echo esc_html($params['bio-name'] );?></h2>
                        <?php } ?>

                        <?php if ( ! empty( $params['bio-sub-title'] ) ) { ?>
                            <h5><?php echo esc_html($params['bio-sub-title'] );?></h5>
                        <?php } ?>
                        
                        <div class="twp-bio-social-widget">
                            <?php if ( ! empty( $params['url-fb'] ) ) { ?>
                                <span><a href="<?php echo esc_url($params['url-fb']); ?>"><i class="fa fa-facebook"></i></a></span></span>
                            <?php } ?>
                            <?php if ( ! empty( $params['url-tw'] ) ) { ?>
                                <span><a href="<?php echo esc_url($params['url-tw']); ?>"><i class=" fa fa-twitter"></i></a></span>
                            <?php } ?>
                            <?php if ( ! empty( $params['url-lt'] ) ) { ?>
                                <span><a href="<?php echo esc_url($params['url-lt']); ?>"><i class=" fa fa-linkedin"></i></a></span>
                            <?php } ?>
                            <?php if ( ! empty( $params['url-ig'] ) ) { ?>
                                <span><a href="<?php echo esc_url($params['url-ig']); ?>"><i class=" fa fa-instagram"></i></a></span>
                            <?php } ?>
                        </div>
                    </div>
                </div><!--/twp-basic-info-->


                <?php if ( ! empty( $params['quote'] ) ) { ?>
                    <div class="twp-quote">
                        <p><?php echo wp_kses_post( $params['quote']); ?></p>
                    </div>
                <?php } ?>
               
            </div>
            <?php echo $args['after_widget'];
        }
    }
endif;
