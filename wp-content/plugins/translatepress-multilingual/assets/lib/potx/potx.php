<?php
// $Id: potx.inc,v 1.1.2.17.2.7.2.19.2.16 2011/01/26 12:16:06 goba Exp $

/**
 * @file
 *   Extraction API used by the web and command line interface.
 *
 *   This include file implements the default string and file version
 *   storage as well as formatting of POT files for web download or
 *   file system level creation. The strings, versions and file contents
 *   are handled with global variables to reduce the possible memory overhead
 *   and API clutter of passing them around. Custom string and version saving
 *   functions can be implemented to use the functionality provided here as an
 *   API for Drupal code to translatable string conversion.
 *
 *   The potx-cli.php script can be used with this include file as
 *   a command line interface to string extraction. The potx.module
 *   can be used as a web interface for manual extraction.
 *
 *   For a module using potx as an extraction API, but providing more
 *   sophisticated functionality on top of it, look into the
 *   'Localization server' module: http://drupal.org/project/l10n_server
 */

/**
 * The current Drupal major API verion.
 *
 * This should be the only difference between different branches of potx.inc
 */
define('TRP_POTX_API_CURRENT', 7);

/**
 * Silence status reports.
 */
define('TRP_POTX_STATUS_SILENT', 0);

/**
 * Drupal message based status reports.
 */
define('TRP_POTX_STATUS_MESSAGE', 1);

/**
 * Command line status reporting.
 *
 * Status goes to standard output, errors to standard error.
 */
define('TRP_POTX_STATUS_CLI', 2);

/**
 * Structured array status logging.
 *
 * Useful for coder review status reporting.
 */
define('TRP_POTX_STATUS_STRUCTURED', 3);

/**
 * Core parsing mode:
 *  - .info files folded into general.pot
 *  - separate files generated for modules
 */
define('TRP_POTX_BUILD_CORE', 0);

/**
 * Multiple files mode:
 *  - .info files folded into their module pot files
 *  - separate files generated for modules
 */
define('TRP_POTX_BUILD_MULTIPLE', 1);

/**
 * Single file mode:
 *  - all files folded into one pot file
 */
define('TRP_POTX_BUILD_SINGLE', 2);

/**
 * Save string to both installer and runtime collection.
 */
define('TRP_POTX_STRING_BOTH', 0);

/**
 * Save string to installer collection only.
 */
define('TRP_POTX_STRING_INSTALLER', 1);

/**
 * Save string to runtime collection only.
 */
define('TRP_POTX_STRING_RUNTIME', 2);

/**
 * Parse source files in Drupal 5.x format.
 */
define('TRP_POTX_API_5', 5);

/**
 * Parse source files in Drupal 6.x format.
 *
 * Changes since 5.x documented at http://drupal.org/node/114774
 */
define('TRP_POTX_API_6', 6);

/**
 * Parse source files in Drupal 7.x format.
 *
 * Changes since 6.x documented at http://drupal.org/node/224333
 */
define('TRP_POTX_API_7', 7);

/**
 * When no context is used. Makes it easy to look these up.
 */
define('TRP_POTX_CONTEXT_NONE', NULL);

/**
 * When there was a context identification error.
 */
define('TRP_POTX_CONTEXT_ERROR', FALSE);

/**
 * Process a file and put extracted information to the given parameters.
 *
 * @param $file_path
 *   Comlete path to file to process.
 * @param $strip_prefix
 *   An integer denoting the number of chars to strip from filepath for output.
 * @param $save_callback
 *   Callback function to use to save the collected strings.
 * @param $version_callback
 *   Callback function to use to save collected version numbers.
 * @param $api_version
 *   Drupal API version to work with.
 */
function trp_potx_process_file( $file_path, $strip_prefix = 0, $save_callback = 'trp_potx_save_string', $version_callback = 'trp_potx_save_version', $api_version = TRP_POTX_API_CURRENT) {
  global $_potx_tokens, $_potx_lookup;

  // Figure out the basename and extension to select extraction method.
  $basename = basename($file_path);
  $name_parts = pathinfo($basename);

  // Always grab the CVS version number from the code
  $code = file_get_contents($file_path);
  $file_name = $strip_prefix > 0 ? substr($file_path, $strip_prefix) : $file_path;
  trp_potx_find_version_number($code, $file_name, $version_callback);

  // The .info files are not PHP code, no need to tokenize.
  if ($name_parts['extension'] == 'info') {
    trp_potx_find_info_file_strings($file_path, $file_name, $save_callback, $api_version);
    return;
  }
  elseif ($name_parts['extension'] == 'js' && $api_version > TRP_POTX_API_5) {
    // @todo: D7 context support.
    trp_potx_parse_js_file($code, $file_name, $save_callback);
  }

  // Extract raw PHP language tokens.
  $raw_tokens = token_get_all($code);
  unset($code);

  // Remove whitespace and possible HTML (the later in templates for example),
  // count line numbers so we can include them in the output.
  $_potx_tokens = array();
  $_potx_lookup = array();
  $token_number = 0;
  $line_number = 1;
  foreach ($raw_tokens as $token) {
    if ((!is_array($token)) || (($token[0] != T_WHITESPACE) && ($token[0] != T_INLINE_HTML))) {
      if (is_array($token)) {
        $token[] = $line_number;
         // Fill array for finding token offsets quickly.
          /** TranslatePress START modifications */
          $src_tokens = apply_filters('trp_scan_gettext_translate_functions', array(
              '__', 'esc_attr__', 'esc_html__', '_e', 'esc_attr_e', 'esc_html_e',
              '_x', 'esc_attr_x', 'esc_html_x', '_ex',
              '_n', '_nx'
          ) );
          if ( $token[ 0 ] == T_STRING || ( $token[ 0 ] == T_VARIABLE && in_array ( $token[ 1 ], $src_tokens ) ) ) {
          /** TranslatePress END modifications */
           if (!isset($_potx_lookup[$token[1]])) {
             $_potx_lookup[$token[1]] = array();
           }
           $_potx_lookup[$token[1]][] = $token_number;
         }
      }
      $_potx_tokens[] = $token;
      $token_number++;
    }
    // Collect line numbers.
    if (is_array($token)) {
      $line_number += count(explode("\n", $token[1])) - 1;
    }
    else {
      $line_number += count(explode("\n", $token)) - 1;
    }
  }
  unset($raw_tokens);
    /** TranslatePress START modifications */
    if ( !empty( $src_tokens ) ) {
        foreach ( $src_tokens as $tk ) {
            trp_potx_find_t_calls_with_context( $file_name, $save_callback, $tk );
        }
    }
    /** TranslatePress END modifications */
}

/**
 * Creates complete file strings with _potx_store()
 *
 * @param $string_mode
 *   Strings to generate files for: POTX_STRING_RUNTIME or POTX_STRING_INSTALLER.
 * @param $build_mode
 *   Storage mode used: single, multiple or core
 * @param $force_name
 *   Forces a given file name to get used, if single mode is on, without extension
 * @param $save_callback
 *   Callback used to save strings previously.
 * @param $version_callback
 *   Callback used to save versions previously.
 * @param $header_callback
 *   Callback to invoke to get the POT header.
 * @param $template_export_langcode
 *   Language code if the template should have language dependent content
 *   (like plural formulas and language name) included.
 * @param $translation_export_langcode
 *   Language code if translations should also be exported.
 * @param $api_version
 *   Drupal API version to work with.
 */
