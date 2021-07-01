<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
require_once B2S_PLUGIN_DIR . 'includes/B2S/Network/Item.php';
require_once B2S_PLUGIN_DIR . 'includes/Tools.php';
$b2sSiteUrl = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/');
$displayName = stripslashes(get_user_by('id', B2S_PLUGIN_BLOG_USER_ID)->display_name);
$displayName = ((empty($displayName) || $displayName == false) ? __("Unknown username", "blog2social") : $displayName);
$networkItem = new B2S_Network_Item();
$networkData = $networkItem->getData();
?>

<div class="b2s-container">
    <div class=" b2s-inbox col-md-12 del-padding-left">
        <div class="col-md-9 del-padding-left del-padding-right">
            <!--Header|Start - Include-->
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
            <!--Header|End-->
            <div class="clearfix"></div>
            <!--Content|Start-->
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="b2s-post">
                            <div class="grid-body">
                                <div class="hidden-lg hidden-md hidden-sm filterShow"><a href="#" onclick="showFilter('show');return false;"><i class="glyphicon glyphicon-chevron-down"></i> <?php esc_html_e('filter', 'blog2social') ?></a></div>
                                <div class="hidden-lg hidden-md hidden-sm filterHide"><a href="#" onclick="showFilter('hide');return false;"><i class="glyphicon glyphicon-chevron-up"></i> <?php esc_html_e('filter', 'blog2social') ?></a></div>
                                <div class="form-inline" role="form">
                                    <?php echo $networkItem->getSelectMandantHtml($networkData['mandanten']); ?>
                                    <div class="form-group b2s-network-mandant-area">
                                        <?php if (B2S_PLUGIN_USER_VERSION > 1) { ?>
                                            <button href="#" class="btn btn-primary btn-sm b2s-network-add-mandant-btn">
                                                <span class="glyphicon glyphicon-plus"></span> <?php esc_html_e('Create new network collection', 'blog2social') ?> <span class="label label-success"></button>
                                        <?php } else { ?>
                                            <button href="#" class="btn btn-primary btn-sm b2s-btn-disabled b2sProFeatureModalBtn" data-type="create-network-profile" data-title="<?php esc_html_e('You want to define a new combination of networks?', 'blog2social') ?>">
                                                <span class="glyphicon glyphicon-plus"></span> <?php esc_html_e('Create new network collection', 'blog2social') ?> <span class="label label-success"> <?php esc_html_e("PRO", "blog2social") ?></span></button>
                                        <?php } ?>

                                        <button href="#" class="btn btn-danger btn-sm b2s-network-mandant-btn-delete" style="display:none;">
                                            <span class="glyphicon glyphicon-trash"></span> <?php esc_html_e('Delete', 'blog2social') ?>
                                        </button>
                                        
                                        <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('network_mandant') ?>"><?php esc_html_e('Info', 'blog2social') ?></a>
                                    </div>
                                    <div class="form-group b2s-network-time-manager-area pull-right hidden-xs">
                                        <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>
                                            <a href="#" class="btn btn-primary btn-sm b2s-get-settings-sched-time-default">
                                            <?php } else { ?>
                                                <a href="#" class="btn btn-primary btn-sm b2s-btn-disabled" data-title = "<?php esc_html_e('You want to schedule your posts and use the Best Time Scheduler?', 'blog2social') ?>" data-toggle ="modal" data-target ="#b2sInfoSchedTimesModal">
                                                <?php } ?>  <span class="glyphicon glyphicon-time"></span> <?php esc_html_e('Load Best Times', 'blog2social'); ?></a>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>
                        <div class="row b2s-network-auth-area">
                            <?php echo $networkItem->getPortale($networkData['mandanten'], $networkData['auth'], $networkData['portale'], $networkData['auth_count'], $networkData['addon_count']); ?>
                        </div>
                        <div class="row b2s-loading-area width-100" style="display: none">
                            <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                            <div class="clearfix"></div>
                            <?php esc_html_e('Loading...', 'blog2social') ?>
                        </div>
                        <?php
                        $noLegend = 1;
                        require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
    </div>
</div>
<input type="hidden" id="lang" value="<?php echo substr(B2S_LANGUAGE, 0, 2); ?>">

