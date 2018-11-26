<?php
/**
 * Admin core functions.
 *
 * @package cac-creative-commons
 */

/**
 * Registers assets used for the plugin.
 *
 * @since 0.1.0
 */
function cac_cc_register_scripts() {
	wp_register_script( 'cac-surveyjs', 'https://surveyjs.azureedge.net/1.0.46/survey.jquery.js', array( 'jquery' ), '1.0.46' );
	wp_register_style( 'cac-surveyjs', 'https://surveyjs.azureedge.net/1.0.46/survey.css', array( 'thickbox' ) );

	//wp_register_script( 'cac-showdown', 'https://cdnjs.cloudflare.com/ajax/libs/showdown/1.6.4/showdown.min.js' );

	wp_register_script( 'cac-creative-commons', CAC_CC_URL . 'assets/js.js', array( 'cac-surveyjs', 'thickbox' ) );

	wp_localize_script( 'cac-creative-commons', 'CAC_Creative_Commons', array( 
		'licenses' => cac_cc_get_licenses(),
		'chooser' => array(
			// 'derivative' key
			'yes' => array(
				// 'commerical' key
				'yes' => 'by',
				'no'  => 'by-nc'
			),
			'share' => array(
				'yes' => 'by-sa',
				'no'  => 'by-nc-sa'
			),
			'no' => array(
				'yes' => 'by-nd',
				'no'  => 'by-nc-nd'
			)
		),
		'questions' => array(
			'publicDomain'     => esc_html__( 'Do you want to use a public domain license?', 'cac-creative-commons' ),
			'publicDomainDesc' => esc_html__( 'If you want to share a work you created with no conditions, select Yes.', 'cac-creative-commons' ),
			'derivative'       => esc_html__( 'Allow adaptations of your work to be shared?', 'cac-creative-commons' ),
			'derivativeDesc'   => esc_html__( "If you select No, only unaltered copies of the work can be used by the licensee. If you select the Share Alike option, you permit others to distribute derivative works only under the same license or a compatible one.", 'cac-creative-commons' ),
			'commercial'       => esc_html__( 'Allow commercial uses of your work?', 'cac-creative-commons' ),
			'commercialDesc'   => esc_html__( 'If you select No, licensees may not use the work for commercial purposes unless they get your permission to do so.', 'cac-creative-commons' )
		),
		'answers' => array(
			'yes'   => esc_html__( 'Yes', 'cac-creative-commons' ),
			'no'    => esc_html__( 'No', 'cac-creative-commons' ),
			'share' => esc_html__( 'Yes, as long as others share alike', 'cac-creative-commons' )
		),
		'sizes' => array(
			'normal'  => '88x31',
			'compact' => '80x15'
		),
		'text' => array(
			'intro'          => esc_html__( 'Creative Commons licenses help you share your work while keeping your copyright. Other people can copy and distribute your work provided they give you credit -- and only on the conditions you specify here. This page helps you choose those conditions.', 'cac-creative-commons' ),
			'intro2'         => esc_html__( 'To license a work, you must be its copyright holder or have express authorization from its copyright holder to do so.', 'cac-creative-commons' ),
			'selected'       => esc_html__( 'Selected License', 'cac-creative-commons' ),
			'freeCulture'    => esc_html__( 'This is a Free Culture License', 'cac-creative-commons' ),
			'notFreeCulture' => esc_html__( 'This is not a Free Culture License', 'cac-creative-commons' )
		),
		'versions' => array(
			'current' => cac_cc_get_license_version(),
			'zero'    => cac_cc_get_zero_license_version()
		)
	) );

	wp_enqueue_script( 'cac-creative-commons' );
	wp_enqueue_style( 'cac-surveyjs' );
}
add_action( 'admin_enqueue_scripts', 'cac_cc_register_scripts' );

/**
 * Outputs the license chooser markup.
 *
 * @since 0.1.0
 */
function cac_cc_button_chooser( $args = array() ) {
	echo cac_cc_get_button_chooser( $args );
}

/**
 * Returns the license chooser markup.
 *
 * @since 0.1.0
 *
 * @param array $args {
 *     Array of arguments.
 *
 *     @type string $modal_title           Thickbox modal title.
 *     @type string $link_label            Label for the link.
 *     @type string $link_class            CSS classes for the link. Default: 'button button-secondary'.
 *     @type string $link_wrapper_element  Element to wrap the link with. Default: 'p'.
 * }
 * @return string
 */
function cac_cc_get_button_chooser( $args = array() ) {
	// Our script must be enqueued before this function returns anything.
	if ( ! wp_script_is( 'cac-creative-commons', 'enqueued' ) ) {
		return '';
	}

	$args = array_merge( array(
		'modal_title' => __( 'Choose a License', 'cac-creative-commons' ),
		'link_label'  => __( 'Choose license', 'cac-creative-commons' ),
		'link_class'  => 'button button-secondary',
		'link_wrapper_element' => 'p'
	), $args );

	$license_val = esc_attr( cac_cc_get_default_license() );

	$args['link_class']  = strip_tags( $args['link_class'] );
	$args['modal_title'] = esc_html( $args['modal_title'] );

	if ( empty( $args['link_wrapper_element'] ) ) {
		$wrapper_start = $wrapper_end = '';
	} else {
		$wrapper_start = sprintf( '<%s>', strip_tags( $args['link_wrapper_element'] ) );
		$wrapper_end   = sprintf( '</%s>', strip_tags( $args['link_wrapper_element'] ) );
	}

	$nonce   = wp_nonce_field( 'cac-cc-license', 'cac-cc-nonce', false, false );
	$chooser = <<<STR

	<input type="hidden" id="cac-cc-license" name="cac-cc-license" value="{$license_val}" />

	{$wrapper_start}<a class="thickbox {$args['link_class']}" title="{$args['modal_title']}" href="#TB_inline?width=600&height=550&inlineId=cac-cc-survey">{$args['link_label']}</a>{$wrapper_end}

	<div id="cac-cc-survey" style="display:none"></div>

STR;

	return $nonce . $chooser;
}
