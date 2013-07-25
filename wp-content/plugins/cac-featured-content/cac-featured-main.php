<?php
/**
 * Protection 
 * 
 * This string of code will prevent hacks from accessing the file directly.
 */
defined( 'ABSPATH' ) or die( 'Cannot access pages directly.' );

/**
 * The directory separator is different between Linux and Microsoft servers.
 * Thankfully PHP sets the DIRECTORY_SEPARATOR constant so that we know what
 * to use.
 */
defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );

/**
 * Actions and Filters
 *
 * @author Dominic Giglio
 * 
 * Register any and all actions here. Nothing should actually be called 
 * directly, the entire system will be based on these actions and hooks.
 */
add_action( 'admin_print_scripts-widgets.php', 'cac_featured_admin_js' );
add_action( 'admin_init', 'cac_featured_admin_init' );
add_action( 'widgets_init', create_function( '', 'register_widget("CAC_Featured_Content_Widget");' ) );

function cac_featured_admin_js() {
  $plugin_url = trailingslashit( get_bloginfo( 'wpurl' ) ) . PLUGINDIR . DS . dirname( plugin_basename( __FILE__ ) );
  wp_enqueue_script( 'jquery-ui-autocomplete' );
  wp_enqueue_script( 'cac_featured_admin_js', $plugin_url . DS . 'js' . DS . 'cac_featured_admin.js', array( 'jquery', 'jquery-ui-autocomplete' ) );
}

