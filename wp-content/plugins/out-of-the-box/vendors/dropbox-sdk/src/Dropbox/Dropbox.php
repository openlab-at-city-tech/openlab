<?php

namespace TheLion\OutoftheBox\API\Dropbox;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use TheLion\OutoftheBox\API\Dropbox\Authentication\DropboxAuthHelper;
use TheLion\OutoftheBox\API\Dropbox\Authentication\OAuth2Client;
use TheLion\OutoftheBox\API\Dropbox\Exceptions\DropboxClientException;
use TheLion\OutoftheBox\API\Dropbox\Http\Clients\DropboxHttpClientFactory;
use TheLion\OutoftheBox\API\Dropbox\Models\Account;
use TheLion\OutoftheBox\API\Dropbox\Models\AccountList;
use TheLion\OutoftheBox\API\Dropbox\Models\AsyncJob;
use TheLion\OutoftheBox\API\Dropbox\Models\CopyReference;
use TheLion\OutoftheBox\API\Dropbox\Models\File;
use TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata;
use TheLion\OutoftheBox\API\Dropbox\Models\FolderMetadata;
use TheLion\OutoftheBox\API\Dropbox\Models\ModelCollection;
use TheLion\OutoftheBox\API\Dropbox\Models\ModelFactory;
use TheLion\OutoftheBox\API\Dropbox\Models\Tag;
use TheLion\OutoftheBox\API\Dropbox\Models\TemporaryLink;
use TheLion\OutoftheBox\API\Dropbox\Models\Thumbnail;
use TheLion\OutoftheBox\API\Dropbox\Security\RandomStringGeneratorFactory;
use TheLion\OutoftheBox\API\Dropbox\Store\PersistentDataStoreFactory;

/**
 * Dropbox.
 */
class Dropbox
{
    /**
     * Uploading a file with the 'uploadFile' method, with the file's
     * size less than this value (~8 MB), the simple `upload` method will be
     * used, if the file size exceed this value (~8 MB), the `startUploadSession`,
     * `appendUploadSession` & `finishUploadSession` methods will be used
     * to upload the file in chunks.
     *
     * @const int
     */
    const AUTO_CHUNKED_UPLOAD_THRESHOLD = 8000000;

    /**
     * The Chunk Size the file will be
     * split into and uploaded (~4 MB).
     *
     * @const int
     */
    const DEFAULT_CHUNK_SIZE = 4000000;

    /**
     * Response header containing file metadata.
     *
     * @const string
     */
    const METADATA_HEADER = 'dropbox-api-result';

    /**
     * The Dropbox App.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\DropboxApp
     */
    protected $app;

    /**
     * OAuth2 Access Token.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * Dropbox Client.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\DropboxClient
     */
    protected $client;

    /**
     * OAuth2 Client.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Authentication\OAuth2Client
     */
    protected $oAuth2Client;

    /**
     * Random String Generator.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Security\RandomStringGeneratorInterface
     */
    protected $randomStringGenerator;

    /**
     * Persistent Data Store.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Store\PersistentDataStoreInterface
     */
    protected $persistentDataStore;

    /**
     * Create a new Dropbox instance.
     *
     * @param \TheLion\OutoftheBox\API\Dropbox\DropboxApp
     * @param array $config Configuration Array
     */
    public function __construct(DropboxApp $app, array $config = [])
    {
        //Configuration
        $config = array_merge([
            'http_client_handler' => null,
            'random_string_generator' => null,
            'persistent_data_store' => null,
        ], $config);

        //Set the app
        $this->app = $app;

        //Set the access token
        $this->setAccessToken($app->getAccessToken());

        //Make the HTTP Client
        $httpClient = DropboxHttpClientFactory::make($config['http_client_handler']);

        //Make and Set the DropboxClient
        $this->client = new DropboxClient($httpClient);

        //Make and Set the Random String Generator
        $this->randomStringGenerator = RandomStringGeneratorFactory::makeRandomStringGenerator($config['random_string_generator']);

        //Make and Set the Persistent Data Store
        $this->persistentDataStore = PersistentDataStoreFactory::makePersistentDataStore($config['persistent_data_store']);
    }

    /**
     * Get the Client.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\DropboxClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get the Access Token.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\AccessToken|string Access Token
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Get the Dropbox App.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\DropboxApp Dropbox App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Get OAuth2Client.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Authentication\OAuth2Client
     */
    public function getOAuth2Client()
    {
        if (!$this->oAuth2Client instanceof OAuth2Client) {
            return new OAuth2Client(
                $this->getApp(),
                $this->getClient(),
                $this->getRandomStringGenerator()
            );
        }

        return $this->oAuth2Client;
    }

    /**
     * Get the Random String Generator.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Security\RandomStringGeneratorInterface
     */
    public function getRandomStringGenerator()
    {
        return $this->randomStringGenerator;
    }

    /**
     * Get Persistent Data Store.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Store\PersistentDataStoreInterface
     */
    public function getPersistentDataStore()
    {
        return $this->persistentDataStore;
    }

