<?php
// TODO: Why hasn't hook init been called?
edge_suite_boot();

// Just to make sure the user has the right to manage compositions. Not sure if
//  form submissions are really secured by menu encapsulation???
if (!current_user_can('edge_suite_administer')) {
  wp_die(__('You are not allowed to administer Edge Suite.', 'edge_suite'));
}

if(isset($_POST['action'])){
  if ($_POST['action'] == 'upload' && isset($_POST['upit']) && $_POST['upit'] == 'yes') {
    edge_suite_process_upload();
  }
  elseif ($_POST['action'] == 'delete') {
    edge_suite_process_delete();
  }
}


function edge_suite_process_delete() {
  if (isset($_POST['edge_suite_composition_delete']) && intval($_POST['edge_suite_composition_delete']) > 0) {
    $definition_id = intval($_POST['edge_suite_composition_delete']);
    $success = edge_suite_comp_delete_definition($definition_id);
    if ($success) {
      echo '<div style="background-color: rgb(255, 251, 204);" id="message" class="updated fade">';
      echo '<p>Deleted composition successfully.</p>';
      echo '</div>';
    }
    else {
      echo '<div class="error"><p>';
      echo 'An error occurred when trying to delete the composition.';
      echo '</p></div>';
    }
  }
}

function edge_suite_process_upload() {
  //Check the file extension
  try {
    $check_file = strtolower($_FILES['edge_suite_composition_upload']['name']);
    $ext_check = end(explode('.', $check_file));

    if (!in_array($ext_check, array('zip', 'gz', 'oam'))) {
      throw new Exception("Invalid filetype.<br />");
    }

    // Validate file size.
    $uploaded_size = $_FILES['edge_suite_composition_upload']['size'];
    $the_max_size = (get_option('edge_suite_max_size') != "") ? get_option('edge_suite_max_size') : 8;

    if ($uploaded_size > $the_max_size * 1024 * 1024) {
      throw new Exception("Your file is too large.<br />");
    }

    //Make sure the file upload box actually has something in it.
    if ($_FILES['edge_suite_composition_upload']['name'] == "") {
      throw new Exception("You didn't select a file to upload<br />");
    }

    // Init file system
    // Todo: this doesn't work with FTP
    //WP_Filesystem();

    $tmp_file = EDGE_SUITE_PUBLIC_DIR . '/tmp/' . $_FILES['edge_suite_composition_upload']['name'];
    if (!is_dir(EDGE_SUITE_PUBLIC_DIR . '/tmp')) {
      if(!mkdir_recursive(EDGE_SUITE_PUBLIC_DIR . '/tmp')){
        throw new Exception("Edge suite tmp could not be created.<br />");
      }
    }
    if(!dir_is_writable(EDGE_SUITE_PUBLIC_DIR . '/tmp/')){
      throw new Exception("Edge suite tmp is not writable.<br />");
    }

    if (move_uploaded_file($_FILES['edge_suite_composition_upload']['tmp_name'], $tmp_file)) {

      edge_suite_comp_create($tmp_file, TRUE);
      echo '<div style="background-color: rgb(255, 251, 204);" id="message" class="updated fade">';
      echo "<p>The file " . basename($_FILES['edge_suite_composition_upload']['name']) . " has been uploaded.</p>";
      echo get_messages();
      echo "</div>";
    }
    else {
      throw new Exception("Sorry, there was a problem uploading your file. You may need to check your folder permissions or other server settings.");
    }
  } catch (Exception $e) {
    echo '<div class="error"><p>';
    echo $e->getMessage();
    echo '</p></div>';
  }
}

?>

<div class="wrap">
  <h2>Edge Suite - Manage compositions</h2>

  <?php
  $msg = check_filesystem();
    if(!empty($msg)){
      echo '<div class="error"><p>';
      echo implode('</br>',$msg);
      echo '</p></div>';
    }
  ?>

    <h3>Upload new composition</h3>
    <form enctype="multipart/form-data" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
        <input name="edge_suite_composition_upload" type="file" />
        <input type="hidden" name="upit" value="yes" />
        <input type="hidden" name="action" value="upload" />
        <p>Upload a new Adobe Edge composition. </br>
           Use Adobe Edge OAM project files which are being generated when publishing the project with option "Animate Deployment Package".
        </p>
        <p class="submit">
            <input type="submit" class="button-primary" value="Upload" />
        </p>
    </form>

<h3>Delete selected composition</h3>

<form enctype="multipart/form-data" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
  <?php
    echo edge_suite_comp_select_form('edge_suite_composition_delete', -1, false);
  ?>
  <input type="hidden" name="upit" value="yes" />
  <input type="hidden" name="action" value="delete" />
  <p class="submit">
    <input type="submit" class="button-primary" value="Delete" />
  </p>
</form>

</div>