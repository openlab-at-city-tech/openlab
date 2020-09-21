<?php
/**
 * @var C_Marketing_Block_Popup $block
 * @var string $link_text
 */ ?>
<div class="wp-block-group has-very-dark-gray-color has-text-color has-background upsell ngg-block-popup"
     style="border: none">
    <div class="wp-block-group__inner-container">
        <div class="wp-block-columns">
            <div class="wp-block-column" style="flex-basis:33.33%">

                <?php
                // Detect if we're using a FontAwesome icon. If the string begins with 'fas' it's not a URL anyway..
                if (strpos($block->thumbnail_url, 'fa-') === 0) { ?>
                    <div style="text-align: center;">
                        <i class="fas <?php print $block->thumbnail_url; ?>"></i>
                    </div>
                <?php } else { ?>
                    <div class="wp-block-image">
                        <figure class="aligncenter size-large">
                            <picture class="wp-image-52320">
                                <img src="<?php print esc_attr($block->thumbnail_url); ?>"
                                     alt="<?php print esc_attr($block->title); ?>">
                            </picture>
                        </figure>
                    </div>
                <?php } ?>
            </div>
            <div class="wp-block-column" style="flex-basis:66.66%">
                <h3 class="has-very-dark-gray-color has-text-color">
                    <?php print $block->title; ?>
                </h3>
                <p class="has-text-color has-very-dark-gray-color">
                    <?php print $block->description; ?>
                </p>
                <p>
                    <?php print $block->footer; ?>
                </p>
            </div>
        </div>
        <div class="wp-block-buttons">
            <div class="wp-block-button">
                <a class="wp-block-button__link has-text-color has-background no-border-radius"
                   href="<?php print $block->get_upgrade_link(); ?>"
                   style="background-color: #9ebc1b; color:#ffffff"
                   target="_blank"
                   rel="noreferrer noopener">
                    <?php print $link_text; ?>
                </a>
            </div>
        </div>
    </div>
</div>