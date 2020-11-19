<div id="OutoftheBox" class="OutoftheBoxDashboard">
  <div class="outofthebox admin-settings">

    <div class="wrap">
      <div class="outofthebox-header">
                <div class="outofthebox-logo"><a href="https://www.wpcloudplugins.com" target="_blank"><img src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/wpcp-logo-dark.svg" height="64" width="64"/></a></div>
        <div class="outofthebox-form-buttons"> <div id="clear_statistics" class="simple-button default clear_statistics" name="clear_statistics"><?php _e('Clear all Statistics','wpcloudplugins'); ?>&nbsp;<div class='oftb-spinner'></div></div></div>
        <div class="outofthebox-title"><?php _e('Reports','wpcloudplugins'); ?></div>
      </div>

      <div class="outofthebox-panel">
        <div id="outofthebox-totals">
          <div class="outofthebox-box outofthebox-box25">
            <div class="outofthebox-box-inner ">
              <div class="outofthebox-option-title nopadding">
                <div class="outofthebox-counter-text"><?php echo __('Total Previews', 'wpcloudplugins'); ?> </div>
                <div class="outofthebox-counter" data-type="outofthebox_previewed_entry">
                  <span>
                    <div class="loading"><div class='loader-beat'></div></div>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="outofthebox-box outofthebox-box25">
            <div class="outofthebox-box-inner">
              <div class="outofthebox-option-title nopadding">
                <div class="outofthebox-counter-text"><?php echo __('Total Downloads', 'wpcloudplugins'); ?></div>
                <div class="outofthebox-counter" data-type="outofthebox_downloaded_entry">
                  <span>
                    <div class="loading"><div class='loader-beat'></div></div>
                  </span>
                </div></div>
            </div>
          </div>

          <div class="outofthebox-box outofthebox-box25">
            <div class="outofthebox-box-inner">
              <div class="outofthebox-option-title nopadding">
                <div class="outofthebox-counter-text"><?php echo __('Items Shared', 'wpcloudplugins'); ?></div>
                <div class="outofthebox-counter" data-type="outofthebox_created_link_to_entry">
                  <span>
                    <div class="loading"><div class='loader-beat'></div></div>
                  </span>
                </div></div>
            </div>
          </div>

          <div class="outofthebox-box outofthebox-box25">
            <div class="outofthebox-box-inner">
              <div class="outofthebox-option-title nopadding">
                <div class="outofthebox-counter-text"><?php echo __('Documents Uploaded', 'wpcloudplugins'); ?></div>
                <div class="outofthebox-counter" data-type="outofthebox_uploaded_entry">
                  <span>
                    <div class="loading"><div class='loader-beat'></div></div>
                  </span>
                </div></div>
            </div>
          </div>
        </div>

        <div class="outofthebox-box">
          <div class="outofthebox-box-inner">
            <div class="outofthebox-event-date-selector">
              <label for="chart_datepicker_from"><?php echo __('From', 'wpcloudplugins'); ?></label>
              <input type="text" class="chart_datepicker_from" name="chart_datepicker_from">
              <label for="chart_datepicker_to"><?php echo __('to', 'wpcloudplugins'); ?></label>
              <input type="text" class="chart_datepicker_to" name="chart_datepicker_to">
            </div>
            <div class="outofthebox-option-title"><?php echo __('Events per Day', 'wpcloudplugins'); ?></div>
            <div class="outofthebox-events-chart-container" style="height:500px !important; position:relative;">
              <div class="loading"><div class='loader-beat'></div></div>
              <canvas id="outofthebox-events-chart"></canvas>
            </div>
          </div>
        </div>

        <div class="outofthebox-box outofthebox-box50">
          <div class="outofthebox-box-inner">
            <div class="outofthebox-option-title"><?php echo __('Top 25 Downloads', 'wpcloudplugins'); ?></div>
            <table id="top-downloads" class="stripe hover order-column" style="width:100%">
              <thead>
                <tr>
                  <th></th>
                  <th><?php echo __('Document', 'wpcloudplugins'); ?></th>
                  <th><?php echo __('Total', 'wpcloudplugins'); ?></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div class="outofthebox-box outofthebox-box50">
          <div class="outofthebox-box-inner">
            <div class="outofthebox-option-title"><?php echo __('Top 25 Users with most Downloads', 'wpcloudplugins'); ?></div>
            <table id="top-users" class="display" style="width:100%">
              <thead>
                <tr>
                  <th></th>
                  <th><?php echo __('User', 'wpcloudplugins'); ?></th>
                  <th><?php echo __('Username'); ?></th>
                  <th><?php echo __('Downloads', 'wpcloudplugins'); ?></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div class="outofthebox-box">
          <div class="outofthebox-box-inner">
            <div class="outofthebox-event-date-selector">
              <label for="chart_datepicker_from"><?php echo __('From', 'wpcloudplugins'); ?></label>
              <input type="text" class="chart_datepicker_from" name="chart_datepicker_from">
              <label for="chart_datepicker_to"><?php echo __('to', 'wpcloudplugins'); ?></label>
              <input type="text" class="chart_datepicker_to" name="chart_datepicker_to">
            </div>
            <div class="outofthebox-option-title"><?php echo __('All Events', 'wpcloudplugins'); ?></div>
            <table id="full-log" class="display" style="width:100%">
              <thead>
                <tr>
                  <th></th>
                  <th class="all"><?php echo __('Description', 'wpcloudplugins'); ?></th>
                  <th><?php echo __('Date', 'wpcloudplugins'); ?></th>
                  <th><?php echo __('Event', 'wpcloudplugins'); ?></th>
                  <th><?php echo __('User', 'wpcloudplugins'); ?></th>
                  <th><?php echo __('Name', 'wpcloudplugins'); ?></th>
                  <th><?php echo __('Location', 'wpcloudplugins'); ?></th>
                  <th><?php echo __('Page', 'wpcloudplugins'); ?></th>
                  <th><?php echo __('Extra', 'wpcloudplugins'); ?></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div class="event-details-template" style="display:none;">
          <div class="event-details-name"></div>

          <div class="outofthebox-box outofthebox-box25">
            <div class="outofthebox-box-inner">
              <div class="event-details-user-template" style="display:none;">
                <div class="event-details-entry-img"></div>
                <a target="_blank" class="event-visit-profile event-button simple-button blue"><i class="fas fa-external-link-square-alt"></i>&nbsp;<?php echo __('Visit Profile'); ?></a>

                <div class="loading"><div class="loader-beat"></div></div>
              </div>

              <div class="event-details-entry-template" style="display:none;">
                <div class="event-details-entry-img"></div>
                <p class="event-details-description"></p>
                <a target="_blank" class="event-download-entry event-button simple-button blue" download><i class="fas fa-arrow-down"></i>&nbsp;<?php echo __('Download'); ?></a>

                <div class="loading"><div class="loader-beat"></div></div>
              </div>

              <br/>

              <div class="event-details-totals-template">
                <div class="outofthebox-option-title tbpadding10 ">
                  <div class="outofthebox-counter-text"><?php echo __('Previews', 'wpcloudplugins'); ?> </div>
                  <div class="outofthebox-counter" data-type="outofthebox_previewed_entry">
                    <span>
                      <div class="loading"><div class='loader-beat'></div></div>
                    </span>
                  </div>
                </div>

                <div class="outofthebox-option-title tbpadding10">
                  <div class="outofthebox-counter-text"><?php echo __('Downloads', 'wpcloudplugins'); ?></div>
                  <div class="outofthebox-counter" data-type="outofthebox_downloaded_entry">
                    <span>
                      <div class="loading"><div class='loader-beat'></div></div>
                    </span>
                  </div>
                </div>

                <div class="outofthebox-option-title tbpadding10">
                  <div class="outofthebox-counter-text"><?php echo __('Shared', 'wpcloudplugins'); ?></div>
                  <div class="outofthebox-counter" data-type="outofthebox_created_link_to_entry">
                    <span>
                      <div class="loading"><div class='loader-beat'></div></div>
                    </span>
                  </div>
                </div>

                <div class="outofthebox-option-title tbpadding10">
                  <div class="outofthebox-counter-text"><?php echo __('Uploads', 'wpcloudplugins'); ?></div>
                  <div class="outofthebox-counter" data-type="outofthebox_uploaded_entry">
                    <span>
                      <div class="loading"><div class='loader-beat'></div></div>
                    </span>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="outofthebox-box outofthebox-box75 event-details-table-template">
            <div class="outofthebox-box-inner">
              <div class="outofthebox-option-title"><?php echo __('Logged Events', 'wpcloudplugins'); ?></div>
              <table id="full-detail-log" class="display" style="width:100%">
                <thead>
                  <tr>
                    <th></th>
                    <th class="all"><?php echo __('Description', 'wpcloudplugins'); ?></th>
                    <th><?php echo __('Date', 'wpcloudplugins'); ?></th>
                    <th><?php echo __('Event', 'wpcloudplugins'); ?></th>
                    <th><?php echo __('User', 'wpcloudplugins'); ?></th>
                    <th><?php echo __('Name', 'wpcloudplugins'); ?></th>
                    <th><?php echo __('Location', 'wpcloudplugins'); ?></th>
                    <th><?php echo __('Page', 'wpcloudplugins'); ?></th>
                    <th><?php echo __('Extra', 'wpcloudplugins'); ?></th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
