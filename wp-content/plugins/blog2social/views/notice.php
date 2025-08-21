<!--after Install-->
<?php wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce'); ?>
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
                    <br>
                    <button id="b2s-debug-connection-btn" class="btn btn-info"><?php esc_html_e('Debug Connection', 'blog2social') ?></button>
                    <div class="b2s-loading-area width-100" style="display: none;">
                        <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                        <div class="text-center b2s-loader-text"><?php esc_html_e("diagnosis in progress...", "blog2social"); ?></div>
                    </div>
                    <div class="b2s-debug-connection-result-area" style="display: none;">
                        <h4><?php esc_html_e('Debug Information', 'blog2social') ?>:</h4>
                        <div class="b2s-debug-connection-result-code"></div>
                        <div class="b2s-debug-connection-result-code-info">
                            <p><?php esc_html_e('Please copy the debug information and contact us directy via form.', 'blog2social') ?></p>
                        </div>
                        <div class="b2s-debug-connection-result-error">
                            <p><?php esc_html_e('It seems that your server IP address is blocking the Blog2Social address, please contact your system administrator who will unblock the Blog2Social address.', 'blog2social') ?></p>
                        </div>
                    </div>

                <?php } else if (defined("B2S_PLUGIN_NOTICE") && B2S_PLUGIN_NOTICE == "UPDATE") { ?> 
                    <h3><?php esc_html_e('Update...', 'blog2social') ?></h3>
                    <br> 
                    <?php esc_html_e('A new version of Blog2Social is available. Update now Blog2Social to continue to use the latest version of the plugin.', 'blog2social') ?>
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




