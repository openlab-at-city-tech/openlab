<?php

use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Wp\Asset;

if (!current_user_can('manage_options')) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);

    exit;
}

global $wp_version;
global $wp_scripts;
global $wp_styles;

$siteUrl = get_site_url();
$pageUrl = $siteUrl . '/spotlight/';

$c = spotlightInsta();
$renderFn = $c->get('ui/main_page/render_fn');

do_action('spotlight/instagram/localize_config');

define('WP_ADMIN', true);

{
    wp_default_styles($wp_styles);
    wp_default_scripts($wp_scripts);
    wp_enqueue_media();
    wp_enqueue_editor();

    // WP SCRIPTS
    $scripts = [];
    resolveAssets($wp_scripts, $scripts, [
        'jquery-core',
        'jquery-migrate',
        'utils',
        'wp-i18n',
        'common',
        'admin-bar',
        'svg-painter',
        'wp-color-picker',
        'wp-auth-check',
        'jquery-ui-draggable',
        'heartbeat',
        'jquery-ui-slider',
        'jquery-touch-punch',
        'wp-util',
        'wp-backbone',
        'media-editor',
        'wp-media-modals',
        'wp-mediaelement',
        'media-audiovideo',
        'clipboard',
        'mce-view',
        'imgareaselect',
        'image-edit',
        'wp-dom-ready',
        'wp-a11y',
        'sli-admin',
        'sli-admin-pro',
    ]);

    $styles = [];
    resolveAssets($wp_styles, $styles, [
        'dashicons',
        'common',
        'forms',
        'dashboard',
        'media',
        'buttons',
        'wp-color-picker',
        'media-views',
        'imgareaselect',
        'sli-admin',
        'sli-admin-pro',
    ]);

    [$headerScripts, $footerScripts] = separateScripts($scripts);

    $stylesHtml = implode("\n", Arrays::map($styles, 'renderStyleTag'));
    $headerScriptsHtml = implode("\n", Arrays::map($headerScripts, 'renderScriptTag'));
    $footerScriptsHtml = implode("\n", Arrays::map($footerScripts, 'renderScriptTag'));
}

?>
    <!doctype html>
    <html lang="en">
        <head>
            <title>Spotlight</title>
            <?= $stylesHtml ?>
            <?= $headerScriptsHtml ?>
            <?php do_action('admin_head'); ?>

            <style type="text/css">
              .spotlight-wrap {
                position: fixed;
                top: 42px;
                bottom: 0;
                left: 0;
                right: 0;
              }
              .wp-submenu {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 42px;
                padding: 8px 10px;
                margin: 0 !important;
                box-sizing: border-box;

                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;

                background: #fff;
                border-bottom: 1px solid var(--sli-line-color);
                list-style: none;
                z-index: 99999;
              }

              .wp-submenu li {
                flex: 0;
                display: inline-block;
                white-space: nowrap;
                padding: 5px 8px;
                list-style: none;
              }

              .wp-submenu li:not(:last-of-type) {
                margin-right: 10px;
              }
            </style>
        </head>
        <body class="wp-admin wp-core-ui js toplevel_page_spotlight-instagram branch-5-6 version-5-6-2 admin-color-fresh locale-en-us customize-support">
            <div class="spotlight-wrap">
                <?= $renderFn() ?>
                <?= $footerScriptsHtml ?>
                <?php do_action('admin_footer'); ?>
            </div>

            <div id="toplevel_page_spotlight-instagram">
                <ul class="wp-submenu">
                    <li><a href="<?= $pageUrl ?>?screen=feeds">Feeds</a></li>
                    <li><a href="<?= $pageUrl ?>?screen=new">Add new</a></li>
                    <li><a href="<?= $pageUrl ?>?screen=promotions">Promotions</a></li>
                    <li><a href="<?= $pageUrl ?>?screen=settings">Settings</a></li>
                </ul>
            </div>
        </body>
    </html>
<?php

/**
 * @since 0.6
 *
 * @param WP_Dependencies $repo
 * @param array           $list
 * @param string[]        $handles
 */
function resolveAssets(WP_Dependencies $repo, array &$list, array $handles)
{
    foreach ($handles as $handle) {
        if (array_key_exists($handle, $repo->registered)) {
            $asset = $repo->registered[$handle];

            if (count($asset->deps) > 0) {
                resolveAssets($repo, $list, $asset->deps);
            }

            if (!array_key_exists($handle, $list)) {
                $list[$handle] = $asset;
            }
        }
    }
}

/**
 * @since 0.6
 *
 * @param _WP_Dependency[] $scripts
 *
 * @return array
 */
function separateScripts(array $scripts): array
{
    $header = [];
    $footer = [];

    foreach ($scripts as $handle => $script) {
        $group = $script->extra['group'] ?? 0;

        if ($group === 1) {
            $footer[] = $script;
        } else {
            $header[] = $script;
        }
    }

    return [$header, $footer];
}

/**
 * @since 0.6
 *
 * @param _WP_Dependency $asset
 *
 * @return string
 */
function getAssetUri(_WP_Dependency $asset): string
{
    if (empty($asset->src)) {
        return '';
    }

    global $wp_version;

    $ver = empty($asset->ver)
        ? $wp_version
        : $asset->ver;

    return $asset->src . '?ver=' . $ver;
}

/**
 * @since 0.6
 *
 * @param string $uri
 * @param int    $type
 *
 * @return string
 */
function resolveRelativeUri(string $uri, int $type): string
{
    global $wp_scripts;
    global $wp_styles;

    $repo = ($type === Asset::SCRIPT) ? $wp_scripts : $wp_styles;

    return (strpos($uri, '/') === 0)
        ? $repo->base_url . $uri
        : $uri;
}

/**
 * @since 0.6
 *
 * @param _WP_Dependency $script
 *
 * @return string
 */
function renderScriptTag(_WP_Dependency $script): string
{
    $uri = getAssetUri($script);
    $uri = resolveRelativeUri($uri, Asset::SCRIPT);

    $html = '';

    // ADD L10N
    if (!empty($script->extra['data'])) {
        $html .= sprintf(
            '<script id="%s-js-extra">%s</script>',
            $script->handle,
            $script->extra['data']
        );
    }

    if (!empty($uri)) {
        $html .= sprintf(
            '<script id="%s-js" type="text/javascript" src="%s"></script>',
            $script->handle,
            esc_attr($uri)
        );
    }

    return $html;
}

/**
 * @since 0.6
 *
 * @param _WP_Dependency $style
 *
 * @return string
 */
function renderStyleTag(_WP_Dependency $style): string
{
    $uri = getAssetUri($style);
    $uri = resolveRelativeUri($uri, Asset::STYLE);

    return empty($uri)
        ? ''
        : sprintf(
            '<link id="%s-css" rel="stylesheet" href="%s" media="%s" />',
            $style->handle,
            esc_attr($uri),
            esc_attr($style->args)
        );
}