    /**
     * Get Dropbox Auth Helper.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Authentication\DropboxAuthHelper
     */
    public function getAuthHelper()
    {
        return new DropboxAuthHelper(
            $this->getOAuth2Client(),
            $this->getRandomStringGenerator(),
            $this->getPersistentDataStore()
        );
    }

    /**
     * Set the Access Token.
     *
     * @param \TheLion\OutoftheBox\API\Dropbox\Models\AccessToken $accessToken Access Token
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Dropbox Dropbox Client
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        $this->getApp()->setAccessToken($accessToken);

        return $this;
    }

    /**
     * Make Request to the API.
     *
     * @param string $method       HTTP Request Method
     * @param string $endpoint     API Endpoint to send Request to
     * @param string $endpointType Endpoint type ['api'|'content']
     * @param array  $params       Request Query Params
     * @param string $accessToken  Access Token to send with the Request
     *
     * @throws \TheLion\OutoftheBox\API\Dropbox\Exceptions\DropboxClientException
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\DropboxResponse
     */
    public function sendRequest($method, $endpoint, $endpointType = 'api', array $params = [], $accessToken = null)
    {
        //Access Token
        $accessToken = $this->getAccessToken() ? $this->getAccessToken()->getToken() : $accessToken;

        // Check if the token is set to expire in the next 120 seconds
        // (or has already expired).
        if ($this->getOAuth2Client()->isAccessTokenExpired()) {
            // Call Update function of plugin itself
            global $OutoftheBox;
            do_action('out-of-the-box-refresh-token', $OutoftheBox->get_processor()->get_current_account());
            $accessToken = $this->getAccessToken()->getToken();
        }

        //Make a DropboxRequest object
        $request = new DropboxRequest($method, $endpoint, $accessToken, $endpointType, $params);

        //Send Request through the DropboxClient
        //Fetch and return the Response
        return $this->getClient()->sendRequest($request);
    }

    /**
     * Make a HTTP POST Request to the API endpoint type.
     *
     * @param string $endpoint    API Endpoint to send Request to
     * @param array  $params      Request Query Params
     * @param string $accessToken Access Token to send with the Request
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\DropboxResponse
     */
    public function postToAPI($endpoint, array $params = [], $accessToken = null)
    {
        return $this->sendRequest('POST', $endpoint, 'api', $params, $accessToken);
    }

    /**
     * Make a HTTP POST Request to the Content endpoint type.
     *
     * @param string $endpoint    Content Endpoint to send Request to
     * @param array  $params      Request Query Params
     * @param string $accessToken Access Token to send with the Request
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\DropboxResponse
     */
    public function postToContent($endpoint, array $params = [], $accessToken = null)
    {
        return $this->sendRequest('POST', $endpoint, 'content', $params, $accessToken);
    }

    /**
     * Make Model from DropboxResponse.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\ModelInterface
     */
    public function makeModelFromResponse(DropboxResponse $response)
    {
        //Get the Decoded Body
        $body = $response->getDecodedBody();

        //Make and Return the Model
        return ModelFactory::make($body);
    }

    /**
     * Make DropboxFile Object.
     *
     * @param DropboxFile|string $dropboxFile DropboxFile object or Path to file
     * @param int                $maxLength   Max Bytes to read from the file
     * @param int                $offset      Seek to specified offset before reading
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\DropboxFile
     */
    public function makeDropboxFile($dropboxFile, $maxLength = -1, $offset = -1)
    {
        //Uploading file by file path
        if (!$dropboxFile instanceof DropboxFile) {
            //File is valid
            if (is_file($dropboxFile)) {
                //Create a DropboxFile Object
                $dropboxFile = new DropboxFile($dropboxFile, $maxLength, $offset);
            } else {
                //File invalid/doesn't exist
                throw new DropboxClientException("File '{$dropboxFile}' is invalid.");
            }
        }

        $dropboxFile->setOffset($offset);
        $dropboxFile->setMaxLength($maxLength);

        //Return the DropboxFile Object
        return $dropboxFile;
    }

    /**
     * Get the Metadata for a file or folder.
     *
     * @param string $path   Path of the file or folder
     * @param array  $params Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-get_metadata
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata|\TheLion\OutoftheBox\API\Dropbox\Models\FolderMetadata
     */
    public function getMetadata($path, array $params = [])
    {
        //Root folder is unsupported
        if ('/' === $path) {
            throw new DropboxClientException('Metadata for the root folder is unsupported.');
        }

        //Set the path
        $params['path'] = $path;

        //Get File Metadata
        $response = $this->postToAPI('/files/get_metadata', $params);

        //Make and Return the Model
        return $this->makeModelFromResponse($response);
    }

