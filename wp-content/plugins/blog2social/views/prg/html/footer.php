<!--Footer Start-->
<div class="b2s-footer">
    <div class="pull-left hidden-xs <?php echo isset($noLegend) ? 'hide' : ''; ?>">
        <?php if (!B2S_System::isblockedArea('B2S_MENU_FOOTER', B2S_PLUGIN_ADMIN)) { ?>
            <small> © <?php echo date('Y'); ?> <a target="_blank" href="http://www.adenion.de" rel="nofollow">Adenion GmbH</a></small>
        <?php } ?>
    </div>
    <div class="pull-right hidden-xs <?php echo isset($noLegend) ? 'hide' : ''; ?>">
        <small>
            <img class="img-width-9" src="<?php echo esc_url(plugins_url('/assets/images/prg/post-icon.png', B2S_PLUGIN_FILE)); ?>" alt="beitrag"> <?php esc_html_e('Post', 'blog2social') ?> 
            <img class="img-width-9" src="<?php echo esc_url(plugins_url('/assets/images/prg/job-icon.png', B2S_PLUGIN_FILE)); ?>" alt="job"> <?php esc_html_e('Job', 'blog2social') ?>
            <img class="img-width-9" src="<?php echo esc_url(plugins_url('/assets/images/prg/event-icon.png', B2S_PLUGIN_FILE)); ?>" alt="event"> <?php esc_html_e('Event', 'blog2social') ?>
            <img class="img-width-9" src="<?php echo esc_url(plugins_url('/assets/images/prg/product-icon.png', B2S_PLUGIN_FILE)); ?>" alt="product"> <?php esc_html_e('Product', 'blog2social') ?>
        </small>
    </div>
</div>
<!--Footer Ende-->