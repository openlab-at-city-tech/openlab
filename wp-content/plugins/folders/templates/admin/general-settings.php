<?php
if ( ! defined( 'ABSPATH' ) ) exit;
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
        <input type="hidden" name="folder_nonce" value="<?php echo wp_create_nonce("folder_settings") ?>">
        <div class="accordion">
            <div class="accordion-header">Folders Settings <span class="dashicons dashicons-arrow-down-alt2"></span></div>
            <div class="accordion-content no-bp" style="display: block">
                <div class="accordion-left">
                    <table class="form-table">
                        <tboby>
                            <?php
                            $post_types = get_post_types( array( ), 'objects' );
                            $post_array = array("page", "post", "attachment");
                            foreach ( $post_types as $post_type ) : ?>
                                <?php
                                if ( ! $post_type->show_ui) continue;
                                $is_checked = !in_array( $post_type->name, $options )?"hide-option":"";
                                $selected_id = (isset($default_folders[$post_type->name]))?$default_folders[$post_type->name]:"all";
                                if(in_array($post_type->name, $post_array)){
                                    ?>
                                    <tr>
                                        <td class="no-padding">
                                            <label for="folders_<?php echo esc_attr($post_type->name); ?>" class="custom-checkbox">
                                                <input type="checkbox" class="folder-select sr-only" id="folders_<?php echo esc_attr($post_type->name); ?>" name="folders_settings[]" value="<?php echo esc_attr($post_type->name); ?>"<?php if ( in_array( $post_type->name, $options ) ) echo ' checked="checked"'; ?>/>
                                                <span></span>
                                            </label>
                                        </td>
                                        <td width="220px">
                                            <label for="folders_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Use folders with: ', WCP_FOLDER )." ".esc_html_e($post_type->label); ?></label>
                                        </td>
                                        <td class="default-folder">
                                            <label class="hide-show-option <?php echo esc_attr($is_checked) ?>" for="folders_for_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Default folder: ', WCP_FOLDER ) ?></label>
                                        </td>
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
                                        <td class="no-padding">
                                            <label for="folders_<?php echo esc_attr($post_type->name); ?>" class="custom-checkbox">
                                                <input type="checkbox" class="sr-only folder-select" id="folders_<?php echo esc_attr($post_type->name); ?>" name="folders_settings[]" value="<?php echo esc_attr($post_type->name); ?>"<?php if ( in_array( $post_type->name, $options ) ) echo ' checked="checked"'; ?>/>
                                                <span></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="folders_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Use folders with: ', WCP_FOLDER )." ".esc_html_e($post_type->label); ?></label>
                                        </td>
                                        <td class="default-folder">
                                            <label class="hide-show-option <?php echo esc_attr($is_checked) ?>" for="folders_for_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Default folder: ', WCP_FOLDER ) ?></label>
                                        </td>
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
                                <td width="20" class="no-padding">
                                    <?php $val = get_option("folders_show_in_menu"); ?>
                                    <input type="hidden" name="folders_show_in_menu" value="off" />
                                    <label  for="folders_show_in_menu" class="custom-checkbox">
                                        <input class="sr-only" type="checkbox" id="folders_show_in_menu" name="folders_show_in_menu" value="on" <?php echo ($val == "on")?"checked='checked'":"" ?>/>
                                        <span></span>
                                    </label>
                                </td>
                                <td colspan="3">
                                    <label for="folders_show_in_menu" ><?php esc_html_e( 'Show Folders in Menu:', WCP_FOLDER ); ?></label>
                                </td>
                            </tr>
                            <!-- Do not make changes here, Only for Free -->
                        </tboby>
                    </table>
                    <input type="hidden" name="folders_settings1[premio_folder_option]" value="yes" />
                </div>
                <div class="accordion-right">
                    <div class="premio-help">
                        <a href="https://premio.io/help/folders/?utm_source=pluginspage" target="_blank">
                            <div class="premio-help-btn">
                                <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/premio-help.png") ?>" alt="Premio Help" class="Premio Help" />
                                <div class="need-help">Need Help</div>
                                <div class="visit-our">Visit our</div>
                                <div class="knowledge-base">knowledge base</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="separator"></div>
                <table class="form-table">
                    <tfoot>
                    <tr>
                        <td class="no-padding" width="20px">
                            <span class="dashicons dashicons-editor-help"></span>
                        </td>
                        <td width="220px">
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
                            You have used <b><?php echo esc_attr($total) ?></b>/<?php echo esc_attr($tlfs) ?> Folders.
                        </td>
                        <td class="no-padding" colspan="2">
                            <a class="upgrade-btn" href="<?php echo esc_url(admin_url("admin.php?page=wcp_folders_upgrade")) ?>"><?php esc_html_e("Upgrade", WCP_FOLDER) ?></a>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="accordion">
            <div class="accordion-header">Customize Folders <span class="dashicons dashicons-arrow-down-alt2"></span></div>
            <div class="accordion-content">
                <div class="accordion-left">
                    <table class="form-table">
                    <?php
                    $color = !isset($customize_folders['new_folder_color'])||empty($customize_folders['new_folder_color'])?"#FA166B":$customize_folders['new_folder_color'];
                    ?>
                    <tr>
                        <td width="220px" class="no-padding">
                            <label for="new_folder_color" ><b>"New Folder"</b> button color</label>
                        </td>
                        <td width="32px">
                            <input type="text" class="color-field" name="customize_folders[new_folder_color]" id="new_folder_color" value="<?php echo esc_attr($color) ?>" />
                        </td>
                        <td rowspan="4" >

                        </td>
                    </tr>
                    <?php
                    $color = !isset($customize_folders['bulk_organize_button_color'])||empty($customize_folders['bulk_organize_button_color'])?"#FA166B":$customize_folders['bulk_organize_button_color'];
                    ?>
                    <tr>
                        <td class="no-padding">
                            <label for="bulk_organize_button_color" ><b>"Bulk Organize"</b> button color</label>
                        </td>
                        <td>
                            <input type="text" class="color-field" name="customize_folders[bulk_organize_button_color]" id="bulk_organize_button_color" value="<?php echo esc_attr($color) ?>" />
                        </td>
                    </tr>
                    <?php
                    $color = !isset($customize_folders['dropdown_color'])||empty($customize_folders['dropdown_color'])?"#484848":$customize_folders['dropdown_color'];
                    ?>
                    <tr>
                        <td class="no-padding">
                            <label for="dropdown_color" >Dropdown color</label>
                        </td>
                        <td>
                            <input type="text" class="color-field" name="customize_folders[dropdown_color]" id="dropdown_color" value="<?php echo esc_attr($color) ?>" />
                        </td>
                    </tr>
                    <?php
                    $color = !isset($customize_folders['folder_bg_color'])||empty($customize_folders['folder_bg_color'])?"#FA166B":$customize_folders['folder_bg_color'];
                    ?>
                    <tr>
                        <td class="no-padding">
                            <label for="folder_bg_color" >Folders background color</label>
                        </td>
                        <td>
                            <input type="text" class="color-field" name="customize_folders[folder_bg_color]" id="folder_bg_color" value="<?php echo esc_attr($color) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="no-padding">
                            <label for="folder_font" >Folders font</label>
                        </td>
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
                        <td class="no-padding">
                            <label for="folder_size" >Folders size</label>
                        </td>
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
                        <?php
                        $show_in_page = !isset($customize_folders['show_in_page'])||empty($customize_folders['show_in_page'])?"show":$customize_folders['show_in_page'];
                        if(empty($show_in_page)) {
                            $show_in_page = "show";
                        }
                        ?>
                        <tr>
                            <td colspan="3" class="no-padding">
                                <input type="hidden" name="customize_folders[show_in_page]" value="hide">
                                <div class="custom-checkbox">
                                    <input id="show_folders" class="sr-only" <?php checked($show_in_page, "show") ?> type="checkbox" name="customize_folders[show_in_page]" value="show">
                                    <span></span>
                                </div>
                                <label for="show_folders">Show Folders in upper position</label>
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
                                        <span class="create_new_folder"><i class="pfolder-add-folder"></i></span>
                                        <span>New Folder</span>
                                    </a>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-options">
                                    <ul>
                                        <li>
                                            <div class="custom-checkbox">
                                                <input type="checkbox" class="sr-only" >
                                                <span></span>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="javascript:;" id="inline-update"><span class="icon pfolder-edit-folder"><span class="path2"></span></span> <span class="text">Rename</span> </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" id="inline-remove"><span class="icon pfolder-remove"></span> <span class="text">Delete</span> </a>
                                        </li>
                                        <li class="last">
                                            <a href="javascript:;" id="expand-collapse-list" data-tooltip="Expand"><span class="icon pfolder-arrow-down"></span></a>
                                        </li>
                                        <li class="last">
                                            <a href="javascript:;" ><span class="icon pfolder-arrow-sort"></span></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="shadow-box">
                                <div class="header-posts">
                                    <a href="javascript:;" class="all-posts active-item">All Files <span class="total-count">215</span></a>
                                </div>
                                <div class="un-categorised-items  ui-droppable">
                                    <a href="javascript:;" class="un-categorized-posts">Unassigned Files <span class="total-count total-empty">191</span> </a>
                                </div>
                                <div class="separator"></div>
                                <ul class="folder-list">
                                    <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span>Folder 1</span><span class="total-count">20</span><span class="clear"></span></a></li>
                                    <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span>Folder 2</span><span class="total-count">13</span><span class="clear"></span></a></li>
                                    <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span>Folder 3</span><span class="total-count">5</span><span class="clear"></span></a></li>
                                </ul>
                                <div class="separator"></div>
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
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php submit_button(); ?>
    </form>
</div>