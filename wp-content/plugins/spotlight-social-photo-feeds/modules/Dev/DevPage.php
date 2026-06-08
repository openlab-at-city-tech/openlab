<?php

namespace RebelCode\Spotlight\Instagram\Modules\Dev;

use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\PostTypes\MediaPostType;
use RebelCode\Spotlight\Instagram\ErrorLog;
use RebelCode\Spotlight\Instagram\Engine\Store\MediaFileStore;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaType;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaItem;
use RebelCode\Spotlight\Instagram\CoreModule;
use RebelCode\Iris\Store;
use Psr\Container\ContainerInterface;

/**
 * The developers page.
 *
 * @since 0.1
 */
class DevPage
{
    const DB_PAGE_SIZE = 50;

    /**
     * @since 0.1
     *
     * @var CoreModule
     */
    protected $core;

    /**
     * @since 0.1
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param CoreModule         $core
     * @param ContainerInterface $container
     */
    public function __construct(CoreModule $core, ContainerInterface $container)
    {
        $this->core = $core;
        $this->container = $container;
    }

    /**
     * @since 0.1
     */
    public function __invoke()
    {
        $currTab = filter_input(INPUT_GET, 'tab');
        $currTab = empty($currTab) ? 'main' : $currTab;

        $tabUrl = function ($tab) {
            return admin_url('admin.php?page=sli-dev&tab=' . $tab);
        };

        $tabClass = function ($tab) use ($currTab) {
            return 'nav-tab' . ($tab === $currTab ? ' nav-tab-active' : '');
        };

        $tabContent = function () use ($currTab) {
            switch ($currTab) {
                default:
                    $this->mainTab();
                    break;
                case 'posts':
                    $this->postsTab();
                    break;
                case 'thumbnails':
                    $this->thumbnailsTab();
                    break;
                case 'services':
                    $this->servicesTab();
                    break;
            }
        }

        ?>
        <div class="wrap">
            <h1>Spotlight Dev</h1>

            <nav class="nav-tab-wrapper">
                <a class="<?= $tabClass('main') ?>" href="<?= $tabUrl('main') ?>">
                    Main
                </a>
                <a class="<?= $tabClass('posts') ?>" href="<?= $tabUrl('posts') ?>">
                    Posts
                </a>
                <a class="<?= $tabClass('thumbnails') ?>" href="<?= $tabUrl('thumbnails') ?>">
                    Thumbnails
                </a>
                <a class="<?= $tabClass('services') ?>" href="<?= $tabUrl('services') ?>">
                    Services
                </a>
            </nav>

            <?php $tabContent() ?>
        </div>
        <?php
    }

