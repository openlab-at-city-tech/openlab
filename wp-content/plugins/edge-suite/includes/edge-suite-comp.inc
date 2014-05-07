<?php
/**
 * @file
 * Edge suite composition functionality.
 */

/* COMPOSITION DEFINITION FUNCTIONS */

/**
 * Creates a new composition from an archive.
 *
 * Extracts the given archive, processes all the files and adds entries
 *
 * @param object $file
 *   The archive file (as Drupal file object) which contains the composition
 * @param bool $replace_libs
 *   Set to true if libraries of the composition should overwrite existing libs.
 * @param int $definition_id_current
 *   Set definition id of existing composition id to replace the composition
 *   with the new composition from the archive.
 *
 * @return int
 *   Returns the definition id of the build definition
 * @throws Exception
 */
function edge_suite_comp_create($file, $replace_libs = FALSE, $definition_id_current = 0) {
  $success = FALSE;
  $definition_id = 0;

  // Check if the definition is being updated.
  $update = FALSE;

  // Set up project paths.
  global $current_user;
  $project_tmp_name = 'clean_u' . $current_user->ID . '_t' . REQUEST_TIME;
  $project_tmp_path = EDGE_SUITE_COMP_PROJECT_DIR . '/' . $project_tmp_name;

  // Check if main edge directory and project dir exists, if not create it.
  if (!is_dir(EDGE_SUITE_COMP_PROJECT_DIR)) {
    if (!is_dir(EDGE_SUITE_PUBLIC_DIR)) {
      mkdir_recursive(EDGE_SUITE_PUBLIC_DIR);
    }
    mkdir_recursive(EDGE_SUITE_COMP_PROJECT_DIR);
  }

  // Set up composition builder.
  require_once 'edge-suite-comp-builder.inc';
  $edge_build = new EdgeCompositionBuilder(EDGE_SUITE_PUBLIC_DIR, EDGE_SUITE_COMP_PROJECT_DIR);
  try {
    // The composition is not being replaced right away, it will be build first
    // and then copied to the right location if needed.
    $edge_build->processArchive($file, $project_tmp_name, FALSE, $replace_libs);

    $project_name = $edge_build->getProjectName();

    if (!empty($project_name)) {
      // Get archive file extension so it can be saved in the DB. The name of
      // the archive is build from data present the definition.
      $ext = pathinfo($file, PATHINFO_EXTENSION);

      $info = array(
        'version' => $edge_build->getEdgeVersion(),
      );
      $info += $edge_build->getDimensions();

      // Add composition definition to the DB.
      $def_record = array(
        'project_name' => $project_name,
        'composition_id' => $edge_build->getCompositionId(),
        'archive_extension' => $ext,
        'info' => serialize($info),
        'uid' => $current_user->ID,
        'created' => REQUEST_TIME,
        'changed' => REQUEST_TIME,
      );


      // Check if this is an update or insert and modify accordingly.
      if ($update) {
        $def_record['definition_id'] = $definition_id_current;
        update_record('edge_suite_composition_definition', $def_record, 'definition_id');
      }
      else {
        $def_record['definition_id'] = write_record('edge_suite_composition_definition', $def_record);
      }

      $definition_id = $def_record['definition_id'];
      if($definition_id == 0){
        throw new Exception('Definition id of 0, database entry could not be created.');
      }
      $def_name = edge_suite_comp_unique_name($def_record);

      $project_path = EDGE_SUITE_COMP_PROJECT_DIR . '/' . $def_name;
      move_file($project_tmp_path, $project_path);

      $success = TRUE;
    }
  } catch (Exception $e) {
    // Clean out all files.

    // Make sure source extraction directory gets cleaned out.
    $edge_build->cleanup();

    // Delete project files. Make sure both directories get deleted.
    if (file_exists($project_tmp_path)) {
      rmdir_recursive($project_tmp_path);
    }
    if (isset($project_path) && file_exists($project_path)) {
      rmdir_recursive($project_path);
    }

    // Hand exception to the next level.
    throw new Exception($e->getMessage());
    // TODO: Libraries might have been copied. Critical?
  }

  // Delete the tmp file no matter what. An unmanaged copy should be in the
  // source folder by now (to be able to rebuild).
  file_delete($file);

  if ($success) {
    set_message(t("@project was successfully imported.", array('@project' => $project_name)));
  }
  else {
    set_message(t("Errors occurred while creating the uploaded project. The import was most likely corrupted."), 'error');

  }

  return $definition_id;
}

/**
 * Builds a unique name for a composition definition.
 *
 * The unique name will be build through the user id, the definition id and
 * the project name, to provide maximal readability.
 *
 * @param object $definition
 *   Composition definition
 *
 * @return null|string
 *   Returns the name or null if the definition is not valid
 */
function edge_suite_comp_unique_name($definition) {
  // TODO: change signature.
  $name = NULL;
  if (isset($definition['definition_id']) && isset($definition['project_name'])) {
    $name = strtolower($definition['project_name']);
    $name .= '_' . $definition['definition_id'];
  }
  return $name;
}


/**
 * Load a composition definition.
 *
 * @param int $definition_id
 *   Id of the composition definition
 *
 * @return mixed|null
 *   Composition definition as an object
 */
function edge_suite_comp_load_definition($definition_id) {
  global $wpdb;

  $definition = NULL;

  $table_name = $wpdb->prefix . "edge_suite_composition_definition";
  $definition = $wpdb->get_row('SELECT * FROM ' . $table_name . ' WHERE definition_id = ' . $definition_id . ';');

  if ($definition != NULL) {
    // Grab composition.
    $definition->info = (object) unserialize($definition->info);
    $definition->project_name_unique = edge_suite_comp_unique_name((array) $definition);
  }
  return $definition;
}


