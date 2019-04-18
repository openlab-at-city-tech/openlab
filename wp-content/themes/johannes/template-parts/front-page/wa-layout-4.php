
<?php if ( johannes_get( 'wa_display_title' ) ): ?>
    <div class="section-head">
        <h3 class="section-title h2"><?php echo esc_html( __johannes('front_page_wa_title') ); ?></h3>
    </div>
<?php endif; ?>

<div class="johannes-section johannes-cover johannes-bg-alt-2 wa-layout wa-layout-4 size-johannes-wa-4">
    
    <?php if ( johannes_get( 'wa_img' ) ): ?>
        <div class="section-bg">
            <?php echo johannes_get( 'wa_img' ) ?>
        </div>
    <?php endif; ?>
    
    <div class="container">
        <div class="section-head johannes-content johannes-offset-bg">
            <h2 class="display-1"><?php echo __johannes( 'front_page_wa_punchline'); ?></h2>
            <?php echo wpautop(__johannes( 'front_page_wa_text')); ?>
            <?php if( johannes_get( 'wa_cta') ) : ?>
            <p><a href="<?php echo esc_url( johannes_get( 'wa_cta_url') ); ?>" class="johannes-button johannes-button-primary johannes-button-large"><?php echo __johannes( 'front_page_wa_cta_label'); ?></a></p>
            <?php endif; ?>
        </div>
    </div>
    
</div>