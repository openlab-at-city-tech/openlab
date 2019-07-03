<p>
    <?php _e('The CSV file contains one or more errors including:', 'openlab-gradebook')?><br />
    <strong><?php _e('Student does not exist or Unacceptable grade type', 'openlab-gradebook')?></strong>
    <p><?php _e('Please click Download Error Messages below to review the error messages in the final column and then reupload the corrected CSV.', 'openlab-gradebook')?></p>
    <a href="<?php echo $csv_link ?>"><?php _e('Download Error Messages', 'openlab-gradebook');?></a>
</p>