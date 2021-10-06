<?php
// helps the WooCommerce bridge integration. Probably at some moment Woo bridge will be fully integrated here
class WatuPROWoo {
	// get WooCommerce products
	static function get_products() {
		$woo_products = array();
		// WooCommerce integration?
		if(watupro_intel() and class_exists('woocommerce') and function_exists('ww_bridge_init')) {
			// find all virtual and downloadable products
			$args =  array(
			    'post_type'      => array('product'),
			    'post_status'    => 'publish',
			    'posts_per_page' => -1,
			    'meta_query'     => array( 
			        array(
			            'key' => '_virtual',
			            'value' => 'yes',
			            'compare' => '=',  
			        ),
			        array(
			            'key' => '_downloadable',
			            'value' => 'yes',
			            'compare' => '=',  
			        )  
			    ),
			);
			$query = new WP_Query( $args );
			
			$woo_products = $query->posts;
		}
		
		return $woo_products;			
	} // end woo_products()
	
	// updates the watupro and watupro-redirect attributes of the associated WooCommerce product
	static function update_woo($quiz_id, $product_id, $old_woo_product_id) {
		global $wpdb;
		$quiz_id = intval($quiz_id);
		
		$atts = get_post_meta($product_id, '_product_attributes', true);
		if(empty($atts)) $atts = array();
		
		// if there is no $product_id we have to check if there was one so to remove the attributes
		if(empty($product_id) and !empty($old_woo_product_id)) {			
			foreach($atts as $cnt => $att) {
				if($att['name'] == 'watupro' or $att['name'] == 'watupro_redirect') unset($atts[$cnt]);
			}			
			
			update_post_meta($old_woo_product_id, '_product_attributes', $atts);
			return false;
		}
		
		// figure out the quiz URL
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $quiz_id));
		
		if($quiz->published_odd and $quiz->published_odd_url) $quiz_url = $quiz->published_odd_url;
		else {
			$post_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE (post_content LIKE '%[watupro ".$quiz_id." %' OR post_content LIKE '%[watupro ".$quiz_id."]%')");
			if(!empty($post_id)) $quiz_url = get_permalink($post_id);
		}
		
		$name_found = $redirect_found = false;
		foreach($atts as $cnt => $att) {
			if($att['name'] == 'watupro') {
				$atts[$cnt]['value'] = $quiz_id;
				$name_found = true;
			}
			if(!empty($quiz_url) and $att['name'] == 'watupro-redirect') {
				$atts[$cnt]['value'] = $quiz_url;
				$redirect_found = true;
			}
		} // end foreach $atts
		
		if(!$name_found) $atts[] = array('name' => 'watupro', 'value' => $quiz_id);
		if(!$redirect_found and !empty($quiz_url)) $atts[] = array('name' => 'watupro-redirect', 'value' => $quiz_url);
		
		update_post_meta($product_id, '_product_attributes', $atts);
	} // end update_woo()
}