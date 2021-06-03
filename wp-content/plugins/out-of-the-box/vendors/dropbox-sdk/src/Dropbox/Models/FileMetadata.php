<?php

namespace TheLion\OutoftheBox\API\Dropbox\Models;

class FileMetadata extends BaseModel {

    /**
     * A unique identifier of the file
     *
     * @var string
     */
    protected $id;

    /**
     * The last component of the path (including extension).
     *
     * @var string
     */
    protected $name;

    /**
     * A unique identifier for the current revision of a file.
     * This field is the same rev as elsewhere in the API and
     * can be used to detect changes and avoid conflicts.
     *
     * @var string
     */
    protected $rev;

    /**
     * The file size in bytes.
     *
     * @var int
     */
    protected $size;

    /**
     * The lowercased full path in the user's Dropbox.
     *
     * @var string
     */
    protected $path_lower;

    /**
     * Additional information if the file is a photo or video.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Models\MediaInfo
     */
    protected $media_info;

    /**
     * Set if this file is contained in a shared folder.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Models\FileSharingInfo
     */
    protected $sharing_info;

    /**
     * The cased path to be used for display purposes only.
     *
     * @var string
     */
    protected $path_display;

    /**
     * For files, this is the modification time set by the
     * desktop client when the file was added to Dropbox.
     *
     * @var DateTime
     */
    protected $client_modified;

    /**
     * The last time the file was modified on Dropbox.
     *
     * @var DateTime
     */
    protected $server_modified;

    /**
     * This flag will only be present if
     * include_has_explicit_shared_members is true in
     * list_folder or get_metadata. If this flag is present,
     * it will be true if this file has any explicit shared
     * members. This is different from sharing_info in that
     * this could be true in the case where a file has explicit
     * members but is not contained within a shared folder.
     *
     * @var bool
     */
    protected $has_explicit_shared_members;

    /**
     * If true, file can be downloaded directly; else the file must be exported. The default for this field is True.
     *
     * @var boolean
     */
    protected $is_downloadable;

    /**
     * Information about format this file can be exported to. This filed must be set if is_downloadable is set to false. This field is optional.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Models\ExportInfo
     */
    protected $export_info;

    /**
     * Create a new FileMetadata instance
     *
     * @param array $data
     */
    public function __construct(array $data) {
        parent::__construct($data);
        $this->id = $this->getDataProperty('id');
        $this->rev = $this->getDataProperty('rev');
        $this->name = $this->getDataProperty('name');
        $this->size = $this->getDataProperty('size');
        $this->path_lower = $this->getDataProperty('path_lower');
        $this->path_display = $this->getDataProperty('path_display');
        $this->client_modified = $this->getDataProperty('client_modified');
        $this->server_modified = $this->getDataProperty('server_modified');
        $this->has_explicit_shared_members = $this->getDataProperty('has_explicit_shared_members');
        $this->is_downloadable = $this->getDataProperty('is_downloadable');

        //Make MediaInfo
        $mediaInfo = $this->getDataProperty('media_info');
        if (is_array($mediaInfo)) {
            $this->media_info = new MediaInfo($mediaInfo);
        }

        //Make SharingInfo
        $sharingInfo = $this->getDataProperty('sharing_info');
        if (is_array($sharingInfo)) {
            $this->sharing_info = new FileSharingInfo($sharingInfo);
        }

        //Make ExportInfo
        $exportInfo = $this->getDataProperty('export_info');
        if (is_array($exportInfo)) {
            $this->export_info = new ExportInfo($exportInfo);
        }
    }

    /**
     * Get the 'id' property of the file model.
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the 'name' property of the file model.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the 'rev' property of the file model.
     *
     * @return string
     */
    public function getRev() {
        return $this->rev;
    }

    /**
     * Get the 'size' property of the file model.
     *
     * @return int
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Get the 'path_lower' property of the file model.
     *
     * @return string
     */
    public function getPathLower() {
        return $this->path_lower;
    }

    /**
     * Get the 'media_info' property of the file model.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\MediaInfo
     */
    public function getMediaInfo() {
        return $this->media_info;
    }

    /**
     * Get the 'sharing_info' property of the file model.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\FileSharingInfo
     */
    public function getSharingInfo() {
        return $this->sharing_info;
    }

    /**
     * Get the 'path_display' property of the file model.
     *
     * @return string
     */
    public function getPathDisplay() {
        return $this->path_display;
    }

    /**
     * Get the 'client_modified' property of the file model.
     *
     * @return DateTime
     */
    public function getClientModified() {
        return $this->client_modified;
    }

    /**
     * Get the 'server_modified' property of the file model.
     *
     * @return DateTime
     */
    public function getServerModified() {
        return $this->server_modified;
    }

    /**
     * Get the 'has_explicit_shared_members' property of the file model.
     *
     * @return bool
     */
    public function hasExplicitSharedMembers() {
        return $this->has_explicit_shared_members;
    }

    /**
     * Get the 'is_downloadable' property of the file model.
     *
     * @return string
     */
    public function getIsDownloadable() {
        return $this->is_downloadable;
    }

    /**
     * Get the 'export' property of the file model.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\ExportInfo
     */
    public function getExportInfo() {
        return $this->export_info;
    }

}
