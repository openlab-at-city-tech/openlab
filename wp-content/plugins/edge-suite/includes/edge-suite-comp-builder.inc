<?php

/**
 * @file
 * The main class to extract, modify and import files from an edge archive
 *
 * The main workflow is:
 *
 * - Extract archive in tmp dir
 * - Find the working directory with the main JS Edge file (_edge.js) and get
 *   the project name and composition id from it
 * - Alter _edge.js file to call custom JS function at runtime which alters path
 *   prefixes in the DOM
 * - Alter _edgePreload.js file to call custom JS function at runtime which
 *   alters path prefixes in the DOM and the preloader object
 * - replace the general edge stage id (id of the main DIV container) with a
 *   project specific id. This is really important to run multiple compositions
 *   along each other on one page
 */


class EdgeCompositionBuilder {

  /**
   * Main archive file which contains the composition
   */
  protected $file = '';

  /**
   * Tmp directory that the archive will be extracted to
   */
  protected $tmpDirBase = "";

  /**
   * Tmp directory that contains the composition files, sub dir of $tmpDirBase
   */
  protected $tmpDir = "";


  /**
   *  Base edge directory
   */
  protected $edgeDir = '';

  /**
   *  Base edge directory for all projects
   */
  protected $edgeProjectDir = '';

  /**
   * Path of the project
   */
  protected $destinationDir = "";

  /**
   * Path of the main edge file _edge.js
   */
  protected $mainEdgeJS = "";

  /**
   * Path of the preloader file _edgePreload.js
   */
  protected $preloaderFile = "";

  /**
   * Name of the project
   */
  protected $projectName = "";

  /**
   * Composition Id
   */
  protected $stageClass = "";

  /**
   * Version of Edge that the composition uses.
   */
  protected $edgeVersion = '';

  /**
   * Dimensions of the composition.
   */
  protected $dimensions;

  /**
   * Libraries that are being used by the composition
   */
  protected $libraries = array();

  /**
   * Build status
   */
  protected $buildSuccess = FALSE;


  /**
   * Default constructor which sets up filename and directory paths.
   *
   * @param string $base_dir
   *   Edge base directory.
   * @param string $project_dir
   *   Edge project directory
   */
  public function __construct($base_dir, $project_dir) {
    $this->edgeDir = $base_dir;
    $this->edgeProjectDir = $project_dir;

    $this->tmpDirBase = $base_dir . '/edge_tmp' . '/src_' . time(); //$user->uid;
    $this->tmpDir = $this->tmpDirBase;

    if (!is_dir($base_dir . '/edge_tmp')) {
      if(!mkdir_recursive($base_dir . '/edge_tmp')){
        throw new Exception ("Can't create edge_tmp directory.");
      };
    }

    if (!is_dir($this->tmpDirBase)) {
      if(!mkdir_recursive($this->tmpDirBase)){
        throw new Exception ("Can't create project tmp directory.");
      }
    }

    if (!is_dir($this->edgeProjectDir)) {
      if(!mkdir_recursive($this->edgeProjectDir)){
        throw new Exception ("Can't project directory.");
      }
    }

    if(!dir_is_writable($base_dir . '/edge_tmp')){
      throw new Exception("edge_tmp is not writable.<br />");
    }

    if(!dir_is_writable($this->tmpDirBase)){
      throw new Exception("Project tmp directory is not writable.<br />");
    }

    if(!dir_is_writable($this->edgeProjectDir)){
      throw new Exception("Specific project directory is not writable.<br />");
    }

    $this->dimensions = array(
      'width' => 0,
      'height' => 0,
      'min-width' => 0,
      'max-width' => 0,
      'min-height' => 0,
      'max-height' => 0,
    );
  }

  /**
   * Name of the project, after the composition has been processed.
   * @return string
   *   Project name
   */
  public function getProjectName() {
    return $this->projectName;
  }

  /**
   * Composition Id (class name) of the project.
   * @return string
   *   Composition Id (class name)
   */
  public function getCompositionId() {
    return $this->stageClass;
  }

  /**
   * Return the edge version.
   * @return string
   *   Edge version as string, e.g. 0.1.6
   */
  public function getEdgeVersion() {
    return $this->edgeVersion;
  }

  /**
   * Return build status.
   * @return bool
   *   true if build was successful, false otherwise
   */
  public function getBuildSuccess() {
    return $this->buildSuccess;
  }

  /**
   * Return the dimensions and its limits of the composition stage.
   * @return array
   *   dimension values for width, height including  min and max values
   */
  public function getDimensions() {
    return $this->dimensions;
  }

