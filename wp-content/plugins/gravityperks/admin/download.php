<?php

require_once(ABSPATH . '/wp-admin/includes/class-wp-list-table.php');
require_once(ABSPATH . '/wp-admin/includes/class-wp-upgrader.php');
require_once(ABSPATH . '/wp-admin/includes/file.php');
require_once(ABSPATH . '/wp-admin/includes/misc.php');
require_once(ABSPATH . '/wp-admin/includes/screen.php');

class GWPerksDownload {

    public static function load_page() {

        switch(gwget('action')) {
        case 'install':
        case 'upgrade':
            self::load_install_page();
            break;
        default:
            self::load_browsing_page();
        }

    }

    public static function load_browsing_page() {

        $perks_table = new GWPerksDownloadTable();
        $perks_table->prepare_items();

        ?>

        <style type="text/css">
            .plugins a.back { display: block; margin: 20px 0 0; }
        </style>

        <div class="wrap plugins">

            <div id="gravity-edit-icon" class="icon32" style="background:url(<?php echo GWPerks::get_base_url() ?>/admin/images/gravity-edit-icon-32.png) no-repeat;"><br></div>
            <h2><?php _e('Download Perk', 'gravityperks'); ?></h2>

            <a href="<?php echo remove_query_arg(array('slug', 'view', 'message')); ?>" class="back"><?php _e('Back to Installed Perks', 'gravityperks'); ?></a>

            <?php if(count($perks_table->items) <= 0): ?>

                <p><?php _e('Oops! There was a problem retrieving all available perks.', 'gravityperks'); ?></p>

            <?php else:

                $perks_table->display();

            endif; ?>

        </div>

        <?php
    }

    public static function load_install_page() {

        $slug = gwget('slug');
        $action = gwget('action');

        if($slug && !wp_verify_nonce(gwget('_wpnonce'), $slug))
            die(__('Oops! Are you sure you have access to this page?', 'gravityperks'));

        $perk_download_url = self::get_download_url($slug);

        switch($action) {
        case 'install':
            self::install_perk($perk_download_url);
            break;
        case 'upgrade':
            self::upgrade_perk($perk_download_url);
            break;
        }

    }

    public static function process_actions() {

    }

    public static function install_perk($source) {

        $installer = new GWPerksUpgrader(new GWPerksUpgraderSkin(array('title' => 'Installing Perk')));

        $installer->install(array(
            'source' => $source,
            'destination' => GWP_PERKS_DIR
            ));

        if(!is_wp_error($installer->result))
            GWPerks::flush_installed_perks();

    }

    public static function upgrade_perk($source) {

        $installer = new GWPerksUpgrader(new GWPerksUpgraderSkin(array('title' => 'Upgrading Perk')));

        $installer->upgrade(array(
            'source' => $source,
            'destination' => GWP_PERKS_DIR
            ));

        if(!is_wp_error($installer->result))
            GWPerks::flush_installed_perks();

    }

}

class GWPerksDownloadTable extends WP_List_Table {

    public $installed_perks;
    public $is_valid_key;

    function __construct() {

        $this->installed_perks = GWPerks::get_installed_perks();
        $this->_column_headers = array(
            array(
                'title' => __('Title', 'gravityperks'),
                'description' => __('Description', 'gravityperks')
                ),
                array(false),
                array(false)
            );

        parent::__construct();

    }

    function prepare_items() {

        $perks = GWPerks::get_perks_listing();
        $this->is_valid_key = GWPerks::has_valid_key();

        if(!is_array($perks)) {
            $this->items = array();
            return;
        }

        $items = array();

        $total = count($perks);
        $page = gwget('paged') ? gwget('paged') : 1;
        $per_page = 10;
        $offset = ($page * $per_page) - $per_page;
        $cap = $total > $offset + $per_page ? $offset + $per_page : $total;

        for($i = $offset; $i < $cap; $i++) {

            $items[$perks[$i]['slug']] = array(
                'title' => $this->get_column_title($perks[$i]),
                'description' => $perks[$i]['description'],
                'slug' => $perks[$i]['slug'],
                'version' => $perks[$i]['version']
                );

        }

        $this->items = $items;

        $this->set_pagination_args( array(
            'total_items' => $total,
            'per_page' => $per_page,
        ) );

    }

    function display_rows() {
        foreach ( $this->items as $slug => $item )
            $this->single_row($item, $slug);
    }

    function single_row($item, $slug) {
        static $row_class = '';

        $row_classes[] = $row_class == '' ? 'alternate' : '';
        $row_classes[] = $this->is_installed($slug) ? 'inactive' : 'active';


        echo '<tr class="' . implode(' ', $row_classes) . '">';
        echo $this->single_row_columns($item);
        echo '</tr>';
    }

    function column_default($item, $column_name) {

        switch($column_name) {
        case 'description':
            ?>

            <div class="plugin-description">
                <p><?php echo gwar($item, $column_name); ?></p>
            </div>

            <div class="second plugin-version-author-uri">

                <?php

                $perk_meta = array();

                if(gwar($item, 'version'))
                    $perk_meta[] = sprintf(__('Version %s', 'gravityperks'), $item['version']);

                if(gwar($item, 'author')) {
                    $author = gwar($item, 'author') ? "<a href=\"{$item['authoruri']}\">{$item['author']}</a>" : $item['author'];
                    $perk_meta[] = sprintf(__('By %s', 'gravityperks'), $author);
                }

                if(gwar($item, 'snippeturi'))
                    $perk_meta[] = '<a href="' . $item['snippeturi'] . '" title="' . esc_attr__('Visit perk site', 'gravityperks') . '">' . __('Visit perk site', 'gravityperks') . '</a>';

                echo implode(' | ', $perk_meta);

                ?>

            </div>

            <?php
            break;
        default:
            echo gwar($item, $column_name);
        }

    }

    function get_column_title($perk) {

        $title = '<strong>' . $perk['title'] . '</strong>';
        $title .= '<div class="row-actions-visible">';

        $actions = array();
        $snp_obj = new GWPerk($perk['slug']);

        if(!self::is_installed($perk['slug'])) {
            if($this->is_valid_key) {
                $actions[] = '<span class="install"><a title="' . __('Install this perk', 'gravityperks') . '" href="' . $snp_obj->get_link_for('install') . '" onclick="gperk.confirmActionUrl(event, \'' . sprintf(__('Are you sure you want to install the %s perk?', 'gravityperks'), $perk['title']) . '\')">' . __('Install', 'gravityperks') . '</a></span>';
            } else {
                $actions[] = '<span class="install"><a title="' . __('Already purchased a license? Click here to enter it.', 'gravityperks') . '" href="' . $snp_obj->get_link_for('plugin_settings') . '">' . __('Enter License Key', 'gravityperks') . '</a></span>';
                $actions[] = '<span class="install"><a title="' . __('Click here to purchase a license!', 'gravityperks') . '" href="' . $snp_obj->get_link_for('purchase') . '" target="_blank">' . __('Purchase License', 'gravityperks') . '</a></span>';
            }
        } else {
            $actions[] = '<span class="installed">' . __('Installed', 'gravityperks') . '</span>';
        }

        $title .= implode(' | ', $actions);
        $title .= '</div>';

        return $title;
    }

    function is_installed($slug) {

        $installed_perks = $this->installed_perks;

        foreach($installed_perks as $installed_perk) {
            if($installed_perk->slug == $slug)
                return true;
        }

        return false;
    }

}