function trp_potx_build_files( $string_mode = TRP_POTX_STRING_RUNTIME, $build_mode = TRP_POTX_BUILD_SINGLE, $force_name = 'general', $save_callback = 'trp_potx_save_string', $version_callback = 'trp_potx_save_version', $header_callback = '_potx_get_header', $template_export_langcode = NULL, $translation_export_langcode = NULL, $api_version = TRP_POTX_API_CURRENT) {
  global $_potx_store;

  // Get strings and versions by reference.
  $strings  = $save_callback(NULL, NULL, NULL, 0, $string_mode);
  $versions = $version_callback();

  // We might not have any string recorded in this string mode.
  if (!is_array($strings)) {
    return;
  }

  foreach ($strings as $string => $string_info) {
    foreach ($string_info as $context => $file_info) {
      // Build a compact list of files this string occured in.
      $occured = $file_list = array();
      // Look for strings appearing in multiple directories (ie.
      // different subprojects). So we can include them in general.pot.
      $last_location = dirname(array_shift(array_keys($file_info)));
      $multiple_locations = FALSE;
      foreach ($file_info as $file => $lines) {
        $occured[] = "$file:". join(';', $lines);
        if (isset($versions[$file])) {
          $file_list[] = $versions[$file];
        }
        if (dirname($file) != $last_location) {
          $multiple_locations = TRUE;
        }
        $last_location = dirname($file);
      }

      // Mark duplicate strings (both translated in the app and in the installer).
      $comment = join(" ", $occured);
      if (strpos($comment, '(dup)') !== FALSE) {
        $comment = '(duplicate) '. str_replace('(dup)', '', $comment);
      }
      $output = "#: $comment\n";

      if ($build_mode == TRP_POTX_BUILD_SINGLE) {
        // File name forcing in single mode.
        $file_name = $force_name;
      }
      elseif (strpos($comment, '.info')) {
        // Store .info file strings either in general.pot or the module pot file,
        // depending on the mode used.
        $file_name = ($build_mode == TRP_POTX_BUILD_CORE ? 'general' : str_replace('.info', '.module', $file_name));
      }
      elseif ($multiple_locations) {
        // Else if occured more than once, store in general.pot.
        $file_name = 'general';
      }
      else {
        // Fold multiple files in the same folder into one.
        if (empty($last_location) || $last_location == '.') {
          $file_name = 'root';
        }
        else {
          $file_name = str_replace('/', '-', $last_location);
        }
      }


      if (strpos($string, "\0") !== FALSE) {
        // Plural strings have a null byte delimited format.
        list($singular, $plural) = explode("\0", $string);
        if (!empty($context)) {
          $output .= "msgctxt \"$context\"\n";
        }
        $output .= "msgid \"$singular\"\n";
        $output .= "msgid_plural \"$plural\"\n";
        if (isset($translation_export_langcode)) {
          $output .= trp_potx_translation_export($translation_export_langcode, $singular, $plural, $api_version);
        }
        else {
          $output .= "msgstr[0] \"\"\n";
          $output .= "msgstr[1] \"\"\n";
        }
      }
      else {
        // Simple strings.
        if (!empty($context)) {
          $output .= "msgctxt \"$context\"\n";
        }
        $output .= "msgid \"$string\"\n";
        if (isset($translation_export_langcode)) {
          $output .= trp_potx_translation_export($translation_export_langcode, $string, NULL, $api_version);
        }
        else {
          $output .= "msgstr \"\"\n";
        }
      }
      $output .= "\n";

      // Store the generated output in the given file storage.
      if (!isset($_potx_store[$file_name])) {
        $_potx_store[$file_name] = array(
          'header'  => $header_callback($file_name, $template_export_langcode, $api_version),
          'sources' => $file_list,
          'strings' => $output,
          'count'   => 1,
        );
      }
      else {
        // Maintain a list of unique file names.
        $_potx_store[$file_name]['sources']  = array_unique(array_merge($_potx_store[$file_name]['sources'], $file_list));
        $_potx_store[$file_name]['strings'] .= $output;
        $_potx_store[$file_name]['count']   += 1;
      }
    }
  }
}

/**
 * Export translations with a specific language.
 *
 * @param $translation_export_langcode
 *   Language code if translations should also be exported.
 * @param $string
 *   String or singular version if $plural was provided.
 * @param $plural
 *   Plural version of singular string.
 * @param $api_version
 *   Drupal API version to work with.
 */
function trp_potx_translation_export( $translation_export_langcode, $string, $plural = NULL, $api_version = TRP_POTX_API_CURRENT) {
  include_once 'includes/locale.inc';

  // Stip out slash escapes.
  $string = stripcslashes($string);

  // Column and table name changed between versions.
  $language_column = $api_version > TRP_POTX_API_5 ? 'language' : 'locale';
  $language_table  = $api_version > TRP_POTX_API_5 ? 'languages' : 'locales_meta';

  if (!isset($plural)) {
    // Single string to look translation up for.
    if ($translation = db_query("SELECT t.translation FROM {locales_source} s LEFT JOIN {locales_target} t ON t.lid = s.lid WHERE s.source = :source AND t.{$language_column} = :langcode", array(':source' => $string, ':langcode' => $translation_export_langcode))->fetchField()) {
      return 'msgstr '. _locale_export_string($translation);
    }
    return "msgstr \"\"\n";
  }

  else {
    // String with plural variants. Fill up source string array first.
    $plural = stripcslashes($plural);
    $strings = array();
    $number_of_plurals = db_query('SELECT plurals FROM {'. $language_table ."} WHERE {$language_column} = :langcode", array(':langcode' => $translation_export_langcode))->fetchField();
    $plural_index = 0;
    while ($plural_index < $number_of_plurals) {
      if ($plural_index == 0) {
        // Add the singular version.
        $strings[] = $string;
      }
      elseif ($plural_index == 1) {
        // Only add plural version if required.
        $strings[] = $plural;
      }
      else {
        // More plural versions only if required, with the lookup source
        // string modified as imported into the database.
        $strings[] = str_replace('@count', '@count['. $plural_index .']', $plural);
      }
      $plural_index++;
    }

    $output = '';
    if (count($strings)) {
      // Source string array was done, so export translations.
      foreach ($strings as $index => $string) {
        if ($translation = db_query("SELECT t.translation FROM {locales_source} s LEFT JOIN {locales_target} t ON t.lid = s.lid WHERE s.source = :source AND t.{$language_column} = :langcode", array(':source' => $string, ':langcode' => $translation_export_langcode))->fetchField()) {
          $output .= 'msgstr['. $index .'] '. _locale_export_string(_locale_export_remove_plural($translation));
        }
        else {
          $output .= "msgstr[". $index ."] \"\"\n";
        }
      }
    }
    else {
      // No plural information was recorded, so export empty placeholders.
      $output .= "msgstr[0] \"\"\n";
      $output .= "msgstr[1] \"\"\n";
    }
    return $output;
  }
}

/**
 * Returns a header generated for a given file
 *
 * @param $file
 *   Name of POT file to generate header for
 * @param $template_export_langcode
 *   Language code if the template should have language dependent content
 *   (like plural formulas and language name) included.
 * @param $api_version
 *   Drupal API version to work with.
 */
function trp_potx_get_header( $file, $template_export_langcode = NULL, $api_version = TRP_POTX_API_CURRENT) {
  // We only have language to use if we should export with that langcode.
  $language = NULL;
  if (isset($template_export_langcode)) {
    $language = db_query($api_version > TRP_POTX_API_5 ? "SELECT language, name, plurals, formula FROM {languages} WHERE language = :langcode" : "SELECT locale, name, plurals, formula FROM {locales_meta} WHERE locale = :langcode", array( ':langcode' => $template_export_langcode))->fetchObject();
  }

  $output  = '# $'.'Id'.'$'."\n";
  $output .= "#\n";
  $output .= '# '. (isset($language) ? $language->name : 'LANGUAGE') .' translation of Drupal ('. $file .")\n";
  $output .= "# Copyright YEAR NAME <EMAIL@ADDRESS>\n";
  $output .= "# --VERSIONS--\n";
  $output .= "#\n";
  $output .= "#, fuzzy\n";
  $output .= "msgid \"\"\n";
  $output .= "msgstr \"\"\n";
  $output .= "\"Project-Id-Version: PROJECT VERSION\\n\"\n";
  $output .= '"POT-Creation-Date: '. date("Y-m-d H:iO") ."\\n\"\n";
  $output .= '"PO-Revision-Date: '. (isset($language) ? date("Y-m-d H:iO") : 'YYYY-mm-DD HH:MM+ZZZZ') ."\\n\"\n";
  $output .= "\"Last-Translator: NAME <EMAIL@ADDRESS>\\n\"\n";
  $output .= "\"Language-Team: ". (isset($language) ? $language->name : 'LANGUAGE') ." <EMAIL@ADDRESS>\\n\"\n";
  $output .= "\"MIME-Version: 1.0\\n\"\n";
  $output .= "\"Content-Type: text/plain; charset=utf-8\\n\"\n";
  $output .= "\"Content-Transfer-Encoding: 8bit\\n\"\n";
  if (isset($language->formula) && isset($language->plurals)) {
    $output .= "\"Plural-Forms: nplurals=". $language->plurals ."; plural=". strtr($language->formula, array('$' => '')) .";\\n\"\n\n";
  }
  else {
    $output .= "\"Plural-Forms: nplurals=INTEGER; plural=EXPRESSION;\\n\"\n\n";
  }
  return $output;
}

