<?php
class Mappress_Db {
	const DB_VERSION = '2.80';

	static function register() {
		self::create_db();
		add_action('wp_ajax_mapp_upgrade', array(__CLASS__, 'ajax_upgrade'));
	}

	static function create_db() {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mapp_maps';

		$exists = $wpdb->get_var("show tables like '$maps_table'");
		if ($exists)
			return;

		$wpdb->show_errors(true);
		$result = $wpdb->query("CREATE TABLE IF NOT EXISTS $maps_table (
			mapid INT NOT NULL AUTO_INCREMENT,
			otype VARCHAR(32),
			oid INT,
			status VARCHAR(64),
			title VARCHAR(512),
			obj LONGTEXT,
			INDEX title_idx (title(191)),
			PRIMARY KEY  (mapid),
			UNIQUE KEY object_idx (otype, oid, mapid)
			) CHARACTER SET utf8;"
		);
		$wpdb->show_errors(false);
		return $result;
	}

	static function ajax_upgrade() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('manage_options'))
			Mappress::ajax_response('Not authorized');

		// Process each upgrade
		$upgrades = array('2.80');
		$current_version = get_option('mappress_db_version');

		foreach($upgrades as $version) {
			if ($current_version && version_compare($current_version, $version, '>='))
				continue;

			$result = self::upgrade();
			if ($result !== true)
				Mappress::ajax_response($result);
		}
		Mappress::ajax_response('OK', array('msgtype' => 'i', 'msg' => __('Success!  Upgrade complete.', 'mappress-google-maps-for-wordpress')));
	}

	// Run all available upgrades, can be called immediately during plugin upgrade or from upgrade screen
	static function upgrade() {
		// Process each upgrade
		$upgrades = array('2.80');
		$current_version = get_option('mappress_db_version');

		foreach($upgrades as $version) {
			if (!$current_version || version_compare($current_version, $version, '>='))
				continue;

			$fn = 'upgrade_' . str_replace('.', '_', $version);

			if (!method_exists(__CLASS__, $fn))
				return 'Upgrade function missing: ' . $fn;

			$result = self::$fn();

			if ($result !== true)
				return $result;

			update_option('mappress_db_version', $version);
			$current_version = $version;
			return true;
		}
	}

	static function upgrade_2_80() {
		global $wpdb;

		$new_maps = $wpdb->prefix . 'mapp_maps';
		$old_maps = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		// Create new maps table
		$wpdb->query("DROP TABLE IF EXISTS $new_maps");
		$result = self::create_db();
		if ($result === false)
			return $wpdb->last_error();

		// Populate
		$sql = "SELECT $posts_table.mapid, $posts_table.postid, $old_maps.obj "
		. " FROM $old_maps "
		. " INNER JOIN $posts_table ON ($posts_table.mapid = $old_maps.mapid)";
		$rows = $wpdb->get_results($sql);

		// Modify rows, can be rerun because most data is read from legacy posts table
		foreach($rows as $row) {
			$mapdata = unserialize($row->obj);
			$mapdata->mapid = $row->mapid;
			$mapdata->oid = $row->postid;
			$mapdata->otype = 'post';
			unset($mapdata->postid);

			// Update POIs to remove correctedAddress
			foreach($mapdata->pois as $poi) {
				if ($poi->correctedAddress) {
					$poi->address = $poi->correctedAddress;
					unset ($poi->correctedAddress);
				}
			}

			$map = new Mappress_Map($mapdata);
			$result = $map->save();

			if ($result === false)
				return 'ERROR SAVING: ' . $wpdb->last_error;
		}
		$wpdb->query("COMMIT WORK");
		return true;
	}

	static function upgrade_check() {
		$current_version = get_option('mappress_db_version');

		if (empty($current_version)) {
			update_option('mappress_db_version', self::DB_VERSION);
			return false;
		}

		return (version_compare($current_version, self::DB_VERSION, '<'));
	}

	static function upgrade_page() {
		$state = array(
			'nonce' => wp_create_nonce('mappress'),
		);

		?>
		<script>var mappress_upgrade_state=<?php echo json_encode($state);?>;</script>
		<div class="mapp-options">
			<div class='mapp-options-header'>
				<div class='mapp-options-header-version'>
					<h1><?php _e('MapPress', 'mappress-google-maps-for-wordpress'); ?></h1>
					<?php echo Mappress::$version; ?>
				</div>
			</div>
			<div class='mapp-upgrade' action=''>
				<?php if (self::upgrade_check()) { ?>
					<h3><?php _e('Map data upgrade', 'mappress-google-maps-for-wordpress') ?></h3>
					<div><?php _e('Your map data needs to be upgraded.', 'mappress-google-maps-for-wordpress');?></div>
					<div class='mapp-upgrade-warning'><?php _e('Please make a database backup now.  Data will be permanently modified.', 'mappress-google-maps-for-wordpress'); ?></div>
					<div id="mapp-db-upgrade"></div>
				<?php } else { ?>
					<h2><?php _e('Your map data is up to date.', 'mappress-google-maps-for-wordpress');?></h2>
				<?php } ?>
			</div>
		</div>
		<?php
	}
}
?>