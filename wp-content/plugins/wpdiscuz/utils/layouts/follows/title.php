<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<li class='wpd-list-item' data-action="wpdGetFollowsPage">
    <i class='fas fa-rss'></i>
    <span><?php echo esc_html($this->options->getPhrase("wc_user_settings_follows")); ?></span>
    <input class='wpd-rel' type='hidden' value='wpd-content-item-3'/>
</li>