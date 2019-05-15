<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class FV_Player_List_Table_View {

  function __construct() {
    add_action( 'init', array( $this, 'load_options' ) );
  }

  function admin_menu(){    
    global $wpdb;
    if( current_user_can('edit_posts')  ) {
      add_menu_page( 'FV Player', 'FV Player', 'edit_posts', 'fv_player', '', flowplayer::get_plugin_url().'/images/icon@x2.png', 30 );
      add_submenu_page(  'fv_player', 'FV Player', 'FV Player', 'edit_posts', 'fv_player', array($this, 'tools_panel') );
    }
  }
  
  function load_options() {
    add_action( 'admin_menu', array($this, 'admin_menu') );
    add_action( 'admin_head', array($this, 'styling') );    
  }
  
  function styling() {
    if( isset($_GET['page']) && $_GET['page'] == 'fv_player' ) {
      global $fv_wp_flowplayer_ver;
      wp_enqueue_style('fv-player-list-view', flowplayer::get_plugin_url().'/css/list-view.css',array(), $fv_wp_flowplayer_ver );
      
      wp_enqueue_media();
    }
		?>
		<style>#adminmenu #toplevel_page_fv_player .wp-menu-image img {width:28px;height:25px;padding-top:4px}</style>
		<?php
  }
  
  function tools_panel() {
		$table = new FV_Player_List_Table();
		$table->prepare_items();
  	?>
  	<div class="wrap">
      <h1 class="wp-heading-inline">FV Player</h1>
      <a href="#" class="page-title-action fv-player-edit" data-add_new="1">Add New</a>
      <a href="#" class="page-title-action fv-player-import">Import</a>

      <div id="fv_player_players_table">
          <form id="fv-player-filter" method="get" action="<?php echo admin_url( 'admin.php?page=fv_player' ); ?>">
              <input type="hidden" name="page" value="fv_player" />

              <?php $table->views() ?>

              <?php $table->advanced_filters(); ?>

              <?php $table->display() ?>
          </form>
      </div>
    
  	</div>
  <?php 
    fv_player_shortcode_editor_scripts_enqueue();
    fv_wp_flowplayer_edit_form_after_editor();
  
    //add_action( 'admin_footer', array($this, 'scripts') );
  }  
}

$FV_Player_List_Table_View = new FV_Player_List_Table_View;
  
class FV_Player_List_Table extends WP_List_Table {

	public $per_page = 25;

	public $base_url;
  
  public $counts;
	
	public $total_impressions = 0;
	
	public $total_clicks = 0;
  
  public $total_items = 0;
  
  private $dropdown_cache = false;

	public function __construct() {

		global $status, $page;

		parent::__construct( array(
			'singular' => 'Log entry',
			'plural'   => 'Log entries',
			'ajax'     => false,
		) );

		$this->get_result_counts();
		$this->process_bulk_action();
		$this->base_url = admin_url( 'admin.php?page=fv_player' );
	}
	
	public function advanced_filters() {
    if ( ! empty( $_REQUEST['orderby'] ) )
      echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
    if ( ! empty( $_REQUEST['order'] ) )
      echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';

    if (isset($_GET['id'])) {
      $input_id = $_GET['id'];
    } else {
      $input_id = null;
    }
    ?>
    <p class="search-box">
      <label class="screen-reader-text" for="<?php echo $input_id ?>">Search players:</label>
      <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
      <?php submit_button( "Search players", 'button', false, false, array('ID' => 'search-submit') ); ?><br/>
    </p>
    <?php
	}
  
	public function get_columns() {
		return array(
			//'cb'          => '<input type="checkbox" />',
			'id'           => __( 'Playlist', 'fv-wordpress-flowplayer' ),
      'player_name'  => __( 'Playlist Name', 'fv-wordpress-flowplayer' ),
      'date_created' => __( 'Date', 'fv-wordpress-flowplayer' ),
      //'author'       => __( 'Author', 'fv-wordpress-flowplayer' ),
			'thumbs'       => __( 'Videos', 'fv-wordpress-flowplayer' ),
      'embeds'       => __( 'Embedded on', 'fv-wordpress-flowplayer' ),
      'shortcode'    => __( 'Shortcode', 'fv-wordpress-flowplayer' ),
      'shortcode-copy'    => '',
		);
	}
  
	public function get_sortable_columns() {
		return array(
		  'id'           => array( 'ID', true ),
      'player_name'  => array( 'player_name', true ),
      'date_created' => array( 'date_created', true )
		);
	}
  
	protected function get_primary_column_name() {
		return 'id';
	}
  
