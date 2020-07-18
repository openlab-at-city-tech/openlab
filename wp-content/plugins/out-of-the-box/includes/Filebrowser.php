<?php

namespace TheLion\OutoftheBox;

class Filebrowser
{
    /**
     * @var \TheLion\OutoftheBox\Processor
     */
    private $_processor;
    private $_search = false;
    private $_parentfolders = [];
    private $_layout;

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

    public function get_files_list()
    {
        $this->_folder = $this->get_processor()->get_client()->get_folder(null, true);

        if ((false !== $this->_folder)) {
            $this->setLayout();
            $this->renderFileList();
        }
    }

    public function search_files()
    {
        $this->_search = true;
        $input = mb_strtolower($_REQUEST['query'], 'UTF-8');
        $this->_folder = $this->get_processor()->get_client()->search($input);

        if ((false !== $this->_folder)) {
            $this->setLayout();
            $this->renderFileList();
        }
    }

    public function setFolder($folder)
    {
        $this->_folder = $folder;
    }

    public function setLayout()
    {
        // Set layout
        $this->_layout = $this->get_processor()->get_shortcode_option('filelayout');
        if (isset($_REQUEST['filelayout'])) {
            switch ($_REQUEST['filelayout']) {
                case 'grid':
                    $this->_layout = 'grid';

                    break;
                case 'list':
                    $this->_layout = 'list';

                    break;
            }
        }
    }

    public function renderFileList()
    {
        // Create HTML Filelist
        $filelist_html = '';

        $filelist_html = "<div class='files layout-".$this->_layout."'>";
        $filelist_html .= "<div class='folders-container'>";

        // Add 'back to Previous folder' if needed
        if (
                (false === $this->_search) &&
                ('' !== $this->_folder->get_path()) &&
                (strtolower($this->_folder->get_path()) !== strtolower($this->get_processor()->get_root_folder()))
        ) {
            $foldername = basename($this->_folder->get_path());
            $location = str_replace('\\', '/', (dirname($this->get_processor()->get_requested_path())));

            $parent_folder_entry = new Entry();
            $parent_folder_entry->set_id('Previous Folder');
            $parent_folder_entry->set_name(__('Previous folder', 'outofthebox'));
            $parent_folder_entry->set_path($location);
            $parent_folder_entry->set_path_display($location);
            $parent_folder_entry->set_is_dir(true);
            $parent_folder_entry->set_parent_folder(true);
            $parent_folder_entry->set_icon(OUTOFTHEBOX_ROOTPATH.'/css/icons/32x32/folder-grey.png');

            if ('list' === $this->_layout) {
                $filelist_html .= $this->renderDirForList($parent_folder_entry);
            } elseif ('grid' === $this->_layout) {
                $filelist_html .= $this->renderDirForGrid($parent_folder_entry);
            }
        }

        // Limit the number of files if needed
        if ('-1' !== $this->get_processor()->get_shortcode_option('max_files') && $this->_folder->has_children()) {
            $children = $this->_folder->get_children();
            $children_sliced = array_slice($children, 0, (int) $this->get_processor()->get_shortcode_option('max_files'));
            $this->_folder->set_children($children_sliced);
        }

        if ($this->_folder->has_children()) {
            $hasfilesorfolders = false;

            foreach ($this->_folder->get_children() as $item) {
                // Render folder div
                if ($item->is_dir()) {
                    if ('list' === $this->_layout) {
                        $filelist_html .= $this->renderDirForList($item);
                    } elseif ('grid' === $this->_layout) {
                        $filelist_html .= $this->renderDirForGrid($item);
                    }

                    if (!$item->is_parent_folder()) {
                        $hasfilesorfolders = true;
                    }
                }
            }
        }

        if (false === $this->_search) {
            if ('list' === $this->_layout) {
                $filelist_html .= $this->renderNewFolderForList();
            } elseif ('grid' === $this->_layout) {
                $filelist_html .= $this->renderNewFolderForGrid();
            }
        }

        $filelist_html .= "</div><div class='files-container'>";

        if ($this->_folder->has_children()) {
            foreach ($this->_folder->get_children() as $item) {
                // Render files div
                if ($item->is_file()) {
                    if ('list' === $this->_layout) {
                        $filelist_html .= $this->renderFileForList($item);
                    } elseif ('grid' === $this->_layout) {
                        $filelist_html .= $this->renderFileForGrid($item);
                    }
                    $hasfilesorfolders = true;
                }
            }

            if (false === $hasfilesorfolders) {
                if ('1' === $this->get_processor()->get_shortcode_option('show_files')) {
                    $filelist_html .= $this->renderNoResults();
                }
            }
        } else {
            if ('1' === $this->get_processor()->get_shortcode_option('show_files') || true === $this->_search) {
                $filelist_html .= $this->renderNoResults();
            }
        }

        $filelist_html .= '</div></div>';

        // Create HTML Filelist title
        $file_path = '<ol class="breadcrumb">';
        $folder_path = array_filter(explode('/', $this->get_processor()->get_requested_path()));
        $root_folder = $this->get_processor()->get_root_folder();
        $current_folder = basename($this->get_processor()->get_requested_path());
        $current_folder = empty($current_folder) ? '/' : $current_folder;
        $location = '';

        $file_path .= "<li class='first-breadcrumb'><a href='javascript:void(0)".rawurlencode($location)."' class='folder current_folder'  data-url='".rawurlencode('/')."'>".$this->get_processor()->get_shortcode_option('root_text').'</a></li>';

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
            $file_path .= "<li><a href='javascript:void(0)' class='folder'>".sprintf(__('Results for %s', 'outofthebox'), "'".$_REQUEST['query']."'").'</a></li>';
        }

