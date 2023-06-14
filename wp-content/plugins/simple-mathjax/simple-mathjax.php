<?php
/*
 * Plugin Name: Simple MathJax
 * Description: Load the mathjax scripts across your wordpress blog
 * Version: 2.0.2
 * Author: Samuel Coskey, Peter Krautzberger, Christian Lawson-Perfect
 * Author URI: https://boolesrings.org
 */

class SimpleMathjax {

  /*
   * Default options for the plugin
   */ 
  public static $default_options = array(
    'major_version' => 3,
    'mathjax_in_admin' => false,
    'custom_mathjax_cdn' => '',
    'custom_mathjax_config' => '',
    'latex_preamble' => ''
  );

  /*
   * Default MathJax configuration scripts, for each major version.
   */
  public static $default_configs = array(
    2 => "MathJax.Hub.Config({\n  tex2jax: {\n    inlineMath: [['$','$'], ['\\\\(','\\\\)']],\n    processEscapes: true,\n    ignoreHtmlClass: 'tex2jax_ignore|editor-rich-text'\n  }\n});\n",
    3 => "MathJax = {\n  tex: {\n    inlineMath: [['$','$'],['\\\\(','\\\\)']], \n    processEscapes: true\n  },\n  options: {\n    ignoreHtmlClass: 'tex2jax_ignore|editor-rich-text'\n  }\n};\n"
  );

  /*
   * Default CDN URLs, for each major version.
   */
  public static $default_cdns = array(
    2 => "//cdn.jsdelivr.net/npm/mathjax@2.7.8/MathJax.js?config=TeX-MML-AM_CHTML,Safe.js",
    3 => "//cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"
  );

  /*
   * Load the plugin's options, beginning with the default options
   */
  public static function load_options() {
    $options = array_merge(self::$default_options,array());

    // restore options from old versions of this plugin
    $old_keys = array('custom_mathjax_cdn', 'custom_mathjax_config', 'latex_preamble', 'mathjax_in_admin');
    $has_old_values = false;
    foreach($old_keys as $key) {
      if(($value = get_option($key)) !== false) {
        $options[$key] = $value;
        $has_old_values = true;
      }
    }


    // apply options set locally
    $set_options = get_option('simple_mathjax_options');
    if(is_array($set_options)) {
      foreach($set_options as $key=>$value) {
        if(array_key_exists($key,$options)) {
          $options[$key] = $value;
        }
      }
    }

    if($has_old_values) {
      $options['major_version'] = 2;
      foreach($old_keys as $key) {
        delete_option($key);
      }
      add_option('simple_mathjax_options',$options);
    }

    return $options;
  }

  /*
   * Insert the mathjax configuration into the page.
   */
  public static function configure_mathjax() {
    $options = self::load_options();
    $version = $options['major_version'];
    $custom_config = wp_kses( $options['custom_mathjax_config'], array() );
    $config = $custom_config ? $custom_config : self::$default_configs[$version];
    if($version==2) {
      echo "\n<script type='text/x-mathjax-config'>\n{$config}\n</script>\n";
    } else {
      echo "\n<script>\n{$config}\n</script>\n";
    }
  }

  /*
   * Load the MathJax scripts.
  */
  public static function add_mathjax() {
    $options = self::load_options();
    $version = $options['major_version'];
    $custom_cdn = esc_url( $options['custom_mathjax_cdn'] );
    $cdn = $custom_cdn ? $custom_cdn : self::$default_cdns[$version];
    wp_enqueue_script('mathjax', $cdn, array(), false, true);
  }

