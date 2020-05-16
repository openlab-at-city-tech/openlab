<?php if ($errors): ?>
        <?php foreach ($errors as $msg): ?>
            <?php echo $msg ?>
        <?php endforeach ?>
    <?php endif ?>
    <?php if ($success AND empty($errors)): ?>
        <div class='success updated'>
            <p><?php esc_html_e($success);?></p>
        </div>
<?php endif ?>

<div class="wrap ngg_settings_page" id='ngg_page_content' style='position: relative; visibility: hidden;'>

    <div class="ngg_page_content_header ">
        <img src='<?php esc_html_e($logo) ?>' class='ngg_admin_icon'>
        <h3><?php esc_html_e($page_heading)?></h3>
        <?php echo $header_message; ?>
    </div>
    
    <form method="POST" action="<?php echo nextgen_esc_url($_SERVER['REQUEST_URI'])?>">
        <?php if (isset($form_header)): ?>
            <?php echo $form_header."\n"; ?>
        <?php endif ?>
        <?php if (isset($nonce)): ?>
        <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce)?>"/>
        <?php endif ?>
        <input type="hidden" name="action"/>
         <!-- <div class="accordion" id="nextgen_admin_accordion"> -->
            <div class="ngg_page_content_menu"">
                <?php foreach($forms as $form): ?>
                    <a href='javascript:void(0)' data-id='<?php esc_attr_e( $form->get_id() ) ?>'><?php esc_html_e( str_replace( array("NextGEN ", "NextGen "), "", $form->get_title())) ?></a>
                <?php endforeach ?>
            </div>
            <div class="ngg_page_content_main"">
                <?php foreach($forms as $form): ?>
                    <div data-id='<?php esc_attr_e($form->get_id()) ?>'>
                        <h3><?php esc_html_e( str_replace( array("NextGEN ", "NextGen "), "", $form->get_title())) ?></h3>
                        <?php echo $form->render(TRUE); ?>
                    </div>
                <?php endforeach ?>
            </div>
        <!-- </div> -->
        <?php if ($show_save_button): ?>
            <p>
                <button type="submit" name='action_proxy' data-proxy-value="save" value="Save" class="button-primary ngg_save_settings_button"><?php _e('Save Options', 'nggallery'); ?></button>
            </p>
        <?php endif ?>
    </form>
</div>