    /**
     * Get the contents of a Folder.
     *
     * @param string $path   Path to the folder. Defaults to root.
     * @param array  $params Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-list_folder
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\MetadataCollection
     */
    public function listFolder($path = null, array $params = [])
    {
        //Specify the root folder as an
        //empty string rather than as "/"
        if ('/' === $path) {
            $path = '';
        }

        //Set the path
        $params['path'] = $path;

        //Get File Metadata
        $response = $this->postToAPI('/files/list_folder', $params);

        //Make and Return the Model
        return $this->makeModelFromResponse($response);
    }

    /**
     * Paginate through all files and retrieve updates to the folder,
     * using the cursor retrieved from listFolder or listFolderContinue.
     *
     * @param string $cursor The cursor returned by your
     *                       last call to listFolder or listFolderContinue
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-list_folder-continue
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\MetadataCollection
     */
    public function listFolderContinue($cursor)
    {
        $response = $this->postToAPI('/files/list_folder/continue', ['cursor' => $cursor]);

        //Make and Return the Model
        return $this->makeModelFromResponse($response);
    }

    /**
     * Get a cursor for the folder's state.
     *
     * @param string $path   Path to the folder. Defaults to root.
     * @param array  $params Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-list_folder-get_latest_cursor
     *
     * @return string The Cursor for the folder's state
     */
    public function listFolderLatestCursor($path, array $params = [])
    {
        //Specify the root folder as an
        //empty string rather than as "/"
        if ('/' === $path) {
            $path = '';
        }

        //Set the path
        $params['path'] = $path;

        //Fetch the cursor
        $response = $this->postToAPI('/files/list_folder/get_latest_cursor', $params);

        //Retrieve the cursor
        $body = $response->getDecodedBody();
        $cursor = isset($body['cursor']) ? $body['cursor'] : false;

        //No cursor returned
        if (!$cursor) {
            throw new DropboxClientException('Could not retrieve cursor. Something went wrong.');
        }

        //Return the cursor
        return $cursor;
    }

    /**
     * Get Revisions of a File.
     *
     * @param string $path   Path to the file
     * @param array  $params Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-list_revisions
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\ModelCollection
     */
    public function listRevisions($path, array $params = [])
    {
        //Set the Path
        $params['path'] = $path;

        //Fetch the Revisions
        $response = $this->postToAPI('/files/list_revisions', $params);

        //The file metadata of the entries, returned by this
        //endpoint doesn't include a '.tag' attribute, which
        //is used by the ModelFactory to resolve the correct
        //model. But since we know that revisions returned
        //are file metadata objects, we can explicitly cast
        //them as \TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata manually.
        $body = $response->getDecodedBody();
        $entries = isset($body['entries']) ? $body['entries'] : [];
        $processedEntries = [];

        foreach ($entries as $entry) {
            $processedEntries[] = new FileMetadata($entry);
        }

        return new ModelCollection($processedEntries);
    }

    /**
     * Search a folder for files/folders.
     *
     * @param string $path   Path to search
     * @param string $query  Search Query
     * @param array  $params Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-search
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\SearchResults
     */
    public function search($path, $query, array $options = [])
    {
        //Specify the root folder as an
        //empty string rather than as "/"
        if ('/' === $path) {
            $path = '';
        }

        //Set the path and query
        $params['query'] = $query;
        $params['options'] = $options;
        $params['options']['path'] = $path;

        //Fetch Search Results
        $response = $this->postToAPI('/files/search_v2', $params);

        //Make and Return the Model
        return $this->makeModelFromResponse($response);
    }

    /**
     * Fetches the next page of search results returned from /search_v2.
     *
     * @param string $cursor The cursor returned by your last call to search
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation?oref=e#files-search-continue:2
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\SearchResults
     */
    public function search_continue($cursor)
    {
        //Fetch Search Results
        $response = $this->postToAPI('/files/search/continue_v2', ['cursor' => $cursor]);

        //Make and Return the Model
        return $this->makeModelFromResponse($response);
    }

    /**
     * Create a folder at the given path.
     *
     * @param string $path       Path to create
     * @param bool   $autorename Auto Rename File
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-create_folder
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\FolderMetadata
     */
    public function createFolder($path, $autorename = false)
    {
        //Path cannot be null
        if (is_null($path)) {
            throw new DropboxClientException('Path cannot be null.');
        }

        //Create Folder
        $response = $this->postToAPI('/files/create_folder', ['path' => $path, 'autorename' => $autorename]);

        //Fetch the Metadata
        $body = $response->getDecodedBody();

        //Make and Return the Model
        return new FolderMetadata($body);
    }

    /**
     * Delete a file or folder at the given path.
     *
     * @param string $path Path to file/folder to delete
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-delete
     *
     * @return FileMetadata|FolderMetadata|\TheLion\OutoftheBox\API\Dropbox\Models\DeletedMetadata
     */
    public function delete($path)
    {
        //Path cannot be null
        if (is_null($path)) {
            throw new DropboxClientException('Path cannot be null.');
        }

        //Delete
        $response = $this->postToAPI('/files/delete', ['path' => $path]);

        return $this->makeModelFromResponse($response);
    }

