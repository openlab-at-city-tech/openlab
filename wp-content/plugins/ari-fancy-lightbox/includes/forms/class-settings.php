<?php
namespace Ari_Fancy_Lightbox\Forms;

use Ari\Forms\Form as Form;

class Settings extends Form {
    function __construct( $options = array() ) {
        if ( ! isset( $options['prefix'] ) ) {
            $options['prefix'] = ARIFANCYLIGHTBOX_SETTINGS_NAME;
        }

        if ( ! isset( $options['fields_namespace'] ) ) {
            $options['fields_namespace'] = array( '\\Ari_Fancy_Lightbox\\Forms\\Fields' );
        }

        parent::__construct( $options );
    }

    protected function setup() {
        $nextgen_description = sprintf(
            __( 'Select "ARI Fancy Lightbox" option on "Lightbox Effects" tab on %s page in NextGEN plugin settings.', 'ari-fancy-lightbox' ),
            sprintf(
                '<a href="%s">%s</a>',
                'admin.php?page=ngg_other_options',
                __( 'Other Options', 'ari-fancy-lightbox' )
            )
        );

        $this->register_groups(
            array(
                'wp_gallery' => array(
                    'header' => __( 'WordPress Galleries', 'ari-fancy-lightbox' ),

                    'description' => __( 'Use parameters from this section to integrate the lightbox with native WordPress and Jetpack galleries.', 'ari-fancy-lightbox' ),
                ),

                'wp_gallery_config',

                'nextgen' => array(
                    'header' => __( 'NextGEN Galleries', 'ari-fancy-lightbox' ),

                    'description' => $nextgen_description,
                ),

                'images' => array(
                    'header' => __( 'Images', 'ari-fancy-lightbox' ),

                    'description' => __( 'Use parameters from this section to open links to images into the lightbox.', 'ari-fancy-lightbox' ),
                ),

                'woocommerce' => array(
                    'header' => __( 'WooCommerce', 'ari-fancy-lightbox' ),

                    'description' => __( 'WooCommerce product images will be opened into the lightbox if the parameter is enabled.', 'ari-fancy-lightbox' ),
                ),

                'youtube' => array(
                    'header' => __( 'YouTube videos', 'ari-fancy-lightbox' ),

                    'description' => __( 'Use parameters from this section to open links to YouTube videos into the lightbox.', 'ari-fancy-lightbox' ),
                ),

                'vimeo' => array(
                    'header' => __( 'Vimeo videos', 'ari-fancy-lightbox' ),

                    'description' => __( 'Use parameters from this section to open links to Vimeo videos into the lightbox.', 'ari-fancy-lightbox' ),
                ),

                'metacafe' => array(
                    'header' => __( 'Metacafe videos', 'ari-fancy-lightbox' ),

                    'description' => __( 'Use parameters from this section to open Metacafe videos into the lightbox.', 'ari-fancy-lightbox' ),
                ),

                'dailymotion' => array(
                    'header' => __( 'Dailymotion videos', 'ari-fancy-lightbox' ),

                    'description' => __( 'Use parameters from this section to open Dailymotion videos into the lightbox.', 'ari-fancy-lightbox' ),
                ),

                'vine' => array(
                    'header' => __( 'Vine videos', 'ari-fancy-lightbox' ),

                    'description' => __( 'Use parameters from this section to open videos from Vine service into the lightbox.', 'ari-fancy-lightbox' ),
                ),

                'instagram' => array(
                    'header' => __( 'Instagram content', 'ari-fancy-lightbox' ),

                    'description' => __( 'Use parameters from this section to open Instagram content into the lightbox.', 'ari-fancy-lightbox' ),
                ),

                'google_maps' => array(
                    'header' => __( 'Google Maps links', 'ari-fancy-lightbox' ),

                    'description' => __( 'Use parameters from this section to open Google Maps links into the lightbox.', 'ari-fancy-lightbox' ),
                ),

                'links' => array(
                    'header' => __( 'Open external links into lightbox', 'ari-fancy-lightbox' ),

                    'description' => __( 'Use parameters from this section to open external pages into the lightbox.', 'ari-fancy-lightbox' ),
                ),

                'lightbox' => array(
                    'header' => __( 'Lightbox', 'ari-fancy-lightbox' ),

                    'description' => __( 'The parameters from this section is used to configure lightbox behavior.', 'ari-fancy-lightbox' ),
                ),

                'lightbox_buttons',

                'pdf' => array(
                    'header' => __( 'PDF files', 'ari-fancy-lightbox' ),

                    'description' => 'Use parameters from this section to open links to PDF files into the lightbox. PDF files from the same domain as your site can be shown in cross-browser PDFJS viewer.',
                ),

                'pdf_config',

                'style' => array(),

                'advanced' => array(),
            )
        );

        // Style
        $this->register_fields(
            array(
                array(
                    'id' => 'style$$overlay_bgcolor',

                    'label' => __( 'Overlay background', 'ari-fancy-lightbox' ),

                    'description' => __( 'Background color of lightbox overlay.', 'ari-fancy-lightbox' ),

                    'class' => 'ari-input-tiny',

                    'type' => 'color',
                ),

                array(
                    'id' => 'style$$overlay_opacity',

                    'label' => __( 'Overlay opacity', 'ari-fancy-lightbox' ),

                    'description' => __( 'Opacity of lightbox overlay.', 'ari-fancy-lightbox' ),

                    'class' => 'ari-input-tiny',

                    'type' => 'spinner',

                    'float' => true,

                    'min' => 0.0,

                    'max' => 1.0,

                    'options' => array(
                        'step' => 0.01,
                    )
                ),

                array(
                    'id' => 'style$$thumbs_bgcolor',

                    'label' => __( 'Thumbnails pane background', 'ari-fancy-lightbox' ),

                    'description' => __( 'Background color of pane with thumbnails.', 'ari-fancy-lightbox' ),

                    'class' => 'ari-input-tiny',

                    'type' => 'color',
                ),

                array(
                    'id' => 'style$$zIndex',

                    'label' => __( 'Lightbox z-index', 'ari-fancy-lightbox' ),

                    'description' => __( 'z-Index of lightbox container element. Can be used to resolve style conflicts when lightbox is hidden under theme elements.', 'ari-fancy-lightbox' ),

                    'class' => 'ari-input-tiny',

                    'type' => 'spinner',

                    'min' => 0,

                    'options' => array(
                        'step' => 1,
                    )
                ),

                array(
                    'id' => 'style$$custom',

                    'label' => __( 'Custom CSS', 'ari-fancy-lightbox' ),

                    'description' => __( 'The defined CSS rules will be added on frontend pages. Can be used to resolve style conflicts or for customization.', 'ari-fancy-lightbox' ),

                    'type' => 'textarea',

                    'rows' => 7,

                    'cols' => 60,
                ),
            ),
            'style'
        );

        // Integration
        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$wp_gallery$$convert',

                    'label' => __( 'Convert WordPress galleries', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links in native WordPress galleries will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'group_switcher',

                    'child_group' => 'wp_gallery_config',
                )
            ),
            'wp_gallery'
        );
        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$wp_gallery$$grouping',

                    'label' => __( 'Navigate between items', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, it will be possible to navigate between gallery items into the lightbox and run slideshow.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                )
            ),
            'wp_gallery_config'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$images$$convert',

                    'label' => __( 'Convert links to images', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links to images will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                ),

                array(
                    'id' => 'convert$$images$$post_grouping',

                    'label' => __( 'Navigate between attachments', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, navigation between all attached images from current post will be possible into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                ),

                array(
                    'id' => 'convert$$images$$grouping_selector',

                    'label' => __( 'Custom grouping selectors', 'ari-fancy-lightbox' ),

                    'description' => __( 'It will be possible to navigate between images which are matched to CSS selector(s) which are specified in the textbox. Place a selector on a new line for each separate group.', 'ari-fancy-lightbox' ),

                    'cols' => 50,

                    'rows' => 3,

                    'type' => 'textarea',
                ),

                array(
                    'id' => 'convert$$images$$titleFromExif',

                    'label' => __( 'Get title from EXIF data', 'ari-fancy-lightbox' ),

                    'description' => __( 'If a title is not specified and image contains EXIF data, the plugin will try to load title from it.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                ),

                array(
                    'id' => 'convert$$images$$filenameToTitle',

                    'label' => __( 'Convert file name to title', 'ari-fancy-lightbox' ),

                    'description' => __( 'If a title is not defined, file name will be used.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                ),

                array(
                    'id' => 'convert$$images$$convertNameSmart',

                    'label' => __( 'Smart title', 'ari-fancy-lightbox' ),

                    'description' => __( 'The parameter is used with "Convert file name to title" parameter, if it is enabled the plugin will try to convert file name to more human-readable format.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                ),
            ),
            'images'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$woocommerce$$convert',

                    'label' => __( 'Attach to product images', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, product images will be shown into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                )
            ),
            'woocommerce'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$youtube$$convert',

                    'label' => __( 'Convert YouTube links', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links to YouTube videos will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                )
            ),
            'youtube'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$vimeo$$convert',

                    'label' => __( 'Convert Vimeo links', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links to Vimeo videos will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                )
            ),
            'vimeo'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$metacafe$$convert',

                    'label' => __( 'Convert Metacafe links', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links to Metacafe videos will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                )
            ),
            'metacafe'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$dailymotion$$convert',

                    'label' => __( 'Convert Dailymotion links', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links to Dailymotion videos will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                )
            ),
            'dailymotion'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$vine$$convert',

                    'label' => __( 'Convert Vine links', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links to Vine videos will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                )
            ),
            'vine'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$instagram$$convert',

                    'label' => __( 'Convert Instagram links', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links to Instagram items will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                )
            ),
            'instagram'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$google_maps$$convert',

                    'label' => __( 'Convert Google Maps links', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links to Google Maps will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                ),

                array(
                    'id' => 'convert$$google_maps$$showMarker',

                    'label' => __( 'Show marker', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, the marker will be shown for the selected place.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                ),
            ),
            'google_maps'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$pdf$$convert',

                    'label' => __( 'Convert PDF links', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links to PDF files will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'group_switcher',

                    'child_group' => 'pdf_config',
                ),
            ),
            'pdf'
        );
        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$pdf$$internal$$convert',

                    'label' => __( 'Local PDF files', 'ari-fancy-lightbox' ),

                    'description' => __( 'Enable this parameter to open local PDF files (from the same domain as your site) into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),

                array(
                    'id' => 'convert$$pdf$$internal$$viewer',

                    'label' => __( 'Local PDF viewer', 'ari-fancy-lightbox' ),

                    'description' => __( 'Select PDF viewer which will be used for local PDF documents.', 'ari-fancy-lightbox' ),

                    'type' => 'select',

                    'options' => array(
                        'iframe' => __( 'IFrame', 'ari-fancy-lightbox' ),

                        'pdfjs' => __( 'PDFJS', 'ari-fancy-lightbox' ),
                    ),

                    'postfix' => true,
                ),

                array(
                    'id' => 'convert$$pdf$$external$$convert',

                    'label' => __( 'External PDF files', 'ari-fancy-lightbox' ),

                    'description' => __( 'Enable this parameter to open external PDF files (from another domain as your site) into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),
            ),
            'pdf_config'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'convert$$links$$convert',

                    'label' => __( 'Convert links', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, links to external pages will be opened into the lightbox.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                )
            ),
            'links'
        );

        // Lightbox options
        $this->register_fields(
            array(
                array(
                    'id' => 'lightbox$$animationEffect',

                    'label' => __( 'Animation effect', 'ari-fancy-lightbox' ),

                    'description' => __( 'Open/close animation effect.', 'ari-fancy-lightbox' ),

                    'type' => 'select',

                    'options' => $this->animation_fx_list(),

                    'postfix' => true,
                ),

                array(
                    'id' => 'lightbox$$animationDuration',

                    'label' => __( 'Animation speed (ms)', 'ari-fancy-lightbox' ),

                    'description' => __( 'Specify duration of animation in milliseconds.', 'ari-fancy-lightbox' ),

                    'class' => 'ari-input-tiny',

                    'type' => 'spinner',

                    'min' => 0,

                    'options' => array(
                        'step' => 500,
                    )
                ),

                array(
                    'id' => 'lightbox$$transitionEffect',

                    'label' => __( 'Transition effect', 'ari-fancy-lightbox' ),

                    'description' => __( 'Transition effect between slides.', 'ari-fancy-lightbox' ),

                    'type' => 'select',

                    'options' => $this->transition_fx_list(),

                    'postfix' => true,
                ),

                array(
                    'id' => 'lightbox$$transitionDuration',

                    'label' => __( 'Transition fx speed (ms)', 'ari-fancy-lightbox' ),

                    'description' => __( 'Specify duration for transition animation in milliseconds.', 'ari-fancy-lightbox' ),

                    'class' => 'ari-input-tiny',

                    'type' => 'spinner',

                    'min' => 0,

                    'options' => array(
                        'step' => 500,
                    )
                ),

                array(
                    'id' => 'lightbox$$idleTime',

                    'label' => __( 'Idle time (sec)', 'ari-fancy-lightbox' ),

                    'description' => __( 'Info bar, controls, comments and other controls will be hidden when a user is inactive for the defined period of time in seconds. Set the parameter to 0, if do not want to hide controls.', 'ari-fancy-lightbox' ),

                    'class' => 'ari-input-tiny',

                    'type' => 'spinner',

                    'min' => 0,

                    'options' => array(
                        'step' => 1,
                    )
                ),

                array(
                    'id' => 'lightbox$$slideShow$$speed',

                    'label' => __( 'Slideshow pause (ms)', 'ari-fancy-lightbox' ),

                    'description' => __( 'Specify pause in milliseconds before showing next slide in slideshow mode.', 'ari-fancy-lightbox' ),

                    'class' => 'ari-input-tiny',

                    'type' => 'spinner',

                    'min' => 0,

                    'options' => array(
                        'step' => 500,
                    )
                ),

                array(
                    'id' => 'lightbox$$slideShow$$autoStart',

                    'label' => __( 'Slideshow auto start', 'ari-fancy-lightbox' ),

                    'description' => __( 'SlideShow will be started automatically when the lightbox is opened.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),

                array(
                    'id' => 'lightbox$$loop',

                    'label' => __( 'Loop navigation', 'ari-fancy-lightbox' ),

                    'description' => __( 'Enable infinite gallery navigation.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),

                array(
                    'id' => 'lightbox$$arrows',

                    'label' => __( 'Show navigation arrows', 'ari-fancy-lightbox' ),

                    'description' => __( 'Enable/disable navigation arrows at the screen edges.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),

                array(
                    'id' => 'lightbox$$closeClickOutside',

                    'label' => __( 'Close on outside click', 'ari-fancy-lightbox' ),

                    'description' => __( 'Close the lightbox when click outside of the content.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),

                array(
                    'id' => 'lightbox$$keyboard',

                    'label' => __( 'Keyboard navigation', 'ari-fancy-lightbox' ),

                    'description' => __( 'Activate keyboard shortcuts to close the lightbox, run slideshow, slides navigation and etc.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),

                array(
                    'id' => 'lightbox$$touch_enabled',

                    'label' => __( 'Enable gestures', 'ari-fancy-lightbox' ),

                    'description' => __( 'Enable gestures (tap, zoom, pan and pinch).', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),

                array(
                    'id' => 'lightbox$$autoFocus',

                    'label' => __( 'Focus the first element', 'ari-fancy-lightbox' ),

                    'description' => __( 'Try to focus on first focusable element after opening.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),

                array(
                    'id' => 'lightbox$$infobar',

                    'label' => __( 'Show info bar', 'ari-fancy-lightbox' ),

                    'description' => __( 'Show/hide information about slides (number of slides, current slide index, navigation arrows).', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),

                array(
                    'id' => 'lightbox$$toolbar',

                    'label' => __( 'Show buttons', 'ari-fancy-lightbox' ),

                    'description' => __( 'Switch off the parameter if want to hide all buttons (fullscreen, slideshow, close, thumbnails).', 'ari-fancy-lightbox' ),

                    'postfix' => true,

                    'type' => 'group_switcher',

                    'child_group' => 'lightbox_buttons',
                ),
            ),
            'lightbox'
        );

        $this->register_fields(
            array(
                array(
                    'id' => 'lightbox$$buttons',

                    'label' => __( 'Buttons', 'ari-fancy-lightbox' ),

                    'description' => __( 'Content items with the selected type(s) will not be shared.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox_group',

                    'options' => array(
                        'slideShow' => __( 'Slideshow', 'ari-fancy-lightbox' ),

                        'fullScreen' => __( 'Fullscreen', 'ari-fancy-lightbox' ),

                        'thumbs' => __( 'Thumbnails', 'ari-fancy-lightbox' ),

                        'close' => __( 'Close', 'ari-fancy-lightbox' ),
                    ),

                    'postfix' => true,
                ),
            ),
            'lightbox_buttons'
        );

        // Advanced
        $this->register_fields(
            array(
                array(
                    'id' => 'advanced$$clean_uninstall',

                    'label' => __( 'Clean uninstall', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, all data will be removed from a database when the plugin is uninstalled. Do not activate the parameter if want to re-install/upgrade the plugin and need to save all settings.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',

                    'postfix' => true,
                ),

                array(
                    'id' => 'advanced$$deregister_3rd_plugins',

                    'label' => __( 'Remove 3rd party plugins', 'ari-fancy-lightbox' ),

                    'description' => __( 'When the parameter is enabled, the plugin will try to remove includes of Fancybox jQuery library which are registered by 3rd party plugins.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                ),

                array(
                    'id' => 'advanced$$load_scripts_in_footer',

                    'label' => __( 'Load scripts in footer', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, javascript files will be loaded in footer.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                ),

                array(
                    'id' => 'lightbox$$protect',

                    'label' => __( 'Protect mode', 'ari-fancy-lightbox' ),

                    'description' => __( 'If the parameter is enabled, mouse right click will be disabled and a simple protection will be used for images.', 'ari-fancy-lightbox' ),

                    'type' => 'checkbox',
                ),

                array(
                    'id' => 'advanced$$custom_js',

                    'label' => __( 'Custom JS code', 'ari-fancy-lightbox' ),

                    'description' => __( 'You can enter any valid JavaScript code in this textarea. It will be executed before initializing of the lightbox. Can be used to resolve javascript conflicts with 3rd party plugins or theme widgets.', 'ari-fancy-lightbox' ),

                    'type' => 'textarea',

                    'rows' => 7,

                    'cols' => 60,
                ),
            ),
            'advanced'
        );

        do_action( 'ari-fancybox-options-setup', $this );
    }

    protected function animation_fx_list() {
        return array(
            '' => __( '- None -', 'ari-fancy-lightbox' ),

            'fade' => __( 'Fade', 'ari-fancy-lightbox' ),

            'zoom' => __( 'Zoom', 'ari-fancy-lightbox' ),

            'zoom-in-out' => __( 'Zoom In-Out', 'ari-fancy-lightbox' ),
        );
    }

    protected function transition_fx_list() {
        return array(
            '' => __( '- None -', 'ari-fancy-lightbox' ),

            'circular' => __( 'Circular', 'ari-fancy-lightbox' ),

            'fade' => __( 'Fade', 'ari-fancy-lightbox' ),

            'rotate' => __( 'Rotate', 'ari-fancy-lightbox' ),

            'slide' => __( 'Slide', 'ari-fancy-lightbox' ),

            'tube' => __( 'Tube', 'ari-fancy-lightbox' ),

            'zoom-in-out' => __( 'Zoom In-Out', 'ari-fancy-lightbox' ),
        );
    }
}
