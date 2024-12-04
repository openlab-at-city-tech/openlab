<?php

/**
 * Block Styles
 *
 * @link https://developer.wordpress.org/reference/functions/register_block_style/
 *
 * @package hello-agency
 * @since 1.0.0
 */

if (function_exists('register_block_style')) {
    /**
     * Register block styles.
     *
     * @since 0.1
     *
     * @return void
     */
    function hello_agency_register_block_styles()
    {
        register_block_style(
            'core/columns',
            array(
                'name'  => 'hello-agency-boxshadow',
                'label' => __('Box Shadow', 'hello-agency')
            )
        );

        register_block_style(
            'core/column',
            array(
                'name'  => 'hello-agency-boxshadow',
                'label' => __('Box Shadow', 'hello-agency')
            )
        );
        register_block_style(
            'core/column',
            array(
                'name'  => 'hello-agency-boxshadow-medium',
                'label' => __('Box Shadow Medium', 'hello-agency')
            )
        );
        register_block_style(
            'core/column',
            array(
                'name'  => 'hello-agency-boxshadow-large',
                'label' => __('Box Shadow Large', 'hello-agency')
            )
        );

        register_block_style(
            'core/group',
            array(
                'name'  => 'hello-agency-boxshadow',
                'label' => __('Box Shadow', 'hello-agency')
            )
        );
        register_block_style(
            'core/group',
            array(
                'name'  => 'hello-agency-boxshadow-medium',
                'label' => __('Box Shadow Medium', 'hello-agency')
            )
        );
        register_block_style(
            'core/group',
            array(
                'name'  => 'hello-agency-boxshadow-large',
                'label' => __('Box Shadow Larger', 'hello-agency')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'hello-agency-boxshadow',
                'label' => __('Box Shadow', 'hello-agency')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'hello-agency-boxshadow-medium',
                'label' => __('Box Shadow Medium', 'hello-agency')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'hello-agency-boxshadow-larger',
                'label' => __('Box Shadow Large', 'hello-agency')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'hello-agency-image-pulse',
                'label' => __('Iamge Pulse Effect', 'hello-agency')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'hello-agency-boxshadow-hover',
                'label' => __('Box Shadow on Hover', 'hello-agency')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'hello-agency-image-hover-pulse',
                'label' => __('Hover Pulse Effect', 'hello-agency')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'hello-agency-image-hover-rotate',
                'label' => __('Hover Rotate Effect', 'hello-agency')
            )
        );
        register_block_style(
            'core/columns',
            array(
                'name'  => 'hello-agency-boxshadow-hover',
                'label' => __('Box Shadow on Hover', 'hello-agency')
            )
        );

        register_block_style(
            'core/column',
            array(
                'name'  => 'hello-agency-boxshadow-hover',
                'label' => __('Box Shadow on Hover', 'hello-agency')
            )
        );

        register_block_style(
            'core/group',
            array(
                'name'  => 'hello-agency-boxshadow-hover',
                'label' => __('Box Shadow on Hover', 'hello-agency')
            )
        );

        register_block_style(
            'core/post-terms',
            array(
                'name'  => 'categories-background-with-round',
                'label' => __('Background with round corner style', 'hello-agency')
            )
        );
        register_block_style(
            'core/post-title',
            array(
                'name'  => 'title-hover-primary-color',
                'label' => __('Hover: Primary color', 'hello-agency')
            )
        );
        register_block_style(
            'core/post-title',
            array(
                'name'  => 'title-hover-secondary-color',
                'label' => __('Hover: Secondary color', 'hello-agency')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-hover-primary-color',
                'label' => __('Hover: Primary Color', 'hello-agency')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-hover-secondary-color',
                'label' => __('Hover: Secondary Color', 'hello-agency')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-hover-primary-bgcolor',
                'label' => __('Hover: Primary color fill', 'hello-agency')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-hover-secondary-bgcolor',
                'label' => __('Hover: Secondary color fill', 'hello-agency')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-hover-white-bgcolor',
                'label' => __('Hover: White color fill', 'hello-agency')
            )
        );

        register_block_style(
            'core/read-more',
            array(
                'name'  => 'readmore-hover-primary-color',
                'label' => __('Hover: Primary Color', 'hello-agency')
            )
        );
        register_block_style(
            'core/read-more',
            array(
                'name'  => 'readmore-hover-secondary-color',
                'label' => __('Hover: Secondary Color', 'hello-agency')
            )
        );
        register_block_style(
            'core/read-more',
            array(
                'name'  => 'readmore-hover-primary-fill',
                'label' => __('Hover: Primary Fill', 'hello-agency')
            )
        );
        register_block_style(
            'core/read-more',
            array(
                'name'  => 'readmore-hover-secondary-fill',
                'label' => __('Hover: secondary Fill', 'hello-agency')
            )
        );

        register_block_style(
            'core/list',
            array(
                'name'  => 'list-style-no-bullet',
                'label' => __('List Style: Hide bullet', 'hello-agency')
            )
        );
        register_block_style(
            'core/list',
            array(
                'name'  => 'hide-bullet-list-link-hover-style-primary',
                'label' => __('Hover style with primary color and hide bullet', 'hello-agency')
            )
        );
        register_block_style(
            'core/list',
            array(
                'name'  => 'hide-bullet-list-link-hover-style-white',
                'label' => __('Hover style with white color and hide bullet', 'hello-agency')
            )
        );

        register_block_style(
            'core/gallery',
            array(
                'name'  => 'enable-grayscale-mode-on-image',
                'label' => __('Enable Grayscale Mode on Image', 'hello-agency')
            )
        );
        register_block_style(
            'core/social-links',
            array(
                'name'  => 'social-icon-border',
                'label' => __('Border Style', 'hello-agency')
            )
        );
        register_block_style(
            'core/page-list',
            array(
                'name'  => 'hello-agency-page-list-bullet-hide-style',
                'label' => __('Hide Bullet Style', 'hello-agency')
            )
        );
        register_block_style(
            'core/categories',
            array(
                'name'  => 'hello-agency-categories-bullet-hide-style',
                'label' => __('Hide Bullet Style', 'hello-agency')
            )
        );
        register_block_style(
            'core/cover',
            array(
                'name'  => 'hello-agency-cover-round-style',
                'label' => __('Round Corner Style', 'hello-agency')
            )
        );
    }
    add_action('init', 'hello_agency_register_block_styles');
}