    /**
     * Delete a file or folder at the given path.
     *
     * @param array $entries Entries to be deleted
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-delete_batch
     *
     * @return FileMetadata|FolderMetadata|\TheLion\OutoftheBox\API\Dropbox\Models\DeletedMetadata
     */
    public function deleteBatch($entries)
    {
        //Path cannot be null
        if (is_null($entries)) {
            throw new DropboxClientException('Entries cannot be null.');
        }

        //Delete
        $response = $this->postToAPI('/files/delete_batch', ['entries' => $entries]);

        //Make and Return the Model
        return $this->waitForAsyncRequest($response, '/files/delete_batch/check');
    }

    /**
     * Move a file or folder to a different location.
     *
     * @param string $fromPath Path to be moved
     * @param string $toPath   Path to be moved to
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-move
     *
     * @return DeletedMetadata|FileMetadata|\TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata
     */
    public function move($fromPath, $toPath)
    {
        //From and To paths cannot be null
        if (is_null($fromPath) || is_null($toPath)) {
            throw new DropboxClientException('From and To paths cannot be null.');
        }

        //Response
        $response = $this->postToAPI('/files/move', ['from_path' => $fromPath, 'to_path' => $toPath]);

        //Make and Return the Model
        return $this->makeModelFromResponse($response);
    }

    /**
     * Move multiple files or folders to different locations at once.
     *
     * @param array $entries Entries to be moved
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-move_batch
     *
     * @return DeletedMetadata|FileMetadata|\TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata
     */
    public function moveBatch($entries)
    {
        if (is_null($entries)) {
            throw new DropboxClientException('From and To paths cannot be null.');
        }

        //Response
        $response = $this->postToAPI('/files/move_batch_v2', ['entries' => $entries]);

        return $this->waitForAsyncRequest($response, '/files/move_batch/check_v2');
    }

    public function waitForAsyncRequest($raw_response, $request_url, $async_job_id = null)
    {
        $response = $this->makeModelFromResponse($raw_response);

        if (!($response instanceof AsyncJob) && !($response instanceof Tag)) {
            return $response;
        }

        if ($response instanceof Tag && 'in_progress' !== $response->getTag()) {
            return $response;
        }

        if ($response instanceof AsyncJob && empty($async_job_id)) {
            $async_job_id = $response->getAsyncJobId();
        }

        usleep(1000000);
        $raw_response = $this->postToAPI($request_url, ['async_job_id' => $async_job_id]);

        return $this->waitForAsyncRequest($raw_response, $request_url, $async_job_id);
    }

    /**
     * Copy a file or folder to a different location.
     *
     * @param string $fromPath Path to be copied
     * @param string $toPath   Path to be copied to
     * @param mixed  $param
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-copy
     *
     * @return DeletedMetadata|FileMetadata|\TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata
     */
    public function copy($fromPath, $toPath, $param = [])
    {
        //From and To paths cannot be null
        if (is_null($fromPath) || is_null($toPath)) {
            throw new DropboxClientException('From and To paths cannot be null.');
        }

        $param['from_path'] = $fromPath;
        $param['to_path'] = $toPath;

        //Response
        $response = $this->postToAPI('/files/copy', $param);

        //Make and Return the Model
        return $this->makeModelFromResponse($response);
    }

    /**
     * Copy a file or folder to a different location.
     *
     * @param array $entries Entries to be copied
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-copy_batch
     *
     * @return DeletedMetadata|FileMetadata|\TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata
     */
    public function copyBatch($entries)
    {
        if (is_null($entries)) {
            throw new DropboxClientException('Entries cannot be null.');
        }

        //Response
        $response = $this->postToAPI('/files/copy_batch_v2', ['entries' => $entries]);

        //Make and Return the Model
        return $this->waitForAsyncRequest($response, '/files/copy_batch/check_v2');
    }

    /**
     * Restore a file to the specific version.
     *
     * @param string $path Path to the file to restore
     * @param string $rev  Revision to store for the file
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-restore
     *
     * @return FileMetadata|FolderMetadata|\TheLion\OutoftheBox\API\Dropbox\Models\DeletedMetadata
     */
    public function restore($path, $rev)
    {
        //Path and Revision cannot be null
        if (is_null($path) || is_null($rev)) {
            throw new DropboxClientException('Path and Revision cannot be null.');
        }

        //Response
        $response = $this->postToAPI('/files/restore', ['path' => $path, 'rev' => $rev]);

        //Fetch the Metadata
        $body = $response->getDecodedBody();

        //Make and Return the Model
        return new FileMetadata($body);
    }

    /**
     * Get Copy Reference.
     *
     * @param string $path Path to the file or folder to get a copy reference to
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-copy_reference-get
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\CopyReference
     */
    public function getCopyReference($path)
    {
        //Path cannot be null
        if (is_null($path)) {
            throw new DropboxClientException('Path cannot be null.');
        }

        //Get Copy Reference
        $response = $this->postToAPI('/files/copy_reference/get', ['path' => $path]);
        $body = $response->getDecodedBody();

        //Make and Return the Model
        return new CopyReference($body);
    }