  function get_user_dropdown( $user_id, $name = false, $disabled = false ) {
    if( !$this->dropdown_cache ) {
      $this->dropdown_cache  = wp_dropdown_users( array(
        'name' => 'user_id',
        'role__not_in' => array('subscriber'),
        'show_option_none' => 'All users',
        'echo' => false
      ) );
    }
    $html = $this->dropdown_cache;
    
    $html = str_replace("value='".$user_id."'>","value='".$user_id."' selected>",$html);
    if( $name ) $html = str_replace("name='user_id' ","name='".$name."' ' ",$html);
    if( $disabled ) $html = str_replace("<select ","<select disabled='disabled' ",$html);
    
    return $html;
  }
	
	public function column_cb( $player ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			'log_id',
			$player->id
		);
	}
  
	public function column_default( $player, $column_name ) {
    $id = $player->id;
		switch ( $column_name ) {
      case 'id':        
        $value = '<span class="fv_player_id_value" data-player_id="'. $id .'">' . $id . '</span>';
        break;
      case 'date_created' :
        $value = $player->date_created > 0 ? "<abbr title='$player->date_created'>".date('Y/m/d',strtotime($player->date_created))."</abbr>" : false;
        break;
      case 'player_name' :        
        $value = "<a href='#' class='fv-player-edit' data-player_id='{$id}'>".$player->player_name."</a>";
        $value .= "<div class='row-actions'>";
        $value .= "<a href='#' class='fv-player-edit' data-player_id='{$id}'>Edit</a> | ";
        $value .= "<a href='#' class='fv-player-export' data-player_id='{$id}' data-nonce='".wp_create_nonce('fv-player-db-export-'.$id)."'>Export</a><span> | ";
        $value .= "<a href='#' class='fv-player-clone' data-player_id='{$id}' data-nonce='".wp_create_nonce('fv-player-db-export-'.$id)."'>Clone</a><span> | ";
        $value .= "<span class='trash'><a href='#' class='fv-player-remove' data-player_id='{$id}' data-nonce='".wp_create_nonce('fv-player-db-remove-'.$id)."'>Delete</a></span>";
        $value .= "</div>";
        break;
      case 'embeds':        
        $player = new FV_Player_Db_Player($id);
        $value = '';
        if( $player->getIsValid() ) {
          if( $posts = $player->getMetaValue('post_id') ) {
            foreach( $posts AS $post ) {
              $value .= '<li><a href="'.get_permalink($post).'" target="_blank">'.get_the_title($post).'</a></li>';
            }
          }
        }
        
        if( $value ) $value = '<ul>'.$value.'</ul>';
        
        break;        
      case 'shortcode':        
        $value = '<input type="text" class="fv-player-shortcode-input" readonly value="'.esc_attr('[fvplayer id="'. $id .'"]').'" />';
        break;
      case 'shortcode-copy':        
        $value = '<a href="#" class="button fv-player-shortcode-copy">Copy</a>';
        break;
			default:
				$value = isset($player->$column_name) && $player->$column_name ? $player->$column_name : '';
				break;

		}
		
		return $value;
	}

	public function get_bulk_actions() { // todo: any bulk action?
		return array();
	}

	public function process_bulk_action() {  // todo: any bulk action?
    return;
	}
  
	public function get_result_counts() {
      $this->total_items = FV_Player_Db_Player::getTotalPlayersCount();
	}

	public function get_data() {
	  $current = !empty($_GET['paged']) ? intval($_GET['paged']) : 1;
    $order = !empty($_GET['order']) ? esc_sql($_GET['order']) : 'desc';
    $order_by = !empty($_GET['orderby']) ? esc_sql($_GET['orderby']) : 'id';
    $single_id = !empty($_GET['id']) ? esc_sql($_GET['id']) : null;
    $search = !empty($_GET['s']) ? esc_sql($_GET['s']) : null;

	  $per_page = $this->per_page;
	  $offset = ( $current - 1 ) * $per_page;
    return FV_Player_Db::getListPageData($order_by, $order, $offset, $per_page, $single_id, $search);
	}
	
	public function prepare_items() {

		wp_reset_vars( array( 'action', 'payment', 'orderby', 'order', 's' ) );

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();
		$data     = $this->get_data();

		// re-count number of players to show when searching
		if (isset($_GET['s']) && $_GET['s']) {
          $this->get_result_counts();
        }

		$status   = isset( $_GET['status'] ) ? $_GET['status'] : 'all';

		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		$this->items = $data;

		$this->set_pagination_args( array(
				'total_items' => $this->total_items,
				'per_page'    => $this->per_page,
				'total_pages' => ceil( $this->total_items / $this->per_page ),
			)
		);
	}
	
}
