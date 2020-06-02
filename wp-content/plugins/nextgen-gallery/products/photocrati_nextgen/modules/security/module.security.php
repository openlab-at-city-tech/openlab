<?php
class M_Security extends C_Base_Module
{
	function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
	{
		parent::define(
			'photocrati-security',
			'Security',
			'Provides utilities to check for credentials and security',
			'3.1.8',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	static function get_mapped_cap($capability_name) 
	{
		switch ($capability_name)
		{
			case 'nextgen_edit_settings':
			{
				$capability_name = 'NextGEN Change options';
				
				break;
			}
			case 'nextgen_edit_style':
			{
				$capability_name = 'NextGEN Change style';
				
				break;
			}
			case 'nextgen_edit_display_settings':
			{
				$capability_name = 'NextGEN Change options';
				
				break;
			}
			case 'nextgen_edit_displayed_gallery':
			{
				$capability_name = 'NextGEN Attach Interface';
				
				break;
			}
			case 'nextgen_edit_gallery':
			{
				$capability_name = 'NextGEN Manage gallery';
				
				break;
			}
			case 'nextgen_edit_gallery_unowned':
			{
				$capability_name = 'NextGEN Manage others gallery';
				
				break;
			}
			case 'nextgen_upload_image':
			case 'nextgen_upload_images':
			{
				$capability_name = 'NextGEN Upload images';
				
				break;
			}
			case 'nextgen_edit_album_settings':
			{
				$capability_name = 'NextGEN Edit album settings';

				break;
			}

			case 'nextgen_edit_album':
			{
				$capability_name = 'NextGEN Edit album';

				break;
			}
		}

		return $capability_name;
	}

	// TODO: Remove this function once Photocrati Pro 5.0.6 is out of circulation. 
	// Its the only thing which uses the security manager any more
	function _register_utilities()
	{
		$this->get_registry()->add_utility('I_Security_Manager', 'C_WordPress_Security_Manager');
	}

	// TODO: Remove this function once Photocrati Pro 5.0.6 is out of circulation. 
	// Its the only thing which uses the security manager any more
	function _register_adapters()
	{
		$this->get_registry()->add_adapter('I_Component_Factory', 'A_Security_Factory');
	}

	static function create_nonce($cap=-1)
	{
		return wp_create_nonce(self::get_mapped_cap($cap));
	}

	static function verify_nonce($nonce, $cap=-1)
	{
		return wp_verify_nonce($nonce, self::get_mapped_cap($cap));
	}

	static function is_allowed($capability_name, $user=FALSE)
	{
		$capability_name = self::get_mapped_cap($capability_name);

		if (!$user) {
			if (function_exists('wp_get_current_user')) $user = wp_get_current_user();
		} else if (is_numeric($user)) {
			$user = WP_User($user);
		}

		return $user && $user->has_cap($capability_name);
	}



    function get_type_list()
    {
        return array(
            'A_Security_Factory' => 'adapter.security_factory.php',
            'C_Security_Actor' => 'class.security_actor.php',
            'C_Security_Manager' => 'class.security_manager.php',
            'C_Security_Token' => 'class.security_token.php',
            'C_Wordpress_Security_Actor' => 'class.wordpress_security_actor.php',
            'C_Wordpress_Security_Manager' => 'class.wordpress_security_manager.php',
            'C_Wordpress_Security_Token' => 'class.wordpress_security_token.php'
        );
    }

}

new M_Security();
