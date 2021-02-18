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
            $this->renderFileList();
        }
    }

    public function search_files()
    {
        $this->_search = true;
        $input = mb_strtolower($_REQUEST['query'], 'UTF-8');
        $this->_folder = $this->get_processor()->get_client()->search($input);

        if ((false !== $this->_folder)) {
            $this->renderFileList();
        }
    }

    public function setFolder($folder)
    {
        $this->_folder = $folder;
    }

    public function renderFileList()
    {
        // Create HTML Filelist
        $filelist_html = '';

        $breadcrumb_class = ('1' === $this->get_processor()->get_shortcode_option('show_breadcrumb')) ? 'has-breadcrumb' : 'no-breadcrumb';

        $filelist_html = "<div class='files {$breadcrumb_class}'>";
        $filelist_html .= "<div class='folders-container'>";

        // Add 'back to Previous folder' if needed

        if (
                (false === $this->_search)
                && ('' !== $this->_folder->get_path())
                && (strtolower($this->_folder->get_path()) !== strtolower($this->get_processor()->get_root_folder()))
        ) {
            $foldername = basename($this->_folder->get_path());
            $location = str_replace('\\', '/', (dirname($this->get_processor()->get_requested_path())));

            $parent_folder_entry = new Entry();
            $parent_folder_entry->set_id('Previous Folder');
            $parent_folder_entry->set_name(__('Previous folder', 'wpcloudplugins'));
            $parent_folder_entry->set_path($location);
            $parent_folder_entry->set_path_display($location);
            $parent_folder_entry->set_is_dir(true);
            $parent_folder_entry->set_parent_folder(true);
            $parent_folder_entry->set_icon(OUTOFTHEBOX_ROOTPATH.'/css/icons/32x32/prev.png');

            $filelist_html .= $this->renderDir($parent_folder_entry);
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
                    $filelist_html .= $this->renderDir($item);

                    if (!$item->is_parent_folder()) {
                        $hasfilesorfolders = true;
                    }
                }
            }
        }

        if (false === $this->_search) {
            $filelist_html .= $this->renderNewFolder();
        }

        $filelist_html .= "</div><div class='files-container'>";

        if ($this->_folder->has_children()) {
            foreach ($this->_folder->get_children() as $item) {
                // Render files div
                if ($item->is_file()) {
                    $filelist_html .= $this->renderFile($item);

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
        $file_path = '<ol class="wpcp-breadcrumb">';
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
            $file_path .= "<li><a href='javascript:void(0)' class='folder'>".sprintf(__('Results for %s', 'wpcloudplugins'), "'".$_REQUEST['query']."'").'</a></li>';
        }

        $file_path .= '</ol>';

        $raw_path = '';
        if (
                (\TheLion\OutoftheBox\Helpers::check_user_role($this->get_processor()->get_setting('permissions_add_shortcodes')))
                || (\TheLion\OutoftheBox\Helpers::check_user_role($this->get_processor()->get_setting('permissions_add_links')))
                || (\TheLion\OutoftheBox\Helpers::check_user_role($this->get_processor()->get_setting('permissions_add_embedded')))
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
            'accountId' => null,
            'virtual' => false,
            'breadcrumb' => $file_path,
            'html' => $filelist_html,
            'expires' => $expires, ]);

        $cached_request = new CacheRequest($this->get_processor());
        $cached_request->add_cached_response($response);

        echo $response;

        exit();
    }

    public function renderNoResults()
    {
        $icon_set = $this->get_processor()->get_setting('loaders');

        $html = "<div class='entry file no-entries'>\n";
        $html .= "<div class='entry_block'>\n";
        $html .= "<div class='entry_thumbnail'><div class='entry_thumbnail-view-bottom'><div class='entry_thumbnail-view-center'>\n";
        $html .= "<a class='entry_link'><img class='preloading' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='".$icon_set['no_results']."' data-src-retina='".$icon_set['no_results']."'/></a>";
        $html .= "</div></div></div>\n";

        $html .= "<div class='entry-info'>";
        $html .= "<div class='entry-info-name'>";
        $html .= "<a class='entry_link' title='".__('This folder is empty', 'wpcloudplugins')."'><div class='entry-name-view'>";
        $html .= '<span>'.__('This folder is empty', 'wpcloudplugins').'</span>';
        $html .= '</div></a>';
        $html .= "</div>\n";

        $html .= "</div>\n";
        $html .= "</div>\n";
        $html .= "</div>\n";

        return $html;
    }

    public function renderDir(Entry $item)
    {
        $return = '';

        $classmoveable = ($this->get_processor()->get_user()->can_move()) ? 'moveable' : '';

        $isparent = $item->is_parent_folder();
        $style = ($isparent) ? ' pf previous ' : '';

        $return .= "<div class='entry folder {$classmoveable}  {$style}' data-id='".$item->get_id()."' data-url='".rawurlencode($item->get_path_display())."' data-name='".htmlspecialchars($item->get_basename(), ENT_QUOTES | ENT_HTML401, 'UTF-8')."'>\n";
        if (false === $item->is_parent_folder()) {
            if ('linkto' === $this->get_processor()->get_shortcode_option('mcepopup') || 'linktobackendglobal' === $this->get_processor()->get_shortcode_option('mcepopup')) {
                $return .= "<div class='entry_linkto'>\n";
                $return .= '<span>'."<input class='button-secondary' type='submit' title='".__('Select folder', 'wpcloudplugins')."' value='".__('Select folder', 'wpcloudplugins')."'>".'</span>';
                $return .= '</div>';
            }
        }

        $return .= "<div class='entry_block'>\n";

        $return .= "<div class='entry-info'>";

        $thumburl = $item->get_icon_retina();
        $return .= "<div class='entry-info-icon'><div class='preloading'></div><img class='preloading' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='{$thumburl}' data-src-retina='{$thumburl}'/></div>";

        $return .= "<div class='entry-info-name'>";
        $return .= "<a class='entry_link' title='{$item->get_basename()}'>";
        $return .= '<span>';
        $return .= (($isparent) ? '<strong>'.__('Previous folder', 'wpcloudplugins').'</strong>' : $item->get_name()).' </span>';
        $return .= '</span>';
        $return .= '</a></div>';

        if (!$isparent) {
            $return .= $this->renderDescription($item);
            $return .= $this->renderActionMenu($item);
            $return .= $this->renderCheckBox($item);
        }

        $return .= "</div>\n";

        $return .= "</div>\n";
        $return .= "</div>\n";

        return $return;
    }

    public function renderFile(Entry $item)
    {
        $link = $this->renderFileNameLink($item);
        $title = $link['filename'].((('1' === $this->get_processor()->get_shortcode_option('show_filesize')) && ($item->get_size() > 0)) ? ' ('.Helpers::bytes_to_size_1024($item->get_size()).')' : '&nbsp;');

        $classmoveable = ($this->get_processor()->get_user()->can_move()) ? 'moveable' : '';

        $thumbnail_url = ($item->has_own_thumbnail() ? $this->get_processor()->get_client()->get_thumbnail($item, true, 640, 480) : $item->get_icon_retina());
        $has_tooltip = ($item->has_own_thumbnail() && !empty($thumbnail_url) && ('shortcode' !== $this->get_processor()->get_shortcode_option('mcepopup')) ? "data-tooltip=''" : '');

        $return = '';
        $return .= "<div class='entry file {$classmoveable}' data-id='".$item->get_id()."' data-url='".rawurlencode($item->get_path_display())."' data-name='".htmlspecialchars($item->get_name(), ENT_QUOTES | ENT_HTML401, 'UTF-8')."' {$has_tooltip}>\n";
        $return .= "<div class='entry_block'>\n";

        $caption = '<span data-id="'.$item->get_id().'"></span>';

        $return .= "<div class='entry_thumbnail'><div class='entry_thumbnail-view-bottom'><div class='entry_thumbnail-view-center'>\n";

        $return .= "<div class='preloading'></div>";
        $return .= "<img class='preloading' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='".$thumbnail_url."' data-src-retina='".$thumbnail_url."' data-src-backup='".$item->get_icon_retina()."'/>";
        $return .= "</div></div></div>\n";

        if ($duration = $item->get_media('duration')) {
            $return .= "<div class='entry-duration'><i class='fas fa-play fa-xs' ></i> ".Helpers::convert_ms_to_time($duration).'</div>';
        }

        $return .= "<div class='entry-info'>";
        $return .= "<div class='entry-info-icon'><img src='".$item->get_icon()."'/></div>";
        $return .= "<div class='entry-info-name'>";
        $return .= '<a '.$link['url'].' '.$link['target']." class='entry_link ".$link['class']."' ".$link['onclick']." title='".$title."' ".$link['lightbox']." data-filename='".$link['filename']."' data-caption='{$caption}'>";
        $return .= '<span>'.$link['filename'].'</span>';
        $return .= '</a>';

        if (('shortcode' === $this->get_processor()->get_shortcode_option('mcepopup')) && (in_array($item->get_extension(), ['mp4', 'm4v', 'ogg', 'ogv', 'webmv', 'mp3', 'm4a', 'oga', 'wav', 'webm']))) {
            $return .= "&nbsp;<a class='entry_media_shortcode'><i class='fas fa-code'></i></a>";
        }

        $return .= '</div>';
        $return .= $this->renderModifiedDate($item);
        $return .= $this->renderSize($item);
        $return .= $this->renderDescription($item);
        $return .= $this->renderActionMenu($item);
        $return .= $this->renderCheckBox($item);
        $return .= "</div>\n";

        $return .= $link['lightbox_inline'];

        $return .= "</div>\n";
        $return .= "</div>\n";

        return $return;
    }

    public function renderSize(EntryAbstract $item)
    {
        if ('1' === $this->get_processor()->get_shortcode_option('show_filesize')) {
            $size = ($item->get_size() > 0) ? Helpers::bytes_to_size_1024($item->get_size()) : '&nbsp;';

            return "<div class='entry-info-size entry-info-metadata'>".$size.'</div>';
        }
    }

    public function renderModifiedDate(EntryAbstract $item)
    {
        if ('1' === $this->get_processor()->get_shortcode_option('show_filedate')) {
            return "<div class='entry-info-modified-date entry-info-metadata'>".$item->get_last_edited_str().'</div>';
        }
    }

    public function renderCheckBox(EntryAbstract $item)
    {
        $checkbox = '';

        if ($item->is_dir()) {
            if ($this->get_processor()->get_user()->can_download_zip() || $this->get_processor()->get_user()->can_delete_folders() || $this->get_processor()->get_user()->can_move_folders()) {
                $checkbox .= "<div class='entry-info-button entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".$item->get_id()."' id='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'/><label for='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'></label></div>";
            }

            if ((in_array($this->get_processor()->get_shortcode_option('mcepopup'), ['links', 'embedded']))) {
                $checkbox .= "<div class='entry-info-button entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".$item->get_id()."' id='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'/><label for='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'></label></div>";
            }
        } else {
            if ($this->get_processor()->get_user()->can_download_zip() || $this->get_processor()->get_user()->can_delete_files() || $this->get_processor()->get_user()->can_move_files()) {
                $checkbox .= "<div class='entry-info-button entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".$item->get_id()."' id='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'/><label for='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'></label></div>";
            }

            if ((in_array($this->get_processor()->get_shortcode_option('mcepopup'), ['links', 'embedded']))) {
                $checkbox .= "<div class='entry-info-button entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='".$item->get_id()."' id='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'/><label for='checkbox-{$this->get_processor()->get_listtoken()}-{$item->get_id()}'></label></div>";
            }
        }

        return $checkbox;
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
                $item->is_dir()
                || false === $item->get_can_preview_by_cloud()
                || false === $this->get_processor()->get_user()->can_view()
        ) {
            $usercanpreview = false;
        }

        // Check if user is allowed to preview the file
        if ($usercanpreview) {
            $url = OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-preview&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();

            // Display Direct links for image and media files
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
                        $lightbox_size = (false !== strpos($item->get_mimetype(), 'audio')) ? 'width: \'85%\',' : 'width: \'85%\', height: \'85%\',';
                        $lightbox .= ' data-options="mousewheel: false, swipe:false, '.$lightbox_size.' thumbnail: \''.$icon.'\'"';
                        $download = 'controlsList="nodownload"';

                        $lightbox_inline = '<div id="'.$id.'" class="html5_player" style="display:none;"><'.$html5_element.' controls '.$download.' preload="metadata"  poster="'.$icon_256.'"> <source data-src="'.$url.'" type="'.$item->get_mimetype().'">'.__('Your browser does not support HTML5. You can only download this file', 'wpcloudplugins').'</'.$html5_element.'></div>';
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

        if ('shortcode' === $this->get_processor()->get_shortcode_option('mcepopup')) {
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

    public function renderDescription(Entry $item)
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

    public function renderActionMenu(Entry $item)
    {
        $html = '';

        $usercanpreview = $this->get_processor()->get_user()->can_preview();

        if (
                $item->is_dir()
                || false === $item->get_can_preview_by_cloud()
                || 'zip' === $item->get_extension()
                || false === $this->get_processor()->get_user()->can_view()
        ) {
            $usercanpreview = false;
        }

        $usercanshare = $this->get_processor()->get_user()->can_share() && true === $item->get_permission('canshare');
        $usercandeeplink = $this->get_processor()->get_user()->can_deeplink();

        $usercanrename = (($item->is_dir()) ? $this->get_processor()->get_user()->can_rename_folders() : $this->get_processor()->get_user()->can_rename_files()) && true === $item->get_permission('canrename');
        $usercanmove = (($item->is_dir()) ? $this->get_processor()->get_user()->can_move_folders() : $this->get_processor()->get_user()->can_move_files()) && true === $item->get_permission('canmove');
        $usercancopy = (($item->is_dir()) ? $this->get_processor()->get_user()->can_copy_folders() : $this->get_processor()->get_user()->can_copy_files());
        $usercandelete = (($item->is_dir()) ? $this->get_processor()->get_user()->can_delete_folders() : $this->get_processor()->get_user()->can_delete_files()) && true === $item->get_permission('candelete');

        $filename = (('1' === $this->get_processor()->get_shortcode_option('show_ext')) ? $item->get_name() : $item->get_basename());

        // View
        $previewurl = OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-preview&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();
        $onclick = "sendGooglePageView('Preview', '".$item->get_name()."');";

        if ($usercanpreview && '1' !== $this->get_processor()->get_shortcode_option('forcedownload')) {
            if ($item->get_can_preview_by_cloud() && '1' === $this->get_processor()->get_shortcode_option('previewinline')) {
                $html .= "<li><a class='entry_action_view' title='".__('Preview', 'wpcloudplugins')."'><i class='fas fa-eye '></i>&nbsp;".__('Preview', 'wpcloudplugins').'</a></li>';
                $html .= "<li><a href='{$previewurl}' target='_blank' class='entry_action_external_view' onclick=\"{$onclick}\" title='".__('Preview (new window)', 'wpcloudplugins')."'><i class='fas fa-desktop '></i>&nbsp;".__('Preview (new window)', 'wpcloudplugins').'</a></li>';
            } elseif ($item->get_can_preview_by_cloud()) {
                if ('1' === $this->get_processor()->get_shortcode_option('previewinline')) {
                    $html .= "<li><a class='entry_action_view' title='".__('Preview', 'wpcloudplugins')."'><i class='fas fa-eye '></i>&nbsp;".__('Preview', 'wpcloudplugins').'</a></li>';
                }
                $html .= "<li><a href='{$previewurl}' target='_blank' class='entry_action_external_view' onclick=\"{$onclick}\" title='".__('Preview (new window)', 'wpcloudplugins')."'><i class='fas fa-desktop '></i>&nbsp;".__('Preview (new window)', 'wpcloudplugins').'</a></li>';
            }
        }

        // Deeplink
        if ($usercandeeplink) {
            $html .= "<li><a class='entry_action_deeplink' title='".__('Direct link', 'wpcloudplugins')."'><i class='fas fa-link '></i>&nbsp;".__('Direct link', 'wpcloudplugins').'</a></li>';
        }

        // Shortlink
        if ($usercanshare) {
            $html .= "<li><a class='entry_action_shortlink' title='".__('Share', 'wpcloudplugins')."'><i class='fas fa-share-alt '></i>&nbsp;".__('Share', 'wpcloudplugins').'</a></li>';
        }

        // Download
        if (($item->is_file()) && ($this->get_processor()->get_user()->can_download())) {
            $target = ('url' === $item->get_extension()) ? 'target="_blank"' : '';
            $html .= "<li><a href='".OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-download&OutoftheBoxpath='.rawurlencode($item->get_path()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken()."&dl=1' {$target} data-filename='".$filename."' class='entry_action_download' title='".__('Download', 'wpcloudplugins')."'><i class='fas fa-arrow-down '></i>&nbsp;".__('Download', 'wpcloudplugins').'</a></li>';
        }
        if (($this->get_processor()->get_user()->can_download()) && $item->is_dir() && '1' === $this->get_processor()->get_shortcode_option('can_download_zip')) {
            $html .= "<li><a class='entry_action_download' download='".$item->get_name()."' data-filename='".$filename."' title='".__('Download', 'wpcloudplugins')."'><i class='fas fa-arrow-down '></i>&nbsp;".__('Download', 'wpcloudplugins').'</a></li>';
        }

        // Move
        if ($usercanmove) {
            $html .= "<li><a class='entry_action_move' title='".__('Move to', 'wpcloudplugins')."'><i class='fas fa-folder-open '></i>&nbsp;".__('Move to', 'wpcloudplugins').'</a></li>';
        }

        // Copy
        if ($usercancopy) {
            $html .= "<li><a class='entry_action_copy' title='".__('Make a copy', 'wpcloudplugins')."'><i class='fas fa-clone'></i>&nbsp;".__('Make a copy', 'wpcloudplugins').'</a></li>';
        }

        // Rename
        if ($usercanrename) {
            $html .= "<li><a class='entry_action_rename' title='".__('Rename', 'wpcloudplugins')."'><i class='fas fa-tag '></i>&nbsp;".__('Rename', 'wpcloudplugins').'</a></li>';
        }

        // Delete
        if ($usercandelete) {
            $html .= "<li><a class='entry_action_delete' title='".__('Delete', 'wpcloudplugins')."'><i class='fas fa-trash '></i>&nbsp;".__('Delete', 'wpcloudplugins').'</a></li>';
        }

        if ('' !== $html) {
            return "<div class='entry-info-button entry-action-menu-button' title='".__('More actions', 'wpcloudplugins')."' tabindex='0'><i class='fas fa-ellipsis-v'></i><div id='menu-".$item->get_id()."' class='entry-action-menu-button-content tippy-content-holder'><ul data-id='".$item->get_id()."' data-name='".$item->get_basename()."'>".$html."</ul></div></div>\n";
        }

        return $html;
    }

    public function renderNewFolder()
    {
        $return = '';

        if (
            false === $this->get_processor()->get_user()->can_add_folders()
            || false === $this->_folder->get_permission('canadd')
            || true === $this->_search
            || '1' === $this->get_processor()->get_shortcode_option('show_breadcrumb')
            ) {
            return $return;
        }

        $icon_set = $this->get_processor()->get_setting('icon_set');
        $return .= "<div class='entry folder newfolder'>\n";
        $return .= "<div class='entry_block'>\n";
        $return .= "<div class='entry_thumbnail'><div class='entry_thumbnail-view-bottom'><div class='entry_thumbnail-view-center'>\n";
        $return .= "<a class='entry_link'><img class='preloading' src='".OUTOFTHEBOX_ROOTPATH."/css/images/transparant.png' data-src='".$icon_set.'128x128/folder-new.png'."' data-src-retina='".$icon_set.'256x256/folder-new.png'."'/></a>";
        $return .= "</div></div></div>\n";

        $return .= "<div class='entry-info'>";
        $return .= "<div class='entry-info-name'>";
        $return .= "<a class='entry_link' title='".__('Add folder', 'wpcloudplugins')."'><div class='entry-name-view'>";
        $return .= '<span>'.__('Add folder', 'wpcloudplugins').'</span>';
        $return .= '</div></a>';
        $return .= "</div>\n";

        $return .= "</div>\n";
        $return .= "</div>\n";
        $return .= "</div>\n";

        return $return;
    }
}
