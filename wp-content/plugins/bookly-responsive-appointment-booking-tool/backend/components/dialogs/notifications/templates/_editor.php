<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Ace;
?>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="float-left mt-2"><?php esc_html_e( 'Body', 'bookly' ) ?></label>
            <ul class="nav nav-tabs justify-content-end mr-2<?php if ( !user_can_richedit() ) : ?> bookly-collapse<?php endif ?>" style="border-bottom: none;">
                <li class="nav-item">
                    <a class="nav-link active" href="#bookly-wp-editor-pane" data-toggle="bookly-tab" data-tinymce><?php esc_html_e( 'Visual', 'bookly' ) ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#bookly-ace-editor-pane" data-toggle="bookly-tab" data-ace><?php esc_html_e( 'Text', 'bookly' ) ?></a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="bookly-wp-editor-pane" class="tab-pane<?php if ( user_can_richedit() ) : ?> active<?php endif ?>">
                    <?php wp_editor( '', 'bookly-js-message', array(
                        'textarea_name' => 'notification[message]',
                        'media_buttons' => false,
                        'editor_height' => 250,
                        'default_editor' => 'tinymce',
                        'quicktags' => false,
                        'editor_css' => '<style>.wp-editor-tools{margin-top:-27px;}.wp-editor-tools [type="button"]{box-sizing:content-box!important;}</style>',
                        'tinymce' => array(
                            'resize' => true,
                            'wp_autoresize_on' => true,
                        ),
                    ) ) ?>
                </div>
                <div id="bookly-ace-editor-pane" class="tab-pane<?php if ( !user_can_richedit() ) : ?> active<?php endif ?>">
                    <?php Ace\Editor::render( 'bookly-notifications' ) ?>
                    <?php if ( !user_can_richedit() ) : ?>
                    <input type="hidden" name="notification[message]" />
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php static::renderTemplate( '_attach' ) ?>