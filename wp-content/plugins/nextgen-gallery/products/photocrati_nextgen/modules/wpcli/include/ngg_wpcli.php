<?php

class C_NGG_WPCLI
{
}

class C_NGG_WPCLI_Cache
{
    /**
     * Flushes NextGen Gallery caches
     * @param array $args
     * @param array $assoc_args
     * @synopsis [--expired]
     */
    function flush($args, $assoc_args)
    {
        $expired = !empty($assoc_args['expired']) ? TRUE : FALSE;
        C_Photocrati_Transient_Manager::flush($expired);
        WP_CLI::success('Flushed caches');
    }
}

class C_NGG_WPCLI_Album
{
    /**
     * Create a new album
     * @param array $args
     * @param array $assoc_args
     * @synopsis <album_name> [--description=<description>] --author=<user_login>
     */
    function create($args, $assoc_args)
    {
        $mapper = C_Album_Mapper::get_instance();
        $user = get_user_by('login', $assoc_args['author']);
        if (!$user)
            WP_CLI::error("Unable to find user {$assoc_args['author']}");

        $description = !empty($assoc_args['description']) ? $assoc_args['description'] : '';

        $album = $mapper->create(array(
            'name'      => $args[0],
            'albumdesc' => $description,
            'author'    => $user->ID,
        ));

        if ($album && $album->save())
        {
            $album_id = $retval = $album->id();
            WP_CLI::success("Created album with id #{$album_id}");
        }
        else {
            WP_CLI::error("Unable to create album");
        }
    }

    /**
     * Deletes the requested album
     * @param $args
     * @param $assoc_args
     * @synopsis <album_id>
     */
    function delete($args, $assoc_args)
    {
        $mapper = C_Album_Mapper::get_instance();
        $album = $mapper->find($args[0]);
        if (!$album)
            WP_CLI::error("Unable to find album {$args[0]}");

        $mapper->destroy($album);
        WP_CLI::success("Album with id #{$album->id} has been deleted");
    }

    /**
     * Change album attributes
     * @param $args
     * @param $assoc_args
     * @synopsis <album_id> [--description=<description>] [--name=<name>]
     */
    function edit($args, $assoc_args)
    {
        $mapper = C_Album_Mapper::get_instance();
        $album = $mapper->find($args[0]);
        if (!$album)
            WP_CLI::error("Unable to find album {$args[0]}");

        if (empty($assoc_args['description']) && empty($assoc_args['name']))
            WP_CLI::error("You must provide a new description or title");

        if (!empty($assoc_args['description']))
            $album->albumdesc = $assoc_args['description'];
        if (!empty($assoc_args['name']))
            $album->name = $assoc_args['name'];

        $mapper->save($album);
        WP_CLI::success("Album with id #{$album->id} has been modified");
    }

    /**
     * @param array $args
     * @param array $assoc_args
     * @subcommand list
     */
    function _list($args, $assoc_args)
    {
        $mapper = C_Album_Mapper::get_instance();
        $display = array();
        foreach ($mapper->find_all() as $album) {
            $display[] = array(
                'id'            => $album->id,
                'name'          => $album->name,
                '# of children' => count($album->sortorder),
                'description'   => $album->albumdesc
            );
        }

        \WP_CLI\Utils\format_items('table', $display, array('id', 'name', '# of children', 'description'));
    }

    /**
     * Adds child galleries or albums to an album
     * @param $args
     * @param $assoc_args
     * @synopsis <album_id> [--galleries=<galleries>] [--albums=<albums>]
     */
    function add_children($args, $assoc_args)
    {
        $album_mapper = C_Album_Mapper::get_instance();
        $album = $album_mapper->find($args[0]);
        if (!$album)
            WP_CLI::error("Unable to find album {$args[0]}");

        if (empty($assoc_args['galleries']) && empty($assoc_args['albums']))
            WP_CLI::error("You must provide new child galleries or albums");

        if (!empty($assoc_args['galleries']))
        {
            $new = explode(',', $assoc_args['galleries']);
            $gallery_mapper = C_Gallery_Mapper::get_instance();
            foreach ($new as $gallery_id) {
                $gallery = $gallery_mapper->find($gallery_id);
                if (!$gallery)
                    WP_CLI::error("Unable to find gallery {$gallery_id}");
                if (in_array($gallery_id, $album->sortorder))
                    WP_CLI::error("Gallery with id {$gallery_id} already belongs to this album");
                $album->sortorder[] = $gallery_id;
            }
        }

        if (!empty($assoc_args['albums']))
        {
            $new = explode(',', $assoc_args['albums']);
            foreach ($new as $album_id) {
                $new_album = $album_mapper->find($album_id);
                if (!$new_album)
                    WP_CLI::error("Unable to find album {$album_id}");
                if (in_array($album_id, $album->sortorder))
                    WP_CLI::error("Album with id {$album_id} already belongs to this album");
                if ($album_id == $args[0])
                    WP_CLI::error("Cannot add an album to itself");
                $album->sortorder[] = 'a' . $album_id;
            }
        }

        $album_mapper->save($album);
        WP_CLI::success("Album with id #{$album->id} has been modified");
    }

