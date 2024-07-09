<?php
add_filter( 'trp_machine_translation_engines', 'trp_mtapi_add_engine', 10 );
function trp_mtapi_add_engine( $engines ){
	$engines[] = array( 'value' => 'mtapi', 'label' => __( 'TranslatePress AI', 'translatepress-multilingual' ) );
	return $engines;
}

add_action( 'trp_machine_translation_extra_settings_middle', 'trp_mtapi_add_settings' );
function trp_mtapi_add_settings( $mt_settings ){
    require_once("class-mtapi-customer.php");
    //$trp = TRP_Translate_Press::get_trp_instance();

	$license = get_option('trp_license_key');
	$status = get_option('trp_license_status');
	$details = get_option('trp_license_details');

    if (!isset($details['valid'][0])) $status = false;

    //dd($status);
	//dd(array($license, $status, $details));
    if ($status === false) :
    ?>
    <tr class="trp-engine" id="mtapi">
        <th scope="row"><img src="<?php echo esc_url( TRP_PLUGIN_URL.'assets/images/'); ?>ai-icon.svg" width="24" height="24"/> TranslatePress AI<?php //this is not localized by choice ?></th>
        <td>
            <p><?php esc_html_e( 'Integrate machine translation directly with your WordPress website.', 'translatepress-multilingual' ); ?></p>
            <p id="trp-mtapi-key" style="color:red;"><?php esc_html_e('No Active License Detected for this website.', 'translatepress-multilingual'); ?></p>
            <p>
            <?php
                printf(
					esc_html__( 'Add a license by visiting the %1$s tab:', 'translatepress-multilingual' ),
                    '<a href="' . esc_url( admin_url('admin.php?page=trp_license_key')) . '"> '. esc_html__('License', 'translatepress-multilingual') . '</a>'
				); ?>
                <a href="<?php echo esc_url( admin_url('admin.php?page=trp_license_key')) ?>" class="button"> <?php esc_html_e('Register License', 'translatepress-multilingual') ?></a></p>
        </td>
    </tr>
    <?php
    endif;

	if ($status === 'valid') :
        $product_name = '<strong>' . str_replace('+', ' ', $details['valid'][0]->item_name) . '</strong>';

        // MTAPI_URL needs to be defined in wp-config.php for local host development
        $mtapi_url = (defined('MTAPI_URL')  ? MTAPI_URL : 'https://mtapi.translatepress.com' );

        $mtapi_server = new TRP_MTAPI_Customer($mtapi_url);
		$site_status = $mtapi_server->lookup_site($license, home_url());

        $site_status['quota'] = isset ( $site_status['quota'] ) ? $site_status['quota'] : 0;
        $quota = ceil($site_status['quota'] / 5);

    ?>
    <tr class="trp-engine" id="mtapi">
        <th scope="row"><img src="<?php echo esc_url(TRP_PLUGIN_URL.'assets/images/'); ?>ai-icon.svg" width="24" height="24"/> TranslatePress AI <?php //this is not localized by choice ?></th>
        <td>
            <p id="trp-mtapi-key" style="color:green;"><?php
                printf(esc_html__('You have a valid %s license.', 'translatepress-multilingual'),
                        wp_kses( $product_name, array( 'strong' => array() ) )
                );
            ?></p>
            <p><?php
                printf(
                        wp_kses( __('Quota: <span id="trp-ai-quota-number">%s</span> available words remaining.', 'translatepress-multilingual'), array('span' => array( 'id' => [] ) ) ),
                        esc_html( number_format($quota) )
                );

                if ( isset( $site_status['exception'][0]['message'] ) && $site_status['exception'][0]['message'] == "Site not found." ){ ?>
                    <span id="trp-refresh-tpai">
                        <span id="trp-refresh-tpai-dashicon" class="dashicons dashicons-controls-repeat"></span>
                        <span id="trp-refresh-tpai-text-recheck">
                            <?php esc_html_e( 'Recheck', 'translatepress-multilingual' ); ?>
                        </span>
                    </span>
                    <span id="trp-refresh-tpai-text-rechecking" style="display:none">
                        <?php esc_html_e( 'Rechecking...', 'translatepress-multilingual' ); ?>
                    </span>
                    <span id="trp-refresh-tpai-text-done" style="display:none">
                        <?php esc_html_e( 'Done.', 'translatepress-multilingual' ); ?>
                    </span>
                <?php } ?>
            </p>
            <p>
	            <?php
	            printf(
		            esc_html__( 'Manage your license & quota on the %s', 'translatepress-multilingual' ),
		            '<a href="' . esc_url( 'https://translatepress.com/account/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettingsAT&utm_campaign=tp-ai') . '" target="_blank"> '. esc_html__('TranslatePress.com Account Page', 'translatepress-multilingual') . '</a>'
	            );
                ?>
            </p>

        </td>
    </tr>
	<?php
	endif;

}