  /**
   * Main function to process/build an edge composition.
   *
   * @param string $filename
   *   Archive to extract.
   * @param string $destination_dirname
   *   Project destination folder.
   * @param bool $replace_project
   *   True if an existing project should be overwritten.
   * @param bool $replace_libraries
   *   True if existing libraries in the shared folder should be overwritten
   *
   * @return bool
   *   true if build process was successful, false otherwise
   * @throws Exception
   */
  public function processArchive($filename, $destination_dirname, $replace_project = FALSE, $replace_libraries = FALSE) {
    $this->file = $filename;
    $this->destinationDir = $this->edgeProjectDir . '/' . $destination_dirname;

    // Check if destination already exists and if it should be replaced. Other
    // abort as there is no valid destination available.
    if (file_exists($this->destinationDir)) {
      if ($replace_project) {
        rmdir_recursive($this->destinationDir);
      }
      else {
        throw new Exception('The composition directory already exists. Please use the according action to replace a composition.');
      }
    }
    if(!mkdir_recursive($this->destinationDir)){
      throw new Exception ('The project directory could not be created.');
    }

    // Extract the archive.
    $this->extract($this->file, $this->tmpDirBase);

    // Find the working directory, set _edge.js file, project name and comp id.
    $this->validateArchiveStructure();

    // Get the preloader file _edgePreload.js.
    $this->getPreloader();

    // Alter main JS edge file to so it calls custom JS from edge_drupal.js.
    $this->alterMainEdgeJs();

    // Alter preload JS file so it calls custom JS from edge_drupal.js.
    $this->alterPreloaderJs();

    // Read in all the libraries and copy them to a shared edge library folder.
    $this->readLibs($replace_libraries);

    // Move all assets (and the _edgeAction.js)
    $this->moveEdgeFiles($this->tmpDir, $this->destinationDir);

    // Get a list of the remaining files and tell the user these files will
    // be ignored.
    $ignored_files = file_scan_directory($this->tmpDir, '/\.*$/');

    // Generate ignored and obsolete file messages.
    $files_names_obsolete = array();
    $files_names_ignored = array();
    foreach ($ignored_files as $f) {
      if (!is_dir($f)) {
        $filename = substr($f, strlen($this->tmpDir));
        if ($filename == $this->projectName . '.edge' || $filename == $this->projectName . '.html') {
          $files_names_obsolete[] = $filename;
        }
        else {
          $files_names_ignored[] = $filename;
        }
      }
    }
    $this->setFileMessages($files_names_obsolete, 'The following files were ignored as they are not needed:');
    $this->setFileMessages($files_names_ignored, 'The following files were ignored as they are not needed/supported:', 'warning');

    // Delete the tmp archive directory with all remaining files.
    $this->cleanup();

    // Set build success.
    $build_success = TRUE;

    return $build_success;
  }

  /**
   * Sets the message followed by a list of file names.
   *
   * @param array $file_list
   *   List of file names
   * @param string $message
   *   Message to be set on top of the list (will be processed through t())
   * @param string $type
   *   Standard Drupal message type
   */
  protected function setFileMessages($file_list, $message, $type = 'status') {
    if (!empty($file_list)) {
      $files_list_string = $message . '</br>';
      foreach ($file_list as $file) {
        $files_list_string .= check_plain($file) . '</br>';
      }
      set_message($files_list_string, $type);
    }
  }

  /**
   * Delete the tmp archive directory with all remaining files.
   */
  public function cleanup() {
    if (file_exists($this->tmpDirBase)) {
      rmdir_recursive($this->tmpDirBase);
    }
  }

  /**
   * Extracts the archive and sets the path to the working directory (tmpDir).
   *
   * @throws Exception
   */
  protected function extract($file, $destination) {
    // Check if file really exists.
    if (!file_exists($this->file)) {
      throw new Exception('Could not find archive: ' . $file . '.');
    }

    // Todo: Directory seems to always exist already
    // Delete old edge tmp dir.
    if (is_dir($destination)) {
      rmdir_recursive($destination);
    }

    // Get archiver.
    $result = unzip($file, $destination);
    if (is_wp_error($result)) {
      throw new Exception('Extraction of ' . $file . ' failed: ' . $result->get_error_message());
    }
    // Add ignored files
    if(is_array($result)){
      // Todo: handle ignored files
    }
  }

