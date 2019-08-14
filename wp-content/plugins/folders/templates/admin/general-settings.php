<?php
defined('ABSPATH') or die('Nope, not accessing this');
?>
<style>
    span.upgrade-message {
        padding: 5px 10px;
        background: #fff;
        display: inline-block;
        font-size: 14px;
        color: #000;
    }
    a.pink, span.pink {
        color: #FF5983;
        text-decoration: none;
        font-weight: bold;
    }
    #setting-form {
        float: left;
        width: 300px;
    }
    <?php if ( function_exists( 'is_rtl' ) && is_rtl() ) { ?>
    #setting-form {
        float: right;
    }
    <?php } ?>
</style>
<div class="wrap">
    <h1><?php _e( 'Folders Settings', WCP_FOLDER ); ?></h1>
    <form action="options.php" method="post" id="setting-form">
        <?php
        settings_fields( 'folders_settings' );
        $options = get_option('folders_settings');
        do_settings_sections( __FILE__ );
        ?>
        <table class="form-table">
            <?php
            $post_types = get_post_types( array( 'public' => true ), 'objects' );
            $post_array = array("page", "post", "attachment");
            foreach ( $post_types as $post_type ) : ?>
                <?php
                if ( ! $post_type->show_ui) continue;
                if(in_array($post_type->name, $post_array)){
                    ?>
                    <tr>
                        <th>
                            <label for="folders_<?php echo $post_type->name; ?>" ><?php echo __( 'Use folders with:', WCP_FOLDER )." ".$post_type->label; ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="folders_<?php echo $post_type->name; ?>" name="folders_settings[]" value="<?php echo $post_type->name; ?>"<?php if ( in_array( $post_type->name, $options ) ) echo ' checked="checked"'; ?>/>
                        </td>
                    </tr>
                <?php
                } else { ?>
                    <tr>
                        <th>
                            <label for="folders_<?php echo $post_type->name; ?>" ><?php echo __( 'Use folders with:', WCP_FOLDER )." ".$post_type->label; ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="folders_<?php echo $post_type->name; ?>" name="folders_settings[]" value="<?php echo $post_type->name; ?>"<?php if ( in_array( $post_type->name, $options ) ) echo ' checked="checked"'; ?>/>
                        </td>
                    </tr>
                <?php } endforeach; ?>
            <tr>
                <th>
                    <label for="folders_<?php echo $post_type->name; ?>" ><?php echo __( 'Show Folders in Menu:', WCP_FOLDER ); ?></label>
                </th>
                <td>
                    <?php $val = get_option("folders_show_in_menu"); ?>
                    <input type="hidden" name="folders_show_in_menu" value="off" />
                    <input type="checkbox" name="folders_show_in_menu" value="on" <?php echo ($val == "on")?"checked='checked'":"" ?>/>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px 0">
                    <?php
                    $total_folders = get_option("folder_old_plugin_folder_status");
                    if($total_folders == false || $total_folders < 10) {
                        $total_folders = 10;
                    }
                    $total = WCP_Folders::get_total_term_folders();
                    if($total > $total_folders) {
                        $total_folders = $total;
                    }
                    ?>
                    <span class="upgrade-message">You have used <?php echo "<span class='pink'>".$total."</span>/".$total_folders ?> Folders. <a class="pink" href="<?php echo admin_url("admin.php?page=wcp_folders_upgrade") ?>"><?php echo __("Upgrade", WCP_FOLDER) ?></a></span>
                </td>
            </tr>
        </table>
        <input type="hidden" name="folders_settings1[premio_folder_option]" value="yes" />
        <?php submit_button(); ?>
    </form>
</div>