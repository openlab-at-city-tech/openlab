<div class="wrap">
    <h2>
        <div id="icon-<?php echo CMTT_MENU_OPTION; ?>" class="icon32">
            <br />
        </div>
        <?php _e(CMTT_NAME, 'cm-tooltip-glossary'); ?> (<?php _e(CMTT_VERSION, 'cm-tooltip-glossary'); ?>)
    </h2>

    <?php CMTT_Free::cmtt_showNav(); ?>

    <?php echo $content; ?>
</div>