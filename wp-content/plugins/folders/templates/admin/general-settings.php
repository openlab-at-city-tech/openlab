<?php
defined('ABSPATH') or wp_die('Nope, not accessing this');
?>
<!-- do not change here, Free/Pro URL Change -->
<link rel='stylesheet' href='<?php echo WCP_FOLDER_URL ?>assets/css/settings.css' type='text/css' media='all' />
<link rel='stylesheet' href='<?php echo WCP_FOLDER_URL ?>assets/css/folder-icon.css' type='text/css' media='all' />
<link rel='stylesheet' href='<?php echo WCP_FOLDER_URL ?>assets/css/spectrum.min.css' type='text/css' media='all' />
<script src="<?php echo WCP_FOLDER_URL ?>assets/js/spectrum.min.js"></script>
<style>
    <?php if ( function_exists( 'is_rtl' ) && is_rtl() ) { ?>
    #setting-form {
        float: right;
    }
    <?php } ?>
</style>
<script>
    jQuery(document).ready(function(){
        jQuery(document).on("click",".folder-select",function(){
            if(jQuery(this).is(":checked")) {
                jQuery(this).closest("tr").find(".hide-show-option").removeClass("hide-option");
            } else {
                jQuery(this).closest("tr").find(".hide-show-option").addClass("hide-option");
            }
        });
        jQuery(document).on("click", ".accordion-header", function(){
            if(jQuery(this).hasClass("active")) {
                jQuery(this).closest(".accordion").find(".accordion-content").slideUp();
                jQuery(this).removeClass("active");
            } else {
                jQuery(this).closest(".accordion").find(".accordion-content").slideDown();
                jQuery(this).addClass("active");
            }
        });
        jQuery(".accordion-header:first").trigger("click");
        jQuery("#folder_font, #folder_size").change(function(){
            setCSSProperties();
        });
        setCSSProperties();
        jQuery('.color-field').spectrum({
            chooseText: "Submit",
            preferredFormat: "hex",
            showInput: true,
            cancelText: "Cancel",
            move: function (color) {
                jQuery(this).val(color.toHexString());
                setCSSProperties();
            },
            change: function (color) {
                jQuery(this).val(color.toHexString());
                setCSSProperties();
            }
        });
    });

    function setCSSProperties() {
        if(jQuery("#new_folder_color").val() != "") {
            jQuery("#add-new-folder").css("border-color", jQuery("#new_folder_color").val());
            jQuery("#add-new-folder").css("background-color", jQuery("#new_folder_color").val());
        }
        if(jQuery("#bulk_organize_button_color").val() != "") {
            jQuery(".organize-button").css("border-color", jQuery("#bulk_organize_button_color").val());
            jQuery(".organize-button").css("background-color", jQuery("#bulk_organize_button_color").val());
            jQuery(".organize-button").css("color", "#ffffff");
        }
        if(jQuery("#dropdown_color").val() != "") {
            jQuery(".media-select").css("border-color", jQuery("#dropdown_color").val());
            jQuery(".media-select").css("color", jQuery("#dropdown_color").val());
        }
        if(jQuery("#folder_bg_color").val() != "") {
            jQuery(".all-posts.active-item").css("border-color", jQuery("#folder_bg_color").val());
            jQuery(".all-posts.active-item").css("background-color", jQuery("#folder_bg_color").val());
            jQuery(".all-posts.active-item").css("color", "#ffffff");
        }
        jQuery("#custom-css").html("");
        if(jQuery("#folder_font").val() != "") {
            font_val = jQuery("#folder_font").val();
            jQuery('head').append('<link href="https://fonts.googleapis.com/css?family=' + font_val + ':400,600,700" rel="stylesheet" type="text/css" class="chaty-google-font">');
            jQuery('.preview-box').css('font-family', font_val);
        } else {
            jQuery('.preview-box').css('style', "");
        }
        if(jQuery("#folder_size").val() != "") {
            jQuery(".folder-list li a span, .header-posts a, .un-categorised-items a").css("font-size", jQuery("#folder_size").val()+"px");
        } else {
            jQuery(".folder-list li a span, .header-posts a, .un-categorised-items a").css("font-size", "14px");
        }
    }
