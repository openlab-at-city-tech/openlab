<?php

// Add new box to the Genesis -> Theme Settings page
// Comment the following line to remove the color select option
add_action('admin_menu', 'ntg_add__style_settings_box', 11);
function ntg_add__style_settings_box() {
global $_genesis_theme_settings_pagehook;
add_meta_box('agentpress-theme-settings-slider', __('AgentPress Color Style', 'genesis'), 'ntg_theme_settings_style_box', $_genesis_theme_settings_pagehook, 'column2');

}

function ntg_theme_settings_style_box() {
    // set the default selection (if empty)
    $style = genesis_get_option('style_selection') ? genesis_get_option('style_selection') : 'style.css';
?>

    <p><label><?php _e('Stylesheet', 'genesis'); ?>: 
        <select name="<?php echo GENESIS_SETTINGS_FIELD; ?>[style_selection]">
            <?php
            foreach ( glob(CHILD_DIR . "/*.css") as $file ) :
            $file = str_replace( CHILD_DIR . '/', '', $file );
            
            if(!genesis_style_check($file, 'genesis')){
            continue;
            }
            
            ?>
                
            <option style="padding-right:10px;" value="<?php echo esc_attr( $file ); ?>" <?php selected($file, $style); ?>><?php echo esc_html( $file ); ?></option>
            
            <?php 
            
            endforeach; ?>
        </select>
    </label></p>
    <p><span class="description">Please select the Lifestyle color style from the drop down list and save your settings. Only stylesheets found in the child theme directory will be included in this list.</span></p>
<?php
}

// Checks if the style sheet is a Genesis style sheet
function genesis_style_check($fileText, $char_list) {

    $fh = fopen(CHILD_DIR . '/' . $fileText, 'r');
    $theData = fread($fh, 500);
    fclose($fh);
    
    $search = strpos($theData, $char_list);
    if($search === false){
            return false;
        }
        return true;
}

// Changes the style sheet per the selection in the theme settings and loads style.css if selected style sheet is not available
add_filter('stylesheet_uri', 'child_stylesheet_uri', 10, 2);
function child_stylesheet_uri($stylesheet, $dir) {
    $style = genesis_get_option('style_selection');
    if ( !$style ) return $stylesheet;
    if (!file_exists(CHILD_DIR . '/' . $style)) return $stylesheet;
    
    return $dir . '/' . $style;
}

?>