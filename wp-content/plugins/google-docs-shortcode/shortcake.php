<?php
/**
 * Shortcake support.
 *
 * @since 0.5.0
 */

shortcode_ui_register_for_shortcode(
	'gdoc',
	array(

		'label' => __( 'Google Drive', 'google-docs-shortcode' ),

		'listItemImage' => '<img src="https://developers.google.com/drive/images/drive_icon_mono.png" alt="" />',

		'attrs' => array(
			array(
				'label' => __( 'Google Doc Link', 'google-docs-shortcode' ),
				'attr'  => 'link',
				'type'  => 'text',
				'description' => __( 'Paste the published-to-the-web Google Doc link or a publicly-shared Google Doc link here.', 'gdrive' )
			),

			array(
				'label' => __( 'Width', 'google-docs-shortcode' ),
				'attr'  => 'width',
				'type'  => 'number',
				'meta' => array(
					'style' => 'width:75px'
				),
				'description' => __( "Enter width in pixels. If left blank, this defaults to the theme's width.", 'gdrive' )
			),

			array(
				'label' => __( 'Height', 'google-docs-shortcode' ),
				'attr'  => 'height',
				'type'  => 'number',
				'meta' => array(
					'style' => 'width:75px'
				),
				'description' => __( "Enter height in pixels. If left blank, this defaults to 300.", 'gdrive' )
			),

			array(
				'label' => __( 'Add Download Link', 'google-docs-shortcode' ),
				'attr'  => 'downloadlink',
				'type' => 'select',
				'options' => array(
					'1' => __( 'Yes', 'google-docs-shortcode' ),
					'' => __( 'No', 'google-docs-shortcode' ),
				),
				'description' => __( 'If checked, this adds a download link after the embedded content.', 'google-docs-shortcode' )
			),

			array(
				'label' => __( 'Type (non-Google Doc only)', 'google-docs-shortcode' ),
				'attr'  => 'type',
				'type' => 'select',
				'options' => array(
					'' => '--',
					'audio' => __( 'Audio', 'google-docs-shortcode' ),
					'other' => __( 'Other (Image, PDF, Microsoft Office, etc.)', 'google-docs-shortcode' ),
				),
				'description' => __( "If your Google Drive item is not a Doc, Slide, Spreadsheet or Form, select the type of item you are embedding.", 'gdrive' )
			),

			array(
				'label' => __( 'Show Doc Header/Footer', 'google-docs-shortcode' ),
				'attr'  => 'seamless',
				'type' => 'select',
				'options' => array(
					'0' => __( 'Yes', 'google-docs-shortcode' ),
					'' => __( 'No', 'google-docs-shortcode' ),
				),
				'description' => __( 'This is only applicable to Google Documents.', 'google-docs-shortcode' )
			),

			array(
				'label' => __( 'Size', 'google-docs-shortcode' ),
				'attr'  => 'size',
				'type' => 'select',
				'options' => array(
					'small'  => __( 'Small - 480 x 299', 'google-docs-shortcode' ),
					'medium' => __( 'Medium - 960 x 559', 'google-docs-shortcode' ),
					'large'  => __( 'Large - 1440 x 839', 'google-docs-shortcode' )
				),
				'description' => __( 'This is only applicable to Google Slides. If you want to set a custom width and height, use the options above.', 'google-docs-shortcode' )
			),
		),

	)
);

/**
 * Enqueues JS needed for toggle functionality in Shortcake.
 *
 * @since 0.5.0
 */
function gdoc_enqueue_shortcode_ui() {
	wp_enqueue_script( 'gdoc', plugin_dir_url( __FILE__ ) . 'shortcake.js' );
}
add_action( 'enqueue_shortcode_ui', 'gdoc_enqueue_shortcode_ui' );