/**
 * Write out generated files to the current folder.
 *
 * @param $http_filename
 *   File name for content-disposition header in case of usage
 *   over HTTP. If not given, files are written to the local filesystem.
 * @param $content_disposition
 *   See RFC2183. 'inline' or 'attachment', with a default of
 *   'inline'. Only used if $http_filename is set.
 * @todo
 *   Look into whether multiple files can be output via HTTP.
 */
function trp_potx_write_files( $http_filename = NULL, $content_disposition = 'inline') {
  global $_potx_store;

  // Generate file lists and output files.
  if (is_array($_potx_store)) {
    foreach ($_potx_store as $file => $contents) {
      // Build replacement for file listing.
      if (count($contents['sources']) > 1) {
        $filelist = "Generated from files:\n#  " . join("\n#  ", $contents['sources']);
      }
      elseif (count($contents['sources']) == 1) {
        $filelist = "Generated from file: " . join('', $contents['sources']);
      }
      else {
        $filelist = 'No version information was available in the source files.';
      }
      $output = str_replace('--VERSIONS--', $filelist, $contents['header'] . $contents['strings']);

      if ($http_filename) {
        // HTTP output.
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Transfer-Encoding: 8bit');
        header("Content-Disposition: $content_disposition; filename=$http_filename");
        print $output;  /*phpcs:ignore */ /* Writing files is never used */
        return;
      }
      else {
        // Local file output, flatten directory structure.
        $file = str_replace('.', '-', preg_replace('![/]?([a-zA-Z_0-9]*/)*!', '', $file)) .'.pot';
        $fp = fopen($file, 'w');
        fwrite($fp, $output);
        fclose($fp);
      }
    }
  }
}

/**
 * Escape quotes in a strings depending on the surrounding
 * quote type used.
 *
 * @param $str
 *   The strings to escape
 */
function trp_potx_format_quoted_string( $str) {
  $quo = substr($str, 0, 1);
  $str = substr($str, 1, -1);
  if ($quo == '"') {
    $str = stripcslashes($str);
  }
  else {
    $str = strtr($str, array("\\'" => "'", "\\\\" => "\\"));
  }
  return addcslashes($str, "\0..\37\\\"");
}

/**
 * Output a marker error with an extract of where the error was found.
 *
 * @param $file
 *   Name of file
 * @param $line
 *   Line number of error
 * @param $marker
 *   Function name with which the error was identified
 * @param $ti
 *   Index on the token array
 * @param $error
 *   Helpful error message for users.
 * @param $docs_url
 *   Documentation reference.
 */
function trp_potx_marker_error( $file, $line, $marker, $ti, $error, $docs_url = NULL) {
  global $_potx_tokens;

  $tokens = '';
  $ti += 2;
  $tc = count($_potx_tokens);
  $par = 1;
  while ((($tc - $ti) > 0) && $par) {
    if (is_array($_potx_tokens[$ti])) {
      $tokens .= $_potx_tokens[$ti][1];
    }
    else {
      $tokens .= $_potx_tokens[$ti];
      if ($_potx_tokens[$ti] == "(") {
        $par++;
      }
      else if ($_potx_tokens[$ti] == ")") {
        $par--;
      }
    }
    $ti++;
  }
  trp_potx_status('error', $error, $file, $line, $marker .'('. $tokens, $docs_url);
}

/**
 * Status notification function.
 *
 * @param $op
 *   Operation to perform or type of message text.
 *     - set:    sets the reporting mode to $value
 *               use one of the POTX_STATUS_* constants as $value
 *     - get:    returns the list of error messages recorded
 *               if $value is true, it also clears the internal message cache
 *     - error:  sends an error message in $value with optional $file and $line
 *     - status: sends a status message in $value
 * @param $value
 *   Value depending on $op.
 * @param $file
 *   Name of file the error message is related to.
 * @param $line
 *   Number of line the error message is related to.
 * @param $excerpt
 *   Excerpt of the code in question, if available.
 * @param $docs_url
 *   URL to the guidelines to follow to fix the problem.
 */
function trp_potx_status( $op, $value = NULL, $file = NULL, $line = NULL, $excerpt = NULL, $docs_url = NULL) {
  static $mode = TRP_POTX_STATUS_CLI;
  static $messages = array();

  switch ($op) {
    case 'set':
      // Setting the reporting mode.
      $mode = $value;
      return;

    case 'get':
      // Getting the errors. Optionally deleting the messages.
      $errors = $messages;
      if (!empty($value)) {
        $messages = array();
      }
      return $errors;

    case 'error':
    case 'status':

      // Location information is required in 3 of the four possible reporting
      // modes as part of the error message. The structured mode needs the
      // file, line and excerpt info separately, not in the text.
      $location_info = '';
      if (($mode != TRP_POTX_STATUS_STRUCTURED) && isset($file)) {
        if (isset($line)) {
          if (isset($excerpt)) {
            $location_info = trp_t('At %excerpt in %file on line %line.', array( '%excerpt' => $excerpt, '%file' => $file, '%line' => $line));
          }
          else {
            $location_info = trp_t('In %file on line %line.', array( '%file' => $file, '%line' => $line));
          }
        }
        else {
          if (isset($excerpt)) {
            $location_info = trp_t('At %excerpt in %file.', array( '%excerpt' => $excerpt, '%file' => $file));
          }
          else {
            $location_info = trp_t('In %file.', array( '%file' => $file));
          }
        }
      }

      // Documentation helpers are provided as readable text in most modes.
      $read_more = '';
      if (($mode != TRP_POTX_STATUS_STRUCTURED) && isset($docs_url)) {
        $read_more = ($mode == TRP_POTX_STATUS_CLI) ? trp_t('Read more at @url', array( '@url' => $docs_url)) : trp_t('Read more at <a href="@url">@url</a>', array( '@url' => $docs_url));
      }

      // Error message or progress text to display.
      switch ($mode) {
        case TRP_POTX_STATUS_MESSAGE:
            /** TranslatePress START modifications */
//          drupal_set_message(join(' ', array($value, $location_info, $read_more)), $op);
            /** TranslatePress END modifications */
          break;
        case TRP_POTX_STATUS_CLI:
            /** TranslatePress START modifications */
          if ( defined ( 'STDERR' ) && defined ( 'STDOUT' ) ) {
              fwrite( $op == 'error' ? STDERR : STDOUT, join( "\n", array( $value, $location_info, $read_more ) ) . "\n\n" );
          }
            /** TranslatePress END modifications */
          break;
        case TRP_POTX_STATUS_SILENT:
          if ($op == 'error') {
            $messages[] = join(' ', array($value, $location_info, $read_more));
          }
          break;
        case TRP_POTX_STATUS_STRUCTURED:
          if ($op == 'error') {
            $messages[] = array($value, $file, $line, $excerpt, $docs_url);
          }
          break;
      }
      return;
  }
}

