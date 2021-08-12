<?php
$getPage = $_GET['page'];
$isPremiumInfo = (B2S_PLUGIN_USER_VERSION == 0) ? 'b2s-btn-disabled' : '';
require_once(B2S_PLUGIN_DIR . 'includes/B2S/Post/Tools.php');
$noticeCount = B2S_Post_Tools::countNewNotifications(B2S_PLUGIN_BLOG_USER_ID);
$approveCount = B2S_Post_Tools::countReadyForApprove(B2S_PLUGIN_BLOG_USER_ID);
?>
<!--Navbar Start-->
<div class="col-md-12 pull-left b2s-post-menu del-padding-left">
    <a class="btn btn-<?php echo ($getPage == 'blog2social-post') ? 'primary' : 'link'; ?> b2s-post-btn b2s-post-all" href="admin.php?page=blog2social-post"><?php esc_html_e('All Posts', 'blog2social') ?></a>
    <?php if ($getPage != "blog2social-curation" && $getPage != "blog2social-curation-draft") { ?>
        <a class="btn btn-<?php echo ($getPage == 'blog2social-favorites') ? 'primary' : 'link'; ?> b2s-post-btn b2s-post-favorites" href="admin.php?page=blog2social-favorites"><?php esc_html_e('Favorites', 'blog2social') ?></a>
        <a class="btn btn-<?php echo ($getPage == 'blog2social-draft-post') ? 'primary' : 'link'; ?> b2s-post-btn b2s-post-draft-post" href="admin.php?page=blog2social-draft-post"><?php esc_html_e('Drafts', 'blog2social') ?></a>
    <?php } else { ?>
        <a class="btn btn-<?php echo ($getPage == 'blog2social-curation-draft') ? 'primary' : 'link'; ?> b2s-post-btn b2s-post-draft" href="admin.php?page=blog2social-curation-draft"><?php esc_html_e('Drafts', 'blog2social') ?></a>
    <?php } ?>
    <a class="btn btn-<?php echo ($getPage == 'blog2social-approve') ? 'primary' : 'link'; ?> b2s-post-btn b2s-post-approve" href="admin.php?page=blog2social-approve"><?php esc_html_e('Instant Sharing', 'blog2social'); if($approveCount > 0) {echo ' <span class="b2s-notice-counter">' . $approveCount . '</span>';} ?></a>
    <a class="btn btn-<?php echo ($getPage == 'blog2social-sched') ? 'primary' : 'link'; ?> b2s-post-btn b2s-post-sched" href="admin.php?page=blog2social-sched"><?php esc_html_e('Scheduled Posts', 'blog2social') ?> <?php echo (!empty($isPremiumInfo) ? '<span class="label label-success">' . esc_html__("SMART", "blog2social") . '</span>' : '' ); ?>  </a>
    <?php if ($getPage != "blog2social") { ?>
        <a class="btn btn-<?php echo ($getPage == 'blog2social-publish') ? 'primary' : 'link'; ?> b2s-post-btn b2s-post-publish" href="admin.php?page=blog2social-publish"><?php esc_html_e('Shared Posts', 'blog2social') ?></a>
        <a class="btn btn-link b2s-post-btn b2s-post-repost" href="admin.php?page=blog2social-repost"><?php esc_html_e('Re-Share Posts', 'blog2social') ?> <?php echo (!empty($isPremiumInfo) ? '<span class="label label-success">' . esc_html__("SMART", "blog2social") . '</span>' : '' ); ?>  </a>
        <a class="btn btn-<?php echo ($getPage == 'blog2social-notice') ? 'primary' : 'link'; ?> b2s-post-btn b2s-post-notice" href="admin.php?page=blog2social-notice"><?php esc_html_e('Notifications', 'blog2social'); if($noticeCount > 0) {echo ' <span class="b2s-notice-counter">' . $noticeCount . '</span>';} ?></a>
        <a class="btn btn-<?php echo ($getPage == 'blog2social-calendar') ? 'primary' : 'link'; ?> b2s-post-btn" href="admin.php?page=blog2social-calendar"><?php esc_html_e('Calendar', 'blog2social') ?> <?php echo (!empty($isPremiumInfo) ? '<span class="label label-success">' . esc_html__("SMART", "blog2social") . '</span>' : '' ); ?>  </a>
    <?php } ?>
</div>
<hr class="pull-left">

<?php if ($getPage != 'blog2social-curation') { ?>
    <div class="hidden-lg hidden-md hidden-sm filterShow"><a href="#" onclick="showFilter('show');return false;"><i class="glyphicon glyphicon-chevron-down"></i><?php esc_html_e('filter', 'blog2social') ?></a></div>
    <div class="hidden-lg hidden-md hidden-sm filterHide"><a href="#" onclick="showFilter('hide');return false;"><i class="glyphicon glyphicon-chevron-up"></i><?php esc_html_e('filter', 'blog2social') ?></a></div>
    <!--Navbar Ende-->
<?php } 