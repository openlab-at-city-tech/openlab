<?php

namespace TheLion\OutoftheBox;

interface EntryInterface
{
    public function convert_api_entry($api_entry);

    public function to_array();

    public function get_id();

    public function set_id($id);

    public function get_name();

    public function set_name($name);

    public function get_basename();

    public function set_basename($basename);

    public function get_path();

    public function set_path($path);

    public function get_path_display();

    public function set_path_display($path_display);

    public function get_parent();

    public function set_parent($parent);

    public function get_children();

    public function set_children($children);

    public function get_extension();

    public function set_extension($extension);

    public function get_mimetype();

    public function set_mimetype($mimetype);

    public function get_is_dir();

    public function set_is_dir($is_dir);

    public function get_size();

    public function set_size($size);

    public function get_description();

    public function set_description($description);

    public function get_last_edited();

    public function get_last_edited_str();

    public function set_last_edited($last_edited);

    public function get_trashed();

    public function set_trashed($trashed);

    public function get_preview_link();

    public function set_preview_link($preview_link);

    public function get_download_link();

    public function set_download_link($download_link);

    public function get_direct_download_link();

    public function set_direct_download_link($direct_download_link);

    public function get_save_as();

    public function set_save_as($save_as);

    public function get_can_preview_by_cloud();

    public function set_can_preview_by_cloud($can_preview_by_cloud);

    public function get_can_edit_by_cloud();

    public function set_can_edit_by_cloud($can_edit_by_cloud);

    public function get_permissions();

    public function set_permissions($permissions);

    public function get_thumbnail($key);

    public function set_thumbnail($key, $url);

    public function has_own_thumbnail();

    public function set_has_own_thumbnail($value = true);

    public function get_icon();

    public function set_icon($icon);

    public function get_media();

    public function set_media($media);

    public function get_additional_data();

    public function set_additional_data($additional_data);

    public function get_default_icon();
}

abstract class EntryAbstract implements EntryInterface
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
                !empty($this->shared_links[$visibility]['expires']) &&
                $this->shared_links[$visibility]['expires'] < time()
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
}

class Entry extends EntryAbstract
{
    /**
     * @param \Kunnu\Dropbox\Models\FileMetadata|\Kunnu\Dropbox\Models\FolderMetadata $api_entry
     */
    public function convert_api_entry($api_entry)
    {
        // Normal Meta Data
        $this->set_id($api_entry->id);
        $this->set_rev($api_entry->rev);
        $this->set_name($api_entry->name);

        if ($api_entry instanceof \Kunnu\Dropbox\Models\FolderMetadata) {
            $this->set_is_dir(true);
        }

        $pathinfo = \TheLion\OutoftheBox\Helpers::get_pathinfo($api_entry->path_lower);
        if ($this->is_file() && isset($pathinfo['extension'])) {
            $this->set_extension(strtolower($pathinfo['extension']));
        }
        $this->set_mimetype_from_extension();

        if ($this->is_file()) {
            $this->set_basename(str_replace('.'.$this->get_extension(), '', $this->get_name()));
        } else {
            $this->set_basename($this->get_name());
        }

        $this->set_size(($this->is_dir()) ? 0 : $api_entry->size);

        if ($this->is_file() && is_string($api_entry->server_modified)) {
            $dtime = \DateTime::createFromFormat('Y-m-d\\TH:i:s\\Z', $api_entry->server_modified, new \DateTimeZone('UTC'));
            $this->set_last_edited($dtime->getTimestamp());
        }

        $this->set_path($api_entry->path_lower);
        $this->set_path_display($api_entry->path_display);

        if ('' !== $api_entry->path_lower) {
            $this->set_parent($pathinfo['dirname']);
        }

        // Set Export formats
        if ($this->is_file() && null !== $api_entry->export_info) {
            $this->set_save_as($api_entry->getExportInfo()->getExportAs());
        }

        /* Can File be previewed via Dropbox?
         * https://www.dropbox.com/developers/core/docs#thumbnails
         */
        $previewsupport = ['pdf', 'txt', 'ai', 'eps', 'odp', 'odt', 'doc', 'docx', 'docm', 'ppt', 'pps', 'ppsx', 'ppsm', 'pptx', 'pptm', 'xls', 'xlsx', 'xlsm', 'rtf', 'jpg', 'jpeg', 'gif', 'png', 'mp4', 'm4v', 'ogg', 'ogv', 'webmv', 'mp3', 'm4a', 'ogg', 'oga', 'wav', 'paper'];
        $openwithdropbox = (in_array($this->get_extension(), $previewsupport));
        if ($openwithdropbox) {
            $this->set_can_preview_by_cloud(true);
        }

        $sharing_info = $api_entry->getSharingInfo();
        // Set the permissions
        $permissions = [
            'canpreview' => $openwithdropbox,
            'candownload' => true,
            'candelete' => (empty($sharing_info) ? true : !$sharing_info->isReadOnly()),
            'canadd' => (empty($sharing_info) ? true : !$sharing_info->isReadOnly()),
            'canrename' => (empty($sharing_info) ? true : !$sharing_info->isReadOnly()),
            'canmove' => (empty($sharing_info) ? true : !$sharing_info->isReadOnly()),
            'canshare' => true,
        ];

        $this->set_permissions($permissions);

        // Icon
        $default_icon = $this->get_default_icon();
        $this->set_icon($default_icon);

        // Thumbnail
        $can_always_create_thumbnail_extensions = ['csv', 'doc', 'docm', 'docx', 'ods', 'odt', 'pdf', 'rtf', 'xls', 'xlsm', 'xlsx', 'odp', 'pps', 'ppsm', 'ppsx', 'ppt', 'pptm', 'pptx', '3fr', 'ai', 'arw', 'bmp', 'cr2', 'crw', 'dcs', 'dcr', 'dng', 'eps', 'erf', 'gif', 'heic', 'jpg', 'jpeg', 'kdc', 'mef', 'mos', 'mrw', 'nef', 'nrw', 'orf', 'pef', 'png', 'psd', 'r3d', 'raf', 'rw2', 'rwl', 'sketch', 'sr2', 'svg', 'svgz', 'tif', 'tiff', 'x3f', '3gp', '3gpp', '3gpp2', 'asf', 'avi', 'dv', 'flv', 'm2t', 'm4v', 'mkv', 'mov', 'mp4', 'mpeg', 'mpg', 'mts', 'oggtheora', 'ogv', 'rm', 'ts', 'vob', 'webm', 'wmv', 'paper'];
        $can_always_create_thumbnail = (in_array($this->get_extension(), $can_always_create_thumbnail_extensions));

        $mediadata = [];

        if (
                $can_always_create_thumbnail ||
                ($this->is_file() && isset($api_entry->media_info) && null !== $api_entry->getMediaInfo())
        ) {
            $this->set_has_own_thumbnail(true);
        }

        $additional_data = [];
        $this->set_additional_data($additional_data);
    }

    public function set_mimetype_from_extension()
    {
        if ($this->is_dir()) {
            return null;
        }

        if (empty($this->extension)) {
            return null;
        }

        $mimetype = Helpers::get_mimetype($this->get_extension());
        $this->set_mimetype($mimetype);
    }

    public function get_icon_large()
    {
        return str_replace('32x32', '128x128', $this->get_icon());
    }

    public function get_icon_retina()
    {
        return str_replace('32x32', '256x256', $this->get_icon());
    }
}
