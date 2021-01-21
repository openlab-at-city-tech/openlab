<input type="text" name="cac_ncs_vcode" value="<?php echo esc_attr( $vcode ); ?>" />
<?php wp_nonce_field( 'openlab_signup_codes', 'openlab_signup_codes_nonce' ); ?>
