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
        if ('POST' !== $_SERVER['REQUEST_METHOD']) {
            exit(-1);
        }

        $this->_search = true;
        $_REQUEST['query'] = esc_attr($_REQUEST['query']);

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

        $filescount = 0;
        $folderscount = 0;

        // Add 'back to Previous folder' if needed
        if ((false === $this->_search) && (strtolower($this->_folder->get_path()) !== strtolower($this->get_processor()->get_root_folder()))) {
            $foldername = basename($this->_folder->get_path());
            $location = str_replace('\\', '/', (dirname($this->get_processor()->get_requested_path())));

            $parent_folder_entry = new Entry();
            $parent_folder_entry->set_id('Previous Folder');
            $parent_folder_entry->set_name(esc_html__('Previous folder', 'wpcloudplugins'));
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
            $imageslist_html = "<div class='images image-collage'>";
            foreach ($this->_folder->get_children() as $item) {
                // Render folder div
                if ($item->is_dir()) {
                    $imageslist_html .= $this->renderDir($item);

                    if (!$item->is_parent_folder()) {
                        ++$folderscount;
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
                    ++$i;
                    ++$filescount;
                }
            }

            $imageslist_html .= '</div>';
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
            $file_path .= "<li><a href='javascript:void(0)' class='folder'>".sprintf(esc_html__('Results for %s', 'wpcloudplugins'), "'".$_REQUEST['query']."'").'</a></li>';
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
            'folderscount' => $folderscount,
            'filescount' => $filescount,
            'html' => $imageslist_html,
        ]);

        $cached_request = new CacheRequest($this->get_processor());
        $cached_request->add_cached_response($response);

        echo $response;

        exit();
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

        $has_access = $item->has_access();
        $accessible = ($has_access) ? '' : ' not-accesible ';

        if ($item->is_parent_folder()) {
            $return .= "<div class='image-container image-folder' data-id='".$item->get_id()."' data-url='".rawurlencode($item->get_path_display())."' data-name='".$item->get_basename()."'>";
        } else {
            $classmoveable = ($this->get_processor()->get_user()->can_move()) ? 'moveable' : '';
            $return .= "<div class='image-container image-folder entry {$classmoveable} {$accessible}' data-url='".rawurlencode($item->get_path_display())."' data-name='".$item->get_basename()."'>";
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
        $return .= "<div class='folder-text'><i class='eva eva-folder'></i>&nbsp;&nbsp;".$text.'</div>';

        $return .= '</a>';

        if (!$item->is_parent_folder() && $has_access) {
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
            $url = str_replace('/s/', '/s/raw/', $this->get_processor()->get_client()->get_shared_link($item));
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
        if ('thumbnail' === $this->get_processor()->get_setting('loadimages') || false === $this->get_processor()->get_user()->can_download()) {
            $url = $this->get_processor()->get_client()->get_thumbnail($item, true, 1024, 768);
        }

        $description = htmlentities($item->get_description(), ENT_QUOTES | ENT_HTML401);
        $data_description = ((!empty($item->description)) ? "data-caption='{$description}'" : '');

        $return .= "<a href='".$url."' title='".htmlspecialchars($item->get_name(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES)."' {$target} class='{$class}' data-type='image' data-entry-id='{$item->get_id()}' {$thumbnail} rel='ilightbox[".$this->get_processor()->get_listtoken()."]' {$data_description}>";

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
        $caption = apply_filters('outofthebox_gallery_lightbox_caption', $item->get_basename(), $item, $this);
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
            'modified' => "<i class='eva eva-clock-outline'></i> ".$item->get_last_edited_str(),
            'size' => ($item->get_size() > 0) ? Helpers::bytes_to_size_1024($item->get_size()) : '',
        ];

        $html .= "<div class='entry-info-button entry-description-button ".(($has_description) ? '-visible' : '')."' tabindex='0'><i class='eva eva-info-outline eva-lg'></i>\n";
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
            $html .= "<div class='entry-info-button entry_action_shortlink' title='".esc_html__('Share', 'wpcloudplugins')."' tabindex='0'><i class='eva eva-share-outline eva-lg'></i>\n";
            $html .= '</div>';
        }

        if ($this->get_processor()->get_user()->can_deeplink()) {
            $html .= "<div class='entry-info-button entry_action_deeplink' title='".esc_html__('Direct link', 'wpcloudplugins')."' tabindex='0'><i class='eva eva-link eva-lg'></i>\n";
            $html .= '</div>';
        }

        if ($this->get_processor()->get_user()->can_download() && $item->is_file()) {
            $html .= "<div class='entry-info-button entry_action_download' title='".esc_html__('Download', 'wpcloudplugins')."' tabindex='0'><a href='".OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken()."&dl=1' download='".$item->get_name()."' class='entry_action_download' title='".esc_html__('Download', 'wpcloudplugins')."'><i class='eva eva-download eva-lg'></i></a>\n";
            $html .= '</div>';
        }

        return $html;
    }

    public function renderActionMenu($item)
    {
        $html = '';

        $usercanread = $this->get_processor()->get_user()->can_download() && ($item->is_file() || '1' === $this->get_processor()->get_shortcode_option('can_download_zip'));
        $usercanshare = $this->get_processor()->get_user()->can_share() && true === $item->get_permission('canshare');
        $usercandeeplink = $this->get_processor()->get_user()->can_deeplink();
        $usercanrename = (($item->is_dir()) ? $this->get_processor()->get_user()->can_rename_folders() : $this->get_processor()->get_user()->can_rename_files()) && true === $item->get_permission('canrename');
        $usercanmove = (($item->is_dir()) ? $this->get_processor()->get_user()->can_move_folders() : $this->get_processor()->get_user()->can_move_files()) && true === $item->get_permission('canmove');
        $usercandelete = (($item->is_dir()) ? $this->get_processor()->get_user()->can_delete_folders() : $this->get_processor()->get_user()->can_delete_files()) && true === $item->get_permission('candelete');

        // Download
        if ($usercanread) {
            if ($item->is_file()) {
                $html .= "<li><a href='".OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken()."&dl=1' data-filename='".$item->get_name()."' class='entry_action_download' title='".esc_html__('Download', 'wpcloudplugins')."'><i class='eva eva-download eva-lg'></i>&nbsp;".esc_html__('Download', 'wpcloudplugins').'</a></li>';
            } else {
                $html .= "<li><a class='entry_action_download' download='".$item->get_name()."' data-filename='".$item->get_name()."' title='".esc_html__('Download', 'wpcloudplugins')."'><i class='eva eva-download eva-lg'></i>&nbsp;".esc_html__('Download', 'wpcloudplugins').'</a></li>';
            }

            if ($usercanrename || $usercanmove) {
                $html .= "<li class='list-separator'></li>";
            }
        }

        // Rename
        if ($usercanrename) {
            $html .= "<li><a class='entry_action_rename' title='".esc_html__('Rename', 'wpcloudplugins')."'><i class='eva eva-edit-2-outline eva-lg'></i>&nbsp;".esc_html__('Rename', 'wpcloudplugins').'</a></li>';
        }

        // Move
        if ($usercanmove) {
            $html .= "<li><a class='entry_action_move' title='".esc_html__('Move to', 'wpcloudplugins')."'><i class='eva eva-corner-down-right eva-lg'></i>&nbsp;".esc_html__('Move to', 'wpcloudplugins').'</a></li>';
        }

        // Delete
        if ($usercandelete) {
            $html .= "<li class='list-separator'></li>";
            $html .= "<li><a class='entry_action_delete' title='".esc_html__('Delete', 'wpcloudplugins')."'><i class='eva eva-trash-2-outline eva-lg'></i>&nbsp;".esc_html__('Delete', 'wpcloudplugins').'</a></li>';
        }

        if ('' !== $html) {
            return "<div class='entry-info-button entry-action-menu-button' title='".esc_html__('More actions', 'wpcloudplugins')."' tabindex='0'><i class='eva eva-more-vertical-outline'></i><div id='menu-".$item->get_id()."' class='entry-action-menu-button-content tippy-content-holder'><ul data-id='".$item->get_id()."' data-name='".$item->get_basename()."'>".$html."</ul></div></div>\n";
        }

        return $html;
    }

    public function renderNewFolder()
    {
        $html = '';

        if (
            false === $this->get_processor()->get_user()->can_add_folders()
            || false === $this->_folder->get_permission('canadd')
            || true === $this->_search
            || '1' === $this->get_processor()->get_shortcode_option('show_breadcrumb')
            ) {
            return $html;
        }

        $height = $this->get_processor()->get_shortcode_option('targetheight');
        $html .= "<div class='image-container image-folder image-add-folder grey newfolder'>";
        $html .= "<a title='".esc_html__('Add folder', 'wpcloudplugins')."'>";
        $html .= "<div class='folder-text'><i class='eva eva-folder-add-outline eva-lg'></i>&nbsp;&nbsp;".esc_html__('Add folder', 'wpcloudplugins').'</div>';
        $html .= "<img class='preloading' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='".plugins_url('css/images/gallery-add-folder.png', dirname(__FILE__))."' data-src-retina='".plugins_url('css/images/gallery-add-folder.png', dirname(__FILE__))."' width='{$height}' height='{$height}' style='width:".$height.'px;height:'.$height."px;'/>";
        $html .= '</a>';

        $html .= "</div>\n";

        return $html;
    }
}
