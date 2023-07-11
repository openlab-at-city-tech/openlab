<div id="b2s-edit-event-modal-<?php echo esc_attr($item->getB2SId()); ?>" class="modal fade" role="dialog" aria-labelledby="b2s-edit-event-modal-<?php echo esc_attr($item->getB2SId()); ?>" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close b2s-modal-close-edit-post close release_locks" data-modal-name="#b2s-edit-event-modal-<?php echo esc_attr($item->getB2SId()); ?>">&times;</button>
                <h4 class="modal-title">
                    <?php echo esc_html__("Edit Post", "blog2social"); ?>
                    <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                        <span class="label label-success"><a href="#" class="b2s-btn-label-premium btn-label-premium-xs b2s-info-btn" data-modal-target="b2sInfoMetaBoxModalAutoPost"><?php echo esc_html__("SMART", "blog2social"); ?></a></span>
                    <?php } ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <form>
                            <input type="hidden" class="b2s-input-hidden" name="action" value="b2s_edit_save_post" />
                            <input type="hidden" class="b2s-input-hidden" id="post_id" name="post_id" value="<?php echo esc_attr($item->getPostId()); ?>">
                            <input type="hidden" class="b2s-input-hidden" id="original_blog_user_id" name="original_blog_user_id" value="<?php echo esc_attr($item->getBlogUserId()); ?>">
                            <input type="hidden" class="b2s-input-hidden" name="b2s_id" value="<?php echo esc_attr($item->getB2SId()); ?>">
                            <input type="hidden" class="b2s-input-hidden" name="sched_details_id" value="<?php echo esc_attr($item->getSchedDetailsId()); ?>">
                            <input type="hidden" class="b2s-input-hidden" id="save_method" name="save_method" value="apply-this" />
                            <input type="hidden" class="b2sChangeOgMeta b2s-input-hidden" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>" name="change_og_meta" value="0">
                            <input type="hidden" class="b2sChangeCardMeta b2s-input-hidden"  data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>" name="change_card_meta" value="0">
                            <input type="hidden" class="b2s-input-hidden" id="b2sUserTimeZone" name="user_timezone" value="0">
                            <input type="hidden" class="b2s-input-hidden" id="b2sRelayPrimaryPostId" name="relay_primary_post_id" value="<?php echo esc_attr($item->getRelayPrimaryPostId()); ?>">
                            <input type="hidden" class="b2s-input-hidden" id="b2sRelayPrimarySchedDate" name="relay_primary_sched_date" value="<?php echo esc_attr($item->getRelayPrimarySchedDate()); ?>">
                            <input type="hidden" class="b2s-input-hidden" id="b2sPostForRelay" name="post_for_relay" value="<?php echo esc_attr($item->getPostForRelay()); ?>">
                            <input type="hidden" class="b2s-input-hidden" id="b2sPostForApprove" name="post_for_approve" value="<?php echo esc_attr($item->getPostForApprove()); ?>">
                            <input type="hidden" class="b2s-input-hidden" id="b2sCurrentPostFormat" value="<?php echo esc_attr($item->getPostFormat()); ?>">
                            <input type="hidden" class="b2s-input-hidden" id="b2sNetworkAuthId" name="network_auth_id" value="<?php echo esc_attr($item->getNetworkAuthId()); ?>">

                            <?php if ($lock_user_id && $lock_user_id != get_current_user_id()) { ?>
                                <div class="alert alert-danger">
                                    <?php echo str_replace("%1", esc_html($lock_user->user_login), esc_html__('This post is blocked by %1', 'blog2social')); ?>.
                                </div>
                            <?php } ?>

                            <?php echo wp_kses($item->getEditHtml(), array(
                                'div' => array(
                                    'class' => array(),
                                    'data-network-auth-id' => array(),
                                    'data-network-id' => array(),
                                    'style' => array(),
                                    'data-network-count' => array(),
                                ),
                                'img' => array(
                                    'class' => array(),
                                    'alt' => array(),
                                    'src' => array(),
                                    'style' => array(),
                                    'data-network-auth-id' => array(),
                                    'data-network-count' => array(),
                                    'data-network-id' => array(),
                                    'data-network-image-change' => array(),
                                    'data-image-count' => array(),
                                ),
                                'h4' => array(
                                    'class' => array(),
                                    'data-network-auth-id' => array(),
                                ),
                                'p' => array(
                                    'class' => array(),
                                ),
                                'i' => array(
                                    'class' => array(),
                                ),
                                'span' => array(
                                    'class' => array(),
                                    'data-network-auth-id' => array(),
                                    'data-network-id' => array(),
                                    'data-network-type' => array(),
                                    'data-network-auth-id' => array(),
                                    'data-post-format-type' => array(),
                                    'data-network-count' => array(),
                                    'aria-hidden' => array(),
                                ),
                                'button' => array(
                                    'type' => array(),
                                    'class' => array(),
                                    'style' => array(),
                                    'data-post-wp-type' => array(),
                                    'data-post-format-type' => array(),
                                    'data-network-auth-id' => array(),
                                    'data-network-type' => array(),
                                    'data-network-id' => array(),
                                    'data-network-count' => array(),
                                    'data-meta-type' => array(),
                                    'data-post-id' => array(),
                                    'data-image-url' => array(),
                                    'data-meta-origin' => array(),
                                    'data-image-count' => array(),
                                ),
                                'input' => array(
                                    'type' => array(),
                                    'name' => array(),
                                    'value' => array(),
                                    'class' => array(),
                                    'data-network-auth-id' => array(),
                                    'data-network-id' => array(),
                                    'data-network-type' => array(),
                                    'data-meta' => array(),
                                    'data-meta-type' => array(),
                                    'dir' => array(),
                                    'onkeyup' => array(),
                                    'placeholder' => array(),
                                    'data-network-count' => array(),
                                    'data-image-count' => array(),
                                ),
                                'textarea' => array(
                                    'class' => array(),
                                    'data-network-count' => array(),
                                    'data-network-id' => array(),
                                    'data-network-text-limit' => array(),
                                    'data-network-auth-id' => array(),
                                    'placeholder' => array(),
                                    'name' => array(),
                                    'onkeyup' => array(),
                                ),
                                'a' => array(
                                    'class' => array(),
                                    'style' => array(),
                                    'data-image-count' => array(),
                                    'data-network-count' => array(),
                                    'data-network-auth-id' => array(),
                                ),
                                'select' => array(
                                    'class' => array(),
                                    'name' => array()
                                ),
                                'option' => array(
                                    'value' => array(),
                                    'selected' => array()
                                ),
                            )); ?>
                            <div class="pull-right">
                                <a href="#" class="btn btn-primary btn-xs b2s-get-settings-sched-time-user"><?php esc_html_e('Load My Times Settings', 'blog2social') ?></a>
                                <a href="#" class="btn btn-primary btn-xs b2s-get-settings-sched-time-default"><?php esc_html_e('Load Best Times', 'blog2social') ?></a>
                            </div>
                            <div class="clearfix"></div>
                            <div class="panel panel-group">
                                <div class="b2s-post-item-details-release-area-details">
                                    <!-- deprecated Network Xing,G+ 8,10 -->
                                    <?php if ($item->getNetworkId() == 8 || $item->getNetworkId() == 10) { ?>
                                        <div class="network-tos-deprecated-warning alert alert-danger" style="display: none;" data-network-id="<?php echo esc_attr($item->getNetworkId()); ?>" data-network-count="0"  data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>">
                                            <?php
                                            if ($item->getNetworkId() == 8) {
                                                esc_html_e("Please note: Your account is connected via an old XING API that is no longer supported by XING after March 31. Please connect your XING profile, as well as your XING company pages (Employer branding profiles) and business pages with the new XING interface in the Blog2Social network settings. To do this, go to the Blog2Social Networks section and connect your XING accounts with the new XING.", "blog2social");
                                                ?>  <a href="<?php echo esc_url(B2S_Tools::getSupportLink('network_tos_blog_032019')); ?>" target="_blank"><?php echo esc_html__('Learn more', 'blog2social') ?></a>
                                            <?php
                                            } else {
                                                esc_html_e("Please note: Google will shut down Google+ for all private accounts (profiles, pages, groups) on 2nd April 2019. You can find further information and the next steps, including how to download your photos and other content here:", "blog2social");
                                                ?>  <a href="https://support.google.com/plus/answer/9195133" target="_blank">https://support.google.com/plus/answer/9195133</a> 
                                            <?php } ?>
                                        </div>
                                        <?php } ?>

                                        <ul class="list-group b2s-post-item-details-release-area-details-ul" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>">
                                            <li class="list-group-item">
                                                <div class="form-group b2s-post-item-details-release-area-details-row" data-network-count="1"  data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>">
                                                        <?php if ((int) $item->getRelayPrimaryPostId() == 0) { ?>
                                                        <div class="clearfix"></div>
                                                        <label class="col-xs-3 del-padding-left b2s-post-item-details-release-area-label-date" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>" data-network-count="1"><?php echo esc_html__('Date', 'blog2social'); ?></label>
                                                        <label class="col-xs-3 del-padding-left b2s-post-item-details-release-area-label-time" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>" data-network-count="1"><?php echo esc_html__('Time', 'blog2social'); ?></label>
                                                        <div class="clearfix"></div>
                                                        <div class="col-xs-3 del-padding-left b2s-post-item-details-release-area-label-date" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>" data-network-count="1"><input type="text" placeholder="<?php echo esc_attr__('Date', 'blog2social'); ?>" name="b2s[<?php echo esc_attr($item->getNetworkAuthId()); ?>][date][0]" data-network-id="<?php echo esc_attr($item->getNetworkId()); ?>" data-network-type="<?php echo esc_attr($item->getNetworkType()); ?>" data-network-count="0" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>"  class="b2s-post-item-details-release-input-date form-control" value="<?php echo esc_attr((substr(B2S_LANGUAGE, 0, 2) == 'de') ? date('d.m.Y', $item->getSchedDate()) : date('Y-m-d', $item->getSchedDate())); ?>" style="min-width: 93px;"></div>
                                                        <div class="col-xs-3 del-padding-left b2s-post-item-details-release-area-label-time" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>" data-network-count="1"><input type="text" placeholder="<?php echo esc_attr__('Time', 'blog2social'); ?>" name="b2s[<?php echo esc_attr($item->getNetworkAuthId()); ?>][time][0]" data-network-id="<?php echo esc_attr($item->getNetworkId()); ?>" data-network-type="<?php echo esc_attr($item->getNetworkType()); ?>" data-network-count="0" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>"  class="b2s-post-item-details-release-input-time form-control" value="<?php echo esc_attr(date('H:i', $item->getSchedDate())); ?>"></div>
                                                        <div class="col-xs-5 del-padding-left b2s-post-item-details-release-area-label-day" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>" data-network-count="1">
                                                            <?php
                                                            //is relay post ?
                                                        } else {
                                                            ?>
                                                            <div class="clearfix"></div>
                                                            <label class="col-xs-3 del-padding-left b2s-post-item-details-relay-area-label-delay" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>" data-network-count="1"><?php echo esc_html__('Delay', 'blog2social'); ?></label>
                                                            <div class="clearfix"></div>
                                                            <div class="col-xs-3 del-padding-left b2s-post-item-details-relay-area-div-delay" data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>" data-network-count="1">
                                                                <select name="b2s[<?php echo esc_attr($item->getNetworkAuthId()); ?>][post_relay_delay][0]" class="form-control b2s-select b2s-post-item-details-relay-input-delay" data-network-count="0"  data-network-auth-id="<?php echo esc_attr($item->getNetworkAuthId()); ?>">
                                                                    <option value="15" <?php echo (($item->getRelayDelayMin() == 15) ? 'selected' : ''); ?> >15 <?php echo esc_html__('min', 'blog2social') ?></option>
                                                                    <option value="30" <?php echo (($item->getRelayDelayMin() == 30) ? 'selected' : ''); ?>>30 <?php echo esc_html__('min', 'blog2social') ?></option>
                                                                    <option value="45" <?php echo (($item->getRelayDelayMin() == 45) ? 'selected' : ''); ?>>45 <?php echo esc_html__('min', 'blog2social') ?></option>
                                                                    <option value="60" <?php echo (($item->getRelayDelayMin() == 60) ? 'selected' : ''); ?>>60 <?php echo esc_html__('min', 'blog2social') ?></option>
                                                                </select>
                                                            </div>
                                                            <div class="col-xs-9">
                                                                <strong><?php echo esc_html__('The orginal tweet is scheduled on:', 'blog2social') ?> <?php echo esc_html(B2S_Util::getCustomDateFormat($item->getRelayPrimarySchedDate(), substr(B2S_LANGUAGE, 0, 2))) ?></strong>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="clearfix"></div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                    <?php if (!$lock_user_id || $lock_user_id == get_current_user_id()) { ?>
                                    <div class="col-xs-12" style="margin-top: 20px;">
                                        <div class="pull-left" style="line-height: 33px">
                                            <span class="b2s-edit-post-delete btn btn-danger" data-post-for-relay="<?php echo esc_attr($item->getPostForRelay()); ?>" data-post-for-approve="<?php echo esc_attr($item->getPostForApprove()); ?>"  data-post-id="<?php echo esc_attr($item->getPostId()); ?>" data-b2s-id="<?php echo esc_attr($item->getB2SId()); ?>">
                                                <span class="glyphicon glyphicon glyphicon-trash "></span> <?php echo esc_html(__("Delete", "blog2social")); ?>
                                            </span>
                                        </div>
                                        <div class="pull-right">
                                            <input class="btn btn-success pull-right b2s-edit-post-save-this" type="submit" value="<?php echo esc_attr__('Change details', 'blog2social'); ?>" data-post-id="<?php echo esc_attr($item->getPostId()); ?>" data-b2s-id="<?php echo esc_attr($item->getB2SId()); ?>">
                                        </div>
                                    </div>
                                <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
