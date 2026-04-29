<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */
 
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
 
require_once(B2S_PLUGIN_DIR . 'includes/B2S/Onboarding/Item.php');
 
$onboarding = new B2S_Onboarding_Item();
$onboarding->startOnboarding();
 
$step = (isset($_GET['step']) && !empty($_GET['step'])) ? (int) $_GET['step'] : $onboarding->getStep() - 1;

?>
 
<div class="b2s-container">
    <div class="b2s-inbox col-md-12 del-padding-left">
        <?php require_once(B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
 
        <div class="col-md-8 del-padding-left del-padding-right">
            <?php require_once(B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
 
            <div class="clearfix"></div>
 
            <div class="b2s-onboarding-page b2s-onboarding-panel">
 
                <!-- Hero -->
                <section class="b2s-onboarding-hero">
                    <div class="b2s-onboarding-hero__content">
                        <span class="b2s-onboarding-badge"><?php esc_html_e("Getting started", "blog2social"); ?></span>
                        <h1 class="b2s-onboarding-title-main">
                            <?php esc_html_e("Welcome to Blog2Social", "blog2social"); ?>
                        </h1>
                        <p class="b2s-onboarding-subtitle">
                            <?php esc_html_e("Connect your networks, create your first post and explore smarter scheduling for your social media workflow in WordPress.", "blog2social"); ?>
                        </p>
                        <div class="b2s-onboarding-hero__actions">
                            <a href="<?php echo esc_url(B2S_Tools::getSupportLink('trial')); ?>" target="_blank" class="btn b2s-btn-ob-primary">
                                <?php esc_html_e("Try Premium", "blog2social"); ?>
                            </a>
                            <a href="#" class="btn b2s-btn-ob-ghost b2s-stop-onboarding-link">
                                <?php esc_html_e("Skip tour", "blog2social"); ?>
                            </a>
                        </div>
                    </div>
                </section>
 
                <!-- Step checklist -->
                <section class="b2s-onboarding-steps">
                    <p class="b2s-onboarding-steps__label"><?php esc_html_e("Your steps", "blog2social"); ?></p>
 
                    <!-- 3-step wizard bar -->
                    <div class="b2s-stepwizard-bar">
                        <div class="b2s-stepwizard-item <?php echo ($step >= 1) ? 'done' : (($step === 0) ? 'active' : ''); ?>">
                            <div class="b2s-stepwizard-dot">
                                <?php if ($step >= 1) : ?>
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M2.5 7L5.5 10L11.5 4" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <?php else : ?>1<?php endif; ?>
                            </div>
                            <span class="b2s-stepwizard-label"><?php esc_html_e("Connect your social networks with Blog2Social", "blog2social"); ?></span>
                        </div>
                        <div class="b2s-stepwizard-item <?php echo ($step >= 2) ? 'done' : (($step === 1) ? 'active' : ''); ?>">
                            <div class="b2s-stepwizard-dot">
                                <?php if ($step >= 2) : ?>
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M2.5 7L5.5 10L11.5 4" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <?php else : ?>2<?php endif; ?>
                            </div>
                            <span class="b2s-stepwizard-label"><?php esc_html_e("Share your first post", "blog2social"); ?></span>
                        </div>
                        <div class="b2s-stepwizard-item <?php echo ($step >= 3) ? 'done' : (($step === 2) ? 'active' : ''); ?>">
                            <div class="b2s-stepwizard-dot">
                                <?php if ($step >= 3) : ?>
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M2.5 7L5.5 10L11.5 4" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <?php else : ?>3<?php endif; ?>
                            </div>
                            <span class="b2s-stepwizard-label"><?php esc_html_e("Try Blog2Social Premium", "blog2social"); ?></span>
                        </div>
                    </div><!-- /.b2s-stepwizard-bar -->
 
                    <!-- Step content: image left, text right -->
                    <?php
                    $step_data = array(
                        0 => array(
                            'img'   => plugins_url('/assets/images/b2s/b2s_onboarding_step1.png', B2S_PLUGIN_FILE),
                            'title' => __('Connect your social networks', 'blog2social'),
                            'text'  => __('Link your social media accounts with Blog2Social and enable cross-platform publishing directly from WordPress.', 'blog2social'),
                            'btn'   => __('Connect', 'blog2social'),
                            'href'  => admin_url('admin.php?page=blog2social-network'),
                        ),
                        1 => array(
                            'img'   => plugins_url('/assets/images/b2s/b2s_onboarding_step2.png', B2S_PLUGIN_FILE),
                            'title' => __('Share your first post', 'blog2social'),
                            'text'  => __('Choose a WordPress post and share it on your connected social media channels.', 'blog2social'),
                            'btn'   => __('Share post', 'blog2social'),
                            'href'  => admin_url('admin.php?page=blog2social-post'),
                        ),
                        2 => array(
                            //'img'   => plugins_url('/assets/images/b2s/b2s_onboarding_step2.png', B2S_PLUGIN_FILE),
                            'title' => __('Try Blog2Social Premium', 'blog2social'),
                            'text'  => __('Discover auto-posting, best-time scheduling, post templates, and video sharing in the 30-day trial.', 'blog2social'),
                            'btn'   => __('Try Premium', 'blog2social'),
                            'href'  => B2S_Tools::getSupportLink('trial'),
                        ),
                    );
                    $current = isset($step_data[$step]) ? $step_data[$step] : $step_data[0];
                    ?>
                    <div class="b2s-onboarding-step-content-wrap">
                        <div class="b2s-onboarding-step-img-col">
                            <?php if(!empty($current['img'])){ ?>
                                <img src="<?php echo esc_url($current['img']); ?>"
                                    alt="<?php echo esc_attr($current['title']); ?>"
                                    class="b2s-onboarding-step-img">
                            <?php 
                            } ?>
                        </div>
                        <div class="b2s-onboarding-step-text-col">
                            <h2 class="b2s-onboarding-step-heading"><?php echo esc_html($current['title']); ?></h2>
                            <p class="b2s-onboarding-step-desc"><?php echo esc_html($current['text']); ?></p>
                            <a href="<?php echo esc_url($current['href']); ?>"
                               class="btn b2s-btn-ob-primary"
                               <?php echo ($step === 2) ? 'target="_blank"' : ''; ?>>
                                <?php echo esc_html($current['btn']); ?>
                            </a>
                        </div>
                    </div><!-- /.b2s-onboarding-step-content-wrap -->
 
                    <?php
                    // getOnboardingHtml still handles internal step tracking / state
                    echo wp_kses(
                        $onboarding->getOnboardingHtml($step),
                        array(
                            'div'   => array('class' => array(), 'id' => array(), 'data-toggle' => array(), 'style' => array()),
                            'img'   => array('src' => array(), 'alt' => array(), 'class' => array(), 'style' => array()),
                            'a'     => array('label' => array(), 'id' => array(), 'href' => array(), 'type' => array(), 'class' => array(), 'target' => array()),
                            'p'     => array('class' => array()),
                            'h4'    => array('class' => array()),
                            'input' => array('class' => array(), 'type' => array(), 'id' => array()),
                            'label' => array('class' => array(), 'for' => array()),
                            'small' => array(),
                            'br'    => array(),
                        )
                    );
                    ?>
                </section>
 
                <!-- Premium section -->
                <div class="b2s-onboarding-premium-section">
                    <section class="b2s-onboarding-features-wrap">
 
                        <div class="b2s-onboarding-section-head">
                            <h2><?php esc_html_e("Unlock more with Premium", "blog2social"); ?></h2>
                            <p><?php esc_html_e("Get access to advanced scheduling, automation and customization tools for your social media workflow.", "blog2social"); ?></p>
                        </div>
 
                        <div class="b2s-onboarding-feature-grid">
                            <div class="b2s-feature-card">
                                <img class="b2s-feature-card__icon" src="<?php echo esc_url(plugins_url('/assets/images/features/calendar-icon.png', B2S_PLUGIN_FILE)); ?>" alt="">
                                <h3><?php esc_html_e("Best Time Manager", "blog2social"); ?></h3>
                                <p><?php esc_html_e("Schedule your posts with predefined best times or set your own publishing schedule.", "blog2social"); ?></p>
                            </div>
                            <div class="b2s-feature-card">
                                <img class="b2s-feature-card__icon" src="<?php echo esc_url(plugins_url('/assets/images/features/automatic-icon.png', B2S_PLUGIN_FILE)); ?>" alt="">
                                <h3><?php esc_html_e("Auto Posting", "blog2social"); ?></h3>
                                <p><?php esc_html_e("Publish posts automatically at the time of publishing or on your custom schedule.", "blog2social"); ?></p>
                            </div>
                            <div class="b2s-feature-card">
                                <img class="b2s-feature-card__icon" src="<?php echo esc_url(plugins_url('/assets/images/features/lamp-icon.png', B2S_PLUGIN_FILE)); ?>" alt="">
                                <h3><?php esc_html_e("Post Templates", "blog2social"); ?></h3>
                                <p><?php esc_html_e("Create reusable post structures to customize your posts per network automatically.", "blog2social"); ?></p>
                            </div>
                            <div class="b2s-feature-card">
                                <img class="b2s-feature-card__icon" src="<?php echo esc_url(plugins_url('/assets/images/features/megafon-icon.png', B2S_PLUGIN_FILE)); ?>" alt="">
                                <h3><?php esc_html_e("Share Video Files", "blog2social"); ?></h3>
                                <p><?php esc_html_e("Publish video content directly from your media library to video platforms and networks.", "blog2social"); ?></p>
                            </div>
                        </div>
 
                        <div class="b2s-onboarding-cta-card">
                            <div class="b2s-onboarding-cta-card__text">
                                <h3><?php esc_html_e("Start your 30-day Premium trial", "blog2social"); ?></h3>
                                <p><?php esc_html_e("Explore advanced features and grow your reach with smarter publishing workflows.", "blog2social"); ?></p>
                            </div>
                            <div class="b2s-onboarding-cta-actions">
                                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('trial')); ?>" target="_blank" class="btn b2s-btn-ob-primary">
                                    <?php esc_html_e("Try Premium", "blog2social"); ?>
                                </a>
                                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('upgrade_version')); ?>" target="_blank" class="btn b2s-btn-ob-secondary">
                                    <?php esc_html_e("Buy Premium", "blog2social"); ?>
                                </a>
                            </div>
                        </div>
 
                    </section>
                </div>
 
                <!-- Footer -->
                <div class="b2s-onboarding-footer-action">
                    <a href="#" class="b2s-stop-onboarding-link">
                        <?php esc_html_e("Exit tour and go to dashboard", "blog2social"); ?>
                    </a>
                </div>
 
            </div><!-- /.b2s-onboarding-panel -->
        </div>
    </div>
</div>
 

