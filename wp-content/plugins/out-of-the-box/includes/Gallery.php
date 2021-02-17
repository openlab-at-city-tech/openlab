<?php

namespace TheLion\OutoftheBox;

class Gallery
{
    /**
     * @var \TheLion\OutoftheBox\Processor
     */
    private $_processor;
    private $_search = false;

    public function __construct(Processor $_processor)
    {
        $this->_processor = $_processor;
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        return $this->_processor;
    }

    public function get_images_list()
    {
        $recursive = ('1' === $this->get_processor()->get_shortcode_option('folderthumbs'));
        $this->_folder = $this->get_processor()->get_client()->get_folder(null, true, $recursive);

        if ((false !== $this->_folder)) {
            $this->renderImagesList();
        }
    }

    public function search_image_files()
    {
        $this->_search = true;
        $input = mb_strtolower($_REQUEST['query'], 'UTF-8');
        $this->_folder = $this->get_processor()->get_client()->search($input);

        if ((false !== $this->_folder)) {
            $this->renderImagesList();
        }
    }

    public function setFolder($folder)
    {
        $this->_folder = $folder;
    }

    public function renderImagesList()
    {
        // Create HTML Filelist
        $imageslist_html = '';

        // Add 'back to Previous folder' if needed
        if ((false === $this->_search) && (strtolower($this->_folder->get_path()) !== strtolower($this->get_processor()->get_root_folder()))) {
            $foldername = basename($this->_folder->get_path());
            $location = str_replace('\\', '/', (dirname($this->get_processor()->get_requested_path())));

            $parent_folder_entry = new Entry();
            $parent_folder_entry->set_id('Previous Folder');
            $parent_folder_entry->set_name(__('Previous folder', 'wpcloudplugins'));
            $parent_folder_entry->set_path($location);
            $parent_folder_entry->set_path_display($location);
            $parent_folder_entry->set_is_dir(true);
            $parent_folder_entry->set_parent_folder(true);
            $parent_folder_entry->set_icon($this->get_processor()->get_setting('icon_set').'128x128/prev.png');
        }

        if ('-1' !== $this->get_processor()->get_shortcode_option('max_files') && $this->_folder->has_children()) {
            $children = $this->_folder->get_children();
            $children_sliced = array_slice($children, 0, (int) $this->get_processor()->get_shortcode_option('max_files'));
            $this->_folder->set_children($children_sliced);
        }

        if ($this->_folder->has_children()) {
            $hasfilesorfolders = false;

            $imageslist_html = "<div class='images image-collage'>";
            foreach ($this->_folder->get_children() as $item) {
                // Render folder div
                if ($item->is_dir()) {
                    $imageslist_html .= $this->renderDir($item);

                    if (!$item->is_parent_folder()) {
                        $hasfilesorfolders = true;
                    }
                }
            }
        }

        $imageslist_html .= $this->renderNewFolder();

        if ($this->_folder->has_children()) {
            $i = 0;
            foreach ($this->_folder->get_children() as $item) {
                // Render file div
                if ($item->is_file()) {
                    $hidden = (('0' !== $this->get_processor()->get_shortcode_option('maximages')) && ($i >= $this->get_processor()->get_shortcode_option('maximages')));
                    $imageslist_html .= $this->renderFile($item, $hidden);
                    $hasfilesorfolders = true;
                    ++$i;
                }
            }

            $imageslist_html .= '</div>';
        } else {
            if (true === $this->_search) {
                $imageslist_html .= '<div class="no_results">'.__('This folder is empty', 'wpcloudplugins').'</div>';
            }
        }

        // Create HTML Filelist title
        $file_path = '<ol class="wpcp-breadcrumb">';
        $folder_path = array_filter(explode('/', $this->get_processor()->get_requested_path()));
        $root_folder = $this->get_processor()->get_root_folder();
        $current_folder = basename($this->get_processor()->get_requested_path());
        $current_folder = empty($current_folder) ? '/' : $current_folder;
        $location = '';

        $file_path .= "<li class='first-breadcrumb'><a href='javascript:void(0)' class='folder current_folder'  data-url='".rawurlencode('/')."'>".$this->get_processor()->get_shortcode_option('root_text').'</a></li>';

        if (count($folder_path) > 0 && (false === $this->_search || 'parent' === $this->get_processor()->get_shortcode_option('searchfrom'))) {
            foreach ($folder_path as $parent_folder) {
                $location .= '/'.$parent_folder;

                if ($parent_folder === $current_folder && '' !== $this->_folder->get_name()) {
                    $file_path .= "<li><a href='javascript:void(0)' class='folder'  data-url='".rawurlencode($location)."'>".$this->_folder->get_name().'</a></li>';
                } else {
                    $file_path .= "<li><a href='javascript:void(0)' class='folder'  data-url='".rawurlencode($location)."'>".$parent_folder.'</a></li>';
                }
            }
        }
        if (true === $this->_search) {
            $file_path .= "<li><a href='javascript:void(0)' class='folder'>".sprintf(__('Results for %s', 'wpcloudplugins'), "'".$_REQUEST['query']."'").'</a></li>';
        }

        $file_path .= '</ol>';

        if (true === $this->_search) {
            $expires = 0;
        } else {
            $expires = time() + 60 * 5;
        }

        $response = json_encode([
            'lastpath' => rawurlencode($this->get_processor()->get_last_path()),
            'accountId' => null,
            'virtual' => false,
            'breadcrumb' => $file_path,
            'html' => $imageslist_html,
            'expires' => $expires, ]);

        $cached_request = new CacheRequest($this->get_processor());
        $cached_request->add_cached_response($response);

        echo $response;

        die();
    }

