<?php

namespace RebelCode\Spotlight\Instagram\Engine\Store;

use ArrayIterator;
use DirectoryIterator;
use Exception;
use RebelCode\Iris\Data\Item;
use RebelCode\Spotlight\Instagram\Config\ConfigEntry;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaItem;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaType;
use RebelCode\Spotlight\Instagram\ErrorLog;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Utils\FileLocation;
use RebelCode\Spotlight\Instagram\Utils\Files;
use RebelCode\Spotlight\Instagram\Utils\Functions;
use RuntimeException;
use SplFileInfo;

class MediaFileStore
{
    const SIZE_SMALL = 's';
    const SIZE_MEDIUM = 'm';
    const SIZE_LARGE = 'l';
    const IMAGE_MIME_TYPE = 'image/jpeg';

    /** @var string */
    protected $dirName;

    /** @var ConfigEntry */
    protected $recipesConfig;

    /** @var ConfigEntry */
    protected $downloadVideosConfig;

    /** @var ThumbnailRecipe[] */
    protected $_recipes;

    /** @var bool */
    protected $_downloadVideos;

    /** @var FileLocation */
    protected $_dirLocCache;

    /**
     * Constructor.
     *
     * @param string $dirName The name of the directory where the files should be stored.
     * @param ConfigEntry $recipesConfig The config entry for the thumbnail recipes.
     * @param ConfigEntry $downloadVideosConfig The config entry for whether video should be downloaded.
     */
    public function __construct(string $dirName, ConfigEntry $recipesConfig, ConfigEntry $downloadVideosConfig)
    {
        $this->dirName = $dirName;
        $this->recipesConfig = $recipesConfig;
        $this->downloadVideosConfig = $downloadVideosConfig;
    }

    /**
     * Downloads the files for an item.
     *
     * @param Item $item The item.
     * @param bool $overwrite Whether to download and overwrite existing local files, if they exist.
     *
     * @return Item The new item with its URLS updated to point to the downloaded files.
     */
    public function downloadForItem(Item $item, bool $overwrite = false): Item
    {
        return $item->withChanges($this->downloadForItemData($item->id, $item->data, $overwrite));
    }

