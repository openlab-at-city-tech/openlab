<?php

class C_NextGen_Product_Installer
{
    function _filter_modules($pope_modules_list, $product)
    {
        foreach ($product as $module_name) {
            $module = C_Component_Registry::get_instance()->get_module($module_name);
            $str = $module->module_id . '|' . $module->module_version;
            $search = array_search($str, $pope_modules_list);
            if (FALSE !== $search)
                unset($pope_modules_list[$search]);
        }
        return $pope_modules_list;
    }

	function get_modules_to_load_for($product_id)
	{
		$modules = array();

		$obj = C_Component_Registry::get_instance()->get_product($product_id);
		try {
			$klass = new ReflectionClass( $obj );
			if ( $klass->hasMethod( 'get_modules_to_load' ) ) {
				$modules = $obj->get_modules_to_load();
			} elseif ( $klass->hasProperty( 'modules' ) ) {
				$modules = $klass->getStaticPropertyValue( 'modules' );
			}

			if ( ! $modules && $klass->hasMethod( 'define_modules' ) ) {
				$modules = $obj->define_modules();
				if ( $klass->hasProperty( 'modules' ) ) {
					$modules = $klass->getStaticPropertyValue( 'modules' );
				}
			}
		}
		catch (ReflectionException $ex) {
			// Oh oh...
		}

		return $modules;
	}

	function uninstall($hard)
	{
        // remove this products modules from the pope_module_list registry
		$registry = C_Component_Registry::get_instance();
		$nextgen_product = $registry->get_product('photocrati-nextgen');
        $pope_modules_list = get_option('pope_module_list', array());
        $pope_modules_list = $this->_filter_modules($pope_modules_list, $nextgen_product->get_modules_to_load());

        // run each modules respective uninstall routines
		foreach ($nextgen_product->get_modules_to_load() as $module_name) {
			if (($handler = C_Photocrati_Installer::get_handler_instance($module_name))) {
                if (method_exists($handler, 'uninstall')) $handler->uninstall($hard);
			}
        }

        // lastly remove this product itself from the pope_module_list registry
        $search = array_search('photocrati-nextgen|' . NGG_PLUGIN_VERSION, $pope_modules_list);
        if (FALSE !== $search)
            unset($pope_modules_list[$search]);

        // TODO: remove this. NextGen Pro's uninstall routine will be updated in a separate release,
        // so to maintain proper support we run the same routine as above for it
        $pro_version = FALSE;
        if (defined('NGG_PRO_PLUGIN_VERSION'))
            $pro_version = 'NGG_PRO_PLUGIN_VERSION';
        if (defined('NEXTGEN_GALLERY_PRO_VERSION'))
            $pro_version = 'NEXTGEN_GALLERY_PRO_VERSION';
        if (FALSE !== $pro_version)
            $pro_version = constant($pro_version);

        if (FALSE !== $pro_version)
        {
	        $pope_modules_list = $this->_filter_modules($pope_modules_list, $this->get_modules_to_load_for('photocrati-nextgen-pro'));
            $search = array_search('photocrati-nextgen-pro|' . $pro_version, $pope_modules_list);
            if (FALSE !== $search)
                unset($pope_modules_list[$search]);
        }

        // TODO: remove this also. NextGen Plus should also be updated in a separate release
        $plus_version = FALSE;
        if (defined('NGG_PLUS_PLUGIN_VERSION'))
            $plus_version = 'NGG_PLUS_PLUGIN_VERSION';
        if (FALSE !== $plus_version)
            $plus_version = constant($plus_version);

        if (FALSE !== $plus_version)
        {
            $pope_modules_list = $this->_filter_modules($pope_modules_list, $this->get_modules_to_load_for('photocrati-nextgen-plus'));
            $search = array_search('photocrati-nextgen-plus|' . $plus_version, $pope_modules_list);
            if (FALSE !== $search)
                unset($pope_modules_list[$search]);
        }

        if (empty($pope_modules_list))
            delete_option('pope_module_list');
        else
            update_option('pope_module_list', $pope_modules_list);

	}
}
