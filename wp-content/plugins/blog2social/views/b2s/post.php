<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
/* Data */
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Filter.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
require_once (B2S_PLUGIN_DIR . 'includes/Options.php');
$optionsCuration = new B2S_Options((int) B2S_PLUGIN_BLOG_USER_ID);
$optionPostFiltersCuration = $optionsCuration->_getOption('post_filters');
$postsPerPageCuration = (isset($optionPostFiltersCuration['postsPerPage']) && (int) $optionPostFiltersCuration['postsPerPage'] > 0) ? (int) $optionPostFiltersCuration['postsPerPage'] : 25;
$userLang = strtolower(substr(get_locale(), 0, 2));
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionUserTimeZone = $options->_getOption('user_time_zone');
$userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
$userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
$selSchedDate = (isset($_GET['schedDate']) && !empty($_GET['schedDate'])) ? wp_date("Y-m-d H:i:s", (strtotime(sanitize_text_field(wp_unslash($_GET['schedDate'])) . ' ' . gmdate('H:i:s'))), new DateTimeZone(date_default_timezone_get())) : "";
$isImagePro = (B2S_PLUGIN_USER_VERSION < 2) ? ' <span class="label label-success">' . esc_html__('Pro', 'blog2social') . '</span>' : '';

$assConnected = false;
$assWordsOpen = 0;
$assWordsTotal = 0;
$assOptions = new B2S_Options((int) B2S_PLUGIN_BLOG_USER_ID, 'B2S_PLUGIN_USER_TOOL');
$assOptionsData = $assOptions->_getOption(1);
global $wpdb;

