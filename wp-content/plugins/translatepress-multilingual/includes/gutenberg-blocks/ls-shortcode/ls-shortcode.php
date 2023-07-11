<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Register: PHP.
 */
add_action(
    'init',
    function () {
        wp_register_script(
            'trp-block-ls-shortcode',
            add_query_arg( [ 'action' => 'trp-block-ls-shortcode.js', ], admin_url( 'admin-ajax.php' ) ),
            [ 'wp-blocks', 'wp-element', 'wp-editor' ],
            microtime(),
            true
        );
        register_block_type(
            __DIR__,
            [
                'render_callback' => function ( $attributes, $content ) {
                    ob_start();
                    do_action( 'trp/language-switcher/render_callback', $attributes, $content );
                    return ob_get_clean();
                },
            ]
        );
    }
);

/**
 * Render: PHP.
 *
 * @param array $attributes Optional. Block attributes. Default empty array.
 * @param string $content Optional. Block content. Default empty string.
 */
add_action(
    'trp/language-switcher/render_callback',
    function ( $attributes, $content ) {
        if ( $attributes['is_preview'] ) {
            echo '<style>
            .trp-language-switcher{
                position: relative;
                display: inline-block;
                padding: 0;
                border: 0;
                margin: 2px;
                box-sizing: border-box;
            }
            
            
            .trp-language-switcher > div {
                box-sizing: border-box;
            
                padding:3px 20px 3px 5px;
                border: 1px solid #c1c1c1;
                border-radius: 3px;
            
                background-image:
                        linear-gradient(45deg, transparent 50%, gray 50%),
                        linear-gradient(135deg, gray 50%, transparent 50%);
            
                background-position:
                        calc(100% - 8px) calc(1em + 0px),
                        calc(100% - 3px) calc(1em + 0px);
            
                background-size:
                        5px 5px,
                        5px 5px;
            
                background-repeat: no-repeat;
            
                background-color: #fff;
            }
            
            .trp-language-switcher > div > a {
                display: block;
                padding: 2px;
                border-radius: 3px;
                color: rgb(7, 105, 173);
            }
            
            .trp-language-switcher > div > a:hover {
                background: #f1f1f1;
            }
            .trp-language-switcher > div > a.trp-ls-shortcode-disabled-language {
                cursor: default;
            }
            .trp-language-switcher > div > a.trp-ls-shortcode-disabled-language:hover {
                background: none;
            }
            
            .trp-language-switcher > div > a > img{
                display: inline;
                margin: 0 3px;
                width: 18px;
                height: 12px;
                border-radius: 0;
            }
            
            .trp-language-switcher .trp-ls-shortcode-current-language{
                display: inline-block;
            }
            .trp-language-switcher:focus .trp-ls-shortcode-current-language,
            .trp-language-switcher:hover .trp-ls-shortcode-current-language{
                visibility: hidden;
            }
            
            .trp-language-switcher .trp-ls-shortcode-language{
                display: inline-block;
                height: 1px;
                overflow: hidden;
                visibility: hidden;
                z-index: 1;
            
                max-height: 250px;
                overflow-y: auto;
                left: 0;
                top: 0;
                min-height: auto;
            }
            
            .trp-language-switcher:focus .trp-ls-shortcode-language,
            .trp-language-switcher:hover .trp-ls-shortcode-language{
                visibility: visible;
                max-height: 250px;
                height: auto;
                overflow-y: auto;
                position: absolute;
                left: 0;
                top: 0;
                display: inline-block !important;
                min-height: auto;
            }
            </style>';
        }
        $atts = [
            'display_setting' => ($attributes['display_setting'] !== '') ? ' display="' . esc_html( $attributes['display_setting'] ) . '"' : '',
            'is_editor' => ($attributes['is_editor']) ? ' is_editor="true"' : '',
        ];
        echo '<div class="trp-block-container">' . do_shortcode( '[language-switcher ' . $atts['display_setting'] . $atts['is_editor'] . ']' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    },
    10,
    2
);

/**
 * Register: JavaScript.
 */
add_action(
    'wp_ajax_trp-block-ls-shortcode.js',
    function () {
        header( 'Content-Type: text/javascript' );

        $trp                 = TRP_Translate_Press::get_trp_instance();
        $trp_settings_object = $trp->get_component( 'settings' );

        $ls_options = $trp_settings_object->get_language_switcher_options();
        unset( $ls_options['full-names-no-html'] ); //only menu ls has this option
        ?>
        ( function ( blocks, i18n, element, serverSideRender, blockEditor, components ) {
            var { __ } = i18n;
            var el = element.createElement;
            var PanelBody = components.PanelBody;
            var SelectControl = components.SelectControl;
            var ToggleControl = components.ToggleControl;
            var TextControl = components.TextControl;
            var InspectorControls = wp.editor.InspectorControls;

            blocks.registerBlockType( 'trp/language-switcher', {
                icon:
                    el('svg', { width: 24, height: 24, viewBox: '0 0 500 500' },
                        el( 'path',
                            {
                                d: "M29.77 482c-1.7 0-5.23 0-8.16-2.56-3.51-3.07-3.5-7.37-3.5-9 .1-139.89.11-286.19 0-447.26 0-1.47 0-5.38 3-8.38s6.91-3 8.38-3q106.14.06 212.26.06c80.88 0 160 0 235-.08 1.51 0 5.51 0 8.55 3s3 6.84 3 8.68c-.08 62.79-.07 128.25-.07 186V249l-5.86 1.62a11.12 11.12 0 0 1-3 .42 10.74 10.74 0 0 1-2.13-.21c-.17 29-.14 58.39-.11 86.86v106.25c0 16.58-10.33 26.89-26.88 26.9H347.81c-24.82 0-57.54 0-90.77.19a12.33 12.33 0 0 1 .15 3.89l-.85 7h-73.92c-48.95 0-101.31 0-152.59.08z",
                                fill: "#fff"
                            }
                        ),
                        el( 'path',
                            {
                                d: "M247.88 481.93l-2.73-1c-1.08-.38-2.18-.76-3.26-1.17-13.8-5.25-22-15.65-23.77-30.09a44.93 44.93 0 0 1-.3-5.38V249.2c0-18.11 10.26-32.09 26.73-36.51a42.83 42.83 0 0 1 11.28-1.19h121.55c4.88 0 7.53 1.48 8.89 3.42a16.24 16.24 0 0 1 8.86-3.36c1-.08 2-.09 2.73-.09h47.94c23.72 0 33.52 6.67 41.93 28.54l.53 1.39v210.26l-.14.74-.32 1.78c-.92 5.18-2 11.06-6.44 16.51a28.87 28.87 0 0 1-21.58 10.78l-.55.46zm115.31-241.35H257c-8.17 0-10.05 1.87-10.05 10v192c0 8.35 1.68 10 10 10h192.42c7.94 0 9.66-1.75 9.66-9.85V250.37c0-8-1.74-9.76-9.81-9.77H398c-.79 0-1.81 0-2.91-.1-7.17-.58-12.37-5-13.64-11.39a13.73 13.73 0 0 1-10.83 10.95 28.27 28.27 0 0 1-6.08.53z",
                            }
                        ),
                        el( 'path',
                            {
                                d: "M359.24 240.44l.55-28.92h41.79v13.1h.51l-.07 16zM119.8 283.19h-62c-23.85 0-38.67-14.9-38.68-38.85V50.95c0-22.81 15.3-38.14 38.05-38.14h194.29c22.71 0 38 15.14 38 37.68.07 38.86.05 78.36 0 116.57v28.14a23.57 23.57 0 0 1-.74 6.58A13.84 13.84 0 0 1 275 212.09h-.3c-7-.14-12.29-4.38-13.76-11.08a28.78 28.78 0 0 1-.5-6.57V52.41c0-8.86-1.57-10.42-10.49-10.42H57.9c-7.7 0-9.58 1.86-9.58 9.47v192.93c0 8 1.76 9.69 9.88 9.69h142.05c6.84 0 11.3 1.45 14.47 4.7a13.71 13.71 0 0 1 3.71 10.22c-.14 5.29-2.68 14.16-18.13 14.16z",

                            }
                        ),
                        el( 'path',
                            {
                                d: "M197.07 223.84a12.71 12.71 0 0 1-6.27-1.68 153.34 153.34 0 0 1-17.54-11.71 166 166 0 0 1-18.95-16.27A192 192 0 0 1 121 220.33l-.58.36c-.7.45-1.52 1-2.46 1.46a14.1 14.1 0 0 1-6.43 1.63 11.63 11.63 0 0 1-10.08-5.73c-3.6-5.93-1.82-12.77 4.33-16.64a183.08 183.08 0 0 0 22.94-16.59c1.56-1.34 3.11-2.8 4.61-4.21s3.1-2.9 4.71-4.32A198.71 198.71 0 0 1 112 134.67l-.29-.61a26.52 26.52 0 0 1-1.17-2.67c-2.43-6.63.22-13.11 6.45-15.76a12.42 12.42 0 0 1 4.88-1 11.89 11.89 0 0 1 10.68 7.1c2.82 5.78 6 12.33 9.63 18.36a194.55 194.55 0 0 0 12.17 18 188 188 0 0 0 26.35-47.7c-5.38 0-11.24.05-18.43.05H92.51c-4.64 0-8.42-1.36-10.93-3.92a11.25 11.25 0 0 1-3.18-8.28c.09-5.5 3.89-11.94 14.29-12h25.9c9.12 0 16.66 0 23.58.09v-.64c.24-9.33 6.27-13.54 12.13-13.54s11.86 4.26 12 13.71v.4h50.14a14.5 14.5 0 0 1 7.23 1.51 11.67 11.67 0 0 1 6.19 12.85 11.44 11.44 0 0 1-11.38 9.76c-2.34.07-4.67.11-6.93.11-1.86 0-3.7 0-5.49-.07a197.76 197.76 0 0 1-35.59 66 181.87 181.87 0 0 0 29.12 23l1 .62a33.7 33.7 0 0 1 3.07 2c5.34 4 6.74 10.86 3.34 16.26a11.78 11.78 0 0 1-10 5.6zm206.12 201.27c-2.93 0-10.15-1.07-14-10.94l-.67-1.74c-2.37-6.13-4.79-12.42-7.05-18.74-3.51 0-7.38.05-12 .05h-33.02c-4.59 0-8.42 0-11.91-.08-2.05 5.85-4.3 11.64-6.48 17.25l-1.31 3.4c-3.77 9.74-11 10.79-13.93 10.79a15 15 0 0 1-4.89-.85 14.17 14.17 0 0 1-8.45-7.39c-1.22-2.61-2.2-7 0-12.81 14.83-39 30.63-80.55 47-123.41 3.87-10.15 11.18-12.28 16.64-12.28h.45c7.69.18 13.51 4.54 16.43 12.28l21.06 55.84q12.72 33.69 25.38 67.29c3.4 9 .37 17-7.74 20.23a14.75 14.75 0 0 1-5.51 1.1zm-7.74-213.01c-6.64-.07-14.36-4.35-14.39-16.22v-38.93c-.08-21.95-14.77-36.77-36.59-37a14.36 14.36 0 0 1 1.73 5.41 14.11 14.11 0 0 1-3.2 10.48 14 14 0 0 1-11 5.34 16.51 16.51 0 0 1-9.82-3.46c-8-5.9-17.35-12.89-26.63-20-5.65-4.32-6.83-9.09-6.82-12.33s1.21-8 6.92-12.39c7.57-5.76 15.93-12 26.29-19.67a17.12 17.12 0 0 1 10.2-3.74 13.84 13.84 0 0 1 10.95 5.52 14.15 14.15 0 0 1 3.1 10.53 14.5 14.5 0 0 1-1.67 5.14 72.88 72.88 0 0 1 15.11 1.57 64.54 64.54 0 0 1 50.55 61.93c.18 11.06.13 22.27.08 33.11v8.64c0 9.59-5.9 16-14.61 16zM175.3 425.06A14.67 14.67 0 0 1 161.17 409a14.41 14.41 0 0 1 1.69-5.1c-38.09-.47-65.68-28.26-65.73-66.42V298.55c0-7.61 3.71-13.18 10.23-15.29a15.37 15.37 0 0 1 4.66-.74c8.08 0 14.21 6.68 14.26 15.54v38.56c0 23 14 37.44 36.63 38.13a13.64 13.64 0 0 1 .95-15.23 13.83 13.83 0 0 1 11.29-5.94 18.12 18.12 0 0 1 10.83 4c9.38 7 17.19 12.84 25.16 18.88 5 3.77 7.48 8.1 7.48 12.87 0 3.25-1.27 8.1-7.33 12.7-8.07 6.14-16.54 12.49-25.91 19.43a16.92 16.92 0 0 1-10.08 3.6z",
                            }
                        ),
                        el( 'path',
                            {
                                d: "M339.85 353.73l5.82-15.22 7.53-19.73 7.43 19.77c1.82 4.82 3.75 10 5.74 15.21l4.1 10.84h-34.79z",
                                fill: "#fff"
                            }
                        ),
                    ),
                attributes: {
                    display_setting : {
                        type: 'string',
                        default: '',
                    },
                    is_preview : {
                        type: 'boolean',
                        default: false,
                    },
                    is_editor : {
                        type: 'boolean',
                        default: true,
                    },
                },

                edit: function ( props ) {
                    return [
                        el(
                            'div',
                            Object.assign( blockEditor.useBlockProps(), { key: 'trp/language-switcher/render' } ),
                            el( serverSideRender,
                                {
                                    block: 'trp/language-switcher',
                                    attributes: props.attributes,
                                }
                            )
                        ),
                        el( InspectorControls, { key: 'trp/language-switcher/inspector' },
                            [
                                el( PanelBody,
                                    {
                                        title: __( 'Language Switcher Settings' , 'translatepress-multilingual' ),
                                        key: 'trp/language-switcher/inspector/ls-settings'
                                    },
                                    [
                                        el( SelectControl,
                                            {
                                                label: __( 'Display' , 'translatepress-multilingual' ),
                                                key: 'trp/language_switcher/inspector/ls_settings/display_setting',
                                                help: __( 'Choose how to display the language names and whether to add flags.' , 'translatepress-multilingual' ),
                                                value: props.attributes.display_setting,
                                                options: [
                                                    {
                                                        label: __( 'Default setting' , 'translatepress-multilingual' ),
                                                        value: ''
                                                    },
                                                <?php
                                                foreach ( $ls_options as $key => $ls_option ) {
                                                    ?>
                                                    {
                                                        label: '<?php echo esc_html( $ls_option['label'] ) ?>',
                                                        value: '<?php echo esc_html( $key ) ?>'
                                                    },
                                                    <?php
                                                }
                                                ?>
                                                        ],
                                                onChange: ( value ) => { props.setAttributes( { display_setting: value } ); },
                                            }
                                        )
                                    ]
                                )
                            ]
                        )
                    ];
                }
            } );
        } )(
            window.wp.blocks,
            window.wp.i18n,
            window.wp.element,
            window.wp.serverSideRender,
            window.wp.blockEditor,
            window.wp.components
        );
        <?php
        exit;
    }
);