/**
 * Detect all occurances of t()-like calls.
 *
 * These sequences are searched for:
 *   T_STRING("$function_name") + "(" + T_CONSTANT_ENCAPSED_STRING + ")"
 *   T_STRING("$function_name") + "(" + T_CONSTANT_ENCAPSED_STRING + ","
 *
 * @param $file
 *   Name of file parsed.
 * @param $save_callback
 *   Callback function used to save strings.
 * @param function_name
 *   The name of the function to look for (could be 't', '$t', 'st'
 *   or any other t-like function).
 * @param $string_mode
 *   String mode to use: POTX_STRING_INSTALLER, POTX_STRING_RUNTIME or
 *   POTX_STRING_BOTH.
 */
function trp_potx_find_t_calls( $file, $save_callback, $function_name = 't', $string_mode = TRP_POTX_STRING_RUNTIME) {
  global $_potx_tokens, $_potx_lookup;

  // Lookup tokens by function name.
  if (isset($_potx_lookup[$function_name])) {
    foreach ($_potx_lookup[$function_name] as $ti) {
      list($ctok, $par, $mid, $rig) = array($_potx_tokens[$ti], $_potx_tokens[$ti+1], $_potx_tokens[$ti+2], $_potx_tokens[$ti+3]);
      list($type, $string, $line) = $ctok;
      if ($par == "(") {
        if (in_array($rig, array(")", ","))
          && (is_array($mid) && ($mid[0] == T_CONSTANT_ENCAPSED_STRING))) {
            // This function is only used for context-less call types.
            $save_callback(trp_potx_format_quoted_string( $mid[1]), TRP_POTX_CONTEXT_NONE, $file, $line, $string_mode);
        }
        else {
          // $function_name() found, but inside is something which is not a string literal.
          trp_potx_marker_error($file, $line, $function_name, $ti, trp_t('The first parameter to @function() should be a literal string. There should be no variables, concatenation, constants or other non-literal strings there.', array( '@function' => $function_name)), 'http://drupal.org/node/322732');
        }
      }
    }
  }
}

/**
 * Detect all occurances of t()-like calls from Drupal 7 (with context).
 *
 * These sequences are searched for:
 *   T_STRING("$function_name") + "(" + T_CONSTANT_ENCAPSED_STRING + ")"
 *   T_STRING("$function_name") + "(" + T_CONSTANT_ENCAPSED_STRING + ","
 *   and then an optional value for the replacements and an optional array
 *   for the options with an optional context key.
 *
 * @param $file
 *   Name of file parsed.
 * @param $save_callback
 *   Callback function used to save strings.
 * @param $function_name
 *   The name of the function to look for (could be 't', '$t', 'st'
 *   or any other t-like function). Drupal 7 only supports context on t(), st()
 *   and $t().
 * @param $string_mode
 *   String mode to use: POTX_STRING_INSTALLER, POTX_STRING_RUNTIME or
 *   POTX_STRING_BOTH.
 */
function trp_potx_find_t_calls_with_context( $file, $save_callback, $function_name = '__', $string_mode = TRP_POTX_STRING_RUNTIME) {
  global $_potx_tokens, $_potx_lookup;

  // Lookup tokens by function name.
  if (isset($_potx_lookup[$function_name])) {
    foreach ($_potx_lookup[$function_name] as $ti) {
      list($ctok, $par, $mid, $rig) = array($_potx_tokens[$ti], $_potx_tokens[$ti+1], $_potx_tokens[$ti+2], $_potx_tokens[$ti+3]);
      list($type, $string, $line) = $ctok;
      if ($par == "(") {
        if (in_array($rig, array(")", ","))
          && (is_array($mid) && ($mid[0] == T_CONSTANT_ENCAPSED_STRING))) {
          // By default, there is no context.
            /** TranslatePress START modifications */
            $domain = TRP_POTX_CONTEXT_NONE;
            $context = false;
			$text_plural = false;
          if ($rig == ',') {
            // If there was a comma after the string, we need to look forward
            // to try and find the context.
              if ( in_array( $function_name, array( '_x','_ex','esc_html_x', 'esc_attr_x', 'esc_html_x') ) ) {
                  $domain_offset = 6;
                  $context_offset = 4;
                  $text = $mid[ 1 ];
              } elseif ( $function_name == '_n' ) {
                  $domain_offset = 8;
                  //in case of a function as the number argument, we need to set a different offset
                  while ( !isset( $_potx_tokens[ $ti + $domain_offset ][ 1 ] ) ||
                      ( isset( $_potx_tokens[ $ti + $domain_offset ][ 1 ] ) && !preg_match ( '#^(\'|")(.+)#', $_potx_tokens[ $ti + $domain_offset ][ 1 ] ) ) ){
                      $domain_offset ++;
                      if ( $domain_offset == 17 ){
                          break;
                      }
                  };
                  $context_offset = false;
                  $text_plural = $_potx_tokens[ $ti + 4 ][ 1 ];
              } elseif ( $function_name == '_nx' ) {
                  $context_offset = 8;
                  while ( !isset( $_potx_tokens[ $ti + $context_offset ][ 1 ] ) ||
                      ( isset( $_potx_tokens[ $ti + $context_offset ][ 1 ] ) && !preg_match ( '#^(\'|")(.+)#', $_potx_tokens[ $ti + $context_offset ][ 1 ] ) ) ){
                      $context_offset ++;
                      if ( $context_offset == 17 ){
                          break;
                      }
                  };
                  $domain_offset = $context_offset + 2;
                  $text_plural = $_potx_tokens[ $ti + 4 ][ 1 ];
              } else {
                  $domain_offset = 4;
                  $context_offset = false;
                  $text = $mid[ 1 ];
              }

              if ( !isset( $_potx_tokens[ $ti + $domain_offset ][ 1 ] ) )
                  return false;

              if ( !preg_match ( '#^(\'|")(.+)#', $_potx_tokens[ $ti + $domain_offset ][ 1 ] ) ) {
                 return false;
              } else {
                  $domain = trim ( $_potx_tokens[ $ti + $domain_offset ][ 1 ], "\"' " );
              }

              // exception for gettext calls with contexts
              if ( false !== $context_offset ) {
                  if ( !preg_match ( '#^(\'|")(.+)#', $_potx_tokens[ $ti + $context_offset ][ 1 ] ) ) {
                      return false;
                  } else {
                      $context = trim ( $_potx_tokens[ $ti + $context_offset ][ 1 ], "\"' " );
                  }

              } else {
                  $context = false;
              }
          }
          if ($domain !== TRP_POTX_CONTEXT_ERROR) {
            // Only save if there was no error in context parsing.
            $save_callback(trp_potx_format_quoted_string( $mid[1]), $domain, @strval ( $context ), $file, $line, $string_mode, trp_potx_format_quoted_string ( $text_plural ));
          }
          /** TranslatePress END modifications */
        }
        else {
          // $function_name() found, but inside is something which is not a string literal.
          trp_potx_marker_error($file, $line, $function_name, $ti, trp_t('The first parameter to @function() should be a literal string. There should be no variables, concatenation, constants or other non-literal strings there.', array( '@function' => $function_name)), 'http://drupal.org/node/322732');
        }
      }
    }
  }
}

/**
 * Detect all occurances of watchdog() calls. Only from Drupal 6.
 *
 * These sequences are searched for:
 *   watchdog + "(" + T_CONSTANT_ENCAPSED_STRING + "," +
 *   T_CONSTANT_ENCAPSED_STRING + something
 *
 * @param $file
 *   Name of file parsed.
 * @param $save_callback
 *   Callback function used to save strings.
 */
