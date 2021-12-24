<?php
$pre_process = ('auto' === $this->options['user_upload_folders']);
$classes = $this->options['class'];

if ('1' === $this->options['upload_auto_start'] || false !== strpos($this->options['class'], 'auto_upload')) {
    $classes .= ' auto_upload ';
}

?>

<div
  class="fileupload-box  <?php echo $classes; ?> "
  style="<?php echo ('upload' === $this->options['mode']) ? "width:{$this->options['maxwidth']};max-width:{$this->options['maxwidth']};" : 'width:100%;max-width: 100%;'; ?>"
  data-preprocess="<?php echo $pre_process; ?>">
  <!-- FORM ELEMENTS -->
  <div id="fileupload-<?php echo $this->listtoken; ?>" class="fileupload-form"
    data-token='<?php echo $this->listtoken; ?>'>
    <input type="hidden" name="acceptfiletypes" value="<?php echo $acceptfiletypes; ?>">
    <input type="hidden" name="minfilesize" value="<?php echo $min_file_size; ?>">
    <input type="hidden" name="maxfilesize" data-limit="<?php echo ($own_limit) ? 1 : 0; ?>"
      value="<?php echo $max_file_size; ?>">
    <input type="hidden" name="maxnumberofuploads" value="<?php echo $max_number_of_uploads; ?>">
    <input type="hidden" name="listtoken" value="<?php echo $this->listtoken; ?>">
    <input type="hidden" name="encryption" value="0">
    <input type='hidden' name='fileupload-filelist_<?php echo $this->listtoken; ?>' class='fileupload-filelist'
      value='<?php echo isset($_REQUEST['fileupload-filelist_'.$this->listtoken]) ? stripslashes($_REQUEST['fileupload-filelist_'.$this->listtoken]) : ''; ?>'>
    <input type="file" name="files[]" class='upload-input upload-input-files' multiple>
    <?php if ('1' === $this->options['upload_folder']) { ?>
    <input type="file" name="files[]" class='upload-input upload-input-folder' multiple directory
      webkitdirectory>
    <?php } ?>
  </div>
  <!-- END FORM ELEMENTS -->

  <!-- UPLOAD BOX HEADER -->
  <div class="fileupload-header">
    <div class="fileupload-header-title">
      <div class="fileupload-empty">
        <div class="fileupload-header-text-title upload-add-file"><?php echo ($this->options['maxnumberofuploads'] > 1 || '-1' === $this->options['maxnumberofuploads']) ? esc_html__('Add your files', 'wpcloudplugins') : esc_html__('Add your file', 'wpcloudplugins'); ?></div>
        <?php if ('1' === $this->options['upload_folder'] && ($this->options['maxnumberofuploads'] > 1 || '-1' === $this->options['maxnumberofuploads'])) { ?>
        <div class="fileupload-header-text-subtitle upload-add-folder"><a
            title="<?php esc_html_e('Or select a folder', 'wpcloudplugins'); ?>"><?php esc_html_e('Or select a folder', 'wpcloudplugins'); ?></a>
        </div>
        <?php } ?>
      </div>
      <div class="fileupload-not-empty">
        <div class="fileupload-header-text-title fileupload-items"></div>
        <div class="fileupload-header-text-subtitle fileupload-items-size"></div>
      </div>
    </div>
    <div class="fileupload-header-button">
      <button class='fileupload-requirements-button button secondary' type="button" title="<?php echo esc_html__('Upload requirements', 'wpcloudplugins'); ?>"><i
          class="eva eva-list eva-lg"></i></button>
      <div class='tippy-content-holder'>
        <div class='tippy-content'>
          <div class="upload-requirements-content-subtitle">
            <?php
            if ($this->options['maxnumberofuploads'] > 0) {
                echo '<div class="upload-requirements-format">'.esc_html__('Maximum', 'wpcloudplugins').': <em><span class="file-max-uploads">'.$this->options['maxnumberofuploads'].' '.esc_html__('file(s)', 'wpcloudplugins').'</span></em></div>';
            }

            if (!empty($this->options['upload_ext']) && '.' !== $this->options['upload_ext']) {
                echo '<div class="upload-requirements-format">'.esc_html__('Format', 'wpcloudplugins').': <em><span class="file-formats">'.str_replace('|', ' • ', strtoupper($this->options['upload_ext'])).'</span></em></div>';
            }

            if (!empty($min_file_size)) {
                echo '<div class="upload-requirements-size">'.esc_html__('Size', 'wpcloudplugins').": <em><span class='min-file-size' >{$min_file_size_str}</span> — <span class='max-file-size'>{$post_max_size_str}</span></em></div>";
            } elseif ($own_limit) {
                echo '<div class="upload-requirements-size">'.esc_html__('Maximum size', 'wpcloudplugins').": <em><span class='max-file-size'>{$post_max_size_str}</span></em></div>";
            }

          ?>
          </div>
        </div>
      </div>
    </div>
    <div class="fileupload-header-button">
      <button class='fileupload-add-button button' type="button" title="<?php echo ($this->options['maxnumberofuploads'] > 1 || '-1' === $this->options['maxnumberofuploads']) ? esc_html__('Add your files', 'wpcloudplugins') : esc_html__('Add your file', 'wpcloudplugins'); ?>"><i class="eva eva-plus-outline eva-lg"></i></button>
      <div class='tippy-content-holder'>
        <div class='tippy-content'>
          <ul>
            <li class="upload-add-file">
              <a title="<?php esc_html_e('Files', 'wpcloudplugins'); ?>">
                <i class="eva eva-file-add-outline eva-lg"></i> <?php echo ($this->options['maxnumberofuploads'] > 1 || '-1' === $this->options['maxnumberofuploads']) ? esc_html__('Files', 'wpcloudplugins') : esc_html__('File', 'wpcloudplugins'); ?>
              </a>
            </li>
            <?php if ('1' === $this->options['upload_folder'] && ($this->options['maxnumberofuploads'] > 1 || '-1' === $this->options['maxnumberofuploads'])) { ?>
            <li class="upload-add-folder">
              <a title="<?php esc_html_e('Folders', 'wpcloudplugins'); ?>">
                <i class="eva eva-folder-add-outline eva-lg"></i> <?php esc_html_e('Folders', 'wpcloudplugins'); ?>
              </a>
            </li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <!-- END UPLOAD BOX HEADER -->

  <!-- UPLOAD PROGRESS -->
  <div class="fileupload-global-progress">
    <div class="fileupload-global-progress-bar"></div>
  </div>
  <!-- END UPLOAD PROGRESS -->

  <!-- UPLOAD BOX LIST -->
  <div class="fileupload-list"
    style="<?php echo (!empty($this->options['maxheight'])) ? 'max-height:'.$this->options['maxheight'] : ''; ?>">
    <table role="table" class="fileupload-table">
      <tbody class="fileupload-table-body">

        <!-- UPLOAD BOX TEMPLATE ROW -->
        <tr class="fileupload-table-row fileupload-table-row-template" role="row">
          <td class="fileupload-table-cell" role="cell" style="flex: 1 1 100%;">
            <div class="fileupload-table-cell-icon"><img class="" src="" /></div>
            <div class="fileupload-table-cell-content">
              <div class="fileupload-table-cell-text fileupload-table-text-title"></div>
              <div class="fileupload-table-cell-text fileupload-table-text-subtitle"></div>
            </div>
          </td>

          <td class="fileupload-table-cell fileupload-table-cell-action" role="cell" style="flex: 1 0 auto;">
            <?php if ($this->get_user()->can_edit_description()) {?>
              <button type="button" title="<?php esc_html_e('Add description', 'wpcloudplugins'); ?>" class="upload-add-description"><i class="eva eva-edit-2-outline"></i> <?php esc_html_e('Description', 'wpcloudplugins'); ?></button>
            <?php } ?>
              <button type="button" title="<?php esc_html_e('Try to upload the file again', 'wpcloudplugins'); ?>" class="upload-redo"><i class="eva eva-refresh"></i></button>
          </td>

          <td class="fileupload-table-cell fileupload-table-cell-result" role="cell" style="flex: 0 0 64px;">
            <i title="<?php esc_html_e('Remove from queue', 'wpcloudplugins'); ?>" aria-label="<?php esc_html_e('Remove from queue', 'wpcloudplugins'); ?>" class="upload-remove eva eva-close eva-lg"></i>
            <i title="<?php esc_html_e('Abort upload', 'wpcloudplugins'); ?>" aria-label="<?php esc_html_e('Abort upload', 'wpcloudplugins'); ?>" class="upload-stop eva eva-stop-circle eva-lg"></i>
            <i class="upload-waiting eva eva-pause-circle-outline eva-lg"></i>
            <i class="upload-success eva eva-checkmark eva-lg"></i>
            <i class="upload-fail eva eva-alert-triangle-outline eva-lg"></i>
            <i class="upload-convert eva eva-settings-outline eva-lg eva-spin"></i>
          </td>

          <td class="fileupload-table-cell fileupload-table-cell-progress" role="cell" style="flex: 0 0 80px;">
            <div class="fileupload-loading-bar label-center" data-preset="circle" data-value="0"></div>
          </td>


        </tr>
        <!-- UPLOAD BOX END TEMPLATE ROW -->
      </tbody>
    </table>
  </div>
  <!-- END UPLOAD BOX LIST -->

  <!-- UPLOAD BOX FOOTER -->
  <div class="fileupload-footer">
    <div class="fileupload-footer-content">
      <button class="fileupload-start-button button" disabled><?php esc_html_e('Start Upload', 'wpcloudplugins'); ?></button>
    </div>
  </div>
  <!-- END UPLOAD BOX FOOTER -->

</div>

<!-- UPLOAD BOX DRAG & DROP -->
<div class="fileupload-drag-drop"></div>
<!-- END UPLOAD DRAG & DROP -->