<div class="modal fade" id="b2s-network-add-mandant" tabindex="-1" role="dialog" aria-labelledby="b2s-network-add-mandant" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-add-mandant" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> <?php esc_html_e('Create new network collection', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <input type="text" class="form-control b2s-network-add-mandant-input" placeholder="Profil">
                    <span class="input-group-btn">
                        <button class="btn btn-success b2s-network-add-mandant-btn-save" type="button"><?php esc_html_e('create', 'blog2social') ?></button>
                    </span>
                    <div class="input-group-btn">
                        <div class="btn btn-default b2s-network-add-mandant-btn-loading b2s-loader-impulse b2s-loader-impulse-sm" style="display:none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2s-network-delete-mandant" tabindex="-1" role="dialog"  aria-labelledby="b2s-network-delete-mandant" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-delete-mandant" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Delete Profile', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('Do you really want to delete this profile', 'blog2social') ?>?
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
                <button class="btn btn-sm btn-danger b2s-btn-network-delete-mandant-confirm"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sInfoNetwork18" tabindex="-1" role="dialog"  aria-labelledby="b2sInfoNetwork18" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoNetwork18" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Google My Business', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('Blog2Social uses the official Google My Business API to share your content on your business listing. You can connect Google My Business listings with up to nine different locations to Blog2Social and you can choose which location you want to share your content on.', 'blog2social'); ?>
                <br>
                <br>
                <?php esc_html_e('Google currently allows access to the API for all companies with up to 9 locations in their Google My Business Listings. However, Google plans to extend the API for companies with more than 9 locations in their Google My Business listings.', 'blog2social'); ?>
                <br>
                <br>
                <a href="https://developers.google.com/my-business/content/posts-data#faqs" target="_blank"><?php esc_html_e('Learn more', 'blog2social'); ?></a>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="b2s-network-delete-auth" tabindex="-1" role="dialog" aria-labelledby="b2s-network-delete-auth" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-delete-auth" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Delete Authorization', 'blog2social') ?></h4>
            </div>
            <div class="row b2s-loading-area width-100">
                <br>
                <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                <div class="clearfix"></div>
                <?php esc_html_e('Loading...', 'blog2social') ?>
            </div>
            <div class="modal-body b2s-btn-network-delete-auth-confirm-text">
                <?php esc_html_e('Do you really want to delete this authorization', 'blog2social') ?>!
            </div>
            <div class="modal-body b2s-btn-network-delete-auth-show-post-text">
                <p class="b2s-btn-network-delete-sched-text" style="display: none;"><?php esc_html_e('You have still set up scheduled posts for this network:', 'blog2social'); ?></p>
                <p class="b2s-btn-network-delete-assign-text" style="display: none;"><?php esc_html_e('This network connection is still assigned to other users.', 'blog2social'); ?></p>
                <p class="b2s-btn-network-delete-assign-sched-text" style="display: none;"><?php esc_html_e('The user to whom the connection is assigned still has scheduled posts.', 'blog2social'); ?></p>
                <p><input type="checkbox" value="0" id="b2s-delete-network-sched-post"></p>
                <ul class="b2s-btn-network-delete-list">
                    <li class="b2s-btn-network-delete-sched-text" style="display: none;"><?php esc_html_e('Delete all scheduled posts for this account irrevocably', 'blog2social') ?> (<span id="b2s-btn-network-delete-auth-show-post-count"></span> <?php esc_html_e('scheduled posts', 'blog2social') ?>)</li>
                    <li class="b2s-btn-network-delete-assign-text" style="display: none;"><?php esc_html_e('The connection is still assigned to other users. Please withdraw the assigned connection from other users first.', 'blog2social'); ?></li>
                    <li class="b2s-btn-network-delete-assign-sched-text" style="display: none;"><?php esc_html_e('Delete all scheduled posts from all user who use this connection.', 'blog2social'); ?></li>
                </ul>
            </div>
            <div class="modal-footer">
                <input type="hidden" value="" id="b2s-delete-network-auth-id">
                <input type="hidden" value="" id="b2s-delete-network-id">
                <input type="hidden" value="" id="b2s-delete-network-type">
                <input type="hidden" value="" id="b2s-delete-assign-network-auth-id">
                <input type="hidden" value="" id="b2s-delete-blog-user-id">
                <input type="hidden" value="" id="b2s-delete-assignment">
                <input type="hidden" value="" id="b2s-delete-assign-list">
                <button class="btn btn-sm btn-danger b2s-btn-network-delete-auth-confirm-btn"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
                <button class="btn btn-sm btn-success b2s-btn-network-delete-auth-show-post-btn"><?php esc_html_e('View schedule posts', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2s-modify-board-and-group-network-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-modify-board-and-group-network-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-modify-board-and-group-network-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 id="b2s-modify-board-and-group-network-modal-title" class="modal-title"></h4>
            </div>
            <div class="row b2s-modify-board-and-group-network-loading-area width-100">
                <br>
                <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
            </div>
            <br>
            <div class="col-md-12">
                <div id="b2s-modify-board-and-group-network-no-data"><div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Please re-authorize your account with Blog2Social and try again', 'blog2social'); ?></div></div>
                <div id="b2s-modify-board-and-group-network-save-success"><div class="alert alert-success"><span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Change successful', 'blog2social'); ?></div></div>
                <div id="b2s-modify-board-and-group-network-save-error"><div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Could not be changed', 'blog2social'); ?></div></div>
            </div>
            <div class="b2s-modify-board-and-group-network-data col-md-12"></div>
            <div class="modal-footer b2s-modify-board-and-group-network-modal-footer">
                <input type="hidden" value="" id="b2s-modify-board-and-group-network-auth-id">
                <input type="hidden" value="" id="b2s-modify-board-and-group-network-id">
                <input type="hidden" value="" id="b2s-modify-board-and-group-network-type">
                <input type="hidden" value="" id="b2s-modify-board-and-group-name">
                <button class="btn btn-sm btn-success b2s-modify-board-and-group-network-save-btn"><?php esc_html_e('modify', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2s-edit-template" tabindex="-1" role="dialog" aria-labelledby="b2s-edit-template" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-edit-template" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-1" alt="Facebook" src="<?php echo plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-2" alt="Twitter" src="<?php echo plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-3" alt="LinkedIn" src="<?php echo plugins_url('/assets/images/portale/3_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-4" alt="Tumblr" src="<?php echo plugins_url('/assets/images/portale/4_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-6" alt="Pinterest" src="<?php echo plugins_url('/assets/images/portale/6_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-7" alt="Flickr" src="<?php echo plugins_url('/assets/images/portale/7_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-9" alt="Diigo" src="<?php echo plugins_url('/assets/images/portale/9_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-11" alt="Medium" src="<?php echo plugins_url('/assets/images/portale/11_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-12" alt="Instagram" src="<?php echo plugins_url('/assets/images/portale/12_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-14" alt="Torial" src="<?php echo plugins_url('/assets/images/portale/14_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-15" alt="Reddit" src="<?php echo plugins_url('/assets/images/portale/15_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-16" alt="Bloglovin" src="<?php echo plugins_url('/assets/images/portale/16_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-17" alt="VKontakte" src="<?php echo plugins_url('/assets/images/portale/17_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-18" alt="Google My Business" src="<?php echo plugins_url('/assets/images/portale/18_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-19" alt="Xing" src="<?php echo plugins_url('/assets/images/portale/19_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <img class="pull-left hidden-xs b2s-img-network b2s-edit-template-network-img" id="b2s-edit-template-network-img-24" alt="Telegram" src="<?php echo plugins_url('/assets/images/portale/24_flat.png', B2S_PLUGIN_FILE); ?>" style="display: none;">
                <h4 class="modal-title b2s-edit-template-title"><?php esc_html_e('Edit Post Template', 'blog2social') ?></h4> <?php echo ((B2S_PLUGIN_USER_VERSION == 0) ? '<span class="label label-success">' . esc_html__('SMART', 'blog2social') . '</span>' : '') ?>
            </div>
            <div class="row b2s-loading-area width-100">
                <br>
                <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                <div class="clearfix"></div>
                <?php esc_html_e('Loading...', 'blog2social') ?>
            </div>
            <div class="modal-body b2s-edit-template-content">

            </div>
            <div class="modal-footer b2s-edit-template-footer">
                <button class="btn btn-primary btn-sm b2s-edit-template-save-btn"><?php esc_html_e('save', 'blog2social'); ?></button>
                <input type="hidden" value="" id="b2s-edit-template-network-id">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sInfoNoCache" tabindex="-1" role="dialog" aria-labelledby="b2sInfoNoCache" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoNoCache" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Instant Caching for Link Posts', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('Please enable this feature, if you are using varnish caching (HTTP accelerator to relieve your website). Blog2Social will add a "no-cache=1" parameter to the post URL of your link posts to ensure that the network always pulls the current meta data of your blog post.', 'blog2social') ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sInfoFormat" tabindex="-1" role="dialog" aria-labelledby="b2sInfoFormat" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoFormat" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Choose your Post Format', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="b2sInfoFormatText" data-network-id="1">
                    <?php esc_html_e('Decide in which post format you want to post your content: Link post or image post.', 'blog2social') ?>
                </div>
                <div class="b2sInfoFormatText" data-network-id="2">
                    <?php esc_html_e('Decide in which post format you want to post your content: Link post or image post.', 'blog2social') ?>
                </div>
                <div class="b2sInfoFormatText" data-network-id="3">
                    <?php esc_html_e('Decide in which post format you want to post your content: Link post or image post.', 'blog2social') ?>
                </div>
                <div class="b2sInfoFormatText" data-network-id="12">
                    <?php esc_html_e('Decide in wich form you want to post your Content. Either as image with frame, or as image cut out.', 'blog2social') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sInfoContent" tabindex="-1" role="dialog" aria-labelledby="b2sInfoContent" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoContent" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Post Content', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('Edit the content of your post. Move elements by drag and drop into the textarea and customize them as you like.', 'blog2social') ?>
                <div class="b2s-template-placeholder-legend">
                    <br/>  
                    <p class="b2s-bold"><?php esc_html_e('Legend', 'blog2social'); ?>:</p>
                    <p>
                        <span class="b2s-bold">{TITLE}</span> - <?php esc_html_e('The title of your post', 'blog2social') ?> <br>
                        <span class="b2s-bold">{EXCERPT}</span> - <?php esc_html_e('The summary of your post (you define it in the side menu of your post).', 'blog2social') ?> <br>
                        <span class="b2s-bold">{CONTENT}</span> - <?php esc_html_e('The content of your post', 'blog2social') ?> <br>
                        <span class="b2s-bold">{KEYWORDS}</span> - <?php esc_html_e('The tags you have set in your post.', 'blog2social') ?> <br>
                        <span class="b2s-bold">{AUTHOR}</span> - <?php esc_html_e('The name of the post author.', 'blog2social') ?> <br>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sInfoCharacterLimit" tabindex="-1" role="dialog" aria-labelledby="b2sInfoCharacterLimit" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoCharacterLimit" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Character limit', 'blog2social') ?> (CONTENT, EXCERPT)</h4>
            </div>
            <div class="modal-body">
                <div class="b2s-info-character-limit-text"><?php esc_html_e('Define the character limit for the variables "EXCERPT" and "CONTENT" individually. Your text will be shortened after the last comma, period, or space character within your character limit.', 'blog2social') ?></div>
                <div class="b2s-info-character-limit-text"><?php esc_html_e('An "EXCERPT" will only be added to your social media post if you have added a manual excerpt in the excerpt editing box of the Gutenberg side menu (document settings) of your post.', 'blog2social') ?></div>
                <div class="b2s-info-character-limit-text"><?php esc_html_e('"TITLES" and "KEYWORDS" (Hashtags) are not shortened. If you select the "TITLE" and "KEYWORD" variables for your social media posts, the character limit you define for the "EXCERPT" and/or "CONTENT" variables will be applied within the remaining available character limit of the social network.', 'blog2social') ?></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sAuthNetwork6Modal" tabindex="-1" role="dialog" aria-labelledby="b2sAuthNetwork6Modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sAuthNetwork6Modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Connect with Pinterest', 'blog2social') ?></h4>
            </div>
            <div class="modal-body b2s-auth-network-6-extension-info-area" style="display: none;">
                <div class="row width-100">
                    <div class="col-md-12">
                        <div class="alert alert-danger b2s-auth-network-6-extension-error" data-info="default"><?php esc_html_e('The login failed. To connect your Pinterest account to Blog2Social, please sign in to Pinterest using the Blog2Social browser extension.', 'blog2social'); ?></div>
                        <h3>1. <?php esc_html_e('Download and activate the Blog2Social extension', 'blog2social'); ?></h3>
                        <div class="b2s-auth-network-6-extension-download-area">
                            <a class="b2s-auth-network-6-extension-download-link" href="https://addons.mozilla.org/en-US/firefox/addon/blog2social/" target="_blank"><img src="<?php echo plugins_url('/assets/images/firefox-extension.png', B2S_PLUGIN_FILE); ?>"></a>
                            <a class="b2s-auth-network-6-extension-download-link" href="https://chrome.google.com/webstore/detail/blog2social-social-media/hobhidjecjjgknjpnclnmfgkajdjlaml" target="_blank"><img src="<?php echo plugins_url('/assets/images/chrome-extension.png', B2S_PLUGIN_FILE); ?>"></a>
                        </div>
                        <h3>2. <?php esc_html_e('Click on "continue"', 'blog2social'); ?></h3>
                    </div>
                </div>
            </div>
            <div class="modal-body b2s-auth-network-6-extension-start-area" style="display: none;">
                <div class="row width-100">
                    <div class="col-md-12">
                        <div class="alert alert-danger b2s-auth-network-6-extension-error" data-info="default"><?php esc_html_e('The login failed. To connect your Pinterest account to Blog2Social, please sign in to Pinterest using the Blog2Social browser extension.', 'blog2social'); ?></div>
                    </div>
                </div>
            </div>
            <div class="modal-body b2s-auth-network-6-extension-success-area width-100" style="display: none;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger b2s-auth-network-6-extension-error" data-info="default" style="display:none;"><?php esc_html_e('An error occurred! Please try again.', 'blog2social'); ?></div>
                        <div class="alert alert-success b2s-auth-network-6-info-extension" data-info="success"><?php esc_html_e('Login up successful. Please confirm that Blog2Social is allowed to publish on your profile.', 'blog2social'); ?></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 b2s-login-form-label"><?php esc_html_e('Select Pinboard', 'blog2social'); ?></label>
                    <div class="col-md-9">
                        <select id="b2s-auth-network-6-board-extension" class="form-control valid" aria-invalid="false"></select>
                    </div>
                </div>
            </div>
            <div class="row b2s-loading-area width-100" style="display: none">
                <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                <div class="clearfix"></div>
                <?php esc_html_e('Loading...', 'blog2social') ?>
            </div>
            <div class="modal-body b2s-auth-network-6-login-area">
                <div class="alert alert-info"><?php esc_html_e('Please make sure to use your original Pinterest login data (email and password). Social Login via Facebook or Google login data will not work here. Please also check if the two-factor authentication in Pinterest is deactivated to ensure a stable connection to Blog2Social.', 'blog2social'); ?></div>
                <div class="alert alert-danger b2s-auth-network-6-info" data-info="default" style="display:none;"><?php esc_html_e('An error occurred! Please try again.', 'blog2social'); ?></div>
                <div class="alert alert-danger b2s-auth-network-6-info" data-info="version" style="display:none;"><?php sprintf(__('You want to connect an additional account? <a target="_blank" href="%s">Upgrade to Blog2Social Premium</a>', 'blog2social'), B2S_Tools::getSupportLink('affiliate')); ?></div>
                <div class="alert alert-danger b2s-auth-network-6-info" data-info="limit" style="display:none;"><?php echo sprintf(__('You want to connect an additional account? <a target="_blank" href="%s">Upgrade to Blog2Social Premium</a>', 'blog2social'), B2S_Tools::getSupportLink('affiliate')); ?></div>
                <div class="alert alert-danger b2s-auth-network-6-info" data-info="invalid" style="display:none;"><?php esc_html_e('Invalid Data! Please try again.', 'blog2social'); ?></div>
                <div class="alert alert-danger b2s-auth-network-6-info" data-info="board" style="display:none;"><?php echo esc_html_e('You have not yet created any pinboards in your Pinterest account. Please set up at least one pinboard to pin on your Pinterest account!', 'blog2social'); ?></div>
                <div class="alert alert-danger b2s-auth-network-6-info" data-info="login" style="display:none;"><?php echo sprintf(__('Login failed. Please check your login data for typos and please check your Pinterest settings, if the two-factor authentication is turned off for this account: <a target="_blank" href="%s">%s</a>', 'blog2social'), esc_url('https://www.pinterest.com/settings/security/'), esc_url('https://www.pinterest.com/settings/security/')); ?></div>
                <div class="alert alert-danger b2s-auth-network-6-info" data-info="access" style="display:none;"><?php esc_html_e('Pinterest has rejected the connection to your blog', 'blog2social'); ?></div>
                <div class="alert alert-danger b2s-auth-network-6-info" data-info="http_request_failed" style="display:none;"><?php esc_html_e('Please select your correct server location and connect again.', 'blog2social'); ?></div>
                <div class="alert alert-danger b2s-auth-network-6-info" data-info="error_code_403" style="display:none;"><?php esc_html_e('Access to this resource on your server is denied! Please check your webserver configuration for caching.', 'blog2social'); ?></div>
                <div class="alert alert-success b2s-auth-network-6-info" data-info="success" style="display:none;"><?php esc_html_e('Login up successful. Please confirm that Blog2Social is allowed to publish on your profile.', 'blog2social'); ?></div>
                <div class="form-group row">
                    <label class="col-md-3 b2s-login-form-label"><?php esc_html_e('E-Mail', 'blog2social'); ?></label>
                    <div class="col-md-9">
                        <input class="form-control username" required="true" type="text" placeholder="" id="b2s-auth-network-6-username" aria-required="true">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 b2s-login-form-label"><?php esc_html_e('Password', 'blog2social'); ?></label>
                    <div class="input-group col-md-9" style="padding-left: 15px; padding-right: 15px;">
                        <input class="form-control password" required="true" type="password" placeholder="" id="b2s-auth-network-6-password" aria-required="true">
                        <span class="input-group-btn">
                            <button class="btn btn-default b2s-auth-network-6-reveal" type="button">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </button>
                        </span>
                    </div>    
                </div>
                <div class="form-group row">
                    <label class="col-md-3 b2s-login-form-label"><?php esc_html_e('Server-Location', 'blog2social'); ?></label>
                    <div class="col-md-9" style="padding-left: 15px; padding-right: 15px;">
                        <select class="form-control" id="b2s-auth-network-6-location">
                            <?php
                            $pinterestCountryList = B2S_Tools::getCountryListByNetwork(6);
                            foreach ($pinterestCountryList as $key => $name) {
                                echo '<option value="' . $key . '"' . (($key == '') ? ' selected="selected"' : '') . '>' . $name['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>    
                </div>
                <div class="b2s-auth-network-6-board-area" style="display: none;">
                    <div class="form-group row">
                        <label class="col-md-3 b2s-login-form-label"><?php esc_html_e('Select Pinboard', 'blog2social'); ?></label>
                        <div class="col-md-9">
                            <select id="b2s-auth-network-6-board" class="form-control valid" aria-invalid="false"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer b2s-edit-template-footer">
                <button class="btn btn-success b2s-auth-network-6-login-btn" type="button"><?php esc_html_e('authorize', 'blog2social'); ?></button>
                <button class="btn btn-success b2s-auth-network-6-confirm-btn" type="button" style="display: none;"><?php esc_html_e('confirm', 'blog2social'); ?></button>
                <button class="btn btn-success b2s-auth-network-6-extension-auth-btn" type="button" style="display: none;"><?php esc_html_e('Sign in to Pinterest', 'blog2social'); ?></button>
                <button class="btn btn-success b2s-auth-network-6-confirm-extension-btn" type="button" style="display: none;"><?php esc_html_e('confirm', 'blog2social'); ?></button>
                <button class="btn btn-success b2s-auth-network-6-extension-continue-btn" type="button" disabled="disabled" style="display: none;"><?php esc_html_e('continue', 'blog2social'); ?></button>
                <input type="hidden" id="b2s-auth-network-6-ident-data">
                <input type="hidden" id="b2s-auth-network-6-username-extension">
                <input type="hidden" id="b2s-auth-network-6-auth-id">
                <input type="hidden" id="b2s-auth-network-6-mandant-id">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2s-edit-network-auth-settings" tabindex="-1" role="dialog" aria-labelledby="b2s-edit-network-auth-settings" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-edit-network-auth-settings" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title b2s-edit-network-auth-settings-title"><?php esc_html_e('Advanced Network Settings', 'blog2social') ?></h4>
            </div>
            <div class="row b2s-loading-area width-100">
                <br>
                <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                <div class="clearfix"></div>
                <?php esc_html_e('Loading...', 'blog2social') ?>
            </div>
            <div class="modal-body">
                <div class="b2s-network-auth-settings-content">
                    <?php if (B2S_PLUGIN_USER_VERSION >= 3) { ?>
                        <div class="row">
                            <div class="col-md-12 b2s-text-bold"><h4><?php esc_html_e('URL Parameters', 'blog2social') ?></h4></div>
                            <div class="col-md-12"><div class="alert alert-danger b2s-url-parameter-error" data-error-reason="save" style="display:none;"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('The parameters could not be saved. Please try again.', 'blog2social') ?></div></div>
                            <div class="col-md-12"><div class="alert alert-danger b2s-url-parameter-error" data-error-reason="default" style="display:none;"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('An error occured. Please contact our support.', 'blog2social') ?></div></div>
                            <div class="col-md-12 del-padding-left b2s-url-parameter-content"></div>
                        </div>
                        <hr>
                        <div class="b2s-move-connection">
                            <div class="row">
                                <div class="col-md-12 b2s-text-bold">
                                    <h4><?php esc_html_e('Network collection', 'blog2social'); ?></h4>
                                </div>
                                <div class="col-md-12 b2s-text-bold">
                                    <span><?php esc_html_e('Move the connection to another network collection.', 'blog2social'); ?></span>
                                </div>
                            </div>
                            <div class="row b2s-move-connection-error" style="display: none;">
                                <div class="col-md-12">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e("An error occured. Please contact our support.", 'blog2social'); ?></div>
                                </div>
                            </div>
                            <div class="row b2s-margin-top-8" id="b2s-move-connection-input">
                                <div class="col-md-8">
                                    <select class="form-control b2s-select" id="b2s-move-connection-select"></select>
                                </div>
                                <div class="col-md-4"><button class="btn btn-primary btn-sm" id="b2s-move-user-auth-to-profile"><?php esc_html_e('move', 'blog2social'); ?></button></div>
                                <input type="hidden" value="" id="b2sUserAuthId">
                                <input type="hidden" value="" id="b2sOldMandantId">
                                <input type="hidden" value="" id="b2sNetworkId">
                                <input type="hidden" value="" id="b2sNetworkType">
                            </div>
                            <div class="row b2s-margin-top-8" id="b2s-move-connection-error" style="display: none;">
                                <div class="col-md-12">
                                    <div class="alert alert-warning"><span class="glyphicon glyphicon-warning-sign glyphicon-warning"></span> <?php esc_html_e('You need at least one network collection', 'blog2social'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="b2s-assignment-area" style="display:none;">
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-md-12 b2s-text-bold">
                                    <h4><?php esc_html_e('Team Management', 'blog2social'); ?></h4>
                                </div>
                                <div class="col-md-12 b2s-margin-bottom-8 b2s-text-bold">
                                    <span><?php esc_html_e('Assign the connection to other blog users', 'blog2social'); ?></span>
                                </div>
                            </div>
                            <div class="row b2s-connection-assign" style="display: none;">
                                <div class="col-md-12 b2s-assign-error" data-error-reason="default" style="display: none;">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e("An error occured. Please contact our support.", 'blog2social'); ?></div>
                                </div>
                                <div class="col-md-12 b2s-assign-error" data-error-reason="internal_server_error" style="display: none;">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e("An error occured. Please contact our support.", 'blog2social'); ?></div>
                                </div>
                                <div class="col-md-12 b2s-assign-error" data-error-reason="invalid_data" style="display: none;">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e("An error occured. Please contact our support.", 'blog2social'); ?></div>
                                </div>
                                <div class="col-md-12 b2s-assign-error" data-error-reason="token_no_business" style="display: none;">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e("You don't have a Business License", 'blog2social'); ?></div>
                                </div>
                                <div class="col-md-12 b2s-assign-error" data-error-reason="assign_token_no_business" style="display: none;">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e("This user don't have a Business License, or it is not the same", 'blog2social'); ?></div>
                                </div>
                                <div class="col-md-12 b2s-assign-error" data-error-reason="network_auth_exists" style="display: none;">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e("The connection has already been assigned to this user.", 'blog2social'); ?></div>
                                </div>
                                <div class="col-md-12 b2s-assign-error" data-error-reason="network_auth_not_exists" style="display: none;">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e("The connection does not exist.", 'blog2social'); ?></div>
                                </div>
                                <div class="col-md-12 b2s-assign-error" data-error-reason="network_auth_assign_exists" style="display: none;">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e("This connection has already been assigned to this user.", 'blog2social'); ?></div>
                                </div>
                                <div class="col-md-12" id="b2s-assign-info">
                                    <div class="alert alert-warning"><span class="glyphicon glyphicon-warning-sign glyphicon-warning"></span> <?php esc_html_e('You can only share the connection with blog users who use the same license as you.', 'blog2social'); ?></div>
                                </div>
                                <div class="col-md-12" id="b2s-no-assign-user" style="display: none;">
                                    <div class="alert alert-warning"><span class="glyphicon glyphicon-warning-sign glyphicon-warning"></span> <?php esc_html_e('There are no other users to whom the connection can be assigned.', 'blog2social'); ?></div>
                                </div>
                                <div class="col-md-8" id="b2s-connection-assign-select"></div>
                                <div class="col-md-4"><button class="btn btn-primary btn-sm" id="b2s-assign-network-user-auth"><?php esc_html_e('assign', 'blog2social'); ?></button></div>
                                <div class="col-md-12 b2s-network-assign-option"><input type="checkbox" id="b2s-network-assign-option-best-times"><label for="b2s-network-assign-option-best-times"> <?php esc_html_e('Apply best time settings', 'blog2social') ?></label></div>
                                <div class="col-md-12 b2s-network-assign-option"><input type="checkbox" id="b2s-network-assign-option-posting-template"><label for="b2s-network-assign-option-posting-template"> <?php esc_html_e('Apply post template settings', 'blog2social') ?></label></div>
                                <div class="col-md-12 b2s-network-assign-option"><input type="checkbox" id="b2s-network-assign-option-url-parameter"><label for="b2s-network-assign-option-url-parameter"> <?php esc_html_e('Apply URL Parameters', 'blog2social') ?></label></div>
                                <div class="col-md-12 b2s-network-assign-list"></div>
                            </div>
                            <div class="row b2s-connection-owner" style="display: none;">
                                <div class="col-sm-12">
                                    <div class="alert alert-info"><span class="glyphicon glyphicon-warning-sign glyphicon-info"></span> <?php esc_html_e('This connection was assigned by') ?> </span><span id="b2s-connection-owner-name"></span></div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-info">
                            <?php esc_html_e('Upgrade to Blog2Social Business to easily bundle your connections into network collection and assign your social media connections to other blog users. You can update and delete the connections as well as select forums or boards. Other users will be able to use the social media connection you assigned to them to post and schedule to your social media profile, page or group.', 'blog2social'); ?>
                            <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" class="b2s-bold b2s-text-underline"><?php esc_html_e('Upgrade to Blog2Social Business', 'blog2social'); ?></a>
                        </div>
                        <div class="row b2s-btn-disabled">
                            <div class="col-md-12 b2s-text-bold"><h4><?php esc_html_e('URL Parameters', 'blog2social') ?></h4></div>
                            <div class="col-md-12 b2s-text-bold"><span><?php echo sprintf(__('Define parameters that will be added to link posts on this network e.g. to create tracking links with UTM paramters. <a target="_blank" href="%s">More information</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('url_parameter'))) ?></span></div>
                            <div class="col-md-12 del-padding-left b2s-url-parameter-content"></div>
                        </div>
                        <hr>
                        <div class="b2s-btn-disabled">
                            <div class="b2s-move-connection">
                                <div class="row">
                                    <div class="col-md-12 b2s-text-bold">
                                        <h4><?php esc_html_e('Network collection', 'blog2social'); ?></h4>
                                    </div>
                                    <div class="col-md-12 b2s-text-bold">
                                        <span><?php esc_html_e('Move the connection to another network collection.', 'blog2social'); ?></span>
                                    </div>
                                </div>
                                <div class="row b2s-margin-top-8" id="b2s-move-connection-input">
                                    <div class="col-md-8">
                                        <select class="form-control b2s-select"><option><?php esc_html_e('My Profile', 'blog2social'); ?></option></select>
                                    </div>
                                    <div class="col-md-4"><button class="btn btn-primary btn-sm"><?php esc_html_e('move', 'blog2social'); ?></button></div>
                                </div>
                            </div>
                            <div class="row b2s-connection-assign">
                                <br>
                                <hr>
                                <div class="col-md-12 b2s-text-bold">
                                    <h4><?php esc_html_e('Team Management', 'blog2social'); ?></h4>
                                </div>
                                <div class="col-md-12 b2s-margin-bottom-8 b2s-text-bold">
                                    <span><?php esc_html_e('Assign the connection to other blog users', 'blog2social'); ?></span>
                                </div>
                                <div class="col-md-8"><select class="form-control b2s-select"><option><?php echo esc_html($displayName); ?></option></select></div>
                                <div class="col-md-4"><button class="btn btn-primary btn-sm"><?php esc_html_e('assign', 'blog2social'); ?></button></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="modal-footer b2s-edit-network-auth-settings-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sNetworkAddPageInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sNetworkAddPageInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sNetworkAddPageInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Add Page', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo sprintf(__('Please make sure to log in with your account which manages your pages and <a href="%s" target="_blank">follow this guide to select all your pages</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('fb_page_auth'))); ?>
                        <button class="btn btn-primary pull-right b2s-add-network-continue-btn"><?php esc_html_e('Continue', 'blog2social'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sNetworkAddGroupInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sNetworkAddGroupInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sNetworkAddGroupInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Add Group', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo sprintf(__('Please make sure to log in with your account which manages your groups and <a href="%s" target="_blank">follow this guide to select all your groups</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('fb_group_auth'))); ?>
                        <button class="btn btn-primary pull-right b2s-add-network-continue-btn"><?php esc_html_e('Continue', 'blog2social'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sNetworkAddInstagramInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sNetworkAddInstagramInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sNetworkAddInstagramInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Add Profile', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo sprintf(__('When you connect Blog2Social with your Instagram account, you might get a notification from Instagram that a server from Germany in the Cologne area is trying to access your account. This is a general security notification due to the fact that the Blog2Social server is located in this area. This is an automatic process that is necessary to establish a connection to Instagram. Rest assured, that this is a common and regular security notice to keep your account safe. <a href="%s" target="_blank">More information: How to connect with Instagram.</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('instagram_auth_faq'))); ?>
                        <button class="btn btn-primary pull-right b2s-add-network-continue-btn"><?php esc_html_e('Continue', 'blog2social'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sNetworkAddInstagramBusinessInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sNetworkAddInstagramBusinessInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sNetworkAddInstagramBusinessInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Connect Instagram Business Account', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo sprintf(__('Please note: Your Instagram Business Account will be connected with Blog2Social via Facebook. Therefore, please make sure that your Instagram Business Account is linked to a Facebook page and enter the corresponding login data. You will find more information and detailed instructions in the <a href="%s" target="_blank">Instagram Business guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('instagram_business_auth_faq'))); ?>
                        <button class="btn btn-primary pull-right b2s-add-network-continue-btn"><?php esc_html_e('Continue', 'blog2social'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sNetworkAddonInfo" tabindex="-1" role="dialog" aria-labelledby="b2sNetworkAddonInfo" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sNetworkAddonInfo" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Network connections', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php esc_html_e('With Blog2Social you can connect up to 16 social media networks and share your posts on your favourite social media accounts automatically.', 'blog2social'); ?>
                        <br>
                        <br>
                        <?php esc_html_e('Each license has a specified number of accounts you can connect per social media network.', 'blog2social'); ?>
                        <br>
                        <br>
                        <?php esc_html_e('Smart: 3 (per user)', 'blog2social'); ?>
                        <br>
                        <?php esc_html_e('Pro: 5 (per user)', 'blog2social'); ?>
                        <br>
                        <?php esc_html_e('Business: 15 (per user)', 'blog2social'); ?>
                        <br>
                        <br>
                        <?php esc_html_e('For example: With the Pro license, each user can connect 5 Facebook accounts + 5 Twitter accounts + 5 Instagram accounts + ...', 'blog2social'); ?>
                        <br>
                        <br>    
                        <?php esc_html_e('You can also purchase additional groups and sites as add-on to your active Blog2Social Premium Pro or Premium Business license:', 'blog2social'); ?>
                        <br>
                        <br>
                        <?php esc_html_e('Facebook groups', 'blog2social'); ?>
                        <br>
                        <?php esc_html_e('Facebook pages', 'blog2social'); ?>
                        <br>
                        <?php esc_html_e('LinkedIn pages', 'blog2social'); ?>
                        <br>
                        <br>
                        <?php esc_html_e('For example: If you purchase 5 Facebook groups, these additional 5 Facebook groups are available for all users. So, when 5 users are activated for the Pro or Business license, each user can connect 1 additional Facebook group, or one user can connect 5 additional Facebook groups.', 'blog2social'); ?>
                        <br>
                        <br>
                        <?php echo sprintf(__('<a href="%s" target="_blank">Get more information on how to add more sites or groups.</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('network_addon_faq'))); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="b2sUserLang" value="<?php echo substr(B2S_LANGUAGE, 0, 2); ?>">
<input type="hidden" id="b2sServerUrl" value="<?php echo B2S_PLUGIN_SERVER_URL; ?>">
<input type="hidden" id="b2sUserVersion" value="<?php echo B2S_PLUGIN_USER_VERSION; ?>">
<input type="hidden" id="b2s-redirect-url-sched-post" value="<?php echo $b2sSiteUrl . 'wp-admin/admin.php?page=blog2social-sched'; ?>"/>