function trp_potx_find_watchdog_calls( $file, $save_callback) {
  global $_potx_tokens, $_potx_lookup;

  // Lookup tokens by function name.
  if (isset($_potx_lookup['watchdog'])) {
    foreach ($_potx_lookup['watchdog'] as $ti) {
      list($ctok, $par, $mtype, $comma, $message, $rig) = array($_potx_tokens[$ti], $_potx_tokens[$ti+1], $_potx_tokens[$ti+2], $_potx_tokens[$ti+3], $_potx_tokens[$ti+4], $_potx_tokens[$ti+5]);
      list($type, $string, $line) = $ctok;
      if ($par == '(') {
        // Both type and message should be a string literal.
        if (in_array($rig, array(')', ',')) && $comma == ','
          && (is_array($mtype) && ($mtype[0] == T_CONSTANT_ENCAPSED_STRING))
          && (is_array($message) && ($message[0] == T_CONSTANT_ENCAPSED_STRING))) {
            // Context is not supported on watchdog().
            $save_callback(trp_potx_format_quoted_string( $mtype[1]), TRP_POTX_CONTEXT_NONE, $file, $line);
            $save_callback(trp_potx_format_quoted_string( $message[1]), TRP_POTX_CONTEXT_NONE, $file, $line);
        }
        else {
          // watchdog() found, but inside is something which is not a string literal.
          trp_potx_marker_error($file, $line, 'watchdog', $ti, trp_t('The first two watchdog() parameters should be literal strings. There should be no variables, concatenation, constants or even a t() call there.'), 'http://drupal.org/node/323101');
        }
      }
    }
  }
}

/**
 * Detect all occurances of format_plural calls.
 *
 * These sequences are searched for:
 *   T_STRING("format_plural") + "(" + ..anything (might be more tokens).. +
 *   "," + T_CONSTANT_ENCAPSED_STRING +
 *   "," + T_CONSTANT_ENCAPSED_STRING + parenthesis (or comma allowed from
 *   Drupal 6)
 *
 * @param $file
 *   Name of file parsed.
 * @param $save_callback
 *   Callback function used to save strings.
 * @param $api_version
 *   Drupal API version to work with.
 */
function trp_potx_find_format_plural_calls( $file, $save_callback, $api_version = TRP_POTX_API_CURRENT) {
  global $_potx_tokens, $_potx_lookup;

  if (isset($_potx_lookup['format_plural'])) {
    foreach ($_potx_lookup['format_plural'] as $ti) {
      list($ctok, $par1) = array($_potx_tokens[$ti], $_potx_tokens[$ti+1]);
      list($type, $string, $line) = $ctok;
      if ($par1 == "(") {
        // Eat up everything that is used as the first parameter
        $tn = $ti + 2;
        $depth = 0;
        while (!($_potx_tokens[$tn] == "," && $depth == 0)) {
          if ($_potx_tokens[$tn] == "(") {
            $depth++;
          }
          elseif ($_potx_tokens[$tn] == ")") {
            $depth--;
          }
          $tn++;
        }
        // Get further parameters
        list($comma1, $singular, $comma2, $plural, $par2) = array($_potx_tokens[$tn], $_potx_tokens[$tn+1], $_potx_tokens[$tn+2], $_potx_tokens[$tn+3], $_potx_tokens[$tn+4]);
        if (($comma2 == ',') && ($par2 == ')' || ($par2 == ',' && $api_version > TRP_POTX_API_5)) &&
          (is_array($singular) && ($singular[0] == T_CONSTANT_ENCAPSED_STRING)) &&
          (is_array($plural) && ($plural[0] == T_CONSTANT_ENCAPSED_STRING))) {
          // By default, there is no context.
          $context = TRP_POTX_CONTEXT_NONE;
          if ($par2 == ',' && $api_version > TRP_POTX_API_6) {
            // If there was a comma after the plural, we need to look forward
            // to try and find the context.
            $context = trp_potx_find_context($ti, $tn + 5, $file, 'format_plural');
          }
          if ($context !== TRP_POTX_CONTEXT_ERROR) {
            // Only save if there was no error in context parsing.
            $save_callback(
              trp_potx_format_quoted_string( $singular[1]) ."\0". trp_potx_format_quoted_string( $plural[1]),
              $context,
              $file,
              $line
            );
          }
        }
        else {
          // format_plural() found, but the parameters are not correct.
          trp_potx_marker_error($file, $line, "format_plural", $ti, trp_t('In format_plural(), the singular and plural strings should be literal strings. There should be no variables, concatenation, constants or even a t() call there.'), 'http://drupal.org/node/323072');
        }
      }
    }
  }
}

/**
 * Detect permission names from the hook_perm() implementations.
 * Note that this will get confused with a similar pattern in a comment,
 * and with dynamic permissions, which need to be accounted for.
 *
 * @param $file
 *   Full path name of file parsed.
 * @param $filebase
 *   Filenaname of file parsed.
 * @param $save_callback
 *   Callback function used to save strings.
 */
function trp_potx_find_perm_hook( $file, $filebase, $save_callback) {
  global $_potx_tokens, $_potx_lookup;

  if (isset($_potx_lookup[$filebase .'_perm'])) {
    // Special case for node module, because it uses dynamic permissions.
    // Include the static permissions by hand. That's about all we can do here.
    if ($filebase == 'node') {
      $line = $_potx_tokens[$_potx_lookup['node_perm'][0]][2];
      // List from node.module 1.763 (checked in on 2006/12/29 at 21:25:36 by drumm)
      $nodeperms = array('administer content types', 'administer nodes', 'access content', 'view revisions', 'revert revisions');
      foreach ($nodeperms as $item) {
        // hook_perm() is only ever found on a Drupal system which does not
        // support context.
        $save_callback($item, TRP_POTX_CONTEXT_NONE, $file, $line);
      }
    }
    else {
      $count = 0;
      foreach ($_potx_lookup[$filebase .'_perm'] as $ti) {
        $tn = $ti;
        while (is_array($_potx_tokens[$tn]) || $_potx_tokens[$tn] != '}') {
          if (is_array($_potx_tokens[$tn]) && $_potx_tokens[$tn][0] == T_CONSTANT_ENCAPSED_STRING) {
            // hook_perm() is only ever found on a Drupal system which does not
            // support context.
            $save_callback(trp_potx_format_quoted_string( $_potx_tokens[ $tn][1]), TRP_POTX_CONTEXT_NONE, $file, $_potx_tokens[ $tn][2]);
            $count++;
          }
          $tn++;
        }
      }
      if (!$count) {
        trp_potx_status('error', trp_t('%hook should have an array of literal string permission names.', array( '%hook' => $filebase .'_perm()')), $file, NULL, NULL, 'http://drupal.org/node/323101');
      }
    }
  }
}

/**
 * Helper function to look up the token closing the current function.
 *
 * @param $here
 *   The token at the function name
 */
function trp_potx_find_end_of_function( $here) {
  global $_potx_tokens;

  // Seek to open brace.
  while (is_array($_potx_tokens[$here]) || $_potx_tokens[$here] != '{') {
    $here++;
  }
  $nesting = 1;
  while ($nesting > 0) {
    $here++;
    if (!is_array($_potx_tokens[$here])) {
      if ($_potx_tokens[$here] == '}') {
        $nesting--;
      }
      if ($_potx_tokens[$here] == '{') {
        $nesting++;
      }
    }
  }
  return $here;
}

/**
 * Helper to move past t() and format_plural() arguments in search of context.
 *
 * @param $here
 *   The token before the start of the arguments
 */
function trp_potx_skip_args( $here) {
  global $_potx_tokens;

  $nesting = 0;
  // Go through to either the end of the function call or to a comma
  // after the current position on the same nesting level.
  while (!(($_potx_tokens[$here] == ',' && $nesting == 0) ||
           ($_potx_tokens[$here] == ')' && $nesting == -1))) {
    $here++;
    if (!is_array($_potx_tokens[$here])) {
      if ($_potx_tokens[$here] == ')') {
        $nesting--;
      }
      if ($_potx_tokens[$here] == '(') {
        $nesting++;
      }
    }
  }
  // If we run out of nesting, it means we reached the end of the function call,
  // so we skipped the arguments but did not find meat for looking at the
  // specified context.
  return ($nesting == 0 ? $here : FALSE);
}

/**
 * Helper to find the value for 'context' on t() and format_plural().
 *
 * @param $tf
 *   Start position of the original function.
 * @param $ti
 *   Start position where we should search from.
 * @param $file
 *   Full path name of file parsed.
 * @param function_name
 *   The name of the function to look for. Either 'format_plural' or 't'
 *   given that Drupal 7 only supports context on these.
 */
