<div class="wrap">
    <h2>
        <div id="icon-<?php echo CMTT_MENU_OPTION; ?>" class="icon32">
            <br />
        </div>
        <?php _e(CMTT_NAME); ?>
    </h2>

    <?php CMTooltipGlossaryBackend::cmtt_showNav(); ?>

    <?php echo $content; ?>
</div>