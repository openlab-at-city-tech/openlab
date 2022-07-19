<?php
$updateMail = get_option('B2S_UPDATE_MAIL_' . B2S_PLUGIN_BLOG_USER_ID);
?>
<?php if ($updateMail == false || empty($updateMail)) { ?>
    <div class="form-inline">
        <label class="b2s-text-xl b2s-color-grey"><?php esc_html_e("Get Social Media News", "blog2social") ?></label>
        <div class="input-group input-group-sm">
            <input id="b2s-mail-update-input" class="form-control" name="b2sMailUpdate" value="<?php echo esc_html($wpUserData->user_email); ?>" placeholder="E-Mail" type="text">
            <span class="input-group-btn">
                <button class="btn btn-primary b2s-mail-btn"><?php esc_html_e('subscribe', 'blog2social') ?></button>
            </span>
        </div>
    </div>
    <input type="hidden" id="user_lang" value="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)) ?>">
    <?php
} 
   