  /*
   * Insert the MathJax preamble inside the body and above the content.
  */
  public static function add_preamble_adder() {
    $options = self::load_options();
    $version = $options['major_version'];
    $preamble = $options['latex_preamble'];
    if ( $preamble ) {
      if($version==2) {
        $preamble = preg_replace('/\\\\/','\\\\\\\\',$preamble);

  ?>
  <script>
    (function() {
      var newContainer = document.createElement('span');
      newContainer.style.setProperty("display","none","");
      var newNode = document.createElement('script');
      newNode.type = "math/tex";
      var preamble = '<?php echo esc_js($preamble); ?>';
      newNode.innerHTML = preamble;
      newContainer.appendChild(newNode);
      document.body.insertBefore(newContainer,document.body.firstChild);
    })();
  </script>
  <?php

      } else if($version==3) {

  ?>
  <script>
    (function() {
      var newNode = document.createElement('span');
      newNode.style.setProperty("display","none","");
      var preamble = '\\( <?= esc_js(addslashes($preamble)); ?> \\)';
      newNode.innerHTML = preamble;
      document.body.insertBefore(newNode,document.body.firstChild);
    })();
  </script>
  <?php

      }
    }
  }

  /*
   * Validate an array of options: only keep keys corresponding to options, and set 'mathjax_in_admin' to a boolean.
   */
  public static function options_validate($options) {
    $cleaned = array();
    foreach(self::$default_options as $key => $value) {
      $cleaned[$key] = $options[$key];
    }
    $cleaned['mathjax_in_admin'] = $cleaned['mathjax_in_admin'] ? true : false;
    return $cleaned;
  }

  /*
   * Text at the top of the 'Main settings' section.
   */
  public static function main_text() {
  }

  /*
   * Text at the top of the 'Configuration' section.
   */
  public static function config_text() {
  }

  /*
   * Select box for the 'MathJax major version' option.
   */
  public static function major_version_input() {
    $options = self::load_options();
  ?>
    <select id="major_version" name="simple_mathjax_options[major_version]">
      <option value="2" <?= $options['major_version']==2 ? 'selected' : '' ?>>2</option>
      <option value="3" <?= $options['major_version']==3 ? 'selected' : '' ?>>3</option>
    </select>
    <p>MathJax versions 2 and 3 work very differently. See the <a href="http://docs.mathjax.org/en/latest/upgrading/v2.html">MathJax documentation</a>.</p>
  <?php
  }

  /*
   * Tickbox for the 'Load MathJax on admin pages?' option.
   */
  public static function in_admin_input() {
    $options = self::load_options();
  ?>
    <input type="checkbox" id="mathjax_in_admin" name="simple_mathjax_options[mathjax_in_admin]" <?= $options['mathjax_in_admin'] ? 'checked' : '' ?>>
    <p>If you tick this box, MathJax will be loaded on admin pages as well as the actual site.</p>
  <?php
  }

  /*
   * Input box for the 'Custom MathJax CDN' option.
   */
  public static function cdn_input() {
    $options = self::load_options();
  ?>
    <input type="text" id="custom_mathjax_cdn" size="50" name="simple_mathjax_options[custom_mathjax_cdn]" value="<?= $options['custom_mathjax_cdn'] ?>">
    <p>If you leave this blank, the default will be used, depending on the major version of MathJax:</p>
    <dl>
      <dt>Version 2</dt>
      <dd><code><?= self::$default_cdns[2] ?></code></dd>
      <dt>Version 3</dt>
      <dd><code><?= self::$default_cdns[3] ?></code></dd>
    </dl>
  <?php
  }

  /*
   * Textarea for the 'Custom MathJax config' option.
   */
  public static function config_input() {
    $options = self::load_options();
  ?>
    <textarea id="custom_mathjax_config" cols="50" rows="10" name="simple_mathjax_options[custom_mathjax_config]"><?= $options['custom_mathjax_config'] ?></textarea>
    <p>This text will be used to configure MathJax. See <a href="https://docs.mathjax.org/en/latest/options/index.html">the documentation on configuring MathJax</a>.</p>
    <p>If you leave this blank, the default will be used, according to the major version of MathJax:</p>
    <dl>
      <dt>Version 2</dt>
      <dd><pre><?= self::$default_configs[2] ?></pre></dd>
      <dt>Version 3</dt>
      <dd><pre><?= self::$default_configs[3] ?></pre></dd>
    </dl>
  <?php
  }

