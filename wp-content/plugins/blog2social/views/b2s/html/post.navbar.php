<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

$getPage = sanitize_text_field(wp_unslash(isset($_GET['page'])? $_GET['page'] : ""));
$getType = (isset($_GET['type']) && !empty($_GET['type'])) ? sanitize_text_field(wp_unslash($_GET['type'])) : 'link';
$isPremiumInfo = (B2S_PLUGIN_USER_VERSION == 0) ? 'b2s-btn-disabled' : '';
$isImagePro = (B2S_PLUGIN_USER_VERSION  < 2) ? ' <span class="label label-success">'. esc_html__('Pro', 'blog2social') .'</span>' : '';
require_once(B2S_PLUGIN_DIR . 'includes/B2S/Post/Tools.php');
$noticeCount = B2S_Post_Tools::countNewNotifications(B2S_PLUGIN_BLOG_USER_ID);
$approveCount = B2S_Post_Tools::countReadyForApprove(B2S_PLUGIN_BLOG_USER_ID);
?>

<div  class="pull-right">
    <div id="b2s-navbar-compose-collapsed-bar">
        <div class="navbar-share-button-container action-grid action-grid-sm">
            <button type="button" id="b2s-wordpress-expand-btn" class="btn b2s-navbar-compose-expand-btn action-card action-card-sm " onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=blog2social-post')); ?>';">
                <div class="icon icon-green icon-sm" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M8 13h8"/>
                        <path d="M8 17h8"/>
                        <path d="M8 9h3"/>
                    </svg>
                </div>
                <div class="content">
                    <h3><?php esc_html_e('Share WordPress content', 'blog2social'); ?></h3>
                </div>
            </button>
            <button type="button" id="b2s-navbar-compose-expand-btn" class="btn b2s-navbar-compose-expand-btn action-card action-card-sm" onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=blog2social-post&opencontent=1')); ?>';">
                <div class="icon icon-blue icon-sm" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                        stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round">
                        <path d="M12 5v14"/>
                        <path d="M5 12h14"/>
                    </svg>
                </div>
                <div class="content">
                    <h3><?php esc_html_e('Create new content', 'blog2social'); ?></h3>
                </div>
            </button>
        </div>
    </div>

    <hr class="pull-left">
</div>