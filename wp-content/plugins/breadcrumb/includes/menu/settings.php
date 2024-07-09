<?php
if ( ! defined('ABSPATH')) exit; // if direct access 

$current_tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : 'builder';




$breadcrumb_settings_tab = array();


$breadcrumb_settings_tab[] = array(
    'id' => 'options',
    'title' => sprintf(__('%s Options','breadcrumb'),'<i class="fas fa-laptop-code"></i>'),
    'priority' => 0,
    'active' => ($current_tab == 'options') ? true : false,
);


$breadcrumb_settings_tab[] = array(
    'id' => 'builder',
    'title' => sprintf(__('%s Builder','breadcrumb'),'<i class="fas fa-pencil-ruler"></i>'),
    'priority' => 10,
    'active' => ($current_tab == 'builder') ? true : false,
);





$breadcrumb_settings_tab[] = array(
    'id' => 'style',
    'title' => sprintf(__('%s Style','breadcrumb'),'<i class="fas fa-palette"></i>'),
    'priority' => 20,
    'active' => ($current_tab == 'style') ? true : false,
);

$breadcrumb_settings_tab[] = array(
    'id' => 'custom_scripts',
    'title' => sprintf(__('%s Custom Scripts','breadcrumb'),'<i class="fas fa-code"></i>'),
    'priority' => 30,
    'active' => ($current_tab == 'custom_scripts') ? true : false,
);

$breadcrumb_settings_tab[] = array(
    'id' => 'help_support',
    'title' => sprintf(__('%s Help & Support','breadcrumb'),'<i class="fas fa-hands-helping"></i>'),
    'priority' => 80,
    'active' => ($current_tab == 'help_support') ? true : false,
);



$breadcrumb_settings_tab[] = array(
    'id' => 'buy_pro',
    'title' => sprintf(__('%s Buy Pro','breadcrumb'),'<i class="fas fa-store"></i>'),
    'priority' => 90,
    'active' => ($current_tab == 'buy_pro') ? true : false,
);


$breadcrumb_settings_tabs = apply_filters('breadcrumb_settings_tabs', $breadcrumb_settings_tab);


$tabs_sorted = array();
foreach ($breadcrumb_settings_tabs as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
array_multisort($tabs_sorted, SORT_ASC, $breadcrumb_settings_tabs);





?>





<div class="wrap">

	<div id="icon-tools" class="icon32"><br></div><?php echo "<h2>".sprintf(__('%s Settings'), breadcrumb_plugin_name )."</h2>";?>
		<form  method="post" action="<?php echo str_replace( '%7E', '~', esc_url_raw($_SERVER['REQUEST_URI'])); ?>">
	        <input type="hidden" name="breadcrumb_hidden" value="Y">
            <input type="hidden" name="tab" value="<?php echo esc_attr($current_tab); ?>">

            <?php
            if(!empty($_POST['breadcrumb_hidden'])){

                $nonce = sanitize_text_field($_POST['_wpnonce']);

                if(wp_verify_nonce( $nonce, 'breadcrumb_nonce' ) && $_POST['breadcrumb_hidden'] == 'Y') {


                    do_action('breadcrumb_settings_save');

                    ?>
                    <div class="updated notice  is-dismissible"><p><strong><?php _e('Changes Saved.', 'breadcrumb' ); ?></strong></p></div>

                    <?php
                }
            }
            ?>

            <div class="settings-tabs-loading" style=""><?php _e('Loading...', 'breadcrumb' ); ?></div>
            <div class="settings-tabs vertical has-right-panel" style="display: none">
                <ul class="tab-navs">
                    <?php
                    foreach ($breadcrumb_settings_tabs as $tab){
                        $id = $tab['id'];
                        $title = $tab['title'];
                        $active = $tab['active'];
                        $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                        $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                        ?>
                        <li <?php if(!empty($data_visible)):  ?> data_visible="<?php echo esc_attr($data_visible); ?>" <?php endif; ?> class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo esc_attr($id); ?>"><?php echo wp_kses_post($title); ?></li>
                        <?php
                    }
                    ?>
                </ul>

                <div class="settings-tabs-right-panel">
                    <?php
                    foreach ($breadcrumb_settings_tabs as $tab) {
                        $id = $tab['id'];
                        $active = $tab['active'];

                        ?>
                        <div class="right-panel-content <?php if($active) echo 'active';?> right-panel-content-<?php echo esc_attr($id); ?>">
                            <?php

                            do_action('breadcrumb_settings_tabs_right_panel_'.$id);
                            ?>

                        </div>
                        <?php

                    }
                    ?>
                </div>

                <?php
                foreach ($breadcrumb_settings_tabs as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];


                    ?>

                    <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo esc_attr($id); ?>">
                        <?php
                        do_action('breadcrumb_settings_tabs_content_'.$id, $tab);
                        ?>
                    </div>
                    <?php
                }
                ?>

                <div class="clear clearfix"></div>
                <p class="submit">
                    <?php wp_nonce_field( 'breadcrumb_nonce' ); ?>
                    <input class="button button-primary" type="submit" name="Submit" value="<?php echo __('Save Changes', 'breadcrumb' ) ?>" />
                </p>

            </div>



		</form>


</div> <!-- end wrap -->
