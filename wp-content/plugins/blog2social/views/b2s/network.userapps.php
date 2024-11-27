<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
require_once B2S_PLUGIN_DIR . 'includes/B2S/Network/UserApp.php';
$appItem = new B2S_Network_UserApp();

$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$supportedNetworks = unserialize(B2S_PLUGIN_USER_APP_NETWORKS);
$networks = unserialize(B2S_PLUGIN_NETWORK);
$appData = $appItem->getData();
$isPremium = (B2S_PLUGIN_USER_VERSION > 0 && !defined("B2S_PLUGIN_TRAIL_END")) ? true : false;
?>

<div class="b2s-container">
    <div class=" b2s-inbox col-md-12 del-padding-left">
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>

        <div class="col-md-9 del-padding-left del-padding-right">

            <!--Header|Start - Include-->
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
            <!--Header|End-->
            <div class="clearfix"></div>
            <!--Content|Start-->
            <div class="panel panel-default">
                <div class="panel-body">             
                    <?php
                    echo wp_kses($appItem->getItemHtml($appData), array(
                        'div' => array(
                            'class' => array(),
                            'data-b2s-auth-info' => array(),
                        ),
                        'form' => array(
                            'id' => array(),
                            'method' => array(),
                        ),
                        'input' => array(
                            'id' => array(),
                            'type' => array(),
                            'value' => array(),
                            'name' => array(),
                            'class' => array(),
                            'readonly' => array(),
                            'data-network-auth-id' => array(),
                            'data-network-mandant-id' => array(),
                            'data-network-id' => array(),
                            'data-network-type' => array(),
                            'data-network-container-mandant-id' => array(),
                        ),
                        'ul' => array(
                            'class' => array(),
                            'data-mandant-id' => array(),
                            'style' => array(),
                            'data-network-id' => array(),
                            'data-network-count' => array(),
                            'data-network-mandant-id' => array(),
                        ),
                        'li' => array(
                            'class' => array(),
                            'data-network-id' => array(),
                            'data-network-mandant-id' => array(),
                            'data-view' => array(),
                            'data-network-auth-id' => array(),
                            'data-network-type' => array(),
                            'data-app-id' => array(),
                        ),
                        'img' => array(
                            'class' => array(),
                            'alt' => array(),
                            'src' => array(),
                        ),
                        'h4' => array(),
                        'a' => array(
                            'href' => array(),
                            'target' => array(),
                            'class' => array(),
                            'onclick' => array(),
                            'data-title' => array(),
                            'data-type' => array(),
                            'data-network-type' => array(),
                            'data-network-id' => array(),
                            'data-network-auth-id' => array(),
                            'data-network-mandant-id' => array(),
                            'data-modal-title' => array(),
                            'data-connection-owner' => array(),
                            'data-auth-method' => array(),
                            'data-app-id' => array(),
                            'data-app-name' => array(),
                            'data-app-key' => array(),
                            'data-app-secret' => array(),
                            'disabled' => array(),
                        ),
                        'span' => array(
                            'class' => array(),
                            'data-network-count-trigger' => array(),
                            'data-network-id' => array(),
                            'data-network-mandant-id' => array(),
                            'data-network-auth-id' => array(),
                            'data-app-id' => array(),
                            'style' => array(),
                            'id' => array()
                        ),
                        'button' => array(
                            'class' => array(),
                            'data-title' => array(),
                            'data-type' => array(),
                            'onclick' => array(),
                            'data-network-id' => array(),
                            'data-app-id' => array(),
                            'data-app-name' => array(),
                            'disabled' => array(),
                        ),
                    ));
                    require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php');
                    ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="b2s-user-license" value="<?php echo $isPremium ?>">


    <div class="modal fade" id="b2sXViolationModal" tabindex="-1" role="dialog" aria-labelledby="b2sXViolationModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sXViolationModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Important Notice: X has changed its policy', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <p>                     
                        <?php esc_html_e('Due to a recent policy change, X (formerly Twitter) no longer permits the integration of personal API keys for social media management through any third-party tools, including Blog2Social. We are actively working on alternative solutions for your X access and will provide further information as soon as possible.', 'blog2social'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="b2sAddUserAppModal" tabindex="-1" role="dialog" aria-labelledby="b2sAddUserAppModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sAddUserAppModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Please enter the credentials of your API app', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <div id="b2s-add-user-app-info-missing" style="display:none">
                        <?php esc_html_e('Please fill all required fields', 'blog2social'); ?>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="network-app-info" data-network-id="2" class="b2s-padding-bottom-5" style="display:none;">
                                <?php esc_html_e('The APP-ID and Secret are needed to set up and authenticate a secure connection to your X (Twitter) account. Our guide will lead you through the process of obtaining your own App Key, the ID and the secret.', 'blog2social'); ?>
                                <br>
                                <?php echo sprintf(__('To obtain your App Key and Sercet, please refer to the following <a href="%s">guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('twitter_faq'))); ?>
                            </p>
                            <p class="network-app-info" data-network-id="6" class="b2s-padding-bottom-5" style="display:none;">
                                <?php esc_html_e('The APP-ID and Secret are needed to set up and authenticate a secure connection to your Pinterest account. Our guide will lead you through the process of obtaining your own App Key, the ID and the secret.', 'blog2social'); ?>
                                <br>
                                <?php echo sprintf(__('To obtain your App-Id and Sercet, please refer to the following <a href="%s">guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('pinterest_faq'))); ?>
                            </p>


                            <form id="b2s-add-app-form" method="post">
                                <div class="form-group">
                                    <input type="text" class="form-control b2s-user-app-form" maxlength="50" id="b2s-add-user-app-name" name="b2s-add-user-app-name" placeholder="App Name">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control b2s-user-app-form" maxlength="100" id="b2s-add-user-app-key" name="b2s-add-user-app-key" placeholder="App Key">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control b2s-user-app-form" maxlength="150" id="b2s-add-user-app-secret" name="b2s-add-user-app-secret" placeholder="App Secret">
                                </div>
                                <div class="clearfix"></div>
                                <input type="hidden" id="b2s-add-user-app-network-id" value="">
                            </form> 
                            <div class="b2s-padding-top-8">
                                <button class="btn btn-primary pull-right b2s-add-app-submit-btn"><?php esc_html_e('Confirm', 'blog2social'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="b2sEditUserAppModal" tabindex="-1" role="dialog" aria-labelledby="b2sEditUserAppModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sEditUserAppModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Edit app data', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <div id="b2s-edit-user-app-info-missing" style="display:none">
                        <?php esc_html_e('Please fill all required fields', 'blog2social'); ?>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?php esc_html_e('Please fill all required fields', 'blog2social'); ?>
                            <div class="form-group">
                                <label for="b2s-edit-user-app-name"><?php esc_html_e('API App Name', 'blog2social') ?></label>

                                <input type="text" maxlength="50" class="form-control b2s-edit-user-app-name" id="b2s-edit-user-app-name" name="b2s-edit-user-app-name" placeholder="App Name" value="">
                            </div>
                            <div class="form-group">
                                <label id="b2s-edit-user-app-key-name" for="b2s-edit-user-app-key"><?php esc_html_e('API Key', 'blog2social') ?></label>
                                <input type="text" maxlength="100" class="form-control b2s-edit-user-app-key" id="b2s-edit-user-app-key" name="b2s-edit-user-app-key" placeholder="App Key">
                            </div>
                            <div class="form-group">
                                <label for="b2s-edit-user-app-secret"><?php esc_html_e('API Secret', 'blog2social') ?></label>
                                <input type="text" maxlength="150" class="form-control b2s-edit-user-app-secret" id="b2s-edit-user-app-secret" name="b2s-edit-user-app-secret" placeholder="App Secret">
                            </div>
                            <input type="hidden" id="b2s-edit-user-app-id" value="">
                            <button class="btn btn-primary pull-right b2s-edit-app-submit-btn"><?php esc_html_e('Confirm', 'blog2social'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="b2sDeleteUserAppModal" tabindex="-1" role="dialog" aria-labelledby="b2sDeleteUserAppModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sDeleteUserAppModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Delete app data', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <?php esc_html_e('WARNING: Deleting this app data will also delete all authorisations and scheduled social media posts associated with this app.', 'blog2social'); ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="b2s-delete-user-app-id" value="">
                    <button class="btn btn-sm btn-danger b2s-btn-network-delete-app-confirm-btn"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="b2sBuyAddonAppsModal" tabindex="-1" role="dialog" aria-labelledby="b2sBuyAddonAppsModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sBuyAddonAppsModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('You want to add additional apps?', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <p>
                        <?php esc_html_e('Purchase additional apps to your actual license.', 'blog2social'); ?>
                    </p>
                    <p>
                        <?php
                        esc_html_e('Click the button to open your Blog2Social account. Please, log in and complete the purchase.', 'blog2social');
                        ?>
                    </p>
                </div>
                <div class="modal-footer">
                    <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('addon_apps'); ?> " class="btn btn-sm btn-success b2s-btn-buy-addon-apps-btn"><?php esc_html_e('purchase additional apps', 'blog2social') ?></a>
                </div>
            </div>
        </div>
    </div>

