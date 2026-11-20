<?php

if (!defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * The campaigns shipped with this release (#2605). See MetaSlider_Campaigns for the format.
 *
 * Keep prices, links and HTML out of the translated text, so translators can't break them.
 *
 * @since 3.114.1
 */
return array(
    array(
        'slug' => 'loyal-2026',
        'starts_at' => null,
        'ends_at' => '2027-03-01T00:00:00+00:00',
        'snooze_days' => 84,
        'rules' => array(
            'min_usage_days' => 30,
        ),
        'content' => array(
            'headline' => __('Thanks for being a loyal MetaSlider user!', 'ml-slider'),
            'intro' => sprintf(
                /* translators: %s: the discount, e.g. 50% */
                __('Get Slideshow Pro and save %s!', 'ml-slider'),
                '50%'
            ),
            'price' => '$39.50',
            'old_price' => '$79',
            'features' => array(
                __('Animated slides', 'ml-slider'),
                __('YouTube & TikTok slides', 'ml-slider'),
                __('WooCommerce slides', 'ml-slider'),
                __('Scheduled slides', 'ml-slider'),
                __('Theme editor', 'ml-slider'),
                __('Premium support', 'ml-slider'),
                __('Post feed slides', 'ml-slider'),
                __('And many more features', 'ml-slider'),
            ),
            'cta_label' => __('Get Slideshow Pro', 'ml-slider'),
            'cta_url' => 'https://www.metaslider.com/upgrade/?utm_source=lite&utm_medium=popup&utm_campaign=loyal-2026',
        ),
    ),
);