  /**
   * Finds the main edge file _edge.js, sets the project name.
   *
   * @throws Exception
   */
  protected function validateArchiveStructure() {
    // Find all files that match the pattern.
    $main_edge_files = file_scan_directory($this->tmpDir, '/_edge\.js$/');
    if (count($main_edge_files) == 0) {
      throw new Exception('Aborting, no file found that matches the main edge JS filename pattern. This is not a valid composition archive.');
    }
    else {
      $main_edge_file = array_shift($main_edge_files);
      $this->tmpDir = dirname($main_edge_file);
      $this->mainEdgeJS = $main_edge_file;

      $file_info = pathinfo($main_edge_file);
      $project_name_tmp = substr($file_info['filename'], 0, strlen($file_info['filename']) - strlen('_edge'));

      // Sanitize project name.
      if (preg_match('/^[a-zA-Z0-9_-]*$/', $project_name_tmp) == 1) {
        $this->projectName = $project_name_tmp;
      }
      else {
        throw new Exception('Aborting, the project name is not valid.');
      }

      // Check if there were multiple main js files.
      if (count($main_edge_files) > 0) {
//        $msg_dir = substr($this->tmpDir, strlen($this->tmpDirBase));
//        $msg = 'Multiple edge.js files found. Everything outside ' . $msg_dir . ' will be ignored.';
      }
    }
  }

  /**
   * Finds the preloader file _edgePreload.js.
   *
   * @throws Exception
   */
  protected function getPreloader() {
    if (!empty($this->projectName)) {
      $js_file_tmp = $this->tmpDir . '/' . $this->projectName . '_edgePreload.js';
    }
    else {
      throw new Exception('Cannot read main JS edge file, project name not set.');
    }

    if (file_exists($js_file_tmp)) {
      $this->preloaderFile = $js_file_tmp;
    }
    else {
      throw new Exception('Cannot find main JS edge file ' . $js_file_tmp . '.');
    }
  }

  /**
   * Alters the main edge file.
   *
   * A calls to alterDomPaths() will be added to add path prefixes to the DOM
   * assets.
   *
   * @throws Exception
   */
  protected function alterMainEdgeJs() {
    $file = $this->mainEdgeJS;
    // Do an extensive file / permission check as this seems to fail with many server configs.
    if(!file_exists($file)){
      throw new Exception('Main edge file ' . $file . ' does not exist.');
    }

    if(!is_readable($file)){
      throw new Exception('Main edge file' . $file . ' exists but is not readable.');
    }

    $content = file_get_contents($file);
    if(!isset($content) || empty($content)){
      throw new Exception('Main edge file ' . $file . ' does exist, cleared is_readable check, but can not be read.');
    }

    $this->parseDimensions($content);

    // Extract the composition id.
    // The relevant JS structure (might be minified).
    // (function($, Edge, compId){....})(jQuery, AdobeEdge, "MyProject");.
    $pattern = '/}\)\(jQuery,\s?AdobeEdge,\s?\"([a-zA-Z0-9_-]*)\"/';
    $matches = array();
    $found = preg_match($pattern, $content, $matches);
    if (!$found) {
      throw new Exception('Unable to parse stage name from main edge file');
    }
    $this->stageClass = $matches[1];

    // Replace alterRegisterCompositionDefn() with custom call to alter
    // symbols and fonts. Older EA versions don't have the opts parameter.
    $register_pattern = '/Edge\.registerCompositionDefn\(compId,\s?symbols,\s?fonts,\s?resources(,\s?opts)?\);/';
    $register_matches = array();
    if (preg_match($register_pattern, $content, $register_matches)) {
      $opts = 'null';
      if (isset($register_matches[1])) {
        $opts = 'opts';
      }
      $register_replace = 'AdobeEdge.alterRegisterCompositionDefn(compId,symbols,fonts,resources,' . $opts . ',Edge.registerCompositionDefn);';
      $register_pattern_found = 0;
      $content = preg_replace($register_pattern, $register_replace, $content, -1, $register_pattern_found);
    }
    else {
      throw new Exception('Unable to alter register handler.');
    }

    // Write modified file.
    $fh = fopen($file, "w");
    fwrite($fh, $content);
    fclose($fh);
  }

