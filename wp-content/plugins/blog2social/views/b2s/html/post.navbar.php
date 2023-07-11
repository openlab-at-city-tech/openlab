<?php
$getPage = sanitize_text_field(wp_unslash($_GET['page']));
$getType = (isset($_GET['type']) && !empty($_GET['type'])) ? sanitize_text_field(wp_unslash($_GET['type'])) : 'link';
$isPremiumInfo = (B2S_PLUGIN_USER_VERSION == 0) ? 'b2s-btn-disabled' : '';
require_once(B2S_PLUGIN_DIR . 'includes/B2S/Post/Tools.php');
$noticeCount = B2S_Post_Tools::countNewNotifications(B2S_PLUGIN_BLOG_USER_ID);
$approveCount = B2S_Post_Tools::countReadyForApprove(B2S_PLUGIN_BLOG_USER_ID);
?>
<!--Navbar Start-->
<div class="col-md-12 pull-left del-padding-left">
    <a class="b2s-bold btn btn-<?php echo ($getPage == 'blog2social-post') ? 'primary' : 'outline-dark'; ?>" href="admin.php?page=blog2social-post"><?php esc_html_e('Share WordPress Content', 'blog2social') ?></a>
    <a class="b2s-bold btn btn-<?php echo ($getPage == 'blog2social-curation' && $getType == 'link') ? 'primary' : 'outline-dark'; ?> b2s-curation-link" href="admin.php?page=blog2social-curation&type=link"><?php esc_html_e('Share New Link Post', 'blog2social') ?></a>
    <a class="b2s-bold btn btn-<?php echo ($getPage == 'blog2social-curation' && $getType == 'text') ? 'primary' : 'outline-dark'; ?> b2s-curation-text" href="admin.php?page=blog2social-curation&type=text"><?php esc_html_e('Share New Text Post', 'blog2social') ?></a>
    <a class="b2s-bold btn btn-<?php echo ($getPage == 'blog2social-curation' && $getType == 'image') ? 'primary' : 'outline-dark'; ?> b2s-curation-image" href="admin.php?page=blog2social-curation&type=image"><?php esc_html_e('Share New Image Post', 'blog2social') ?></a>
    <a class="b2s-bold btn btn-<?php echo ($getPage == 'blog2social-video') ? 'primary' : 'outline-dark'; ?> b2s-curation-video" href="admin.php?page=blog2social-video"><?php esc_html_e('Share New Video Post', 'blog2social') ?></a>
    </div>

<?php
if(isset($_GET['page']) && $_GET['page'] == "blog2social-publish"){
    echo '<a href="#" id="b2s-delete-modal-btn" class="btn btn-primary b2s-bold b2s-delete-post-btn btn-sm">' .  esc_html__('Mass delete posts', 'blog2social') .' </a> ';

}
?>

<hr class="pull-left">

<?php if ($getPage != 'blog2social-curation') { ?>
    <div class="hidden-lg hidden-md hidden-sm filterShow"><a href="#" onclick="showFilter('show');return false;"><i class="glyphicon glyphicon-chevron-down"></i><?php esc_html_e('filter', 'blog2social') ?></a></div>
    <div class="hidden-lg hidden-md hidden-sm filterHide"><a href="#" onclick="showFilter('hide');return false;"><i class="glyphicon glyphicon-chevron-up"></i><?php esc_html_e('filter', 'blog2social') ?></a></div>
    <!--Navbar Ende-->
<?php } 