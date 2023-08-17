<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib;
?>
<!--
Plugin Name: Bookly – Responsive WordPress Appointment Booking and Scheduling Plugin
Plugin URI: https://www.booking-wp-plugin.com/?utm_source=bookly_admin&utm_medium=plugins_page&utm_campaign=plugins_page
Version: <?php echo Lib\Plugin::getVersion() ?>
-->
<?php include '_css.php' ?>
<div id="bookly-form-<?php echo esc_attr( $form_id ) ?>" class="bookly-form" data-form_id="<?php echo esc_attr( $form_id ) ?>">
    <div style="text-align: center"><img src="<?php echo includes_url( 'js/tinymce/skins/lightgray/img/loader.gif' ) ?>" alt="<?php esc_attr_e( 'Loading...', 'bookly' ) ?>"/></div>
</div>
<?php if ( get_option( 'bookly_gen_show_powered_by' ) ) : ?>
    <div class="powered-by-bookly"><?php esc_html_e( 'Powered by', 'bookly' ) ?>
        <a href="https://www.booking-wp-plugin.com/?utm_source=referral&utm_medium=booking_widget" target="_blank">Bookly</a> —
        <a href="https://www.booking-wp-plugin.com/?utm_source=referral&utm_medium=booking_widget" target="_blank">WordPress Booking Plugin</a>
    </div>
<?php endif ?>
<script type="text/javascript">
    (function (win, fn) {
        var done = false, top = true,
            doc = win.document,
            root = doc.documentElement,
            modern = doc.addEventListener,
            add = modern ? 'addEventListener' : 'attachEvent',
            rem = modern ? 'removeEventListener' : 'detachEvent',
            pre = modern ? '' : 'on',
            init = function(e) {
                if (e.type == 'readystatechange') if (doc.readyState != 'complete') return;
                (e.type == 'load' ? win : doc)[rem](pre + e.type, init, false);
                if (!done) { done = true; fn.call(win, e.type || e); }
            },
            poll = function() {
                try { root.doScroll('left'); } catch(e) { setTimeout(poll, 50); return; }
                init('poll');
            };
        if (doc.readyState == 'complete') fn.call(win, 'lazy');
        else {
            if (!modern) if (root.doScroll) {
                try { top = !win.frameElement; } catch(e) { }
                if (top) poll();
            }
            doc[add](pre + 'DOMContentLoaded', init, false);
            doc[add](pre + 'readystatechange', init, false);
            win[add](pre + 'load', init, false);
        }
    })(window, function() {
        window.bookly( <?php echo json_encode( $bookly_options ) ?> );
    });
</script>