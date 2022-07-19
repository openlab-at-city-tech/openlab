<?php
$customizeArea = B2S_System::customizeArea();
?>
<!-- Sidebar|Start -Include-->
<div class="col-md-3 del-padding-left hidden-xs hidden-sm">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12 del-padding-right">
                <div class="row">
                    <div class="media">
                        <?php if (is_array($customizeArea) && isset($customizeArea['image_path']) && !empty($customizeArea['image_path'])) { ?>
                            <div class="col-md-12 del-padding-left">
                                <img class="img-responsive" src="<?php echo esc_url($customizeArea['image_path']); ?>" alt="logo">    
                            </div> 
                        <?php } else { ?>
                            <div class="col-md-2 del-padding-left">
                                <a class="" href="https://www.blog2social.com" target="_blank">
                                    <img class="img-responsive b2s-img-logo" src="<?php echo esc_url(plugins_url('/assets/images/b2s@64.png', B2S_PLUGIN_FILE)); ?>" alt="logo">
                                </a>
                            </div>
                            <div class="col-md-10 del-padding-left">
                                <div class="media-body">
                                    <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_LOGO', B2S_PLUGIN_ADMIN)) { ?> 
                                        <a href="https://www.blog2social.com" class="b2s-btn-logo" target="_blank"><?php esc_html_e("Blog2Social", "blog2social") ?></a> 
                                        <span class="b2s-sidebar-version padding-left-5"><?php echo ($b2sLastVersion !== false) ? esc_html__("Version", "blog2social") . ' ' . B2S_Util::getVersion(B2S_PLUGIN_VERSION) : ''; ?> </span>
                                    <?php } ?>
                                    <br>
                                    <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_LICENSE', B2S_PLUGIN_ADMIN)) { ?> 
                                        <div class="b2s-sidebar-licence padding-left-5"><?php esc_html_e("License", "blog2social") ?>:
                                            <a href="admin.php?page=blog2social-premium" class="b2s-sidebar-btn-licence b2s-key-name">
                                                <?php
                                                $versionType = unserialize(B2S_PLUGIN_VERSION_TYPE);
                                                if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time()) {
                                                    echo 'FREE-TRIAL (' . esc_html($versionType[B2S_PLUGIN_USER_VERSION]) . ')';
                                                } else {
                                                    echo esc_html($versionType[B2S_PLUGIN_USER_VERSION]);
                                                }
                                                ?><i class="b2s-sidebar-licence-btn-edit glyphicon glyphicon-pencil"></i>
                                            </a>
                                            <?php
                                            if(B2S_PLUGIN_USER_VERSION == 0) {
                                                echo "<br>";
                                                if((defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) < time()) || get_option('B2S_PLUGIN_DISABLE_TRAIL') == true){
                                                    echo '<a class="btn-link b2s-free-link" target="_blank" href="'.esc_url(B2S_Tools::getSupportLink('affiliate')).'">' . esc_html__('Upgrade to Premium', 'blog2social') . '</a>';
                                                } else {
                                                    echo '<a class="btn-link b2s-free-link" target="_blank" href="'.esc_url(B2S_Tools::getSupportLink('feature')).'">' . esc_html__('Start your 30-day free Premium trial', 'blog2social') . '</a>';
                                                }
                                            }
                                            ?>  
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="cleardfix"></div>
            <div class="col-md-12">
                <div class="row">
                    <hr>
                    <div class="b2s-sidebar-head">
                        <ul>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-question-sign glyphicon-success"></i> <a href="admin.php?page=blog2social-support" target="_blank" class="b2s-sidebar-menu-item"><?php esc_html_e("Help & Support", "blog2social") ?></a> 
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>