<?php
if (!defined("ABSPATH")) {
    exit();
}
$title = get_the_title($item->comment_post_ID);

$canDeleteComment = (boolean) apply_filters("wpdiscuz_can_delete_comment", true, $item->comment_ID);
$commentLeftStyle = $canDeleteComment ? "" : "width:99%;border-right:none;";
?>
<div class="wpd-item">
    <div class="wpd-item-left" style="<?php echo $commentLeftStyle; ?>">
        <div class="wpd-item-link wpd-comment-meta">
            <i class="fas fa-user"></i>
            <?php echo esc_html($item->comment_author); ?> &nbsp; 
            <i class="fas fa-calendar-alt"></i> 
            <?php echo esc_html($this->getCommentDate($item)); ?>
        </div>
        <div class="wpd-item-link wpd-comment-item-link">
            <a class="wpd-comment-link" href="<?php echo esc_url_raw(get_comment_link($item)); ?>" target="_blank">
                <?php echo get_comment_excerpt($item->comment_ID); ?>
            </a>
        </div>
        <div class="wpd-item-link wpd-post-item-link">
            <span><?php echo esc_html($this->options->getPhrase("wc_user_settings_response_to")); ?></span>
            <a class="wpd-post-link" href="<?php echo esc_url_raw(get_permalink($item->comment_post_ID)); ?>" target="_blank" title="<?php echo esc_attr($title); ?>">
                <?php echo esc_html($title); ?>
            </a>
        </div>  
    </div>
    <?php if ($canDeleteComment) { ?>
        <div class="wpd-item-right">        
            <a href="#" class="wpd-delete-content wpd-not-clicked" data-wpd-content-id="<?php echo esc_attr($item->comment_ID); ?>" data-wpd-parent-id="<?php echo esc_attr($item->comment_parent); ?>" data-wpd-delete-action="wpdDeleteComment" title="<?php esc_attr_e($this->options->getPhrase("wc_delete_this_comment", ["comment" => $item])); ?>">
                <i class="fas fa-trash-alt"></i>
            </a>
        </div>
    <?php } ?>
    <div class="wpd-clear"></div>
</div>