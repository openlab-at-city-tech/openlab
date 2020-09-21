<?php
/**
 * @var C_Marketing_Block_Card $block
 * @var string $link_text
 */
?>
<div class="wp-block-column upsell ngg-block-card">

    <div class="ngg-block-card-title">
        <img src="<?php print esc_attr($block->icon); ?>"
             alt="<?php print esc_attr($block->title); ?>"/>
        <h2>
            <?php print $block->title; ?>
        </h2>
    </div>

    <p>
        <?php print $block->description; ?>
    </p>

    <div class="wp-block-buttons">
        <div class="wp-block-button">
            <?php // Allow 'empty' cards to be generated to maintain two-column layouts ?>
            <?php if (!empty($block->title) || !empty($block->description)) { ?>
                <a class="wp-block-button__link has-text-color has-background no-border-radius"
                   href="<?php print esc_attr($block->get_upgrade_link()); ?>"
                   style="background-color:#9ebc1b;color:#ffffff" target="_blank"
                   rel="noreferrer noopener">
                    <?php print $link_text; ?>
                </a>
            <?php } ?>
        </div>
    </div>
</div>