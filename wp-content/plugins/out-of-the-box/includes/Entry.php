<?php

namespace TheLion\OutoftheBox;

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
        $previewsupport = ['pdf', 'txt', 'ai', 'eps', 'odp', 'odt', 'doc', 'docx', 'docm', 'ppt', 'pps', 'ppsx', 'ppsm', 'pptx', 'pptm', 'xls', 'xlsx', 'xlsm', 'rtf', 'jpg', 'jpeg', 'gif', 'png', 'mp4', 'm4v', 'ogg', 'ogv', 'webmv', 'mp3', 'm4a', 'ogg', 'oga', 'wav', 'paper', 'gdoc', 'gslides', 'gsheet'];
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
                $can_always_create_thumbnail
                || ($this->is_file() && isset($api_entry->media_info) && null !== $api_entry->getMediaInfo())
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
