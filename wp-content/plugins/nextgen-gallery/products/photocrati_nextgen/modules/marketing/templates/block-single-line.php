<?php
/**
 * @var C_Marketing_Block_Single_Line $block
 * @var string $link_text
 */
?>
<div class="ngg-marketing-single-line">
    <p>
        <?php print $block->title; ?>
        &nbsp;
        <a class="ngg-marketing-single-line-link"
           href="<?php print esc_attr($block->get_upgrade_link()); ?>"
           target="_blank"
           rel="noreferrer noopener">
            <?php print $link_text; ?>
        </a>
    </p>
</div>