function trp_potx_find_context( $tf, $ti, $file, $function_name) {
  global $_potx_tokens;

  // Start from after the comma and skip the possible arguments for the function
  // so we can look for the context.
  if (($ti = trp_potx_skip_args($ti)) && ( $_potx_tokens[ $ti] == ',')) {
    // Now we actually might have some definition for a context. The $options
    // argument is coming up, which might have a key for context.
    list($com, $arr, $par) = array($_potx_tokens[$ti], $_potx_tokens[$ti+1], $_potx_tokens[$ti+2]);
    if ($com == ',' && $arr[1] == 'array' && $par == '(') {
      $nesting = 0;
      $ti += 3;
      // Go through to either the end of the array or to the key definition of
      // context on the same nesting level.
      while (!((is_array($_potx_tokens[$ti]) && (in_array($_potx_tokens[$ti][1], array('"context"', "'context'"))) && ($_potx_tokens[$ti][0] == T_CONSTANT_ENCAPSED_STRING) && ($nesting == 0)) ||
               ($_potx_tokens[$ti] == ')' && $nesting == -1))) {
        $ti++;
        if (!is_array($_potx_tokens[$ti])) {
          if ($_potx_tokens[$ti] == ')') {
            $nesting--;
          }
          if ($_potx_tokens[$ti] == '(') {
            $nesting++;
          }
        }
      }
      if ($nesting == 0) {
        // Found the 'context' key on the top level of the $options array.
        list($arw, $str) = array($_potx_tokens[$ti+1], $_potx_tokens[$ti+2]);
        if (is_array($arw) && $arw[1] == '=>' && is_array($str) && $str[0] == T_CONSTANT_ENCAPSED_STRING) {
          return trp_potx_format_quoted_string( $str[1]);
        }
        else {
          list($type, $string, $line) = $_potx_tokens[$ti];
          // @todo: fix error reference.
          trp_potx_marker_error($file, $line, $function_name, $tf, trp_t('The context element in the options array argument to @function() should be a literal string. There should be no variables, concatenation, constants or other non-literal strings there.', array( '@function' => $function_name)), 'http://drupal.org/node/322732');
          // Return with error.
          return TRP_POTX_CONTEXT_ERROR;
        }
      }
      else {
        // Did not found 'context' key in $options array.
        return TRP_POTX_CONTEXT_NONE;
      }
    }
  }

  // After skipping args, we did not find a comma to look for $options.
  return TRP_POTX_CONTEXT_NONE;
}

/**
 * List of menu item titles. Only from Drupal 6.
 *
 * @param $file
 *   Full path name of file parsed.
 * @param $filebase
 *   Filenaname of file parsed.
 * @param $save_callback
 *   Callback function used to save strings.
 */
function trp_potx_find_menu_hooks( $file, $filebase, $save_callback) {
  global $_potx_tokens, $_potx_lookup;

  $hooks = array('_menu', '_menu_alter');
  $keys = array("'title'", '"title"', "'description'", '"description"');

  foreach ($hooks as $hook) {
    if (isset($_potx_lookup[$filebase . $hook]) && is_array($_potx_lookup[$filebase . $hook])) {
      // We have this menu hook in this file.
      foreach ($_potx_lookup[$filebase . $hook] as $ti) {
        $end = trp_potx_find_end_of_function($ti);
        $tn = $ti;
        while ($tn < $end) {

          // Support for array syntax more commonly used in menu hooks:
          // $items = array('node/add' => array('title' => 'Add content'));
          if ($_potx_tokens[$tn][0] == T_CONSTANT_ENCAPSED_STRING && in_array($_potx_tokens[$tn][1], $keys) && $_potx_tokens[$tn+1][0] == T_DOUBLE_ARROW) {
            if ($_potx_tokens[$tn+2][0] == T_CONSTANT_ENCAPSED_STRING) {
              // We cannot export menu item context.
              $save_callback(
                trp_potx_format_quoted_string( $_potx_tokens[ $tn+2][1]),
                TRP_POTX_CONTEXT_NONE,
                $file,
                $_potx_tokens[$tn+2][2]
              );
              $tn+=2; // Jump forward by 2.
            }
            else {
              trp_potx_status('error', trp_t('Invalid menu %element definition found in %hook. Title and description keys of the menu array should be literal strings.', array( '%element' => $_potx_tokens[ $tn][1], '%hook' => $filebase . $hook .'()')), $file, $_potx_tokens[ $tn][2], NULL, 'http://drupal.org/node/323101');
            }
          }

          // Support for array syntax more commonly used in menu alters:
          // $items['node/add']['title'] = 'Add content here';
          if (is_string($_potx_tokens[$tn]) && $_potx_tokens[$tn] == '[' && $_potx_tokens[$tn+1][0] == T_CONSTANT_ENCAPSED_STRING && in_array($_potx_tokens[$tn+1][1], $keys) && is_string($_potx_tokens[$tn+2]) && $_potx_tokens[$tn+2] == ']') {
            if (is_string($_potx_tokens[$tn+3]) && $_potx_tokens[$tn+3] == '=' && $_potx_tokens[$tn+4][0] == T_CONSTANT_ENCAPSED_STRING) {
              // We cannot export menu item context.
              $save_callback(
                trp_potx_format_quoted_string( $_potx_tokens[ $tn+4][1]),
                TRP_POTX_CONTEXT_NONE,
                $file,
                $_potx_tokens[$tn+4][2]
              );
              $tn+=4; // Jump forward by 4.
            }
            else {
              trp_potx_status('error', trp_t('Invalid menu %element definition found in %hook. Title and description keys of the menu array should be literal strings.', array( '%element' => $_potx_tokens[ $tn+1][1], '%hook' => $filebase . $hook .'()')), $file, $_potx_tokens[ $tn+1][2], NULL, 'http://drupal.org/node/323101');
            }
          }
          $tn++;
        }
      }
    }
  }
}

/**
 * Get languages names from Drupal's locale.inc.
 *
 * @param $file
 *   Full path name of file parsed
 * @param $save_callback
 *   Callback function used to save strings.
 * @param $api_version
 *   Drupal API version to work with.
 */
function trp_potx_find_language_names( $file, $save_callback, $api_version = TRP_POTX_API_CURRENT) {
  global $_potx_tokens, $_potx_lookup;

  foreach ($_potx_lookup[$api_version > TRP_POTX_API_5 ? '_locale_get_predefined_list' : '_locale_get_iso639_list'] as $ti) {
    // Search for the definition of _locale_get_predefined_list(), not where it is called.
    if ($_potx_tokens[$ti-1][0] == T_FUNCTION) {
      break;
    }
  }

  $end = trp_potx_find_end_of_function($ti);
  $ti += 7; // function name, (, ), {, return, array, (
  while ($ti < $end) {
    while ($_potx_tokens[$ti][0] != T_ARRAY) {
      if (!is_array($_potx_tokens[$ti]) && $_potx_tokens[$ti] == ';') {
        // We passed the end of the list, break out to function level
        // to prevent an infinite loop.
        break 2;
      }
      $ti++;
    }
    $ti += 2; // array, (
    // Language names are context-less.
    $save_callback(trp_potx_format_quoted_string( $_potx_tokens[ $ti][1]), TRP_POTX_CONTEXT_NONE, $file, $_potx_tokens[ $ti][2]);
  }
}

/**
 * Get the exact CVS version number from the file, so we can
 * push that into the generated output.
 *
 * @param $code
 *   Complete source code of the file parsed.
 * @param $file
 *   Name of the file parsed.
 * @param $version_callback
 *   Callback used to save the version information.
 */
function trp_potx_find_version_number( $code, $file, $version_callback) {
  // Prevent CVS from replacing this pattern with actual info.
  if (preg_match('!\\$I'.'d: ([^\\$]+) Exp \\$!', $code, $version_info)) {
    $version_callback($version_info[1], $file);
  }
  else {
    // Unknown version information.
    $version_callback($file .': n/a', $file);
  }
}

/**
 * Add date strings, which cannot be extracted otherwise.
 * This is called for locale.module.
 *
 * @param $file
 *   Name of the file parsed.
 * @param $save_callback
 *   Callback function used to save strings.
 * @param $api_version
 *   Drupal API version to work with.
 */
