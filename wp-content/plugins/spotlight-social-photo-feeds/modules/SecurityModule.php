<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Extension;
use Dhii\Services\Factories\FuncService;
use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Config\ConfigEntry;
use RebelCode\Spotlight\Instagram\Config\ConfigSet;
use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Wp\Notice;
use RebelCode\Spotlight\Instagram\Wp\NoticesManager;

class SecurityModule extends Module
{
    // Config keys
    const CFG_SHOW_HTTPS_NOTICE = 'showHttpsNotice';
    const CFG_SHOW_OPENSSL_NOTICE = 'showOpenSslNotice';
    // Notice IDs
    const NTC_NO_HTTPS = 'no_https';
    const NTC_NO_OPENSSL_EXT = 'no_openssl_ext';

    public function run(ContainerInterface $c): void
    {
        /* @var ConfigSet $cfg */
        $cfg = $c->get('config/set');

        /* @var NoticesManager $nm */
        $nm = $c->get('wp/notices/manager');

        if ($cfg->get(static::CFG_SHOW_HTTPS_NOTICE)->getValue()) {
            $nm->show(static::NTC_NO_HTTPS);
        }

        if ($cfg->get(static::CFG_SHOW_OPENSSL_NOTICE)->getValue()) {
            $nm->show(static::NTC_NO_OPENSSL_EXT);
        }
    }

    public function getFactories(): array
    {
        return [
            //==========================================================================
            // NOTICES
            //==========================================================================

            'notices/no_https' => new Factory(['config/show_https_notice'], function (ConfigEntry $option) {
                return new Notice(static::NTC_NO_HTTPS, Notice::WARNING, Notice::disableOption($option),
                    sprintf(
                        _x(
                            '%1$s: We strongly recommend that you switch your site to HTTPS to prevent your access tokens from being compromised.',
                            '%1$s is the name of the plugin',
                            'sli'
                        ),
                        '<b>' . SL_INSTA_NAME . '</b>'
                    )
                );
            }),

            'notices/no_openssl_ext' => new Factory(['config/show_openssl_ext_notice'], function (ConfigEntry $option) {
                return new Notice(static::NTC_NO_OPENSSL_EXT, Notice::WARNING, Notice::disableOption($option),
                    sprintf(
                        _x(
                            '%1$s: We strongly recommend that you enable the %2$s PHP extension to allow Spotlight to encrypt your account access tokens.',
                            '%1$s is the name of the plugin, %2$s is "openssl"',
                            'sli'
                        ),
                        '<b>' . SL_INSTA_NAME . '</b>',
                        '<code>openssl</code>'
                    )
                );
            }),

            //==========================================================================
            // CONFIG
            //==========================================================================

            'config/show_https_notice' => new Factory([], function () {
                return new WpOption('sli_show_https_notice', 0, true, WpOption::SANITIZE_BOOL);
            }),

            'config/show_openssl_ext_notice' => new Factory([], function () {
                return new WpOption('sli_show_openssl_ext_notice', 0, true, WpOption::SANITIZE_BOOL);
            }),

            //==========================================================================
            // MIGRATIONS
            //==========================================================================

            'migrations/*/check_ssl' => new FuncService(['@config/set'], function ($v1, $v2, ConfigSet $cfg) {
                $isHttps = apply_filters('spotlight/security/is_https', wp_is_using_https());
                $hasOpenSsl = apply_filters('spotlight/security/has_openssl_ext', extension_loaded('openssl'));

                $cfg->get(static::CFG_SHOW_HTTPS_NOTICE)->setValue(!$isHttps);
                $cfg->get(static::CFG_SHOW_OPENSSL_NOTICE)->setValue(!$hasOpenSsl);
            }),
        ];
    }

    public function getExtensions(): array
    {
        return [
            // Register the config entries
            'config/entries' => new ArrayExtension([
                static::CFG_SHOW_HTTPS_NOTICE => 'config/show_https_notice',
                static::CFG_SHOW_OPENSSL_NOTICE => 'config/show_openssl_ext_notice',
            ]),

            // Register the notices
            'wp/notices' => new ArrayExtension([
                'notices/no_https',
                'notices/no_openssl_ext',
            ]),

            // Register the migrations
            'migrator/migrations' => new ArrayExtension([
                'migrations/*/check_ssl',
            ]),

            'ui/l10n/admin-common' => new Extension([], function ($l10n) {
                $l10n['security'] = [
                    'isUsingHttps' => wp_is_using_https(),
                    'hasOpensslExt' => extension_loaded('openssl'),
                ];

                return $l10n;
            }),
        ];
    }
}
