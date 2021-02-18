<?php

namespace Kunnu\Dropbox\Models;

class SearchResult extends BaseModel
{
    /**
     * Indicates what type of match was found for the result.
     *
     * @var string
     */
    protected $matchType;

    /**
     * File\Folder Metadata.
     *
     * @var \Kunnu\Dropbox\Models\FileMetadata|\Kunnu\Dropbox\Models\FolderMetadata
     */
    protected $metadata;

    /**
     * Create a new SearchResult instance.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $matchType = $this->getDataProperty('match_type');
        $this->matchType = isset($matchType['.tag']) ? $matchType['.tag'] : null;
        $this->setMetadata();
    }

    /**
     * Indicates what type of match was found for the result.
     *
     * @return bool
     */
    public function getMatchType()
    {
        return $this->matchType;
    }

    /**
     * Get the Search Result Metadata.
     *
     * @return \Kunnu\Dropbox\Models\FileMetadata|\Kunnu\Dropbox\Models\FolderMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set Metadata.
     */
    protected function setMetadata()
    {
        $metadata = $this->getDataProperty('metadata');

        if (is_array($metadata)) {
            $this->metadata = ModelFactory::make($metadata);
        }
    }
}
