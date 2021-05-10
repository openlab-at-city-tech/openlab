<?php
namespace TheLion\OutoftheBox\API\Dropbox\Models;

class File extends BaseModel
{

    /**
     * The file contents
     *
     * @var string
     */
    protected $contents;

    /**
     * File Metadata
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata
     */
    protected $metadata;


    /**
     * Create a new File instance
     *
     * @param array  $data
     * @param string $contents
     */
    public function __construct(array $data, $contents)
    {
        parent::__construct($data);
        $this->contents = $contents;
        $this->metadata = new FileMetadata($data);
    }

    /**
     * The metadata for the file
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Get the file contents
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }
}
