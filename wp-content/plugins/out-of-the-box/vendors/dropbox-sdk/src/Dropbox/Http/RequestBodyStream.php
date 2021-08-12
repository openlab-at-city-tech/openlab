<?php
namespace TheLion\OutoftheBox\API\Dropbox\Http;

use TheLion\OutoftheBox\API\Dropbox\DropboxFile;

/**
 * RequestBodyStream
 */
class RequestBodyStream implements RequestBodyInterface
{

    /**
     * File to be sent with the Request
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\DropboxFile
     */
    protected $file;

    /**
     * Create a new RequestBodyStream instance
     *
     * @param \TheLion\OutoftheBox\API\Dropbox\DropboxFile $file
     */
    public function __construct(DropboxFile $file)
    {
        $this->file = $file;
    }

    /**
     * Get the Body of the Request
     *
     * @return resource
     */
    public function getBody()
    {
        return $this->file->getContents();
    }
}
