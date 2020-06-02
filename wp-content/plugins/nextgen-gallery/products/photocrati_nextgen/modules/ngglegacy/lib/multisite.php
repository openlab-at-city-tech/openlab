<?php
/**
* Main PHP Class for Multisite setup
* 
* @author Alex Rabe 
* 
* 
*/
class nggWPMU{

	/**
	 * Check the Quota under WPMU. Only needed for this case
	 * 
	 * @class nggWPMU
	 * @return bool $result
	 */
	function check_quota() {
        	if ( get_site_option( 'upload_space_check_disabled' ) )
        		return false;

			if ( (is_multisite()) && nggWPMU::wpmu_enable_function('wpmuQuotaCheck'))
				if( $error = upload_is_user_over_quota( false ) ) {
					nggGallery::show_error( __( 'Sorry, you have used your space allocation. Please delete some files to upload more files.','nggallery' ) );
					return true;
				}
			return false;
	}
    
    /**
     * Check for site admin
     * 
     * @return bool
     */
    function wpmu_site_admin() {
    	if (function_exists('is_super_admin'))
        {
            if (is_super_admin())
            {
                return true;
            }
        }
    			
    	return false;
    }

    /**
     * Check for site wide options
     * 
     * @param string $value
     * @return string|bool
     */
    function wpmu_enable_function($value) {
    	if (is_multisite()) {
    		$ngg_options = get_site_option('ngg_options');
    		return $ngg_options[$value];
    	}
    	// if this is not WPMU, enable it !
    	return true;
    }    
}