    /**
     * Save Copy Reference.
     *
     * @param string $path          Path to the file or folder to get a copy reference to
     * @param string $copyReference Copy reference returned by getCopyReference
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-copy_reference-save
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata|\TheLion\OutoftheBox\API\Dropbox\Models\FolderMetadata
     */
    public function saveCopyReference($path, $copyReference)
    {
        //Path and Copy Reference cannot be null
        if (is_null($path) || is_null($copyReference)) {
            throw new DropboxClientException('Path and Copy Reference cannot be null.');
        }

        //Save Copy Reference
        $response = $this->postToAPI('/files/copy_reference/save', ['path' => $path, 'copy_reference' => $copyReference]);
        $body = $response->getDecodedBody();

        //Response doesn't have Metadata
        if (!isset($body['metadata']) || !is_array($body['metadata'])) {
            throw new DropboxClientException('Invalid Response.');
        }

        //Make and return the Model
        return ModelFactory::make($body['metadata']);
    }

    /**
     * Get a temporary link to stream contents of a file.
     *
     * @param string $path Path to the file you want a temporary link to
     *
     * https://www.dropbox.com/developers/documentation/http/documentation#files-get_temporary_link
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\TemporaryLink
     */
    public function getTemporaryLink($path)
    {
        //Path cannot be null
        if (is_null($path)) {
            throw new DropboxClientException('Path cannot be null.');
        }

        //Get Temporary Link
        $response = $this->postToAPI('/files/get_temporary_link', ['path' => $path]);

        //Make and Return the Model
        return $this->makeModelFromResponse($response);
    }

    /**
     * Save a specified URL into a file in user's Dropbox.
     *
     * @param string $path Path where the URL will be saved
     * @param string $url  URL to be saved
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-save_url
     *
     * @return string Async Job ID
     */
    public function saveUrl($path, $url)
    {
        //Path and URL cannot be null
        if (is_null($path) || is_null($url)) {
            throw new DropboxClientException('Path and URL cannot be null.');
        }

        //Save URL
        $response = $this->postToAPI('/files/save_url', ['path' => $path, 'url' => $url]);
        $body = $response->getDecodedBody();

        if (!isset($body['async_job_id'])) {
            throw new DropboxClientException('Could not retrieve Async Job ID.');
        }

        //Return the Asunc Job ID
        return $body['async_job_id'];
    }

    /**
     * Save a specified URL into a file in user's Dropbox.
     *
     * @param string $path       Path where the URL will be saved
     * @param string $url        URL to be saved
     * @param mixed  $asyncJobId
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-save_url-check_job_status
     *
     * @return FileMetadata|string Status (failed|in_progress) or FileMetadata (if complete)
     */
    public function checkJobStatus($asyncJobId)
    {
        //Async Job ID cannot be null
        if (is_null($asyncJobId)) {
            throw new DropboxClientException('Async Job ID cannot be null.');
        }

        //Get Job Status
        $response = $this->postToAPI('/files/save_url/check_job_status', ['async_job_id' => $asyncJobId]);
        $body = $response->getDecodedBody();

        //Status
        $status = isset($body['.tag']) ? $body['.tag'] : '';

        //If status is complete
        if ('complete' === $status) {
            return new FileMetadata($body);
        }

        //Return the status
        return $status;
    }

    /**
     * Get a one-time use temporary upload link to upload a file to a Dropbox location.
     *
     * @param string $path        Path to upload the file to
     * @param array  $commit_info Additional Params
     * @param array  $duration    how long before this link expires, in seconds
     * @param mixed  $origin
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-upload
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\TemporaryLink
     */
    public function getTemporarilyUploadLink($path, array $commit_info = [], $origin = '', $duration = 14400)
    {
        $params = [];
        $params['commit_info'] = $commit_info;
        $params['commit_info']['path'] = $path;
        $params['duration'] = $duration;

        //Get temporarily Link
        //Make a DropboxRequest object
        $request = new DropboxRequest('POST', '/files/get_temporary_upload_link', $this->getAccessToken()->getToken(), ' api', $params);

        // Set Origin Header
        $request->setHeaders(['Origin' => $origin]);

        //Send Request through the DropboxClient
        //Fetch and return the Response
        $result = $this->getClient()->sendRequest($request);

        $body = $result->getDecodedBody();

        //Make and Return the Model
        return new TemporaryLink($body);
    }

    /**
     * Upload a File to Dropbox.
     *
     * @param DropboxFile|string $dropboxFile DropboxFile object or Path to file
     * @param string             $path        Path to upload the file to
     * @param array              $params      Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-upload
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata
     */
    public function upload($dropboxFile, $path, array $params = [])
    {
        //Make Dropbox File
        $dropboxFile = $this->makeDropboxFile($dropboxFile);

        //If the file is larger than the Chunked Upload Threshold
        if ($dropboxFile->getSize() > static::AUTO_CHUNKED_UPLOAD_THRESHOLD) {
            //Upload the file in sessions/chunks
            return $this->uploadChunked($dropboxFile, $path, null, null, $params);
        }

        //Simple file upload
        return $this->simpleUpload($dropboxFile, $path, $params);
    }

