<div class="wrap">
  <h2>Edge Suite - Settings</h2>

  <form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <?php settings_fields('edge_suite_options'); ?>
    <table class="form-table">

      <tr valign="top">
        <th scope="row">Default Composition</th>
        <td>
          <?php
          $selected = intval(get_option('edge_suite_comp_default'));
          echo edge_suite_comp_select_form('edge_suite_comp_default', $selected, false);
          ?>
          <br/>
        <span class="setting-description">
          Default Edge Composition that will be shown on all pages.
        </span>
        </td>
      </tr>


      <tr valign="top">
        <th scope="row">Blog Page Composition</th>
        <td>
          <?php
          $selected = intval(get_option('edge_suite_comp_homepage'));
          echo edge_suite_comp_select_form('edge_suite_comp_homepage', $selected);
          ?>
          <br/>
        <span class="setting-description">
          Edge Composition that will be shown on the homepage.
        </span>
        </td>
      </tr>

      <?php
        $server_post_size =  ini_get('post_max_size');
        $server_upload_size =  ini_get('upload_max_filesize');
        $server_max_size = $server_post_size < $server_upload_size ? $server_post_size : $server_upload_size;

        $file_size_hint = 'Your server supports files up to ' . $server_max_size;
        $file_size_hint .= ' (post_max_size: ' . $server_post_size . ' / upload_max_filesize: ' . $server_max_size . ').';
      ?>
      <tr valign="top">
          <th scope="row">Max upload file size</th>
          <td>
              <input type="text" name="edge_suite_max_size"
                     value="<?php echo intval(get_option('edge_suite_max_size')); ?>"/>
        <span class="setting-description">
          File size in MB<br/>
            This is the max size that your file uploads will be limited to. 5 MB is the default upload size.<br>
            <?php print $file_size_hint; ?>
        </span>
          </td>
      </tr>

      <tr valign="top">
          <th scope="row">Widget shortcode</th>
          <td>
            <?php
            $selected = intval(get_option('edge_suite_widget_shortcode')) == 1 ? 'checked="checked"' : '';
            ?>
            <p><input type="checkbox" name="edge_suite_widget_shortcode" value="1" <?php echo $selected; ?>"/>
                Enable usage of shortcodes in widgets</p>
          </td>
      </tr>

      <tr valign="top">
          <th scope="row">jQuery NoConflict mode (experimental)</th>
          <td>
            <?php
            $selected = intval(get_option('edge_suite_jquery_noconflict')) == 1 ? 'checked="checked"' : '';
            ?>
            <p><input type="checkbox" name="edge_suite_jquery_noconflict" value="1" <?php echo $selected; ?>"/>
                Run a separate instance of jQuery for Edge Animate.</p>
            <span class="setting-description">
                Activate this option if you experience problems with other jQuery-based JavaScript plugins.
                Edge Suite will initialize separate different jQuery versions.
            </span>
          </td>
      </tr>

      <tr valign="top">
          <th scope="row">Edge Suite debug mode</th>
          <td>
            <?php
            $selected = intval(get_option('edge_suite_debug')) == 1 ? 'checked="checked"' : '';
            ?>
            <p><input type="checkbox" name="edge_suite_debug" value="1" <?php echo $selected; ?>"/>
                Run Edge Suite in debug mode</p>
          </td>
      </tr>

      <tr valign="top">
          <th scope="row">Deactivation deletion</th>
          <td>
            <?php
            $selected = intval(get_option('edge_suite_deactivation_delete')) == 1 ? 'checked="checked"' : '';
            ?>
            <p><input type="checkbox" name="edge_suite_deactivation_delete" value="1" <?php echo $selected; ?>"/>
                Delete Edge Suite assets and settings on plugin deactivation</p>
            <span class="setting-description">
              Activate this option to delete all uploaded compositions (files and database entries) including all Edge Suite
              settings from wordpress when deactivating the plugin. This should be activated if you are unable to delete files
              manually through FTP and want to clean out Edge Suite completely.
            </span>
          </td>
      </tr>


    </table>

    <input type="hidden" name="action" value="update"/>
    <input type="hidden" name="page_options"
           value="edge_suite_max_size,edge_suite_comp_default,edge_suite_comp_homepage,edge_suite_deactivation_delete"/>

    <p class="submit">
      <input type="submit" class="button-primary"
             value="<?php _e('Save Changes') ?>"/>
    </p>

  </form>
</div>