</script>
<div id="custom-css">

</div>
<div class="wrap">
    <h1><?php esc_attr_e( 'Folders Settings', WCP_FOLDER ); ?></h1>
    <form action="options.php" method="post" id="setting-form">
        <?php
        settings_fields('folders_settings');
        settings_fields('default_folders');
        settings_fields('customize_folders');
        $options = get_option('folders_settings');
        $default_folders = get_option('default_folders');
        $customize_folders = get_option('customize_folders');
        $default_folders = (empty($default_folders) || !is_array($default_folders))?array():$default_folders;
        do_settings_sections( __FILE__ );
        ?>
        <div class="accordion">
            <div class="accordion-header">Folders Settings <span class="dashicons dashicons-arrow-down-alt2"></span></div>
            <div class="accordion-content" style="display: block">
                <div class="accordion-left">
                    <table class="form-table">
                        <?php
                        $post_types = get_post_types( array( 'public' => true ), 'objects' );
                        $post_array = array("page", "post", "attachment");
                        foreach ( $post_types as $post_type ) : ?>
                            <?php
                            if ( ! $post_type->show_ui) continue;
                            $is_checked = !in_array( $post_type->name, $options )?"hide-option":"";
                            $selected_id = (isset($default_folders[$post_type->name]))?$default_folders[$post_type->name]:"all";
                            if(in_array($post_type->name, $post_array)){
                                ?>
                                <tr>
                                    <th width="220px">
                                        <label for="folders_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Use folders with: ', WCP_FOLDER )." ".esc_html_e($post_type->label); ?></label>
                                    </th>
                                    <td>
                                        <input type="checkbox" class="folder-select" id="folders_<?php echo esc_attr($post_type->name); ?>" name="folders_settings[]" value="<?php echo esc_attr($post_type->name); ?>"<?php if ( in_array( $post_type->name, $options ) ) echo ' checked="checked"'; ?>/>
                                    </td>
                                    <th class="default-folder">
                                        <label class="hide-show-option <?php echo esc_attr($is_checked) ?>" for="folders_for_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Default folder: ', WCP_FOLDER ) ?></label>
                                    </th>
                                    <td>
                                        <select class="hide-show-option <?php echo esc_attr($is_checked) ?>" id="folders_for_<?php echo esc_attr($post_type->name); ?>" name="default_folders[<?php echo esc_attr($post_type->name); ?>]" ?>">
                                            <option value="">All <?php echo esc_attr($post_type->label) ?> Folder</option>
                                            <option value="-1" <?php echo ($selected_id == -1)?"selected":"" ?>>Unassigned <?php echo esc_attr($post_type->label) ?></option>
                                            <?php
                                            if(isset($terms_data[$post_type->name]) && !empty($terms_data[$post_type->name])) {
                                                foreach ($terms_data[$post_type->name] as $term) {
                                                    $selected = ($selected_id == $term->slug)?"selected":"";
                                                    echo "<option ".esc_attr($selected)." value='".esc_attr($term->slug)."'>".esc_attr($term->name)."</option>";
                                                }
                                            } ?>
                                        </select>
                                    </td>
                                </tr>
                                <?php
                            } else { ?>
                                <tr>
                                    <th>
                                        <label for="folders_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Use folders with: ', WCP_FOLDER )." ".esc_html_e($post_type->label); ?></label>
                                    </th>
                                    <td>
                                        <input type="checkbox" class="folder-select" id="folders_<?php echo esc_attr($post_type->name); ?>" name="folders_settings[]" value="<?php echo esc_attr($post_type->name); ?>"<?php if ( in_array( $post_type->name, $options ) ) echo ' checked="checked"'; ?>/>
                                    </td>
                                    <th class="default-folder">
                                        <label class="hide-show-option <?php echo esc_attr($is_checked) ?>" for="folders_for_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Default folder: ', WCP_FOLDER ) ?></label>
                                    </th>
                                    <td>
                                        <select class="hide-show-option <?php echo esc_attr($is_checked) ?>" id="folders_for_<?php echo esc_attr($post_type->name); ?>" name="default_folders[<?php echo esc_attr($post_type->name); ?>]" ?>">
                                            <option value="">All <?php echo esc_attr($post_type->label) ?> Folder</option>
                                            <option value="-1" <?php echo ($selected_id == -1)?"selected":"" ?>>Unassigned <?php echo esc_attr($post_type->label) ?></option>
                                            <?php
                                            if(isset($terms_data[$post_type->name]) && !empty($terms_data[$post_type->name])) {
                                                foreach ($terms_data[$post_type->name] as $term) {
                                                    $selected = ($selected_id == $term->slug)?"selected":"";
                                                    echo "<option ".esc_attr($selected)." value='".esc_attr($term->slug)."'>".esc_attr($term->name)."</option>";
                                                }
                                            } ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php } endforeach; ?>
                        <tr>
                            <th>
                                <label for="folders_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Show Folders in Menu:', WCP_FOLDER ); ?></label>
                            </th>
                            <td>
                                <?php $val = get_option("folders_show_in_menu"); ?>
                                <input type="hidden" name="folders_show_in_menu" value="off" />
                                <input type="checkbox" name="folders_show_in_menu" value="on" <?php echo ($val == "on")?"checked='checked'":"" ?>/>
                            </td>
                        </tr>
                        <!-- Do not make changes here, Only for Free -->

                        <tr>
                            <td colspan="2" style="padding: 10px 0">
                                <?php
                                $tlfs = get_option("folder_old_plugin_folder_status");
                                if($tlfs == false || $tlfs < 10) {
                                    $tlfs = 10;
                                }
                                $total = WCP_Folders::get_ttl_fldrs();
                                if($total > $tlfs) {
                                    $tlfs = $total;
                                }
                                ?>
                                <span class="upgrade-message">You have used <span class='pink'><?php echo esc_attr($total) ?></span>/<?php echo esc_attr($tlfs) ?> Folders. <a class="pink" href="<?php echo esc_url(admin_url("admin.php?page=wcp_folders_upgrade")) ?>"><?php esc_html_e("Upgrade", WCP_FOLDER) ?></a></span>
                            </td>
                        </tr>

                    </table>
                    <input type="hidden" name="folders_settings1[premio_folder_option]" value="yes" />
                </div>
                <div class="accordion-right">
                    <div class="premio-help">
                        <a href="https://premio.io/help/folders/?utm_source=pluginspage" target="_blank">
                            <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/premio-help.png") ?>" alt="Premio Help" class="Premio Help" />
                        </a>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="accordion">
            <div class="accordion-header">Customize Folders <span class="dashicons dashicons-arrow-down-alt2"></span></div>
            <div class="accordion-content">
                <div class="accordion-left">
                    <table class="form-table">
                        <?php
                        $color = !isset($customize_folders['new_folder_color'])||empty($customize_folders['new_folder_color'])?"#F51366":$customize_folders['new_folder_color'];
                        ?>
                        <tr>
                            <th width="220px">
                                <label for="new_folder_color" >New Folder button color</label>
                            </th>
                            <td width="32px">
                                <input type="text" class="color-field" name="customize_folders[new_folder_color]" id="new_folder_color" value="<?php echo esc_attr($color) ?>" />
                            </td>
                            <td rowspan="4" >

                            </td>
                        </tr>
                        <?php
                        $color = !isset($customize_folders['bulk_organize_button_color'])||empty($customize_folders['bulk_organize_button_color'])?"#F51366":$customize_folders['bulk_organize_button_color'];
                        ?>
                        <tr>
                            <th>
                                <label for="bulk_organize_button_color" >Bulk Organize button color</label>
                            </th>
                            <td>
                                <input type="text" class="color-field" name="customize_folders[bulk_organize_button_color]" id="bulk_organize_button_color" value="<?php echo esc_attr($color) ?>" />
                            </td>
                        </tr>
                        <?php
                        $color = !isset($customize_folders['dropdown_color'])||empty($customize_folders['dropdown_color'])?"#F51366":$customize_folders['dropdown_color'];
                        ?>
                        <tr>
                            <th>
                                <label for="dropdown_color" >Dropdown color</label>
                            </th>
                            <td>
                                <input type="text" class="color-field" name="customize_folders[dropdown_color]" id="dropdown_color" value="<?php echo esc_attr($color) ?>" />
                            </td>
                        </tr>
                        <?php
                        $color = !isset($customize_folders['folder_bg_color'])||empty($customize_folders['folder_bg_color'])?"#008ec2":$customize_folders['folder_bg_color'];
                        ?>
                        <tr>
                            <th>
                                <label for="folder_bg_color" >Folders background color</label>
                            </th>
                            <td>
                                <input type="text" class="color-field" name="customize_folders[folder_bg_color]" id="folder_bg_color" value="<?php echo esc_attr($color) ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="folder_font" >Folders font</label>
                            </th>
                            <td colspan="2">
                                <?php
                                $font = !isset($customize_folders['folder_font'])||empty($customize_folders['folder_font'])?"":$customize_folders['folder_font'];
                                $index = 0;
                                ?>
                                <select name="customize_folders[folder_font]" id="folder_font" >
                                    <?php $group = '';
                                    foreach ($fonts as $key => $value):
                                        $title = $key;
                                        if($index == 0) {
                                            $key = "";
                                        }
                                        $index++;
                                        if ($value != $group) {
                                            echo '<optgroup label="' . $value . '">';
                                            $group = $value;
                                        }
                                        ?>
                                        <option value="<?php echo $key; ?>" <?php selected($font, $key); ?>><?php echo $title; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="folder_size" >Folders size</label>
                            </th>
                            <td colspan="2">
                                <?php
                                $sizes = array(
                                    "12" => "Small",
                                    "16" => "Medium",
                                    "20" => "Large"
                                );
                                $size = !isset($customize_folders['folder_size'])||empty($customize_folders['folder_size'])?"16":$customize_folders['folder_size'];
                                ?>
                                <select name="customize_folders[folder_size]" id="folder_size" >
                                    <?php
                                    foreach ($sizes as $key=>$value) {
                                        $selected = ($key == $size)?"selected":"";
                                        echo "<option ".$selected." value='".$key."'>".$value."</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="accordion-right">
                    <div class="preview-text">
                        Preview
                        <div class="preview-text-info">See the full functionality on your media library, posts, pages, and custom posts</div>
                    </div>
                    <div class="preview-inner-box">
                        <div class="preview-box">
                            <div class="wcp-custom-form">
                                <div class="form-title">
                                    Folders
                                    <a href="javascript:;" class="add-new-folder" id="add-new-folder">
                                        <span class="folder-icon-create_new_folder"></span>
                                        <span>New Folder</span>
                                    </a>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-options">
                                    <ul>
                                        <li>
                                            <a href="javascript:;" id="inline-update"><span class="icon folder-icon-border_color"><span class="path1"></span><span class="path2"></span></span> <span class="text">Rename</span> </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" id="inline-remove"><span class="icon folder-icon-delete"></span> <span class="text">Delete</span> </a>
                                        </li>
                                        <li class="last">
                                            <a href="javascript:;" id="expand-collapse-list" class="folder-tooltip" data-tooltip="Expand"><span class="icon folder-icon-expand_more"></span></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="header-posts">
                                <a href="javascript:;" class="all-posts active-item"><span class="wcp-icon folder-icon-insert_drive_file"></span> All Files <span class="total-count">0</span></a>
                            </div>
                            <div class="un-categorised-items  ui-droppable">
                                <a href="javascript:;" class="un-categorized-posts">Unassigned Files <span class="total-count total-empty">0</span> </a>
                            </div>

                            <ul class="folder-list">
                                <li><a href="javascript:;"><i class="wcp-icon folder-icon-folder"></i> <span>Folder 1</span></a></li>
                                <li><a href="javascript:;"><i class="wcp-icon folder-icon-folder"></i> <span>Folder 2</span></a></li>
                                <li><a href="javascript:;"><i class="wcp-icon folder-icon-folder"></i> <span>Folder 3</span></a></li>
                            </ul>
                        </div>
                        <div class="media-buttons">
                            <select class="media-select">
                                <option>All Files</option>
                                <option>Folder 1</option>
                                <option>Folder 2</option>
                                <option>Folder 3</option>
                            </select>
                            <button type="button" class="button organize-button">Bulk Organize</button>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php submit_button(); ?>
    </form>
</div>