function cac_featured_admin_init() {
  require dirname( __FILE__ ) . '/cac-featured-autocomplete.php';
  load_plugin_textdomain( 'cac-featured-content', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

/**
 * This class holds all the view related variables needed to display an instance 
 * of the CAC Featured Content Widget. They are stored here to help keep
 * the global namespace clean and avoid naming conflicts.
 *
 * @author Dominic Giglio
*/
class CAC_Featured_Content_View {

  // this array holds all the variables that are accessed "magically"
  private $data = array();

  // the __setter will set any property of the $data array to the given value
  public function __set( $name, $value ) {
    $this->data[$name] = $value;
  }

  // the __getter will return any value for the associated name
  public function __get( $name ) {
    if ( array_key_exists( $name, $this->data ) ) {
        return $this->data[$name];
    }
  }

} // end CAC_Featured_Content_View class

/**
 * Main CAC Featured Content Class
 *
 * @author Dominic Giglio
 */
class CAC_Featured_Content_Widget extends WP_Widget {

	/**
	 * Widget settings
	 * 
	 * This multi-dimensional array holds the options used to create the WordPress Widget that
	 * will be displayed to administrators. All of its values are set in __construct() because
   * you can't internationalize or localize the strings when declaring an array's default values.
   * These array's options can then be found in the $params variable within the widget method.
	 */
	protected $widget = array();

	/**
	 * Constructor
   *
   * @author Dominic Giglio
	 * 
	 * Registers the widget details with the parent class, based off of the options
	 * that were defined within the widget property. This method does not need to be
	 * changed.
	 */

  // PHP5 constructor
	function __construct() {

    $this->widget = array(

      'name' => __( 'CAC Featured Content', 'cac-featured-content' ),

      // this description will display within the administrative widgets area
      'description' => __( 'The CAC Featured Content plugin provides a widget that allows you to select from five different content "types" to feature in any number of widgetized areas.', 'cac-featured-content' ),

      // determines whether or not to use the sidebar _before and _after html
      'do_wrapper' => true, 

      // to render a view file from the views directory set this to true,
      // to render the code in the html() method set this to false
      'view' => true,

      // the fields array holds each element that will be output by the form() 
      // method on the admin widgets page
      'fields' => array(
        // widget title
        array(
          'name'    => __( 'Widget Title', 'cac-featured-content' ),
          'desc'    => '',
          'id'      => 'title',
          'type'    => 'text',
          'default' => 'Featured'
        ),
        // title HTML element
        array(
          'name'    => __( 'Widget Title Element', 'cac-featured-content' ),
          'desc'    => '',
          'id'      => 'title_element',
          'class'   => 'alignright',
          'type'    => 'select',
          'default' => 'h3',
          'options' => array(
            'h1' => 'h1',
            'h2' => 'h2',
            'h3' => 'h3',
            'h4' => 'h4',
            'h5' => 'h5',
            'h6' => 'h6'
          )
        ),
        // type of featured content
        array(
          'name'    => __( 'Featured Content Type', 'cac-featured-content' ),
          'desc'    => '',
          'id'      => 'featured_content_type',
          'class'   => 'featured_select alignright',
          'type'    => 'select',
          'options' => array(
            'group'    => __( 'Group', 'cac-featured-content' ),
            'post'     => __( 'Post', 'cac-featured-content' ),
            'member'   => __( 'Member', 'cac-featured-content' ),
            'resource' => __( 'Resource', 'cac-featured-content' )
          )
        ),
        // featured post
        array(
          'name' => __( 'Enter featured post name', 'cac-featured-content' ),
          'desc' => __( 'no http:// required', 'cac-featured-content' ),
          'id'   => 'featured_post',
          'type' => 'text',
          'class' => 'featured_post featured_post_ac autocomplete'
        ),
        // featured group
        array(
          'name'  => __( 'Enter featured group name', 'cac-featured-content' ),
          'desc'  => '',
          'id'    => 'featured_group',
          'type'  => 'text',
          'class' => 'featured_group featured_group_ac autocomplete'
        ),
        // featured member
        array(
          'name' => __( 'Enter featured member username', 'cac-featured-content' ),
          'desc' => '',
          'id'   => 'featured_member',
          'type' => 'text',
          'class' => 'featured_member featured_member_ac autocomplete'
        ),
        // featured resource title
        array(
          'name' => __( 'Enter featured resource title', 'cac-featured-content' ),
          'desc' => '',
          'id'   => 'featured_resource_title',
          'type' => 'text'
        ),
        // featured resource link
        array(
          'name' => __( 'Enter featured resource link', 'cac-featured-content' ),
          'desc' => '',
          'id'   => 'featured_resource_link',
          'type' => 'text'
        ),
        // custom description
        array(
          'name'    => __( 'Custom Description', 'cac-featured-content' ),
          'desc'    => __( "Leave this blank and we'll select text appropriate for your featured item (e.g. a group description).", 'cac-featured-content' ),
          'id'      => 'custom_description',
          'type'    => 'textarea'
        ),
        // text crop length
        array(
          'name'    => __( 'Crop Length (characters)', 'cac-featured-content' ),
          'desc'    => '',
          'id'      => 'crop_length',
          'type'    => 'text',
          'class'   => 'small-text',
          'default' => '250'
        ),
        // read more text
        array(
          'name'    => __( 'Read More Label', 'cac-featured-content' ),
          'desc'    => '',
          'id'      => 'read_more',
          'type'    => 'text',
          'default' => 'Read More...'
        ),
        // display images checkbox
        array(
          'id'   => 'display_images',
          'class' => 'cfcw_checkbox',
          'type' => 'checkbox',
          'options' => array(
              // 'value in db' => 'name to display'
              '1' => __( 'Display Images', 'cac-featured-content' )
          ),
        ),
        // image url
        array(
          'name'    => __( 'Image URL', 'cac-featured-content' ),
          'desc'    => __( "Leave this blank and we'll try to find an image appropriate to your featured item (e.g. an avatar).", 'cac-featured-content' ),
          'id'      => 'image_url',
          'type'    => 'text',
        ),
        // image width
        array(
          'name'    => __( 'Image Width', 'cac-featured-content' ),
          'desc'    => '',
          'id'      => 'image_width',
          'type'    => 'text',
          'class'   => 'small-text',
          'default' => '50'
        ),
        // image height
        array(
          'name'    => __( 'Image Height', 'cac-featured-content' ),
          'desc'    => '',
          'id'      => 'image_height',
          'type'    => 'text',
          'class'   => 'small-text',
          'default' => '50'
        ),
      )
    );

    // we need to handle featured types differently between MS and Non-MS sites so we
    // add support for featured blogs in the admin here, only if MS is enabled
    if ( is_multisite() ) {
      // add the 'blog' content type
      $this->widget['fields'][2]['options']['blog'] = __( 'Blog', 'cac-featured-content' );
      
      // tweak the post input field description
      $this->widget['fields'][3]['desc'] = __( 'You must enter a blog address first <br /> no http:// required', 'cac-featured-content' );

      // add the featured_blog text input field
      // because of the way the form() method builds the admin interface we need the
      // blog text input field to be in a specific position, so we use array_splice()
      array_splice( $this->widget['fields'], 3, 0 , array(
        array(
          'name' => __( 'Enter featured blog address', 'cac-featured-content' ),
          'desc' => __( 'no http:// required', 'cac-featured-content' ),
          'id'   => 'featured_blog',
          'type' => 'text',
          'class' => 'featured_blog featured_blog_ac autocomplete')
        )
      );
    }

		// instantiate our widget
		parent::WP_Widget( 
			$id = sanitize_title( get_class( $this ) ), 
			$name = ( isset( $this->widget['name']) ? $this->widget['name'] : $classname ), 
			$options = array( 'description' => $this->widget['description'] )
		);

	}

	/**
	 * Widget View
	 * 
	 * This method determines what view method is being used and gives that view
	 * method the proper parameters to operate.
	 *
	 * @param array $sidebar
	 * @param array $params
	 */
	function widget( $sidebar, $params ) {

		//initializing variables
		$this->widget['number'] = $this->number;
		$do_wrapper = ( !isset( $this->widget['do_wrapper'] ) || $this->widget['do_wrapper'] );

		if ( $do_wrapper ) 
			echo $sidebar['before_widget'];

    // require the controller which will setup and load our views
    require 'cac-featured-controller.php';

		if ( $do_wrapper ) 
			echo $sidebar['after_widget'];
	}

	/**
	 * Administration Form
	 * 
	 * This method is called from within the wp-admin/widgets area when this
	 * widget is placed into a sidebar. The resulting is a widget options form
	 * that allows the admin to modify how the widget operates.
	 * 
	 * You do not need to adjust this method what-so-ever, it will parse the array
	 * parameters given to it from the protected widget property of this class.
	 *
	 * @param array $instance
	 * @return boolean
	 */
  function form( $instance = array() ) {

    // initializing
    $fields = $this->widget['fields'];

    $defaults = array(
      'name'     => '',
      'desc'     => '',
      'id'       => '',
      'type'     => 'text',
      'options'  => array(),
      'default'  => '',
      'value'    => '',
      'class'    => '',
      'multiple' => '',
      'args'     => array(
        'hide_empty'   => 0, 
        'name'         => 'element_name', 
        'hierarchical' => true
      )
    );
    
    // reasons to fail
    if ( !empty( $fields ) ) {

      foreach ( $fields as $field ) {

        // initializing the individual field
        $field = wp_parse_args( $field, $defaults );
        $field['args'] = wp_parse_args( $field['args'], $defaults['args'] );
        
        extract( $field );
        $field['args']['name'] = $element_name = $id;
        
        // grabbing the meta value
        if ( array_key_exists( $id, $instance ) )
          @$meta = esc_attr( $instance[$id] );
        else
          $meta = $default;

        $field['args']['name'] = $element_name = $this->get_field_name( $id );
        $id = $this->get_field_id( $id );
        
        switch ( $type ) : default: ?>
          <?php case 'text': ?>
            <p>
              <label for="<?php echo $id; ?>">
                <?php echo $name; ?>:
                <input id="<?php echo $id; ?>" name="<?php echo $element_name; ?>" 
                  value="<?php echo $meta; ?>" type="<?php echo $type; ?>" 
                  class="text large-text <?php echo $class; ?>" />
              </label>
              <br/>
              <span class="description"><?php echo $desc; ?></span>
            </p>
          <?php break; ?>
          <?php case 'textarea': ?>
            <p>
              <label for="<?php echo $id; ?>">
                <?php echo $name; ?>:
                <textarea cols="60" rows="4" style="width:97%"
                  id="<?php echo $id; ?>" name="<?php echo $element_name; ?>" 
                  class="large-text <?php echo $class; ?>"><?php echo $meta; ?></textarea>
              </label>
              <br/>
              <span class="description"><?php echo $desc; ?></span>
            </p>
          <?php break; ?>
          <?php case 'select': ?>
            <p>
              <label for="<?php echo $id; ?>">
                <?php echo $name; ?>:
                <select <?php echo $multiple ? "MULTIPLE SIZE='$multiple'" : ''; ?>
                  id="<?php echo $id; ?>" name="<?php echo $element_name; ?>" 
                  class="<?php echo $class; ?>">
                  
                  <?php foreach ( (array) $options as $_value => $_name ): ?>
                    <?php $_value = !is_int( $_value ) ? $_value : $_name; ?>
                    <option 
                      value="<?php echo $_value; ?>"
                      <?php echo $meta == $_value ? ' selected="selected"' : ''; ?>
                      ><?php echo $_name; ?>
                    </option>
                  <?php endforeach; ?>

                </select>
              </label>
              <br/>
              <span class="description"><?php echo $desc; ?></span>
            </p>
          <?php break; ?>
          <?php case 'radio': ?>
            <p><?php echo $name; ?>:</p>
            <p>
              <?php foreach ( (array) $options as $_value => $_name ): ?>
                <label class="<?php echo $element_name; ?>" for="<?php echo $id; ?>">
                  <input name="<?php echo $element_name; ?>"  id="<?php echo $id; ?>" 
                    value="<?php echo $_value; ?>" type="<?php echo $type; ?>" 
                    <?php echo $meta == $_value ? ' checked="checked"' : ''; ?>
                    class="<?php echo $class; ?>" />
                  <?php echo $_name; ?>
                </label>
              <?php endforeach; ?>
              <br/>
              <span class="description"><?php echo $desc; ?></span>
            </p>
          <?php break; ?>
          <?php case 'checkbox': ?>
            <?php if ( $name ): ?>
              <p><?php echo $name; ?> : </p>
            <?php endif; ?>
            
            <p>
              <!-- first hidden input forces this item to be submitted 
              via javascript, when it is not checked -->
              <input type="hidden" name="<?php echo $element_name; ?>" value="" />
              
              <?php foreach ( (array) $options as $_value => $_name ): ?>
              <label class="<?php echo $element_name; ?>" for="<?php echo $id; ?>">
                <input value="<?php echo $_value; ?>" type="<?php echo $type; ?>" 
                  name="<?php echo $element_name; ?>" id="<?php echo $id; ?>" 
                  <?php echo $meta == $_value? 'checked="checked"' :''; ?>
                  class="<?php echo $class; ?>" />
                <?php echo $_name; ?>
              </label>
              <br/>
              <?php endforeach; ?>
              <span class="description"><?php echo $desc; ?></span>
            </p>
          <?php break; ?>
          <?php case 'title': ?>
              <h3 style="border: 1px solid #ddd;
                padding: 10px;
                background: #eee;
                border-radius: 2px;
                color: #666;
                margin: 0;"><?php echo $name; ?></h3>
          <?php break; ?>
          <?php case 'hidden': ?>
            <input 
              id="<?php echo $id; ?>" name="<?php echo $element_name; ?>" 
              value="<?php echo $meta; ?>" type="<?php echo $type; ?>" 
              style="visibility:hidden;" />
          <?php break; ?>
      <?php endswitch;
      }
    }
    return true;
  }

	/**
	 * Update the Administrative parameters
	 * 
	 * This function will merge any posted parameters with that of the saved
	 * parameters. This ensures that the widget options never get lost. This
	 * method does not need to be changed.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = wp_parse_args( $new_instance, $old_instance );
		return $instance;
	}

} // end CAC_Featured_Content_Widget class

?>
