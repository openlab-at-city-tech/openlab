<?php
/**
 * Created by PhpStorm.
 * User: biggie18
 * Date: 6/15/18
 * Time: 3:33 PM
 */
?>

<div class="wrap ays-quiz-list-table ays_quiz_attributes_list_table">
    <div class="ays-quiz-heading-box">
        <div class="ays-quiz-wordpress-user-manual-box">
            <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
        </div>
    </div>
    <h1 class="wp-heading-inline">
        <?php
        echo __(esc_html(get_admin_page_title()),$this->plugin_name);
        echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action button-primary ays-quiz-add-new-button">' . __('Add New', $this->plugin_name) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
        ?>
    </h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php
                        $this->attributes_obj->views();
                    ?>
                    <form method="post">
                        <?php
                            $this->attributes_obj->prepare_items();
                            $this->attributes_obj->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>

    <h1 class="wp-heading-inline">
        <?php
        echo __(esc_html(get_admin_page_title()),$this->plugin_name);
        echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action button-primary ays-quiz-add-new-button">' . __('Add New', $this->plugin_name) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
        ?>
    </h1>

    <div class="ays-quiz-how-to-user-custom-fields-box" style="margin: auto;">
        <div class="ays-quiz-how-to-user-custom-fields-title">
            <h4><?php echo __( "Learn How to Use Custom Fields in Quiz Maker", $this->plugin_name ); ?></h4>
        </div>
        <div class="ays-quiz-how-to-user-custom-fields-youtube-video">
            <iframe width="560" height="315" class="ays-quiz-responsive-with-for-iframe" src="https://www.youtube.com/embed/Guq_SncdCMo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
        </div>
    </div>
</div>
