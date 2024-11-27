<?php
$updateMail = get_option('B2S_UPDATE_MAIL_' . B2S_PLUGIN_BLOG_USER_ID);
?>
<?php if ($updateMail == false || empty($updateMail)) { ?>
    <div class="form-inline">
        <label class="b2s-text-xl b2s-color-white"><?php esc_html_e("Blog2Social News", "blog2social") ?></label>
        <input id="b2s-mail-update-input" class="form-control input-sm" name="b2sMailUpdate" value="<?php echo esc_html($wpUserData->user_email); ?>" placeholder="E-Mail" type="text">
        <button class="btn btn-success b2s-font-bold b2s-btn-dashboard-filled b2s-mail-btn"><?php esc_html_e('subscribe', 'blog2social') ?></button>
    </div>
    <input type="hidden" id="user_lang" value="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)) ?>">
    <?php
} 
   
