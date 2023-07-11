<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
/* Data */
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Filter.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
require_once B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php';
require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
$b2sShowByDate = isset($_GET['b2sShowByDate']) ? (preg_match("#^[0-9\-.\]]+$#", trim(sanitize_text_field(wp_unslash($_GET['b2sShowByDate'])))) ? trim(sanitize_text_field(wp_unslash($_GET['b2sShowByDate']))) : "") : ""; //YYYY-mm-dd
$b2sUserAuthId = isset($_GET['b2sUserAuthId']) ? (int) $_GET['b2sUserAuthId'] : 0;
$b2sPostBlogId = isset($_GET['b2sPostBlogId']) ? (int) $_GET['b2sPostBlogId'] : 0;
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionPostFilters = $options->_getOption('post_filters');
$postsPerPage = (isset($optionPostFilters['postsPerPage']) && (int) $optionPostFilters['postsPerPage'] > 0) ? (int) $optionPostFilters['postsPerPage'] : 25;
?>

<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
            <div class="col-md-9 del-padding-left del-padding-right">
                <!--Header|Start - Include-->
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                <!--Header|End-->
                <div class="clearfix"></div>
                <!--Navbar|Start-->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/post.navbar.php'); ?>
                    </div>
                </div>
                <!--Navbar|End-->
                <div class="clearfix"></div>
                <!--Content|Start-->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <!--Posts from Wordpress Start-->
                        <!--Filter Start-->
                        <div class="b2s-post">
                            <div class="grid-body">
                                <!-- Filter Post Start-->
                                <form class="b2sSortForm form-inline pull-left" action="#">
                                    <input id="b2sType" type="hidden" value="approve" name="b2sType">
                                    <input id="b2sShowByDate" type="hidden" value="<?php echo esc_attr($b2sShowByDate); ?>" name="b2sShowByDate">
                                    <input id="b2sUserAuthId" type="hidden" value="<?php echo esc_attr($b2sUserAuthId); ?>" name="b2sUserAuthId">
                                    <input id="b2sPostBlogId" type="hidden" value="<?php echo esc_attr($b2sPostBlogId); ?>" name="b2sPostBlogId">
                                    <input id="b2sPagination" type="hidden" value="1" name="b2sPagination">
                                    <?php
                                    $postFilter = new B2S_Post_Filter('approve');
                                    echo wp_kses($postFilter->getItemHtml('blog2social-approve'), array(
                                        'div' => array(
                                            'class' => array()
                                        ),
                                        'input' => array(
                                            'id' => array(),
                                            'name' => array(),
                                            'class' => array(),
                                            'value' => array(),
                                            'type' => array(),
                                            'placeholder' => array(),
                                        ),
                                        'a' => array(
                                            'href' => array(),
                                            'id' => array(),
                                            'class' => array()
                                        ),
                                        'span' => array(
                                            'class' => array()
                                        ),
                                        'small' => array(),
                                        'select' => array(
                                            'id' => array(),
                                            'name' => array(),
                                            'class' => array()
                                        ),
                                        'option' => array(
                                            'value' => array()
                                        )
                                    ));
                                    ?>
                                </form>
                                <!-- Filter Post Ende-->
                                <br/>
                            </div>       
                        </div>
                        <div class="clearfix"></div> 
                        <!--Filter End-->
                        <div class="b2s-sort-area">
                            <div class="b2s-loading-area" style="display:none">
                                <br>
                                <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                <div class="clearfix"></div>
                                <div class="text-center b2s-loader-text"><?php esc_html_e("Loading...", "blog2social"); ?></div>
                            </div>
                            <div class="row b2s-sort-result-area">
                                <div class="col-md-12">
                                    <ul class="list-group b2s-sort-result-item-area"></ul>
                                    <br>
                                    <nav class="b2s-sort-pagination-area text-center">
                                        <div class="btn-group btn-group-sm pull-right b2s-post-per-page-area hidden-xs" role="group">
                                            <button type="button" class="btn <?php echo ((int) $postsPerPage == 25) ? "btn-primary" : "btn-default" ?> b2s-post-per-page" data-post-per-page="25">25</button>
                                            <button type="button" class="btn <?php echo ((int) $postsPerPage == 50) ? "btn-primary" : "btn-default" ?> b2s-post-per-page" data-post-per-page="50">50</button>
                                            <button type="button" class="btn <?php echo ((int) $postsPerPage == 100) ? "btn-primary" : "btn-default" ?> b2s-post-per-page" data-post-per-page="100">100</button>
                                        </div>
                                        <div class="b2s-sort-pagination-content"></div>
                                    </nav>
                                    <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php'); ?> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="b2sLang" value="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)); ?>">          
<input type="hidden" id="b2sUserLang" value="<?php echo esc_attr(strtolower(substr(get_locale(), 0, 2))); ?>">
<input type="hidden" id="user_version" name="user_version" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION); ?>">
<input type="hidden" id="b2sServerUrl" value="<?php echo esc_url(B2S_PLUGIN_SERVER_URL); ?>">
<input type="hidden" id="b2sPostId" value="">


<div class="modal fade b2s-delete-approve-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-delete-approve-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-delete-approve-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Delete Social Media Posts', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <b><?php esc_html_e('Are you sure you want to delete these Social Media posts?', 'blog2social') ?> </b>
                <br>
                (<?php esc_html_e('Number of entries', 'blog2social') ?>:  <span id="b2s-delete-confirm-post-count"></span>)
                <input type="hidden" value="" id="b2s-delete-confirm-post-id">
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
                <button class="btn btn-danger b2s-approve-delete-confirm-btn"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade b2s-publish-approve-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-publish-approve-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e('Do you want to mark this post as published ?', 'blog2social') ?> </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" value="" id="b2s-approve-blog-post-id">
                <input type="hidden" value="" id="b2s-approve-post-id">
                <button class="btn btn-success b2s-approve-publish-confirm-btn"><?php esc_html_e('YES', 'blog2social') ?></button>
                <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>