    /**
     * Upload a File to Dropbox in a single request.
     *
     * @param DropboxFile|string $dropboxFile DropboxFile object or Path to file
     * @param string             $path        Path to upload the file to
     * @param array              $params      Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-upload
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata
     */
    public function simpleUpload($dropboxFile, $path, array $params = [])
    {
        //Make Dropbox File
        $dropboxFile = $this->makeDropboxFile($dropboxFile);

        //Set the path and file
        $params['path'] = $path;
        $params['file'] = $dropboxFile;

        //Upload File
        $file = $this->postToContent('/files/upload', $params);
        $body = $file->getDecodedBody();

        //Make and Return the Model
        return new FileMetadata($body);
    }

    /**
     * Start an Upload Session.
     *
     * @param DropboxFile|string $dropboxFile DropboxFile object or Path to file
     * @param int                $chunkSize   Size of file chunk to upload
     * @param bool               $close       Closes the session for "appendUploadSession"
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-upload_session-start
     *
     * @return string Unique identifier for the upload session
     */
    public function startUploadSession($dropboxFile, $chunkSize = -1, $close = false)
    {
        //Make Dropbox File with the given chunk size
        $dropboxFile = $this->makeDropboxFile($dropboxFile, $chunkSize);

        //Set the close param
        $params['close'] = $close ? true : false;

        //Set the file param
        $params['file'] = $dropboxFile;

        //Upload File
        $file = $this->postToContent('/files/upload_session/start', $params);
        $body = $file->getDecodedBody();

        //Cannot retrieve Session ID
        if (!isset($body['session_id'])) {
            throw new DropboxClientException('Could not retrieve Session ID.');
        }

        //Return the Session ID
        return $body['session_id'];
    }

    /**
     * Finish an upload session and save the uploaded data to the given file path.
     *
     * @param DropboxFile|string $dropboxFile DropboxFile object or Path to file
     * @param string             $sessionId   Session ID returned by `startUploadSession`
     * @param int                $offset      The amount of data that has been uploaded so far
     * @param int                $remaining   The amount of data that is remaining
     * @param string             $path        Path to save the file to, on Dropbox
     * @param array              $params      Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-upload_session-finish
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\FileMetadata
     */
    public function finishUploadSession($dropboxFile, $sessionId, $offset, $remaining, $path, array $params = [])
    {
        //Make Dropbox File
        $dropboxFile = $this->makeDropboxFile($dropboxFile, $remaining, $offset);

        //Session ID, offset, remaining and path cannot be null
        if (is_null($sessionId) || is_null($path) || is_null($offset) || is_null($remaining)) {
            throw new DropboxClientException('Session ID, offset, remaining and path cannot be null');
        }

        $queryParams = [];

        //Set the File
        $queryParams['file'] = $dropboxFile;

        //Set the Cursor: Session ID and Offset
        $queryParams['cursor'] = ['session_id' => $sessionId, 'offset' => $offset];

        //Set the path
        $params['path'] = $path;
        //Set the Commit
        $queryParams['commit'] = $params;

        //Upload File
        $file = $this->postToContent('/files/upload_session/finish', $queryParams);
        $body = $file->getDecodedBody();

        //Make and Return the Model
        return new FileMetadata($body);
    }

    /**
     * Append more data to an Upload Session.
     *
     * @param DropboxFile|string $dropboxFile DropboxFile object or Path to file
     * @param string             $sessionId   Session ID returned by `startUploadSession`
     * @param int                $offset      The amount of data that has been uploaded so far
     * @param int                $chunkSize   The amount of data to upload
     * @param bool               $close       Closes the session for futher "appendUploadSession" calls
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-upload_session-append_v2
     *
     * @return string Unique identifier for the upload session
     */
    public function appendUploadSession($dropboxFile, $sessionId, $offset, $chunkSize, $close = false)
    {
        //Make Dropbox File
        $dropboxFile = $this->makeDropboxFile($dropboxFile, $chunkSize, $offset);

        //Session ID, offset, chunkSize and path cannot be null
        if (is_null($sessionId) || is_null($offset) || is_null($chunkSize)) {
            throw new DropboxClientException('Session ID, offset and chunk size cannot be null');
        }

        $params = [];

        //Set the File
        $params['file'] = $dropboxFile;

        //Set the Cursor: Session ID and Offset
        $params['cursor'] = ['session_id' => $sessionId, 'offset' => $offset];

        //Set the close param
        $params['close'] = $close ? true : false;

        //Since this endpoint doesn't have
        //any return values, we'll disable the
        //response validation for this request.
        $params['validateResponse'] = false;

        //Upload File
        $file = $this->postToContent('/files/upload_session/append_v2', $params);

        //Make and Return the Model
        return $sessionId;
    }