    /**
     * Downloads the files for an item, using its data.
     *
     * @param string $id The item's ID.
     * @param array $data The item's data.
     * @param bool $overwrite Whether to download and overwrite existing local files, if they exist.
     *
     * @return array The new item data, with the URLS updated to point to the downloaded files.
     */
    public function downloadForItemData(string $id, array $data, bool $overwrite = false): array
    {
        $type = $data[MediaItem::MEDIA_TYPE] ?? '';
        $isVideo = $type === MediaType::VIDEO;
        $children = $type === MediaType::ALBUM ? $data[MediaItem::CHILDREN] ?? [] : [];

        $mediaUrl = $data[MediaItem::MEDIA_URL] ?? '';
        $thumbnailUrl = $data[MediaItem::THUMBNAIL_URL] ?? '';
        $thumbnailUrl = empty($thumbnailUrl) ? null : $thumbnailUrl;

        // Use the `thumbnail` for videos, and the `media_url` for everything else
        $remoteImgUrl = $isVideo ? $thumbnailUrl : $mediaUrl;

        // If the image is not from Instagram, use the "shortcode URL" approach
        $shortcode = $data[MediaItem::SHORTCODE] ?? '';
        $fallbackUrl = empty($shortcode) ? '' : "https://www.instagram.com/p/$shortcode/media/?size=l";
        $remoteImgIsIg = $remoteImgUrl && $this->isUrlFromIg($remoteImgUrl);
        $remoteImgUrl = $remoteImgIsIg ? $remoteImgUrl : $fallbackUrl;

        if ($remoteImgUrl && ($remoteImgIsIg || $overwrite)) {
            $refImage = $this->getReferenceImage($id, $remoteImgUrl);

            try {
                // Get and save the original image's dimensions
                if (empty($data[MediaItem::MEDIA_SIZE])) {
                    $refImage->downloadFrom($refImage->url);
                    $imgSize = @getimagesize($refImage->path);

                    if (is_array($imgSize) && $imgSize[0] > 0 && $imgSize[1] > 0) {
                        $data[MediaItem::MEDIA_SIZE] = [
                            'width' => $imgSize[0],
                            'height' => $imgSize[1],
                        ];
                    }
                }

                // Generate the thumbnails from the base image and save their URLs into the data
                $thumbnails = $this->generateThumbnails($refImage, $id);
                if ($thumbnails !== null) {
                    $data[MediaItem::THUMBNAILS] = array_map(Functions::property('url'), $thumbnails);
                }

                // If the item has no main thumbnail, set it to the largest available thumbnail
                if (empty($data[MediaItem::THUMBNAIL_URL])) {
                    $largeRecipe = $this->getRecipes()[static::SIZE_LARGE] ?? null;
                    $largeThumbnail = $data[MediaItem::THUMBNAILS][static::SIZE_LARGE] ?? null;

                    if ($largeRecipe && $largeThumbnail) {
                        $data[MediaItem::THUMBNAIL_URL] = $largeThumbnail;
                    } else {
                        $data[MediaItem::THUMBNAIL_URL] = $data[MediaItem::MEDIA_URL];
                    }
                }
            } catch (Exception $e) {
                ErrorLog::exception($e);
            } finally {
                if (file_exists($refImage->path) && !is_dir($refImage->path)) {
                    @unlink($refImage->path);
                }
            }
        }

        // Download videos for video IG posts
        if ($isVideo) {
            $videoFile = $this->getVideoLocation($id);

            if ($this->shouldDownloadVideos()) {
                try {
                    $videoFile->downloadFrom($mediaUrl, $overwrite);
                    $data[MediaItem::MEDIA_URL] = $videoFile->url;
                } catch (Exception $e) {
                    // Do nothing
                }
            } else {
                // Only delete the video file if the item's URL points to Instagram.
                // Videos don't automatically reset their URLs when whe user disables video downloading.
                // The videos are updated on the next fetch, which will bring in a new video URL.
                $videoMediaUrl = $data[MediaItem::MEDIA_URL] ?? null;

                if (is_string($videoMediaUrl) && $this->isUrlFromIg($videoMediaUrl)) {
                    @unlink($videoFile->path);
                }
            }
        }

        // Recurse for children IG posts
        if (!empty($children)) {
            foreach ($children as $idx => $child) {
                $childId = $child[MediaItem::CHILD_ID] ?? null;
                if (!empty($childId)) {
                    $data[MediaItem::CHILDREN][$idx] = $this->downloadForItemData($childId, $child, $overwrite);
                }
            }
        }

        return $data;
    }

    /**
     * Retrieves the thumbnail files for an IG post.
     *
     * @param string $id The ID of the IG post.
     *
     * @return FileLocation[]
     */
    public function getThumbnailsFor(string $id): array
    {
        $result = [];

        foreach ($this->getRecipes() as $size => $recipe) {
            $result[$size] = false;
            $file = $this->findSavedThumbnail($id, $size);

            if ($file && file_exists($file->path)) {
                $result[$size] = $file;
            }
        }

        return $result;
    }

    /**
     * Retrieves all the files in the directory.
     *
     * @return iterable<SplFileInfo>
     */
    public function getAll(): iterable
    {
        $location = $this->getDirLocation();

        try {
            $iterator = new DirectoryIterator($location->path);
        } catch (UnexpectedValueException $e) {
            $iterator = new ArrayIterator([]);
        }

        return $iterator;
    }

    /**
     * Retrieves the number of files in the directory.
     *
     * @return int
     */
    public function getNumFiles(): int
    {
        return count(@scandir($this->getDirLocation()->path)) - 2;
    }

    /**
     * Deletes the thumbnails for a single IG post.
     *
     * @param array $data The IG post item data.
     */
    public function deleteFor(array $data): void
    {
        $id = $data[MediaItem::MEDIA_ID] ?? $data[MediaItem::CHILD_ID] ?? null;

        if ($id !== null) {
            foreach ($this->getThumbnailsFor($id) as $file) {
                if ($file) {
                    @unlink($file->path);
                }
            }

            $video = $this->getVideoLocation($id);
            if (file_exists($video->path)) {
                @unlink($video->path);
            }

            foreach ($data[MediaItem::CHILDREN] ?? [] as $child) {
                $this->deleteFor($child);
            }
        }
    }

    /**
     * Deletes all the thumbnails.
     */
    public function deleteAll(): void
    {
        Files::rmDirRecursive($this->getDirLocation()->path);
    }