    /**
     * Renders the main debug tab.
     *
     * @since 0.1
     */
    protected function mainTab()
    {
        global $sliRuntime;
        ?>

        <h2>Runtime info</h2>

        <table class="widefat striped">
            <tbody>
                <tr>
                    <td>Basename</td>
                    <td><?= $sliRuntime->info->basename ?></td>
                </tr>
                <tr>
                    <td>Version</td>
                    <td><?= $sliRuntime->info->version ?></td>
                </tr>
                <tr>
                    <td>File</td>
                    <td><?= $sliRuntime->info->file ?></td>
                </tr>
                <tr>
                    <td>Directory</td>
                    <td><?= $sliRuntime->info->dir ?></td>
                </tr>
                <tr>
                    <td>Is Premium</td>
                    <td><?= $sliRuntime->info->isPro ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <td>Tier</td>
                    <td><?= $this->container->get('plugin/tier') ?></td>
                </tr>
                <tr>
                    <td>Free version</td>
                    <td><?= $sliRuntime->freeVersion ?></td>
                </tr>
                <tr>
                    <td>PRO version</td>
                    <td><?= $sliRuntime->proVersion ?></td>
                </tr>
                <tr>
                    <td>Is Free active</td>
                    <td><?= $sliRuntime->isFreeActive ? 'yes' : 'no' ?></td>
                </tr>
                <tr>
                    <td>Is PRO active</td>
                    <td><?= $sliRuntime->isProActive ? 'yes' : 'no' ?></td>
                </tr>
                <tr>
                    <td>Is a development build</td>
                    <td><?= (defined('SL_INSTA_DEV_ENV')  && SL_INSTA_DEV) ? 'yes' : 'no' ?></td>
                </tr>
                <tr>
                    <td>Assets base URL</td>
                    <td><?= spotlightInsta()->get('ui/assets_url') ?></td>
                </tr>
                <tr>
                    <td>REST API base URL</td>
                    <td><?= spotlightInsta()->get('rest_api/base_url') ?></td>
                </tr>
                <tr>
                    <td>Freemius SDK version</td>
                    <td><?php
                        global $sliFreemius;
                        echo $sliFreemius->version;
                        ?></td>
                </tr>
                <tr>
                    <td>Error Log path</td>
                    <td><?= ErrorLog::getDebugLogPath() ?></td>
                </tr>
            </tbody>
        </table>

        <h2>Reset</h2>

        <form method="POST">
            <input type="hidden" name="sli_reset_db" value="<?= wp_create_nonce('sli_reset_db') ?>" />
            <p>Remove plugin data and cache from the database</p>
            <p>
                <label>
                    <input type="checkbox" name="sli_reset_accounts" value="1" />
                    Delete accounts
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="sli_reset_feeds" value="1" />
                    Delete feeds
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="sli_reset_posts" value="1" />
                    Delete posts
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="sli_reset_import_lock" value="1" checked />
                    Reset import lock
                    (current: <?= $this->container->get('engine/importer/lock')->isSet() ? 'locked' : 'unlocked' ?>)
                </label>
            </p>
            <button type="submit" class="button">
                Reset database
            </button>
        </form>

        <?php do_action('spotlight/instagram/dev_page/tools') ?>

        <?php if (false): ?>
            <h2>Spotlight Error Log</h2>
            <p>Add a simulated error to the Spotlight error log.</p>
            <form method="POST">
                <input type="hidden" name="sli_dev_sim_error" value="<?= wp_create_nonce('sli_dev_sim_error') ?>" />
                <div style="margin-bottom: 10px">
                    <input
                        type="text"
                        name="sli_dev_error_msg"
                        placeholder="(Optional) Error message"
                        aria-label="Error message"
                    />
                </div>
                <button type="submit" class="button">
                    Add simulated error
                </button>
            </form>
        <?php endif; ?>

        <h2>WP Debug Log</h2>

        <?php if (file_exists(WP_CONTENT_DIR . '/debug.log')): ?>
            <form method="POST">
                <input type="hidden" name="sli_clear_log" value="<?= wp_create_nonce('sli_clear_log') ?>" />
                <button type="submit" class="button">Clear log</button>
            </form>
        <?php endif;

        $showLog = boolval($_GET['show_log'] ?? false);
        if (!$showLog) {
            ?>
            <a href="<?= admin_url('admin.php?page=sli-dev&tab=main&show_log=1') ?>">
                Show log
            </a>
            <?php
            return;
        }

        $debugLog = file_exists(WP_CONTENT_DIR . '/debug.log')
            ? file_get_contents(WP_CONTENT_DIR . '/debug.log')
            : '';

        if (empty($debugLog)) : ?>
            <p>The log is empty</p>
        <?php else : ?>
            <details style="margin-top: 10px">
                <summary>Show/Hide</summary>
                <pre class="sli-debug-log"><?= $debugLog ?></pre>
            </details>

            <style>
              .sli-debug-log {
                padding: 5px;
                background: rgba(0, 0, 0, 0.1);
                overflow-x: auto;
              }

              summary {
                cursor: pointer;
                user-select: none;
              }
            </style>
        <?php
        endif;
    }

