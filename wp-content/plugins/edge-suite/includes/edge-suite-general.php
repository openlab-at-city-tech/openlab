<?php



// Not used yet
function fs_connect( $directories = array() ) {
  global $wp_filesystem;

  $url = "edge_suite";
  if ( false === ($credentials = request_filesystem_credentials($url)) )
    return false;

  if ( ! WP_Filesystem($credentials) ) {
    $error = true;
    if ( is_object($wp_filesystem) && $wp_filesystem->errors->get_error_code() )
      $error = $wp_filesystem->errors;
    $this->skin->request_filesystem_credentials($error); //Failed to connect, Error and request again
    return false;
  }

  if ( ! is_object($wp_filesystem) )
    return new WP_Error('fs_unavailable', $this->strings['fs_unavailable'] );

  if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
    return new WP_Error('fs_error', $this->strings['fs_error'], $wp_filesystem->errors);

  foreach ( (array)$directories as $dir ) {
        if ( ! $wp_filesystem->find_folder($dir) )
          return new WP_Error('fs_no_folder', sprintf($this->strings['fs_no_folder'], $dir));
  }
  return true;
}

function check_filesystem(){
  $msg = array();

  //Check if dir is writable and create directory structure.
  if (!mkdir_recursive(EDGE_SUITE_COMP_PROJECT_DIR)) {
    $msg['create_folder'] = 'Edge Suite: Unable to create project directory (' . EDGE_SUITE_COMP_PROJECT_DIR . '). Is its parent directory writable by the server?';
  }


  if(!class_exists('ZipArchive')){
    $msg['zip'] = 'Your server is not able to extract zip files (PHP Class "ZipArchive" not found).';
  }


  if( ini_get('safe_mode') ){
    $msg['safemode'] = "PHP is running in safe mode, uploading compositions will not work.";
  }

  // Check file system method

  $method = null;
  $fs_msg = '';
  if ( function_exists('getmyuid') && function_exists('fileowner') ){
    $context = trailingslashit(EDGE_SUITE_PUBLIC_DIR);
    $temp_file_name = $context . 'temp-write-test-edge-' . time();
    $temp_handle = @fopen($temp_file_name, 'w');
    if ( $temp_handle ) {
      $myuid = getmyuid();
      $owner = @fileowner($temp_file_name);
      @fclose($temp_handle);
      @unlink($temp_file_name);
      if ( $myuid == $owner )
        $method = 'direct';
      else {
        $fs_msg = 'The owner of the scripts (most likely your FTP user) is different from the user that runs the PHP server process. </br>';
        $fs_msg .= 'Files handled through Edge Suite might not be manually deletable by you through FTP. <br>';
        $fs_msg .= 'Alternative uploading methods are on the roadmap for Edge Suite.';
      }
    }
    else{
      $fs_msg = 'Tmp test file could not be written to ' . EDGE_SUITE_PUBLIC_DIR;
    }
  }
  else{
    $fs_msg = 'PHP functions getmyuid() and fileowner() could not be found.';
  }
  if(!empty($fs_msg)){
    $msg['filesystem'] = $fs_msg;
  }

  return $msg;

}


function unzip($file, $to){
  if ( class_exists('ZipArchive') && apply_filters('unzip_file_use_ziparchive', true ) ) {

    require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
    require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
    $filesystem = new WP_Filesystem_Direct(null);

    // Set up default file/folder permissions (copied from WP_Filesystem()).
    if ( ! defined('FS_CHMOD_DIR') )
      define('FS_CHMOD_DIR', 0755 );
    if ( ! defined('FS_CHMOD_FILE') )
      define('FS_CHMOD_FILE', 0644 );


    if (!$filesystem->chmod($file, FS_CHMOD_FILE)){
      return new WP_Error('chmod_failed', __('Could not change permissions on archive.'));
    }

    $z = new ZipArchive();

    // PHP4-compat - php4 classes can't contain constants
    $zopen = $z->open($file, /* ZIPARCHIVE::CHECKCONS */ 4);
    $ignored = array();
    if ( true !== $zopen )
      return new WP_Error('incompatible_archive', __('Incompatible Archive.'));


    for ( $i = 0; $i < $z->numFiles; $i++ ) {
      if ( ! $info = $z->statIndex($i) )
        return new WP_Error('stat_failed', __('Could not retrieve file from archive.'));

      if ( '/' == substr($info['name'], -1) ) // directory
        continue;

      if ( '__MACOSX/' === substr($info['name'], 0, 9) ) // Don't extract the OS X-created __MACOSX directory files
        continue;

      if(!preg_match('/(.*)\.(' . EDGE_SUITE_ALLOWED_ASSET_EXTENSIONS . ')$/', $info['name'])){
        $ignored[] = $info['name'];
        continue;
      }

      if ( !mkdir_recursive(trailingslashit($to) . dirname($info['name']))) {
        return new WP_Error('dir_creation_failed', __('Could not create directories for file to be extracted.'), $to . $info['name']);
      }
      $contents = $z->getFromIndex($i);
      if ( false === $contents )
        return new WP_Error('extract_failed', __('Could not extract file from archive.'), $info['name']);

      if ( ! $filesystem->put_contents( trailingslashit($to) . $info['name'], $contents, FS_CHMOD_FILE) )
        return new WP_Error('copy_failed', __('Could not copy file.'), $to . $info['name']);
    }

    $z->close();

    return $ignored;

  }

  return false;
}

function file_scan_directory($dir, $pattern) {
  $files = list_files($dir);
  $matching_files = array();
  if($files !== FALSE){
    foreach ($files as $file) {
      if (preg_match($pattern, $file)) {
        $matching_files[] = $file;
      }
    }
  }
  return $matching_files;
}

function rmdir_recursive($dir) {
  $files = glob($dir . '/*');
  if($files !== FALSE){
    foreach($files as $file) {
      if(@is_dir($file))
        rmdir_recursive($file);
      else
        @unlink($file);
    }
  }
  @rmdir($dir);
}

function mkdir_recursive($path){
  if (wp_mkdir_p($path)) {
    return true;
  }
  else{
    return false;
  }
}

function dir_is_writable($path){
  //global $wp_filesystem;
  return is_writable($path);
}


function move_file($source, $destination){
  // Todo: add overwrite flag
  // Todo: copy new file, delete old file
  return rename($source, $destination);

}

function check_plain($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}


function set_message($msg, $type = 'status  ') {
  global $edge_suite;
  $edge_suite->msg[] = $msg;
}

function get_messages() {
  global $edge_suite;
  return "\n" . implode("</br>\n", $edge_suite->msg) . "\n";
}


function file_delete($file) {
  unlink($file);
}

function t($string, array $args = array()) {
  if (empty($args)) {
    return $string;
  }
  else {
    return format_string($string, $args);
  }
}

function format_string($string, array $args = array()) {
  // Transform arguments before inserting them.
  foreach ($args as $key => $value) {
    switch ($key[0]) {
      case '@':
        // Escaped only.
        $args[$key] = check_plain($value);
        break;

      case '%':
      default:
        // Escaped and placeholder.
        $args[$key] = '<em class="placeholder">' . check_plain($value) . '</em>';
        break;

      case '!':
        // Pass-through.
    }
  }
  return strtr($string, $args);
}


function update_record($table, $values, $key) {
  global $wpdb;
  $table_name = $wpdb->prefix . $table;
  $wpdb->update($table_name, $values, array($key => $values[$key]));
}

function write_record($table, $values) {
  global $wpdb;
  $table_name = $wpdb->prefix . $table;
  $wpdb->insert($table_name, $values);
  return $wpdb->insert_id;
}
