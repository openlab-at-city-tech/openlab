<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div id="bookly-tbs" class="wrap">
    <div id="bookly-wpml-resave-notice" class="alert alert-warning" data-action="bookly_dismiss_wpml_resave_notice">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div class="form-row">
            <div class="mr-3"><i class='fas fa-exclamation-triangle fa-2x'></i></div>
            <div class="col">
                <?php printf( esc_html__( 'If you use WPML and notice that some translations don\'t appear on front end, you will need to restore them. Go to WPML, select strings within %s domain, choose any translation, and click the "Save" button.', 'bookly' ), '<b>bookly</b>' ) ?>
            </div>
        </div>
    </div>
</div>