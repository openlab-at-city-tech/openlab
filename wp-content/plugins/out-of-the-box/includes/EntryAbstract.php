<?php

namespace TheLion\OutoftheBox;

abstract class EntryAbstract
{
    public $id;
    public $name;
    public $basename;
    public $rev;
    public $path;
    public $path_display;
    public $children;
    public $parent;
    public $extension;
    public $mimetype;
    public $is_dir = false;
    public $size;
    public $description;
    public $last_edited;
    public $trashed = false;
    public $preview_link;
    public $download_link;
    public $direct_download_link;
    public $shared_links;
    public $save_as = [];
    public $can_preview_by_cloud = false;
    public $can_edit_by_cloud = false;
    public $permissions = [
        'canpreview' => false,
        'candelete' => false,
        'canadd' => false,
        'canrename' => false,
        'canmove' => false,
    ];
    public $thumbnails = [
    ];
    public $has_own_thumbnail = false;
    public $icon = false;
    public $backup_icon;
    public $media;
    public $additional_data = [];
    // Parent folder, only used for displaying the Previous Folder entry
    public $pf = false;
    public $has_access = true;

    public function __construct($api_entry = null)
    {
        if (null !== $api_entry) {
            $this->convert_api_entry($api_entry);
        }

        $this->backup_icon = $this->get_default_icon();
    }

    abstract public function convert_api_entry($entry);

    public function to_array()
    {
        $entry = (array) $this;

        // Remove Unused data
        unset($entry['parent'], $entry['mimetype'], $entry['direct_download_link'], $entry['additional_data']);
        //unset($entry['trashed']);

        // Update id to make sure that it can be used in DOM if needed
        $entry['id'] = urlencode($entry['id']);

        // Update size
        $entry['size'] = ($entry['size'] > 0) ? $entry['size'] : '';

        // Add datetime string for browser that doen't support toLocaleDateString
        $entry['last_edited_str'] = get_date_from_gmt(date('Y-m-d H:i:s', $entry['last_edited']), get_option('date_format').' '.get_option('time_format'));
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        return $this->id = $id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        return $this->name = $name;
    }

    public function get_basename()
    {
        return $this->basename;
    }

    public function set_basename($basename)
    {
        return $this->basename = $basename;
    }

    public function get_rev()
    {
        return $this->rev;
    }

    public function set_rev($rev)
    {
        return $this->rev = $rev;
    }

    public function get_path()
    {
        return $this->path;
    }

    public function set_path($path)
    {
        return $this->path = $path;
    }

    public function get_path_display()
    {
        return $this->path_display;
    }

    public function set_path_display($path_display)
    {
        return $this->path_display = $path_display;
    }

    /**
     * @return \TheLion\OutoftheBox\Entry[]
     */
    public function get_children()
    {
        return $this->children;
    }

    public function set_children($children)
    {
        return $this->children = $children;
    }

