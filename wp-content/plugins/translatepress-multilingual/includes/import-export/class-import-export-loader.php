<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Always loaded: holds the tool's hook callbacks and loads its classes only when one of them runs.
class TRP_Import_Export_Loader {

    const PAGE_SLUG = 'trp_import_export';

    protected $settings;
    protected $tool;

    public function __construct( $settings ) {
        $this->settings = $settings;
    }

    private function get_tool() {
        if ( ! $this->tool ) {
            $dir = TRP_PLUGIN_DIR . 'includes/import-export/';
            require_once $dir . 'class-import-export-job.php';
            require_once $dir . 'class-import-export-archive.php';
            require_once $dir . 'class-import-export-exporter.php';
            require_once $dir . 'class-import-export-writer.php';
            require_once $dir . 'interface-import-source.php';
            require_once $dir . 'sources/class-import-source-translatepress.php';
            require_once $dir . 'class-import-source-registry.php';
            require_once $dir . 'class-import-export.php';
            $this->tool = new TRP_Import_Export( $this->settings );
        }
        return $this->tool;
    }

    protected function get_capability() {
        return apply_filters( 'trp_settings_capability', 'manage_options' );
    }

    public function add_submenu_page() {
        add_submenu_page(
            'TRPHidden',
            __( 'TranslatePress Import & Export Translations', 'translatepress-multilingual' ),
            'TRPHidden',
            $this->get_capability(),
            self::PAGE_SLUG,
            array( $this, 'render_page' )
        );
    }

    public function render_page() {
        $this->get_tool()->render_page();
    }

    public function highlight_advanced_tab( $active_tab ) {
        return ( $active_tab === self::PAGE_SLUG ) ? 'trp_advanced_page' : $active_tab;
    }

    public function enqueue_scripts_and_styles( $hook ) {
        if ( $hook !== 'admin_page_' . self::PAGE_SLUG ) {
            return;
        }
        $this->get_tool()->enqueue_scripts_and_styles();
    }

    public function download() {
        $this->get_tool()->download();
    }

    public function handle_request() {
        $this->get_tool()->handle_request();
    }
}
