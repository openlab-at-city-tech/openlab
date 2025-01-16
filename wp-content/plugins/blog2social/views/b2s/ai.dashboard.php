<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
$b2sSiteUrl = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/');
?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
            <div class="col-md-9 del-padding-left del-padding-right">
                <!--Header|Start - Include-->
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                <!--Header|End-->
                <div class="clearfix"></div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="b2s-ass-title-strong"><?php esc_html_e("Welcome to the Blog2Social AI assistant", "blog2social"); ?></h4>  
                                <br>
                                <p><?php esc_html_e("Discover how the AI text assistant Assistini can take your social media posts to the next level. Assistini AI provides you with creative ideas and optimizes your texts to improve the performance of your social media posts and the interaction with your followers. Whether you post on Instagram, Twitter, Facebook or LinkedIn - Assistini is your reliable creative partner.", "blog2social"); ?></p>
                                <br>
                                <a class="b2s-ass-register-btn text-center" target="_blank" href="https://b2s.li/wp-plugin-assistini-login"><?php esc_html_e('Connect with Assistini AI now', 'blog2Social'); ?></a>
                            </div>
                            <div class="col-md-6 hidden-sm hidden-xs text-center">
                                <img class="b2s-ass-img-welcome" src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-welcome.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini"> 
                            </div>                            
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-6 hidden-sm hidden-xs text-center">
                                <img class="img-responsive" src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-rewrite.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini"> 
                            </div>                            
                            <div class="col-md-6">
                                <h4 class="b2s-ass-title-strong"><?php esc_html_e("How does the AI assistant work in Blog2Social?", "blog2social"); ?></h4>  
                                <p><?php esc_html_e("Work smarter, create better social media posts. Assistini AI is your personal time saver for creating content.", "blog2social"); ?></p>
                                <br>
                                <h5 class="b2s-ass-title-h5">1. <?php esc_html_e("Create and schedule a social media post", "blog2social"); ?></h5>
                                <p><?php esc_html_e("Create your social media post in Blog2Social as usual.", "blog2social"); ?></p>
                                <h5 class="b2s-ass-title-h5">2. <?php esc_html_e("Optimize your existing text with the AI assistant", "blog2social"); ?></h5>
                                <p><?php esc_html_e("In your editor you can optimize your text using Assistini AI. Click the button \"Rewrite with Assistini AI\" and Assistini will handle the rest. Within moments the AI assistant will generate a customized caption for the selected network.", "blog2social"); ?></p>
                                <h5 class="b2s-ass-title-h5">3. <?php esc_html_e("Share your post", "blog2social"); ?></h5>
                                <p><?php esc_html_e("Now, you can post your content to your social media networks as usual.", "blog2social"); ?></p>
                                <br>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <h4 class="b2s-ass-title-strong"><?php esc_html_e("More than social media posts - use the full power of Assistini", "blog2social"); ?></h4>  
                                <p><?php esc_html_e("Assistini offers you everything you need for your content creation. Write more than just social media posts - create blog posts, newsletter, multilingual content and much more!", "blog2social"); ?></p>
                                <br>
                                <p><?php esc_html_e("The full version of Assistini can help you work even more efficiently and offers you a number of features", "blog2social"); ?></p>
                                <br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-4 text-center">
                                <div class="thumbnail b2s-ass-thumbnail-dashboard">
                                    <img src="<?php echo esc_url(plugins_url('/assets/images/ass/tool_1.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini">
                                    <div class="caption">
                                        <h4><?php esc_html_e("AI based content ideas", "blog2social"); ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 text-center">
                                <div class="thumbnail b2s-ass-thumbnail-dashboard">
                                    <img src="<?php echo esc_url(plugins_url('/assets/images/ass/tool_2.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini">
                                    <div class="caption">
                                        <h4><?php esc_html_e("Contextual writing", "blog2social"); ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 text-center">
                                <div class="thumbnail b2s-ass-thumbnail-dashboard">
                                    <img src="<?php echo esc_url(plugins_url('/assets/images/ass/tool_3.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini">
                                    <div class="caption">
                                        <h4><?php esc_html_e("Optimization of style and tone", "blog2social"); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-4 col-md-offset-2 text-center">
                                <div class="thumbnail b2s-ass-thumbnail-dashboard">
                                    <img src="<?php echo esc_url(plugins_url('/assets/images/ass/tool_4.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini">
                                    <div class="caption">
                                        <h4><?php esc_html_e("Search engine optimized texts", "blog2social"); ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 text-center">
                                <div class="thumbnail b2s-ass-thumbnail-dashboard">
                                    <img src="<?php echo esc_url(plugins_url('/assets/images/ass/tool_5.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini">
                                    <div class="caption">
                                        <h4><?php esc_html_e("Translation", "blog2social"); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row text-center">
                            <a class="b2s-ass-register-btn" target="_blank" href="https://b2s.li/wp-plugin-assistini-website"><?php esc_html_e('learn more', 'blog2Social'); ?></a>
                        </div>


                    </div>
                </div>
                <div class="clearfix"></div>

            </div>
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
        </div>
    </div>
    <div class="col-md-12">
        <?php
        $noLegend = 1;
        require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php');
        ?>
    </div>
</div>