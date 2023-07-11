<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
/* Data */
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Filter.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Metrics/Item.php');
require_once(B2S_PLUGIN_DIR . 'includes/Options.php');

$metrics = new B2S_Metrics_Item();
$networkCount = $metrics->getNetworkCount();

require_once (B2S_PLUGIN_DIR . 'includes/Options.php');
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionMetricsStarted = $options->_getOption('metrics_started');
if ($optionMetricsStarted !== false) {
    $optionMetricsStarted = true;
}
if (isset($_GET['metrics_banner']) && (int) $_GET['metrics_banner'] == 1) {
    $options->_setOption('metrics_banner', true);
}
$optionMetricsFeedback = $options->_getOption('metrics_feedback');

$options = new B2S_Options((int) B2S_PLUGIN_BLOG_USER_ID);
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
                <!--Content|Start-->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="b2s-loading-area">
                            <br>
                            <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                            <div class="clearfix"></div>
                            <div class="text-center b2s-loader-text"><?php esc_html_e("Loading...", "blog2social"); ?></div>
                        </div>
                        <div class="b2s-metrics-area" style="display:none">
                            <!--Filter Start-->
                            <div class="">
                                <div class="grid-body">
                                    <!-- Filter Post Start-->
                                    <form class="b2sSortForm form-inline pull-left" action="#">
                                        <input id="b2sType" type="hidden" value="publish" name="b2sType">
                                        <input id="b2sPagination" type="hidden" value="1" name="b2sPagination">
                                        <div class="col-md-10 b2s-calendar-filter form-inline del-padding-left del-padding-right">
                                            <div class="b2s-calendar-filter-network-list" style="display: block ruby;">
                                                <label><input type="radio" class="b2s-calendar-filter-network-btn" checked="" name="b2s-calendar-filter-network-btn" value="all"><span><?php esc_html_e('all', 'blog2social') ?></span></label>
                                                <label><input type="radio" class="b2s-calendar-filter-network-btn" name="b2s-calendar-filter-network-btn" value="1"><span><img class="b2s-calendar-filter-img" alt="Facebook" src="<?php echo esc_url(plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE)); ?>"></span></label>
                                                <label><input type="radio" class="b2s-calendar-filter-network-btn" name="b2s-calendar-filter-network-btn" value="2"><span><img class="b2s-calendar-filter-img" alt="Twitter" src="<?php echo esc_url(plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE)); ?>"></span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-2 padding-right-0">
                                            <a class="btn btn-success pull-right b2s-metrics-feedback-btn"><?php esc_html_e("Feedback", "blog2social"); ?></a>
                                        </div>
                                    </form>
                                    <!-- Filter Post Ende-->
                                    <br>
                                </div>       
                            </div>
                            <div class="clearfix"></div>

                            <!-- Metric Summary -->
                            <div class="b2s-sort-area">
                                <span style="font-weight: bold; font-size: 24px;"><?php esc_html_e('Social Media Metrics Summary', 'blog2social') ?></span><a class="btn btn-link b2s-metrics-legend-info-modal-btn"><?php esc_html_e('Info', 'blog2social') ?></a>
                                <div class="b2s-activity-search-content pull-right">
                                    <input class="form-control" id="b2s-metrics-date-picker" value="<?php echo (substr(B2S_LANGUAGE, 0, 2) == 'de') ? date('d.m.Y', strtotime("-1 week")) : date('Y-m-d', strtotime("-1 week")); ?>" data-language='<?php echo (substr(B2S_LANGUAGE, 0, 2) == 'de' ? 'de' : 'en'); ?>' />
                                </div>
                                <br>
                                <div class="b2s-metric-sub-area" style="background-color: #ddd; padding: 10px; margin-top: 10px;">
                                    <?php
                                    foreach ($networkCount as $networkId => $networkCount) {
                                        echo '<div style="display: inline-flex; margin-right: 30px;">
                                                <img style="margin-right: 10px;" alt="' . esc_attr(unserialize(B2S_PLUGIN_NETWORK)[$networkId]) . '" src="' . esc_url(plugins_url('/assets/images/portale/' . esc_attr($networkId) . '_flat.png', B2S_PLUGIN_FILE)) . '">
                                                <div style="text-align: center;">
                                                    <b>' . esc_html(unserialize(B2S_PLUGIN_NETWORK)[$networkId]) . '</b><br>
                                                    ' . esc_html($networkCount) . '<br>
                                                    Accounts
                                                </div>
                                            </div>';
                                    }
                                    ?>
                                </div>
                                <div class="b2s-metric-sub-area" style="background-color: #ddd; padding: 10px; margin-top: 20px; margin-bottom: 20px;">
                                    <div style="display: inline-flex;">
                                        <div style="font-size: 45px;background-color: #79B232;padding: 15px 20px 15px 10px;border-radius: 50%;color: white;margin-right: 15px;"><i class="glyphicon glyphicon-send"></i></div>
                                        <div style="text-align: center; margin-top: 15px; margin-right: 60px;">
                                            <b><?php esc_html_e('Published Posts', 'blog2social') ?></b><br>
                                            <span class="b2s-posts-total-data" style="font-size: 14px;"></span>
                                            <span id="b2s-posts-status" class="glyphicon glyphicon-arrow-up"></span> <span class="b2s-posts-gain-data" style="font-size: 10px;"></span>
                                        </div>
                                    </div>
                                    <div style="display: inline-flex;">
                                        <div style="font-size: 45px;background-color: #79B232;padding: 15px 15px 15px 15px;border-radius: 50%;color: white;margin-right: 15px;"><i class="glyphicon glyphicon-eye-open"></i></div>
                                        <div style="text-align: center; margin-top: 15px; margin-right: 60px;">
                                            <b><?php esc_html_e('Impressions', 'blog2social') ?></b><br>
                                            <span class="b2s-impressions-total-data" style="font-size: 14px;"></span>
                                            <span id="b2s-impressions-status" class="glyphicon glyphicon-arrow-down"></span> <span class="b2s-impressions-gain-data" style="font-size: 10px;"></span>
                                        </div>
                                    </div>
                                    <div style="display: inline-flex;">
                                        <div style="font-size: 45px;background-color: #79B232;padding: 15px 15px 15px 15px;border-radius: 50%;color: white;margin-right: 15px;"><i class="glyphicon glyphicon-hand-up"></i></div>
                                        <div style="text-align: center; margin-top: 15px; margin-right: 60px;">
                                            <b><?php esc_html_e('Post Interactions', 'blog2social') ?></b><br>
                                            <span class="b2s-engagements-total-data" style="font-size: 14px;"></span>
                                            <span id="b2s-engagements-status" class="glyphicon glyphicon-arrow-up"></span> <span class="b2s-engagements-gain-data" style="font-size: 10px;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="clearfix"></div>
                            <!--Filter End-->
                            <div class="b2s-sort-area">
                                <div class="row b2s-sort-result-area">
                                    <div class="col-md-12">
                                        <span style="font-size: 24px;font-weight: bold;"><?php esc_html_e('Post Metrics', 'blog2social') ?></span>
                                        <div style="margin-top: 10px;">
                                            <select class="b2s-filter-active">
                                                <option value="0"><?php esc_html_e('All posts', 'blog2social') ?></option>
                                                <option value="1"><?php esc_html_e('Only active', 'blog2social') ?></option>
                                                <option value="2"><?php esc_html_e('Archive', 'blog2social') ?></option>
                                            </select>
                                            <button class="btn btn-default b2s-sort-posts" data-sort-type="favorites"><?php esc_html_e('Favorites', 'blog2social') ?></button>
                                            <button class="btn btn-default b2s-sort-posts" data-sort-type="impressions"><?php esc_html_e('Most Impressions', 'blog2social') ?></button>
                                            <button class="btn btn-default b2s-sort-posts" data-sort-type="likes"><?php esc_html_e('Most Likes', 'blog2social') ?></button>
                                            <button class="btn btn-default b2s-sort-posts" data-sort-type="reshares"><?php esc_html_e('Most Reshares', 'blog2social') ?></button>
                                            <button class="btn btn-default b2s-sort-posts" data-sort-type="comments"><?php esc_html_e('Most Comments', 'blog2social') ?></button>
                                        </div>
                                        <div style="padding: 4px 4px; background-color: #ddd; font-weight: bold; margin-top: 15px;">
                                            <div style="width: 12%;display: inline-block;"><?php esc_html_e('Published On', 'blog2social') ?></div>
                                            <div style="width: 41%;display: inline-block;"><?php esc_html_e('Posts', 'blog2social') ?></div>
                                            <div style="width: 9%;display: inline-block;"><?php esc_html_e('Impressions', 'blog2social') ?></div>
                                            <div style="width: 5%;display: inline-block;"><?php esc_html_e('Likes', 'blog2social') ?></div>
                                            <div style="width: 6%;display: inline-block;"><?php esc_html_e('Shares', 'blog2social') ?></div>
                                            <div style="width: 6%;display: inline-block;"><?php esc_html_e('Comments', 'blog2social') ?></div>
                                            <div style="display: inline-block;"></div>
                                        </div>
                                        <ul class="list-group b2s-sort-result-item-area">
                                        </ul>
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
</div>