    /** Renders the posts tab. */
    protected function postsTab()
    {
        /* @var $cpt PostType */
        $cpt = $this->container->get('media/cpt');
        /** @var Store $store */
        $store = $this->container->get('engine/store');

        $page = filter_input(INPUT_GET, 'db_page', FILTER_SANITIZE_NUMBER_INT);
        $page = empty($page) ? 1 : max(1, intval($page));

        $query = new Store\Query(
            [],
            new Store\Query\Order(Store\Query\Order::DESC, MediaPostType::TIMESTAMP),
            null,
            static::DB_PAGE_SIZE,
            ($page - 1) * static::DB_PAGE_SIZE
        );

        $mediaList = $store->query($query);
        $totalNum = array_sum((array) wp_count_posts($cpt->getSlug()));
        $numPages = ceil($totalNum / static::DB_PAGE_SIZE);

        $prevPageUrl = $page > 1 ? admin_url('admin.php?page=sli-dev&tab=posts&db_page=' . ($page - 1)) : '';
        $nextPageUrl = $page < $numPages ? admin_url('admin.php?page=sli-dev&tab=posts&db_page=' . ($page + 1)) : '';

        $headings = function () {
            ?>
            <tr>
                <th class="sli-db-col-id">ID</th>
                <th class="sli-db-col-link">Link</th>
                <th class="sli-db-col-caption">Caption</th>
                <th class="sli-db-col-type">Type</th>
                <th class="sli-db-col-source">Source</th>
                <th class="sli-db-col-date">Date &amp; Time</th>
                <th class="sli-db-col-last-seen">Last seen</th>
                <th class="sli-db-col-actions"></th>
            </tr>
            <?php
        };

        ?>
        <style>
          .sli-db-col-caption {
            word-break: break-word;
          }
        </style>

        <h2>Media</h2>

        <?php
            echo $this->tableNav($page, $numPages, $totalNum, $prevPageUrl, $nextPageUrl, function () {
                ob_start();
                ?>
                <form method="POST">
                    <?php wp_nonce_field(DevDeleteMedia::NONCE_ACTION, DevDeleteMedia::NONCE_PARAM) ?>
                    <button type="submit" class="button">Delete all</button>
                </form>
                <?php
                return ob_get_clean();
            })
        ?>

        <table class="widefat striped sli-db-table">
            <thead>
                <?php $headings() ?>
            </thead>
            <tbody>
                <?php if (empty($mediaList)) : ?>
                    <tr>
                        <td colspan="7">There are no media posts in the database.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($mediaList as $media):
                    $id = $media->id;
                    $localId = $media->localId;
                    $sources = $media->sources;
                    $url = $media->get(MediaItem::MEDIA_URL);
                    $permalink = $media->get(MediaItem::PERMALINK);
                    $caption = $media->get(MediaItem::CAPTION);
                    $type = $media->get(MediaItem::MEDIA_TYPE);
                    $type = ($type === MediaType::ALBUM) ? 'ALBUM' : $type;
                    $username = $media->get(MediaItem::USERNAME);
                    $timestamp = $media->get(MediaItem::TIMESTAMP);
                    $lastRequested = $media->get(MediaItem::LAST_REQUESTED);
                    ?>
                    <tr>
                        <td class="sli-db-col-id"><?= $localId ?></td>
                        <td class="sli-db-col-link">
                            <?php if (empty($permalink)): ?>
                                <?= $id ?> <i>(Missing permalink)</i>
                            <?php else: ?>
                                <a href="<?= $permalink ?>" target="_blank">
                                    <?= $id ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="sli-db-col-caption">
                            <?= wp_trim_words($caption, 12) ?>
                        </td>
                        <td class="sli-db-col-type">
                            <?php if (empty($url)): ?>
                                <?= $type ?> <i>(Missing media URL)</i>
                            <?php else: ?>
                                <a href="<?= $url ?>" target="_blank">
                                    <?= $type ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="sli-db-col-source">
                            <?php

                            if (!empty($username)) : ?>
                                <a href="https://www.instagram.com/<?= $username ?>" target="_blank">
                                    <?= $username ?>
                                </a>
                            <?php else:
                                $source = $sources[0] ?? new Source('', '');
                                $isHashtag = stripos($source->type, 'hashtag');
                                $prefix = $isHashtag ? '#' : '';
                                ?>
                                <a href="https://www.instagram.com/explore/tags/<?= $source->id ?>" target="_blank">
                                    <?= $prefix . $source->id ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="sli-db-col-date"><?= $timestamp ?></td>
                        <td class="sli-db-col-last-seen">
                            <?= is_numeric($lastRequested) ? date(DATE_ISO8601, $lastRequested): '' ?>
                        </td>
                        <td class="sli-db-col-actions">
                            <a
                                href="<?=
                                wp_nonce_url(
                                    admin_url("admin.php?page=sli-dev&tab=posts&db_page=$page&id=$localId"),
                                    DevDeleteMedia::NONCE_ACTION,
                                    DevDeleteMedia::NONCE_PARAM
                                )
                                ?>"
                                style="color: #b32d2e">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <?php $headings() ?>
            </tfoot>
        </table>
        <?php
    }

