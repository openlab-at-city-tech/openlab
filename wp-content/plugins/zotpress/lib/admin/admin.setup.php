<?php if (!isset( $_GET['setupstep'] )) { ?>

    <div id="zp-Setup">

        <div id="zp-Zotpress-Navigation">

            <div id="zp-Icon">
                <img src="<?php
    echo ZOTPRESS_PLUGIN_URL;
    ?>/images/icon-64x64.png" title="Zotero + WordPress = Zotpress">
            </div>

            <div class="nav">
                <div id="step-1" class="nav-item nav-tab-active"><strong>1.</strong> <?php
    _e('Validate Account','zotpress');
    ?></div>
                <div id="step-2" class="nav-item"><strong>2.</strong> <?php
    _e('Default Options','zotpress');
    ?></div>
            </div>

        </div><!-- #zp-Zotpress-Navigation -->

        <div id="zp-Setup-Step">

            <?php
    $zp_check_curl = (int) function_exists('curl_version');
    $zp_check_streams = (int) function_exists('stream_get_contents');
    $zp_check_fsock = (int) function_exists('fsockopen');
    if ( ($zp_check_curl + $zp_check_streams + $zp_check_fsock) <= 1 ) { ?>
            <div id="zp-Setup-Check" class="error">
        <p><strong><?php _e('Warning','zotpress'); ?>!</strong> <?php _e('Zotpress requires at least one of the following to work: cURL, fopen with Streams (PHP 5), or fsockopen. You will not be able to use Zotpress until your administrator or tech support has set up one of these options. cURL is recommended.','zotpress'); ?></p>
    </div>
    <?php }
    ?>

            <div id="zp-AddAccount-Form" class="visible">
                <?php
    include(__DIR__ . '/admin.accounts.addform.php');
    ?>
            </div>

        </div>

    </div>



<?php
} elseif (isset($_GET['setupstep']) && $_GET['setupstep'] == "two") {
    ?>

    <div id="zp-Setup">

        <div id="zp-Zotpress-Navigation">

            <div id="zp-Icon">
                <img src="<?php
    echo ZOTPRESS_PLUGIN_URL;
    ?>/images/icon-64x64.png" title="Zotero + WordPress = Zotpress">
            </div>

            <div class="nav">
                <div id="step-1" class="nav-item"><strong>1.</strong> <?php
    _e('Validate Account','zotpress');
    ?></div>
                <div id="step-2" class="nav-item nav-tab-active"><strong>2.</strong> <?php
    _e('Default Options','zotpress');
    ?></div>
            </div>

        </div><!-- #zp-Zotpress-Navigation -->

        <div id="zp-Setup-Step">

            <h3><?php
    _e('Set Default Options','zotpress');
    ?></h3>

            <?php
    include(__DIR__ . "/admin.options.form.php");
    ?>

            <div id="zp-Zotpress-Setup-Buttons" class="proceed">
                <input type="button" id="zp-Zotpress-Setup-Options-Complete" class="button-primary" value="<?php
    _e('Finish','zotpress');
    ?>">
            </div>

        </div>

    </div>

<?php
} ?>