<div class="modal fade b2s-delete-publish-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-delete-publish-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-delete-publish-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Delete entries from the reporting', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <b><?php esc_html_e('You are sure, you want to delete entries from the reporting?', 'blog2social') ?></b>
                <br>
                (<?php esc_html_e('Number of entries', 'blog2social') ?>:  <span id="b2s-delete-confirm-post-count"></span>) 
                <input type="hidden" value="" id="b2s-delete-confirm-post-id">
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
                <button class="btn btn-danger b2s-publish-delete-confirm-btn"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade b2s-metrics-starting-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-metrics-starting-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <span class="b2s-bold text-success" style="font-size: 24px;"><?php esc_html_e('Welcome to the trial of the beta version "Social Media Metrics"!', 'blog2social') ?></span>
                <img src="<?php echo esc_url(plugins_url('/assets/images/metrics/social-symbols.png', B2S_PLUGIN_FILE)); ?>" style="width: 80px; float: right;" alt="blog2social">
                <br>
                <br>
                <?php esc_html_e('You can now track the performance of your posts directly in Blog2Social, starting with Facebook and Twitter, as well as other networks to follow. And you can test it exclusively and for free!', 'blog2social') ?>
                <br>
                <?php esc_html_e("Here's how to start tracking your social media posts:", 'blog2social') ?>
                <br>
                <br>
                <div class="row">
                    <div class="col-md-5 col-md-offset-1">
                        <button class="btn btn-success width-100"><?php esc_html_e("Create and Share a Social Media Post", 'blog2social') ?> <i class="glyphicon glyphicon-send"></i></button>
                        <br>
                        <?php esc_html_e('Create a new social media post for which you want to track the metrics. You can share:', 'blog2social') ?>
                        <ul style="list-style: disc; list-style-position: inside;">
                            <li><?php esc_html_e('WordPress posts, pages and products', 'blog2social') ?></li>
                            <li><?php esc_html_e('Imported posts', 'blog2social') ?></li>
                            <li><?php esc_html_e('"Social Media Posts" consisting of a link, video, image or text.', 'blog2social') ?></li>
                        </ul>
                    </div>
                    <div class="col-md-5 text-center">
                        <button class="btn btn-success width-100"><?php esc_html_e("Check Social Media Metrics", 'blog2social') ?> <i class="glyphicon glyphicon-eye-open"></i></button>
                        <br>
                        <?php esc_html_e('After 24 hours, your post will be updated for the first time and you can see the metrics under the menu item "Social Media Metrics". The social media posts are monitored for 30 days. After 30 days, the last status will be permanently recorded and can be accessed in the archive at any time.', 'blog2social') ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="pull-left b2s-bold"><?php esc_html_e('We hope you enjoy analysing your posts!', 'blog2social') ?></span>
                <button class="btn btn-success b2s-metrics-starting-confirm-btn"><?php esc_html_e('Ok, I want to get started!', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade b2s-metrics-info-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-metrics-info-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-metrics-info-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo esc_html__('Social Media Metrics', 'blog2social') . ' <span class="label label-success label-sm">' . esc_html__("BETA", "blog2social") . '</span>'; ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('You can now track the performance of your posts for Facebook and Twitter directly in Blog2Social. With these Social Media Metrics, you can analyze the performance of your social media posts you shared with the Blog2Social. Use it to optimize your social media strategy to reach your audience and to get better results for your social media posts.', 'blog2social') ?>
                <br>
                <br>
                <?php esc_html_e("You can track the following Social Media Metrics depending on the social networks:", 'blog2social') ?>
                <br>
                <ul style="list-style: disc; list-style-position: inside;">
                    <li><?php esc_html_e('Impressions: A count of how many times the post has been viewed.', 'blog2social') ?></li>
                    <li><?php esc_html_e('Link clicks: A count of link clicks, to further content. (Available for Twitter)', 'blog2social') ?></li>
                    <li><?php esc_html_e('Likes: A count of how many times the post has been liked.', 'blog2social') ?></li>
                    <li><?php esc_html_e('Re-Shares/ Re-Tweets: A count of how many times the post has been reshared or retweeted.', 'blog2social') ?></li>
                    <li><?php esc_html_e('Comments: A count of how many times the post has been replied to.', 'blog2social') ?></li>
                </ul>
                <br>
                <br>
                <?php esc_html_e('How to check the Social Media Metrics?', 'blog2social') ?>
                <br>
                <br>
                <?php esc_html_e('The first step is to create and share a social media post for which you want to track the metrics. You can share WordPress posts, pages and products, imported posts as well as "Social Media Posts" consisting of a link, video, image or text. After 24 hours, you can check the first metrics of the post under the menu item "Social Media Metrics".', 'blog2social') ?>
                <br>
                <br>
                <?php esc_html_e('Please note: The metrics for the social media posts are updated every 24 hours. The social media posts are monitored for 30 days. After 30 days, the last status is permanently recorded and can be accessed in the archive at any time.', 'blog2social') ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success b2s-metrics-info-close-btn"><?php esc_html_e('Ok, I want to get started!', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade b2s-metrics-legend-info-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-metrics-legend-info-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-metrics-legend-info-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Comparison of Social Media Metrics', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('With this function, you can compare the social media metrics for a period you choose by yourself and check whether the number of shared posts, impressions and interactions has increased or decreased. To do so, simply select a period via the calendar on the right, for example 3 days, to compare this time with the previous 3 days. This gives you a total number on the left and a comparison to the 3 days before on the right.', 'blog2social') ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade b2s-metrics-feedback-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-metrics-feedback-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-metrics-feedback-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Feedback', 'blog2social'); ?></h4>
                <div id="b2s-metrics-feedback-checkbox">
                    <input type="checkbox" id="b2s-metrics-dont-show-again" name="b2s-metrics-dont-show-again" value="0">
                    <label for="b2s-metrics-dont-show-again"><?php esc_html_e("Don't show this again", "blog2social") ?> </label><br>
                </div>
            </div>
            <div class="modal-body">
                <iframe src="<?php echo esc_url(B2S_Tools::getSupportLink('metrics_feedback')); ?>" width="100%" height="500px"></iframe>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="b2sOptionMetricsStarted" value="<?php echo (($optionMetricsStarted == true) ? '1' : '0') ?>">
<input type="hidden" id="b2sOptionMetricsFeedback" value="<?php echo (($optionMetricsFeedback == true) ? '1' : '0') ?>">

<input type="hidden" id="b2sLang" value="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)); ?>">
<input type="hidden" id="b2sUserLang" value="<?php echo esc_attr(strtolower(substr(get_locale(), 0, 2))); ?>">