function trp_potx_add_date_strings( $file, $save_callback, $api_version = TRP_POTX_API_CURRENT) {
  for ($i = 1; $i <= 12; $i++) {
    $stamp = mktime(0, 0, 0, $i, 1, 1971);
    if ($api_version > TRP_POTX_API_6) {
      // From Drupal 7, long month names are saved with this context.
      $save_callback(date("F", $stamp), 'Long month name', $file);
    }
    elseif ($api_version > TRP_POTX_API_5) {
      // Drupal 6 uses a little hack. No context.
      $save_callback('!long-month-name '. date("F", $stamp), TRP_POTX_CONTEXT_NONE, $file);
    }
    else {
      // Older versions just accept the confusion, no context.
      $save_callback(date("F", $stamp), TRP_POTX_CONTEXT_NONE, $file);
    }
    // Short month names lack a context anyway.
    $save_callback(date("M", $stamp), TRP_POTX_CONTEXT_NONE, $file);
  }
  for ($i = 0; $i <= 7; $i++) {
    $stamp = $i * 86400;
    $save_callback(date("D", $stamp), TRP_POTX_CONTEXT_NONE, $file);
    $save_callback(date("l", $stamp), TRP_POTX_CONTEXT_NONE, $file);
  }
  $save_callback('am', TRP_POTX_CONTEXT_NONE, $file);
  $save_callback('pm', TRP_POTX_CONTEXT_NONE, $file);
  $save_callback('AM', TRP_POTX_CONTEXT_NONE, $file);
  $save_callback('PM', TRP_POTX_CONTEXT_NONE, $file);
}

/**
 * Add format_interval special strings, which cannot be
 * extracted otherwise. This is called for common.inc
 *
 * @param $file
 *   Name of the file parsed.
 * @param $save_callback
 *   Callback function used to save strings.
 * @param $api_version
 *   Drupal API version to work with.
 */
function trp_potx_add_format_interval_strings( $file, $save_callback, $api_version = TRP_POTX_API_CURRENT) {
  $components = array(
    '1 year' => '@count years',
    '1 week' => '@count weeks',
    '1 day'  => '@count days',
    '1 hour' => '@count hours',
    '1 min'  => '@count min',
    '1 sec'  => '@count sec'
  );
  if ($api_version > TRP_POTX_API_6) {
    // Month support added in Drupal 7.
    $components['1 month'] = '@count months';
  }

  foreach ($components as $singular => $plural) {
    // Intervals support no context.
    $save_callback($singular ."\0". $plural, TRP_POTX_CONTEXT_NONE, $file);
  }
}

/**
 * Add default theme region names, which cannot be extracted otherwise.
 * These default names are defined in system.module
 *
 * @param $file
 *   Name of the file parsed.
 * @param $save_callback
 *   Callback function used to save strings.
 * @param $api_version
 *   Drupal API version to work with.
 */
function trp_potx_add_default_region_names( $file, $save_callback, $api_version = TRP_POTX_API_CURRENT) {
  $regions = array(
    'left' => 'Left sidebar',
    'right' => 'Right sidebar',
    'content' => 'Content',
    'header' => 'Header',
    'footer' => 'Footer',
  );
  if ($api_version > TRP_POTX_API_6) {
    // @todo: Update with final region list when D7 stabilizes.
    $regions['highlight'] = 'Highlighted content';
    $regions['help'] = 'Help';
    $regions['page_top'] = 'Page top';
  }
  foreach ($regions as $region) {
    // Regions come with the default context.
    $save_callback($region, TRP_POTX_CONTEXT_NONE, $file);
  }
}

/**
 * Parse an .info file and add relevant strings to the list.
 *
 * @param $file_path
 *   Complete file path to load contents with.
 * @param $file_name
 *   Stripped file name to use in outpout.
 * @param $strings
 *   Current strings array
 * @param $api_version
 *   Drupal API version to work with.
 */
function trp_potx_find_info_file_strings( $file_path, $file_name, $save_callback, $api_version = TRP_POTX_API_CURRENT) {
  $info = array();

  if (file_exists($file_path)) {
    $info = $api_version > TRP_POTX_API_5 ? trp_drupal_parse_info_file($file_path) : parse_ini_file($file_path);
  }

  // We need the name, description and package values. Others,
  // like core and PHP compatibility, timestamps or versions
  // are not to be translated.
  foreach (array('name', 'description', 'package') as $key) {
    if (isset($info[$key])) {
      // No context support for .info file strings.
      $save_callback(addcslashes($info[$key], "\0..\37\\\""), TRP_POTX_CONTEXT_NONE, $file_name);
    }
  }

  // Add regions names from themes.
  if (isset($info['regions']) && is_array($info['regions'])) {
    foreach ($info['regions'] as $region => $region_name) {
      // No context support for .info file strings.
      $save_callback(addcslashes($region_name, "\0..\37\\\""), TRP_POTX_CONTEXT_NONE, $file_name);
    }
  }
}

/**
 * Parse a JavaScript file for translatables. Only from Drupal 6.
 *
 * Extracts strings wrapped in Drupal.t() and Drupal.formatPlural()
 * calls and inserts them into potx storage.
 *
 * Regex code lifted from _locale_parse_js_file().
 */
function trp_potx_parse_js_file( $code, $file, $save_callback) {
  $js_string_regex = '(?:(?:\'(?:\\\\\'|[^\'])*\'|"(?:\\\\"|[^"])*")(?:\s*\+\s*)?)+';

  // Match all calls to Drupal.t() in an array.
  // Note: \s also matches newlines with the 's' modifier.
  preg_match_all('~[^\w]Drupal\s*\.\s*t\s*\(\s*('. $js_string_regex .')\s*[,\)]~s', $code, $t_matches, PREG_SET_ORDER);
  if (isset($t_matches) && count($t_matches)) {
    foreach ($t_matches as $match) {
      // Remove match from code to help us identify faulty Drupal.t() calls.
      $code = str_replace($match[0], '', $code);
      // @todo: figure out how to parse out context, once Drupal supports it.
      $save_callback(trp_potx_parse_js_string( $match[1]), TRP_POTX_CONTEXT_NONE, $file, 0);
    }
  }

  // Match all Drupal.formatPlural() calls in another array.
  preg_match_all('~[^\w]Drupal\s*\.\s*formatPlural\s*\(\s*.+?\s*,\s*('. $js_string_regex .')\s*,\s*((?:(?:\'(?:\\\\\'|[^\'])*@count(?:\\\\\'|[^\'])*\'|"(?:\\\\"|[^"])*@count(?:\\\\"|[^"])*")(?:\s*\+\s*)?)+)\s*[,\)]~s', $code, $plural_matches, PREG_SET_ORDER);
  if (isset($plural_matches) && count($plural_matches)) {
    foreach ($plural_matches as $index => $match) {
      // Remove match from code to help us identify faulty
      // Drupal.formatPlural() calls later.
      $code = str_replace($match[0], '', $code);
      // @todo: figure out how to parse out context, once Drupal supports it.
      $save_callback(
        trp_potx_parse_js_string( $match[1]) ."\0". trp_potx_parse_js_string( $match[2]),
        TRP_POTX_CONTEXT_NONE,
        $file,
        0
      );
    }
  }

  // Any remaining Drupal.t() or Drupal.formatPlural() calls are evil. This
  // regex is not terribly accurate (ie. code wrapped inside will confuse
  // the match), but we only need some unique part to identify the faulty calls.
  preg_match_all('~[^\w]Drupal\s*\.\s*(t|formatPlural)\s*\([^)]+\)~s', $code, $faulty_matches, PREG_SET_ORDER);
  if (isset($faulty_matches) && count($faulty_matches)) {
    foreach ($faulty_matches as $index => $match) {
      $message = ($match[1] == 't') ? trp_t('Drupal.t() calls should have a single literal string as their first parameter.') : trp_t('The singular and plural string parameters on Drupal.formatPlural() calls should be literal strings, plural containing a @count placeholder.');
      trp_potx_status('error', $message, $file, NULL, $match[0], 'http://drupal.org/node/323109');
    }
  }
}