if ($wpdb->get_var($wpdb->prepare("SELECT `id`, `access_token` FROM `{$wpdb->prefix}b2s_user_tool` WHERE `blog_user_id` = %d AND `tool_id` = 1", (int) B2S_PLUGIN_BLOG_USER_ID))) {
    $sqlResult = $wpdb->get_row($wpdb->prepare("SELECT `id`, `access_token` FROM `{$wpdb->prefix}b2s_user_tool` WHERE `blog_user_id` = %d AND `tool_id` = 1", (int) B2S_PLUGIN_BLOG_USER_ID));
    if (isset($sqlResult->id) && (int) $sqlResult->id > 0 && isset($sqlResult->access_token) && !empty($sqlResult->access_token) && isset($assOptionsData['account']['words_open']) && isset($assOptionsData['account']['words_total'])) {
        $assConnected = true;
        $assWordsOpen = (int) $assOptionsData['account']['words_open'];
        $assWordsTotal = (int) $assOptionsData['account']['words_total'];
    }
}
?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
            <div class="col-md-9 del-padding-left del-padding-right">
                <!--Header|Start - Include-->
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                <!--Header|End-->
                <input type="hidden" id="b2s-curation-post-format" value="2">
                <!--Compose Area|Start-->
                <!-- Collapsed bar: shown by default until user expands -->
                <div id="b2s-compose-collapsed-bar">
                    <div class="action-grid">
                        <button type="button" id="b2s-wordpress-expand-btn" class="btn b2s-compose-expand-btn action-card share-post-tab-active">
                            <div class="icon icon-green" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none"
                                    stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <path d="M14 2v6h6"/>
                                    <path d="M8 13h8"/>
                                    <path d="M8 17h8"/>
                                    <path d="M8 9h3"/>
                                </svg>
                            </div>
                            <div class="content share-post-tab-content">
                                <h3 class="break-words"><?php esc_html_e('Share existing WordPress content', 'blog2social'); ?></h3>
                                <p class="break-words hidden-sm hidden-xs"><?php esc_html_e('Select a post or page from your site to share', 'blog2social'); ?></p>
                            </div>
                        </button>
                        <button type="button" id="b2s-compose-expand-btn" class="btn b2s-compose-expand-btn action-card">
                            <div class="icon icon-blue" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none"
                                    stroke="currentColor" stroke-width="2.5"
                                    stroke-linecap="round">
                                    <path d="M12 5v14"/>
                                    <path d="M5 12h14"/>
                                </svg>
                            </div>
                            <div class="content share-post-tab-content">
                                <h3 class="break-words"><?php esc_html_e('Create a new custom post', 'blog2social'); ?></h3>
                                <p class="break-words hidden-sm hidden-xs"><?php esc_html_e('Craft a social post from scratch using text, links, images, or video.', 'blog2social'); ?></p>
                            </div>
                        </button>
                    </div>
                </div>
                <!-- Full compose area: hidden by default -->
                <div class="b2s-curation-section-seperator"></div>
                <div class="col-md-9 del-padding-left del-padding-right b2s-all-loading-area" style="display:none;">
                    <br>
                    <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                    <div class="clearfix"></div>
                    <div class="text-center b2s-loader-text"><?php esc_html_e("Load data...", "blog2social"); ?></div>
                </div>
                <div id="b2s-compose-expand-area" style="display:none;">
                <div class="b2s-compose-outer">
                    <div class="b2s-compose-col-main">
                        <div class="panel panel-default b2s-compose-panel">
                            <div class="panel-body">

                                <!-- Alert messages (JS-controlled, kept for compatibility) -->
                                <div id="b2s-curation-no-review-info" class="alert alert-danger" style="display:none;">
                                    <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('No link preview available. Please check your link.', 'blog2social'); ?>
                                </div>
                                <div id="b2s-curation-no-auth-info" class="alert alert-danger" style="display:none;">
                                    <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('No connected networks. Please make sure to connect at least one social media account.', 'blog2social'); ?>
                                </div>
                                <div id="b2s-curation-no-data-info" class="alert alert-danger" style="display:none;">
                                    <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Invalid data. Please check your data.', 'blog2social'); ?>
                                </div>
                                <div id="b2s-curation-saved-draft-info" class="alert alert-success" style="display:none;">
                                    <span class="glyphicon glyphicon-success glyphicon-ok"></span> <?php esc_html_e('Saved as draft.', 'blog2social'); ?>
                                </div>

                                <!-- Compose header row: avatar + label + save draft -->
                                <div class="b2s-compose-header">
                                    <div class="b2s-compose-header-left">
                                        <span class="b2s-compose-header-label"><?php esc_html_e('What do you want to share today?', 'blog2social'); ?></span>
                                    </div>
                                    <div class="b2s-compose-draft-actions">
                                        <button type="button" id="b2s-btn-compose-save-draft" class="btn btn-link b2s-compose-save-draft-link">
                                            <?php esc_html_e('Save draft', 'blog2social'); ?>
                                        </button>
                                        <button type="button" id="b2s-btn-load-draft" class="btn btn-link b2s-compose-load-draft-link">
                                            <?php esc_html_e('Load draft', 'blog2social'); ?>
                                        </button>
                                    </div>
                                </div>

                                <form id="b2s-curation-post-form" method="post">
                                    <!-- Unified compose input -->
                                    <div class="b2s-compose-unified-input">
                                        <input
                                            type="text"
                                            id="b2s-compose-main-title"
                                            name="title"
                                            class="b2s-compose-main-title"
                                            placeholder="<?php esc_attr_e('Add a title (optional)', 'blog2social'); ?>"
                                        >
                                        <div class="b2s-compose-title-divider"></div>
                                        <div class="b2s-compose-textarea-wrap">
                                            <textarea
                                                id="b2s-compose-main-textarea"
                                                class="form-control b2s-compose-main-textarea b2s-post-item-details-item-message-input"
                                                name="comment"
                                                placeholder="<?php esc_attr_e('Write something, paste a link, or add an image...', 'blog2social'); ?>"
                                                rows="10"
                                            ></textarea>
                                            <button type="button" class="b2s-compose-emoji-inline-btn b2s-post-item-details-item-message-emoji-btn" title="<?php esc_attr_e('Emoji', 'blog2social'); ?>">
                                                <img src="<?php echo esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)); ?>" width="16" height="16" alt="Emoji">
                                            </button>
                                        </div>
                                    </div>
                                    <div class="b2s-curation-ass-btn-group">
                                        <button type="button" class="btn btn-xs btn-ass b2s-post-item-ass-auth-btn" <?php echo $assConnected ? 'style="display:none;"' : ''; ?>><?php esc_html_e('Improve post with AI', 'blog2social'); ?></button>
                                        <button type="button" class="btn btn-xs btn-ass b2s-post-item-ass-create-btn" <?php echo $assConnected ? '' : 'style="display:none;"'; ?>><?php esc_html_e('Rewrite with Assistini AI', 'blog2social'); ?></button>
                                        <button type="button" class="btn btn-xs btn-ass b2s-post-item-ass-reset-btn" <?php echo $assConnected ? '' : 'style="display:none;"'; ?>><?php esc_html_e('Reset', 'blog2social'); ?></button>
                                        <span class="b2s-post-item-textarea-icon-container b2s-curation-ass-loader"><i class="b2s-post-item-textarea-icon"></i></span>
                                        <button type="button" id="b2s-curation-ai-text-tag-btn" class="btn btn-xs b2s-ai-generated-tag-btn b2s-ai-generated-text-tag-btn"><i class="glyphicon glyphicon-flash"></i> <?php esc_html_e('Mark text as AI generated', 'blog2social'); ?></button>
                                    </div>
                                    <input type="hidden" id="b2s-curation-ass-original-message" value="">
                                    <input type="hidden" id="b2s-ship-ass-connected" value="<?php echo esc_attr((int) $assConnected); ?>">
                                    <input type="hidden" id="b2s-ship-ass-words-open" value="<?php echo esc_attr($assWordsOpen); ?>">
                                    <input type="hidden" id="b2s-ship-ass-words-total" value="<?php echo esc_attr($assWordsTotal); ?>">
                                    <input type="hidden" id="b2s-curation-user-lang" value="<?php echo esc_attr($userLang); ?>">

                                    <!-- Validation error messages -->
                                    <div id="b2s-error-text-empty" class="b2s-compose-error-msg" style="display:none; color:#F5365C;">
                                        <span class="glyphicon glyphicon-exclamation-sign" ></span> <?php esc_html_e('Please enter a message.', 'blog2social'); ?>
                                    </div>
                                    <div id="b2s-error-image-empty" class="b2s-compose-error-msg" style="display:none; color:#F5365C;">
                                        <span class="glyphicon glyphicon-exclamation-sign" ></span> <?php esc_html_e('Please add an image.', 'blog2social'); ?>
                                    </div>

                                    <!-- Hidden inputs for Form Submission  -->
                                    <div style="display:none;">
                                        <input type="hidden" class="b2s-image-url-hidden-field" value="" name="image_url">
                                        <input type="hidden" class="b2s-image-id-hidden-field" value="" name="image_id">
                                        <input type="hidden" id="b2s-curation-input-url" name="url" value="" ?>">
                                        <input type="hidden" name="b2s_user_timezone" value="<?php echo esc_attr($userTimeZoneOffset); ?>">
                                        <input type="hidden" id="b2s-post-curation-image-url" name="link_image_url" value="">
                                        <input type="hidden" id="b2s-curation-ai-image-tag-hidden" name="image_is_ai_generated" value="0">
                                        <input type="hidden" id="b2s-curation-ai-text-tag-hidden" name="text_is_ai_generated" value="0">
                                    </div>
                                
                                    <!-- Toolbar row: Image / Video + Settings toggle -->
                                    <div class="b2s-compose-toolbar">
                                        <button type="button" class="btn btn-default btn-sm b2s-compose-toolbar-btn b2s-compose-direct-upload-btn" <?php if (B2S_PLUGIN_USER_VERSION < 2) { echo 'disabled'; } ?>>
                                            <i class="glyphicon glyphicon-picture"></i> <?php esc_html_e('Image', 'blog2social'); ?><?php echo wp_kses($isImagePro, array('span' => array('class' => array()))); ?>
                                        </button>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=blog2social-video')); ?>" class="btn btn-default btn-sm b2s-compose-toolbar-btn">
                                            <i class="glyphicon glyphicon-film"></i> <?php esc_html_e('Video', 'blog2social'); ?>
                                        </a>
                                        <div id="b2s-curation-network-group-wrap" style="display:none;">
                                            <i class="glyphicon glyphicon-user b2s-curation-network-select-glyphicon"></i>
                                            <select id="b2s-curation-preview-profile-select" class="form-control b2s-preview-network-group-select">
                                            </select>
                                            <i class="glyphicon glyphicon-chevron-down b2s-compose-settings-toggle-icon select-chevron"></i>

                                            
                                        </div>
                                        <a class="b2s-preview-network-info-link" href="#" style="vertical-align: middle;"><i class="glyphicon glyphicon-question-sign"></i></a>
                                        <button type="button" id="b2s-compose-settings-toggle" class="btn btn-default btn-sm b2s-compose-toolbar-btn b2s-compose-settings-toggle-btn">
                                            <i class="glyphicon glyphicon-cog"></i>
                                            <span id="b2s-compose-settings-toggle-label"><?php esc_html_e('Advanced', 'blog2social'); ?></span>
                                            <i class="glyphicon glyphicon-chevron-down b2s-compose-settings-toggle-icon"></i>
                                        </button>
                                        <div class="b2s-compose-toolbar-spacer"></div>
                                        <button type="button" id="b2s-btn-curation-customize" class="btn btn-primary btn-lg b2s-btn-curation-customize"><?php esc_html_e('Customize', 'blog2social'); ?></button>
                                        <button type="button" id="b2s-btn-curation-share" class="btn btn-success btn-lg b2s-btn-curation-share"><?php esc_html_e('Share', 'blog2social'); ?></button>
                                    </div>

                                    <!-- Settings panel (collapsible, closed by default) -->
                                    <div id="b2s-compose-settings-panel" style="display:none;">
                                        <!-- Send options: JS fills .b2s-curation-settings-area with ship-type dropdown + network group -->
                                        <div class="b2s-compose-send-options">
                                            <div class="b2s-curation-settings-area" style="display:none;"></div>
                                            <div class="b2s-compose-toggle-row">
                                                <label class="b2s-compose-toggle-wrap">
                                                    <input type="checkbox" name="apply_post_templates" value="1" class="b2s-compose-toggle-input b2s-curation-post-form-apply-post-templates-checkbox" id="b2s-apply-post-templates-toggle">
                                                    <span class="b2s-compose-toggle-slider"></span>
                                                </label>
                                                <span class="b2s-compose-toggle-label"><?php esc_html_e('Apply post templates', 'blog2social'); ?></span>
                                                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('post_templates_without_highlight')); ?>" class="b2s-compose-info-link b2s-post-templates-info-trigger" target="_blank"><?php esc_html_e("What's this?", 'blog2social'); ?></a>
                                            </div>
                                        </div> 
                                    </div>

                                    <input type="hidden" id="b2s-draft-id" value="" name="b2s-draft-id">
                                    <textarea id="b2s-post-curation-comment-dummy" style="display:none;"></textarea>

                                </form>

                               
                            </div>
                        </div>
                         <!-- Re-share area (JS-controlled) -->
                                <div class="row b2s-curation-post-list-area">
                                    <div class="b2s-curation-post-list"></div>
                                </div>

                    </div>

                    <!-- Live preview column -->
                    <div class="b2s-compose-col-preview hidden-sm hidden-xs">
                        <div id="b2s-curation-preview" class="b2s-curation-preview">
                             <div class="col-md-9 del-padding-left del-padding-right b2s-preview-loading-area" style="display:none;">
                                <br>
                                <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                <div class="clearfix"></div>
                                <div class="text-center b2s-loader-text"><?php esc_html_e("Load data...", "blog2social"); ?></div>
                            </div>
                            <div class="panel panel-default b2s-compose-preview-panel">
                                <div class="panel-body">
                                    <div id="b2s-compose-link-status" style="display:none;">
                                        <span class="b2s-badge-link-detected">
                                            <i class="glyphicon glyphicon-link"></i>
                                            <?php esc_html_e('Link post detected', 'blog2social'); ?>
                                        </span>
                                        <span class="b2s-badge-image-detected" style="display:none;">
                                            <i class="glyphicon glyphicon-picture"></i>
                                            <?php esc_html_e('Image post detected', 'blog2social'); ?>
                                        </span>
                                        <span class="b2s-badge-text-detected" style="display:none;">
                                            <i class="glyphicon glyphicon-font"></i>
                                            <?php esc_html_e('Text post detected', 'blog2social'); ?>
                                        </span>
                                        <span id="b2s-compose-og-status" class="b2s-og-loaded-label" style="display:none;">
                                            <?php esc_html_e('OG preview loaded', 'blog2social'); ?>
                                        </span>
                                    </div>
                                    <p class="b2s-compose-preview-label"><?php esc_html_e('Live preview', 'blog2social'); ?></p>
                                    <div class="media">
                                        <div id="b2s-curation-preview-body-account-img" class="b2s-curation-preview-body-account-img">
                                            <div style="display: flex;">
                                                <img style="margin-bottom: 5px; margin-right: 5px; width: 20px;" class="media-object " src="<?php echo esc_url(plugins_url('/assets/images/b2s_icon_large.png', B2S_PLUGIN_FILE)); ?>" alt="Blog2Social">
                                                <h4 class="media-heading">
                                                    Blog2Social
                                                    <small><i><?php esc_html_e('now', 'blog2social'); ?></i></small>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="media-body">
                                            <div class="b2-preview-post-title" style="font-weight: bold; font-size: 18px; margin-bottom: 3px; margin-top: 3px;"></div>
                                            <div id="b2s-curation-preview-body-text" class="b2s-curation-preview-body-text">
                                                <?php esc_html_e('Write something...', 'blog2social'); ?>
                                            </div>
                                        </div>
                                        <div class="b2s-curation-link-preview" style="margin-top: 10px; border: 1px solid lightgray; display:none;">
                                            <div>
                                                <div style="position:relative;display:inline-block;width:100%;">
                                                    <img src="<?php echo esc_url(plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE)); ?>" class="img-responsive b2s-curation-link-preview-image" alt="Link-Preview" style="display:none;">
                                                    <button id="b2s-preview-image-remove-btn" type="button" class="b2s-preview-image-remove-btn" style="display:none;" title="<?php esc_attr_e('Remove image', 'blog2social'); ?>">&times;</button>
                                                    <button type="button" id="b2s-curation-ai-image-tag-btn" class="btn btn-xs b2s-ai-generated-tag-btn b2s-ai-generated-image-tag-btn" style="display:none;"><i class="glyphicon glyphicon-flash"></i> <?php esc_html_e('Mark image as AI generated', 'blog2social'); ?></button>
                                                </div>
                                                <div style="margin-top: 5px; margin-left: 5px; margin-bottom: 5px;">
                                                    <strong style="margin-top: 3px;" class="b2s-curation-link-preview-title"></strong><br>
                                                    <small class="b2s-curation-link-preview-description"></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer" style="background-color: #fff;">
                                    <div class="row text-center text-muted">
                                        <div class="col-xs-4"><i class="glyphicon glyphicon-thumbs-up"></i> <?php esc_html_e('Like', 'blog2social'); ?></div>
                                        <div class="col-xs-4"><i class="glyphicon glyphicon-comment"></i> <?php esc_html_e('Comment', 'blog2social'); ?></div>
                                        <div class="col-xs-4"><i class="glyphicon glyphicon-share"></i> <?php esc_html_e('Share', 'blog2social'); ?></div>
                                    </div>
                                </div>
                                <div class="b2s-compose-preview-sharing-to">
                                    <div id="b2s-curation-no-auth-preview" class="b2s-compose-preview-no-network" style="display:none;">
                                        <i class="glyphicon glyphicon-exclamation-sign"></i>
                                        <?php esc_html_e('No networks connected', 'blog2social'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div><!-- /#b2s-compose-expand-area -->
                <!--Compose Area|End-->

                <!-- WordPress posts list -->
                <div class="b2s-wp-posts-section">

                    <!-- Always-visible: search + toggle button -->
                    <div class="b2s-wp-filter-bar">
                        <div class="b2s-wp-filter-bar-left">
                            <input type="text" class="form-control" id="b2sSortPostTitle" name="b2sSortPostTitle" placeholder="<?php esc_attr_e('Search by title...', 'blog2social'); ?>">
                            <button type="button" class="btn btn-default btn-sm" id="b2s-wp-filter-toggle">
                                <i class="glyphicon glyphicon-filter"></i>
                                <span id="b2s-wp-filter-toggle-label" data-show="<?php esc_attr_e('Show filters', 'blog2social'); ?>" data-hide="<?php esc_attr_e('Hide filters', 'blog2social'); ?>"><?php esc_html_e('Show filters', 'blog2social'); ?></span>
                            </button>
                        </div>
                    </div>

                    <!-- Collapsible filter panel — contains the full B2S_Post_Filter form.
                        Hidden inputs (b2sUserLang, b2sPostBlogId etc.) are readable by jQuery
                        even when the panel is display:none. -->
                    <div id="b2s-wp-filter-panel" style="display:none;">
                        <form class="b2sSortForm form-inline" action="#">
                            <input id="b2sType" type="hidden" value="all" name="b2sType">
                            <input id="b2sShowByDate" type="hidden" value="" name="b2sShowByDate">
                            <input id="b2sPagination" type="hidden" value="1" name="b2sPagination">
                            <?php
                            $postFilterCuration = new B2S_Post_Filter('curation');
                            echo wp_kses($postFilterCuration->getItemHtml(), array(
                                'div'    => array('class' => array()),
                                'input'  => array('id' => array(), 'name' => array(), 'class' => array(), 'value' => array(), 'type' => array(), 'placeholder' => array()),
                                'a'      => array('href' => array(), 'id' => array(), 'class' => array()),
                                'span'   => array('class' => array()),
                                'small'  => array(),
                                'select' => array('id' => array(), 'name' => array(), 'class' => array()),
                                'option' => array('value' => array(), 'selected' => array()),
                            ));
                            ?>
                        </form>
                    </div>

                    <!-- Posts list (curation.draft.js fills these) -->
                    <div class="b2s-wp-posts-list-area">
                    <div class="b2s-loading-area" style="display:none;">
                        <br>
                        <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                        <div class="clearfix"></div>
                        <div class="text-center b2s-loader-text"><?php esc_html_e("Load data...", "blog2social"); ?></div>
                    </div>
                            <div class="b2s-server-connection-fail alert alert-danger" style="display:none;">
                            <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Server connection failed. Please try again.', 'blog2social'); ?>
                        </div>
                        <div class="b2s-sort-result-area" style="display:none;">
                            <div class="b2s-sort-result-item-area"></div>
                            <div class="b2s-sort-pagination-area">
                                <div class="btn-group btn-group-sm pull-right b2s-post-per-page-area hidden-xs" role="group">
                                    <button type="button" class="btn <?php echo ((int) $postsPerPageCuration == 25) ? 'btn-primary' : 'btn-default'; ?> b2s-post-per-page" data-post-per-page="25">25</button>
                                    <button type="button" class="btn <?php echo ((int) $postsPerPageCuration == 50) ? 'btn-primary' : 'btn-default'; ?> b2s-post-per-page" data-post-per-page="50">50</button>
                                    <button type="button" class="btn <?php echo ((int) $postsPerPageCuration == 100) ? 'btn-primary' : 'btn-default'; ?> b2s-post-per-page" data-post-per-page="100">100</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs for JS -->
                <input type="hidden" id="b2sSelSchedDate" value="<?php echo esc_attr((($selSchedDate != "") ? strtotime($selSchedDate) . '000' : '')); ?>">
                <input type="hidden" id="b2sServerUrl" value="<?php echo esc_attr(B2S_PLUGIN_SERVER_URL); ?>">
                <input type="hidden" id="b2sJsTextPublish" value="<?php esc_attr_e('published', 'blog2social'); ?>">
                <input type="hidden" id="b2sEmojiTranslation" value='<?php echo esc_attr(json_encode(B2S_Tools::getEmojiTranslationList())); ?>'>
                <input type="hidden" id="b2sDefaultNoImage" value="<?php echo esc_url(plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE)); ?>">
                <input type="hidden" id="b2sMaxSchedDate" value="<?php echo esc_attr(wp_date('Y-m-d', strtotime("+ 3 years"), new DateTimeZone(date_default_timezone_get()))); ?>">
                <input type="hidden" id="b2s_user_version" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION); ?>">
                
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php'); ?> 
            </div>
        </div>
    </div>