        $file_path .= '</ol>';

        $raw_path = '';
        if (
                (\TheLion\OutoftheBox\Helpers::check_user_role($this->get_processor()->get_setting('permissions_add_shortcodes'))) ||
                (\TheLion\OutoftheBox\Helpers::check_user_role($this->get_processor()->get_setting('permissions_add_links'))) ||
                (\TheLion\OutoftheBox\Helpers::check_user_role($this->get_processor()->get_setting('permissions_add_embedded')))
        ) {
            $raw_path = (null !== $this->_folder->get_path()) ? $this->_folder->get_path() : '';
        }

        if (true === $this->_search) {
            $expires = 0;
        } else {
            $expires = time() + 60 * 5;
        }

        $response = json_encode([
            'lastpath' => rawurlencode($this->get_processor()->get_last_path()),
            'rawpath' => $raw_path,
            'breadcrumb' => $file_path,
            'html' => $filelist_html,
            'expires' => $expires, ]);

        $cached_request = new CacheRequest($this->get_processor());
        $cached_request->add_cached_response($response);

        echo $response;
        die();
    }

    public function renderNoResults()
    {
        $html = '';

        if ('list' === $this->_layout) {
            $html .= '
  <div class="entry folder no-entries">
<div class="entry_icon">
<img src="'.OUTOFTHEBOX_ROOTPATH.'/css/images/loader_no_results.png" ></div>
<div class="entry_name"><a class="entry_link">'.__('No files or folders found', 'outofthebox').'</a></div></div>
';
        } else {
            $html .= '<div class="entry file no-entries">
<div class="entry_block">
<div class="entry_thumbnail"><div class="entry_thumbnail-view-bottom"><div class="entry_thumbnail-view-center">
<a class="entry_link"><img class="preloading" src="'.OUTOFTHEBOX_ROOTPATH.'/css/images/transparant.png" data-src="'.OUTOFTHEBOX_ROOTPATH.'/css/images/loader_no_results.png" data-src-retina="'.OUTOFTHEBOX_ROOTPATH.'/css/images/loader_no_results.png"></a></div></div></div>
<div class="entry_name"><a class="entry_link"><div class="entry-name-view"><span><strong>'.__('No files or folders found', 'outofthebox').'</strong></span></div></a></div>
</div>
</div>';
        }

        return $html;
    }

    public function renderDirForList(Entry $item)
    {
        $return = '';

        $classmoveable = ($this->get_processor()->get_user()->can_move()) ? 'moveable' : '';
        $style = ($item->is_parent_folder()) ? ' previous ' : '';

        $return .= "<div class='entry folder {$classmoveable} {$style}' data-id='".$item->get_id()."' data-url='".rawurlencode($item->get_path_display())."' data-name=\"".htmlspecialchars($item->get_basename(), ENT_QUOTES | ENT_HTML401, 'UTF-8')."\">\n";
        $return .= "<div class='entry_icon' data-url='".rawurlencode($item->get_path_display())."'><img src='".$item->get_icon()."'/></div>\n";

        if (false === $item->is_parent_folder()) {
            if ('linkto' === $this->get_processor()->get_shortcode_option('mcepopup') || 'linktobackendglobal' === $this->get_processor()->get_shortcode_option('mcepopup')) {
                $return .= "<div class='entry_linkto'>\n";
                $return .= '<span>'."<input class='button-secondary' type='submit' title='".__('Select folder', 'outofthebox')."' value='".__('Select folder', 'outofthebox')."'>".'</span>';
                $return .= '</div>';
            }

            if ($this->get_processor()->get_user()->can_download_zip() || $this->get_processor()->get_user()->can_delete_folders() || $this->get_processor()->get_user()->can_move_folders()) {
                $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".rawurlencode($item->get_name())."'/></div>";
            }

            if ('links' === $this->get_processor()->get_shortcode_option('mcepopup')) {
                $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".rawurlencode($item->get_name())."'/></div>";
            }

            $return .= "<div class='entry_edit'>";
            $return .= $this->renderEditItem($item);
            $return .= '</div>';

            $return .= "<div class='entry_name'><a class='entry_link' title='{$item->get_basename()}'><span>".$item->get_basename().'</span></a></div>';
        } else {
            $return .= "<div class='entry_name'><a class='entry_link' title='{$item->get_basename()}'><span>".$item->get_name().'</span></a></div>';
        }

        $return .= "</div>\n";

        return $return;
    }

    public function renderDirForGrid(Entry $item)
    {
        $return = '';

        $classmoveable = ($this->get_processor()->get_user()->can_move()) ? 'moveable' : '';
        $style = ($item->is_parent_folder()) ? ' previous ' : '';

        $return .= "<div class='entry folder {$classmoveable}  {$style}' data-id='".$item->get_id()."' data-url='".rawurlencode($item->get_path_display())."' data-name='".htmlspecialchars($item->get_basename(), ENT_QUOTES | ENT_HTML401, 'UTF-8')."'>\n";
        if (false === $item->is_parent_folder()) {
            if ('linkto' === $this->get_processor()->get_shortcode_option('mcepopup') || 'linktobackendglobal' === $this->get_processor()->get_shortcode_option('mcepopup')) {
                $return .= "<div class='entry_linkto'>\n";
                $return .= '<span>'."<input class='button-secondary' type='submit' title='".__('Select folder', 'outofthebox')."' value='".__('Select folder', 'outofthebox')."'>".'</span>';
                $return .= '</div>';
            }
        }

        $return .= "<div class='entry_block'>\n";

        if (false === $item->is_parent_folder()) {
            $return .= "<div class='entry_edit'>";

            if ($this->get_processor()->get_user()->can_download_zip() || $this->get_processor()->get_user()->can_delete_folders() || $this->get_processor()->get_user()->can_move_folders()) {
                $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".rawurlencode($item->get_name())."'/></div>";
            }

            if (('links' === $this->get_processor()->get_shortcode_option('mcepopup'))) {
                $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".rawurlencode($item->get_name())."'/></div>";
            }

            $return .= $this->renderEditItem($item);
            $return .= '</div>';
        }

        $return .= "<div class='entry_thumbnail'><div class='entry_thumbnail-view-bottom'><div class='entry_thumbnail-view-center'>\n";
        $return .= "<a class='entry_link' title='{$item->get_basename()}'><div class='preloading'></div><img class='preloading' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='".$item->get_icon_large()."' data-src-retina='".$item->get_icon_retina()."'/></a>";
        $return .= "</div></div></div>\n";
        $return .= "<div class='entry_name'><a class='entry_link' title='{$item->get_basename()}'><div class='entry-name-view'><span>";

        $return .= (($item->is_parent_folder()) ? '<strong>'.$item->get_name().'</strong>' : $item->get_name()).' </span></div></a>';
        $return .= "</div>\n";
        $return .= "</div>\n";
        $return .= "</div>\n";

        return $return;
    }

    public function renderFileForList(Entry $item)
    {
        $return = '';
        $classmoveable = ($this->get_processor()->get_user()->can_move()) ? 'moveable' : '';

        $thumbnail_url = ($item->has_own_thumbnail() ? $this->get_processor()->get_client()->get_thumbnail($item, true, 256, 256) : false);

        $return .= "<div class='entry file {$classmoveable}' data-id='".$item->get_id()."' data-url='".rawurlencode($item->get_path_display())."' data-name=\"".$item->get_name().'" '.((!empty($thumbnail_url)) ? "data-tooltip=''" : '').">\n";
        $return .= "<div class='entry_icon'><img src='".$item->get_icon()."'/></div>";

        $link = $this->renderFileNameLink($item);
        $title = $link['filename'].((('1' === $this->get_processor()->get_shortcode_option('show_filesize')) && ($item->get_size() > 0)) ? ' ('.\TheLion\OutoftheBox\Helpers::bytes_to_size_1024($item->get_size()).')' : '&nbsp;');

        if ($this->get_processor()->get_user()->can_download_zip() || $this->get_processor()->get_user()->can_delete_files() || $this->get_processor()->get_user()->can_move_files()) {
            $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".rawurlencode($item->get_name())."'/></div>";
        }

        if (in_array($this->get_processor()->get_shortcode_option('mcepopup'), ['links', 'embedded'])) {
            $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".rawurlencode($item->get_name())."'/></div>";
        }

        $return .= "<div class='entry_edit_placheholder'><div class='entry_edit'>";
        $return .= $this->renderEditItem($item);
        $return .= '</div></div>';

        $download_url = OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken().'&dl=1';
        $caption = ($this->get_processor()->get_user()->can_download()) ? '<a href="'.$download_url.'" title="'.__('Download', 'outofthebox').'"><i class="fas fa-arrow-circle-down" aria-hidden="true"></i></a>&nbsp' : '';
        $caption .= $link['filename'];

        $add_caption = true;
        if (in_array($item->get_extension(), ['mp4', 'm4v', 'ogg', 'ogv', 'webmv', 'mp3', 'm4a', 'ogg', 'oga', 'wav'])) {
            // Don't overlap the player controls with the caption
            $add_caption = false;
        }

        $return .= "<a {$link['url']} {$link['target']} class='{$link['class']}' title='{$title}' {$link['lightbox']} {$link['onclick']} data-filename='{$link['filename']}' ".(($add_caption) ? "data-caption='{$caption}'" : '').'>';

        if ('1' === $this->get_processor()->get_shortcode_option('show_filesize')) {
            $return .= "<div class='entry_size'>".\TheLion\OutoftheBox\Helpers::bytes_to_size_1024($item->get_size()).'</div>';
        }

        if ('1' === $this->get_processor()->get_shortcode_option('show_filedate')) {
            $return .= "<div class='entry_lastedit'>".$item->get_last_edited_str().'</div>';
        }

        if (!empty($thumbnail_url)) {
            $return .= "<div class='description_textbox'>";
            $return .= "<div class='preloading'></div>";
            $return .= "<img class='preloading hidden' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='".$thumbnail_url."' data-src-retina='".$thumbnail_url."' data-src-backup='".$item->get_icon_large()."'/>";
            $return .= '</div>';
        }

        $return .= "<div class='entry_name'><span>".$link['filename'];

        if (('shortcode' === $this->get_processor()->get_shortcode_option('mcepopup')) && (in_array($item->get_extension(), ['mp4', 'm4v', 'ogg', 'ogv', 'webmv', 'mp3', 'm4a', 'oga', 'wav', 'webm']))) {
            $return .= "&nbsp;<a class='entry_media_shortcode'><i class='fas fa-code'></i></a>";
        }

        $return .= '</span></div>';

        if (true === $this->_search) {
            $return .= "<div class='entry_foundpath'>".$item->get_path().'</div>';
        }

        $return .= '</a>';

        $return .= $link['lightbox_inline'];
        $return .= "</div>\n";

        return $return;
    }

    public function renderFileForGrid(Entry $item)
    {
        $link = $this->renderFileNameLink($item);
        $title = $link['filename'].((('1' === $this->get_processor()->get_shortcode_option('show_filesize')) && ($item->get_size() > 0)) ? ' ('.Helpers::bytes_to_size_1024($item->get_size()).')' : '&nbsp;');

        $classmoveable = ($this->get_processor()->get_user()->can_move()) ? 'moveable' : '';

        $return = '';
        $return .= "<div class='entry file {$classmoveable}' data-id='".$item->get_id()."' data-url='".rawurlencode($item->get_path_display())."' data-name='".htmlspecialchars($item->get_name(), ENT_QUOTES | ENT_HTML401, 'UTF-8')."'>\n";
        $return .= "<div class='entry_block'>\n";

        $return .= "<div class='entry_edit'>";

        if ($this->get_processor()->get_user()->can_download_zip() || $this->get_processor()->get_user()->can_delete_files() || $this->get_processor()->get_user()->can_move_files()) {
            $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".rawurlencode($item->get_name())."'/></div>";
        }

        if ((in_array($this->get_processor()->get_shortcode_option('mcepopup'), ['links', 'embedded']))) {
            $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".rawurlencode($item->get_name())."'/></div>";
        }

        $return .= $this->renderEditItem($item);
        $return .= '</div>';

        $download_url = OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken().'&dl=1';
        $caption = ($this->get_processor()->get_user()->can_download()) ? '<a href="'.$download_url.'" title="'.__('Download', 'outofthebox').'"><i class="fas fa-arrow-circle-down" aria-hidden="true"></i></a>&nbsp' : '';
        $caption .= $link['filename'];

        $add_caption = true;
        if (in_array($item->get_extension(), ['mp4', 'm4v', 'ogg', 'ogv', 'webmv', 'mp3', 'm4a', 'ogg', 'oga', 'wav'])) {
            // Don't overlap the player controls with the caption
            $add_caption = false;
        }

        $return .= '<a '.$link['url'].' '.$link['target']." class='entry_link ".$link['class']."' ".$link['onclick']." title='".$title."' ".$link['lightbox']." data-filename='".$link['filename']."' ".(($add_caption) ? "data-caption='{$caption}'" : '').'>';

        $return .= "<div class='entry_thumbnail'><div class='entry_thumbnail-view-bottom'><div class='entry_thumbnail-view-center'>\n";

        $thumbnail_url = ($item->has_own_thumbnail() ? $this->get_processor()->get_client()->get_thumbnail($item, true, 640, 480) : $item->get_icon_retina());

        $return .= "<div class='preloading'></div>";
        $return .= "<img class='preloading' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='".$thumbnail_url."' data-src-retina='".$thumbnail_url."' data-src-backup='".$item->get_icon_retina()."'/>";
        $return .= "</div></div></div>\n";

        if ($duration = $item->get_media('duration')) {
            $return .= "<div class='entry-duration'><i class='fas fa-play fa-xs' ></i> ".Helpers::convert_ms_to_time($duration).'</div>';
        }

        $return .= "<div class='entry_name'>";

        $return .= "<div class='entry-name-view'><span>".$link['filename'].'</span></div>';
        $return .= "</div>\n";
        $return .= "</a>\n";

        $return .= $link['lightbox_inline'];

        $return .= "</div>\n";
        $return .= "</div>\n";

        return $return;
    }

    public function renderFileNameLink(Entry $item)
    {
        $class = '';
        $url = '';
        $target = '';
        $onclick = '';
        $lightbox = '';
        $lightbox_inline = '';
        $datatype = 'iframe';
        $filename = ('1' === $this->get_processor()->get_shortcode_option('show_ext')) ? $item->get_name() : $item->get_basename();

        // Check if user is allowed to preview the file
        $usercanpreview = $this->get_processor()->get_user()->can_preview() && '1' !== $this->get_processor()->get_shortcode_option('forcedownload');

        if (
                $item->is_dir() ||
                false === $item->get_can_preview_by_cloud() ||
                false === $this->get_processor()->get_user()->can_view()
        ) {
            $usercanpreview = false;
        }

        // Check if user is allowed to preview the file
        if ($usercanpreview) {
            $url = OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-preview&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();

            // Display direct links for image and media files
            if (in_array($item->get_extension(), ['jpg', 'jpeg', 'gif', 'png'])) {
                $datatype = 'image';
                if ($this->get_processor()->get_client()->has_temporarily_link($item)) {
                    $url = $this->get_processor()->get_client()->get_temporarily_link($item);
                } elseif ($this->get_processor()->get_client()->has_shared_link($item)) {
                    $url = $this->get_processor()->get_client()->get_shared_link($item).'?raw=1';
                }

                // Use preview thumbnail or raw  file
                if ('thumbnail' === $this->get_processor()->get_setting('loadimages')) {
                    $url = $this->get_processor()->get_client()->get_thumbnail($item, true, 1024, 768);
                }
            } elseif (in_array($item->get_extension(), ['mp4', 'm4v', 'ogg', 'ogv', 'webmv', 'mp3', 'm4a', 'ogg', 'oga', 'wav'])) {
                $datatype = 'inline';
                if ($this->get_processor()->get_client()->has_temporarily_link($item)) {
                    $url = $this->get_processor()->get_client()->get_temporarily_link($item);
                }
            }

            // Check if we need to preview inline
            if ('1' === $this->get_processor()->get_shortcode_option('previewinline')) {
                $class = 'entry_link ilightbox-group';
                $onclick = "sendGooglePageView('Preview', '{$item->get_name()}');";

                // Lightbox Settings
                $lightbox = "rel='ilightbox[".$this->get_processor()->get_listtoken()."]' ";
                $lightbox .= 'data-type="'.$datatype.'"';

                switch ($datatype) {
                    case 'image':
                        $lightbox .= ' data-options="thumbnail: \''.$this->get_processor()->get_client()->get_thumbnail($item, true, 256, 256).'\'"';

                        break;
                    case 'inline':
                        $id = 'ilightbox_'.$this->get_processor()->get_listtoken().'_'.md5($item->get_id());
                        $html5_element = (false === strpos($item->get_mimetype(), 'video')) ? 'audio' : 'video';
                        $icon = ($item->has_own_thumbnail() ? $this->get_processor()->get_client()->get_thumbnail($item, true, 0, 256, 256) : $item->get_icon_large());
                        $icon_256 = ($item->has_own_thumbnail() ? $this->get_processor()->get_client()->get_thumbnail($item, true, 256, 256) : $item->get_icon_large());
                        $lightbox_size = (false !== strpos($item->get_mimetype(), 'audio')) ? '' : 'width: \'85%\', height: \'85%\',';
                        $lightbox .= ' data-options="mousewheel: false, '.$lightbox_size.' thumbnail: \''.$icon.'\'"';
                        $thumbnail = ('audio' === $html5_element) ? '<div class="html5_player_thumbnail"><img src="'.$icon_256.'"/><h3>'.$item->get_basename().'</h3></div>' : '';
                        $download = ($this->get_processor()->get_user()->can_download()) ? '' : 'controlsList="nodownload"';
                        $lightbox_inline = '<div id="'.$id.'" class="html5_player" style="display:none;"><div class="html5_player_container"><div style="width:100%"><'.$html5_element.' controls '.$download.' preload="metadata"  poster="'.$icon_256.'"> <source data-src="'.$url.'" type="'.$item->get_mimetype().'">'.__('Your browser does not support HTML5. You can only download this file', 'outofthebox').'</'.$html5_element.'></div>'.$thumbnail.'</div></div>';
                        $url = '#'.$id;

                        break;
                    case 'iframe':
                        $icon_128 = ($item->has_own_thumbnail() ? $this->get_processor()->get_client()->get_thumbnail($item, true, 256, 256) : $item->get_icon_large());
                        $lightbox .= ' data-options="mousewheel: false, width: \'85%\', height: \'80%\', thumbnail: \''.$icon_128.'\'"';
                        // no break
                    default:
                        break;
                }
            } else {
                $class = 'entry_action_external_view';
                $target = '_blank';
                $onclick = "sendGooglePageView('Preview  (new window)', '{$item->get_name()}');";
            }
        } elseif (('0' === $this->get_processor()->get_shortcode_option('mcepopup')) && $this->get_processor()->get_user()->can_download()) {
            // Check if user is allowed to download file

            $url = OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();
            $class = 'entry_action_download';

            $target = ('url' === $item->get_extension()) ? '"_blank"' : $target;
        }

        if ('woocommerce' === $this->get_processor()->get_shortcode_option('mcepopup')) {
            $class = 'entry_woocommerce_link';
            $url = '';
        }

        if ($this->get_processor()->is_mobile() && 'iframe' === $datatype) {
            $lightbox = '';
            $class = 'entry_action_external_view';
            $target = '_blank';
            $onclick = "sendGooglePageView('Preview  (new window)', '{$item->get_name()}');";
        }

        if (!empty($url)) {
            $url = "href='".$url."'";
        }
        if (!empty($target)) {
            $target = "target='".$target."'";
        }
        if (!empty($onclick)) {
            $onclick = 'onclick="'.$onclick.'"';
        }

        return ['filename' => htmlspecialchars($filename, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES, 'UTF-8'), 'class' => $class, 'url' => $url, 'lightbox' => $lightbox, 'lightbox_inline' => $lightbox_inline, 'target' => $target, 'onclick' => $onclick];
    }

    public function renderEditItem(Entry $item)
    {
        $html = '';

        $usercanpreview = $this->get_processor()->get_user()->can_preview();

        if (
                $item->is_dir() ||
                false === $item->get_can_preview_by_cloud() ||
                'zip' === $item->get_extension() ||
                false === $this->get_processor()->get_user()->can_view()
        ) {
            $usercanpreview = false;
        }

        $usercanshare = $this->get_processor()->get_user()->can_share() && true === $item->get_permission('canshare');
        $usercandeeplink = $this->get_processor()->get_user()->can_deeplink();

        $usercanrename = (($item->is_dir()) ? $this->get_processor()->get_user()->can_rename_folders() : $this->get_processor()->get_user()->can_rename_files()) && true === $item->get_permission('canrename');
        $usercanmove = (($item->is_dir()) ? $this->get_processor()->get_user()->can_move_folders() : $this->get_processor()->get_user()->can_move_files()) && true === $item->get_permission('canmove');
        $usercandelete = (($item->is_dir()) ? $this->get_processor()->get_user()->can_delete_folders() : $this->get_processor()->get_user()->can_delete_files()) && true === $item->get_permission('candelete');

        $filename = (('1' === $this->get_processor()->get_shortcode_option('show_ext')) ? $item->get_name() : $item->get_basename());

        // View
        $previewurl = OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-preview&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();
        $onclick = "sendGooglePageView('Preview', '".$item->get_name()."');";

        if ($usercanpreview && '1' !== $this->get_processor()->get_shortcode_option('forcedownload')) {
            if ($item->get_can_preview_by_cloud() && '1' === $this->get_processor()->get_shortcode_option('previewinline')) {
                $html .= "<li><a class='entry_action_view' title='".__('Preview', 'outofthebox')."'><i class='fas fa-eye fa-lg'></i>&nbsp;".__('Preview', 'outofthebox').'</a></li>';
                $html .= "<li><a href='{$previewurl}' target='_blank' class='entry_action_external_view' onclick=\"{$onclick}\" title='".__('Preview (new window)', 'outofthebox')."'><i class='fas fa-desktop fa-lg'></i>&nbsp;".__('Preview (new window)', 'outofthebox').'</a></li>';
            } elseif ($item->get_can_preview_by_cloud()) {
                if ('1' === $this->get_processor()->get_shortcode_option('previewinline')) {
                    $html .= "<li><a class='entry_action_view' title='".__('Preview', 'outofthebox')."'><i class='fas fa-eye fa-lg'></i>&nbsp;".__('Preview', 'outofthebox').'</a></li>';
                }
                $html .= "<li><a href='{$previewurl}' target='_blank' class='entry_action_external_view' onclick=\"{$onclick}\" title='".__('Preview (new window)', 'outofthebox')."'><i class='fas fa-desktop fa-lg'></i>&nbsp;".__('Preview (new window)', 'outofthebox').'</a></li>';
            }
        }

        // Download
        if (($item->is_file()) && ($this->get_processor()->get_user()->can_download())) {
            $target = ('url' === $item->get_extension()) ? 'target="_blank"' : '';
            $html .= "<li><a href='".OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken()."&dl=1' {$target} data-filename='".$filename."' class='entry_action_download' title='".__('Download', 'outofthebox')."'><i class='fas fa-download fa-lg'></i>&nbsp;".__('Download', 'outofthebox').'</a></li>';
        }
        if (($this->get_processor()->get_user()->can_download()) && $item->is_dir() && '1' === $this->get_processor()->get_shortcode_option('can_download_zip')) {
            $html .= "<li><a href='".OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-create-zip&id='.$item->get_id().'&lastpath='.rawurlencode($item->get_path_display()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken().'&_ajax_nonce='.wp_create_nonce('outofthebox-create-zip')."' class='entry_action_download' download='".$item->get_name()."' data-filename='".$filename."' title='".__('Download', 'outofthebox')."'><i class='fas fa-download fa-lg'></i>&nbsp;".__('Download', 'outofthebox').'</a></li>';
        }

        // Rename
        if ($usercanrename) {
            $html .= "<li><a class='entry_action_rename' title='".__('Rename', 'outofthebox')."'><i class='fas fa-tag fa-lg'></i>&nbsp;".__('Rename', 'outofthebox').'</a></li>';
        }

        // Move
        if ($usercanmove) {
            $html .= "<li><a class='entry_action_move' title='".__('Move to', 'outofthebox')."'><i class='fas fa-folder-open fa-lg'></i>&nbsp;".__('Move to', 'outofthebox').'</a></li>';
        }

        // Delete
        if ($usercandelete) {
            $html .= "<li><a class='entry_action_delete' title='".__('Delete', 'outofthebox')."'><i class='fas fa-trash fa-lg'></i>&nbsp;".__('Delete', 'outofthebox').'</a></li>';
        }

        // Deeplink
        if ($usercandeeplink) {
            $html .= "<li><a class='entry_action_deeplink' title='".__('Direct Link', 'outofthebox')."'><i class='fas fa-link fa-lg'></i>&nbsp;".__('Direct Link', 'outofthebox').'</a></li>';
        }

        // Shortlink
        if ($usercanshare) {
            $html .= "<li><a class='entry_action_shortlink' title='".__('Share', 'outofthebox')."'><i class='fas fa-share-alt fa-lg'></i>&nbsp;".__('Share', 'outofthebox').'</a></li>';
        }

        if ('' !== $html) {
            return "<a class='entry_edit_menu'><i class='fas fa-chevron-circle-down fa-lg'></i></a><div id='menu-".$item->get_id()."' class='oftb-dropdown-menu'><ul data-path='".rawurlencode($item->get_path_display())."' data-name=\"".$item->get_basename().'">'.$html."</ul></div>\n";
        }

        return $html;
    }

    public function renderNewFolderForList()
    {
        $html = '';
        if (false === $this->_search) {
            $icon_set = $this->get_processor()->get_setting('icon_set');

            if ($this->get_processor()->get_user()->can_add_folders() && true === $this->_folder->get_permission('canadd')) {
                $html .= "<div class='entry folder newfolder'>";
                $html .= "<div class='entry_icon'><img src='".$icon_set."32x32/folder-new.png'/></div>";
                $html .= "<div class='entry_name'>".__('Add folder', 'outofthebox').'</div>';
                $html .= "<div class='entry_description'>".__('Add a new folder in this directory', 'outofthebox').'</div>';
                $html .= '</div>';
            }
        }

        return $html;
    }

    public function renderNewFolderForGrid()
    {
        $return = '';
        if (false === $this->_search) {
            if ($this->get_processor()->get_user()->can_add_folders() && true === $this->_folder->get_permission('canadd')) {
                $icon_set = $this->get_processor()->get_setting('icon_set');
                $return .= "<div class='entry folder newfolder'>\n";
                $return .= "<div class='entry_block'>\n";
                $return .= "<div class='entry_thumbnail'><div class='entry_thumbnail-view-bottom'><div class='entry_thumbnail-view-center'>\n";
                $return .= "<a class='entry_link'><img class='preloading' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='".$icon_set.'128x128/folder-new.png'."' data-src-retina='".$icon_set.'256x256/folder-new.png'."'/></a>";
                $return .= "</div></div></div>\n";
                $return .= "<div class='entry_name'><a class='entry_link'><div class='entry-name-view'><span>".__('Add folder', 'outofthebox').'</span></div></a>';
                $return .= "</div>\n";
                $return .= "</div>\n";
                $return .= "</div>\n";
            }
        }

        return $return;
    }
}