    /**
     * Retrieves the file path and URL for an IG post's video file.
     *
     * @param string $id The ID of the IG post.
     * @return FileLocation
     */
    public function getVideoLocation(string $id): FileLocation
    {
        $dirInfo = $this->getDirLocation();

        return new FileLocation(
            $dirInfo->path . '/' . $id . '.mp4',
            $dirInfo->url . '/' . $id . '.mp4'
        );
    }

    /**
     * Retrieves the file path and URL for one of an IG post's image files.
     *
     * @param string $id The ID of the IG post.
     * @param string|null $size The name of the size of the recipe.
     * @param ThumbnailRecipe|null $recipe The recipe.
     * @return FileLocation
     */
    public function getThumbnailLocation(
        string $id,
        ?string $size = null,
        ?ThumbnailRecipe $recipe = null
    ): FileLocation {
        $dirInfo = $this->getDirLocation();
        $fileName = $id;
        $fileName .= $size ? "-$size" : '';
        $fileName .= $recipe ? "-{$recipe->quality}-{$recipe->width}" : '';
        $fileName .= '.jpg';

        return new FileLocation(
            $dirInfo->path . '/' . $fileName,
            $dirInfo->url . '/' . $fileName
        );
    }

    /**
     * Retrieves the file path and URL for one of an IG post's image files.
     *
     * @param string $id The ID of the IG post.
     * @param string $size The name of the size of the recipe.
     * @param ThumbnailRecipe|null $recipe Optional recipe. If the file does not exist and the recipe is given, the
     *                                     method will use the recipe to return the location of where the files should
     *                                     be. If the file does not exist and a recipe is not given, null is returned.
     * @return FileLocation|null
     */
    public function findSavedThumbnail(string $id, string $size, ?ThumbnailRecipe $recipe = null): ?FileLocation
    {
        $dirInfo = $this->getDirLocation();
        $list = glob("{$dirInfo->path}/$id-$size*.jpg");

        if (count($list) > 0) {
            $fileName = basename(reset($list));

            return new FileLocation(
                $dirInfo->path . '/' . $fileName,
                $dirInfo->url . '/' . $fileName
            );
        } elseif ($recipe) {
            return $this->getThumbnailLocation($id, $size, $recipe);
        } else {
            return null;
        }
    }

    /**
     * Retrieves the file path and URL for an IG post's saved thumbnail.
     *
     * @param FileLocation $file The file.
     * @return ThumbnailRecipe|null The recipe, or null if it could not be determined.
     */
    public function getRecipeForSavedThumbnail(FileLocation $file): ?ThumbnailRecipe
    {
        $fileName = basename($file->path);
        $parts = explode('-', $fileName);

        if (count($parts) === 4) {
            $quality = intval($parts[2]);
            $width = intval($parts[3]);

            return new ThumbnailRecipe(true, $width, $quality);
        } else {
            return null;
        }
    }

    /**
     * Retrieves the path and URL for the directory where the files are stored.
     *
     * @return FileLocation
     */
    public function getDirLocation(): FileLocation
    {
        if ($this->_dirLocCache !== null) {
            return $this->_dirLocCache;
        }

        $uploadDir = wp_upload_dir();

        if (isset($uploadDir['error']) && $uploadDir['error'] !== false) {
            throw new RuntimeException(
                'Spotlight failed to access your uploads directory: ' . $uploadDir['error']
            );
        }

        if (!is_dir($uploadDir['basedir'])) {
            if (!mkdir($uploadDir['basedir'], 0775)) {
                throw new RuntimeException(
                    'Spotlight failed to create the uploads directory: ' . $uploadDir['basedir']
                );
            }
        }

        $subDir = $uploadDir['basedir'] . '/' . $this->dirName;
        if (!is_dir($subDir)) {
            if (!mkdir($subDir, 0775)) {
                throw new RuntimeException(
                    'Spotlight failed to create its photo uploads directory: ' . $subDir
                );
            }
        }

        // Fix the URL protocol to be HTTPS when the site is using SSL
        $baseUrl = is_ssl()
            ? str_replace('http://', 'https://', $uploadDir['baseurl'])
            : $uploadDir['baseurl'];

        return $this->_dirLocCache = new FileLocation($subDir, $baseUrl . '/' . $this->dirName);
    }