</div>

<!-- Modals (unchanged) -->
<div class="modal fade b2s-publish-approve-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-publish-approve-modal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e('Do you want to mark this post as published ?', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" value="" id="b2s-approve-network-auth-id">
                <input type="hidden" value="" id="b2s-approve-post-id">
                <button class="btn btn-success b2s-approve-publish-confirm-btn"><?php esc_html_e('YES', 'blog2social'); ?></button>
                <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade b2s-licence-condition-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-licence-condition-modal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-licence-condition-modal">&times;</button>
                <h4 class="modal-title licence-condition-daily-modal-title"><?php esc_html_e("You've reached your daily posting limit!", "blog2social"); ?></h4>
                <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>
                    <h4 class="modal-title licence-condition-sched-modal-title b2s-info-display-none"><?php esc_html_e("You've reached your posting limit!", "blog2social"); ?></h4>
                <?php } ?>
            </div>
            <div class="modal-body">
                <p><?php esc_html_e('To increase your limit and enjoy more features, consider upgrading.', 'blog2social'); ?></p>
                <br>
                <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('upgrade_version')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade', 'blog2social'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php
$modalNames = array("b2sPreFeatureScheduleModal");
include (B2S_PLUGIN_DIR . 'views/b2s/partials/general-modal.php');
?>

