<?php
/**
 * Register all PublishPress Blocks via PHP for WordPress.org detection
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Accordion block
 */
function advgbRegisterBlockAccordion() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/accordions', array(
        'attributes' => array(
            'accordionItems' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),
    ));
}

/**
 * Register Button block
 */
function advgbRegisterBlockButton() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/button', array(
        'attributes' => array(
            'text' => array(
                'type' => 'string',
                'default' => 'Button',
            ),
            'url' => array(
                'type' => 'string',
                'default' => '#',
            ),
            'newTab' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
            'align' => array(
                'type' => 'string',
                'default' => 'left',
            ),
        ),

    ));
}

/**
 * Register Columns block
 */
function advgbRegisterBlockColumns() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/columns', array(
        'attributes' => array(
            'columns' => array(
                'type' => 'number',
                'default' => 2,
            ),
            'columnsT' => array(
                'type' => 'number',
                'default' => 2,
            ),
            'columnsM' => array(
                'type' => 'number',
                'default' => 1,
            ),
            'gap' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),

    ));
}

/**
 * Register Contact Form block
 */
function advgbRegisterBlockContactForm() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/contact-form', array(
        'attributes' => array(
            'toEmail' => array(
                'type' => 'string',
                'default' => '',
            ),
            'subject' => array(
                'type' => 'string',
                'default' => '',
            ),
            'fields' => array(
                'type' => 'array',
                'default' => array(),
            ),
        ),

    ));
}

/**
 * Register Count Up block
 */
function advgbRegisterBlockCountUp() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/count-up', array(
        'attributes' => array(
            'startNumber' => array(
                'type' => 'number',
                'default' => 0,
            ),
            'endNumber' => array(
                'type' => 'number',
                'default' => 100,
            ),
            'duration' => array(
                'type' => 'number',
                'default' => 2000,
            ),
            'prefix' => array(
                'type' => 'string',
                'default' => '',
            ),
            'suffix' => array(
                'type' => 'string',
                'default' => '',
            ),
        ),

    ));
}

/**
 * Register Image block
 */
function advgbRegisterBlockImage() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/image', array(
        'attributes' => array(
            'url' => array(
                'type' => 'string',
                'default' => '',
            ),
            'alt' => array(
                'type' => 'string',
                'default' => '',
            ),
            'id' => array(
                'type' => 'number',
                'default' => 0,
            ),
            'size' => array(
                'type' => 'string',
                'default' => 'large',
            ),
        ),

    ));
}

/**
 * Register Images Slider block
 */
function advgbRegisterBlockImagesSlider() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/images-slider', array(
        'attributes' => array(
            'images' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'autoplay' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'autoplaySpeed' => array(
                'type' => 'number',
                'default' => 3000,
            ),
        ),

    ));
}

/**
 * Register Info Box block
 */
function advgbRegisterBlockInfoBox() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/infobox', array(
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => '',
            ),
            'content' => array(
                'type' => 'string',
                'default' => '',
            ),
            'icon' => array(
                'type' => 'string',
                'default' => '',
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),

    ));
}

/**
 * Register Icon block
 */
function advgbRegisterBlockIcon() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/icon', array(
        'attributes' => array(
            'icon' => array(
                'type' => 'string',
                'default' => '',
            ),
            'iconType' => array(
                'type' => 'string',
                'default' => 'material',
            ),
            'size' => array(
                'type' => 'number',
                'default' => 24,
            ),
            'color' => array(
                'type' => 'string',
                'default' => '#000000',
            ),
        ),

    ));
}

/**
 * Register List block
 */
function advgbRegisterBlockList() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/list', array(
        'attributes' => array(
            'items' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'ordered' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),

    ));
}

/**
 * Register Login Form block
 */
function advgbRegisterBlockLoginForm() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/login-form', array(
        'attributes' => array(
            'redirectUrl' => array(
                'type' => 'string',
                'default' => '',
            ),
            'showRememberMe' => array(
                'type' => 'boolean',
                'default' => true,
            ),
            'showLostPassword' => array(
                'type' => 'boolean',
                'default' => true,
            ),
        ),

    ));
}

/**
 * Register Map block
 */
function advgbRegisterBlockMap() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/map', array(
        'attributes' => array(
            'latitude' => array(
                'type' => 'number',
                'default' => 40.7128,
            ),
            'longitude' => array(
                'type' => 'number',
                'default' => -74.0060,
            ),
            'zoom' => array(
                'type' => 'number',
                'default' => 14,
            ),
            'height' => array(
                'type' => 'string',
                'default' => '400px',
            ),
        ),

    ));
}

/**
 * Register Newsletter block
 */
function advgbRegisterBlockNewsletter() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/newsletter', array(
        'attributes' => array(
            'listId' => array(
                'type' => 'string',
                'default' => '',
            ),
            'placeholder' => array(
                'type' => 'string',
                'default' => 'Enter your email',
            ),
            'buttonText' => array(
                'type' => 'string',
                'default' => 'Subscribe',
            ),
        ),

    ));
}

/**
 * Register Search Bar block
 */
