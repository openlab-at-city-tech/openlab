<?php
/**
 * Class C_Cache
 * @mixin Mixin_Cache
 * @implements I_Cache
 */
class C_Cache extends C_Component
{
    public static $_instances = array();
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Cache');
        $this->implement('I_Cache');
    }
    /**
     * @param bool|string $context
     * @return C_Cache
     */
    public static function get_instance($context = False)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Cache($context);
        }
        return self::$_instances[$context];
    }
}
class Mixin_Cache extends Mixin
{
    /**
     * Empties a directory of all of its content
     *
     * @param string $directory Absolute path
     * @param bool $recursive Remove files from subdirectories of the cache
     * @param string $regex (optional) Only remove files matching pattern; '/^.+\.png$/i' will match all .png
     */
    public function flush_directory($directory, $recursive = TRUE, $regex = NULL)
    {
        // It is possible that the cache directory has not been created yet
        if (!is_dir($directory)) {
            return;
        }
        if ($recursive) {
            $directory = new DirectoryIterator($directory);
        } else {
            $directory = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory), RecursiveIteratorIterator::CHILD_FIRST);
        }
        if (!is_null($regex)) {
            $iterator = RegexIterator($directory, $regex, RecursiveRegexIterator::GET_MATCH);
        } else {
            $iterator = $directory;
        }
        foreach ($iterator as $file) {
            if ($file->isFile() || $file->isLink()) {
                @unlink($file->getPathname());
            } elseif ($file->isDir() && !$file->isDot() && $recursive) {
                @rmdir($file->getPathname());
            }
        }
    }
    /**
     * Flushes cache from all available galleries
     *
     * @param array $galleries When provided only the requested galleries' cache is flushed
     */
    public function flush_galleries($galleries = array())
    {
        global $wpdb;
        if (empty($galleries)) {
            $galleries = C_Gallery_Mapper::get_instance()->find_all();
        }
        foreach ($galleries as $gallery) {
            C_Gallery_Storage::get_instance()->flush_cache($gallery);
        }
        // Remove images still in the DB whose gallery no longer exists
        $wpdb->query("DELETE FROM `{$wpdb->nggpictures}` WHERE `galleryid` NOT IN (SELECT `gid` FROM `{$wpdb->nggallery}`)");
    }
}