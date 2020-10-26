<div class="list-container" style="width:<?php echo $this->options['maxwidth']; ?>;max-width:<?php echo $this->options['maxwidth']; ?>;">
  <?php
  if ('1' === $this->options['show_breadcrumb'] || '1' === $this->options['search'] || '1' === $this->options['show_refreshbutton'] ||
          $this->get_user()->can_add_folders() || $this->get_user()->can_download_zip() || $this->get_user()->can_delete_files() || $this->get_user()->can_delete_folders() || $this->get_user()->can_move_folders() || $this->get_user()->can_move_files()) {
      ?>
      <div class="nav-header"><?php if ('1' === $this->options['show_breadcrumb']) { ?>
            <a class="nav-home" title="<?php _e('Back to our first folder', 'wpcloudplugins'); ?>">
              <i class="fas fa-home"></i>
            </a>
            <div class="nav-title"><?php _e('Loading...', 'wpcloudplugins'); ?></div>
            <?php
            if ('1' === $this->options['search']) {
                ?>
                <a class="nav-search">
                  <i class="fas fa-search"></i>
                </a>

                <div class="tippy-content-holder search-div">
                  <div class="search-remove"><i class="fas fa-times-circle "></i></div>
                  <input name="q" type="text" size="40" aria-label="<?php echo __('Search', 'wpcloudplugins'); ?>" placeholder="<?php echo __('Search filenames', 'wpcloudplugins').(('1' === $this->options['show_files']  /* $this->options['searchcontents'] === '1' Not yet supported */) ? ' '.__('and content', 'wpcloudplugins') : ''); ?>" class="search-input" />
                </div>
            <?php
            } ?>

                        <a class="nav-sort" title="<?php _e('Sort options', 'wpcloudplugins'); ?>">
              <i class="fas fa-sort-amount-up"></i>
            </a>
            <div class="tippy-content-holder">
              <ul class='nav-sorting-list'>
                <li><a class="nav-sorting-field nav-name <?php echo ('name' === $this->options['sort_field']) ? 'sort-selected' : ''; ?>" data-field="name" title="<?php _e('Name', 'wpcloudplugins'); ?>">
                    <?php _e('Name', 'wpcloudplugins'); ?>
                  </a>
                </li>
                <li><a class="nav-sorting-field nav-size <?php echo ('size' === $this->options['sort_field']) ? 'sort-selected' : ''; ?>" data-field="size" title="<?php _e('Size', 'wpcloudplugins'); ?>">
                    <?php _e('Size', 'wpcloudplugins'); ?>
                  </a>
                </li>
                <li><a class="nav-sorting-field nav-modified <?php echo ('modified' === $this->options['sort_field']) ? 'sort-selected' : ''; ?>" data-field="modified" title="<?php _e('Modified', 'wpcloudplugins'); ?>">
                   <?php _e('Modified', 'wpcloudplugins'); ?>
                  </a>
                </li>
                <li class="list-separator">&nbsp;</li>
                <li><a class="nav-sorting-order nav-asc <?php echo ('asc' === $this->options['sort_order']) ? 'sort-selected' : ''; ?>" data-order="asc" title="<?php _e('Ascending', 'wpcloudplugins'); ?>">
                   <?php _e('Ascending', 'wpcloudplugins'); ?>
                  </a>
                </li>
                <li><a class="nav-sorting-order nav-desc <?php echo ('desc' === $this->options['sort_order']) ? 'sort-selected' : ''; ?>" data-order="desc" title="<?php _e('Descending', 'wpcloudplugins'); ?>">
                   <?php _e('Descending', 'wpcloudplugins'); ?>
                  </a>
                </li>
              </ul>
            </div>
            
      <a class="nav-gear" title="<?php _e('More actions', 'wpcloudplugins'); ?>">
              <i class="fas fa-ellipsis-v"></i>
            </a>
            <div class="tippy-content-holder" data-token="<?php echo $this->listtoken; ?>">
              <ul data-id="<?php echo $this->listtoken; ?>">
                <li style="display: none"><a class="nav-layout nav-layout-grid" title="<?php _e('Thumbnails view', 'wpcloudplugins'); ?>">
                    <i class="fas fa-th-large "></i><?php _e('Thumbnails view', 'wpcloudplugins'); ?>
                  </a>
                </li>
                <li><a class="nav-layout nav-layout-list" title="<?php _e('List view', 'wpcloudplugins'); ?>">
                    <i class="fas fa-th-list "></i><?php _e('List view', 'wpcloudplugins'); ?>
                  </a>
                </li>   
                <?php
                if ($this->get_user()->can_add_folders()) {
                    ?>
                  <li><a class="nav-new-folder newfolder" data-mimetype='' title="<?php _e('Add folder', 'wpcloudplugins'); ?>"><i class="fas fa-folder-plus"></i><?php _e('Add folder', 'wpcloudplugins'); ?></a></li>
                  <?php
                }

                if ($this->get_user()->can_create_document()) {
                    ?>
                    <li>
                      <a class="nav-new-entry" title="<?php _e('Create document', 'wpcloudplugins'); ?>"><i class="fas fa-file-plus"></i><?php _e('Create document', 'wpcloudplugins'); ?></a>
                      <ul>
                      </ul>
                    </li>
                <?php
                }

                if ($this->get_user()->can_upload()) {
                    ?>
                    <li><a class="nav-upload" title="<?php _e('Upload files', 'wpcloudplugins'); ?>"><i class="fas fa-upload "></i><?php _e('Upload files', 'wpcloudplugins'); ?></a></li>
                    <?php
                }

                if ($this->get_user()->can_download_zip()) {
                    ?>
                    <li><a class="all-files-to-zip" download><i class='fas fa-archive '></i><?php _e('Download all', 'wpcloudplugins'); ?> (.zip)</a></li>
                    <li><a class="selected-files-to-zip" download><i class='fas fa-archive '></i><?php _e('Download selected', 'wpcloudplugins'); ?> (.zip)</a></li>
                    <?php
                }

                if ($this->get_user()->can_move_folders() || $this->get_user()->can_move_files()) {
                    ?>
                    <li><a class='selected-files-move' title='" <?php _e('Move to', 'wpcloudplugins'); ?>"'><i class='fas fa-folder-open '></i><?php _e('Move to', 'wpcloudplugins'); ?></a></li>
                    <?php
                }

                if ($this->get_user()->can_delete_files() || $this->get_user()->can_delete_folders()) {
                    ?>
                    <li><a class="selected-files-delete" title="<?php _e('Delete', 'wpcloudplugins'); ?>"><i class="fas fa-trash "></i><?php _e('Delete', 'wpcloudplugins'); ?></a></li>
                    <?php
                }

                if ($this->get_user()->can_deeplink()) {
                    ?>
                    <li><a class='entry_action_deeplink_folder' title='<?php _e('Direct link', 'wpcloudplugins'); ?>'><i class='fas fa-link '></i><?php _e('Direct link', 'wpcloudplugins'); ?></a></li>
                    <?php
                }
                if ($this->get_user()->can_share()) {
                    ?>
                    <li><a class='entry_action_shortlink_folder' title='<?php _e('Share folder', 'wpcloudplugins'); ?>'><i class='fas fa-share-alt '></i><?php _e('Share folder', 'wpcloudplugins'); ?></a></li>
                    <?php
                }
                ?>
                <li class='gear-menu-no-options' style="display: none"><a><i class='fas fa-info-circle '></i><?php _e('No options...', 'wpcloudplugins'); ?></a></li>
              </ul>
            </div><?php
        }
      if ('1' === $this->options['show_refreshbutton']) {
          ?>
            <a class="nav-refresh" title="<?php _e('Refresh', 'wpcloudplugins'); ?>">
              <i class="fas fa-sync"></i>
            </a>
            <?php
      }

      if ('0' === $this->options['single_account']) {
          $current_account = $this->get_accounts()->get_account_by_id($dataaccountid);
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
    <div class="ajax-filelist" style="<?php echo (!empty($this->options['maxheight'])) ? 'max-height:'.$this->options['maxheight'].';overflow-y: scroll;' : ''; ?>">&nbsp;</div>
    <div class="scroll-to-top"><a><i class="fas fa-chevron-circle-up fa-2x"></i></a></div>
  </div>
</div>