    public function getThumbnailsForDir(Entry $item, $thumbnails = [], $totalthumbs = 3)
    {
        if ($item->has_children()) {
            // First select the thumbnails in the folder itself
            foreach ($item->get_children() as $folder_child) {
                if (count($thumbnails) === $totalthumbs) {
                    return $thumbnails;
                }

                if (true === $folder_child->has_own_thumbnail()) {
                    $thumbnails[] = $folder_child;

                    continue;
                }
            }

            // Secondly select the thumbnails in the folder sub folders
            foreach ($item->get_children() as $folder_child) {
                if (count($thumbnails) === $totalthumbs) {
                    return $thumbnails;
                }

                if ($folder_child->is_dir()) {
                    $thumbnails = $this->getThumbnailsForDir($folder_child, $thumbnails, $totalthumbs);
                }
            }
        }

        return $thumbnails;
    }

    public function renderDir(Entry $item)
    {
        $return = '';

        $target_height = $this->get_processor()->get_shortcode_option('targetheight');
        $target_width = round($target_height * (4 / 3));

        if ($item->is_parent_folder()) {
            $return .= "<div class='image-container image-folder' data-id='".$item->get_id()."' data-url='".rawurlencode($item->get_path_display())."' data-name='".$item->get_basename()."'>";
        } else {
            $classmoveable = ($this->get_processor()->get_user()->can_move()) ? 'moveable' : '';
            $return .= "<div class='image-container image-folder entry {$classmoveable}' data-url='".rawurlencode($item->get_path_display())."' data-name='".$item->get_basename()."'>";
        }
        $return .= "<a title='".$item->get_name()."'>";
        $return .= "<div class='preloading'></div>";
        $return .= "<img class='image-folder-img' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' width='{$target_width}' height='{$target_height}' style='width:{$target_width}px !important;height:{$target_height}px !important; '/>";

        if ('1' === $this->get_processor()->get_shortcode_option('folderthumbs')) {
            $thumbnail_entries = $this->getThumbnailsForDir($item);

            if (count($thumbnail_entries) > 0) {
                foreach ($thumbnail_entries as $key => $entry) {
                    $i = $key + 1;
                    $thumbnail_url = $this->get_processor()->get_client()->get_thumbnail($entry, true, round($target_width * 1.5), round($target_height * 1.5));
                    $return .= "<div class='folder-thumb thumb{$i}' style='width:".$target_width.'px;height:'.$target_height.'px;background-image: url('.$thumbnail_url.")'></div>";
                }
            }
        }

        $text = $item->get_name();
        $text = apply_filters('outofthebox_gallery_entry_text', $text, $item, $this);
        $return .= "<div class='folder-text'><i class='fas fa-folder'></i>&nbsp;&nbsp;".$text.'</div>';

        $return .= '</a>';

        if (!$item->is_parent_folder()) {
            $return .= "<div class='entry-info'>";
            $return .= $this->renderDescription($item);
            $return .= $this->renderButtons($item);
            $return .= $this->renderActionMenu($item);

            if ($this->get_processor()->get_user()->can_download_zip() || $this->get_processor()->get_user()->can_delete_files() || $this->get_processor()->get_user()->can_move_files()) {
                $return .= "<div class='entry_checkbox entry-info-button '><input type='checkbox' name='selected-files[]' class='selected-files' value='".$item->get_id()."' id='checkbox-info-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'/><label for='checkbox-info-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'></label></div>";
            }

            $return .= '</div>';
        }

        $return .= "<div class='entry-top-actions'>";

        $return .= $this->renderButtons($item);
        $return .= $this->renderActionMenu($item);

        if ($this->get_processor()->get_user()->can_download_zip() || $this->get_processor()->get_user()->can_delete_folders() || $this->get_processor()->get_user()->can_move_folders()) {
            $return .= "<div class='entry_checkbox entry-info-button '><input type='checkbox' name='selected-files[]' class='selected-files' value='".$item->get_id()."' id='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'/><label for='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'></label></div>";
        }

        $return .= '</div>';

        $return .= "</div>\n";

        return $return;
    }

