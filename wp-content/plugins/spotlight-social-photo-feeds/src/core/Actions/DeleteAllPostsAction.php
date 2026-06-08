<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Actions;

use RebelCode\Iris\Utils\Marker;
use RebelCode\Spotlight\Instagram\Engine\Store\MediaFileStore;
use RebelCode\Spotlight\Instagram\ErrorLog;
use RebelCode\Spotlight\Instagram\Utils\DbQueries;

class DeleteAllPostsAction
{
    /* The limit to use in the delete query. Effectively, the size of the batches for the delete query. */
    const LIMIT = 500;

    /** @var string */
    protected $cpt;

    /** @var MediaFileStore */
    protected $fileStore;

    /** @var string */
    protected $batchCron;

    /** @var Marker */
    protected $importerLock;

    /** @var Marker */
    protected $importerInterrupt;

    /**
     * Constructor.
     *
     * @param string $cpt
     * @param MediaFileStore $fileStore
     * @param string $batchCron
     * @param Marker $importerLock
     * @param Marker $importerInterrupt
     */
    public function __construct(
        string $cpt,
        MediaFileStore $fileStore,
        string $batchCron,
        Marker $importerLock,
        Marker $importerInterrupt
    ) {
        $this->cpt = $cpt;
        $this->fileStore = $fileStore;
        $this->batchCron = $batchCron;
        $this->importerLock = $importerLock;
        $this->importerInterrupt = $importerInterrupt;
    }

    public function __invoke()
    {
        try {
            set_time_limit(30 * 60);

            global $wpdb;
            $total = 0;

            // If the importer is running, interrupt it
            if ($this->importerLock->isSet()) {
                $this->importerInterrupt->create();
            }

            do {
                $query = DbQueries::deletePostsByType([$this->cpt], static::LIMIT);
                $count = $wpdb->query($query);

                $total += $count;
            } while ($count !== false && $count > 0);

            // Delete all thumbnails
            $this->fileStore->deleteAll();

            wp_unschedule_hook($this->batchCron);

            return $total;
        } catch (\Throwable $exception) {
            ErrorLog::exception($exception);
            return 0;
        }
    }
}
