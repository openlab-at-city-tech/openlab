<?php
    /**
    * add new package meta
    * 
    * @param mixed $pid
    * @param mixed $name
    * @param mixed $value
    */
    function add_wpdm_meta($pid, $name, $value, $uniq = false){
        global $wpdb;
        $value = is_array($value)?serialize($value):$value;
        $uniq = $uniq?1:0;
        $duplicate = $wpdb->get_var("select pid from {$wpdb->prefix}wpdm_filemeta where pid='$pid' and `name`='$name' and uniq=1");
        if($duplicate&&$uniq) return false;
        $wpdb->insert("{$wpdb->prefix}wpdm_filemeta",array('pid'=>$pid, 'name'=>$name, 'value'=>$value, 'uniq'=>$uniq));            
        return true;
    }
    
    /**
    * update package meta
    * 
    * @param mixed $pid
    * @param mixed $name
    * @param mixed $value
    * @param mixed $uniq
    */
    function update_wpdm_meta($pid, $name, $value, $uniq = false){
        global $wpdb;
        $wpdb->show_errors();
        $uniq = $uniq?1:0;
        delete_wpdm_meta($pid, $name);
        $value = is_array($value)?serialize($value):$value;
        add_wpdm_meta($pid, $name, $value, $uniq);
    }
    
    
    /**
    * delete package meta
    * 
    * @param mixed $pid
    * @param mixed $name
    */
    function delete_wpdm_meta($pid, $name){
        global $wpdb;
        $wpdb->query("delete from {$wpdb->prefix}wpdm_filemeta where pid='$pid' and `name`='$name'");
    }
    
    /**
    * get package meta
    * 
    * @param mixed $pid
    * @param mixed $name
    * @param mixed $single
    */
    function get_wpdm_meta($pid, $name, $single = true){
        global $wpdb;
        $data = $wpdb->get_results("select * from {$wpdb->prefix}wpdm_filemeta where pid='$pid' and `name`='$name'");
        if($single==true)
        return is_array(@unserialize($data[0]->value))?unserialize($data[0]->value):$data[0]->value;
        foreach($data as $d){
            $d->value = is_array(@unserialize($d->value))?unserialize($d->value):$d->value;
            $metas[$d->name] = $d->value;
        }
        return $metas;
    }
    
    /**
    * check if multi-user ebabled
    * 
    * @param mixed $cond
    */
    
    function wpdm_multi_user($cond=''){
        global $wpdb, $current_user;
        get_currentuserinfo(); 
        $ismu = get_option('wpdm_multi_user')==1&&!$current_user->caps['administrator']?true:false;
        return $ismu&&$cond?$cond:$ismu;
    }
    
    // Function that output's the contents of the dashboard widget
    function wpdm_dashboard_widget_function() {
        echo "<img src='".plugins_url('/download-manager/images/wpdm-logo.png')."' /><br/>";
         
        if(function_exists('curl_init')){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,'http://wpdownloadmanager.com/notice.php');
        curl_exec($ch);
        curl_close($ch);
        } else 
        echo "CURL need to be enabled!";
    }

    // Function that beeng used in the action hook
    function wpdm_add_dashboard_widgets() {
        wp_add_dashboard_widget('wpdm_dashboard_widget', 'WordPress Download Manager', 'wpdm_dashboard_widget_function');
        global $wp_meta_boxes;              
        $side_dashboard = $wp_meta_boxes['dashboard']['side']['core'];    
        $wpdm_widget = array('wpdm_dashboard_widget' => $wp_meta_boxes['dashboard']['normal']['core']['wpdm_dashboard_widget']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['wpdm_dashboard_widget']);
        $sorted_dashboard = array_merge($wpdm_widget, $side_dashboard);
        $wp_meta_boxes['dashboard']['side']['core'] = $sorted_dashboard;         
    }

    // Register the new dashboard widget into the 'wp_dashboard_setup' action
    add_action('wp_dashboard_setup', 'wpdm_add_dashboard_widgets',999999 );

    
    /**
    * popup
    * 
    */
    function wpdm_popup(){
    ?>
    <script language="JavaScript">
    <!--
      jQuery(function(){
        jQuery('.popup-link').click(function(){
          jQuery.prompt("<iframe noborder=true width=380px height=400px src='"+this.href+"'></iframe>",{ buttons: {  Close: false } });
          return false;
        });  
          
      });
    //-->
    </script>
    <style>
            /*-------------impromptu---------- */
            .jqifade{ position: absolute; background-color: #aaaaaa; }
            div.jqi{ width: 400px; font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; position: absolute; background-color: #ffffff; font-size: 11px; text-align: left; border: solid 1px #eeeeee; -moz-border-radius: 10px; -webkit-border-radius: 10px; padding: 7px; }
            div.jqi .jqicontainer{ font-weight: bold; }
            div.jqi .jqiclose{ position: absolute; top: 4px; right: -2px; width: 18px; cursor: default; color: #bbbbbb; font-weight: bold; }
            div.jqi .jqimessage{ padding: 10px; line-height: 20px; color: #444444; }
            div.jqi .jqibuttons{ text-align: right; padding: 5px 0 5px 0; border: solid 1px #eeeeee; background-color: #f4f4f4; }
            div.jqi button{ padding: 3px 10px; margin: 0 10px; background-color: #2F6073; border: solid 1px #f4f4f4; color: #ffffff; font-weight: bold; font-size: 12px; }
            div.jqi button:hover{ background-color: #728A8C; }
            div.jqi button.jqidefaultbutton{ background-color: #BF5E26; }
            .jqiwarning .jqi .jqibuttons{ background-color: #BF5E26; }
            /*-------------------------------- */
    </style>
    
    <?php
    }
    
    function __msg($key){
        include("messages.php");
        return $msgs[$key]?$msgs[$key]:$key;
    }
    
    function wpdm_skip_ngg_resource_manager($r){
        return false;
    }

    add_filter('run_ngg_resource_manager', 'wpdm_skip_ngg_resource_manager');
    
?>