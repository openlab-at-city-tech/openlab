<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
$assConnected = false;
global $wpdb;
if ($wpdb->get_var($wpdb->prepare("SELECT `id`, `access_token` FROM `{$wpdb->prefix}b2s_user_tool` WHERE `blog_user_id` = %d AND `tool_id` = 1", (int) B2S_PLUGIN_BLOG_USER_ID))) {
    $sqlResult = $wpdb->get_row($wpdb->prepare("SELECT `id`, `access_token` FROM `{$wpdb->prefix}b2s_user_tool` WHERE `blog_user_id` = %d AND `tool_id` = 1", (int) B2S_PLUGIN_BLOG_USER_ID));
    if (isset($sqlResult->id) && (int) $sqlResult->id > 0 && isset($sqlResult->access_token) && !empty($sqlResult->access_token)){
        $assConnected = true;
    }
}
?>

<input type="hidden" id="b2sServerUrl" value="<?php echo esc_url(B2S_PLUGIN_SERVER_URL); ?>">

<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>

            <div class="col-md-9 del-padding-left del-padding-right">
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                <div class="clearfix"></div>

                <div class="b2s-ai-page">

                    <!-- HERO -->
                    <div class="b2s-ai-hero">
                        <div class="b2s-ai-hero-inner">
                            <div class="b2s-ai-hero-left">
                                <div class="b2s-ai-hero-badge">
                                    <?php esc_html_e('AI Assistant', 'blog2social'); ?>
                                </div>
                                <h1><?php esc_html_e('Welcome to the Blog2Social AI Assistant', 'blog2social'); ?></h1>
                                <p class="b2s-ai-hero-desc">
                                    <?php esc_html_e('Discover how Assistini AI takes your social media posts to the next level. Creative ideas, optimized texts, better performance - for Instagram, Twitter, Facebook and LinkedIn.', 'blog2social'); ?>
                                </p>
                                <div class="b2s-ai-connect-row">
                                    <?php
                                    $default_label   = __('Connect with Assistini AI now', 'blog2social');
                                    $connected_label = __('Connected with Assistini AI', 'blog2social');
                                    ?>
                                    <a class="b2s-ass-register-btn b2s-ai-btn-connect <?php echo esc_attr($assConnected ? 'is-connected b2s-btn-disabled b2s-ass-connected' : ''); ?>"
                                       href="#"
                                       data-default-label="<?php echo esc_attr($default_label); ?>"
                                       data-connected-label="<?php echo esc_attr($connected_label); ?>">
                                        <?php echo $assConnected ? esc_html($connected_label) : esc_html($default_label); ?>
                                    </a>
                                    <button type="button"
                                            class="b2s-ass-logout-btn b2s-ai-btn-logout btn-link"
                                            style="<?php echo esc_attr($assConnected ? '' : 'display:none;'); ?>">
                                        <?php esc_html_e('log out', 'blog2social'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="b2s-ai-hero-visual hidden-sm hidden-xs">
                                <img src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-welcome.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini AI">
                            </div>
                        </div>
                    </div>

                    <div class="b2s-ai-content">

                        <!-- HOW IT WORKS -->
                        <div>
                            <div class="b2s-ai-section-label">
                                <h2><?php esc_html_e('How does the AI assistant work in Blog2Social?', 'blog2social'); ?></h2>
                                <span class="b2s-ai-pill"><?php esc_html_e('3 Steps', 'blog2social'); ?></span>
                            </div>
                            <div class="b2s-ai-steps">
                                <div class="b2s-ai-step-card">
                                    <div class="b2s-ai-step-num">01</div>
                                    <h3><?php esc_html_e('Create and schedule a social media post', 'blog2social'); ?></h3>
                                    <p><?php esc_html_e('Create your social media post in Blog2Social as usual.', 'blog2social'); ?></p>
                                </div>
                                <div class="b2s-ai-step-card">
                                    <div class="b2s-ai-step-num">02</div>
                                    <h3><?php esc_html_e('Optimize your existing text with the AI assistant', 'blog2social'); ?></h3>
                                    <p><?php esc_html_e('Click the button "Rewrite with Assistini AI" and Assistini will handle the rest. Within moments the AI assistant will generate a customized caption for the selected network.', 'blog2social'); ?></p>
                                </div>
                                <div class="b2s-ai-step-card">
                                    <div class="b2s-ai-step-num">03</div>
                                    <h3><?php esc_html_e('Share your post', 'blog2social'); ?></h3>
                                    <p><?php esc_html_e('Now, you can post your content to your social media networks as usual.', 'blog2social'); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- PREVIEW STRIP -->
                        <div class="b2s-ai-preview-strip">
                            <div class="b2s-ai-preview-img hidden-xs">
                                <img src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-rewrite.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini Rewrite">
                            </div>
                            <div class="b2s-ai-preview-copy">
                                <p class="b2s-ai-eyebrow"><?php esc_html_e('AI-powered', 'blog2social'); ?></p>
                                <h2><?php esc_html_e('Work smarter, create better social media posts', 'blog2social'); ?></h2>
                                <p><?php esc_html_e('Assistini AI is your personal time saver for creating content. Write more than just social media posts - create blog posts, newsletter, multilingual content and much more!', 'blog2social'); ?></p>
                            </div>
                        </div>

                        <!-- FEATURES -->
                        <div>
                            <div class="b2s-ai-section-label">
                                <h2><?php esc_html_e('More than social media posts - use the full power of Assistini', 'blog2social'); ?></h2>
                            </div>
                            <div class="b2s-ai-features-grid">
                                <div class="b2s-ai-feature-card">
                                    <div class="b2s-ai-feature-icon">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/ass/tool_1.png', B2S_PLUGIN_FILE)); ?>" alt="">
                                    </div>
                                    <h4><?php esc_html_e('AI based content ideas', 'blog2social'); ?></h4>
                                </div>
                                <div class="b2s-ai-feature-card">
                                    <div class="b2s-ai-feature-icon">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/ass/tool_2.png', B2S_PLUGIN_FILE)); ?>" alt="">
                                    </div>
                                    <h4><?php esc_html_e('Contextual writing', 'blog2social'); ?></h4>
                                </div>
                                <div class="b2s-ai-feature-card">
                                    <div class="b2s-ai-feature-icon">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/ass/tool_3.png', B2S_PLUGIN_FILE)); ?>" alt="">
                                    </div>
                                    <h4><?php esc_html_e('Optimization of style and tone', 'blog2social'); ?></h4>
                                </div>
                                <div class="b2s-ai-feature-card">
                                    <div class="b2s-ai-feature-icon">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/ass/tool_4.png', B2S_PLUGIN_FILE)); ?>" alt="">
                                    </div>
                                    <h4><?php esc_html_e('Search engine optimized texts', 'blog2social'); ?></h4>
                                </div>
                                <div class="b2s-ai-feature-card">
                                    <div class="b2s-ai-feature-icon">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/ass/tool_5.png', B2S_PLUGIN_FILE)); ?>" alt="">
                                    </div>
                                    <h4><?php esc_html_e('Translation', 'blog2social'); ?></h4>
                                </div>
                            </div>
                        </div>

                        <!-- CTA BANNER -->
                        <div class="b2s-ai-cta-banner">
                            <div class="b2s-ai-cta-copy">
                                <h3><?php esc_html_e('The full version of Assistini can help you work even more efficiently', 'blog2social'); ?></h3>
                                <p><?php esc_html_e('Assistini offers you everything you need for your content creation. Write more than just social media posts - create blog posts, newsletter, multilingual content and much more!', 'blog2social'); ?></p>
                            </div>
                            <a class="b2s-ai-btn-cta" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url(B2S_Tools::getSupportLink('assistini_website')); ?>">
                                <?php esc_html_e('learn more', 'blog2social'); ?> &#8594;
                            </a>
                        </div>

                    </div><!-- /b2s-ai-content -->
                </div><!-- /b2s-ai-page -->

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