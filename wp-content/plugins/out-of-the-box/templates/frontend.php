<div class="list-container" style="width:<?php echo $this->options['maxwidth']; ?>;max-width:<?php echo $this->options['maxwidth']; ?>;">
  <?php
  if ('1' === $this->options['show_breadcrumb'] || '1' === $this->options['search'] || '1' === $this->options['show_refreshbutton'] ||
          $this->get_user()->can_download_zip() || $this->get_user()->can_delete_files() || $this->get_user()->can_delete_folders() || $this->get_user()->can_move_folders() || $this->get_user()->can_move_files()) {
      ?>
      <div class="nav-header"><?php if ('1' === $this->options['show_breadcrumb']) { ?>
            <a class="nav-home" title="<?php _e('Back to our first folder', 'outofthebox'); ?>">
              <i class="fas fa-home"></i>
            </a>
            <div class="nav-title"><?php _e('Loading...', 'outofthebox'); ?></div>
            <?php
            if ('1' === $this->options['search']) {
                ?>
                <a class="nav-search">
                  <i class="fas fa-search"></i>
                </a>

                <div class="search-div">
                  <div class="search-remove"><i class="fas fa-times-circle fa-lg"></i></div>
                  <input name="q" type="text" size="40" aria-label="<?php echo __('Search', 'outofthebox'); ?>" placeholder="<?php echo __('Search filenames', 'outofthebox').((1 /* $this->options['searchcontents'] === '1' Not yet supported */) ? ' '.__('and within contents', 'outofthebox') : ''); ?>" class="search-input" />
                </div>
            <?php
            } ?>
            <a class="nav-gear" title="<?php _e('Options', 'outofthebox'); ?>">
              <i class="fas fa-cog"></i>
            </a>
            <div class="gear-menu" data-token="<?php echo $this->listtoken; ?>">
              <ul data-id="<?php echo $this->listtoken; ?>">
                <li style="display: none"><a class="nav-layout nav-layout-grid" title="<?php _e('Thumbnails view', 'outofthebox'); ?>">
                    <i class="fas fa-th-large fa-lg"></i><?php _e('Thumbnails view', 'outofthebox'); ?>
                  </a>
                </li>
                <li><a class="nav-layout nav-layout-list" title="<?php _e('List view', 'outofthebox'); ?>">
                    <i class="fas fa-th-list fa-lg"></i><?php _e('List view', 'outofthebox'); ?>
                  </a>
                </li>   
                <?php
                if ($this->get_user()->can_add_folders()) {
                    ?>
                    <li><a class="nav-new-folder newfolder" title="<?php _e('Add folder', 'outofthebox'); ?>"><i class="fas fa-folder-plus fa-lg"></i><?php _e('Add folder', 'outofthebox'); ?></a></li>
                    <?php
                }

                if ($this->get_user()->can_upload()) {
                    ?>
                    <li><a class="nav-upload" title="<?php _e('Upload files', 'outofthebox'); ?>"><i class="fas fa-upload fa-lg"></i><?php _e('Upload files', 'outofthebox'); ?></a></li>
                    <?php
                }

                if ($this->get_user()->can_download_zip()) {
                    ?>
                    <li><a class="all-files-to-zip" download><i class='fas fa-archive fa-lg'></i><?php _e('Download all', 'outofthebox'); ?> (.zip)</a></li>
                    <li><a class="selected-files-to-zip" download><i class='fas fa-archive fa-lg'></i><?php _e('Download selected', 'outofthebox'); ?> (.zip)</a></li>
                    <?php
                }

                if ($this->get_user()->can_move_folders() || $this->get_user()->can_move_files()) {
                    ?>
                    <li><a class='selected-files-move' title='" <?php _e('Move to', 'outofthebox'); ?>"'><i class='fas fa-folder-open fa-lg'></i><?php _e('Move to', 'outofthebox'); ?></a></li>
                    <?php
                }

                if ($this->get_user()->can_delete_files() || $this->get_user()->can_delete_folders()) {
                    ?>
                    <li><a class="selected-files-delete" title="<?php _e('Delete selected', 'outofthebox'); ?>"><i class="fas fa-trash fa-lg"></i><?php _e('Delete selected', 'outofthebox'); ?></a></li>
                    <?php
                }

                if ($this->get_user()->can_deeplink()) {
                    ?>
                    <li><a class='entry_action_deeplink_folder' title='<?php _e('Direct Link', 'outofthebox'); ?>'><i class='fas fa-link fa-lg'></i><?php _e('Direct Link', 'outofthebox'); ?></a></li>
                    <?php
                }
                if ($this->get_user()->can_share()) {
                    ?>
                    <li><a class='entry_action_shortlink_folder' title='<?php _e('Share folder', 'outofthebox'); ?>'><i class='fas fa-share-alt fa-lg'></i><?php _e('Share folder', 'outofthebox'); ?></a></li>
                    <?php
                }
                ?>
                <li class='gear-menu-no-options' style="display: none"><a><i class='fas fa-info-circle fa-lg'></i><?php _e('No options...', 'outofthebox'); ?></a></li>
              </ul>
            </div><?php
        }
      if ('1' === $this->options['show_refreshbutton']) {
          ?>
            <a class="nav-refresh" title="<?php _e('Refresh', 'outofthebox'); ?>">
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
  <?php if ('1' === $this->options['show_columnnames']) { ?>
      <div class='column_names'>
        <div class='entry_icon'></div>
        <?php if ($this->get_user()->can_download_zip() || $this->get_user()->can_delete_files() || $this->get_user()->can_delete_folders()) {
      ?>
            <div class='entry_checkallbox'><input type='checkbox' name='select-all-files' class='select-all-files'/></div>
            <?php
  }
        ?>
        <div class='entry_edit'>&nbsp;</div>
        <?php
        if ('1' === $this->options['show_filesize']) {
            ?>
            <div class='entry_size sortable <?php echo ('size' === $this->options['sort_field']) ? $this->options['sort_order'] : ''; ?>' data-sortname="size"><span class="sort_icon">&nbsp;</span><a class='entry_sort'><?php _e('Size', 'outofthebox'); ?></a></div>
            <?php
        }

        if ('1' === $this->options['show_filedate']) {
            ?>
            <div class='entry_lastedit sortable <?php echo ('modified' === $this->options['sort_field']) ? $this->options['sort_order'] : ''; ?>' data-sortname="modified"><a class='entry_sort'><?php _e('Date modified', 'outofthebox'); ?></a><span class="sort_icon">&nbsp;</span></div>
            <?php
        }
        ?>
        <div class='entry_name sortable <?php echo ('name' === $this->options['sort_field']) ? $this->options['sort_order'] : ''; ?>' data-sortname="name"><a class='entry_sort'><?php _e('Name', 'outofthebox'); ?></a><span class="sort_icon">&nbsp;</span></div>
      </div>
  <?php } ?>
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