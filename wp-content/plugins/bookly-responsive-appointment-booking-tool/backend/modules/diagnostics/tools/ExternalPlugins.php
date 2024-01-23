<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

class ExternalPlugins extends Tool
{
    protected $slug = 'external-plugins';
    protected $hidden = true;

    protected $plugins = array(
        'ari-adminer' => array(
            'name' => 'ARI',
            'source' => 'https://downloads.wordpress.org/plugin/ari-adminer.zip',
            'basename' => 'ari-adminer/ari-adminer.php',
        ),
        'wp-file-manager' => array(
            'name' => 'File Manager',
            'source' => 'wordpress',
            'basename' => 'wp-file-manager/file_folder_manager.php',
        ),
        'wp-mail-logging' => array(
            'name' => 'WP Mail Logging',
            'source' => 'wordpress',
            'basename' => 'wp-mail-logging/wp-mail-logging.php',
        ),
        'code-snippets' => array(
            'name' => 'Code Snippets',
            'source' => 'wordpress',
            'basename' => 'code-snippets/code-snippets.php',
        ),
        'pexlechris-adminer' => array(
            'name' => 'WP Adminer',
            'source' => 'wordpress',
            'basename' => 'pexlechris-adminer/pexlechris-adminer.php',
        ),
    );

    public function __construct()
    {
        $this->title = 'External Plugins';
    }

    public function render()
    {
        self::renderTemplate( '_external_plugins', array( 'plugins' => $this->plugins ) );
    }

    public function install()
    {
        $plugin = self::parameter( 'plugin' );
        if ( array_key_exists( $plugin, $this->plugins ) ) {
            if ( ! file_exists( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->plugins[ $plugin ]['basename'] ) ) {
                if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
                    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                }
                $upgrader = new \Plugin_Upgrader( new \Automatic_Upgrader_Skin() );
                if ( $this->plugins[ $plugin ]['source'] === 'wordpress' ) {
                    if ( ! function_exists( 'plugins_api' ) ) {
                        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
                    }
                    $response = plugins_api(
                        'plugin_information',
                        array(
                            'slug' => $plugin,
                        )
                    );
                    if ( $response instanceof \WP_Error ) {
                        wp_send_json_error();
                    }
                    $source = $response->download_link;
                } else {
                    $source = $this->plugins[ $plugin ]['source'];
                }

                $state = $upgrader->install( $source );
            } else {
                $state = true;
            }

            if ( $state === true ) {
                activate_plugin( $this->plugins[ $plugin ]['basename'] );
                wp_send_json_success();
            }
        }

        wp_send_json_error();
    }

    public function delete()
    {
        $plugin = self::parameter( 'plugin' );
        if ( array_key_exists( $plugin, $this->plugins ) ) {
            deactivate_plugins( array( $this->plugins[ $plugin ]['basename'] ) );
            $state = delete_plugins( array( $this->plugins[ $plugin ]['basename'] ) );
            if ( $state === true ) {
                wp_send_json_success();
            }
        }

        wp_send_json_error();
    }

    public function activate()
    {
        $plugin = self::parameter( 'plugin' );
        if ( array_key_exists( $plugin, $this->plugins ) ) {
            $state = activate_plugin( $this->plugins[ $plugin ]['basename'] );
            if ( $state === null ) {
                wp_send_json_success();
            }
        }

        wp_send_json_error();
    }
}