  protected function parseDimensions($content){
    // Parse dimensions of the composition.
    // Uncompressed version.
    // Get the stage section first.
    $pattern_stage_style = '/"\${_stage}":\s?\[\n?\r?((\s*\[(.)*\],?)*)/i';
    $matches = array();
    if (preg_match($pattern_stage_style, $content, $matches)) {
      $matches_dimensions = array();
      // Parse the actual pixel.
      $dimension_keys = implode('|', array_keys($this->dimensions));
      $pattern_stage_size = '/\["style",\s?"(' . $dimension_keys . ')*",\s?\'([0-9]*(px|%))\'\]/';
      if (count($matches) > 1 && preg_match_all($pattern_stage_size, $matches[1], $matches_dimensions) && count($matches_dimensions) == 4 && count($matches_dimensions[1]) == count($matches_dimensions[2])) {
        $keys = $matches_dimensions[1];
        $values = $matches_dimensions[2];

        foreach (array_keys($this->dimensions) as $dimension_key) {
          $pos = array_search($dimension_key, $keys);
          if ($pos !== FALSE) {
            $this->dimensions[$dimension_key] = $values[$pos];
          }
        }
      }
    }

    // Minified version.
    // Find stage variable first.
    $stage_variable_match = array();
    $dimension_error_msg = '';
    if (preg_match('/(e\d*)=\'\$\{_Stage\}\'/', $content, $stage_variable_match)) {
      $stage_variable = $stage_variable_match[1];

      // Find property function calls for stage variable.
      $pattern_stage_style = '/;A1.A\(' . $stage_variable . '\)(\.[PT]\([a-z0-9,%"_\.]+\))*;/i';
      $matches = array();
      if (preg_match($pattern_stage_style, $content, $matches)) {
        $properties = array();
        // Explode property values.
        if (preg_match_all('/P\(([a-z0-9,%"_]+)\)/i', $matches[0], $properties)) {
          foreach($properties[1] as $property_string){
            $property_values = explode(',',$property_string);
            // Full properties: P(h,280,_,_,p) or P(w,100,_,_,"%").
            if(count($property_values) == 5){
              // Check if it's pixel or relative measure.
              $measure = '';
              if($property_values[4] == '"%"'){
                $measure = '%';
              }
              else if($property_values[4] == 'p'){
                $measure = 'px';
              }
              else{
                continue;
              }
            }
            //P(xw,600)
            if(count($property_values) == 2){
              $measure = 'px';
            }

            if( !empty($measure)){
              switch($property_values[0]){
                case 'h':
                  $this->dimensions['height'] = $property_values[1] . $measure;
                  break;

                case 'w':
                  $this->dimensions['width'] = $property_values[1] . $measure;
                  break;

              }
            }
          }
        }
        else{
          $dimension_error_msg = 'Unable to explode stage properties.';
        }
      }
      else{
        $dimension_error_msg = 'Unable to spot stage property calls.';
      }
    }
    else{
      $dimension_error_msg = 'Unable to spot stage variable.';
    }

    if ($this->dimensions['height'] == 0 || $this->dimensions['width'] == 0){
      set_message(t('Auto detection of stage dimensions failed: ' . $dimension_error_msg));
    }
    else{
      set_message(t('Auto detection of stage dimensions successful.'));
    }
  }

  /**
   * Alters the edge preloader file by injecting custom function calls.
   *
   * A call to loadResources will be replaced by a call to the Edge-Drupal
   * bridge JS function alterPreloadPaths() which will add path prefixes to
   * all resource and the call the original loader.
   * Calls to alterDomPaths() will be added to add path prefixes to the DOM
   * assets as well
   */
  protected function alterPreloaderJs() {
    $file = $this->preloaderFile;
    $content = file_get_contents($file);

    // Search for the loadResource function call.
    $load_pattern = '/loadResources\(aLoader,\s?doDelayLoad\);/';

    // Replace original call with call to custom function, alters preload paths.
    $load_replace = 'AdobeEdge.alterPreloadPaths(compId, aLoader, doDelayLoad, loadResources);';
    $content = preg_replace($load_pattern, $load_replace, $content);

    // Search for the okToLaunchComposition function call.
    $launch_pattern = '/AdobeEdge\.okToLaunchComposition\(compId\)/';

    // Replace original call with call to custom function, alters jQuery.
    $launch_replace = 'AdobeEdge.alterOkToLaunchComposition(compId)';
    $content = preg_replace($launch_pattern, $launch_replace, $content);

    // Find the end of the main JS function and inject to function calls to
    // alter preContent and dlContent DOM, see JS function doc.
    // Expected structure (might be minified).
    // (function(compId){...})("MyProject").
    $dom_pattern = '/\}\)\(\s?\"' . $this->stageClass . '\"\);/';
    $dom_replace = 'AdobeEdge.alterDomPaths(preContent.dom, compId);' . "\n";
    $dom_replace .= 'AdobeEdge.alterDomPaths(dlContent.dom, compId);' . "\n";
    $dom_replace .= '})("' . $this->stageClass . '");';
    $content = preg_replace($dom_pattern, $dom_replace, $content);

    // Write new file.
    $fh = fopen($file, "w");
    fwrite($fh, $content);
    fclose($fh);
  }

