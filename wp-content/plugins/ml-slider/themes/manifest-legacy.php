<?php

/**
 * A list of themes. For now they will be sorted outside this file,
 * but as the list grows we might want to start organizing this list
 * The type should be free, premium, or bonus
 */
return array(
    'default-base' => array(
        'folder' => 'default-base',
        'title' => 'Base',
        'type' => 'free',
        'supports' => array('flex'),
        'tags' => array(),
        'description' => __('This is the default MetaSlider theme.', 'ml-slider')
    ),
    'bitono' => array(
        'folder' => 'bitono',
        'title' => 'Bitono',
        'type' => 'free',
        'supports' => array( 'flex' ),
        'tags' => array(),
        'description' => __('Bitono is a minimalist theme with a 2-color scheme. Recommended for Image, External Image and Post feed slides.', 'ml-slider')
    ),
    'clarity' => array(
        'folder' => 'clarity',
        'title' => 'Clarity',
        'type' => 'free',
        'supports' => array( 'flex' ),
        'tags' => array(),
        'description' => __('Clarity is focused on accessibility. It has easy-to-read fonts, and a straightforward, distraction-free interface.', 'ml-slider')
    ),
    'databold' => array(
        'folder' => 'databold',
        'title' => 'Databold',
        'type' => 'free',
        'supports' => array( 'flex' ),
        'tags' => array(),
        'description' => __('Databold is a modern, business theme with lots of room for your captions. Databold is recommended for Image, External Image, and Post Feed slides.', 'ml-slider')
    ),
    'draxler' => array(
        'folder' => 'draxler',
        'title' => 'Draxler',
        'type' => 'free',
        'supports' => array( 'flex' ),
        'tags' => array(),
        'description' => __('Draxler is a stylish theme that places the navigation arrows in the top-right corner. There\'s also plenty of room for your captions.', 'ml-slider')
    ),
    'nexus' => array(
        'folder' => 'nexus',
        'title' => 'Nexus',
        'type' => 'free',
        'supports' => array( 'flex' ),
        'tags' => array(),
        'description' => __('Featuring a built-in button with custom link, Nexus seamlessly integrates key points and interactive elements, making your presentation both engaging and navigable.', 'ml-slider')
    ),
    'cubic' => array(
        'folder' => 'cubic',
        'title' => 'Cubic',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo', 'coin'),
        'tags' => array('light', 'square', 'bold', 'flat'),
        'description' => __('A standard slideshow layout with a modern design and large, clear arrows.', 'ml-slider'),
        'images' => array('andre-benz-631450-unsplash.jpg', 'etienne-beauregard-riverin-48305-unsplash.jpg', 'wabi-jayme-578762-unsplash.jpg', 'dorigo-wu-14676-unsplash.jpg', 'olav-ahrens-rotne-1087667-unsplash.jpg')
    ),
    'outline' => array(
        'folder' => 'outline',
        'title' => 'Outline',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo', 'coin'),
        'tags' => array('light', 'lines', 'bold', 'square'),
        'description' => __('A clean, subtle theme that features block arrows and bold design.', 'ml-slider'),
        'images' => array('wabi-jayme-578762-unsplash.jpg', 'nick-cooper-731773-unsplash.jpg', 'olav-ahrens-rotne-1087667-unsplash.jpg', 'muhammad-rizki-1094746-unsplash.jpg', 'dorigo-wu-14676-unsplash.jpg')
    ),
    'bubble' => array(
        'folder' => 'bubble',
        'title' => 'Bubble',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo', 'coin'),
        'tags' => array('light', 'bold', 'round'),
        'description' => __('A fun, circular design to brighten up your site. This theme works well with dark images', 'ml-slider'),
        'images' => array('timothy-eberly-728185-unsplash.jpg', 'wabi-jayme-578762-unsplash.jpg', 'ella-olsson-1094090-unsplash.jpg', 'fabio-mangione-236846-unsplash.jpg', 'victoria-shes-1096105-unsplash.jpg')
    ),
    'simply-dark' => array(
        'folder' => 'simply-dark',
        'title' => 'Simply Dark',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo', 'coin'),
        'tags' => array('dark', 'minimalist'),
        'description' => __('A smart, contemporary design that is built to blend seamlessly into any theme.', 'ml-slider'),
        'images' => array(
            array(
                'filename' => 'etienne-beauregard-riverin-48305-unsplash.jpg',
                // 'caption' => 'Here is an example of a slide with a caption.',
                // 'title' => 'About Us',
                // 'alt' => 'A photo of our office',
                // 'description' => 'A description is also possible'
            ),
            array(
                'filename' => 'danny-howe-361436-unsplash.jpg',
                // 'caption' => '<h2>Captions can have<br><span style="font-size:130%">HTML</span></h2>.'
            ),
            array(
                'filename' => 'norbert-levajsics-203627-unsplash.jpg',
                // 'caption' => ''
            ),
            array(
                'filename' => 'manki-kim-269196-unsplash.jpg',
            ),
            array(
                'filename' => 'danny-howe-361436-unsplash.jpg'
            )
        ),
        'instructions' => 'Optionally you can add some special instructions for the user to follow. You can also use <strong>HTML</strong>'
    ),
    'jenga' => array(
        'folder' => 'jenga',
        'title' => 'Jenga',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo', 'coin'),
        'tags' => array('light', 'minimalist'),
        'description' => __('This theme places the controls vertically for a unique look.', 'ml-slider'),
        'images' => array('michael-discenza-unsplash.jpg', 'etienne-beauregard-riverin-48305-unsplash.jpg', 'wabi-jayme-578762-unsplash.jpg', 'dorigo-wu-14676-unsplash.jpg', 'nick-cooper-731773-unsplash.jpg')
    ),
    'disjoint' => array(
        'folder' => 'disjoint',
        'title' => 'Disjoint',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo', 'coin'),
        'tags' => array('light', 'bold', 'square'),
        'description' => __('A futuristic and linear design that has a unique, horizontal navigation.', 'ml-slider'),
        'images' => array('artem-bali-680991-unsplash.jpg', 'manki-kim-269196-unsplash.jpg', 'danny-howe-361436-unsplash.jpg', 'victoria-shes-1096105-unsplash.jpg', 'ella-olsson-1094090-unsplash.jpg')
    ),
    'blend' => array(
        'folder' => 'blend',
        'title' => 'Blend',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo', 'coin'),
        'tags' => array('light', 'minimalist', 'lines'),
        'description' => __('This lightweight design uses numbers for the navigation. The navigation, caption, and arrows are share the same line.', 'ml-slider'),
        'images' => array('manki-kim-269196-unsplash.jpg', 'dorigo-wu-14676-unsplash.jpg', 'artem-bali-680991-unsplash.jpg', 'fabio-mangione-236846-unsplash.jpg', 'olav-ahrens-rotne-1087667-unsplash.jpg')
    ),
    'precognition' => array(
        'folder' => 'precognition',
        'title' => 'Precognition',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo', 'coin'),
        'tags' => array('light', 'special'),
        'description' => __('This theme has a special additional functionality that uses image titles as the slide navigation. ', 'ml-slider'),
        'instructions' => __('If you would like to use the image titles as the navigation, you can do so by editing the image title in the media library or manually on the slide (SEO tab).', 'ml-slider'),
        'images' => array(
            array(
                'filename' => 'norbert-levajsics-203627-unsplash.jpg',
                'title' => 'Image by Norbert Levajsics'
            ),
            array(
                'filename' => 'danny-howe-361436-unsplash.jpg',
                'title' => 'Image by Danny Howe'
            ),
            array(
                'filename' => 'manki-kim-269196-unsplash.jpg',
                'title' => 'Image by Manki Kim'
            ),
            array(
                'filename' => 'yoann-siloine-532511-unsplash.jpg',
                'title' => 'Image by Yoann Siloine'
            ),
            array(
                'filename' => 'erol-ahmed-305920-unsplash.jpg',
                'title' => 'Image by Erol Ahmed'
            ),
        )
    ),
    'radix' => array(
        'folder' => 'radix',
        'title' => 'Radix',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo', 'coin'),
        'tags' => array('light', 'special', 'square'),
        'description' => __('This theme has a unique design that gives it a sophisticated look.', 'ml-slider'),
        'images' => array('margo-brodowicz-183156-unsplash.jpg', 'manki-kim-269196-unsplash.jpg', 'artem-bali-680991-unsplash.jpg', 'ella-olsson-1094090-unsplash.jpg', 'muhammad-rizki-1094746-unsplash.jpg')
    ),
    'highway' => array(
        'folder' => 'highway',
        'title' => 'Highway',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo', 'coin'),
        'tags' => array('light', 'bold', 'square', 'rounded'),
        'description' => __('A bold and clear design that works well on a darker images.', 'ml-slider'),
        'images' => array('nick-cooper-731773-unsplash.jpg', 'victoria-shes-1096105-unsplash.jpg', 'tim-peterson-1099515-unsplash.jpg', 'ella-olsson-1094090-unsplash.jpg', 'olav-ahrens-rotne-1087667-unsplash.jpg')
    ),
    'architekt' => array(
        'folder' => 'architekt',
        'title' => 'Architekt',
        'type' => 'free',
        'supports' => array('flex', 'responsive', 'nivo'),
        'tags' => array('light', 'minimalist'),
        'description' => __('A minimalist theme that gets out of the way so you can showcasing your beautiful pictures. Best used with Image Slides.', 'ml-slider'),
        'images' => array('danny-howe-361436-unsplash.jpg', 'etienne-beauregard-riverin-48305-unsplash.jpg', 'luca-bravo-198062-unsplash.jpg', 'fabio-mangione-236846-unsplash.jpg', 'olav-ahrens-rotne-1087667-unsplash.jpg')
    ),
    'nivo-light' => array(
        'folder' => 'nivo-light',
        'title' => 'Nivo Light',
        'type' => 'free',
        'supports' => array('nivo'),
        'tags' => array('nivo only'),
        'description' => __('The Nivo Light theme included here for legacy purposes. Note: only works with Nivo Slider', 'ml-slider'),
    ),
    'nivo-bar' => array(
        'folder' => 'nivo-bar',
        'title' => 'Nivo Bar',
        'type' => 'free',
        'supports' => array('nivo'),
        'tags' => array('nivo only'),
        'description' => __('The Nivo Bar theme included here for legacy purposes. Note: only works with Nivo Slider', 'ml-slider'),
    ),
    'nivo-dark' => array(
        'folder' => 'nivo-dark',
        'title' => 'Nivo Dark',
        'type' => 'free',
        'supports' => array('nivo'),
        'tags' => array('nivo only'),
        'description' => __('The Nivo Dark theme included here for legacy purposes. Note: only works with Nivo Slider', 'ml-slider'),
    )
);
