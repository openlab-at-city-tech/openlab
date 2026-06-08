<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use RebelCode\Spotlight\Instagram\Wp\CronJob;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\Config\ConfigEntry;
use Psr\Container\ContainerInterface;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factories\FuncService;

/**
 * The module that adds the migration system.
 *
 * @since 0.4.1
 */
class MigrationModule extends Module
{
    /**
     * Controls whether migrations use the locking mechanisms.
     *
     * @since 0.4.1
     */
    const CHECK_LOCK = false;

    /**
     * @inheritDoc
     *
     * @since 0.4.1
     */
    public function run(ContainerInterface $c): void
    {
        // Hook in the migration function into the migration cron
        add_action('spotlight/instagram/migration', $c->get('function'), 10, 2);

        /* @var $verCfg ConfigEntry */
        $verCfg = $c->get('config/version');

        $dbVer = $verCfg->getValue();
        $currVer = SL_INSTA_VERSION;

        // If "0.0" (no version in DB), it could be a new installation.
        // Try to detect an existing installation by checking for accounts.
        // If there are accounts in the DB, keep the "0.0" to invoke the migration.
        // If there are no accounts, it's most likely a new installation. No migration needed.
        if ($dbVer === '0.0') {
            $dbVer = count($c->get('accounts/cpt')->query()) > 0 ? $dbVer : $currVer;
        }

        /* @var $lockCfg ConfigEntry */
        $lockCfg = $c->get('config/lock');
        // Check if migrations are locked
        $isLocked = static::CHECK_LOCK && $lockCfg->getValue() === '1';

        // Compare the DB and current versions. If DB version is lower, run the migrations
        if (!$isLocked && version_compare($dbVer ?? '0.0', $currVer ?? '0.0', '<')) {
            // Lock to prevent other threads from registering the cron
            $lockCfg->setValue('1');

            // Register the migration cron
            CronJob::register(new CronJob('spotlight/instagram/migration', [$dbVer, SL_INSTA_VERSION]));
        }
    }

    /**
     * @inheritDoc
     *
     * @since 0.4.1
     */
    public function getFactories(): array
    {
        return [
            // The DB config that stores the previous version
            'config/version' => new Value(new WpOption('sli_version', '0.0')),

            // The DB config that "locks" other threads from also performing the upgrade
            'config/lock' => new Value(new WpOption('sli_upgrade_lock', '0')),

            // The list of callbacks to invoke if a migration is required
            'migrations' => new Value([]),

            // The migration function
            'function' => new FuncService(
                ['config/lock', 'config/version', 'migrations'],
                function ($dbVer, $currVer, ConfigEntry $lock, ConfigEntry $version, array $migrations) {
                    try {
                        // Make sure the migration is locked
                        $lock->setValue('1');

                        // Run the callbacks
                        Arrays::callEach($migrations, [$dbVer, $currVer]);

                        // Update the version config
                        $version->setValue(SL_INSTA_VERSION);
                    } finally {
                        // Unlock the upgrade process
                        $lock->setValue('0');
                    }
                }
            ),
        ];
    }
}
