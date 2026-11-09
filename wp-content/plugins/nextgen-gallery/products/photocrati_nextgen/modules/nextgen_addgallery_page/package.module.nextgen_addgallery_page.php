<?php
/**
 * Class A_Import_Folder_Form
 *
 * @mixin C_Form
 * @adapts I_Form for import_folder context
 */
class A_Import_Folder_Form extends Mixin
{
    public function get_title()
    {
        return __('Import Folder', 'nggallery');
    }
    public function enqueue_static_resources()
    {
        wp_enqueue_style('jquery.filetree');
        wp_enqueue_style('ngg_progressbar');
        wp_enqueue_script('jquery.filetree');
        wp_enqueue_script('ngg_progressbar');
        return $this->call_parent('enqueue_static_resources');
    }
    public function render()
    {
        return $this->object->render_partial('photocrati-nextgen_addgallery_page#import_folder', ['browse_nonce' => \Imagely\NGG\Util\Security::create_nonce('nextgen_upload_image'), 'import_nonce' => \Imagely\NGG\Util\Security::create_nonce('nextgen_upload_image')], true);
    }
}
/**
 * Class A_Import_Media_Library_Form
 *
 * @mixin C_Form
 * @adapts I_Form for import_media_library context
 */
class A_Import_Media_Library_Form extends Mixin
{
    public function get_title()
    {
        return __('Import from Media Library', 'nggallery');
    }
    public function enqueue_static_resources()
    {
        wp_enqueue_media();
        wp_enqueue_script('nextgen_media_library_import-js');
        wp_enqueue_style('nextgen_media_library_import-css');
        $url = admin_url() . 'admin.php?page=nggallery-manage-gallery&mode=edit&gid={gid}';
        $i18n_array = [
            'admin_url' => admin_url(),
            'title' => __('Import Images into NextGen Gallery', 'nggallery'),
            /* translators: %s: number of images */
            'import_multiple' => __('Import %s images', 'nggallery'),
            'import_singular' => __('Import 1 image', 'nggallery'),
            /* translators: %s: gallery management URL */
            'imported_multiple' => sprintf(__('{count} images were uploaded successfully. <a href="%s" target="_blank">Manage gallery</a>', 'nggallery'), $url),
            /* translators: %s: gallery management URL */
            'imported_singular' => sprintf(__('1 image was uploaded successfully. <a href="%s" target="_blank">Manage gallery</a>', 'nggallery'), $url),
            'imported_none' => __('0 images were uploaded', 'nggallery'),
            'progress_title' => __('Importing gallery', 'nggallery'),
            'in_progress' => __('In Progress...', 'nggallery'),
            'gritter_title' => __('Upload complete. Great job!', 'nggallery'),
            'gritter_error' => __('Oops! Sorry, but an error occured. This may be due to a server misconfiguration. Check your PHP error log or ask your hosting provider for assistance.', 'nggallery'),
            'nonce' => \Imagely\NGG\Util\Security::create_nonce('nextgen_upload_image'),
        ];
        wp_localize_script('nextgen_media_library_import-js', 'ngg_importml_i18n', $i18n_array);
    }
    public function render()
    {
        $i18n = [
            'select-images-to-continue' => __('Please make a selection to continue', 'nggallery'),
            'select-opener' => __('Select images', 'nggallery'),
            /* translators: %d: number of images */
            'selected-image-import' => __('Import %d image(s)', 'nggallery'),
        ];
        return $this->object->render_partial('photocrati-nextgen_addgallery_page#import_media_library', ['i18n' => $i18n, 'galleries' => $this->object->get_galleries()], true);
    }
    public function get_galleries()
    {
        $galleries = [];
        if (\Imagely\NGG\Util\Security::is_allowed('nextgen_edit_gallery')) {
            $galleries = \Imagely\NGG\DataMappers\Gallery::get_instance()->find_all();
            if (!\Imagely\NGG\Util\Security::is_allowed('nextgen_edit_gallery_unowned')) {
                $galleries_all = $galleries;
                $galleries = [];
                foreach ($galleries_all as $gallery) {
                    if (wp_get_current_user()->ID == (int) $gallery->author) {
                        $galleries[] = $gallery;
                    }
                }
            }
        }
        return $galleries;
    }
}
/**
 * Class A_NextGen_AddGallery_Ajax
 *
 * @mixin C_Ajax_Controller
 * @adapts I_Ajax_Controller
 */
