<?php

namespace Kunnu\Dropbox\Models;

class ModelFactory {

    /**
     * Make a Model Factory
     *
     * @param  array  $data Model Data
     *
     * @return \Kunnu\Dropbox\Models\ModelInterface
     */
    public static function make(array $data = array()) {
        if (isset($data['.tag']) && isset($data['id'])) {
            $tag = $data['.tag'];

            //File
            if ($tag === 'file') {
                if (isset($data['url'])) {
                    return new FileLinkMetadata($data);
                }

                return new FileMetadata($data);
            }

            //Folder
            if ($tag === 'folder') {
                if (isset($data['url'])) {
                    return new FolderLinkMetadata($data);
                }
                return new FolderMetadata($data);
            }
        }

        //Temporary Link
        if (isset($data['metadata']) && isset($data['link'])) {
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
        if (isset($data['.tag']) && $data['.tag'] === 'success') {

            if (isset($data['success'])) {
                return self::make($data['success']);
            } elseif (isset($data['metadata'])) {
                return self::make($data['metadata']);
            }
        }
        //
        //Simple Tag response
        if (isset($data['.tag']) && count($data) === 1) {
            return new Tag($data);
        }

        //Base Model
        return new BaseModel($data);
    }

}
