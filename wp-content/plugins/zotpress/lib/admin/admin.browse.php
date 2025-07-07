<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


global $wpdb;

$zp_accounts_total = zotpress_get_total_accounts( $wpdb );
$zp_account = false;
$api_user_id = false;

// Display Browse page if there's at least one Zotero account synced
if ( $zp_accounts_total > 0 )
{
    if ( isset($_GET['api_user_id'])
            && preg_match("/^\\d+\$/", sanitize_text_field(wp_unslash($_GET['api_user_id']))) )
    {
        $zp_account_temp = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM `".$wpdb->prefix."zotpress` 
                WHERE `api_user_id`=%s",
                array(sanitize_text_field(wp_unslash($_GET['api_user_id'])))
            ), OBJECT
        );

        if ( (array)$zp_account_temp !== [] )
        {
            $zp_account = $zp_account_temp;
            $api_user_id = $zp_account->api_user_id;
        }
    }
    elseif ( get_option("Zotpress_DefaultAccount") )
    {
        // $zp_account_temp = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".get_option("Zotpress_DefaultAccount")."'", OBJECT);
        $zp_account_temp = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM `".$wpdb->prefix."zotpress` 
                WHERE `api_user_id`=%s",
                get_option("Zotpress_DefaultAccount")
            ), OBJECT
        );
        
        if ( (array)$zp_account_temp !== [] )
        {
            $zp_account = $zp_account_temp;
            $api_user_id = $zp_account->api_user_id;
        }
        else
        {
            $zp_account_temp = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1");

            if ( (array)$zp_account_temp !== [] )
            {
                $zp_account = $zp_account_temp;
                $api_user_id = $zp_account->api_user_id;
            }
        }
    }

    else
    {
        $zp_account_temp = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1");

        if ( ( is_array($zp_account_temp)
                    || is_object($zp_account_temp)
                    || $zp_account_temp instanceof Countable )
                && (array)$zp_account_temp !== [] )
        {
            $zp_account = $zp_account_temp;
            $api_user_id = $zp_account->api_user_id;
        }
    }


    // Use Browse class
    $zpLib = new zotpressLib;
    $zpLib->setAccount($zp_account);
    $zpLib->setType("dropdown");
    $zpLib->setAdmin(true);
    $zpLib->setShowImage(true);
?>

<div id="zp-Zotpress" class="wrap">

    <?php include( dirname(__FILE__) . '/admin.menu.php' ); ?>

    <div class="zp-Browse-Wrapper">

        <h3><?php

        if ( $zp_accounts_total === 1 ): esc_html_e('Your Library', 'zotpress'); else: ?>

            <div class="zp-Browse-Accounts">
                <?php
                echo wp_kses(
                    zotpress_get_accounts( $wpdb, true, false, false, false, esc_html($api_user_id) ),
                    array(
                        'label' => array(
                            'for' => array(),
                            'class' => array(),
                            'style' => array()
                        ),
                        'select' => array(
                            'id' => array(),
                            'class' => array(),
                            'style' => array(),
                            'name' => array(),
                        ),
                        'option' => array(
                            'id' => array(),
                            'class' => array(),
                            'rel' => array(),
                            'name' => array(),
                            'value' => array(),
                            'selected' => array(),
                        ),
                    ));
                ?>
            </div>

        <?php endif; ?></h3>

        <div class="zp-Browse-Account-Options">

            <?php $is_default = false; if ( get_option("Zotpress_DefaultAccount") && get_option("Zotpress_DefaultAccount") == $api_user_id ) { $is_default = true; } ?>
            <a href="javascript:void(0);" rel="<?php echo esc_html($api_user_id); ?>" class="zp-Browse-Account-Default zp-Account-Default button button-secondary dashicons <?php if ( $is_default ) { echo "zp-IsDefaultAccount dashicons-star-filled disabled"; } else { echo "dashicons-star-empty"; } ?>"><?php if ( $is_default ) { esc_html_e('Default','zotpress'); } else { esc_html_e('Set as Default','zotpress'); } ?></a>

        </div>

        <span id="ZOTPRESS_PLUGIN_URL"><?php echo esc_url(ZOTPRESS_PLUGIN_URL); ?></span>

        <?php
        // echo $zpLib->getLib();
        echo wp_kses(
                $zpLib->getLib(),
                array(
                    'a' => array(
                        'id' => array(),
                        'class' => array(),
                        'name' => array()
                    ),
                    'p' => array(),
                    'i' => array(),
                    'b' => array(),
                    'em' => array(),
                    'strong' => array(),
                    'div' => array(
                        'id' => array(),
                        'class' => array(),
                        'style' => array()
                    ),
                    'span' => array(
                        'id' => array(),
                        'class' => array(),
                        'style' => array()
                    ),
                    'select' => array(
                        'id' => array(),
                        'class' => array(),
                        'style' => array(),
                        'name' => array(),
                    ),
                    'option' => array(
                        'id' => array(),
                        'class' => array(),
                        'rel' => array(),
                        'name' => array(),
                        'selected' => array(),
                        'value' => array()
                    ),
                ));
    ?>

    </div><!-- .zp-Browse-Wrapper -->

