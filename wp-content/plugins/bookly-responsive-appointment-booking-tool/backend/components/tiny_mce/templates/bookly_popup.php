<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div id="bookly-tinymce-popup" style="display: none">
    <form id="bookly-short-code-form">
        <table>
            <?php static::renderTemplate( 'bookly_form' ) ?>
            <tr>
                <td></td>
                <td class='wp-core-ui'>
                    <button class="button button-primary bookly-js-insert-shortcode" type="button"><?php esc_html_e( 'Insert', 'bookly' ) ?></button>
                </td>
            </tr>
        </table>
    </form>
</div>
<style type="text/css">
    #bookly-short-code-form { margin-top: 15px; }
    #bookly-short-code-form table { width: 100%; }
    #bookly-short-code-form table td select { width: 100%; margin-bottom: 5px; }
    .bookly-media-icon {
        display: inline-block;
        width: 16px;
        height: 16px;
        vertical-align: text-top;
        margin: 0 2px;
        background: url("<?php echo plugins_url( 'resources/images/calendar.png', __DIR__ ) ?>") 0 0 no-repeat;
    }
    #TB_overlay { z-index: 100001 !important; }
    #TB_window { z-index: 100002 !important; }
</style>