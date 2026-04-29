<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access.' );
}

/**
 * Quickstart helper class for MetaSlider
 * 
 * @since 3.106
 */
class MetaSliderQuickstart
{
    /**
     * Get quickstart options
     * 
     * @return array
     */
    public function quickstart_options()
    {
        $data = array(
            array(
                'slug' => 'nature-site',
                'label' => esc_html__( 'Nature Site', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ), 
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/nature-site/'
            ),
            array(
                'slug' => 'travel-itinerary',
                'label' => esc_html__( 'Travel Itinerary', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ), 
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/travel-itinerary/'
            ),
            array(
                'slug' => 'city-guide',
                'label' => esc_html__( 'City Guide', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ), 
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/city-guide/'
            ),
            array(
                'slug' => 'black-and-white-photography',
                'label' => esc_html__( 'Black and White Photography', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ), 
                'features' => array( 'boxed' ),
                'animation' => array( 'zooming', 'ken-burns' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/black-and-white-photography/'
            ),
            array(
                'slug' => 'productivity-guide',
                'label' => esc_html__( 'Productivity Guide', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ), 
                'features' => array(),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/productivity-guide/'
            ),
            array(
                'slug' => 'real-estate-listings',
                'label' => esc_html__( 'Real Estate Listings', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ), 
                'features' => array( 'boxed' ),
                'animation' => array( 'fade' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/real-estate-listings/'
            ),
            array(
                'slug' => 'landscape-photography',
                'label' => esc_html__( 'Landscape Photography', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'features' => array( 'full-width' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/landscape-photography/'
            ),
            array(
                'slug' => 'client-logos',
                'label' => esc_html__( 'Client Logos', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'features' => array( 'carousel' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/client-logos/'
            ),
            array(
                'slug' => 'wildlife-photography',
                'label' => esc_html__( 'Wildlife Photography', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'features' => array( 'boxed' ),
                'animation' => array( 'fade' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/wildlife-photography/'
            ),
            array(
                'slug' => 'image',
                'label' => esc_html__( 'Images', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/image/'
            ),
            array(
                'slug' => 'carousel',
                'label' => esc_html__( 'Image Carousel', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ), 
                'features' => array( 'carousel' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/carousel-slideshow/'
            ),
            array(
                'slug' => 'withcaption',
                'label' => esc_html__( 'Image Carousel with Captions', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ),
                'features' => array( 'carousel' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/carousel-with-captions/'
            ),
            array(
                'slug' => 'creative-process',
                'label' => esc_html__( 'Creative Process', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ), 
                'features' => array( 'boxed' ),
                'animation' => array( 'slide', 'vertical' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/creative-process/'
            ),
            array(
                'slug' => 'architecture',
                'label' => esc_html__( 'Architecture', 'ml-slider' ),
                'is_dummy' => false, // Is Free
                'price' => 'free',
                'type' => array( 'image' ), 
                'features' => array( 'boxed' ),
                'animation' => array( 'fade', 'ken-burns' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/architecture/'
            ),
            array(
                'slug' => 'marathon-journey',
                'label' => esc_html__( 'Marathon Journey', 'ml-slider' ),
                'type' => array( 'local_video' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed', 'video-caption' ),
                'animation' => array( 'fade' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/marathon-journey/'
            ),
            array(
                'slug' => 'cars-display',
                'label' => esc_html__( 'Cars Display', 'ml-slider' ),
                'type' => array( 'html_overlay' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed', 'thumbnail-nav' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/cars-display/'
            ),
            array(
                'slug' => 'watch-shop-hero',
                'label' => esc_html__( 'Watch Shop Hero', 'ml-slider' ),
                'type' => array( 'html_overlay' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'slide', 'vertical' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/watch-shop-hero/'
            ),
            array(
                'slug' => 'creative-agency-hero',
                'label' => esc_html__( 'Creative Agency Hero', 'ml-slider' ),
                'type' => array( 'html_overlay' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'fade' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/creative-agency-hero/'
            ),
            array(
                'slug' => 'fashion-trends',
                'label' => esc_html__( 'Fashion Trends', 'ml-slider' ),
                'type' => array( 'html_overlay' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'zooming' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/fashion-trends/'
            ),
            array(
                'slug' => 'entertainment-youtube-shorts-feed',
                'label' => esc_html__( 'Entertainment YouTube Shorts Feed', 'ml-slider' ),
                'type' => array( 'youtube' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'carousel' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/entertainment-youtube-shorts-feed/'
            ),
            array(
                'slug' => 'album-cover-showcase',
                'label' => esc_html__( 'Album Cover Showcase', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed', 'lightbox' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/album-cover-showcase/'
            ),
            array(
                'slug' => 'business-presentation',
                'label' => esc_html__( 'Business Presentation', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/business-presentation/'
            ),
            array(
                'slug' => 'books-by-genre',
                'label' => esc_html__( 'Books by Genre', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'carousel' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/books-by-genre/'
            ),
            array(
                'slug' => 'another-testimonial',
                'label' => esc_html__( 'Testimonials', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/another-testimonial/'
            ),
            array(
                'slug' => 'skincare-routine-guide',
                'label' => esc_html__( 'Skincare Routine Guide', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/skincare-routine-guide/'
            ),
            array(
                'slug' => 'indoor-plant-selection-guide',
                'label' => esc_html__( 'Indoor Plant Selection Guide', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/indoor-plant-selection-guide/'
            ),
            array(
                'slug' => 'tiktok-vertical-videos',
                'label' => esc_html__( 'TikTok Vertical Videos', 'ml-slider' ),
                'type' => array( 'tiktok' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'slide', 'vertical' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/tiktok-vertical-videos/'
            ),
            array(
                'slug' => 'daily-routine-videos',
                'label' => esc_html__( 'Daily Routine Videos', 'ml-slider' ),
                'type' => array( 'local_video' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'fade' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/daily-routine-videos/'
            ),
            array(
                'slug' => 'coffee-brewing-guide',
                'label' => esc_html__( 'Coffee Brewing Guide', 'ml-slider' ),
                'type' => array( 'custom_html' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'flip' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/coffee-brewing-guide/'
            ),
            array(
                'slug' => 'upcoming-city-events',
                'label' => esc_html__( 'Upcoming City Events', 'ml-slider' ),
                'type' => array( 'post_feed' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed', 'carousel' ),
                'animation' => array( 'slide' ),
                'integration' => array( 'tec', 'posts' ),
                'demo' => 'https://demo.metaslider.com/upcoming-city-events/'
            ),
            array(
                'slug' => 'product-categories',
                'label' => esc_html__( 'Product Categories', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'carousel' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/product-categories/'
            ),
            array(
                'slug' => 'recent-products',
                'label' => esc_html__( 'Recent WooCommerce Products', 'ml-slider' ),
                'type' => array( 'woocommerce' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed', 'carousel' ),
                'animation' => array( 'slide' ),
                'integration' => array( 'woocommerce' ),
                'demo' => 'https://demo.metaslider.com/recent-products/'
            ),
            array(
                'slug' => 'latest-news-posts',
                'label' => esc_html__( 'Latest News Posts', 'ml-slider' ),
                'type' => array( 'post_feed' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => array( 'posts' ),
                'demo' => 'https://demo.metaslider.com/latest-news-posts/'
            ),
            array(
                'slug' => 'product-demo',
                'label' => esc_html__( 'Product Demo', 'ml-slider' ),
                'type' => array( 'youtube' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/product-demo/'
            ),
            array(
                'slug' => 'trending-videos',
                'label' => esc_html__( 'Trending Videos', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed', 'carousel' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/trending-videos/'
            ),
            array(
                'slug' => 'hotel-rooms',
                'label' => esc_html__( 'Hotel Rooms', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/hotel-rooms/'
            ),
            array(
                'slug' => 'client-testimonials',
                'label' => esc_html__( 'Client Testimonials', 'ml-slider' ),
                'type' => array( 'layer' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed' ),
                'animation' => array( 'fade' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/client-testimonials/'
            ),
            array(
                'slug' => 'meet-the-team',
                'label' => esc_html__( 'Meet the Team', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'full-width', 'carousel' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/meet-the-team/'
            ),
            array(
                'slug' => 'curated-videos',
                'label' => esc_html__( 'Curated Videos', 'ml-slider' ),
                'type' => array( 'vimeo' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed', 'thumbnail-nav' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/curated-videos/'
            ),
            array(
                'slug' => 'natural-skincare-presentation',
                'label' => esc_html__( 'Natural Skincare Presentation', 'ml-slider' ),
                'type' => array( 'image' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'features' => array( 'boxed', 'thumbnail-nav' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/natural-skincare-presentation/'
            ),
            array(
                'slug' => 'youtube',
                'label' => __( 'YouTube', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'youtube' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/youtube-slideshow/'
            ),
            array(
                'slug' => 'youtube-shorts',
                'label' => __( 'YouTube Shorts Carousel', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'youtube' ),
                'features' => array( 'carousel', 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/youtube-shorts-carousel/'
            ),
            array(
                'slug' => 'tiktok',	
                'label' => __( 'TikTok Carousel', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'tiktok' ),
                'features' => array( 'carousel', 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/tiktok-carousel/'
            ),
            array(
                'slug' => 'vimeo',
                'label' => __( 'Vimeo', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'vimeo' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/vimeo-slideshow/'
            ),
            array(
                'slug' => 'local-video',
                'label' => __( 'Local Video', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'local_video' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/local-video-slideshow/'
            ),
            array(
                'slug' => 'layer-slides',
                'label' => __( 'Layer Slides', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'html_overlay' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'fade' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/layer-slides-slideshow/'
            ),
            array(
                'slug' => 'post-feed',
                'label' => __( 'Post Feed', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'post_feed' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => array( 'posts' ),
                'demo' => 'https://demo.metaslider.com/post-feed-slideshow/'
            ),
            array(
                'slug' => 'external',
                'label' => __( 'External Image', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'external' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/external-image/'
            ),
            array(
                'slug' => 'external-video',
                'label' => __( 'External Video', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'external_video' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/external-video-slideshow/'
            ),
            array(
                'slug' => 'custom-html',
                'label' => __( 'Custom HTML', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'custom_html' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/custom-html-slideshow/'
            ),
            array(
                'slug' => 'woocommerce',
                'label' => __( 'WooCommerce Carousel', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'woocommerce' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'slide' ),
                'integration' => array( 'woocommerce' ),
                'demo' => 'https://demo.metaslider.com/woocommerce-carousel/'
            ),
            array(
                'slug' => 'bold-motion',
                'label' => __( 'Bold Motion', 'ml-slider' ),
                'is_dummy' => true, // Is Pro
                'price' => 'pro',
                'type' => array( 'external_video' ),
                'features' => array( 'boxed' ),
                'animation' => array( 'slide', 'vertical' ),
                'integration' => false,
                'demo' => 'https://demo.metaslider.com/bold-motion/'
            )
        );

        return $data;
    }

    /**
     * Extract demo slugs with optional filtering
     * 
     * @param array $filter Filter results. e.g. only with 'price' => 'free', 'lorem' => 'ipsum', etc.
     *                      array('price' => 'free')
     *                      array('lorem' => 'ipsum')
     *                      array('price' => 'free', 'lorem' => 'ipsum')
     * 
     * @return array
     */
    public function quickstart_slugs( $filter = array() )
    {
        $options = $this->quickstart_options();

        $slugs = array_column(
            array_filter( $options, function ( $item ) use ( $filter ) {

                // No filters - return all
                if ( empty( $filter ) ) {
                    return true;
                }

                foreach ( $filter as $key => $value ) {

                    if ( ! isset( $item[ $key ] ) ) {
                        return false;
                    }

                    // Support arrays OR scalars
                    if ( is_array( $item[ $key ] ) ) {
                        if ( ! in_array( $value, $item[ $key ], true ) ) {
                            return false;
                        }
                    } else {
                        if ( $item[ $key ] !== $value ) {
                            return false;
                        }
                    }
                }

                return true;
            }),
            'slug'
        );

        return $slugs;
    }

    /**
     * Get slide type translatable label
     * 
     * @since 3.107.0
     * 
     * @param string $type Slide type. e.g. 'external', 'image', etc.
     * 
     * @return string
     */
    public function slide_type_label( $type )
    {
        switch ( $type ) {
            default:
            case 'image':
                return __( 'Image', 'ml-slider' );
                break;
            case 'vimeo':
                return __( 'Vimeo', 'ml-slider' );
                break;
            case 'youtube':
                return __( 'YouTube', 'ml-slider' );
                break;
            case 'local_video':
                return __( 'Local Video', 'ml-slider' );
                break;
            case 'external':
                return __( 'External Image', 'ml-slider' );
                break;
            case 'external_video':
                return __( 'External Video', 'ml-slider' );
                break;
            case 'tiktok':
                return __( 'TikTok', 'ml-slider' );
                break;
            case 'custom_html':
                return __( 'Custom HTML', 'ml-slider' );
                break;
            case 'html_overlay':
                return __( 'Layer Slide', 'ml-slider' );
                break;
            case 'post_feed':
                return __( 'Post Feed', 'ml-slider' );
                break;
        }
    }
}