  /*
   * Textarea for the 'Custom LaTeX preamble' option.
   */
  public static function latex_preamble_input() {
    $options = self::load_options();
  ?>
    <textarea id="latex_preamble" cols="50" rows="10" name="simple_mathjax_options[latex_preamble]"><?= $options['latex_preamble'] ?></textarea>
    <p>This LaTeX will be run invisibly before any other LaTeX on the page. A good place to put \newcommand's and \renewcommand's</p>
    <p><strong>Do not us $ signs</strong>, they will be added for you</p>
    <p>E.g.</p>
    <pre>\newcommand{\NN}{\mathbb N}
  \newcommand{\abs}[1]{\left|#1\right|}</pre>
  <?php
  }

  /*
   * The options pane in the settings section
  */
  public static function create_menu() {
    $simple_mathjax_page = add_options_page(
      'Simple MathJax options',  // Name of page
      'Simple MathJax',           // Label in menu
      'manage_options',           // Capability required
      'simple_mathjax_options',  // Menu slug, used to uniquely identify the page
      'SimpleMathJax::options_page'    // Function that renders the options page
    );

    if ( ! $simple_mathjax_page )
      return;

    //call register settings function
    add_action( 'admin_init', 'SimpleMathJax::register_settings' );
  }

  /*
   * Render the options page.
   */
  public static function options_page() {
  ?>
  <div>
  <h1>Simple MathJax options</h1>
  <form method="post" action="options.php">
    <?php settings_fields( 'simple_mathjax_options' ); ?>
    <?php do_settings_sections('simple_mathjax'); ?>

    <button type="submit"><?php _e('Save Changes'); ?></button>
  </form>
  </div>
  <?php }

  public static function register_settings() {
    register_setting(
      'simple_mathjax_options', 
      'simple_mathjax_options', 
      array(
        'sanitize_callback' => 'SimpleMathJax::options_validate'
      )
    );

    add_settings_section(
      'simple_mathjax_main', 
      'Main Settings', 
      'SimpleMathJax::main_text', 
      'simple_mathjax'
    );
    add_settings_section('simple_mathjax_config',
      'Configuration',
      'SimpleMathJax::config_text',
      'simple_mathjax'
    );

    add_settings_field(
      'major_version',
      'MathJax major version',
      'SimpleMathJax::major_version_input',
      'simple_mathjax',
      'simple_mathjax_main',
      array(
        'label_for' => 'major_version'
      )
    );
    add_settings_field(
      'mathjax_in_admin',
      'Load MathJax on admin pages?',
      'SimpleMathJax::in_admin_input',
      'simple_mathjax',
      'simple_mathjax_main',
      array(
        'label_for' => 'mathjax_in_admin'
      )
    );

    add_settings_field(
      'custom_mathjax_cdn',
      'Custom MathJax CDN',
      'SimpleMathJax::cdn_input',
      'simple_mathjax',
      'simple_mathjax_config',
      array(
        'label_for' => 'custom_mathjax_cdn'
      )
    );
    add_settings_field(
      'custom_mathjax_config',
      'Custom MathJax config',
      'SimpleMathJax::config_input',
      'simple_mathjax',
      'simple_mathjax_config',
      array(
        'label_for' => 'custom_mathjax_config'
      )
    );
    add_settings_field(
      'latex_preamble',
      'Custom LaTeX preamble',
      'SimpleMathJax::latex_preamble_input',
      'simple_mathjax',
      'simple_mathjax_config',
      array(
        'label_for' => 'latex_preamble'
      )
    );

  }
}

add_action('wp_head','SimpleMathJax::configure_mathjax',1);
add_action('wp_enqueue_scripts', 'SimpleMathJax::add_mathjax');
add_action('wp_footer', 'SimpleMathJax::add_preamble_adder');
/*
 * Perform all three actions in admin pages too, if the option is set (CP)
 */
$options = SimpleMathJax::load_options();
if ( $options['mathjax_in_admin'] ) {
   add_action('admin_head', 'SimpleMathJax::configure_mathjax', 1);
   add_action('admin_enqueue_scripts', 'SimpleMathJax::add_mathjax');
   add_action('admin_footer', 'SimpleMathJax::add_preamble_adder');
}
add_action('admin_menu', 'SimpleMathJax::create_menu');