class A_NextGen_AddGallery_Ajax extends Mixin
{
    public function create_new_gallery_action()
    {
        $gallery_name = urldecode((string) $this->param('gallery_name'));
        $gallery_mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();
        $retval = ['gallery_name' => esc_html($gallery_name), 'gallery_id' => null];
        if (!$this->validate_ajax_request('nextgen_upload_image', true)) {
            $action = 'nextgen_upload_image';
            $retval['allowed'] = \Imagely\NGG\Util\Security::is_allowed($action);
            $retval['verified_token'] = !isset($_REQUEST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), $action);
            $retval['error'] = __('No permissions to upload images. Try refreshing the page or ensuring that your user account has sufficient roles/privileges.', 'nggallery');
            return $retval;
        }
        if (strlen($gallery_name) > 0) {
            $gallery = $gallery_mapper->create(['title' => $gallery_name]);
            if (!$gallery->save()) {
                $retval['error'] = $gallery->validate();
            } else {
                $retval['gallery_id'] = $gallery->id();
            }
        } else {
            $retval['error'] = __('No gallery name specified', 'nggallery');
        }
        return $retval;
    }
    public function upload_image_action()
    {
        $created_gallery = false;
        $gallery_id = intval($this->param('gallery_id'));
        $gallery_name = urldecode((string) $this->param('gallery_name'));
        $gallery_mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();
        $retval = ['gallery_name' => esc_html($gallery_name)];
        if ($this->validate_ajax_request('nextgen_upload_image', true)) {
            if (!class_exists('DOMDocument')) {
                $retval['error'] = __('Please ask your hosting provider or system administrator to enable the PHP XML module which is required for image uploads', 'nggallery');
            } else {
                // The destination arrives from the request and nothing downstream reads its
                // author: upload_zip() and upload_image() both take the id on trust. The only
                // gate above is the capability to upload *somewhere*, so without this a holder of
                // "NextGEN Upload images" alone can drop files - or a whole ZIP - into any
                // gallery on the site, where they are served publicly.
                //
                // The Add Gallery screen already filters its dropdown to galleries the caller may
                // manage (adapter.upload_images_form.php::get_galleries()), so this only refuses
                // what the UI never offered - the restriction was simply never enforced on the
                // handler. Same test and same predicate as import_media_library_action() below.
                //
                // Only for an existing gallery: $gallery_id == 0 creates one below, which takes
                // the caller as its author, so that branch is left exactly as it was. The error is
                // set without returning, so the handler's own error response still carries it.
                if ($gallery_id > 0) {
                    $destination = $gallery_mapper->find($gallery_id);
                    if (!$destination || !\Imagely\NGG\Util\Security::can_manage_owned($destination->author)) {
                        $retval['error'] = __('You do not have permission to add images to that gallery.', 'nggallery');
                    }
                }
                // We need to create a gallery.
                if ($gallery_id == 0) {
                    if (strlen($gallery_name) > 0) {
                        $gallery = $gallery_mapper->create(['title' => $gallery_name]);
                        if (!$gallery->save()) {
                            $retval['error'] = $gallery->validate();
                        } else {
                            $created_gallery = true;
                            $gallery_id = $gallery->id();
                        }
                    } else {
                        $retval['error'] = __('No gallery name specified', 'nggallery');
                    }
                }
                // Upload the image to the gallery.
                if (empty($retval['error'])) {
                    $retval['gallery_id'] = $gallery_id;
                    $settings = \Imagely\NGG\Settings\Settings::get_instance();
                    $storage = \Imagely\NGG\DataStorage\Manager::get_instance();
                    try {
                        if ($storage->is_zip()) {
                            $results = $storage->upload_zip($gallery_id);
                            if ($results) {
                                $retval = $results;
                            } else {
                                $retval['error'] = __('Failed to extract images from ZIP', 'nggallery');
                            }
                        } else {
                            $image_id = $storage->upload_image($gallery_id);
                            if ($image_id) {
                                $retval['image_ids'] = [$image_id];
                                // check if image was resized correctly.
                                if ($settings->get('imgAutoResize')) {
                                    $image_path = $storage->get_full_abspath($image_id);
                                    $image_thumb = new \Imagely\NGG\DataTypes\LegacyThumbnail($image_path, true);
                                    if ($image_thumb->error) {
                                        /* translators: %1$s: error message */
                                        $retval['error'] = sprintf(__('Automatic image resizing failed [%1$s].', 'nggallery'), $image_thumb->errmsg);
                                    }
                                }
                                // check if thumb was generated correctly.
                                $thumb_path = $storage->get_image_abspath($image_id, 'thumb');
                                if (!file_exists($thumb_path)) {
                                    $retval['error'] = __('Thumbnail generation failed.', 'nggallery');
                                }
                            } else {
                                $retval['error'] = __('Image generation failed', 'nggallery');
                            }
                        }
                    } catch (E_NggErrorException $ex) {
                        $retval['error'] = $ex->getMessage();
                        if ($created_gallery) {
                            $gallery_mapper->destroy($gallery_id);
                        }
                    } catch (RuntimeException $ex) {
                        // E_UploadException extends RuntimeException, not E_NggErrorException: its
                        // message already names the rejected file(s), so it is shown as-is rather
                        // than wrapped in "An unexpected error occurred". Matches
                        // \Imagely\NGG\REST\DataMappers\ImageREST, which catches RuntimeException first.
                        //
                        // Routed through exception_message() because not every RuntimeException
                        // carries a message: E_EntityNotFoundException is an empty class and
                        // import_image_file() throws it bare. Assigning getMessage() directly left
                        // $retval['error'] empty, which skipped the 500 header below and made a
                        // failed upload toast as a success.
                        $retval['error'] = \Imagely\NGG\Util\Sanitization::exception_message($ex, __('The image could not be imported.', 'nggallery'));
                        if ($created_gallery) {
                            $gallery_mapper->destroy($gallery_id);
                        }
                    } catch (Exception $ex) {
                        /* translators: %s: error message */
                        $retval['error'] = sprintf(__('An unexpected error occurred: %s', 'nggallery'), $ex->getMessage());
                    }
                }
            }
        } else {
            $action = 'nextgen_upload_image';
            $retval['allowed'] = \Imagely\NGG\Util\Security::is_allowed($action);
            $retval['verified_token'] = !isset($_REQUEST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), $action);
            $retval['error'] = __('No permissions to upload images. Try refreshing the page or ensuring that your user account has sufficient roles/privileges.', 'nggallery');
        }
        // Sending a 500 header is used for uppy.js to determine upload failures.
        if (!empty($retval['error'])) {
            header('HTTP/1.1 500 Internal Server Error');
        }
        return $retval;
    }
    private function get_import_root_abspath()
    {
        if (is_multisite()) {
            $root = \Imagely\NGG\DataStorage\Manager::get_instance()->get_upload_abspath();
        } else {
            $root = NGG_IMPORT_ROOT;
        }
        $root = str_replace('/', DIRECTORY_SEPARATOR, $root);
        return untrailingslashit($root);
    }
    /**
     * Whether $path resolves to the import root itself or something inside it.
     *
     * The previous test asked only whether the resolved root appeared ANYWHERE in the resolved
     * path, which is a substring match rather than containment: for a root of
     * `.../site/wp-content`, the sibling `.../site/wp-content-backup` carries the root as a prefix
     * of its own name and passed, so a caller could list and import from a backup copy of the
     * content directory sitting beside the root. Anchoring the match at offset 0 and comparing
     * trailing-slashed paths makes `wp-content-backup` fail while `wp-content/gallery` passes.
     *
     * A false from realpath() is a denial rather than a 0 == 0 match: it means the path does not
     * exist, or a symlink in it pointed outside. Same predicate as the REST twins
     * (\Imagely\NGG\REST\DataMappers\ImageREST::browse_folder() / import_folder()).
     *
     * @param string $path Candidate path, already joined onto the import root.
     * @param string $root Import root as get_import_root_abspath() reports it.
     * @return bool
     */
    private function is_within_import_root($path, $root)
    {
        $real_path = realpath($path);
        $real_root = realpath($root);
        if (false === $real_path || false === $real_root) {
            return false;
        }
        return 0 === strpos(trailingslashit(wp_normalize_path($real_path)), trailingslashit(wp_normalize_path($real_root)));
    }
    public function browse_folder_action()
    {
        $retval = [];
        $html = [];
        // check_folder_import_permission() is the gate the REST twin for this operation
        // (imagely/v1/folders/browse) already uses: it requires 'NextGEN Manage gallery' on top of
        // the upload capability and honours the 'wpmuImportFolder' network option. Its third leg,
        // 'NextGEN Import image folder', adds nothing today - nggGallery::current_user_can()
        // aliases that capability to 'NextGEN Upload images' because set_role_caps() never
        // installs it (see issue #198), which the line above already required. Those controls
        // used to live only in the screen
        // renderer - M_NextGen_AddGallery_Page::_register_adapters() registers this AJAX adapter
        // unconditionally because "AJAX operations aren't admin requests" - so this handler
        // accepted exactly what the Add Gallery screen was configured to forbid, and the nonce it
        // verifies is the one that screen prints into its own form. See issue #1093 (audit #7.2).
        if ($this->validate_ajax_request('nextgen_upload_image', true) && \Imagely\NGG\REST\DataMappers\ImageREST::check_folder_import_permission()) {
            $dir = urldecode((string) $this->param('dir'));
            if ($dir) {
                $fs = \Imagely\NGG\Util\Filesystem::get_instance();
                $root = $this->get_import_root_abspath();
                $browse_path = $fs->join_paths($root, $dir);
                if ($this->is_within_import_root($browse_path, $root)) {
                    // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
                    if (@file_exists($browse_path)) {
                        $files = scandir($browse_path);
                        natcasesort($files);
                        if (count($files) > 2) {
                            /* The 2 accounts for . and .. */
                            $html[] = '<ul class="jqueryFileTree" style="display: none;">';
                            foreach ($files as $file) {
                                $file_path = $fs->join_paths($browse_path, $file);
                                $rel_file_path = str_replace($root, '', $file_path);
                                // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
                                if (@file_exists($file_path) && $file != '.' && $file != '..' && is_dir($file_path)) {
                                    $html[] = '<li class="directory collapsed"><a href="#" rel="' . htmlentities($rel_file_path) . '/">' . htmlentities($file) . '</a></li>';
                                }
                            }
                            $html[] = '</ul>';
                        }
                        $retval['html'] = implode("\n", $html);
                    } else {
                        $retval['error'] = __('Directory does not exist.', 'nggallery');
                    }
                } else {
                    $retval['error'] = __('No permissions to browse folders. Try refreshing the page or ensuring that your user account has sufficient roles/privileges.', 'nggallery');
                }
            } else {
                $retval['error'] = __('No directory specified.', 'nggallery');
            }
        } else {
            $retval['error'] = __('No permissions to browse folders. Try refreshing the page or ensuring that your user account has sufficient roles/privileges.', 'nggallery');
        }
        return $retval;
    }
    public function import_folder_action()
    {
        $retval = [];
        // Same gate as browse_folder_action() above and as the REST twin
        // (imagely/v1/folders/import): the capability tier and the 'wpmuImportFolder' network
        // option belong next to the nonce, not in the screen renderer.
        // Without it, 'NextGEN Upload images' alone copied the image files of any directory under
        // the import root into a gallery - on multisite even with the feature switched off for the
        // whole network, which is the shipped default. See issue #1093 (audit #7.2).
        if ($this->validate_ajax_request('nextgen_upload_image', true) && \Imagely\NGG\REST\DataMappers\ImageREST::check_folder_import_permission()) {
            $folder = $this->param('folder');
            if ($folder) {
                $storage = \Imagely\NGG\DataStorage\Manager::get_instance();
                $fs = \Imagely\NGG\Util\Filesystem::get_instance();
                try {
                    $keep_files = $this->param('keep_location') == 'on';
                    $gallery_title = $this->param('gallery_title', null);
                    if (empty($gallery_title)) {
                        $gallery_title = null;
                    }
                    $root = $this->get_import_root_abspath();
                    $import_path = $fs->join_paths($root, $folder);
                    if ($this->is_within_import_root($import_path, $root)) {
                        $retval = $storage->import_gallery_from_fs($import_path, false, !$keep_files, $gallery_title);
                        if (!$retval) {
                            $retval = ['error' => 'Could not import folder. No images found.'];
                        } elseif (!empty($retval['skipped_files'])) {
                            // A per-file failure is handled inside import_gallery_from_fs() so one
                            // bad file no longer aborts the batch - which also means the exception
                            // that used to land in the catch arms below never reaches them. Without
                            // this the screen reports a plain "Successfully imported N images" for
                            // an import that partly failed.
                            // Escaped here, at the producer: the completion toast hands this string
                            // to $.gritter.add({text:...}), which splices it into raw markup, so a
                            // file name is page markup unless it is escaped first. Escaping the
                            // list covers both the warning and the error built from it below.
                            $retval['skipped_files'] = array_map('esc_html', $retval['skipped_files']);
                            $skipped_label = implode(', ', $retval['skipped_files']);
                            if (empty($retval['image_ids'])) {
                                $retval['error'] = esc_html(sprintf(
                                    /* translators: %s: comma-separated list of file names that could not be imported. */
                                    __('No images were imported. These files could not be imported: %s.', 'nggallery'),
                                    $skipped_label
                                ));
                            } else {
                                $retval['warning'] = esc_html(sprintf(
                                    /* translators: %s: comma-separated list of file names that could not be imported. */
                                    __('These files were skipped and could not be imported: %s.', 'nggallery'),
                                    $skipped_label
                                ));
                            }
                        }
                    } else {
                        $retval['error'] = __('No permissions to import folders. Try refreshing the page or ensuring that your user account has sufficient roles/privileges.', 'nggallery');
                    }
                } catch (E_NggErrorException $ex) {
                    $retval['error'] = $ex->getMessage();
                } catch (Exception $ex) {
                    $retval['error'] = __('An unexpected error occured.', 'nggallery');
                    $retval['error_details'] = $ex->getMessage();
                }
            } else {
                $retval['error'] = __('No folder specified', 'nggallery');
            }
        } else {
            $retval['error'] = __('No permissions to import folders. Try refreshing the page or ensuring that your user account has sufficient roles/privileges.', 'nggallery');
        }
        return $retval;
    }
    public function import_media_library_action()
    {
        $retval = [];
        $created_gallery = false;
        $gallery_id = intval($this->param('gallery_id'));
        $gallery_name = urldecode((string) $this->param('gallery_name'));
        $gallery_mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();
        $image_mapper = \Imagely\NGG\DataMappers\Image::get_instance();
        // Get attachment_ids directly from $_REQUEST since param() doesn't handle arrays correctly
        // phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended -- Nonce is verified in validate_ajax_request
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Values are sanitized via array_map('intval') on line 262
        $attachment_ids = isset($_REQUEST['attachment_ids']) ? wp_unslash($_REQUEST['attachment_ids']) : [];
        if (!is_array($attachment_ids)) {
            $attachment_ids = [$attachment_ids];
        }
        $attachment_ids = array_map('intval', $attachment_ids);
        // phpcs:enable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended
        if ($this->validate_ajax_request('nextgen_upload_image', true)) {
            if (empty($attachment_ids) || !is_array($attachment_ids)) {
                $retval['error'] = __('An unexpected error occured.', 'nggallery') . ' No attachment IDs provided.';
                return $retval;
            }
            // The destination arrives from the request and its author was never read, so an
            // existing gallery has to be authorized before anything is written into it. A gallery
            // created below instead takes the caller as its author, so it needs no check.
            //
            // This handler's REST twin gets the same test from check_edit_permission(); here there
            // is no permission callback, and validate_ajax_request() above authorizes only the
            // capability to upload *somewhere*.
            if (empty($retval['error']) && $gallery_id > 0) {
                // can_manage_owned(), not ImageREST::current_user_can_manage_gallery(). The latter
                // accepts stored ownership alone, which is the defect #1111 records for the legacy
                // AJAX handlers and #1004 tracks across the remaining gates - adding a call site
                // for it here would spread that rather than close it, and would disagree with the
                // featured-image sink hardened in this same change. can_manage_owned() requires a
                // live management capability first and only then considers ownership, so a
                // delegated manager whose capability was revoked no longer passes for galleries
                // they once authored.
                $destination = \Imagely\NGG\DataMappers\Gallery::get_instance()->find($gallery_id);
                if (!$destination || !\Imagely\NGG\Util\Security::can_manage_owned($destination->author)) {
                    $retval['error'] = __('You do not have permission to add images to that gallery.', 'nggallery');
                    return $retval;
                }
            }
            if (empty($retval['error']) && $gallery_id == 0) {
                if (strlen($gallery_name) > 0) {
                    $gallery = $gallery_mapper->create(['title' => $gallery_name]);
                    if (!$gallery->save()) {
                        $retval['error'] = $gallery->validate();
                    } else {
                        $created_gallery = true;
                        $gallery_id = $gallery->id();
                    }
                } else {
                    $retval['error'] = __('No gallery name specified', 'nggallery');
                }
            }
            if (empty($retval['error'])) {
                $retval['gallery_id'] = $gallery_id;
                $storage = \Imagely\NGG\DataStorage\Manager::get_instance();
                // Originals can be several times larger than the scaled derivative.
                if (function_exists('wp_raise_memory_limit')) {
                    wp_raise_memory_limit('image');
                }
                $refused = [];
                foreach ($attachment_ids as $id) {
                    try {
                        // Each id is an object reference from the request, so it needs an
                        // object-level check of its own - the same one this handler's REST twin
                        // applies. Without it the only gate is "may upload images at all", and a
                        // delegated 'NextGEN Upload images' holder can read any file in the media
                        // library, including attachments on other users' private and draft posts,
                        // and have it republished at a public URL inside a gallery.
                        //
                        // Named by id rather than by filename, because get_attached_file()
                        // resolves for an attachment this caller may not read.
                        if (!\Imagely\NGG\Util\Security::can_read_attachment($id)) {
                            // Skipped, not fatal - and emphatically not `break` with a destroy.
                            // Gallery::destroy() defaults to $with_dependencies = false, so tearing
                            // the gallery down here would delete only its row and strand the images
                            // already imported in this same request: their rows keep pointing at a
                            // gallery that no longer exists and their files stay served at public
                            // URLs. A mixed-ownership selection in the media-library picker is an
                            // ordinary thing to do, so that would be routine, not exceptional.
                            //
                            // Matches the REST twin, which collects and continues and only destroys
                            // an empty gallery it created, after the loop.
                            $refused[] = (int) $id;
                            continue;
                        }
                        // Import the attached file (the smaller derivative for a downscaled
                        // upload); the untouched original is promoted into the backup below.
                        $abspath = get_attached_file($id);
                        if (empty($abspath) || !file_exists($abspath)) {
                            $retval['error'] = __('Image file not found for attachment ID: ', 'nggallery') . $id;
                            break;
                        }
                        // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
                        $file_data = @file_get_contents($abspath);
                        // Name the gallery file after the original so '-scaled' does not leak.
                        $file_name = \Imagely\NGG\Display\I18N::mb_basename(\Imagely\NGG\Util\Filesystem::get_attachment_original_abspath($id));
                        $attachment = get_post($id);
                        if (empty($file_data)) {
                            $retval['error'] = __('Image generation failed. Could not read file: ', 'nggallery') . $abspath;
                            break;
                        }
                        $image = $storage->upload_image($gallery_id, $file_name, $file_data);
                        if ($image) {
                            // Potentially import metadata from WordPress.
                            $image = $image_mapper->find($image);
                            $storage->store_attachment_original_as_backup($image, $id);
                            if (!empty($attachment->post_excerpt)) {
                                $image->alttext = $attachment->post_excerpt;
                            }
                            if (!empty($attachment->post_content)) {
                                $image->description = $attachment->post_content;
                            }
                            $image = apply_filters('ngg_medialibrary_imported_image', $image, $attachment);
                            $image_mapper->save($image);
                            $retval['image_ids'][] = $image->{$image->id_field};
                        } else {
                            $retval['error'] = __('Image generation failed', 'nggallery');
                            break;
                        }
                    } catch (E_NggErrorException $ex) {
                        $retval['error'] = $ex->getMessage();
                        if ($created_gallery) {
                            $gallery_mapper->destroy($gallery_id);
                        }
                        break;
                    } catch (RuntimeException $ex) {
                        // Same reason as the upload handler above: E_UploadException extends
                        // RuntimeException, and its message already names the rejected file, so it
                        // is shown as-is instead of behind "An unexpected error occured."
                        // exception_message() for the same reason too - a bare
                        // E_EntityNotFoundException would otherwise blank the error and suppress
                        // the 500.
                        $retval['error'] = \Imagely\NGG\Util\Sanitization::exception_message($ex, __('The image could not be imported.', 'nggallery'));
                        if ($created_gallery) {
                            $gallery_mapper->destroy($gallery_id);
                        }
                        break;
                    } catch (Exception $ex) {
                        $retval['error'] = __('An unexpected error occured.', 'nggallery') . ' ' . $ex->getMessage();
                        if ($created_gallery) {
                            $gallery_mapper->destroy($gallery_id);
                        }
                        break;
                    }
                }
                // Report the refusals, and clean up only a gallery this request created that
                // ended up with nothing in it. Mirrors the REST twin: a gallery that kept even
                // one image must survive, because destroying it makes that image unreachable
                // from every admin screen while its file stays served.
                if (!empty($refused)) {
                    $message = sprintf(
                        /* translators: %s: comma-separated list of attachment IDs. */
                        _n('You do not have permission to import attachment ID %s, so it was skipped.', 'You do not have permission to import these attachment IDs, so they were skipped: %s.', count($refused), 'nggallery'),
                        implode(', ', $refused)
                    );
                    if (empty($retval['image_ids'])) {
                        if ($created_gallery && !$image_mapper->find_all_for_gallery($gallery_id)) {
                            $gallery_mapper->destroy($gallery_id);
                        }
                        $retval['error'] = $message;
                    } else {
                        $retval['warning'] = $message;
                    }
                }
            }
        } else {
            $retval['error'] = __('No permissions to upload images. Try refreshing the page or ensuring that your user account has sufficient roles/privileges.', 'nggallery');
        }
        if (!empty($retval['error'])) {
            return $retval;
        } else {
            $retval['gallery_name'] = esc_html($gallery_name);
        }
        return $retval;
    }
}
/**
 * Class A_NextGen_AddGallery_Controller
 *
 * @mixin C_NextGen_Admin_Page_Controller
 * @adapts I_NextGen_Admin_Page
 */