    /**
     * Upload file in sessions/chunks.
     *
     * @param DropboxFile|string $dropboxFile DropboxFile object or Path to file
     * @param string             $path        Path to save the file to, on Dropbox
     * @param int                $fileSize    The size of the file
     * @param int                $chunkSize   The amount of data to upload in each chunk
     * @param array              $params      Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-upload_session-start
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-upload_session-finish
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-upload_session-append_v2
     *
     * @return string Unique identifier for the upload session
     */
    public function uploadChunked($dropboxFile, $path, $fileSize = null, $chunkSize = null, array $params = [])
    {
        //Make Dropbox File
        $dropboxFile = $this->makeDropboxFile($dropboxFile);

        //No file size specified explicitly
        if (is_null($fileSize)) {
            $fileSize = $dropboxFile->getSize();
        }

        //No chunk size specified, use default size
        if (is_null($chunkSize)) {
            $chunkSize = static::DEFAULT_CHUNK_SIZE;
        }

        //If the filesize is smaller
        //than the chunk size, we'll
        //make the chunk size relatively
        //smaller than the file size
        if ($fileSize <= $chunkSize) {
            $chunkSize = $fileSize / 2;
        }

        //Start the Upload Session with the file path
        //since the DropboxFile object will be created
        //again using the new chunk size.
        $sessionId = $this->startUploadSession($dropboxFile->getFilePath(), $chunkSize);

        //Uploaded
        $uploaded = $chunkSize;

        //Remaining
        $remaining = $fileSize - $chunkSize;

        //While the remaining bytes are
        //more than the chunk size, append
        //the chunk to the upload session.
        while ($remaining > $chunkSize) {
            //Append the next chunk to the Upload session
            $sessionId = $this->appendUploadSession($dropboxFile, $sessionId, $uploaded, $chunkSize);

            //Update remaining and uploaded
            $uploaded = $uploaded + $chunkSize;
            $remaining = $remaining - $chunkSize;

            // Update the progress
            $status = [
                'bytes_up_so_far' => $uploaded,
                'total_bytes_up_expected' => $fileSize,
                'percentage' => (round(($uploaded / $fileSize) * 100)),
                'progress' => 'uploading-to-cloud',
            ];

            $hash = $_REQUEST['hash'];
            $current = \TheLion\OutoftheBox\Upload::get_upload_progress($hash);
            $current['status'] = $status;
            \TheLion\OutoftheBox\Upload::set_upload_progress($_REQUEST['hash'], $current);
        }

        //Finish the Upload Session and return the Uploaded File Metadata
        return $this->finishUploadSession($dropboxFile, $sessionId, $uploaded, $remaining, $path, $params);
    }

    /**
     * Get a thumbnail for an image.
     *
     * @param string $path   Path to the file you want a thumbnail to
     * @param string $size   Size for the thumbnail image ['w32h32','w64h64','w128h128','w256h256','w480h320','w640h480','w960h640','w1024h768','w2048h1536']
     * @param string $format Format for the thumbnail image ['jpeg'|'png']
     * @param string $mode   How to resize and crop the image to achieve the desired size. ['strict', 'bestfit', 'fitone_bestfit]
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-get_thumbnail
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\Thumbnail
     */
    public function getThumbnail($path, $size = 'w256h256', $format = 'jpeg', $mode = 'strict')
    {
        //Path cannot be null
        if (is_null($path)) {
            throw new DropboxClientException('Path cannot be null.');
        }

        //Invalid Format
        if (!in_array($format, ['jpeg', 'png'])) {
            throw new DropboxClientException("Invalid format. Must either be 'jpeg' or 'png'.");
        }

        //Get Thumbnail
        $response = $this->postToContent('/files/get_thumbnail', ['path' => $path, 'mode' => $mode, 'format' => $format, 'size' => $size]);

        //Get file metadata from response headers
        $metadata = $this->getMetadataFromResponseHeaders($response);

        //File Contents
        $contents = $response->getBody();

        //Make and return a Thumbnail model
        return new Thumbnail($metadata, $contents);
    }

    /**
     * Preview a File.
     *
     * @param string $path Path to the file you want to download
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-get_preview
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\File
     */
    public function preview($path)
    {
        //Path cannot be null
        if (is_null($path)) {
            throw new DropboxClientException('Path cannot be null.');
        }

        //Download File
        $response = $this->postToContent('/files/get_preview', ['path' => $path]);

        //Get file metadata from response headers
        $metadata = $this->getMetadataFromResponseHeaders($response);

        //File Contents
        $contents = $response->getBody();

        //Make and return a File model
        return new File($metadata, $contents);
    }

    /**
     * Download a File.
     *
     * @param string     $path   Path to the file you want to download
     * @param null|mixed $export
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-download
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\File
     */
    public function download($path, $export = null)
    {
        //Path cannot be null
        if (is_null($path)) {
            throw new DropboxClientException('Path cannot be null.');
        }

        if ($export) {
            //Export File
            $response = $this->postToContent('/files/export', ['path' => $path]);
        } else {
            //Download File
            $response = $this->postToContent('/files/download', ['path' => $path]);
        }

        //Get file metadata from response headers
        $metadata = $this->getMetadataFromResponseHeaders($response);

        //File Contents
        $contents = $response->getBody();

        //Make and return a File model
        return new File($metadata, $contents);
    }

