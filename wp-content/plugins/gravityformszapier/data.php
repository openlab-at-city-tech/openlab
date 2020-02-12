<?php
class GFZapierData{

	/**
	 * Gets called when Gravity Forms upgrade process is completed.
	 *
	 * @since 2.1.2
	 *
	 * @param string $db_version          The current Gravity Forms database version.
	 * @param string $previous_db_version The previous Gravity Forms database version.
	 * @param bool   $force_upgrade       Is the upgrade being forced? i.e. from the link on the system status page.
	 */
	public static function post_gravityforms_upgrade( $db_version, $previous_db_version, $force_upgrade ) {
		if ( $force_upgrade ) {
			self::update_table();
		}
	}

    public static function update_table(){
        global $wpdb;
        $table_name = self::get_zapier_table_name();

        if ( ! empty($wpdb->charset) )
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if ( ! empty($wpdb->collate) )
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = "CREATE TABLE $table_name (
              id mediumint(8) unsigned not null auto_increment,
              form_id mediumint(8) unsigned not null,
              is_active tinyint(1) not null default 1,
              name varchar(150) not null,
              url varchar(150) not null,
              meta longtext,
              PRIMARY KEY  (id),
              KEY form_id (form_id)
            )$charset_collate;";

        require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function get_zapier_table_name(){
        global $wpdb;
        return $wpdb->prefix . "rg_zapier";
    }

    public static function get_feeds(){
        global $wpdb;
        $table_name = self::get_zapier_table_name();
        $form_table_name = RGFormsModel::get_form_table_name();
        $sql = "SELECT s.id, s.is_active, s.form_id, s.name, s.url, s.meta, f.title as form_title
                FROM $table_name s
                INNER JOIN $form_table_name f ON s.form_id = f.id";

        $results = $wpdb->get_results($sql, ARRAY_A);

        $count = sizeof($results);
        for($i=0; $i<$count; $i++){
            $results[$i]["meta"] = maybe_unserialize($results[$i]["meta"]);
        }

        return $results;
    }

    public static function delete_feed($id){
        global $wpdb;
        $table_name = self::get_zapier_table_name();
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id=%s", $id));
    }

    public static function get_feed_by_form($form_id, $only_active = false){
        global $wpdb;
        $table_name = self::get_zapier_table_name();
        $active_clause = $only_active ? " AND is_active=1" : "";
        $sql = $wpdb->prepare("SELECT id, form_id, is_active, name, url, meta FROM $table_name WHERE form_id=%d $active_clause", $form_id);
        $results = $wpdb->get_results($sql, ARRAY_A);
        if(empty($results))
            return array();

        //Deserializing meta
        $count = sizeof($results);
        for($i=0; $i<$count; $i++){
            $results[$i]["meta"] = maybe_unserialize($results[$i]["meta"]);
        }
        return $results;
    }

    public static function get_feed($id){
        global $wpdb;
        $table_name = self::get_zapier_table_name();
        $sql = $wpdb->prepare("SELECT id, form_id, is_active, name, url, meta FROM $table_name WHERE id=%d", $id);
        $results = $wpdb->get_results($sql, ARRAY_A);
        if(empty($results))
            return array();

        $result = $results[0];
        $result["meta"] = maybe_unserialize($result["meta"]);
        return $result;
    }

    public static function update_feed($id, $form_id, $is_active, $name, $url, $meta){
        global $wpdb;
        $table_name = self::get_zapier_table_name();
        $meta = maybe_serialize($meta);
        if($id == 0){
            //insert
            $wpdb->insert($table_name, array("form_id" => $form_id, "is_active"=> $is_active, "name" => $name, "url" => $url, "meta" => $meta), array("%d", "%d", "%s", "%s", "%s", "%s"));
            $id = $wpdb->get_var("SELECT LAST_INSERT_ID()");
        }
        else{
            //update
            $wpdb->update($table_name, array("form_id" => $form_id, "is_active"=> $is_active, "name" => $name, "url" => $url, "meta" => $meta), array("id" => $id), array("%d", "%d", "%s", "%s", "%s", "%s"));
        }

        return $id;
    }

    public static function drop_tables(){
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS " . self::get_zapier_table_name());
    }
}