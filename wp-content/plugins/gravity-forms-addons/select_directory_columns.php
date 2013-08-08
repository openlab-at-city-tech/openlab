<?php

//For backwards compatibility, load wordpress if it hasn't been loaded yet
//Will be used if this file is being called directly
if(!class_exists("RGForms")){
    for ( $i = 0; $i < $depth = 10; $i++ ) {
        $wp_root_path = str_repeat( '../', $i );

        if ( file_exists("{$wp_root_path}wp-load.php" ) ) {
            require_once("{$wp_root_path}wp-load.php");
            require_once("{$wp_root_path}wp-admin/includes/admin.php");
            break;
        }
    }

    auth_redirect();
}
class GFDirectorySelectColumns{

	public function __construct() {
		self::select_columns_page();
	}


	public static function select_columns_page(){

	$form_id = $_GET["id"];
	if(empty($form_id)){
		echo __("Oops! We could not locate your form. Please try again.", "gravity-forms-addons");
		exit;
	}

	//reading form metadata
	$form = RGFormsModel::get_form_meta($form_id);

	?>
	<html>
		<head>
			<?php
			wp_print_styles(array("wp-admin", "colors-fresh"));
			wp_print_scripts(array("jquery", "sack", "jquery-ui-sortable"));
			?>
			<style type="text/css">
				body {
					font-family:"Lucida Grande",Verdana,Arial,sans-serif;
				}
				#wrapper { padding: 10px;}
				#sortable_available, #sortable_selected {
					list-style-type: none; margin: 0; padding: 2px;
					min-height:250px;
					border:1px solid #eaeaea;
					-moz-border-radius:4px; -webkit-border-radius:4px; -khtml-border-radius:4px; border-radius:4px;
					background-color:#FFF;
				}
				#sortable_available li, #sortable_selected li { margin: 0 2px 2px 2px; padding:2px; width: 96%; border:1px solid white; cursor:pointer; font-size: 13px;}
				.field_hover { border: 1px dashed #2175A9!important;}
				.placeholder{background-color: #FFF0A5; height:20px;}
				.gcolumn_wrapper { overflow:auto; height:80%; }
				.gcolumn_container_left, .gcolumn_container_right {width:46%;}
				.gcolumn_container_left {float:left;}
				.gcolumn_container_right {float:right;}
				.gform_select_column_heading{font-weight:bold; padding-bottom:7px; font-size:13px;}
				.column-arrow-mid {
					float:left;
					width:7%;
					min-width: 27px;
					height:100%;
					background: url(<?php echo plugins_url('images/arrow-rightleft.jpg', __FILE__); ?>) repeat-y top center;
					/*background-attachment: fixed;*/
					margin-top:26px;
				}
				.panel-instructions {
					border-bottom: 1px solid #dfdfdf;
					color: #555;
					font-size:12px;
					margin-bottom:6px;
				}
				.panel-instructions p {
					padding: 0 0 5px 0;
					margin: 0;
				}
				.clear { clear: both; display: block;}
				div.panel-buttons {margin-top:8px}
				div.panel-buttons {*margin-top:0px} /* ie specific */
				code { font-size: 12px;}
			</style>

			<script type="text/javascript">
				function equalHeights(makeTheseEqual) {
			        var currentTallest = 0;
			        var $makeTheseEqual = jQuery(makeTheseEqual);
			        $makeTheseEqual.each(function(i){
			            if (jQuery(this).height() > currentTallest) { currentTallest = jQuery(this).height(); }
			        });
			        // for ie6, set height since min-height isn't supported
			        if (jQuery.browser.msie && jQuery.browser.version === 6.0) {
			            $makeTheseEqual.css({'height': currentTallest});
			        }
			        $makeTheseEqual.css({'min-height': currentTallest});
			    }

				jQuery(document).ready(function($) {

					$("#sortable_available, #sortable_selected").sortable({connectWith: '.sortable_connected', placeholder: 'placeholder'});

					equalHeights('.gcolumn_wrapper ul, .column-arrow-mid');

					$(".sortable_connected li").hover(
						function(){
							$(this).addClass("field_hover");
						},
						function(){
							$(this).removeClass("field_hover");
						}
					);

				});

				var columns = new Array();

				function SelectColumns(){
					columns = [];
					jQuery("#sortable_selected li").each(function(){
						columns.push(this.id);
					});
					ChangeColumns(columns);
				}

				function ChangeColumns(columns){
					var json_columns = JSON.stringify(columns);
					UpdateColumns(jQuery("body").data('formid'), json_columns);
	            }

	            function UpdateColumns(form_id, columns) {
	            	var mysack = new sack("<?php echo admin_url("admin-ajax.php")?>" );
	                mysack.execute = 1;
	                mysack.method = 'POST';
	                mysack.setVar( "action", "change_directory_columns" );
	                mysack.setVar( "gforms_directory_columns", "<?php echo wp_create_nonce("gforms_directory_columns") ?>" );
	                mysack.setVar( "form_id", form_id);
	                mysack.setVar( "directory_columns", columns);
	                mysack.onCompletion = function() { self.parent.tb_remove(); };
	                mysack.onError = function() { alert('<?php echo esc_js(__("Ajax error while setting lead property", "gravity-forms-addons")) ?>' )};
	                mysack.runAJAX();

	                return true;
	            }
			</script>

		</head>
		<body data-formid="<?php echo $form_id; ?>">
			<div id="wrapper">
			<?php
			$columns = GFDirectory::get_grid_columns($form_id);
			$field_ids = array_keys($columns);
			#$form = RGFormsModel::get_form_meta($form_id);
			array_push($form["fields"],array("id" => "id" , "label" => __("Entry Id", "gravity-forms-addons")));
			array_push($form["fields"],array("id" => "date_created" , "label" => __("Entry Date", "gravity-forms-addons")));
			array_push($form["fields"],array("id" => "ip" , "label" => __("User IP", "gravity-forms-addons")));
			array_push($form["fields"],array("id" => "source_url" , "label" => __("Source Url", "gravity-forms-addons")));
			array_push($form["fields"],array("id" => "payment_status" , "label" => __("Payment Status", "gravity-forms-addons")));
			array_push($form["fields"],array("id" => "transaction_id" , "label" => __("Transaction Id", "gravity-forms-addons")));
			array_push($form["fields"],array("id" => "payment_amount" , "label" => __("Payment Amount", "gravity-forms-addons")));
			array_push($form["fields"],array("id" => "payment_date" , "label" => __("Payment Date", "gravity-forms-addons")));
			array_push($form["fields"],array("id" => "created_by" , "label" => __("User", "gravity-forms-addons")));

			$form = self::get_selectable_entry_meta($form);
			?>
			<div class="panel-instructions">
				<p><?php _e("Drag &amp; drop to order and select which columns are displayed in the Gravity Forms Directory.", "gravity-forms-addons") ?></p>
				<p><?php echo sprintf(__("Embed the Directory on a post or a page using %s ", "gravity-forms-addons"), '<code>[directory form="'.$_GET['id'].'"]</code>'); ?></p>
			</div>
			<div class="clear"></div>
			<div class="gcolumn_wrapper">
				<div class="gcolumn_container_left">
					<div class="gform_select_column_heading"><?php _e("Visible Columns", "gravity-forms-addons"); ?></div>
					<ul id="sortable_selected" class="sortable_connected">
						<?php
						foreach($columns as $field_id => $field_info){
							?>
							<li id="<?php echo $field_id?>"><?php echo esc_html($field_info["label"]) ?></li>
							<?php
						}
						?>
					</ul>
				</div>

				<div class="column-arrow-mid"></div>

				<div class="gcolumn_container_right" id="available_column">
					<div class="gform_select_column_heading"> <?php _e("Hidden Columns", "gravity-forms-addons"); ?></div>
					<ul id="sortable_available" class="sortable_connected">
						<?php

						$approvedcolumn = GFDirectory::get_approved_column($form);
						foreach($form["fields"] as $field){
							if(
							   in_array(RGFormsModel::get_input_type($field), array("checkbox", 'address', 'radio', 'name')) &&
							   !in_array($field["id"], $field_ids)
							   && floatval($field['id']) !== floor(floatval($approvedcolumn))
							){
								?>
								<li id="<?php echo $field["id"]?>"><?php echo esc_html(rgar($field,"label")) ?></li>
								<?php
							}

							if(
							   	is_array(rgar($field, "inputs"))
							  ){
								foreach($field["inputs"] as $input){
									if(!in_array($input["id"], $field_ids) && !($field["type"] == "creditcard" && in_array($input["id"], array(floatval("{$field["id"]}.2"), floatval("{$field["id"]}.3")))) ){
									?>
										<li id="<?php echo $input["id"]?>"><?php echo esc_html(GFDirectory::get_label($field, $input["id"])) ?> <span class="description">(<?php echo esc_html(rgar($field,"label")) ?>)</span></li>
										<?php
									}
								}
							}
							else if(!rgar($field, "displayOnly") && !in_array($field["id"], $field_ids)){
								?>
								<li id="<?php echo $field["id"]?>"><?php echo  esc_html($field["label"]) ?></li>
								<?php
							}
						}
						?>
					</ul>
				</div>
			</div>

			<div class="panel-buttons">
				<input type="button" value="  <?php _e("Save", "gravity-forms-addons"); ?>  " class="button-primary" onclick="SelectColumns();"/>&nbsp;
				<input type="button" value="<?php _e("Cancel", "gravity-forms-addons"); ?>" class="button" onclick="self.parent.tb_remove();"/>
			</div>
		</div>
		</body>
	</html>

	<?php

	}

	public static function get_selectable_entry_meta($form){
		$entry_meta = GFFormsModel::get_entry_meta($form["id"]);
		$keys = array_keys($entry_meta);
		foreach ($keys as $key){
			array_push($form["fields"],array("id" => $key , "label" => $entry_meta[$key]['label']));
		}
		return $form;
    }

}

$SelectColumns = new GFDirectorySelectColumns();

function rg_has_field_id($id, $field_ids){
	foreach($field_ids as $field_id){
		if(is_numeric($id) && is_numeric($field_id) && intval($id) == intval($field_id))
			return true;
		if($id == $field_id)
			return true;

	}
	return false;
}