  /**
   * Helper function which replaces a string in a file with str_replace.
   *
   * @param string $file
   *   The file to work with
   * @param string $search
   *   Search phrase
   * @param string $replace
   *   Replace phrase
   */
  protected function replaceStringInFile($file, $search, $replace) {
    $content = file_get_contents($file);

    $content = str_replace($search, $replace, $content);

    $fh = fopen($file, "w");
    fwrite($fh, $content);
    fclose($fh);
  }

  /**
   * Library handling.
   *
   * Checks which libraries are included in the project library directory and
   * copies them to the shared library folder.
   */
  protected function readLibs($overwrite = FALSE) {
    global $wp_filesystem;
    // Read edge common lib files.
    $edge_lib_files = file_scan_directory($this->tmpDir . '/edge_includes', '/.*/');

    // Set up shared folder if it doesn't exist.
    if (!is_dir($this->edgeDir . '/edge_includes')) {
      mkdir_recursive($this->edgeDir . '/edge_includes');
    }

    // Collect libs in array to save them in configuration.
    $lib_names = array();
    $lib_updates = $lib_ignored = $lib_added = array();
    foreach ($edge_lib_files as $l) {
      $lib_file_info = pathinfo($l);
      $lib = new stdClass();
      $lib->uri = $l;
      $lib->filename = $lib_file_info['basename'];
      // Only look at .js files.
      if (preg_match('/\.js$/', $lib->uri)) {
        // Check if lib already exists in shared folder.
        $exists = FALSE;
        if (file_exists($this->edgeDir . '/edge_includes/' . $lib->filename)) {
          $exists = TRUE;
        }


        if ($overwrite || !$exists) {
          $moved = move_file($lib->uri, $this->edgeDir . '/edge_includes/' . $lib->filename);
          if (!$moved) {
            set_message('Library ' . check_plain($lib->filename) . ' could not be added/moved.');
          }
          if ($overwrite) {
            $lib_updates[] = $lib->filename;
          }
          else {
            $lib_added[] = $lib->filename;
          }
        }
        else {
          $lib_ignored[] = $lib->filename;
          unlink($lib->uri);
        }
        // Add to lib array.
        $lib_names[] = $lib->filename;

        // Check for edge version, look for e.g. edge.0.1.6.min.js.
        if (empty($this->edgeVersion)) {
          $matches = array();
          preg_match('/edge\.([\.0-9]*)(?:\.min)?\.js$/', $lib->filename, $matches);
          if (count($matches) > 0) {
            $this->edgeVersion = $matches[1];
          }
        }
      }
    }

    // Output library file messages.
    $this->setFileMessages($lib_updates, 'The following libraries have been updated:');
    $this->setFileMessages($lib_added, 'The following libraries have been added:');
    $this->setFileMessages($lib_ignored, 'The following libraries are already present on the server, the existing versions will be used:');

    $this->libraries = $lib_names;
  }

  /**
   * Copies all asset files with certain extensions.
   *
   * Copies files with js,png,... extension from the tmp
   * project directory to the permanent project directory.
   */
  protected function moveEdgeFiles($src, $dest) {

    $files = file_scan_directory($src, '/\.(' . EDGE_SUITE_ALLOWED_ASSET_EXTENSIONS . ')$/');
    // TODO: Does this work with nested folders?
    // Todo: Check mimetype. Are mime types calculated from the file extension?
    foreach ($files as $f) {
      $dir = dirname($f);
      // Create folder structure in the permanent directory.
      // Get dir path relative to project tmp base path without leading slash.
      $rel_dir = substr($dir, strlen($src) + 1);
      if(substr($rel_dir, 0 ,8) === 'publish/'){
        continue;
      }
      if (!file_exists($dest . '/' . $rel_dir)) {
        mkdir_recursive($dest . '/' . $rel_dir);
      }
      // TODO: Feedback if file couldn't be moved.
      $file_info = pathinfo($f);
      $moved = move_file($f, $dest . '/' . $rel_dir . '/' . $file_info['basename']);
    }
  }

}
