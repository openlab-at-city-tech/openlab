<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Filter.php');
?>

<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <div class="col-md-9 del-padding-left del-padding-right">
                <!--Header|Start - Include-->
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                <!--Header|End-->
                <div class="clearfix"></div>
                <!--Content|Start-->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <!--Posts from Wordpress Start-->
                        <!--Filter Start-->
                        <div class="b2s-post">
                            <div class="grid-body">
                                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/post.navbar.php'); ?>
                                <!-- Filter Post Start-->
                                <form class="b2sSortForm form-inline pull-left" action="#">
                                    <input id="b2sType" type="hidden" value="draft-post" name="b2sType">
                                    <input id="b2sPagination" type="hidden" value="1" name="b2sPagination">
                                    <?php
                                    $postFilter = new B2S_Post_Filter('draft-post');
                                    echo $postFilter->getItemHtml();
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
                                            <button type="button" class="btn btn-primary b2s-post-per-page" data-post-per-page="25">25</button>
                                            <button type="button" class="btn btn-default b2s-post-per-page" data-post-per-page="50">50</button>
                                            <button type="button" class="btn btn-default b2s-post-per-page" data-post-per-page="100">100</button>
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
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
        </div>
    </div>
</div>

<input type="hidden" id="b2sLang" value="<?php echo substr(B2S_LANGUAGE, 0, 2); ?>">          
<input type="hidden" id="b2sUserLang" value="<?php echo strtolower(substr(get_locale(), 0, 2)); ?>">
<input type="hidden" id="user_version" name="user_version" value="<?php echo B2S_PLUGIN_USER_VERSION; ?>">
<input type="hidden" id="b2sServerUrl" value="<?php echo B2S_PLUGIN_SERVER_URL; ?>">
<input type="hidden" id="b2sPostId" value="">


<div class="modal fade b2s-delete-draft-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-delete-draft-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-delete-draft-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Delete Social Media Draft', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <b><?php esc_html_e('Are you sure you want to delete this Social Media draft?', 'blog2social') ?> </b>
                <input type="hidden" value="" id="b2s-delete-confirm-draft-id">
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
                <button class="btn btn-danger b2s-draft-delete-confirm-btn"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>