/**
 * Store url
 *
 * In order of priority MTAPI_STORE_URL, tpcom.local, translatepress.com
 *
 * @return string
 */
function trp_mtapi_get_store_url() {
    $store_url = ( !isset( $store_url ) ) ? ( ( defined( 'MTAPI_STORE_URL' ) ) ? MTAPI_STORE_URL : null ) : $store_url;
    $store_url = ( !isset( $store_url ) ) ? ( ( defined( 'MTAPI_URL' ) && MTAPI_URL == 'http://mtapi.local' ) ? 'http://tpcom.local' : null ) : $store_url;
    return ( !isset( $store_url ) ) ? "https://translatepress.com" : $store_url;
}

/**
 * Make sure translatepress.com syncs with MTAPI for this license and this site
 *
 * Performed when saving Automatic Translation tab settings
 */
add_filter( 'trp_machine_translation_sanitize_settings', 'trp_mtapi_sync_license', 10, 2 );
function trp_mtapi_sync_license( $settings, $mt_settings ) {
    if ( $settings['translation-engine'] === 'mtapi' ) {
        $license = get_option( 'trp_license_key' );
        $status  = get_option( 'trp_license_status' );

        if ( $status === 'valid' ) {
            trp_mtapi_sync_license_call( $license );
        }
    }

    return $settings;
}

/**
 * Make translatepress.com sync with MTAPI for this license and this site
 */
function trp_mtapi_sync_license_call( $license_key ) {
    $trp = TRP_Translate_Press::get_trp_instance();

    if ( !empty( $trp->active_pro_addons ) ) {

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'sync_mtapi_license',
            'license'    => $license_key,
            'url'        => home_url()
        );
        $store_url  = trp_mtapi_get_store_url();
        // Call the custom API.
        $response = wp_remote_post( $store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
        return $response;
    }
    return false;
}

add_action('wp_ajax_trp_ai_recheck_quota','trp_ai_recheck_quota');
function trp_ai_recheck_quota(){
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX && current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_ai_recheck_quota' ) {
            $nonce_okay = check_ajax_referer( 'trp-tpai-recheck', 'nonce' );
            if ( $nonce_okay ){
                $license = get_option( 'trp_license_key' );
                $status  = get_option( 'trp_license_status' );

                if ( $status === 'valid' ) {
                    $response = trp_mtapi_sync_license_call( $license );
                    if ( is_array( $response ) && ! is_wp_error( $response ) && isset( $response['response'] ) &&
                        isset( $response['response']['code']) && $response['response']['code'] == 200 ) {

                        $mtapi_url = (defined('MTAPI_URL')  ? MTAPI_URL : 'https://mtapi.translatepress.com' );

                        require_once("class-mtapi-customer.php");
                        $mtapi_server = new TRP_MTAPI_Customer($mtapi_url);
                        $site_status = $mtapi_server->lookup_site($license, home_url());

                        $site_status['quota'] = isset ( $site_status['quota'] ) ? $site_status['quota'] : 0;
                        $quota = intval(ceil($site_status['quota'] / 5));
                        echo trp_safe_json_encode( ['quota' => $quota ] ); //phpcs:ignore
                    }
                }
            }

        }
    }
    wp_die();
}