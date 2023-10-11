<?php
    $tab_url = "?page=".$this->plugin_name."-question-categories";
?>


<div id="tags">
    <div class="wrap ays-quiz-tab-content ays-quiz-tab-content-active ays-quiz-list-table ays_quiz_question_tags_list_table">
        <h1 class="wp-heading-inline">
            <?php
                echo __('Question Tags',$this->plugin_name);
                echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action button-primary ays-quiz-add-new-button">' . __('Add New', $this->plugin_name) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
            ?>
        </h1>
        <div class="nav-tab-wrapper">
            <a href="<?php echo $tab_url; ?>" class="no-js nav-tab">
                <?php echo __("Categories", $this->plugin_name);?>
            </a>
            <a href="#tags" class="nav-tab nav-tab-active">
                <?php echo __("Tags", $this->plugin_name);?>
            </a>
        </div>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <?php
                            $this->question_tags_obj->views();
                        ?>
                        <form method="post">
                            <?php
                            $this->question_tags_obj->prepare_items();
                            $this->question_tags_obj->search_box('Search', $this->plugin_name);
                            $this->question_tags_obj->display();
                            ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
        <h1 class="wp-heading-inline">
            <?php
                echo __('Question Tags',$this->plugin_name);
                echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action button-primary ays-quiz-add-new-button">' . __('Add New', $this->plugin_name) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
            ?>
        </h1>
    </div>
</div>