    /**
     * Removes child galleries or albums attached to an album
     * @param $args
     * @param $assoc_args
     * @synopsis <album_id> [--galleries=<galleries>] [--albums=<albums>]
     */
    function delete_children($args, $assoc_args)
    {
        $album_mapper = C_Album_Mapper::get_instance();
        $parent_album = $album_mapper->find($args[0]);
        if (!$parent_album)
            WP_CLI::error("Unable to find album {$args[0]}");

        if (empty($assoc_args['galleries']) && empty($assoc_args['albums']))
            WP_CLI::error("You must provide a child gallery or album to remove");

        $galleries = array();
        $albums    = array();

        if (!empty($assoc_args['galleries']))
            $galleries = explode(',', $assoc_args['galleries']);
        if (!empty($assoc_args['albums']))
            $albums = explode(',', $assoc_args['albums']);

        foreach ($parent_album->sortorder as $ndx => $child) {
            foreach ($galleries as $gallery) {
                if ($gallery == $child)
                    unset($parent_album->sortorder[$ndx]);
            }
            foreach ($albums as $album) {
                if ('a' . $album == $child)
                    unset($parent_album->sortorder[$ndx]);
            }
        }

        $album_mapper->save($parent_album);
        WP_CLI::success("Album with id #{$parent_album->id} has been modified");
    }

    /**
     * Lists all child galleries and albums belonging to an album
     * @param $args
     * @param $assoc_args
     * @synopsis <album_id>
     */
    function list_children($args, $assoc_args)
    {
        $album_mapper   = C_Album_Mapper::get_instance();
        $gallery_mapper = C_Gallery_Mapper::get_instance();

        $album = $album_mapper->find($args[0]);
        if (!$album)
            WP_CLI::error("Unable to find album {$args[0]}");

        $display = array();
        foreach ($album->sortorder as $child) {
            $is_album = (strpos($child, 'a') === 0) ? TRUE : FALSE;
            if ($is_album)
            {
                $child = str_replace('a', '', $child);
                $child_album = $album_mapper->find($child);
                $display[] = array(
                    'id'          => $child_album->id,
                    'type'        => 'album',
                    'title'       => $child_album->name,
                    'description' => $child_album->albumdesc
                );
            }
            else {
                $child_gallery = $gallery_mapper->find($child);
                $display[] = array(
                    'id'          => $child_gallery->gid,
                    'type'        => 'gallery',
                    'title'       => $child_gallery->title,
                    'description' => $child_gallery->galdesc
                );
            }

        }

        \WP_CLI\Utils\format_items('table', $display, array('id', 'type', 'title', 'description'));
    }
}

class C_NGG_WPCLI_Gallery
{
    /**
     * Create a new gallery
     * @param array $args
     * @param array $assoc_args
     * @synopsis <gallery_name> [--description=<description>] --author=<user_login>
     */
    function create($args, $assoc_args)
    {
        $mapper = C_Gallery_Mapper::get_instance();
        $user = get_user_by('login', $assoc_args['author']);
        if (!$user)
            WP_CLI::error("Unable to find user {$assoc_args['author']}");

        $description = !empty($assoc_args['description']) ? $assoc_args['description'] : '';

        $gallery = $mapper->create(array(
            'title'   => $args[0],
            'galdesc' => $description,
            'author'  => $user->ID,
        ));

        if ($gallery && $gallery->save())
        {
            $gallery_id = $retval = $gallery->id();
            WP_CLI::success("Created gallery with id #{$gallery_id}");
        }
        else {
            WP_CLI::error("Unable to create gallery");
        }
    }