    public function has_children()
    {
        return count($this->children) > 0;
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public function set_parent($parent)
    {
        return $this->parent = $parent;
    }

    public function has_parent()
    {
        return '' !== $this->parent && '/' !== $this->parent;
    }

    public function get_extension()
    {
        return $this->extension;
    }

    public function set_extension($extension)
    {
        return $this->extension = $extension;
    }

    public function get_mimetype()
    {
        return $this->mimetype;
    }

    public function set_mimetype($mimetype)
    {
        return $this->mimetype = $mimetype;
    }

    public function get_is_dir()
    {
        return $this->is_dir;
    }

    public function is_dir()
    {
        return $this->is_dir;
    }

    public function is_file()
    {
        return !$this->is_dir;
    }

    public function set_is_dir($is_dir)
    {
        return $this->is_dir = (bool) $is_dir;
    }

    public function get_size()
    {
        return $this->size;
    }

    public function set_size($size)
    {
        return $this->size = (int) $size;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function set_description($description)
    {
        return $this->description = $description;
    }

    public function get_last_edited()
    {
        return $this->last_edited;
    }

    public function get_last_edited_str()
    {
        // Add datetime string for browser that doen't support toLocaleDateString
        $last_edited = $this->get_last_edited();
        if (empty($last_edited)) {
            return '';
        }

        $localtime = get_date_from_gmt(date('Y-m-d H:i:s', $last_edited));

        return date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($localtime));
    }

    public function set_last_edited($last_edited)
    {
        return $this->last_edited = $last_edited;
    }

    public function get_trashed()
    {
        return $this->trashed;
    }

    public function set_trashed($trashed = true)
    {
        return $this->trashed = $trashed;
    }

    public function get_preview_link()
    {
        return $this->preview_link;
    }

    public function set_preview_link($preview_link)
    {
        return $this->preview_link = $preview_link;
    }

    public function get_download_link()
    {
        return $this->download_link;
    }

    public function set_download_link($download_link)
    {
        return $this->download_link = $download_link;
    }

    public function get_direct_download_link()
    {
        return $this->direct_download_link;
    }

    public function set_direct_download_link($direct_download_link)
    {
        return $this->direct_download_link = $direct_download_link;
    }

    public function get_shared_links()
    {
        return $this->shared_links;
    }

    public function set_shared_link_by_visibility($url, $visibility = 'public', $expires = false, $shortened = false)
    {
        return $this->shared_links[$visibility] = [
            'url' => $url,
            'shortened' => $shortened,
            'expires' => $expires,
        ];
    }

    public function get_shared_link_by_visibility($visibility = 'public', $shortened = false)
    {
        if (!isset($this->shared_links[$visibility])) {
            return null;
        }

        if (
                !empty($this->shared_links[$visibility]['expires'])
                && $this->shared_links[$visibility]['expires'] < time()
        ) {
            return null;
        }

        if (false !== $shortened) {
            if (empty($this->shared_links[$visibility]['shortend'])) {
                return null;
            }

            return $this->shared_links[$visibility]['shortend'];
        }

        return $this->shared_links[$visibility]['url'];
    }

    public function get_save_as()
    {
        return $this->save_as;
    }

    public function set_save_as($save_as)
    {
        return $this->save_as = $save_as;
    }

    public function get_can_preview_by_cloud()
    {
        return $this->can_preview_by_cloud;
    }

    public function set_can_preview_by_cloud($can_preview_by_cloud)
    {
        return $this->can_preview_by_cloud = $can_preview_by_cloud;
    }

    public function get_can_edit_by_cloud()
    {
        return $this->can_edit_by_cloud;
    }

    public function set_can_edit_by_cloud($can_edit_by_cloud)
    {
        return $this->can_edit_by_cloud = $can_edit_by_cloud;
    }

    public function get_permission($permission = null)
    {
        if (!isset($this->permissions[$permission])) {
            return null;
        }

        return $this->permissions[$permission];
    }

    public function get_permissions()
    {
        return $this->permissions;
    }

    public function set_permissions($permissions)
    {
        return $this->permissions = $permissions;
    }

    public function get_thumbnail($key)
    {
        if (!isset($this->thumbnails[$key])) {
            return null;
        }

        return $this->thumbnails[$key];
    }

    public function set_thumbnail($key, $url)
    {
        return $this->thumbnails[$key] = $url;
    }

    public function has_own_thumbnail()
    {
        return $this->has_own_thumbnail;
    }

    public function set_has_own_thumbnail($value = true)
    {
        return $this->has_own_thumbnail = $value;
    }

    public function get_icon()
    {
        if (empty($this->icon)) {
            return $this->get_default_thumbnail_icon();
        }

        return $this->icon;
    }

    public function set_icon($icon)
    {
        return $this->icon = $icon;
    }

    public function get_media($key = null)
    {
        if (empty($key)) {
            return $this->media;
        }

        if (!isset($this->media[$key])) {
            return null;
        }

        return $this->media[$key];
    }

    public function set_media($media)
    {
        return $this->media = $media;
    }

    public function get_additional_data()
    {
        return $this->additional_data;
    }

    public function get_additional_data_by_key($key)
    {
        if (isset($this->additional_data[$key])) {
            return $this->additional_data;
        }

        return null;
    }

    public function set_additional_data($additional_data)
    {
        return $this->additional_data = $additional_data;
    }

    public function set_additional_data_by_key($key, $data)
    {
        return $this->additional_data[$key] = $data;
    }

    public function is_parent_folder()
    {
        return $this->pf;
    }

    public function set_parent_folder($value)
    {
        return $this->pf = (bool) $value;
    }

    public function get_default_icon()
    {
        return Helpers::get_default_icon($this->get_mimetype(), $this->is_dir());
    }

    /**
     * Get the value of has_access.
     */
    public function has_access()
    {
        return $this->has_access;
    }

    /**
     * Set the value of has_access.
     *
     * @param bool $has_access
     *
     * @return self
     */
    public function set_has_access($has_access = true)
    {
        $this->has_access = $has_access;

        return $this;
    }
}