class A_NextGen_AddGallery_Controller extends Mixin
{
    public function get_page_title()
    {
        return __('Add Gallery / Images', 'nggallery');
    }
    public function get_required_permission()
    {
        return 'NextGEN Upload images';
    }
    public function enqueue_backend_resources()
    {
        $this->call_parent('enqueue_backend_resources');
        wp_enqueue_style('nextgen_addgallery_page');
        wp_enqueue_script('nextgen_addgallery_page');
        wp_enqueue_script('frame_event_publisher');
    }
    public function show_save_button()
    {
        return false;
    }
}
/**
 * Class A_NextGen_AddGallery_Pages
 *
 * @mixin C_NextGen_Admin_Page_Manager
 * @adapts I_Page_Manager
 */
class A_NextGen_AddGallery_Pages extends Mixin
{
    public function setup()
    {
        $this->object->add(NGG_ADD_GALLERY_SLUG, ['adapter' => 'A_NextGen_AddGallery_Controller', 'parent' => NGGFOLDER, 'add_menu' => true, 'before' => 'nggallery-manage-gallery']);
        return $this->call_parent('setup');
    }
}
/**
 * Class A_Upload_Images_Form
 *
 * @mixin C_Form
 * @property C_MVC_Controller|A_Upload_Images_Form $object
 * @adapts I_Form for "upload_images" context
 */