/**
 * Clean up string found in JavaScript source code. Only from Drupal 6.
 */
function trp_potx_parse_js_string( $string) {
  return trp_potx_format_quoted_string(implode('', preg_split('~(?<!\\\\)[\'"]\s*\+\s*[\'"]~s', $string)));
}

/**
 * Collect a list of file names relevant for extraction,
 * starting from the given path.
 *
 * @param $path
 *   Where to start searching for files recursively.
 *   Provide non-empty path values with a trailing slash.
 * @param $basename
 *   Allows the restriction of search to a specific basename
 *   (ie. to collect files for a specific module).
 * @param $api_version
 *   Drupal API version to work with.
 * @param $skip_self
 *   Skip potx related files. To be used when command line type of extraction
 *   is used and the potx files at in the webroot, and their strings should not
 *   end up in the generated template. Set to TRUE if skiping is needed.
 */
function trp_potx_explore_dir( $path = '', $basename = '*', $api_version = TRP_POTX_API_CURRENT, $skip_self = FALSE) {
  // It would be so nice to just use GLOB_BRACE, but it is not available on all
  // operarting systems, so we are working around the missing functionality.
  $extensions = array('php', 'inc', 'module', 'engine', 'theme', 'install', 'info', 'profile');
  if ($api_version > TRP_POTX_API_5) {
    $extensions[] = 'js';
  }
  $files = array();
  foreach ($extensions as $extension) {
    $files_here = glob($path . $basename .'.'. $extension);
    if (is_array($files_here)) {
      $files = array_merge($files, $files_here);
    }
    if ($basename != '*') {
      // Basename was specific, so look for things like basename.admin.inc as well.
      // If the basnename was *, the above glob() already covered this case.
      $files_here = glob($path . $basename .'.*.'. $extension);
      if (is_array($files_here)) {
        $files = array_merge($files, $files_here);
      }
    }
  }

  // Grab subdirectories.
  $dirs = glob($path .'*', GLOB_ONLYDIR);
  if (is_array($dirs)) {
    foreach ($dirs as $dir) {
      if (!preg_match("!(^|.+/)(CVS|\.svn|\.git|tests)$!", $dir)) {
        $files = array_merge($files, trp_potx_explore_dir("$dir/", $basename));
      }
    }
  }

  // Skip API and test files. Also skip our own files, if that was requested.
  $skip_pattern = ($skip_self ? '!(potx-cli\.php|potx\.inc|\.api\.php|\.test)$!' : '!(\.api\.php|\.test)$!');
  foreach ($files as $id => $file_name) {
    if (preg_match($skip_pattern, $file_name)) {
      unset($files[$id]);
    }
  }
  return $files;
}

/**
 * Default $version_callback used by the potx system. Saves values
 * to a global array to reduce memory consumption problems when
 * passing around big chunks of values.
 *
 * @param $value
 *   The ersion number value of $file. If NULL, the collected
 *   values are returned.
 * @param $file
 *   Name of file where the version information was found.
 */
function trp_potx_save_version( $value = NULL, $file = NULL) {
  global $_potx_versions;

  if (isset($value)) {
    $_potx_versions[$file] = $value;
  }
  else {
    return $_potx_versions;
  }
}

/**
 * Default $save_callback used by the potx system. Saves values
 * to global arrays to reduce memory consumption problems when
 * passing around big chunks of values.
 *
 * @param $value
 *   The string value. If NULL, the array of collected values
 *   are returned for the given $string_mode.
 * @param $context
 *   From Drupal 7, separate contexts are supported. POTX_CONTEXT_NONE is
 *   the default, if the code does not specify a context otherwise.
 * @param $file
 *   Name of file where the string was found.
 * @param $line
 *   Line number where the string was found.
 * @param $string_mode
 *   String mode: POTX_STRING_INSTALLER, POTX_STRING_RUNTIME
 *   or POTX_STRING_BOTH.
 */
function trp_potx_save_string( $value = NULL, $context = NULL, $file = NULL, $line = 0, $string_mode = TRP_POTX_STRING_RUNTIME) {
  global $_potx_strings, $_potx_install;

  if (isset($value)) {

    // Value set but empty. Mark error on empty translatable string. Only trim
    // for empty string checking, since we should store leading/trailing
    // whitespace as it appears in the string otherwise.
    $check_empty = trim($value);
    if (empty($check_empty)) {
      trp_potx_status('error', trp_t('Empty string attempted to be localized. Please do not leave test code for localization in your source.'), $file, $line);
      return;
    }

    switch ($string_mode) {
      case TRP_POTX_STRING_BOTH:
        // Mark installer strings as duplicates of runtime strings if
        // the string was both recorded in the runtime and in the installer.
        $_potx_install[$value][$context][$file][] = $line .' (dup)';
        // Break intentionally missing.
      case TRP_POTX_STRING_RUNTIME:
        // Mark runtime strings as duplicates of installer strings if
        // the string was both recorded in the runtime and in the installer.
        $_potx_strings[$value][$context][$file][] = $line . ($string_mode == TRP_POTX_STRING_BOTH ? ' (dup)' : '');
        break;
      case TRP_POTX_STRING_INSTALLER:
        $_potx_install[$value][$context][$file][] = $line;
        break;
    }
  }
  else {
    return ($string_mode == TRP_POTX_STRING_RUNTIME ? $_potx_strings : $_potx_install);
  }
}

if (!function_exists('trp_t')) {
  // If invoked outside of Drupal, t() will not exist, but
  // used to format the error message, so we provide a replacement.
  function trp_t( $string, $args = array()) {
    return strtr($string, $args);
  }
}

if (!function_exists('trp_drupal_parse_info_file')) {
  // If invoked outside of Drupal, drupal_parse_info_file() will not be available,
  // but we need this function to properly parse Drupal 6 .info files.
  // Directly copied from common.inc,v 1.756.2.76 2010/02/01 16:01:41 goba Exp.
  function trp_drupal_parse_info_file( $filename) {
    $info = array();
    $constants = get_defined_constants();

    if (!file_exists($filename)) {
      return $info;
    }

    $data = file_get_contents($filename);
    if (preg_match_all('
      @^\s*                           # Start at the beginning of a line, ignoring leading whitespace
      ((?:
        [^=;\[\]]|                    # Key names cannot contain equal signs, semi-colons or square brackets,
        \[[^\[\]]*\]                  # unless they are balanced and not nested
      )+?)
      \s*=\s*                         # Key/value pairs are separated by equal signs (ignoring white-space)
      (?:
        ("(?:[^"]|(?<=\\\\)")*")|     # Double-quoted string, which may contain slash-escaped quotes/slashes
        (\'(?:[^\']|(?<=\\\\)\')*\')| # Single-quoted string, which may contain slash-escaped quotes/slashes
        ([^\r\n]*?)                   # Non-quoted string
      )\s*$                           # Stop at the next end of a line, ignoring trailing whitespace
      @msx', $data, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        // Fetch the key and value string
        $i = 0;
        foreach (array('key', 'value1', 'value2', 'value3') as $var) {
          $$var = isset($match[++$i]) ? $match[$i] : '';
        }
        $value = stripslashes(substr($value1, 1, -1)) . stripslashes(substr($value2, 1, -1)) . $value3;

        // Parse array syntax
        $keys = preg_split('/\]?\[/', rtrim($key, ']'));
        $last = array_pop($keys);
        $parent = &$info;

        // Create nested arrays
        foreach ($keys as $key) {
          if ($key == '') {
            $key = count($parent);
          }
          if (!isset($parent[$key]) || !is_array($parent[$key])) {
            $parent[$key] = array();
          }
          $parent = &$parent[$key];
        }

        // Handle PHP constants.
        if (isset($constants[$value])) {
          $value = $constants[$value];
        }

        // Insert actual value
        if ($last == '') {
          $last = count($parent);
        }
        $parent[$last] = $value;
      }
    }

    return $info;
  }
}
