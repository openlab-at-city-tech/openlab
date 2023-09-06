<div class="wrap">
    <div class="ays-quiz-heading-box">
        <div class="ays-quiz-wordpress-user-manual-box">
            <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
        </div>
    </div>
    <h1 class="wp-heading-inline">
        <?php echo __(esc_html(get_admin_page_title()),$this->plugin_name); ?>
        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('History of the users’ payments for passing the quiz.',$this->plugin_name)?>">
            <i class="ays_fa ays_fa_info_circle"></i>
        </a>
    </h1>
    <div id="tab1" class="ays-quiz-tab-content ays-quiz-tab-content-active">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <?php
                                $this->orders_obj->prepare_items();
                                $this->orders_obj->display();
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
