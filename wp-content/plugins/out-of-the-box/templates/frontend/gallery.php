<div class="list-container" style="width:<?php echo $this->options['maxwidth']; ?>;max-width:<?php echo $this->options['maxwidth']; ?>;">
  <?php
  if ('1' === $this->options['show_breadcrumb'] || '1' === $this->options['search'] || '1' === $this->options['show_refreshbutton']
          || $this->get_user()->can_add_folders() || $this->get_user()->can_download_zip() || $this->get_user()->can_delete_files() || $this->get_user()->can_delete_folders() || $this->get_user()->can_move_folders() || $this->get_user()->can_move_files()) {
      ?><div class="nav-header"><?php if ('1' === $this->options['show_breadcrumb']) { ?>
            <a class="nav-home entry-info-button" title="<?php esc_html_e('Back to our first folder', 'wpcloudplugins'); ?>">
              <i class="eva eva-home-outline"></i>
            </a>
            <div class="nav-title"><?php esc_html_e('Loading...', 'wpcloudplugins'); ?></div>
            <?php
            if ('1' === $this->options['search']) {
                ?>
                <a class="nav-search entry-info-button">
                  <i class="eva eva-search"></i>
                </a>

                <div class="tippy-content-holder search-div">
                  <div class="search-icon"><i class="eva eva-search eva-lg"></i></div>
                  <div class="search-remove"><i class="eva eva-close-circle "></i></div>
                  <input name="q" type="text" size="40" aria-label="<?php echo esc_html__('Search', 'wpcloudplugins'); ?>" placeholder="<?php echo esc_html__('Search filenames', 'wpcloudplugins').(('1' === $this->options['show_files'] /* $this->options['searchcontents'] === '1' Not yet supported */) ? ' '.esc_html__('and content', 'wpcloudplugins') : ''); ?>" class="search-input" />
                </div>
                <?php
            }

                ?>
                <a class="nav-gear entry-info-button" title="<?php esc_html_e('More actions', 'wpcloudplugins'); ?>">
                  <i class="eva eva-more-vertical-outline"></i>
                </a>
                <div class="tippy-content-holder gear-div" data-token="<?php echo $this->listtoken; ?>">
                  <ul>
                    <?php
                    $need_separator = false;

                    if ($this->get_user()->can_add_folders()) {
                        $need_separator = true; ?>
                        <li><a class="nav-new-folder newfolder" data-mimetype='' title="<?php esc_html_e('Add folder', 'wpcloudplugins'); ?>"><i class="eva eva-folder-add-outline eva-lg "></i><?php esc_html_e('Add folder', 'wpcloudplugins'); ?></a></li>
                        <?php
                    }

                if ($this->get_user()->can_upload()) {
                    $need_separator = true; ?>
                        <li><a class="nav-upload" title="<?php esc_html_e('Upload files', 'wpcloudplugins'); ?>"><i class="eva eva-upload-outline eva-lg "></i><?php esc_html_e('Upload files', 'wpcloudplugins'); ?></a></li>
                        <?php
                }

                if ($this->get_user()->can_move_folders() || $this->get_user()->can_move_files() || $this->get_user()->can_delete_files() || $this->get_user()->can_delete_folders() || $this->get_user()->can_download_zip()) {
                    if ($need_separator) {
                        ?><li class='list-separator'></li><?php
                    }

                    $need_separator = true; ?>
                    <li><a class='select-all' title='" <?php esc_html_e('Select all', 'wpcloudplugins'); ?>"'><i class='eva eva-radio-button-on eva-lg'></i><?php esc_html_e('Select all', 'wpcloudplugins'); ?></a></li>
                    <li style="display:none"><a class='deselect-all' title='" <?php esc_html_e('Deselect all', 'wpcloudplugins'); ?>"'><i class='eva eva-radio-button-off eva-lg'></i><?php esc_html_e('Deselect all', 'wpcloudplugins'); ?></a></li>
                    <?php
                }

                if ($this->get_user()->can_download_zip()) {
                    $need_separator = true; ?>
                        <li><a class="all-files-to-zip" download><i class='eva eva-download eva-lg'></i><?php esc_html_e('Download folder', 'wpcloudplugins'); ?></a></li>
                        <li><a class="selected-files-to-zip" download><i class='eva eva-download eva-lg'></i><?php esc_html_e('Download selected', 'wpcloudplugins'); ?></a></li>
                        <?php
                }

                if ($this->get_user()->can_move_folders() || $this->get_user()->can_move_files()) {
                    $need_separator = true; ?>
                        <li><a class='selected-files-move' title='" <?php esc_html_e('Move to', 'wpcloudplugins'); ?>"'><i class='eva eva-corner-down-right eva-lg'></i><?php esc_html_e('Move to', 'wpcloudplugins'); ?></a></li>
                        <?php
                }

                if ($this->get_user()->can_delete_files() || $this->get_user()->can_delete_folders()) {
                    $need_separator = true; ?>
                        <li><a class="selected-files-delete" title="<?php esc_html_e('Delete', 'wpcloudplugins'); ?>"><i class="eva eva-trash-2-outline eva-lg "></i><?php esc_html_e('Delete', 'wpcloudplugins'); ?></a></li>
                        <?php
                }

                if ($need_separator && ($this->get_user()->can_deeplink() || $this->get_user()->can_share())) {
                    ?><li class='list-separator'></li><?php
                }

                if ($this->get_user()->can_deeplink()) {
                    $need_separator = true; ?>
                        <li><a class='entry_action_deeplink_folder' title='<?php esc_html_e('Direct link', 'wpcloudplugins'); ?>'><i class='eva eva-link eva-lg'></i><?php esc_html_e('Direct link', 'wpcloudplugins'); ?></a></li>
                        <?php
                }
                if ($this->get_user()->can_share()) {
                    $need_separator = true; ?>
                        <li><a class='entry_action_shortlink_folder' title='<?php esc_html_e('Share folder', 'wpcloudplugins'); ?>'><i class='eva eva-share-outline eva-lg'></i><?php esc_html_e('Share folder', 'wpcloudplugins'); ?></a></li>
                        <?php
                } ?>
                    <li class='gear-menu-no-options' style="display: none"><a><i class='eva eva-info-outline eva-lg '></i><?php esc_html_e('No options...', 'wpcloudplugins'); ?></a></li>
                  </ul>
                </div><?php
        }
      if ('1' === $this->options['show_refreshbutton']) {
          ?>
            <a class="nav-refresh entry-info-button" title="<?php esc_html_e('Refresh', 'wpcloudplugins'); ?>">
              <i class="eva eva-sync"></i>
            </a>
            <?php
      }

      if ('0' === $this->options['single_account']) {
          $primary_account = $this->get_accounts()->get_primary_account();

          if (null === $current_account) {
              echo "<div class='nav-account-selector' data-account-id='{$primary_account->get_id()}' title='{$primary_account->get_name()}'><img src='{$primary_account->get_image()}' onerror='this.src=\"".OUTOFTHEBOX_ROOTPATH."/css/images/usericon.png\"' /><span>{$primary_account->get_name()}</span></div>\n";
          } else {
              echo "<div class='nav-account-selector' data-account-id='{$current_account->get_id()}' title='{$current_account->get_name()}'><img src='{$current_account->get_image()}' onerror='this.src=\"".OUTOFTHEBOX_ROOTPATH."/css/images/usericon.png\"' /><span>{$current_account->get_name()}</span></div>\n";
          }

          echo "<div class='nav-account-selector-content'>";
          foreach ($this->get_accounts()->list_accounts() as $account_id => $account) {
              echo "<div class='nav-account-selector' data-account-id='{$account->get_id()}' title='{$account->get_name()}'><img src='{$account->get_image()}' onerror='this.src=\"".OUTOFTHEBOX_ROOTPATH."/css/images/usericon.png\"' /><span>{$account->get_name()}</span></div>\n";
          }
          echo '</div>';
      } ?></div>
  <?php
  } ?>
  <div class="file-container">
    <div class="loading initialize"><?php
      $loaders = $this->get_setting('loaders');

      switch ($loaders['style']) {
          case 'custom':
              break;

          case 'beat':
              ?>
              <div class='loader-beat'></div>
              <?php
              break;

          case 'spinner':
              ?>
              <svg class="loader-spinner" viewBox="25 25 50 50">
              <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10"></circle>
              </svg>
              <?php
              break;
      }
      ?></div>
    <div class="ajax-filelist" style="<?php echo (!empty($this->options['maxheight'])) ? 'max-height:'.$this->options['maxheight'].';overflow-y: scroll;' : ''; ?>">
      <div class='images image-collage'>
        <?php $target_height = $this->options['targetheight'];
        \TheLion\OutoftheBox\Skeleton::get_gallery_placeholders($target_height, 5);
        ?>
      </div></div>
    <div class="scroll-to-top"><a><i class="eva eva-arrow-circle-up eva-2x"></i></a></div>
  </div>
</div>