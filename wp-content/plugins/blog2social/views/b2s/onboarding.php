<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');

require_once(B2S_PLUGIN_DIR . 'includes/B2S/Onboarding/Item.php');

$onboarding = new B2S_Onboarding_Item();
$onboarding->startOnboarding();
?>

<div class="b2s-container">
    <div class=" b2s-inbox col-md-12 del-padding-left">
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
        <div class="col-md-8 del-padding-left del-padding-right">
            <!--Header|Start - Include-->
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
            <!--Header|End-->
            <div class="clearfix"></div>
            <!--Content|Start-->
            <div class="panel panel-default">
                <h1 class="text-center"><?php esc_html_e("Welcome to Blog2Social", "blog2social") ?></h1>
                <div class="text-center"> <?php esc_html_e("Thank you for choosing Blog2Social - all-in-one social media management Wordpress tool.", "blog2social") ?></div>
                <?php
                $step = (isset($_GET['step']) && !empty($_GET['step'])) ? (int) $_GET['step'] : 0;
                echo wp_kses($onboarding->getOnboardingHtml($step), array(
                    'div' => array(
                        'class' => array(),
                        'id' => array(),
                        'data-toggle' => array(),
                        'style' => array(),
                    ),
                    'img' => array(
                        'src' => array(),
                        'alt' => array(),
                        'class' => array(),
                        'style' => array(),
                    ),
                    'a' => array(
                        'label' => array(),
                        'id' => array(),
                        'href' => array(),
                        'type' => array(),
                        'class' => array(),
                        'target' => array()
                    ),
                    'p' => array(
                        'class' => array(),
                    ),
                    'h4' => array(
                        'class' => array(),
                    ),
                    'input' => array(
                        'class' => array(),
                        'type' => array(),
                        'id' => array(),
                    ),
                    'label' => array(
                        'class' => array(),
                        'for' => array(),
                    ),
                    'small' => array(),
                    'br' => array(),
                    'link' => array(
                        'href' => array(),
                        'rel' => array(),
                        'id' => array(),
                    ),
                ));
                ?>

                <div class="b2s-onboarding-grey-background">
                    <div class="text-center b2s-onboarding-title"><?php esc_html_e("Try Blog2Social Premium with more awesome features for scheduling and sharing 30-days for free.", "blog2social") ?></div>
                    <div class="row text-center b2s-onboarding-features">
                        <div class="col-sm-2 b2s-onboarding-features-box shadow-sm rounded">
                            <img class="b2s-onboarding-features-img" src="<?php echo esc_url(plugins_url('/assets/images/features/calendar-icon.png', B2S_PLUGIN_FILE)) ?>">
                            <br>
                            <?php esc_html_e("Best Time Manager", "blog2social") ?>
                            <div class="b2s-onboarding-features-text">

                                <?php esc_html_e("Schedule your social media posts with pre-defined best-times or at your own time settings for auto-scheduling.", "blog2social") ?>
                            </div>
                        </div>
                        <div class="col-sm-2 b2s-onboarding-features-box shadow-sm rounded">
                            <img class="b2s-onboarding-features-img" src="<?php echo esc_url(plugins_url('/assets/images/features/automatic-icon.png', B2S_PLUGIN_FILE)) ?>">
                            <br>
                            <?php esc_html_e("Auto Posting", "blog2social") ?>
                            <div class="b2s-onboarding-features-text">

                                <?php esc_html_e("Automatically share posts at the time of publishing or at any scheduled time.", "blog2social") ?>
                            </div>
                        </div>
                        <div class="col-sm-2 b2s-onboarding-features-box shadow-sm rounded">
                            <img class="b2s-onboarding-features-img" src="<?php echo esc_url(plugins_url('/assets/images/features/lamp-icon.png', B2S_PLUGIN_FILE)) ?>">
                            <br>
                            <?php esc_html_e("Post Templates", "blog2social") ?>
                            <div class="b2s-onboarding-features-text">

                                <?php esc_html_e("Define a unique post structure to automatically customize your social media posts.", "blog2social") ?>
                            </div>
                        </div>
                        <div class="col-sm-2 b2s-onboarding-features-box shadow-sm rounded">
                            <img class="b2s-onboarding-features-img" src="<?php echo esc_url(plugins_url('/assets/images/features/megafon-icon.png', B2S_PLUGIN_FILE)) ?>">
                            <br>
                            <?php esc_html_e("Share Video Files", "blog2social") ?>
                            <div class="b2s-onboarding-features-text">
                                <?php esc_html_e("Share your video content straight from your media library on video platforms and social networks.", "blog2social") ?>
                            </div>
                        </div>
                    </div>
                    <div class="b2s-onboarding-premium text-center">
                        <?php esc_html_e("Try Blog2Social Premium with many more great features and increase your visibility and reach on social media.", "blog2social") ?>

                        <div class="row text-center">
                            <div class="col-sm-6 b2s-onboarding-premium-left">
                                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('trial')) ?>" target="blank_" class="btn text-center b2s-onboarding-button-filled "><?php esc_html_e("Try Premium", "blog2social") ?></a>

                            </div>
                            <div class="col-sm-6 b2s-onboarding-premium-right">
                                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('pricing')) ?>" target="blank_" class="btn text-center b2s-onboarding-button"><?php esc_html_e("Buy Premium", "blog2social") ?></a>

                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="btn text-center b2s-stop-onboarding b2s-stop-onboarding-link"><?php esc_html_e("Exit Tour & go to Dashboard", "blog2social") ?></a>
            </div>
        </div>
    </div>