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
        'instructions' => __( 'This is the Beta version of the Hero Theme and currently it must be full-width. It should also be used with Carousel Mode disabled.', 'ml-slider' )
    )
);