    public function stream($stream, $path, $export = null)
    {
        //Path cannot be null
        if (is_null($path)) {
            throw new DropboxClientException('Path cannot be null.');
        }

        //Make a DropboxRequest object
        $request = new DropboxRequest('POST', '/files/download', $this->getAccessToken()->getToken(), 'content', ['path' => $path]);
        $method = $request->getMethod();

        //Prepare Request
        list($url, $headers, $requestBody) = $this->getClient()->prepareRequest($request);

        //Send the Request to the Server through the HTTP Client
        //and fetch the raw response as DropboxRawResponse

        $options = ['curl' => [
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FILE => $stream,
        ]];

        $this->getClient()->getHttpClient()->send($url, $method, $requestBody, $headers, $options);

        rewind($stream);

        return $stream;
    }

    /**
     * List shared links of this user.
     *
     * @param string $path   Path to the folder. Defaults to root.
     * @param string $cursor the cursor returned by your last call to list_shared_links
     * @param array  $params Additional Params
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#sharing-list_shared_links
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\MetadataCollection
     */
    public function listSharedLinks($path = null, $cursor = null, array $params = ['direct_only' => true])
    {
        //Specify the root folder as an
        //empty string rather than as "/"
        if ('/' === $path) {
            $path = '';
        }

        //Set the path
        if (!empty($path)) {
            $params['path'] = $path;
        } elseif (!empty($cursor)) {
            $params['cursor'] = $cursor;
        }

        //Get File Metadata
        $response = $this->postToAPI('/sharing/list_shared_links', $params);

        //Make and Return the Model
        return $this->makeModelFromResponse($response);
    }

    /**
     * Create a shared link with custom settings.
     *
     * @param string             $path     The path to be shared by the shared link
     * @param SharedLinkSettings $settings the requested settings for the newly created shared link This field is optional
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#files-create_folder
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\FileLinkMetadata|\TheLion\OutoftheBox\API\Dropbox\Models\FolderLinkMetadata
     */
    public function createSharedLinkWithSettings($path, $settings = [])
    {
        //Path cannot be null
        if (is_null($path)) {
            throw new DropboxClientException('Path cannot be null.');
        }

        //Create Folder
        $response = $this->postToAPI('/sharing/create_shared_link_with_settings', ['path' => $path, 'settings' => $settings]);

        //Make and Return the Model
        return $this->makeModelFromResponse($response);
    }

    /**
     * Get Current Account.
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#users-get_current_account
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\Account
     */
    public function getCurrentAccount()
    {
        //Get current account
        $response = $this->postToAPI('/users/get_current_account', []);
        $body = $response->getDecodedBody();

        //Make and return the model
        return new Account($body);
    }

    /**
     * Get Account.
     *
     * @param string $account_id Account ID of the account to get details for
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#users-get_account
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\Account
     */
    public function getAccount($account_id)
    {
        //Get account
        $response = $this->postToAPI('/users/get_account', ['account_id' => $account_id]);
        $body = $response->getDecodedBody();

        //Make and return the model
        return new Account($body);
    }

    /**
     * Get Multiple Accounts in one call.
     *
     * @param string $account_id Account ID of the account to get details for
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#users-get_account_batch
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\AccountList
     */
    public function getAccounts(array $account_ids = [])
    {
        //Get account
        $response = $this->postToAPI('/users/get_account_batch', ['account_ids' => $account_ids]);
        $body = $response->getDecodedBody();

        //Make and return the model
        return new AccountList($body);
    }

    /**
     * Get Space Usage for the current user's account.
     *
     * @see https://www.dropbox.com/developers/documentation/http/documentation#users-get_space_usage
     *
     * @return array
     */
    public function getSpaceUsage()
    {
        //Get space usage
        $response = $this->postToAPI('/users/get_space_usage', []);

        return $response->getDecodedBody();
        //Return the decoded body
    }

    /**
     * Get metadata from response headers.
     *
     * @return array
     */
    protected function getMetadataFromResponseHeaders(DropboxResponse $response)
    {
        //Response Headers
        $headers = $response->getHeaders();

        //Empty metadata for when
        //metadata isn't returned
        $metadata = [];

        //If metadata is avaialble
        if (isset($headers[static::METADATA_HEADER])) {
            //File Metadata
            $data = $headers[static::METADATA_HEADER];

            //The metadata is present in the first index
            //of the metadata response header array
            if (is_array($data) && isset($data[0])) {
                $data = $data[0];
            }

            //Since the metadata is returned as a json string
            //it needs to be decoded into an associative array
            $metadata = json_decode((string) $data, true);
        }

        //Return the metadata
        return $metadata;
    }
}
