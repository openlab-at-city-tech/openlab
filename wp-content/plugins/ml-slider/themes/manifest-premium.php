<?php

/**
 * These themes type 'premium' are listed 
 * but not allowed to be selected when MetaSlider Pro is not installed and active.
 */
return array(
    'retsu' => array(
        'folder' => 'retsu',
        'title' => 'Retsu',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'tags' => array( 
            __( 'minimalist', 'ml-slider' ), 
            __(  '2 columns', 'ml-slider' )
        ),
        'description' => __( 'A 2 columns minimalistic theme to split your images and captions.', 'ml-slider' ),
        'instructions' => __( 'Image, External URL and Post Feed slides are displayed in 2 columns, while the others slide types are displayed in 1 column.', 'ml-slider' )
    ),
    'social-play' => array(
        'folder' => 'social-play',
        'title' => 'Social Play',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'tags' => array( 
            __( 'minimalist', 'ml-slider' ), 
            __(  'videos', 'ml-slider' ),
            __(  'vertical', 'ml-slider' )
        ),
        'description' => __( 'A theme to showcase vertical images and videos.', 'ml-slider' ),
        'instructions' => __( 'Ideal for Images, Post Feed slides, YouTube and Vimeo vertical videos.', 'ml-slider' )
    ),
    'hero' => array(
        'folder' => 'hero',
        'title' => 'Hero',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'description' => __( 'A dynamic slideshow theme that emphasizes the active slide by scaling it larger than the surrounding slides, creating a hero-like focal point that draws the viewer\'s attention.', 'ml-slider' ),
        'instructions' => __( 'It should be used with 100% Width and Carousel Mode disabled, Transition Effect set to Slide and Center Align enabled. Currently works on all slide types except External Image and Tiktok Videos.', 'ml-slider' )
    ),
    'revelio' => array(
        'folder' => 'revelio',
        'title' => 'Revelio',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'description' => __( 'A slideshow theme that unveils captions on hover, adding a dynamic touch to portfolios and showcases.', 'ml-slider' ),
        'instructions' => __( 'Best used on slideshows with captions.', 'ml-slider' )
    ),
    'visage' => array(
        'folder' => 'visage',
        'title' => 'Visage',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'description' => __( 'A modern slideshow theme with captions positioned at the top, showcasing features with sleek elegance.', 'ml-slider' ),
        'instructions' => __( 'When using this theme with Carousel Mode, adjust the Carousel Margin setting to create space between the slides.', 'ml-slider' ),
    ),
    'focus' => array(
        'folder' => 'focus',
        'title' => 'Focus',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'description' => __( 'A dynamic slideshow theme that emphasizes the active slide by using grayscale and opacity for the surrounding slides, creating a focal point that draws the viewer\'s attention.', 'ml-slider' ),
        'instructions' => __( 'It should be used with 100% Width and Carousel Mode disabled, Transition Effect set to Slide and Center Align enabled. Currently works on all slide types except External Image and Tiktok Videos.', 'ml-slider' )
    ),
    'praise-loop' => array(
        'folder' => 'praise-loop',
        'title' => 'Praise Loop',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'description' => __( 'A dynamic slideshow theme to display testimonials that emphasizes the active slide by using grayscale for the surrounding slides, creating a focal point that draws the viewer\'s attention.', 'ml-slider' ),
        'instructions' => __( 'Best used with Image slides. It should also be used with Hidden Arrows, Carousel Mode disabled, Transition Effect set to Slide, Smart Crop enabled and Crop Source set to "Custom width/height".', 'ml-slider' ),
    ),
    'parallel' => array(
        'folder' => 'parallel',
        'title' => 'Parallel',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'description' => __( 'A 2 columns minimalistic theme to split your images and captions.', 'ml-slider' ),
        'instructions' => __( 'Image, External Image, Post Feed, Local Video and External Video slides are displayed in 2 columns, while the other slide types are displayed in 1 column.', 'ml-slider' ),
    ),
    'tandem' => array(
        'folder' => 'tandem',
        'title' => 'Tandem',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'description' => __( 'A 2 columns minimalistic theme to split your images and captions.', 'ml-slider' ),
        'instructions' => __( 'Image, External Image, Post Feed, Local Video and External Video slides are displayed in 2 columns, while the other slide types are displayed in 1 column.', 'ml-slider' ),
    ),
    'zonora' => array(
        'folder' => 'zonora',
        'title' => 'Zonora',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'description' => __( 'A 2 rows modern theme to split your images and captions ideal for WooCommerce products.', 'ml-slider' )
    ),
    'handimart' => array(
        'folder' => 'handimart',
        'title' => 'Handimart',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'description' => __( 'A stylish theme ideal for WooCommerce products.', 'ml-slider' )
    ),
    'nami' => array(
        'folder' => 'nami',
        'title' => 'Nami',
        'type' => 'premium',
        'supports' => array( 'flex' ),
        'description' => __( 'A 2 columns minimalistic theme to split your images and captions.', 'ml-slider' ),
        'instructions' => __( 'Image, External Image, Post Feed, Local Video and External Video slides are displayed in 2 columns, while the other slide types are displayed in 1 column.', 'ml-slider' ),
    )
);