    /**
     * Deletes the requested gallery
     * @param $args
     * @param $assoc_args
     * @synopsis <gallery_id> [--delete-files]
     */
    function delete($args, $assoc_args)
    {
        $mapper = C_Gallery_Mapper::get_instance();
        $gallery = $mapper->find($args[0]);
        if (!$gallery)
            WP_CLI::error("Unable to find gallery {$args[0]}");

        $remove_files = !empty($assoc_args['delete-files']) ? TRUE : FALSE;

        $mapper->destroy($gallery, $remove_files);
        WP_CLI::success("Gallery with id #{$gallery->gid} has been deleted");
    }

    /**
     * Change gallery attributes
     * @param $args
     * @param $assoc_args
     * @synopsis <gallery_id> [--description=<description>] [--title=<title>]
     */
    function edit($args, $assoc_args)
    {
        $mapper = C_Gallery_Mapper::get_instance();
        $gallery = $mapper->find($args[0]);
        if (!$gallery)
            WP_CLI::error("Unable to find gallery {$args[0]}");

        if (empty($assoc_args['description'] && empty($assoc_args['title'])))
            WP_CLI::error("You must provide a new description or title");

        if (!empty($assoc_args['description']))
            $gallery->galdesc = $assoc_args['description'];
        if (!empty($assoc_args['title']))
            $gallery->name = $assoc_args['title'];

        $mapper->save($gallery);
        WP_CLI::success("Gallery with id #{$gallery->gid} has been modified");
    }

    /**
     * @param array $args
     * @param array $assoc_args
     * @subcommand list
     */
    function _list($args, $assoc_args)
    {
        $mapper = C_Gallery_Mapper::get_instance();
        $display = array();
        foreach ($mapper->find_all() as $gallery) {
            $display[] = array(
                'id' => $gallery->gid,
                'title' => $gallery->name,
                'path' => $gallery->path,
                'description' => $gallery->galdesc
            );
        }

        \WP_CLI\Utils\format_items('table', $display, array('id', 'title', 'path', 'description'));
    }

    /**
     * @param $args
     * @param $assoc_args
     * @synopsis <gallery_id>
     */
    function list_children($args, $assoc_args)
    {
        $gallery_mapper = C_Gallery_Mapper::get_instance();
        $image_mapper   = C_Image_Mapper::get_instance();

        $gallery = $gallery_mapper->find($args[0]);
        if (!$gallery)
            WP_CLI::error("Unable to find gallery {$args[0]}");

        $images = $image_mapper->select()->where(array('galleryid = %s', $gallery->gid))->run_query();
        $display = array();

        foreach ($images as $image) {
            $display[] = array(
                'id'          => $image->pid,
                'title'       => $image->alttext,
                'excluded'    => $image->exclude == 0 ? 'no' : 'yes',
                'sort order'  => $image->sortorder,
                'description' => $image->description
            );
        }

        \WP_CLI\Utils\format_items('table', $display, array('id', 'title', 'excluded', 'sort order', 'description'));

    }
}

class C_NGG_WPCLI_Image
{
    /**
     * Import an image from the filesystem into NextGen
     * @param array $args
     * @param array $assoc_args
     * @synopsis --filename=<absolute-path> --gallery=<gallery-id>
     */
    function import($args, $assoc_args)
    {
        $mapper = C_Gallery_Mapper::get_instance();
        $storage = C_Gallery_Storage::get_instance();

        if (($gallery = $mapper->find($assoc_args['gallery'], TRUE)))
        {
            $file_data = @file_get_contents($assoc_args['filename']);
            $file_name = M_I18n::mb_basename($assoc_args['filename']);

            if (empty($file_data))
                WP_CLI::error('Could not load file');


            $image_id = $storage->upload_base64_image($gallery, $file_data, $file_name);

            if (!$image_id)
                WP_CLI::error('Could not import image');
            else
                WP_CLI::success("Imported image with id #{$image_id}");
        }
        else {
            WP_CLI::error("Gallery not found (with id #{$assoc_args['gallery']}");
        }
    }

    /**
     * Change image attributes
     * @param $args
     * @param $assoc_args
     * @synopsis <image_id> [--description=<description>] [--title=<title>]
     */
    function edit($args, $assoc_args)
    {
        $mapper = C_Image_Mapper::get_instance();
        $image = $mapper->find($args[0]);
        if (!$image)
            WP_CLI::error("Unable to find image {$args[0]}");

        if (empty($assoc_args['description']) && empty($assoc_args['title']))
            WP_CLI::error("You must provide a new description or title");

        if (!empty($assoc_args['description']))
            $image->description = $assoc_args['description'];
        if (!empty($assoc_args['title']))
            $image->alttext = $assoc_args['title'];

        $mapper->save($image);
        WP_CLI::success("Image with id #{$image->pid} has been modified");
    }
}

