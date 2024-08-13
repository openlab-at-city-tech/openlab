<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

class FileMonitor extends Tool
{
    protected $slug = 'file-monitor';
    protected $hidden = true;
    protected $list;
    const SLICE_LENGTH = 8;

    public function __construct()
    {
        $this->title = 'File monitor';
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $list = $this->getList();
        $slice_length = self::SLICE_LENGTH;

        return self::renderTemplate( '_file_monitor', compact( 'list', 'slice_length' ), false );
    }

    /**
     * @inheritDoc
     */
    public function hasError()
    {
        $this->getList();

        return ! empty( $this->list );
    }

    /**
     * @return array
     */
    private function getList()
    {
        if ( $this->list === null ) {
            if ( ! class_exists( 'WP_Filesystem_Direct', false ) ) {
                require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
                require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
            }

            $fs = new \WP_Filesystem_Direct( null );
            foreach ( apply_filters( 'bookly_plugins', array() ) as $slug => $plugin_class ) {
                /** @var $plugin_class \Bookly\Lib\Plugin */
                $mtime = $fs->mtime( $plugin_class::getMainFile() );
                $data = array(
                    'title' => $plugin_class::getTitle(),
                    'slug' => $slug,
                    'plugin' => $plugin_class::getBasename(),
                );
                if ( $mtime === false ) {
                    $data['list'] = array( 'skipped, the file main.php was not found in the root directory' => null );
                    $data['mod_count'] = -self::SLICE_LENGTH;
                    $this->list[] = $data;
                    continue;
                }
                $tree = $this->tree( $fs->dirlist( $plugin_class::getDirectory(), true, true ), '', $mtime );
                if ( $tree ) {
                    $flat = $this->flat( $tree, '' );
                    $count = count( $flat );
                    if ( count( $flat ) > self::SLICE_LENGTH ) {
                        $flat = array_slice( $flat, 0, self::SLICE_LENGTH );
                    }
                    $data['list'] = $flat;
                    $data['mod_count'] = $count - self::SLICE_LENGTH;
                    $this->list[] = $data;
                }
            }
        }

        return $this->list;
    }

    /**
     * @param array $node
     * @param string $path
     * @param integer $modified_at
     * @return array|null
     */
    private function tree( $node, $path, $modified_at )
    {
        $result = array();
        if ( isset( $node['name'] ) ) {
            if ( $node['type'] === 'd' ) {
                $path .= '\\' . $node['name'];

                return $this->tree( $node['files'], $path, $modified_at );
            }
            if ( $node['type'] === 'f' ) {
                if ( abs( $node['lastmodunix'] - $modified_at ) > 5 ) {
                    return $node['lastmod'] . ' ' . $node['time'];
                }

                return null;
            }
        } else {
            foreach ( $node as $name => $f ) {
                $mod = $this->tree( $f, $path, $modified_at );
                if ( $mod ) {
                    $result[ $name ] = $mod;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $tree
     * @param string $path
     * @return array
     */
    private function flat( $tree, $path )
    {
        $result = array();
        foreach ( $tree as $node => $data ) {
            if ( is_array( $data ) ) {
                $items = $this->flat( $data, $path . '/' . $node );
                foreach ( $items as $key => $value ) {
                    $result[ $key ] = $value;
                }
            } else {
                $result[ $path . '/' . $node ] = $data;
            }
        }

        return $result;
    }
}