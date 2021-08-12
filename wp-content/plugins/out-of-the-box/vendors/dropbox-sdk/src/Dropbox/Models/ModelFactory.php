<?php

namespace TheLion\OutoftheBox\API\Dropbox\Models;

class ModelFactory
{
    /**
     * Make a Model Factory.
     *
     * @param array $data Model Data
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\ModelInterface
     */
    public static function make(array $data = [])
    {
        if (isset($data['.tag'], $data['id'])) {
            $tag = $data['.tag'];

            //File
            if ('file' === $tag) {
                if (isset($data['url'])) {
                    return new FileLinkMetadata($data);
                }

                return new FileMetadata($data);
            }

            //Folder
            if ('folder' === $tag) {
                if (isset($data['url'])) {
                    return new FolderLinkMetadata($data);
                }

                return new FolderMetadata($data);
            }
        }

        //Temporary Link
        if (isset($data['metadata'], $data['link'])) {
            return new TemporaryLink($data);
        }

        //List
        if (isset($data['entries'])) {
            return new MetadataCollection($data);
        }

        if (isset($data['entries'])) {
            return new MetadataCollection($data);
        }

        //List
        if (isset($data['links'])) {
            return new MetadataCollection($data);
        }

        //Search Results
        if (isset($data['matches'])) {
            return new SearchResults($data);
        }

        // Async Job
        if (isset($data['async_job_id'])) {
            return new AsyncJob($data);
        }

        //Deleted File/Folder
        //if (!isset($data['.tag']) || !isset($data['id'])) {
        //    return new DeletedMetadata($data);
        //}
        //
        //Simple BatchV2 Result response
        if (isset($data['.tag']) && 'success' === $data['.tag']) {
            if (isset($data['success'])) {
                return self::make($data['success']);
            }
            if (isset($data['metadata'])) {
                return self::make($data['metadata']);
            }
        }
        //
        //Simple Tag response
        if (isset($data['.tag']) && 1 === count($data)) {
            return new Tag($data);
        }

        //Simple SearchV2 Result response
        if (isset($data['.tag']) && 'metadata' === $data['.tag']) {
            if (isset($data['metadata'])) {
                return self::make($data['metadata']);
            }
        }

        //Base Model
        return new BaseModel($data);
    }
}
