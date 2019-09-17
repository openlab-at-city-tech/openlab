<?php
defined('ABSPATH') or die('Nope, not accessing this');
?>
<style>
    .ui-state-highlight {
        background: transparent;
        border: dashed 1px #0073AA;
        width:180px;
        height: 30px !important;
    }
    <?php
    $string = "";
    global $typenow;
    $width = get_option("wcp_dynamic_width_for_" . $typenow);
    if($width == null || empty($width)) {
        $width = 292;
    }
    $width = $width - 40;
    ?>
</style>
<div id="wcp-custom-style">
    <style>
        <?php
            $string = "";
            for($i=0;$i<=15;$i++) {
            $string .= " .space > .route >";
            $new_width = $width - (13+(20*$i));
                echo "#custom-menu > {$string} .title { width: {$new_width}px !important; } ";
            }
        ?>
    </style>
</div>
<div id="media-css">

</div>
<?php
$optionName = $typenow."_parent_status";
$status = get_option($optionName);
global $typenow;
$title = ucfirst($typenow);
if($typenow == "page") {
    $title = "Pages";
} else if($typenow == "post") {
    $title = "Posts";
} else if($typenow == "attachment") {
    $title = "Files";
}
$display_status = "wcp_dynamic_display_status_" . $typenow;
$display_status = get_option($display_status);
?>
<div id="wcp-content" class="<?php echo isset($display_status) && $display_status == "hide"?"hide-folders-area":""  ?>" >
    <div id="wcp-content-resize">
        <div class="wcp-content">
            <div class="wcp-hide-show-buttons">
                <div class="toggle-buttons hide-folders <?php echo !isset($display_status) || $display_status != "hide"?"active":""  ?>"><span class="dashicons dashicons-arrow-left"></span></div>
                <div class="toggle-buttons show-folders <?php echo isset($display_status) && $display_status == "hide"?"active":""  ?>"><span class="dashicons dashicons-arrow-right"></span></div>
            </div>
            <div class='wcp-container'>
                <?php echo $form_html ?>
                <div class="header-posts">
                    <a href="javascript:;" class="all-posts"><span class="wcp-icon folder-icon-insert_drive_file"></span> <?php echo __("All ".$title, WCP_FOLDER) ?> <span class="total-count"><?php echo $total_posts ?></span></a>
                </div>
                <div class="un-categorised-items">
                    <a href="javascript:;" class="un-categorized-posts"><?php echo __("Unassigned ".$title, WCP_FOLDER) ?> <span class="total-count total-empty"><?php echo $total_empty ?></span> </a>
                </div>
                <div id="custom-menu" class="wcp-custom-menu <?php echo ($status==1)?"active":"" ?>">
                    <!--<div class="wcp-parent" id="title0"><i class="fa fa-folder-o"></i> All Folders</div>-->
                    <ul class='space first-space' id='space_0'>
                        <?php echo $terms_data; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>