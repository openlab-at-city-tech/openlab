<?php if (!defined('ABSPATH')) {
    die('No direct access.');
} ?>

<div class="metaslider-ui metaslider metaslider_themeEditor">
    <div class="wrap">
        <div class="metaslider-start mt-16">
            <div class="metaslider-welcome">
                <div class="welcome-panel-content items-center">
                    <h2>
                        <?php _e( 'Introducing the Theme Editor in MetaSlider Pro', 'ml-slider' ); ?>
                    </h2>
                </div>
                <div class="welcome-panel-content" style="min-height:270px;">
                    <div class="ms-panel-container items-center">
                        <div>
                            <p>
                                <img src="<?php echo esc_url( METASLIDER_ADMIN_ASSETS_URL . 'images/hero-theme-editor.jpg' ) ?>" alt="<?php _e( 'Theme Editor', 'ml-slider' ); ?>">
                            </p>
                        </div>
                        <div>
                            <h3 class="ms-heading leading-tight">
                                <?php _e( 'Create your own MetaSlider Themes', 'ml-slider' ); ?>
                            </h3>
                            <p>
                                <?php _e( 'The Theme Editor is an easy and powerful way to design themes that exactly match your site.', 'ml-slider' ); ?>
                            </p>
                            <p>
                                <?php _e( 'The Theme Editor allows you to customize almost every aspect of your slideshow themes. You can change the arrows, navigation, captions, Play / Pause button, and other slideshow details.', 'ml-slider' ); ?>
                            </p>
                            <p>
                                <?php _e( 'Upgrade to MetaSlider Pro and you can create a beautiful WordPress slideshow in minutes.', 'ml-slider' ); ?>
                            </p>
                            <p>
                                <a href="https://www.metaslider.com/upgrade?utm_source=lite&utm_medium=banner&utm_campaign=pro" class="ml-upgrade-button w-auto" target="_blank">
                                    <?php _e( 'Upgrade now', 'ml-slider' ); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>