<div class="modal fade" id="b2sInfoCCModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoCCModal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="b2s-modal-header-image"><?php esc_html_e('You want to create image posts with any image from your media library?', 'blog2social'); ?></h4>
                <h4 class="modal-title" id="b2s-modal-header-text"><?php esc_html_e('You want to create text posts?', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body">
                <?php if (B2S_PLUGIN_USER_VERSION <= 1) { ?>
                    <p><?php esc_html_e('With Blog2Social you can share your WordPress posts and pages as well as create your own social media posts to share any content based on text, links, images, or video links, or even third-party content from any sources. This enables you to manage all your social media content in one place directly from your WordPress dashboard. Schedule and share link posts, text posts, image posts, and video posts (video links, for example from Youtube) and provide your followers with the best content-mix on your social media networks.', 'blog2social'); ?></p>
                    <br>
                    <p class="b2s-bold"><?php esc_html_e('Unlock Blog2Social Premium Pro to create and share image posts, video links, and text posts from any source.', 'blog2social'); ?></p>
                    <br>
                    <?php esc_html_e('Share image posts:', 'blog2social'); ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Grab more attention for your content with photos, videos, or infographics.', 'blog2social'); ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Share images to get them into the Google image search to further increase your outreach and traffic from search engines.', 'blog2social'); ?><br>
                    <br>
                    <?php esc_html_e('Share text posts:', 'blog2social'); ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Share pure text messages and personal comments with your followers and readers.', 'blog2social'); ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Use hashtags, @mentions, or emojis to share your feelings.', 'blog2social'); ?><br>
                    <br>
                    <?php
                    /* translators: %s: URL to the social media posts guide */
                    echo wp_kses(sprintf(__('Learn more about how to share social media posts in the <a href="%s" target="_blank">social media posts guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('cc_info_faq'))), array('a' => array('href' => array(), 'target' => array())));
                    ?>
                    <br>
                    <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('upgrade_version')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade to PRO and above', 'blog2social'); ?></a>
                    <br>
                    <div style="text-align:center;"><?php
                    /* translators: %s: URL to the Blog2Social Premium trial page */
                    echo wp_kses(sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')), array('a' => array('href' => array(), 'target' => array())));
                    ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sTextPostInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sTextPostInfoModal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Social Media Posts', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php esc_html_e('Text posts enable you to share pure text messages and personal comments with your followers and readers. You can also customize your posts with individual hashtags, @mentions, or emojis.', 'blog2social'); ?></p>
                <p><?php
                /* translators: %s: URL to the social media text post guide */
                echo wp_kses(sprintf(__('Get more information on how to share a text post with hashtags, @mentions and emojis in the <a href="%s" target="_blank">social media posts guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('cc_text_post_info'))), array('a' => array('href' => array(), 'target' => array()))); ?></p>
                <p><?php
                /* translators: %s: URL to the network settings page */
                echo wp_kses(sprintf(__('In the <a href="%s">Network Settings</a> you can define one or more network selections for your posts.', 'blog2social'), 'admin.php?page=blog2social-network'), array('a' => array('href' => array(), 'target' => array()))); ?></p>
            </div>
        </div>
    </div>
</div>

<div id="b2s-network-select-image" class="modal fade" role="dialog" aria-labelledby="b2s-network-select-image" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-select-image">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Select image', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php
                        require_once B2S_PLUGIN_DIR . 'includes/B2S/Ship/Image.php';
                        $image = new B2S_Ship_Image('curation');
                        echo wp_kses($image->getItemHtml(0, '', '', $userLang), array(
                            'div' => array('class' => array(), 'style' => array()),
                            'span' => array('id' => array()),
                            'a' => array('target' => array(), 'href' => array()),
                            'i' => array('class' => array()),
                            'label' => array('for' => array()),
                            'img' => array('class' => array(), 'alt' => array(), 'src' => array()),
                            'input' => array('class' => array(), 'type' => array(), 'value' => array(), 'id' => array(), 'name' => array(), 'checked' => array()),
                            'br' => array(),
                            'button' => array('class' => array(), 'data-network-id' => array(), 'data-post-id' => array(), 'data-network-auth-id' => array(), 'data-meta-type' => array(), 'data-image-count' => array(), 'style' => array())
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="b2s-posttype-info-modal" class="modal fade" role="dialog" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-xlg" style="width: 1100px";>
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-posttype-info-modal">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('What is a Link Post / Image Post?', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body" style="text-align:center; padding: 0px !important;">
                <div class="b2s-posttype-info-modal-wrapper">
    <!-- existing component -->

                <!-- translators: %s: URL to the social media posts guide -->
                <main class="b2s-posttype-info-modal-page">
                    <section class="b2s-posttype-info-modal-comparison">
                    <!-- LINK POST -->
                    <article class="b2s-posttype-info-modal-panel b2s-posttype-info-modal-panel--link">
                        <div class="b2s-posttype-info-modal-panel__header">
                        <div class="b2s-posttype-info-modal-type-pill b2s-posttype-info-modal-type-pill--blue">
                            <span class="b2s-posttype-info-modal-icon b2s-posttype-info-modal-icon--link" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.07.07l2-2A5 5 0 0 0 12 4l-1.15 1.15"/><path d="M14 11a5 5 0 0 0-7.07-.07l-2 2A5 5 0 0 0 12 20l1.15-1.15"/></svg>
                            </span>
                            <?php esc_html_e('LINK POST', 'blog2social'); ?>
                        </div>
                        <h2><?php esc_html_e('Share website content', 'blog2social'); ?><br><?php esc_html_e('with an automatic preview', 'blog2social'); ?></h2>
                        <p><?php esc_html_e('Perfect for blog articles, news, guides and external links.', 'blog2social'); ?></p>
                        </div>

                        <div class="b2s-posttype-info-modal-visual b2s-posttype-info-modal-visual--link">
                        <div class="b2s-posttype-info-modal-browser-card">
                            <div class="b2s-posttype-info-modal-browser-bar">
                            <span></span><span></span><span></span>
                            <small><?php esc_html_e('www.your-blog.com', 'blog2social'); ?></small>
                            </div>
                            <div class="b2s-posttype-info-modal-browser-body">
                            <div class="b2s-posttype-info-modal-skeleton b2s-posttype-info-modal-skeleton--short"></div>
                            <div class="b2s-posttype-info-modal-skeleton"></div>
                            <div class="b2s-posttype-info-modal-skeleton b2s-posttype-info-modal-skeleton--medium"></div>
                            <div class="b2s-posttype-info-modal-browser-content">
                                <div class="b2s-posttype-info-modal-fake-landscape">
                                <span class="b2s-posttype-info-modal-sun"></span>
                                <span class="b2s-posttype-info-modal-mountain b2s-posttype-info-modal-mountain--one"></span>
                                <span class="b2s-posttype-info-modal-mountain b2s-posttype-info-modal-mountain--two"></span>
                                </div>
                                <div class="b2s-posttype-info-modal-side-lines">
                                <i></i><i></i><i></i><i></i>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="b2s-posttype-info-modal-connector b2s-posttype-info-modal-connector--blue">
                            <span class="b2s-posttype-info-modal-connector__dots"><?php esc_html_e('•••', 'blog2social'); ?></span>
                            <span class="b2s-posttype-info-modal-connector__circle"><?php esc_html_e('↗', 'blog2social'); ?></span>
                            <span class="b2s-posttype-info-modal-connector__arrow"><?php esc_html_e('➜', 'blog2social'); ?></span>
                        </div>

                        <div class="b2s-posttype-info-modal-social-card b2s-posttype-info-modal-social-card--facebook">
                            <div class="b2s-posttype-info-modal-social-head">
                            <span class="b2s-posttype-info-modal-social-logo">f</span>
                            <span class="b2s-posttype-info-modal-social-name"></span>
                            </div>
                            <p class="b2s-posttype-info-modal-social-copy"><?php esc_html_e('Check out this helpful', 'blog2social'); ?><br><?php esc_html_e('article!', 'blog2social'); ?></p>
                            <div class="b2s-posttype-info-modal-preview-image">
                            <span class="b2s-posttype-info-modal-sun"></span>
                            <span class="b2s-posttype-info-modal-mountain b2s-posttype-info-modal-mountain--one"></span>
                            <span class="b2s-posttype-info-modal-mountain b2s-posttype-info-modal-mountain--two"></span>
                            </div>
                            <div class="b2s-posttype-info-modal-preview-meta">
                            <small><?php esc_html_e('YOUR-WEBSITE.COM', 'blog2social'); ?></small>
                            <strong><?php esc_html_e('10 Tips for Successful', 'blog2social'); ?><br><?php esc_html_e('Social Media Marketing', 'blog2social'); ?></strong>
                            <p><?php esc_html_e('Proven strategies to grow your reach', 'blog2social'); ?><br><?php esc_html_e('and achieve your goals.', 'blog2social'); ?></p>
                            </div>
                            <div class="b2s-posttype-info-modal-social-footer">
                            <span><?php esc_html_e('👍 ❤️ 12', 'blog2social'); ?></span><span><?php esc_html_e('2 Comments', 'blog2social'); ?></span><span><?php esc_html_e('5 Shares', 'blog2social'); ?></span>
                            </div>
                        </div>
                        </div>

                        <div class="b2s-posttype-info-modal-feature-list">
                        <div class="b2s-posttype-info-modal-feature">
                            <span class="b2s-posttype-info-modal-feature-icon b2s-posttype-info-modal-feature-icon--blue">✣</span>
                            <div><h3><?php esc_html_e('Automatic link preview', 'blog2social'); ?></h3><p><?php esc_html_e('Social networks automatically create a preview', 'blog2social'); ?><br><?php esc_html_e('from your website content.', 'blog2social'); ?></p></div>
                        </div>
                        <div class="b2s-posttype-info-modal-feature">
                            <span class="b2s-posttype-info-modal-feature-icon b2s-posttype-info-modal-feature-icon--blue">➤</span>
                            <div><h3><?php esc_html_e('Clicks drive traffic to your website', 'blog2social'); ?></h3><p><?php esc_html_e('Users click through to read the full content', 'blog2social'); ?><br><?php esc_html_e('on your website.', 'blog2social'); ?></p></div>
                        </div>
                        <div class="b2s-posttype-info-modal-feature">
                            <span class="b2s-posttype-info-modal-feature-icon b2s-posttype-info-modal-feature-icon--blue">&lt;/&gt;</span>
                            <div><h3><?php esc_html_e('Uses Open Graph metadata', 'blog2social'); ?></h3><p><?php esc_html_e('The preview is based on the OG data', 'blog2social'); ?><br><?php esc_html_e('from your website.', 'blog2social'); ?></p></div>
                        </div>
                        </div>

                        <div class="b2s-posttype-info-modal-info-box b2s-posttype-info-modal-info-box--blue">
                        <p><?php esc_html_e('When you paste a link, social networks automatically generate', 'blog2social'); ?><br><?php esc_html_e('a preview based on your website content.', 'blog2social'); ?></p>
                        </div>
                    </article>

                    <div class="b2s-posttype-info-modal-vs-badge"><?php esc_html_e('VS.', 'blog2social'); ?></div>

                    <!-- IMAGE POST -->
                    <article class="b2s-posttype-info-modal-panel b2s-posttype-info-modal-panel--image">
                        <div class="b2s-posttype-info-modal-panel__header">
                        <div class="b2s-posttype-info-modal-type-pill b2s-posttype-info-modal-type-pill--orange">
                            <span class="b2s-posttype-info-modal-icon b2s-posttype-info-modal-icon--image" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="1"/><circle cx="8" cy="9" r="1.5"/><path d="m4 18 5-5 3 3 2-2 6 4"/></svg>
                            </span>
                            <?php esc_html_e('IMAGE POST', 'blog2social'); ?>
                        </div>
                        <h2><?php esc_html_e('Upload images directly', 'blog2social'); ?><br><?php esc_html_e('to social media', 'blog2social'); ?></h2>
                        <p><?php esc_html_e('Ideal for promotions, announcements, quotes and visual campaigns.', 'blog2social'); ?></p>
                        </div>

                        <div class="b2s-posttype-info-modal-visual b2s-posttype-info-modal-visual--image">
                        <div class="b2s-posttype-info-modal-upload-box">
                            <div class="b2s-posttype-info-modal-upload-icon">↑</div>
                            <strong><?php esc_html_e('Upload image', 'blog2social'); ?></strong>
                            <span><?php esc_html_e('No link required', 'blog2social'); ?></span>
                        </div>

                        <div class="b2s-posttype-info-modal-connector b2s-posttype-info-modal-connector--orange">
                            <span class="b2s-posttype-info-modal-connector__dots"><?php esc_html_e('•••', 'blog2social'); ?></span>
                            <span class="b2s-posttype-info-modal-connector__arrow"><?php esc_html_e('➜', 'blog2social'); ?></span>
                        </div>

                        <div class="b2s-posttype-info-modal-social-card b2s-posttype-info-modal-social-card--instagram">
                            <div class="b2s-posttype-info-modal-social-head">
                            <span class="b2s-posttype-info-modal-social-logo b2s-posttype-info-modal-social-logo--ig"><?php esc_html_e('◎', 'blog2social'); ?></span>
                            <span class="b2s-posttype-info-modal-social-name"></span>
                            </div>
                            <div class="b2s-posttype-info-modal-sale-art">
                            <div class="b2s-posttype-info-modal-sale-copy">
                                <strong><?php esc_html_e('SUMMER', 'blog2social'); ?><br><?php esc_html_e('SALE', 'blog2social'); ?></strong>
                                <small><?php esc_html_e('UP TO', 'blog2social'); ?></small>
                                <b><?php esc_html_e('50%', 'blog2social'); ?><br><?php esc_html_e('OFF', 'blog2social'); ?></b>
                            </div>
                            <div class="b2s-posttype-info-modal-leaf b2s-posttype-info-modal-leaf--one"></div>
                            <div class="b2s-posttype-info-modal-leaf b2s-posttype-info-modal-leaf--two"></div>
                            <div class="b2s-posttype-info-modal-orange-fruit"><span></span></div>
                            </div>
                            <div class="b2s-posttype-info-modal-instagram-actions"><?php esc_html_e('♥ ♡ ✈       ♧', 'blog2social'); ?></div>
                            <div class="b2s-posttype-info-modal-likes"><?php esc_html_e('♥ 128 Likes', 'blog2social'); ?></div>
                        </div>
                        </div>

                        <div class="b2s-posttype-info-modal-feature-list">
                        <div class="b2s-posttype-info-modal-feature">
                            <span class="b2s-posttype-info-modal-feature-icon b2s-posttype-info-modal-feature-icon--orange"><?php esc_html_e('◉', 'blog2social'); ?></span>
                            <div><h3><?php esc_html_e('High visual impact', 'blog2social'); ?></h3><p><?php esc_html_e('Images grab more attention and engagement', 'blog2social'); ?><br><?php esc_html_e('in the feed.', 'blog2social'); ?></p></div>
                        </div>
                        <div class="b2s-posttype-info-modal-feature">
                            <span class="b2s-posttype-info-modal-feature-icon b2s-posttype-info-modal-feature-icon--orange"><?php esc_html_e('▯', 'blog2social'); ?></span>
                            <div><h3><?php esc_html_e('Native appearance in the feed', 'blog2social'); ?></h3><p><?php esc_html_e('The image is uploaded directly and displayed', 'blog2social'); ?><br><?php esc_html_e('natively on the platform.', 'blog2social'); ?></p></div>
                        </div>
                        <div class="b2s-posttype-info-modal-feature">
                            <span class="b2s-posttype-info-modal-feature-icon b2s-posttype-info-modal-feature-icon--orange"><?php esc_html_e('◎', 'blog2social'); ?></span>
                            <div><h3><?php esc_html_e('Ideal for branding & campaigns', 'blog2social'); ?></h3><p><?php esc_html_e('Perfect for promotions, announcements and content', 'blog2social'); ?><br><?php esc_html_e('that should stand out in the feed.', 'blog2social'); ?></p></div>
                        </div>
                        </div>

                        <div class="b2s-posttype-info-modal-info-box b2s-posttype-info-modal-info-box--orange">
                        <p><?php esc_html_e('Images are uploaded directly to the network', 'blog2social'); ?><br><?php esc_html_e('instead of generating a website preview.', 'blog2social'); ?></p>
                        </div>
                    </article>
                    </section>
                </main>
                <!-- translators: %s: URL to the social media posts guide -->
                </div>
            </div>
        </div>
    </div>
</div>

<div id="b2s-calendar-modal" class="modal fade" role="dialog" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-calendar-modal">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Scheduled posts', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body b2s-calendar-modal-body" data-language="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)); ?>">
                <div class="b2s-cal-header">
                    <button type="button" class="btn btn-default btn-sm" id="b2s-cal-prev"><i class="glyphicon glyphicon-chevron-left"></i></button>
                    <span id="b2s-cal-title" class="b2s-cal-title"></span>
                    <button type="button" class="btn btn-default btn-sm" id="b2s-cal-next"><i class="glyphicon glyphicon-chevron-right"></i></button>
                </div>
                <div id="b2s-cal-loading" class="b2s-cal-loading" style="display:none;">
                    <i class="glyphicon glyphicon-refresh b2s-spin"></i> <?php esc_html_e('Loading...', 'blog2social'); ?>
                </div>
                <div id="b2s-cal-grid"></div>
                <div id="b2s-cal-day-detail" class="b2s-cal-day-detail" style="display:none;">
                    <h5 id="b2s-cal-day-detail-title" class="b2s-cal-day-detail-title"></h5>
                    <ul id="b2s-cal-day-detail-list" class="b2s-cal-day-list"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="b2s-load-draft-modal" class="modal fade" role="dialog" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-load-draft-modal">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Load draft', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body">
                <div id="b2s-load-draft-loading" style="text-align:center; padding:20px;">
                    <i class="glyphicon glyphicon-refresh b2s-spin"></i>
                </div>
                <div id="b2s-load-draft-empty" style="display:none; text-align:center; padding:20px; color:#888;">
                    <?php esc_html_e('No drafts saved yet.', 'blog2social'); ?>
                </div>
                <ul id="b2s-load-draft-list" class="list-group" style="display:none; margin-bottom:0;"></ul>
            </div>
        </div>
    </div>
</div>

<div id="b2sInfoNetworkModal" class="modal fade" role="dialog" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoNetworkModal">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Network collections', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body b2s-ni-body">
                <div class="b2s-ni-wrap">
                    <!-- Title -->
                    <div class="b2s-ni-title-row">
                        <div class="b2s-ni-title-icon">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        </div>
                        <div>
                            <div class="b2s-ni-title-text"><?php esc_html_e('How Network Collections Work', 'blog2social'); ?></div>
                            <div class="b2s-ni-subtitle"><?php esc_html_e('Organize your connected social media accounts and publish smarter.', 'blog2social'); ?></div>
                        </div>
                    </div>

                    <!-- Steps -->
                    <div class="b2s-ni-steps">

                        <!-- Step 1 -->
                        <div class="b2s-ni-step">
                            <div class="b2s-ni-step-head">
                                <span class="b2s-ni-step-num">1</span>
                                <strong class="b2s-ni-step-title"><?php esc_html_e('Connect your social accounts', 'blog2social'); ?></strong>
                            </div>
                            <p class="b2s-ni-step-body"><?php esc_html_e('Share links, images, or videos to your connected accounts.', 'blog2social'); ?></p>
                            <div class="b2s-ni-connect-row">
                                <div class="b2s-ni-icon-col">
                                    <?php foreach (array(1, 45, 12, 3, 32, 36) as $b2sNid) : ?>
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/' . (int) $b2sNid . '_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-sm" alt="">
                                    <?php endforeach; ?>
                                </div>
                                <span class="b2s-ni-arrow">&#8594;</span>
                                <div class="b2s-ni-dest">
                                    <div class="b2s-ni-dest-icon">
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    </div>
                                    <span class="b2s-ni-dest-label"><?php esc_html_e('Connected accounts', 'blog2social'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="b2s-ni-step-arrow">&#8594;</div>

                        <!-- Step 2 -->
                        <div class="b2s-ni-step">
                            <div class="b2s-ni-step-head">
                                <span class="b2s-ni-step-num">2</span>
                                <strong class="b2s-ni-step-title"><?php esc_html_e('All accounts go into the default group "My Profile"', 'blog2social'); ?></strong>
                            </div>
                            <p class="b2s-ni-step-body"><?php esc_html_e('By default, every connected network is automatically added to "My Profile".', 'blog2social'); ?></p>
                            <div class="b2s-ni-group-box">
                                <div class="b2s-ni-group-head">
                                    <div class="b2s-ni-group-icon">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="#fff" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </div>
                                    <strong class="b2s-ni-group-name"><?php esc_html_e('My Profile', 'blog2social'); ?></strong>
                                </div>
                                <div class="b2s-ni-group-icons">
                                    <?php foreach (array(1, 45, 12, 3, 32, 36) as $b2sNid) : ?>
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/' . (int) $b2sNid . '_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-md" alt="">
                                    <?php endforeach; ?>
                                </div>
                                <div class="b2s-ni-group-label"><?php esc_html_e('Standard group (automatic)', 'blog2social'); ?></div>
                            </div>
                        </div>

                        <div class="b2s-ni-step-arrow">&#8594;</div>

                        <!-- Step 3 (Pro) -->
                        <div class="b2s-ni-step b2s-ni-step-pro">
                            <div class="b2s-ni-step-head b2s-ni-step-head-wrap">
                                <span class="b2s-ni-step-num b2s-ni-step-num-pro">3</span>
                                <strong class="b2s-ni-step-title b2s-ni-step-title-pro"><?php esc_html_e('With Pro, create more custom groups', 'blog2social'); ?></strong>
                                <span class="b2s-ni-pro-badge"><?php esc_html_e('PRO', 'blog2social'); ?></span>
                            </div>
                            <p class="b2s-ni-step-body b2s-ni-step-body-sm"><?php esc_html_e('Create additional network groups to organize your profiles more flexibly.', 'blog2social'); ?></p>
                            <div class="b2s-ni-custom-groups">
                                <div class="b2s-ni-cg-row b2s-ni-cg-row-green">
                                    <div class="b2s-ni-cg-label-wrap">
                                        <div class="b2s-ni-cg-icon b2s-ni-cg-icon-green">
                                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#22c55e" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        </div>
                                        <span class="b2s-ni-cg-name b2s-ni-cg-name-green"><?php esc_html_e('Team A', 'blog2social'); ?></span>
                                    </div>
                                    <div class="b2s-ni-cg-icons">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-xs" alt="">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/45_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-xs" alt="">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/3_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-xs" alt="">
                                    </div>
                                </div>
                                <div class="b2s-ni-cg-row b2s-ni-cg-row-purple">
                                    <div class="b2s-ni-cg-label-wrap">
                                        <div class="b2s-ni-cg-icon b2s-ni-cg-icon-purple">
                                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#a855f7" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        </div>
                                        <span class="b2s-ni-cg-name b2s-ni-cg-name-purple"><?php esc_html_e('Clients', 'blog2social'); ?></span>
                                    </div>
                                    <div class="b2s-ni-cg-icons">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/12_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-xs" alt="">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/3_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-xs" alt="">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/32_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-xs" alt="">
                                    </div>
                                </div>
                                <div class="b2s-ni-cg-row b2s-ni-cg-row-blue">
                                    <div class="b2s-ni-cg-label-wrap">
                                        <div class="b2s-ni-cg-icon b2s-ni-cg-icon-blue">
                                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </div>
                                        <span class="b2s-ni-cg-name b2s-ni-cg-name-blue"><?php esc_html_e('My Profile', 'blog2social'); ?></span>
                                    </div>
                                    <div class="b2s-ni-cg-icons">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-xs" alt="">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/45_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-xs" alt="">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/12_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-xs" alt="">
                                        <img src="<?php echo esc_url(plugins_url('/assets/images/portale/36_flat.png', B2S_PLUGIN_FILE)); ?>" class="b2s-ni-net-icon-xs" alt="">
                                    </div>
                                </div>
                                <div class="b2s-ni-group-label"><?php esc_html_e('Custom groups (Pro feature)', 'blog2social'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom info row -->
                    <div class="b2s-ni-info-row">
                        <div class="b2s-ni-info-item">
                            <div class="b2s-ni-info-icon b2s-ni-info-icon-circle">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            </div>
                            <div>
                                <strong class="b2s-ni-info-title"><?php esc_html_e('Manage your collections in Network Settings', 'blog2social'); ?></strong>
                                <p class="b2s-ni-info-text"><?php esc_html_e('Create and manage your Network Collections in Blog2Social.', 'blog2social'); ?></p>
                            </div>
                        </div>
                        <div class="b2s-ni-info-item">
                            <div class="b2s-ni-info-icon b2s-ni-info-icon-square">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                            </div>
                            <div>
                                <strong class="b2s-ni-info-title"><?php esc_html_e('Open "Networks" to create a Collection.', 'blog2social'); ?></strong>
                                <p class="b2s-ni-info-text"><?php esc_html_e('Go to Network Settings and open "Networks".', 'blog2social'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="b2s-ni-open-btn-wrap">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=blog2social-network')); ?>" class="btn btn-primary"><?php esc_html_e('Open Networks', 'blog2social'); ?></a>
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
                <button class="btn btn-danger b2s-all-posts-delete-confirm-btn"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade b2s-delete-sched-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-delete-sched-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-delete-sched-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Delete entries from the scheduling', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <b><?php esc_html_e('You are sure you want to delete entries from the scheduling?', 'blog2social') ?> </b>
                <br>
                (<?php esc_html_e('Number of entries', 'blog2social') ?>:  <span id="b2s-delete-confirm-post-count"></span>)
                <input type="hidden" value="" id="b2s-delete-confirm-post-id">
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
                <button class="btn btn-danger b2s-all-sched-posts-delete-confirm-btn"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sTwitterInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sTwitterInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sTwitterInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Select X profile:', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('To comply with the X TOS and to avoid duplicate posts, posts will be sent to your primary X profile.', 'blog2social') ?> <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('network_tos_faq_032018')) ?>"><?php esc_html_e('More information', 'blog2social') ?></a>
            </div>
        </div>
    </div>
</div>