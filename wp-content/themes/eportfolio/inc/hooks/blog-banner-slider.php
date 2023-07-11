<?php
if (!function_exists('eportfolio_blog_banner_slider')) :
    /**
     * Blog Banner Section
     *
     * @since ePortfolio 1.0.0
     *
     * 
     * 
     */
    function eportfolio_blog_banner_slider()
    {
        ?>
        <?php if (1 == eportfolio_get_option('show_slider_on_blog')) { ?>
            <div class="twp-blog-slider-section">

                <?php 
                $eportfolio_select_category_for_banner_section = esc_attr(eportfolio_get_option('select_category_for_blog_slider'));
                $eportfolio_number_of_home_banner_section = absint(eportfolio_get_option('blog_page_slider_number'));
                $eportfolio_blog_banner_slider_args = array(
                    'post_type' => 'post',
                    'cat' => absint($eportfolio_select_category_for_banner_section),
                    'ignore_sticky_posts' => true,
                    'posts_per_page' => absint( $eportfolio_number_of_home_banner_section ),
                ); ?>
                    <?php $twp_rtl_class = 'false';
                    if(is_rtl()){ 
                        $twp_rtl_class = 'true';
                    }?>
                        <div class="twp-blog-slider"  data-slick='{"rtl": <?php echo esc_attr($twp_rtl_class); ?>}'>
                            <?php 
                            $eportfolio_blog_banner_slider_post_query = new WP_Query($eportfolio_blog_banner_slider_args);
                            if ($eportfolio_blog_banner_slider_post_query->have_posts()) :
                            while ($eportfolio_blog_banner_slider_post_query->have_posts()) : $eportfolio_blog_banner_slider_post_query->the_post();
                                if(has_post_thumbnail()){
                                    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'medium-large' );
                                    $url = $thumb['0'];
                                }
                                else{
                                    $url = '';
                                }
                                ?>
                                <div class="twp-wrapper">
                                    <div class="twp-blog-post">
                                        <div class="twp-image-section data-bg" data-background="<?php echo esc_url($url); ?>">
                                        </div>
                                        <div class="twp-desc">
                                            <h3 class="twp-section-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                            <div class="twp-categories">
                                                <?php eportfolio_post_categories(); ?>
                                            </div>
                                            <?php the_excerpt(); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile;
                            endif; 
                            wp_reset_postdata(); ?>

                        </div>
            </div>
        <?php } ?>
        
        <?php
    }   
endif;
add_action('eportfolio_action_blog_banner_slider', 'eportfolio_blog_banner_slider', 10);