    /**
     * Generates thumbnails for a source image, using the internal recipes.
     *
     * @param FileLocation $refImage The source image location.
     * @param string $id The ID of the IG post. Used to create the file names for the generated thumbnails.
     * @param bool $overwrite True to overwrite existing thumbnails. If false (default), thumbnails won't be generated
     *                        if they already exist.
     *
     * @return array<string, FileLocation>|null An associative array of thumbnail {@link FileLocation} instances, keyed
     *                                          by their respective recipe name, or null on failure.
     */
    protected function generateThumbnails(FileLocation $refImage, string $id, bool $overwrite = false): ?array
    {
        $result = [];

        foreach ($this->getRecipes() as $size => $recipe) {
            $newThumbnail = $this->getThumbnailLocation($id, $size, $recipe);

            // Get the currently saved thumbnail, if one exists
            $savedThumbnail = $this->findSavedThumbnail($id, $size);
            $savedExists = $savedThumbnail && file_exists($savedThumbnail->path);
            $savedRecipe = $savedExists ? $this->getRecipeForSavedThumbnail($savedThumbnail) : null;

            if ($recipe->enabled) {
                $isSameRecipe = $savedRecipe && $savedRecipe->isEqualTo($recipe);

                // Generate a new thumbnail if one doesn't yet exist, or it exists but the recipe is different
                if (!$savedExists || !$isSameRecipe || $overwrite) {
                    // Remove the currently saved thumbnail before generating the new one
                    if ($savedExists) {
                        @unlink($savedThumbnail->path);
                    }

                    $newThumbnail = $this->optimizeImage($refImage, $newThumbnail, $recipe);
                }

                $result[$size] = $newThumbnail;
            } else {
                // If the file exists but the recipe is disabled, delete the file
                if ($savedExists) {
                    @unlink($savedThumbnail->path);
                }
            }
        }

        return $result;
    }

    /**
     * Retrieves the thumbnail recipes from config.
     *
     * @return ThumbnailRecipe[]
     */
    protected function getRecipes(): array
    {
        if ($this->_recipes === null) {
            $this->_recipes = Arrays::map($this->recipesConfig->getValue(), [ThumbnailRecipe::class, 'fromArray']);

            uasort($this->_recipes, function (ThumbnailRecipe $a, ThumbnailRecipe $b) {
                return $a->width <=> $b->width;
            });
        }

        return $this->_recipes;
    }

    /**
     * Retrieves whether videos should be downloaded, from the config.
     *
     * @return bool
     */
    protected function shouldDownloadVideos(): bool
    {
        return ($this->_downloadVideos === null)
            ? $this->_downloadVideos = $this->downloadVideosConfig->getValue()
            : $this->_downloadVideos;
    }

    /**
     * Resizes and optimizes an image using the WordPress image editor.
     *
     * @param FileLocation $src The source image.
     * @param FileLocation $dest The destination image.
     * @param ThumbnailRecipe $recipe The recipe to use.
     * @return FileLocation|null The location of the optimized file, or null on failure. This may be the same as $dest.
     */
    protected function optimizeImage(FileLocation $src, FileLocation $dest, ThumbnailRecipe $recipe): ?FileLocation
    {
        if (!file_exists($src->path)) {
            $src->downloadFrom($src->url);
        }

        $editor = wp_get_image_editor($src->path);

        if (is_wp_error($editor)) {
            return null;
        }

        if (!empty($recipe->width)) {
            @$editor->resize($recipe->width, null);
        }

        @$editor->set_quality($recipe->quality);

        $editorResult = @$editor->save($dest->path, static::IMAGE_MIME_TYPE);

        if (is_wp_error($editorResult)) {
            return null;
        } else {
            $oldName = basename($dest->path);
            $newName = basename($editorResult['path']);

            return new FileLocation(
                str_replace($oldName, $newName, $dest->path),
                str_replace($oldName, $newName, $dest->url)
            );
        }
    }

    /**
     * Retrieves the reference image - the remote full-sized IG image which is used to generate the thumbnails.
     *
     * @param string $id The ID of the item.
     * @param string $url The remote image URL.
     * @return FileLocation
     */
    protected function getReferenceImage(string $id, string $url): FileLocation {
        $file = $this->getThumbnailLocation($id);
        $file->url = $url;

        return $file;
    }

    /**
     * Determines if a URL points to Instagram's servers.
     *
     * @param string $url The URL to check.
     * @return bool True if the URL points to Instagram's servers, false otherwise.
     */
    protected function isUrlFromIg(string $url): bool
    {
        $remoteHost = empty($url)
            ? ''
            : parse_url($url, PHP_URL_HOST);

        return stripos($remoteHost, 'instagram.com') !== false;
    }
}
