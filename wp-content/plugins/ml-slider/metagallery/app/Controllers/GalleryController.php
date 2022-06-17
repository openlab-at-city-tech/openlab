<?php
/**
 * Controls Galleries
 */

namespace Extendify\MetaGallery\Controllers;

if (!defined('ABSPATH')) {
    die('No direct access.');
}

use Extendify\MetaGallery\View;
use Extendify\MetaGallery\Models\Gallery;
use Extendify\MetaGallery\App;

/**
 * The controller for galleries
 */
class GalleryController
{

    /**
     * Display the onboarding page.
     *
     * @return Extendify\MetaGallery\View
     */
    public function start()
    {
        if (!\get_option('metagallery_opened')) {
            \update_option('metagallery_opened', true);
        }

        return View::queue('start', 'landing');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Extendify\MetaGallery\View
     */
    public function index()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (isset($_GET['gallery'])) {
            // Remove the gallery param if it's set.
            \wp_safe_redirect(
                \admin_url('admin.php?page=' . \esc_attr(METAGALLERY_PAGE_NAME) . '&route=archive')
            );
            exit;
        }

        $galleries = Gallery::get()->all();
        if (!$galleries) {
            \wp_safe_redirect(
                // For now, re-route users with no galleries to the start page.
                \admin_url('admin.php?page=' . \esc_attr(METAGALLERY_PAGE_NAME) . '&route=start')
            );
            exit;
        }

        return View::queue('archive', 'landing', ['galleries' => $galleries]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (isset($_GET['gallery'])) {
            // Remove the gallery param if it's set.
            \wp_safe_redirect(
                \admin_url('admin.php?page=' . \esc_attr(METAGALLERY_PAGE_NAME) . '&route=create')
            );
            exit;
        }

        return View::queue('create');
    }

    /**
     * Store a newly created resource in storage.
     * Note: Users without Auth and capability cannot reach this method.
     *
     * @param \WP_REST_Request $request - The request.
     *
     * @return void
     */
    public function store(\WP_REST_Request $request)
    {
        // Do not pass in a default config or the like as MS does,
        // since that will tie the config to a version. Just check
        // every setting on the way out (when rendering the UI)
        // or pass in config from the front to the back!
        $gallery = new Gallery();
        $gallery->title = \sanitize_text_field($request->get_param('title'));
        $gallery->images = [];
        $gallery->settings = [];
        $id = $gallery->save();

        \wp_safe_redirect(
            \admin_url('admin.php?page=' . \esc_attr(METAGALLERY_PAGE_NAME) . '&route=single&gallery=' . \esc_attr($id))
        );
        exit;
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (!isset($_GET['gallery'])) {
            // TODO: Flash message if they tried to access a gallery that wasnt set.
            \wp_safe_redirect(
                \admin_url('admin.php?page=' . \esc_attr(METAGALLERY_PAGE_NAME) . '&route=archive')
            );
            exit;
        }

        $gallery = Gallery::get()->where(
            [
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                'p' => (string) \sanitize_text_field(\wp_unslash($_GET['gallery'])),
            ]
        )->query();

        if (!$gallery) {
            // TODO: Flash message if they tried to access a gallery doesnt exist.
            \wp_safe_redirect(
                \admin_url('admin.php?page=' . \esc_attr(METAGALLERY_PAGE_NAME) . '&route=archive')
            );
            exit;
        }

        return View::queue('single', 'main', ['gallery' => $gallery]);
    }

    // phpcs:disable
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    // public function edit($id)
    // {
    //     //
    // }
    // phpcs:enable

    /**
     * Update the specified resource in storage.
     *
     * @param  \WP_REST_Request $request - The request.
     * @return Response
     */
    public function update(\WP_REST_Request $request)
    {
        $id = $request['id'];
        if (\get_post_type($request['id']) !== 'metagallery') {
            return new \WP_Error(
                'update_unauthorized',
                \esc_html__('You are not authorized to update this Gallery', 'metagallery')
            );
        }

        // Always include the title, but use whitelist for others.
        \update_post_meta($id, 'metagallery-title', $request->get_param('title'));
        foreach ($request->get_params() as $key => $value) {
            if (in_array($key, ['settings', 'images'], true)) {
                \update_post_meta($id, 'metagallery-' . $key, json_decode($value, true));
            }
        }

        // Update gallery last modified time.
        \wp_update_post(['ID' => $id]);

        // API request.
        if ($request->get_header('x_requested_with') === 'XMLHttpRequest') {
            $g = Gallery::get()->where(['p' => $id])->query();
            // TODO: fail better.
            return isset($g[0]) ? $g[0] : [];
        }

        \wp_safe_redirect(
            \admin_url('admin.php?page=' . \esc_attr(METAGALLERY_PAGE_NAME) . '&route=single&gallery=' . \esc_attr($id))
        );
        exit;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \WP_REST_Request $request - The request.
     *
     * @return void
     */
    public function destroy(\WP_REST_Request $request)
    {
        $id = intval($request->get_param('galleryId'));
        if (\get_post_type($id) === 'metagallery') {
            \wp_trash_post($id);
        }

        // TODO: 'flash' a message?
        \wp_safe_redirect(
            \admin_url('admin.php?page=' . \esc_attr(METAGALLERY_PAGE_NAME) . '&route=archive')
        );
        exit;
    }
}
