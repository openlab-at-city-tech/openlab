<?php defined('ABSPATH') || die; ?>

<div class="advgb-header" style="padding-top: 40px">
    <h1 class="header-title"><?php esc_html_e('Blocks Pro', 'advanced-gutenberg'); ?></h1>
</div>

<div class="tab-content clearfix">
    <div id="pp-blocks-menu-wrapper" class="postbox" style="box-shadow: none;">
        <div class="pp-blocks-menus-promo">
            <div class="pp-blocks-menus-promo-inner">
                <img src="<?php echo esc_url(plugins_url('assets/images/pro-placeholder.jpg', dirname(dirname(__FILE__)))) ?>" class="pp-blocks-desktop" />
                <img src="<?php echo esc_url(plugins_url('assets/images/pro-placeholder.jpg', dirname(dirname(__FILE__)))) ?>" class="pp-blocks-mobile" />
                <div class="pp-blocks-menus-promo-content">
                    <p>
                        <?php _e('You can use Countdown, Pricing Table and Feature List blocks. These blocks are available in PublishPress Blocks Pro', 'advanced-gutenberg'); ?>
                    </p>
                    <p>
                        <a href="https://publishpress.com/links/blocks-banner" target="_blank">
                            <?php _e('Upgrade to Pro', 'advanced-gutenberg'); ?>
                        </a>
                    </p>
                </div>
                <div class="pp-blocks-menus-promo-gradient"></div>
            </div>
        </div>
    </div>
</div>