    /** Renders the thumbnails tab. */
    protected function thumbnailsTab()
    {
        /** @var MediaFileStore $fileStore */
        $fileStore = $this->container->get('engine/store/files');

        $dirUrl = $fileStore->getDirLocation()->url;
        $iterator = $fileStore->getAll();
        $totalNum = $fileStore->getNumFiles();

        $numPages = ceil($totalNum / static::DB_PAGE_SIZE);
        $page = filter_input(INPUT_GET, 'db_page', FILTER_SANITIZE_NUMBER_INT);
        $page = empty($page) ? 1 : max(1, intval($page));
        $offset = ($page - 1) * static::DB_PAGE_SIZE;

        $prevPageUrl = $page > 1
            ? admin_url('admin.php?page=sli-dev&tab=thumbnails&db_page=' . ($page - 1))
            : '';

        $nextPageUrl = $page < $numPages
            ? admin_url('admin.php?page=sli-dev&tab=thumbnails&db_page=' . ($page + 1))
            : '';

        echo $this->tableNav($page, $numPages, $totalNum, $prevPageUrl, $nextPageUrl, function () {
            ob_start();
            ?>
            <form method="POST">
                <input
                    type="hidden"
                    name="sli_delete_thumbnails"
                    value="<?= wp_create_nonce('sli_delete_thumbnails') ?>"
                />
                <button type="submit" class="button">Delete all</button>
            </form>
            <?php
            return ob_get_clean();
        });

        ?>
        <div class="sli-db-thumbnail-grid">
            <?php foreach ($iterator as $idx => $file) :
                if ($file->isDot() || $idx < $offset) {
                    continue;
                }

                if ($idx > ($page * static::DB_PAGE_SIZE)) {
                    break;
                }

                $fileName = $file->getFilename();
                $fileUrl = $dirUrl . '/' . $fileName;

                ?>
                <figure class="sli-db-thumbnail">
                    <div>
                        <a href="<?= $fileUrl ?>" target="_blank">
                            <img width="150" src="<?= $fileUrl ?>" alt="<?= $fileName ?>" />
                        </a>
                    </div>
                </figure>
            <?php endforeach; ?>

        </div>
        <style>
          .sli-db-thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            grid-gap: 10px;
            width: 100%;
            margin-top: 20px;
          }

          .sli-db-thumbnail-grid figure {
            margin: 0;
          }

          .sli-db-thumbnail-grid figure > div {
            position: relative;
            padding-bottom: 100%;
            height: 0;
          }

          .sli-db-thumbnail-grid figure > div a {
            position: absolute;
            inset: 0;
            display: flex;
            flex-flow: column;
            justify-content: center;
            align-items: center;
          }

          .sli-db-thumbnail-grid figure a img {
            width: 100%;
            height: 100%;
            object-fit: cover;
          }
        </style>
        <?php
    }

    /**
     * Renders the services tab.
     *
     * @since 0.1
     */
    protected function servicesTab()
    {
        [$factories] = $this->core->getCompiledServices();

        $keys = array_keys($factories);
        $tree = ServiceTree::buildTree('/', $keys, $this->container);

        echo '<h2>Services</h2>';
        echo ServiceTree::renderTree($tree);
    }

    /** Renders table navigation HTML */
    protected function tableNav(
        int $page = 1,
        int $numPages = 1,
        ?int $total = null,
        ?string $prevUrl = null,
        ?string $nextUrl = null,
        ?callable $actions = null
    ) {
        ob_start();
        ?>
        <div class="tablenav top">
            <?php if (is_callable($actions)): ?>
                <div class="alignleft actions">
                    <?php echo $actions() ?>
                </div>
            <?php endif; ?>
            <div class="tablenav-pages <?= ($numPages <= 1 ? 'one-page' : '') ?>">
                <?php if ($total !== null): ?>
                    <span class="displaying-num"><?= $total ?> items</span>
                <?php endif; ?>

                <?php if ($numPages > 1): ?>
                    <span class="pagination-links">
                    <?php if ($page > 1) : ?>
                        <a class="tablenav-pages-navspan button" href="<?= $prevUrl ?>">&lsaquo;</a>
                    <?php else: ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>
                    <?php endif; ?>

                    <span class="paging-input">
                        <span class="tablenav-paging-text">
                            &nbsp;
                            <span><?= $page ?></span>
                            of
                            <span><?= $numPages ?></span>
                            &nbsp;
                        </span>
                    </span>

                    <?php if ($page < $numPages) : ?>
                        <a class="tablenav-pages-navspan button" href="<?= $nextUrl ?>">&rsaquo;</a>
                    <?php else: ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>
                    <?php endif; ?>
                </span>
                <?php endif; ?>
            </div>
            <br class="break" />
        </div>
        <?php

        return ob_get_clean();
    }
}