/**
 * Fully delete a composition from the DB and filesystem.
 *
 * Deletes the definition, all instances and all files of the composition.
 *
 * @param int $definition_id
 *   Id of the composition definition
 *
 * @return bool
 *   True if composition was deleted, false otherwise
 */
function edge_suite_comp_delete_definition($definition_id) {
  global $wpdb;

  // Load composition definition to get all the necessary data.
  $definition = edge_suite_comp_load_definition($definition_id);

  WP_Filesystem();

  // Delete the unpacked files, means the project directory.
  $project_dir = EDGE_SUITE_COMP_PROJECT_DIR . '/' . $definition->project_name_unique;
  if (is_dir($project_dir)) {
    rmdir_recursive($project_dir);
  }

  $table_name = $wpdb->prefix . "edge_suite_composition_definition";
  $num = $wpdb->delete($table_name, array('definition_id' => $definition_id));

  // Todo: Delete meta entries for the composition?

  return $num > 0;
}


/**
 * Render the stage with the given values and configuration.
 *
 * @param object $definition_id
 *   The definition id.
 *
 * @return array
 *   Array with stage content and header scripts.
 */
function edge_suite_comp_render($definition_id, $styles = '', $data = array()) {
  $scripts = array();
  $stage = '';
  if ($definition_id > 0) {
    $definition = edge_suite_comp_load_definition($definition_id);

    if(!isset($definition->project_name)){
      return NULL;
    }

    $edge_lib_path = EDGE_SUITE_PUBLIC_DIR_REL;
    $project_path = EDGE_SUITE_COMP_PROJECT_DIR_REL . '/' . $definition->project_name_unique;

    $preload_url = $project_path . '/' . $definition->project_name . '_edgePreload.js';

    $js_inline = 'window.AdobeEdge = window.AdobeEdge || {};';
    $js_inline .= 'window.AdobeEdge.pathPrefix = window.AdobeEdge.pathPrefix || {};';
    $js_inline .= "AdobeEdge.pathPrefix.libs = '" . $edge_lib_path . "';";
    $js_inline .= "AdobeEdge.pathPrefix.comps = AdobeEdge.pathPrefix.comps || {};";
    $js_inline .= "AdobeEdge.pathPrefix.comps['" . $definition->composition_id . "'] = '" . $project_path . "';";

    if(get_option('edge_suite_debug') == 1){
      $js_inline .= "AdobeEdge.edgesuiteDebug = 1;";
    }

    if(get_option('edge_suite_jquery_noconflict') == 1){
      $js_inline .= "AdobeEdge.edgesuiteNoConflict = 1;";
    }

    $scripts = array();
    $scripts[] = '<script type="text/javascript">' . $js_inline . '</script>';
    $scripts[] = '<script type="text/javascript" src="' . plugins_url('/edge-suite') . '/includes/edge-wordpress.js"></script>';
    $scripts[] = '<script type="text/javascript" src="' . $preload_url . '"></script>';


    $div_id = 'Stage_' . $definition->project_name_unique;

    // Add dimension if it was possible to parse them. This avoids jumping of the
    // empty divs when using multiple instances on one page.
    $height = '';
    if (isset($definition->info->height) && intval($definition->info->height) > 0) {
      $height = 'height:' . $definition->info->height . ';';
    }

    $width = '';
    if (isset($definition->info->width) && intval($definition->info->width) > 0) {
      $width = 'width:' . $definition->info->width . ';';
    }

    // Generate JSON data for more advanced animations, if supplied.
    $data_string = '';
    if (isset($data) && !empty($data)) {
      $data_string = '<script class="data" type="text/data">' . json_encode($data) . '</script>';
    }

    // Put everything together.
    $stage = '<p><div id="' . $div_id . '" style="' . $height . $width . $styles . '" class="' . $definition->composition_id . '">' . $data_string .  '</div></p>';

  }

  return array('scripts' => $scripts, 'stage' => $stage);
}


/**
 * Renders an iframe with the according callback url
 *
 * @param $definition_id Id of the composition definition to be viewed
 * @param string $css_iframe_style CSS styles that will be added to the iframe
 * @return string HTML for the iframe with the specific composition url
 */
function edge_suite_comp_iframe($definition_id, $css_iframe_style = '') {
    $frame = '';
    if ($definition_id > 0) {
      $definition = edge_suite_comp_load_definition($definition_id);

      $url = get_bloginfo('wpurl') . '/' . 'index.php?edge_suite_iframe=' . $definition_id;
      // Get dimensions and set iframe.
      $width = isset($definition->info->width) ? $definition->info->width : 0;
      $height = isset($definition->info->height) ? $definition->info->height : 0;
      $style = 'style="width:' . $width . ';height:' . $height . ';' . $css_iframe_style . '" ';
      $style .= 'scrolling="no" marginheight="0" marginwidth="0" frameborder="0"';
      $frame = '<iframe src="' . $url . '" name="composition-' . $definition_id . '" ' . $style . '></iframe> ';
    }
    return $frame;
}

/**
 * Renders a full HTML page for the given definition id.
 *
 * @param integer definition id of the composition to render.
 *
 * @return string
 *   Returns the full HTML document, ready to be printed.
 *
 */
function edge_suite_comp_full_page($definition_id) {

  $html = NULL;

  // Render composition instance.
  $content = edge_suite_comp_render($definition_id);
  if($content != NULL){
    // Output full HTML structure.
    if(isset($content['scripts']) && is_array($content['scripts']) && !empty($content['stage'])){
      $scripts =  implode("\n", $content['scripts']);
      $stage = $content['stage'];
      $html =
<<<HTML
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    $scripts
  </head>
  <body>
      $stage
  </body>
</html>
HTML;
    }
  }
  return $html;
}