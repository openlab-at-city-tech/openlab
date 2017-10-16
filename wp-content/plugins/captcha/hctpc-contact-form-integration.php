<?php 

/*---------------------------------------------sizh work start fom here------------------------------------------------------------------------*/

/**
 *
 * Add Captcha To WP Login Form.
 *
 */

function add_hctpc_captcha( $content ) {
    return $content . hctpc_display_captcha_custom( 'wp_login', 'cptch_wp_login' );
}
add_filter( 'login_form_middle', 'add_hctpc_captcha' );




/**
 * Add custom shortcode to Contact Form 7
 */
add_action( 'wpcf7_init', 'add_shortcode_wpcaptcha' );
function add_shortcode_wpcaptcha() {
    wpcf7_add_shortcode( 'wpcaptcha', 'captcha_shortcode', true );
}


/*
 * Captcha shortcode
 *
 */

function captcha_shortcode($tag){

	$tag = new WPCF7_Shortcode( $tag );
	$captcha =  cptch_display_filter();
	return $captcha;
	
}

/*
 * 
 * Add Validation of Captcha in Contact Form 7.
 *
 */

add_filter('wpcf7_validate_wpcaptcha*', 'wpcaptcha_wpcf7_if_spam', 20, 2);
add_filter('wpcf7_validate_wpcaptcha', 'wpcaptcha_wpcf7_if_spam', 20, 2);

function wpcaptcha_wpcf7_if_spam($result, $tag) {
	
	$tag = new WPCF7_Shortcode( $tag );

	global $hctpc_options;

	$str_key = $hctpc_options['str_key']['key'];
	
	$number_val = isset( $_REQUEST['hctpc_number'] ) ? trim( $_REQUEST['hctpc_number'] ) : '';
	
	$hctpc_result = isset( $_REQUEST['hctpc_result'] ) ? trim( $_REQUEST['hctpc_result'] ) : '';
	
	$hctpc_time = isset( $_REQUEST['hctpc_time'] ) ? trim( $_REQUEST['hctpc_time'] ) : '';
	
	if( empty($number_val) && empty($hctpc_result) && empty($hctpc_time)  ) {
        $tag->name = "hctpc_number";
        $result->invalidate( $tag, __('Please enter the value.', 'cf7-wp-captcha') );
    }
	
	if ( isset( $_REQUEST['hctpc_result'] ) && isset( $_REQUEST['hctpc_number'] ) && isset( $_REQUEST['hctpc_time'] ) && 0 != strcasecmp( trim( hctpc_decode( $_REQUEST['hctpc_result'], $str_key, $_REQUEST['hctpc_time'] ) ), $_REQUEST['hctpc_number'] ) ) {
        $tag->name = "hctpc_number";
        $result->invalidate( $tag, __('Please enter the correct value.', 'cf7-wp-captcha') );
    }
	
	

	//print_r($result);
	//exit;
	return $result;

}


/**
 *
 *
 * After Send Mail relode New Captcha Funcion.
 *
 */
 


 /*
 *
 * Add Contact Form Tag Generator Button
 *
 */

add_action( 'wpcf7_admin_init', 'wpcaptcha_add_tag_generator', 75 );

function wpcaptcha_add_tag_generator() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'wpcaptcha', __( 'WP Captcha', 'cf7-wp-captcha' ),
		'wpcaptcha_tag_generator', array( 'nameless' => 1 ) );
}

function wpcaptcha_tag_generator( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() ); ?>
	<div class="control-box">
    <fieldset>
    	<legend>For captcha you can copy shortcode and paste in contact form container.</legend>
    <table class="form-table">    
    <tbody>
    <tr>
    <th scope="row" style="padding-top:15px"><label for="captcha_shortcode"><?php echo esc_html( __( 'Captcha Shortcode', 'contact-form-7' ) ); ?></label></th>
    <td><p class="captcha_short">[wpcaptcha]</p></td>
    </tr>
    </tbody>
    </table>
    </fieldset>
	</div>
	<div class="insert-box">
	<input type="text" value="[wpcaptcha]" class="captcha" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox" style="overflow:hidden; float:right">
	<input type="button" class="button button-primary insert-tag-captcha" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>

	<br class="clear" />

	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-captchatag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a Captcha field, you need to insert the corresponding Captcha Shortcode (%s) into the field on the Captcha tab.", 'contact-form-7' ) ), '<strong><span class="captcha-tag"></span></strong>' ); ?><input type="text" class="captcha-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-captchatag' ); ?>" /></label></p>
</div>
<?php
}