function advgbRegisterBlockSearchBar() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/search-bar', array(
        'attributes' => array(
            'placeholder' => array(
                'type' => 'string',
                'default' => 'Search...',
            ),
            'buttonText' => array(
                'type' => 'string',
                'default' => 'Search',
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),

    ));
}

/**
 * Register Social Links block
 */
function advgbRegisterBlockSocialLinks() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/social-links', array(
        'attributes' => array(
            'socials' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
            'size' => array(
                'type' => 'string',
                'default' => 'medium',
            ),
        ),

    ));
}

/**
 * Register Summary block
 */
function advgbRegisterBlockSummary() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/summary', array(
        'attributes' => array(
            'headings' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),

    ));
}

/**
 * Register Table block
 */
function advgbRegisterBlockTable() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/table', array(
        'attributes' => array(
            'headers' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'rows' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),

    ));
}

/**
 * Register Tabs block
 */
function advgbRegisterBlockTabs() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/tabs', array(
        'attributes' => array(
            'tabs' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),

    ));
}

/**
 * Register Testimonial block
 */
function advgbRegisterBlockTestimonial() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/testimonial', array(
        'attributes' => array(
            'content' => array(
                'type' => 'string',
                'default' => '',
            ),
            'author' => array(
                'type' => 'string',
                'default' => '',
            ),
            'role' => array(
                'type' => 'string',
                'default' => '',
            ),
            'image' => array(
                'type' => 'object',
                'default' => null,
            ),
        ),

    ));
}

/**
 * Register Video block
 */
function advgbRegisterBlockVideo() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/video', array(
        'attributes' => array(
            'url' => array(
                'type' => 'string',
                'default' => '',
            ),
            'poster' => array(
                'type' => 'string',
                'default' => '',
            ),
            'autoplay' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'loop' => array(
                'type' => 'boolean',
                'default' => false,
            ),
        ),

    ));
}

/**
 * Register Woo Products block
 */
function advgbRegisterBlockWooProducts() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/woo-products', array(
        'attributes' => array(
            'columns' => array(
                'type' => 'number',
                'default' => 4,
            ),
            'rows' => array(
                'type' => 'number',
                'default' => 1,
            ),
            'category' => array(
                'type' => 'string',
                'default' => '',
            ),
            'orderby' => array(
                'type' => 'string',
                'default' => 'date',
            ),
            'order' => array(
                'type' => 'string',
                'default' => 'desc',
            ),
        ),

    ));
}

/**
 * Register Adv Tabs block
 */
function advgbRegisterBlockAdvTabs() {
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/adv-tabs', array(
        'attributes' => array(
            'tabs' => array(
                'type' => 'array',


     'default' => array(),
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
            'orientation' => array(
                'type' => 'string',
                'default' => 'horizontal',
            ),
        ),

    ));
}

/**
 * Register Pro blocks (if Pro is active)
 */
function advgbRegisterProBlocks() {
    if (!defined('ADVANCED_GUTENBERG_PRO_LOADED')) {
        return;
    }

    if (!function_exists('register_block_type')) {
        return;
    }

    // Countdown block
    register_block_type('advgb/countdown', array(
        'attributes' => array(
            'date' => array(
                'type' => 'string',
                'default' => '',
            ),
            'time' => array(
                'type' => 'string',
                'default' => '00:00:00',
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),

    ));

    // Feature block
    register_block_type('advgb/feature', array(
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => '',
            ),
            'text' => array(
                'type' => 'string',
                'default' => '',
            ),
            'icon' => array(
                'type' => 'string',
                'default' => '',
            ),
        ),

    ));

    // Feature List block
    register_block_type('advgb/feature-list', array(
        'attributes' => array(
            'features' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),

    ));

    // Pricing Table block
    register_block_type('advgb/pricing-table', array(
        'attributes' => array(
            'plans' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default',
            ),
        ),

    ));
}

/**
 * Initialize all block registrations
 */
function advgbRegisterAllBlocks() {
    // Register all free blocks
    advgbRegisterBlockAccordion();
    advgbRegisterBlockButton();
    advgbRegisterBlockColumns();
    advgbRegisterBlockContactForm();
    advgbRegisterBlockCountUp();
    advgbRegisterBlockImage();
    advgbRegisterBlockImagesSlider();
    advgbRegisterBlockInfoBox();
    advgbRegisterBlockIcon();
    advgbRegisterBlockList();
    advgbRegisterBlockLoginForm();
    advgbRegisterBlockMap();
    advgbRegisterBlockNewsletter();
    advgbRegisterBlockSearchBar();
    advgbRegisterBlockSocialLinks();
    advgbRegisterBlockSummary();
    advgbRegisterBlockTable();
    advgbRegisterBlockTabs();
    advgbRegisterBlockTestimonial();
    advgbRegisterBlockVideo();
    advgbRegisterBlockWooProducts();
    advgbRegisterBlockAdvTabs();

    // Register Pro blocks if available
    advgbRegisterProBlocks();
}

// Hook into WordPress init
add_action('init', 'advgbRegisterAllBlocks');