class C_NGG_WPCLI_Settings
{
    /**
     * @param array $args
     * @param array $assoc_args
     * @subcommand list
     */
    function _list($args, $assoc_args)
    {
        $settings = C_NextGen_Settings::get_instance();
        $temporary = $settings->to_array();
        $display = array();
        foreach ($temporary as $key => $val) {
            $display[] = array(
                'key'   => $key,
                'value' => $val
            );
        }
        \WP_CLI\Utils\format_items('table', $display, array('key', 'value'));
    }

    /**
     * @param array $args
     * @param array $assoc_args
     * @synopsis <key> <value>
     */
    function edit($args, $assoc_args)
    {
        $settings = C_NextGen_Settings::get_instance();
        $settings->set($args[0], $args[1]);
        $settings->save();
        WP_CLI::success("Setting has been updated");
    }

    /**
     * Export all NextGen settings to a file in JSON format
     * @param $args
     * @param $assoc_args
     * @synopsis <json-file-path>
     */
    function export($args, $assoc_args)
    {
        $settings = C_NextGen_Settings::get_instance();
        file_put_contents($args[0], $settings->to_json());
        WP_CLI::success("Settings have been stored in {$args[0]}");
    }

    /**
     * Import settings from a JSON file
     * @param array $args
     * @param array $assoc_args
     * @synopsis <json-file-path>
     */
    function import($args, $assoc_args)
    {
        $settings = C_NextGen_Settings::get_instance();
        $file_content = file_get_contents($args[0]);
        $json = json_decode($file_content);

        if ($json === NULL)
            WP_CLI::error("Could not parse JSON file");

        foreach ($json as $key => $value) {
            $settings->set($key, $value);
        }

        $settings->save();

        WP_CLI::success("Settings have been imported from {$args[0]}");
    }

    /**
     * Deactivates NextGen and NextGen Pro and resets all settings to their default state
     * @param array $args
     * @param array $assoc_args
     */
    function reset($args, $assoc_args)
    {
        WP_CLI::confirm("Are you sure you want to reset all NextGen settings?", $assoc_args);

        $settings = C_NextGen_Settings::get_instance();
        C_Photocrati_Transient_Manager::flush();

        if (defined('NGG_PRO_PLUGIN_VERSION') || defined('NEXTGEN_GALLERY_PRO_VERSION'))
            C_Photocrati_Installer::uninstall('photocrati-nextgen-pro');
        if (defined('NGG_PLUS_PLUGIN_VERSION'))
            C_Photocrati_Installer::uninstall('photocrati-nextgen-plus');
        C_Photocrati_Installer::uninstall('photocrati-nextgen');

        // removes all ngg_options entry in wp_options
        $settings->reset();
        $settings->destroy();

        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->posts} WHERE post_type = %s", 'display_type'));
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->posts} WHERE post_type = %s", 'lightbox_library'));

        WP_CLI::success("All NextGen settings have been reset");
    }
}

class C_NGG_WPCLI_Notifications
{
    /**
     * Clear all dismissed notifications handled by C_Admin_Notification_Manager
     * @param array $args
     * @param array $assoc_args
     * @synopsis
     */
    function clear_dismissed($args, $assoc_args)
    {
        $settings = C_NextGen_Settings::get_instance();
        $settings->set('dismissed_notifications', array());
        $settings->set('gallery_created_after_reviews_introduced', FALSE);
        $settings->save();
    }
}

WP_CLI::add_command('ngg',               'C_NGG_WPCLI');
WP_CLI::add_command('ngg album',         'C_NGG_WPCLI_Album');
WP_CLI::add_command('ngg cache',         'C_NGG_WPCLI_Cache');
WP_CLI::add_command('ngg gallery',       'C_NGG_WPCLI_Gallery');
WP_CLI::add_command('ngg image',         'C_NGG_WPCLI_Image');
WP_CLI::add_command('ngg notifications', 'C_NGG_WPCLI_Notifications');
WP_CLI::add_command('ngg settings',      'C_NGG_WPCLI_Settings');