</div>


<?php } else { ?>

<div id="zp-Zotpress">

    <div id="zp-Setup">

        <div id="zp-Zotpress-Navigation">

            <div id="zp-Icon">
                <img src="<?php echo esc_url(ZOTPRESS_PLUGIN_URL); ?>/images/icon-64x64.png" title="<?php esc_html_e('Zotero + WordPress = Zotpress','zotpress'); ?>">
            </div>

            <div class="nav">
                <div id="step-1" class="nav-item nav-tab-active"><?php esc_html_e('System Check','zotpress'); ?></div>
            </div>

        </div><!-- #zp-Zotpress-Navigation -->

        <div id="zp-Setup-Step">

            <h3><?php esc_html_e('Welcome to Zotpress','zotpress'); ?></h3>

            <div id="zp-Setup-Check">

                <p>
                    <?php esc_html_e('Before we get started, let\'s make sure your system can support Zotpress','zotpress'); ?>:
                </p>

                <?php

                $zp_check_curl = (int) function_exists('curl_version');
                $zp_check_streams = (int) function_exists('stream_get_contents');
                $zp_check_fsock = (int) function_exists('fsockopen');

                if ( ($zp_check_curl + $zp_check_streams + $zp_check_fsock) <= 1 ) { ?>

                <div id="zp-Setup-Check-Message" class="error">
                    <p><strong><em><?php esc_html_e('Warning','zotpress'); ?>:</em></strong> <?php esc_html_e('Zotpress requires at least one of the following: <strong>cURL, fopen with Streams (PHP 5), or fsockopen</strong>. You will not be able to import items until your administrator or tech support has set up one of these options. cURL is recommended.','zotpress'); ?></p>
                </div>

                <?php } else { ?>

                <div id="zp-Setup-Check-Message" class="updated">
                    <p><strong><em><?php esc_html_e('Hurrah','zotpress'); ?>!</em></strong> <?php esc_html_e('Your system meets the requirements necessary for Zotpress to communicate with Zotero from WordPress','zotpress'); ?>.</p>
                </div>

                <p><?php esc_html_e('Sometimes systems aren\'t configured to allow communication with external websites. Let\'s check by accessing WordPress.org','zotpress'); ?>:

                <?php

                $response = wp_remote_get( "https://wordpress.org", array( 'headers' => array("Zotero-API-Version: 2") ) );

                if ( $response["response"]["code"] == 200 ) { ?>

                <script>

                jQuery(document).ready(function() {

                    jQuery("#zp-Connect-Next").removeAttr("disabled").click(function()
                    {
                        window.parent.location = "admin.php?page=Zotpress&setup=true";
                        return false;
                    });

                });

                </script>

                <div id="zp-Setup-Check-Message" class="updated">
                    <p><strong><em><?php esc_html_e('Great','zotpress'); ?>!</em></strong> <?php esc_html_e('We successfully connected to WordPress.org','zotpress'); ?>.</p>
                </div>

                <p><?php esc_html_e('Everything appears to check out. Let\'s continue setting up Zotpress by adding your Zotero account. Click "Next."','zotpress'); ?>

                <?php } else { ?>

                <div id="zp-Setup-Check-Message" class="error">
                    <p><strong><em><?php esc_html_e('Warning','zotpress'); ?>:</em></strong> <?php esc_html_e('Zotpress was not able to connect to WordPress.org','zotpress'); ?>.</p>
                </div>

                <p><?php esc_html_e('Unfortunately, Zotpress ran into an error. Here\'s what WordPress has to say','zotpress'); ?>: <?php if ( is_wp_error($response) ) { echo esc_html($response->get_error_message()); } else { esc_html_e("Sorry, but there's no details on the error",'zotpress'); } ?>.</p>

                <p><?php esc_html_e('First, try reloading. If the error recurs, your system may not be set up to run Zotpress. Please contact your system administrator or website host and ask about allowing PHP scripts to access content like RSS feeds from external websites through cURL, fopen with Streams (PHP 5), or fsockopen','zotpress'); ?>.</p>

                <p><?php esc_html_e('You can still try to use Zotpress, but it may not work and/or you may encounter further errors','zotpress'); ?>.</p>

                <script>

                jQuery(document).ready(function() {

                    jQuery("#zp-Connect").removeAttr("disabled").click(function()
                    {
                        window.parent.location = "admin.php?page=Zotpress&setup=true";
                        return false;
                    });

                });

                </script>

                <?php }
                } ?>

            </div>

            <div class="proceed">
                <input id="zp-Connect-Next" name="zp-Connect" class="button-primary" type="submit" value="<?php esc_html_e('Next','zotpress'); ?>" tabindex="5" disabled="disabled">
            </div>

        </div>

    </div>

</div>

<?php } ?>