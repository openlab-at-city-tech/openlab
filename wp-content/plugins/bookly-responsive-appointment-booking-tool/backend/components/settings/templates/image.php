<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div id="bookly-js-<?php echo esc_attr( $option_name ) ?>">
    <input type="hidden" name="<?php echo esc_attr( $option_name ) ?>" data-default="<?php echo esc_attr( $option_value ) ?>" value="<?php echo esc_attr( $option_value ) ?>">
    <div class="bookly-js-image bookly-thumb <?php echo esc_attr( $class ) ?>" style="<?php echo esc_attr( $img_style ) ?>" data-style="<?php echo esc_attr( $img_style ) ?>">
        <?php if ( current_user_can( 'upload_files' ) ) : ?>
            <a class="far fa-fw fa-trash-alt text-danger bookly-thumb-delete" href="javascript:void(0)" style="<?php echo esc_attr( $delete_style ) ?>" title="<?php esc_attr_e( 'Delete', 'bookly' ) ?>"></a>
            <div class="bookly-thumb-edit">
                <label class="bookly-thumb-edit-btn"><?php esc_html_e( 'Image', 'bookly' ) ?></label>
            </div>
        <?php endif ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        $('#bookly-js-<?php echo esc_attr( $option_name ) ?> label').on('click', function () {
            var frame = wp.media({
                library: {type: 'image'},
                multiple: false
            });
            frame.on('select', function () {
                var selection = frame.state().get('selection').toJSON(),
                    img_src
                ;
                if (selection.length) {
                    if (selection[0].sizes['full'] !== undefined) {
                        img_src = selection[0].sizes['full'].url;
                    } else {
                        img_src = selection[0].url;
                    }
                    $('[name=<?php echo esc_attr( $option_name ) ?>]').val(selection[0].id);
                    $('#bookly-js-<?php echo esc_attr( $option_name ) ?> .bookly-js-image').css({'background-image': 'url(' + img_src + ')', 'background-size': 'contain'});
                    $('#bookly-js-<?php echo esc_attr( $option_name ) ?> .bookly-thumb-delete').show();
                    $(this).hide();
                }
            });
            frame.open();
        });

        $('#bookly-js-<?php echo esc_attr( $option_name ) ?>')
        .on('click', '.bookly-thumb-delete', function () {
            var $thumb = $(this).closest('.bookly-js-image');
            $thumb.attr('style', '');
            $('[name=<?php echo esc_attr( $option_name ) ?>]').val('');
        });
    });
</script>