class A_Upload_Images_Form extends Mixin
{
    public function get_title()
    {
        return __('Upload Images', 'nggallery');
    }
    public function enqueue_static_resources()
    {
        wp_enqueue_script('uppy');
        wp_enqueue_script('uppy_i18n');
        wp_enqueue_style('uppy');
        wp_enqueue_script('toastify');
        wp_enqueue_style('toastify');
        wp_localize_script('uppy', 'NggUploadImages_i18n', $this->object->get_i18n_strings());
        M_Ajax::pass_data_to_js('uppy', 'NggUppyCoreSettings', $this->object->get_uppy_core_settings());
        M_Ajax::pass_data_to_js('uppy', 'NggUppyDashboardSettings', $this->object->get_uppy_dashboard_settings());
        M_Ajax::pass_data_to_js('uppy', 'NggXHRSettings', $this->object->get_uppy_xhr_settings());
    }
    public function get_uppy_note()
    {
        $core_settings = $this->object->get_uppy_core_settings();
        $max_size = $core_settings['restrictions']['maxfileSize'];
        $max_size_megabytes = round((int) $max_size / (1024 * 1024));
        /* translators: %d: maximum file size in megabytes */
        return sprintf(__('You may select files up to %dMB', 'nggallery'), $max_size_megabytes);
    }
    public function get_uppy_xhr_settings()
    {
        return apply_filters('ngg_uppy_xhr_settings', ['timeout' => intval(NGG_UPLOAD_TIMEOUT) * 1000, 'limit' => intval(NGG_UPLOAD_LIMIT), 'fieldName' => 'file']);
    }
    public function get_uppy_core_settings()
    {
        $mime = apply_filters('ngg_allowed_mime_types', NGG_DEFAULT_ALLOWED_MIME_TYPES);
        return apply_filters('ngg_uppy_core_settings', ['locale' => $this->object->get_uppy_locale(), 'restrictions' => ['maxfileSize' => wp_max_upload_size(), 'allowedFileTypes' => $this->can_upload_zips() ? array_merge($mime, ['.zip']) : get_allowed_mime_types()]]);
    }
    public function get_uppy_dashboard_settings()
    {
        return apply_filters('ngg_uppy_dashboard_settings', ['inline' => true, 'target' => '#uploader', 'width' => '100%', 'proudlyDisplayPoweredByUppy' => false, 'hideRetryButton' => true, 'note' => $this->object->get_uppy_note(), 'locale' => ['strings' => ['dropPaste' => $this->can_upload_zips() ? __('Drag image and ZIP files here or %{browse}', 'nggallery') : __('Drag image files here or %{browse}', 'nggallery')]]]);
    }
    public function get_uppy_locale()
    {
        $locale = get_locale();
        $mapping = ['ar' => 'ar_SA', 'bg' => 'bg_BG', 'zh-cn' => 'zh_CN', 'zh-tw' => 'zh_TW', 'hr' => 'hr_HR', 'cs' => 'cs_CZ', 'da' => 'da_DK', 'nl' => 'nl_NL', 'en' => 'en_US', 'fi' => 'fi_FI', 'fr' => 'fr_FR', 'gl' => 'gl_ES', 'de' => 'de_DE', 'el' => 'el_GR', 'he' => 'he_IL', 'hu' => 'hu_HU', 'is' => 'is_IS', 'id' => 'id_ID', 'it' => 'it_IT', 'ja' => 'ja_JP', 'ko' => 'ko_KR', 'fa' => 'fa_IR', 'pl' => 'pl_PL', 'pt-br' => 'pt_BR', 'pt' => 'pt_PT', 'ro' => 'ro_RO', 'ru' => 'ru_RU', 'sr' => 'sr_RS', 'sk' => 'sk_SK', 'es' => 'es_ES', 'sv' => 'sv_SE', 'th' => 'th_TH', 'tr' => 'tr_TR', 'vi' => 'vi_VN'];
        if (!empty($mapping[$locale])) {
            $locale = $mapping[$locale];
        }
        return $locale;
    }
    public function can_upload_zips()
    {
        $global_settings = \Imagely\NGG\Settings\GlobalSettings::get_instance();
        return !is_multisite() || is_multisite() && $global_settings->get('wpmuZipUpload');
    }
    public function get_i18n_strings()
    {
        return ['locale' => $this->object->get_uppy_locale(), 'no_image_uploaded' => __('No images were uploaded successfully.', 'nggallery'), 'one_image_uploaded' => __('1 image was uploaded successfully.', 'nggallery'), 'x_images_uploaded' => __('{count} images were uploaded successfully.', 'nggallery'), 'manage_gallery' => __('Manage gallery > {name}', 'nggallery'), 'image_failed' => __('Image {filename} failed to upload: {error}', 'nggallery'), 'image_warning' => __('{filename}: {warning}', 'nggallery'), 'drag_files_here' => $this->can_upload_zips() ? __('Drag image and ZIP files here or %{browse}', 'nggallery') : __('Drag image files here or %{browse}', 'nggallery')];
    }
    public function render()
    {
        return $this->object->render_partial('photocrati-nextgen_addgallery_page#upload_images', ['galleries' => $this->object->get_galleries(), 'nonce' => \Imagely\NGG\Util\Security::create_nonce('nextgen_upload_image')], true);
    }
    public function get_galleries()
    {
        $galleries = [];
        if (\Imagely\NGG\Util\Security::is_allowed('nextgen_edit_gallery')) {
            $gallery_mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();
            $galleries = $gallery_mapper->find_all();
            if (!\Imagely\NGG\Util\Security::is_allowed('nextgen_edit_gallery_unowned')) {
                $galleries_all = $galleries;
                $galleries = [];
                foreach ($galleries_all as $gallery) {
                    if (wp_get_current_user()->ID == (int) $gallery->author) {
                        $galleries[] = $gallery;
                    }
                }
            }
        }
        return $galleries;
    }
}