    public function renderFile(Entry $item, $hidden = false)
    {
        $hidden_class = ($hidden) ? 'hidden' : '';
        $target_height = $this->get_processor()->get_shortcode_option('targetheight');

        // API call doesn't return image sizes bu default, so initially crop the images to get this working inside the gallyer grid)
        $height = $target_height;
        $width = $target_height;

        $thumbnail_url = $this->get_processor()->get_client()->get_thumbnail($item, true, 0, round($height * 1.5), true);

        // If we do have dimension data available, use that instead
        $cached_entry = $this->get_processor()->get_cache()->get_node_by_id($item->get_id());
        if (!empty($cached_entry)) {
            $media_height = $cached_entry->get_media_info('height');
            $media_width = $cached_entry->get_media_info('width');

            if (!empty($media_height) && !empty($media_width)) {
                $width = round(($target_height / $media_height) * $media_width);
                $thumbnail_url = $this->get_processor()->get_client()->get_thumbnail($item, true, 0, round($height * 1.5));
            }
        }

        if ((!empty($_REQUEST['deeplink'])) && (md5($item->get_id()) === $_REQUEST['deeplink'])) {
            $class = ' deeplink';
        }

        $classmoveable = ($this->get_processor()->get_user()->can_move()) ? 'moveable' : '';
        $return = "<div class='image-container {$hidden_class} entry {$classmoveable}' data-id='".$item->get_id()."' data-url='".rawurlencode($item->get_path_display())."' data-name='".$item->get_name()."'>";

        $thumbnail = 'data-options="thumbnail: \''.$thumbnail_url.'\'"';
        $class = 'ilightbox-group';
        $target = '';

        $url = OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-preview&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();
        if ($this->get_processor()->get_client()->has_shared_link($item)) {
            $url = $this->get_processor()->get_client()->get_shared_link($item);
            $url = $url.'?raw=1';
        } elseif ($this->get_processor()->get_client()->has_temporarily_link($item)) {
            $url = $this->get_processor()->get_client()->get_temporarily_link($item);
        }

        // If previewinline attribute is set, open image in new window
        if ('0' === $this->get_processor()->get_shortcode_option('previewinline')) {
            $url = str_replace('?dl=1', '?raw=1', $url);
            $class = '';
            $target = ' target="_blank" ';
        }

        // Use preview thumbnail or raw  file
        if ('thumbnail' === $this->get_processor()->get_setting('loadimages')) {
            $url = $this->get_processor()->get_client()->get_thumbnail($item, true, 1024, 768);
        }

        $caption = '<span data-id="'.$item->get_id().'"></span>';

        $return .= "<a href='".$url."' title='".htmlspecialchars($item->get_name(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES)."' {$target} class='{$class}' data-type='image' data-caption='{$caption}' {$thumbnail} rel='ilightbox[".$this->get_processor()->get_listtoken()."]'><span class='image-rollover'></span>";

        $return .= "<div class='preloading'></div>";
        $return .= "<img referrerPolicy='no-referrer' class='preloading' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='".$thumbnail_url."' data-src-retina='".$thumbnail_url."' width='{$width}' height='{$height}' style='width:{$width}px !important;height:{$height}px !important; '/>";

        $text = '';
        if ('1' === $this->get_processor()->get_shortcode_option('show_filenames')) {
            $text = $item->get_basename();
            $text = apply_filters('outofthebox_gallery_entry_text', $text, $item, $this);
            $return .= "<div class='entry-text'>".$text.'</div>';
        }

        $return .= '</a>';

        if (false === empty($item->description)) {
            $return .= '<div class="entry-inline-description '.('1' === $this->get_processor()->get_shortcode_option('show_descriptions_on_top') ? ' description-visible ' : '').('1' === $this->get_processor()->get_shortcode_option('show_filenames') ? ' description-above-name ' : '').'"><span>'.nl2br($item->get_description()).'</span></div>';
        }

        $return .= "<div class='entry-info'>";
        $return .= "<div class='entry-info-name'>";
        $caption_description = ((!empty($item->description)) ? $item->get_description() : $item->get_name());
        $caption = apply_filters('outofthebox_gallery_lightbox_caption', $caption_description, $item, $this);
        $return .= '<span>'.$caption.'</span></div>';
        $return .= $this->renderButtons($item);
        $return .= "</div>\n";

        $return .= "<div class='entry-top-actions'>";

        if ('1' === $this->get_processor()->get_shortcode_option('show_filenames')) {
            $return .= $this->renderDescription($item);
        }

        $return .= $this->renderButtons($item);
        $return .= $this->renderActionMenu($item);

        if ($this->get_processor()->get_user()->can_download_zip() || $this->get_processor()->get_user()->can_delete_files() || $this->get_processor()->get_user()->can_move_files()) {
            $return .= "<div class='entry_checkbox entry-info-button '><input type='checkbox' name='selected-files[]' class='selected-files' value='".$item->get_id()."' id='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'/><label for='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'></label></div>";
        }

        $return .= '</div>';

        $return .= "</div>\n";

        return $return;
    }

    public function renderDescription($item)
    {
        $html = '';

        if ($item->is_dir()) {
            return $html;
        }

        $has_description = (false === empty($item->description));

        $metadata = [
            'modified' => "<i class='fas fa-history'></i> ".$item->get_last_edited_str(),
            'size' => ($item->get_size() > 0) ? Helpers::bytes_to_size_1024($item->get_size()) : '',
        ];

        $html .= "<div class='entry-info-button entry-description-button ".(($has_description) ? '-visible' : '')."' tabindex='0'><i class='fas fa-info-circle'></i>\n";
        $html .= "<div class='tippy-content-holder'>";
        $html .= "<div class='description-textbox'>";
        $html .= ($has_description) ? "<div class='description-text'>".nl2br($item->get_description()).'</div>' : '';
        $html .= "<div class='description-file-info'>".implode(' &bull; ', array_filter($metadata)).'</div>';

        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public function renderButtons($item)
    {
        $html = '';

        if ($this->get_processor()->get_user()->can_share()) {
            $html .= "<div class='entry-info-button entry_action_shortlink' title='".__('Direct link', 'wpcloudplugins')."' tabindex='0'><i class='fas fa-share-alt'></i>\n";
            $html .= '</div>';
        }

        if ($this->get_processor()->get_user()->can_deeplink()) {
            $html .= "<div class='entry-info-button entry_action_deeplink' title='".__('Share', 'wpcloudplugins')."' tabindex='0'><i class='fas fa-link'></i>\n";
            $html .= '</div>';
        }

        if ($this->get_processor()->get_user()->can_download() && $item->is_file()) {
            $html .= "<div class='entry-info-button entry_action_download' title='".__('Download', 'wpcloudplugins')."' tabindex='0'><a href='".OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken()."&dl=1' download='".$item->get_name()."' class='entry_action_download' title='".__('Download', 'wpcloudplugins')."'><i class='fas fa-arrow-down'></i></a>\n";
            $html .= '</div>';
        }

        return $html;
    }

    public function renderActionMenu($item)
    {
        $html = '';

        $usercanshare = $this->get_processor()->get_user()->can_share() && true === $item->get_permission('canshare');
        $usercandeeplink = $this->get_processor()->get_user()->can_deeplink();
        $usercanrename = (($item->is_dir()) ? $this->get_processor()->get_user()->can_rename_folders() : $this->get_processor()->get_user()->can_rename_files()) && true === $item->get_permission('canrename');
        $usercanmove = (($item->is_dir()) ? $this->get_processor()->get_user()->can_move_folders() : $this->get_processor()->get_user()->can_move_files()) && true === $item->get_permission('canmove');
        $usercandelete = (($item->is_dir()) ? $this->get_processor()->get_user()->can_delete_folders() : $this->get_processor()->get_user()->can_delete_files()) && true === $item->get_permission('candelete');

        $filename = (('1' === $this->get_processor()->get_shortcode_option('show_ext')) ? $item->get_name() : $item->get_basename());

        // Download
        if (($item->is_file()) && ($this->get_processor()->get_user()->can_download())) {
            $html .= "<li><a href='".OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken()."&dl=1' data-filename='".$filename."' class='entry_action_download' title='".__('Download', 'wpcloudplugins')."'><i class='fas fa-arrow-down '></i>&nbsp;".__('Download', 'wpcloudplugins').'</a></li>';
        }

        if (($this->get_processor()->get_user()->can_download()) && $item->is_dir() && '1' === $this->get_processor()->get_shortcode_option('can_download_zip')) {
            $html .= "<li><a class='entry_action_download' download='".$item->get_name()."' data-filename='".$filename."' title='".__('Download', 'wpcloudplugins')."'><i class='fas fa-arrow-down '></i>&nbsp;".__('Download', 'wpcloudplugins').'</a></li>';
        }

        // Move
        if ($usercanmove) {
            $html .= "<li><a class='entry_action_move' title='".__('Move to', 'wpcloudplugins')."'><i class='fas fa-folder-open '></i>&nbsp;".__('Move to', 'wpcloudplugins').'</a></li>';
        }

        // Delete
        if ($usercandelete) {
            $html .= "<li><a class='entry_action_delete' title='".__('Delete', 'wpcloudplugins')."'><i class='fas fa-trash '></i>&nbsp;".__('Delete', 'wpcloudplugins').'</a></li>';
        }

        // Rename
        if ($usercanrename) {
            $html .= "<li><a class='entry_action_rename' title='".__('Rename', 'wpcloudplugins')."'><i class='fas fa-tag '></i>&nbsp;".__('Rename', 'wpcloudplugins').'</a></li>';
        }

        if ('' !== $html) {
            return "<div class='entry-info-button entry-action-menu-button' title='".__('More actions', 'wpcloudplugins')."' tabindex='0'><i class='fas fa-ellipsis-v'></i><div id='menu-".$item->get_id()."' class='entry-action-menu-button-content tippy-content-holder'><ul data-id='".$item->get_id()."' data-name='".$item->get_basename()."'>".$html."</ul></div></div>\n";
        }

        return $html;
    }

    public function renderNewFolder()
    {
        $html = '';

        if (
            false === $this->get_processor()->get_user()->can_add_folders() ||
            false === $this->_folder->get_permission('canadd') ||
            true === $this->_search ||
            '1' === $this->get_processor()->get_shortcode_option('show_breadcrumb')
            ) {
            return $html;
        }

        $height = $this->get_processor()->get_shortcode_option('targetheight');
        $html .= "<div class='image-container image-folder image-add-folder grey newfolder'>";
        $html .= "<a title='".__('Add folder', 'wpcloudplugins')."'><div class='folder-text'>".__('Add folder', 'wpcloudplugins').'</div>';
        $html .= "<img class='preloading' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='".plugins_url('css/images/folder.png', dirname(__FILE__))."' width='{$height}' height='{$height}' style='width:".$height.'px;height:'.$height."px;'/>";
        $html .= '</a>';
        $html .= "</div>\n";

        return $html;
    }
}
