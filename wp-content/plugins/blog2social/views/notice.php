<!--after Install-->
<div class="b2s-container">
    <div class="col-xs-12 col-md-offset-4 col-md-4">
        <div class="panel panel-group">
            <div class="panel-body text-center">
                <!--Logo-->
                <a target="_blank" href="https://www.blog2social.com">
                    <img class="img-error img-responsive" src="<?php echo esc_url(plugins_url('/assets/images/b2s/b2s_logo.png', B2S_PLUGIN_FILE)); ?>" alt="blog2social">
                </a>
                <?php if (defined("B2S_PLUGIN_NOTICE") && B2S_PLUGIN_NOTICE == "CONNECTION") { ?>
                    <small>ERROR-CODE: <?php echo esc_html(B2S_PLUGIN_NOTICE); ?></small>
                    <h3><?php esc_html_e('Connection is broken...', 'blog2social') ?></h3>
                    <br>    
                    <?php esc_html_e('The connection to your server has been interrupted. Please make sure that your blog is reachable. If your server does not respond or is too slow, Blog2Social cannot connect to the internet. Try again later or contact your webmaster, if this error message persists.', 'blog2social') ?>
                    <br>
                <?php } else if (defined("B2S_PLUGIN_NOTICE") && B2S_PLUGIN_NOTICE == "UPDATE") { ?> 
                    <h3><?php esc_html_e('Update...', 'blog2social') ?></h3>
                    <br> 
                    <?php _e('<b> A new version of Blog2Social is available. </b> Update now <br> Blog2Social to continue to use the latest version of the plugin.', 'blog2social') ?>
                    <br>
                    <br>
                    <?php $updateUrl = get_option('home') . ((substr(get_option('home'), -1, 1) == '/') ? '' : '/') . 'wp-admin/plugins.php'; ?>
                    <a class="btn btn-link btn-lg" href="<?php echo esc_url($updateUrl); ?>"><?php esc_html_e('Update Blog2Social', 'blog2social') ?></a>
                    <br>
                <?php } else { ?>
                    <h3><?php esc_html_e('Unknown error', 'blog2social') ?></h3>
                    <br> 
                    <b> <?php esc_html_e('An unknown error occurred!', 'blog2social'); ?> </b><br>
                    <?php esc_html_e('Please contact our support!', 'blog2social') ?>
                    <br>
                <?php } ?>
                <br>
            </div>
        </div>
    </div>    
</div>




