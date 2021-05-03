<?php

class core_pdf_embedder {
	
	protected function useminified() {
		return !defined( 'SCRIPT_DEBUG' ) || !SCRIPT_DEBUG;
	}
	
	protected function __construct() {
		$this->add_actions();
		register_activation_hook($this->my_plugin_basename(), array( $this, 'pdfemb_activation_hook' ) );
	}
	
	// May be overridden in basic or premium
	public function pdfemb_activation_hook($network_wide) {
	}
	
	public function pdfemb_wp_enqueue_scripts() {
	}
	
	protected $inserted_scripts = false;
	protected function insert_scripts() {
		if (!$this->inserted_scripts) {
			$this->inserted_scripts = true;
			wp_enqueue_script( 'pdfemb_embed_pdf_js' );
			
			wp_enqueue_script( 'pdfemb_pdf_js' );
			
			wp_enqueue_style( 'pdfemb_embed_pdf_css', $this->my_plugin_url().'css/pdfemb-embed-pdf-'.$this->PLUGIN_VERSION.'.css', array(), $this->PLUGIN_VERSION );
		}
	}
	
	protected function get_translation_array() {
		$options = $this->get_option_pdfemb();
		return Array('worker_src' => $this->my_plugin_url().'js/pdfjs/pdf-'.$this->PLUGIN_VERSION.'.worker'.($this->useminified() ? '.min' : '').'.js',
		        'cmap_url' => $this->my_plugin_url().'js/pdfjs/cmaps/',
		        'poweredby'=>$options['poweredby'],
            'objectL10n' => array(
                'loading' => esc_html__('Loading...', 'pdf-embedder'),
                'page' => esc_html__('Page', 'pdf-embedder'),
                'zoom' => esc_html__('Zoom', 'pdf-embedder'),
                'prev' => esc_html__('Previous page', 'pdf-embedder'),
                'next' => esc_html__('Next page', 'pdf-embedder'),
                'zoomin' => esc_html__('Zoom In', 'pdf-embedder'),
                'zoomout' => esc_html__('Zoom Out', 'pdf-embedder'),
                'secure' => esc_html__('Secure', 'pdf-embedder'),
                'download' => esc_html__('Download PDF', 'pdf-embedder'),
                'fullscreen' => esc_html__('Full Screen', 'pdf-embedder'),
                'domainerror' => esc_html__('Error: URL to the PDF file must be on exactly the same domain as the current web page.', 'pdf-embedder'),
                'clickhereinfo' => esc_html__('Click here for more info', 'pdf-embedder'),
                'widthheightinvalid' => esc_html__("PDF page width or height are invalid", 'pdf-embedder'),
                'viewinfullscreen' => esc_html__("View in Full Screen", 'pdf-embedder')
            ));
	}
	
	protected function get_extra_js_name() {
		return '';
	}
		
	
	// SHORTCODES
	
	// Take over PDF type in media gallery
	public function pdfemb_upload_mimes($existing_mimes = array()) {
		$existing_mimes['pdf'] = 'application/pdf';
		return $existing_mimes;
	}
	
	public function pdfemb_post_mime_types($post_mime_types) {
		$post_mime_types['application/pdf'] = array( __( 'PDFs' , 'pdf-embedder'), __( 'Manage PDFs' , 'pdf-embedder'), _n_noop( 'PDF <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>' , 'pdf-embedder') );
		return $post_mime_types;
	}
	
	// Embed PDF shortcode instead of link
	public function pdfemb_media_send_to_editor($html, $id, $attachment) {
		$pdf_url = '';
		$title = '';
		if (isset($attachment['url']) && preg_match( "/\.pdf$/i", $attachment['url'])) {
			$pdf_url = $attachment['url'];
			$title = isset($attachment['post_title']) ? $attachment['post_title'] : '';
		}
		elseif ($id > 0) {
			$post = get_post($id);
			if ($post && isset($post->post_mime_type) && $post->post_mime_type == 'application/pdf') {
				$pdf_url = wp_get_attachment_url($id);
				$title = get_the_title($id);
			}
		}

		if ($pdf_url != '') {
			if ($title != '') {
				$title_from_url = $this->make_title_from_url($pdf_url);
				if ($title == $title_from_url || $this->make_title_from_url('/'.$title) == $title_from_url) {
					// This would be the default title anyway based on URL
					// OR if you take .pdf off title it would match, so that's close enough - don't load up shortcode with title param
					$title = '';
				}
				else {
					$title = ' title="' . esc_attr( $title ) . '"';
				}
			}

			return apply_filters('pdfemb_override_send_to_editor', '[pdf-embedder url="' . $pdf_url . '"'.$title.']', $html, $id, $attachment);
		} else {
			return $html;
		}
	}
	
	protected function modify_pdfurl($url) {
		return set_url_scheme($url);
	}

	public function pdfemb_shortcode_display_pdf($atts, $content=null) {
		$atts = apply_filters('pdfemb_filter_shortcode_attrs', $atts);

		if (!isset($atts['url'])) {
			return '<b>PDF Embedder requires a url attribute</b>';
		}
		$url = $atts['url'];
		
		$this->insert_scripts();

        // Get defaults

        $options = $this->get_option_pdfemb();
		
		$width = isset($atts['width']) ? $atts['width'] : $options['pdfemb_width'];
		$height = isset($atts['height']) ? $atts['height'] : $options['pdfemb_height'];
		
		$extra_style = "";
		if (is_numeric($width)) {
			$extra_style .= "width: ".$width."px; ";
		}
        elseif ($width!='max' && $width!='auto') {
            $width = 'max';
        }

		if (is_numeric($height)) {
			$extra_style .= "height: ".$height."px; ";
		}
		elseif ($height!='max' && $height!='auto') {
			$height = 'max';
		}
		
		$toolbar = isset($atts['toolbar']) && in_array($atts['toolbar'], array('top', 'bottom', 'both', 'none')) ? $atts['toolbar'] : $options['pdfemb_toolbar'];
        if (!in_array($toolbar, array('top', 'bottom', 'both', 'none'))) {
            $toolbar = 'bottom';
        }
		
		$toolbar_fixed = isset($atts['toolbarfixed']) ? $atts['toolbarfixed'] : $options['pdfemb_toolbarfixed'];
        if (!in_array($toolbar_fixed, array('on', 'off'))) {
            $toolbar_fixed = 'off';
        }

		$title = isset($atts['title']) && $atts['title'] != '' ? $atts['title'] : $this->make_title_from_url($url);

		$pdfurl = $this->modify_pdfurl($url);
		$esc_pdfurl = esc_attr($pdfurl);

		$returnhtml = '<a href="'.$esc_pdfurl.'" class="pdfemb-viewer" style="'.esc_attr($extra_style).'" '
						.'data-width="'.esc_attr($width).'" data-height="'.esc_attr($height).'" ';
		
		$returnhtml .= $this->extra_shortcode_attrs($atts, $content);
						
		$returnhtml .= ' data-toolbar="'.$toolbar.'" data-toolbar-fixed="'.$toolbar_fixed.'">'.esc_html( $title ).'<br/></a>';
		
		if (!is_null($content)) {
			$returnhtml .= do_shortcode($content);
		}
		return $returnhtml;
	}

	protected function make_title_from_url($url) {
		if (preg_match( '|/([^/]+?)(\.pdf(\?[^/]*)?)?$|i', $url, $matches)) {
			return $matches[1];
		}
		return $url;
	}
	
	protected function extra_shortcode_attrs($atts, $content=null) {
		return '';
	}
	
	// ADMIN OPTIONS
	// *************
	
	protected function get_options_menuname() {
		return 'pdfemb_list_options';
	}
	
	protected function get_options_pagename() {
		return 'pdfemb_options';
	}

	protected function is_multisite_and_network_activated() {
	    if (!is_multisite()) {
	        return false;
        }

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
        return is_plugin_active_for_network($this->my_plugin_basename());
    }
	
	protected function get_settings_url() {
		return $this->is_multisite_and_network_activated()
		? network_admin_url( 'settings.php?page='.$this->get_options_menuname() )
		: admin_url( 'options-general.php?page='.$this->get_options_menuname() );
	}
	
	public function pdfemb_admin_menu() {
		if ($this->is_multisite_and_network_activated()) {
			add_submenu_page( 'settings.php', __('PDF Embedder settings', 'pdf-embedder'), __('PDF Embedder', 'pdf-embedder'),
			'manage_network_options', $this->get_options_menuname(),
			array($this, 'pdfemb_options_do_page'));
		}
		else {
			add_options_page( __('PDF Embedder settings', 'pdf-embedder'), __('PDF Embedder', 'pdf-embedder'),
			'manage_options', $this->get_options_menuname(),
			array($this, 'pdfemb_options_do_page'));
		}
	}
	
	public function pdfemb_options_do_page() {

        wp_enqueue_script( 'pdfemb_admin_js', $this->my_plugin_url().'js/admin/pdfemb-admin-'.$this->PLUGIN_VERSION.'.js', array('jquery'), $this->PLUGIN_VERSION );
        wp_enqueue_style( 'pdfemb_admin_css', $this->my_plugin_url().'css/pdfemb-admin-'.$this->PLUGIN_VERSION.'.css', array(), $this->PLUGIN_VERSION  );

        $submit_page = $this->is_multisite_and_network_activated() ? 'edit.php?action='.$this->get_options_menuname() : 'options.php';
	
		if ($this->is_multisite_and_network_activated()) {
			$this->pdfemb_options_do_network_errors();
		}
		?>
			  
		<div>
		
    		<h2><?php esc_html_e('PDF Embedder setup', 'pdf-embedder'); ?></h2>

            <p><?php esc_html_e('To use the plugin, just embed PDFs in the same way as you would normally embed images in your posts/pages - but try with a PDF file instead.', 'pdf-embedder'); ?></p>
            <p><?php esc_html_e("From the post editor, click Add Media, and then drag-and-drop your PDF file into the media library.
                When you insert the PDF into your post, it will automatically embed using the plugin's viewer.", 'pdf-embedder'); ?></p>


            <div id="pdfemb-tablewrapper">

            <div id="pdfemb-tableleft" class="pdfemb-tablecell">

                <h2 id="pdfemb-tabs" class="nav-tab-wrapper">
                    <a href="#main" id="main-tab" class="nav-tab nav-tab-active"><?php esc_html_e('Main Settings', 'pdf-embedder'); ?></a>
                    <a href="#mobile" id="mobile-tab" class="nav-tab"><?php esc_html_e('Mobile', 'pdf-embedder'); ?></a>
                    <a href="#secure" id="secure-tab" class="nav-tab"><?php esc_html_e('Secure', 'pdf-embedder'); ?></a>
                    <?php $this->draw_more_tabs(); ?>
                </h2>

                <form action="<?php echo $submit_page; ?>" method="post" id="pdfemb_form" enctype="multipart/form-data" >

        <?php

        echo '<div id="main-section" class="pdfembtab active">';
        $this->pdfemb_mainsection_text();
        echo '</div>';

        echo '<div id="mobile-section" class="pdfembtab">';
        $this->pdfemb_mobilesection_text();
        echo '</div>';

        echo '<div id="secure-section" class="pdfembtab">';
        $this->pdfemb_securesection_text();
        echo '</div>';

        $this->draw_extra_sections();

        settings_fields($this->get_options_pagename());
		
		?>

                    <p class="submit">
                        <input type="submit" value="<?php esc_html_e('Save Changes', 'pdf-embedder'); ?>" class="button button-primary" id="submit" name="submit">
                    </p>
				
                </form>
            </div>

            <?php $this->options_do_sidebar(); ?>

        </div>
		
		</div>  <?php
	}

    protected function options_do_sidebar() {
    }

    protected function draw_more_tabs() {
    }

    protected function draw_extra_sections() {
    }

    // Override elsewhere
	protected function pdfemb_mainsection_text() {
        $options = $this->get_option_pdfemb();
		?>


        <h2><?php _e('Default Viewer Settings', 'pdf-embedder'); ?></h2>

        <label for="input_pdfemb_width" class="textinput"><?php _e('Width', 'pdf-embedder'); ?></label>
        <input id='input_pdfemb_width' class='textinput' name='<?php echo $this->get_options_name(); ?>[pdfemb_width]' size='10' type='text' value='<?php echo esc_attr($options['pdfemb_width']); ?>' />
		<br class="clear"/>

        <label for="input_pdfemb_height" class="textinput"><?php _e('Height', 'pdf-embedder'); ?></label>
        <input id='input_pdfemb_height' class='textinput' name='<?php echo $this->get_options_name(); ?>[pdfemb_height]' size='10' type='text' value='<?php echo esc_attr($options['pdfemb_height']); ?>' />
        <br class="clear"/>

        <p class="desc big"><i><?php _e('Enter <b>max</b> or an integer number of pixels', 'pdf-embedder'); ?></i></p>

        <br class="clear"/>

        <label for="pdfemb_toolbar" class="textinput"><?php esc_html_e('Toolbar Location', 'pdf-embedder'); ?></label>
        <select name='<?php echo $this->get_options_name(); ?>[pdfemb_toolbar]' id='pdfemb_toolbar' class='select'>
            <option value="top" <?php echo $options['pdfemb_toolbar'] == 'top' ? 'selected' : ''; ?>><?php esc_html_e('Top', 'pdf-embedder'); ?></option>
            <option value="bottom" <?php echo $options['pdfemb_toolbar'] == 'bottom' ? 'selected' : ''; ?>><?php esc_html_e('Bottom', 'pdf-embedder'); ?></option>
            <option value="both" <?php echo $options['pdfemb_toolbar'] == 'both' ? 'selected' : ''; ?>><?php esc_html_e('Both', 'pdf-embedder'); ?></option>
	        <?php $this->no_toolbar_option($options); ?>
        </select>
        <br class="clear" />
        <br class="clear" />

        <label for="pdfemb_toolbarfixed" class="textinput"><?php esc_html_e('Toolbar Hover', 'pdf-embedder'); ?></label>
        <span>
        <input type="radio" name='<?php echo $this->get_options_name(); ?>[pdfemb_toolbarfixed]' id='pdfemb_toolbarfixed_off' class='radio' value="off" <?php echo $options['pdfemb_toolbarfixed'] == 'off' ? 'checked' : ''; ?> />
        <label for="pdfemb_toolbarfixed_off" class="radio"><?php esc_html_e('Toolbar appears only on hover over document', 'pdf-embedder'); ?></label>
        </span>
        <br/>
        <span>
        <input type="radio" name='<?php echo $this->get_options_name(); ?>[pdfemb_toolbarfixed]' id='pdfemb_toolbarfixed_on' class='radio' value="on" <?php echo $options['pdfemb_toolbarfixed'] == 'on' ? 'checked' : ''; ?> />
        <label for="pdfemb_toolbarfixed_on" class="radio"><?php esc_html_e('Toolbar always visible', 'pdf-embedder'); ?></label>
        </span>
        <br/><br/>
        <label for="pdfemb_toolbarfixed" class="textinput"><?php esc_html_e('Display Credit', 'pdf-embedder'); ?></label>
        <span>
        <input type='checkbox' name='<?php echo $this->get_options_name(); ?>[poweredby]' id='poweredby' class='checkbox' <?php echo $options['poweredby'] == 'on' ? 'checked' : ''; ?>  />

        <label for="poweredby" class="checkbox plain" style="margin-left: 10px;"><?php esc_html_e('Display "Powered by wp-pdf.com" on PDF Viewer with a link to our site. Spread the love!', 'pdf-embedder'); ?></label>
		</span>
		<?php
            $this->pdfemb_mainsection_extra();
        ?>

        <br class="clear" />
        <br class="clear" />



        <p><?php printf( __('You can override these defaults for specific embeds by modifying the shortcodes - see <a href="%s" target="_blank">instructions</a>.', 'pdf-embedder'), $this->get_instructions_url()); ?></p>

        <?php
	}

	protected function no_toolbar_option($options) {
		// Override in commercial
	}

	protected function pdfemb_mainsection_extra() {
        // Override in Basic and Commercial
	}

    protected function get_instructions_url() {
        return 'http://wp-pdf.com/free-instructions/?utm_source=PDF%20Settings%20Main&utm_medium=freemium&utm_campaign=Freemium';
    }

    protected function pdfemb_mobilesection_text() {
    }

    protected function pdfemb_securesection_text()
    {
        ?>

        <h2><?php esc_html_e('Protect your PDFs using PDF Embedder Secure', 'pdf-embedder'); ?></h2>
        <p><?php _e('Our <b>PDF Embedder Premium Secure</b> plugin provides the same simple but elegant viewer for your website visitors, with the added protection that
            it is difficult for users to download or print the original PDF document.', 'pdf-embedder'); ?></p>

        <p><?php esc_html_e('This means that your PDF is unlikely to be shared outside your site where you have no control over who views, prints, or shares it.', 'pdf-embedder'); ?></p>

	    <p><?php esc_html_e("Optionally add a watermark containing the user's name or email address to discourage sharing of screenshots.", 'pdf-embedder'); ?></p>

        <p><?php printf( __('See our website <a href="%s">wp-pdf.com</a> for more details and purchase options.', 'pdf-embedder'), 'http://wp-pdf.com/secure/?utm_source=PDF%20Settings%20Secure&utm_medium=freemium&utm_campaign=Freemium' ); ?>
        </p>

        <?php
    }

	public function pdfemb_options_validate($input) {
		$newinput = Array();

        $newinput['pdfemb_width'] = isset($input['pdfemb_width']) ? trim(strtolower($input['pdfemb_width'])) : 'max';
        if (!is_numeric($newinput['pdfemb_width']) && $newinput['pdfemb_width']!='max' && $newinput['pdfemb_width']!='auto') {
            add_settings_error(
                'pdfemb_width',
                'widtherror',
                self::get_error_string('pdfemb_width|widtherror'),
                'error'
            );
        }

        $newinput['pdfemb_height'] = isset($input['pdfemb_height']) ? trim(strtolower($input['pdfemb_height'])) : 'max';
        if (!is_numeric($newinput['pdfemb_height']) && $newinput['pdfemb_height']!='max' && $newinput['pdfemb_height']!='auto') {
            add_settings_error(
                'pdfemb_height',
                'heighterror',
                self::get_error_string('pdfemb_height|heighterror'),
                'error'
            );
        }

        if (isset($input['pdfemb_toolbar']) && in_array($input['pdfemb_toolbar'], array('top', 'bottom', 'both', 'none'))) {
            $newinput['pdfemb_toolbar'] = $input['pdfemb_toolbar'];
        }
        else {
            $newinput['pdfemb_toolbar'] = 'bottom';
        }

        if (isset($input['pdfemb_toolbarfixed']) && in_array($input['pdfemb_toolbarfixed'], array('on', 'off'))) {
            $newinput['pdfemb_toolbarfixed'] = $input['pdfemb_toolbarfixed'];
        }

        $newinput['pdfemb_version'] = $this->PLUGIN_VERSION;

        if (isset($input['poweredby']) && in_array($input['poweredby'], array('on', 'off'))) {
        	$newinput['poweredby'] = $input['poweredby'];
        }else{
        	$newinput['poweredby'] = 'off';
        }
        
		return $newinput;
	}
	
	protected function get_error_string($fielderror) {
        $local_error_strings = Array(
            'pdfemb_width|widtherror' => __('Width must be "max" or an integer (number of pixels)', 'pdf-embedder'),
            'pdfemb_height|heighterror' => __('Height must be "max" or an integer (number of pixels)', 'pdf-embedder')
        );
        if (isset($local_error_strings[$fielderror])) {
            return $local_error_strings[$fielderror];
        }

		return __('Unspecified error', 'pdf-embedder');
	}

	public function pdfemb_save_network_options() {
		check_admin_referer( $this->get_options_pagename().'-options' );
	
		if (isset($_POST[$this->get_options_name()]) && is_array($_POST[$this->get_options_name()])) {
			$inoptions = $_POST[$this->get_options_name()];
			
			$outoptions = $this->pdfemb_options_validate($inoptions);
			
			$error_code = Array();
			$error_setting = Array();
			foreach (get_settings_errors() as $e) {
				if (is_array($e) && isset($e['code']) && isset($e['setting'])) {
					$error_code[] = $e['code'];
					$error_setting[] = $e['setting'];
				}
			}

			if ($this->is_multisite_and_network_activated()) {
				update_site_option( $this->get_options_name(), $outoptions );
			}
			else {
				update_option( $this->get_options_name(), $outoptions );
            }

			// redirect to settings page in network
			wp_redirect(
			add_query_arg(
			array( 'page' => $this->get_options_menuname(),
			'updated' => true,
			'error_setting' => $error_setting,
			'error_code' => $error_code ),
			network_admin_url( 'admin.php' )
			)
			);
			exit;
		}
	}
	
	protected function pdfemb_options_do_network_errors() {
		if (isset($_REQUEST['updated']) && $_REQUEST['updated']) {
			?>
					<div id="setting-error-settings_updated" class="updated settings-error">
					<p>
					<strong><?php esc_html_e('Settings saved', 'pdf-embedder'); ?></strong>
					</p>
					</div>
				<?php
			}
	
			if (isset($_REQUEST['error_setting']) && is_array($_REQUEST['error_setting'])
				&& isset($_REQUEST['error_code']) && is_array($_REQUEST['error_code'])) {
				$error_code = $_REQUEST['error_code'];
				$error_setting = $_REQUEST['error_setting'];
				if (count($error_code) > 0 && count($error_code) == count($error_setting)) {
					for ($i=0; $i<count($error_code) ; ++$i) {
						?>
					<div id="setting-error-settings_<?php echo $i; ?>" class="error settings-error">
					<p>
					<strong><?php echo htmlentities2($this->get_error_string($error_setting[$i].'|'.$error_code[$i])); ?></strong>
					</p>
					</div>
						<?php
				}
			}
		}
	}
	
	// OPTIONS

    protected function get_options_name() {
        return 'pdfemb';
    }

	protected function get_default_options() {
		return Array(
            'pdfemb_width' => 'max',
            'pdfemb_height' => 'max',
            'pdfemb_toolbar' => 'bottom',
            'pdfemb_toolbarfixed' => 'off',
            'poweredby' => 'off',
            'pdfemb_version' => $this->PLUGIN_VERSION
        );
	}
	
	protected $pdfemb_options = null;
	protected function get_option_pdfemb() {
		if ($this->pdfemb_options != null) {
			return $this->pdfemb_options;
		}

		if ($this->is_multisite_and_network_activated()) {
			$option = get_site_option( $this->get_options_name(), Array() );
		}
		else {
			$option = get_option( $this->get_options_name(), Array() );
        }
	
		$default_options = $this->get_default_options();
		foreach ($default_options as $k => $v) {
			if (!isset($option[$k])) {
				$option[$k] = $v;
			}
		}
	
		$this->pdfemb_options = $option;
		return $this->pdfemb_options;
	}

	protected function save_option_pdfemb($option) {
		if ($this->is_multisite_and_network_activated()) {
			update_site_option( $this->get_options_name(), $option );
		}
		else {
			update_option( $this->get_options_name(), $option );
        }
		$this->pdfemb_options = $option;
	}

	// ADMIN
	
	public function pdfemb_admin_init() {
		// Add PDF as a supported upload type to Media Gallery
		add_filter( 'upload_mimes', array($this, 'pdfemb_upload_mimes') );
		
		// Filter for PDFs in Media Gallery
		add_filter( 'post_mime_types', array($this, 'pdfemb_post_mime_types') );

		// Embed PDF shortcode instead of link
		add_filter( 'media_send_to_editor', array( $this, 'pdfemb_media_send_to_editor' ), 20, 3 );

		register_setting( $this->get_options_pagename(), $this->get_options_name(), Array($this, 'pdfemb_options_validate') );

		add_filter( 'attachment_fields_to_edit', array($this, 'pdfemb_attachment_fields_to_edit'), 10, 2 );

		wp_enqueue_style( 'pdfemb_admin_other_css', $this->my_plugin_url().'css/pdfemb-admin-other-'.$this->PLUGIN_VERSION.'.css', array(), $this->PLUGIN_VERSION  );
		if (is_admin()) {
			add_action( 'enqueue_block_editor_assets', array($this, 'gutenberg_enqueue_block_editor_assets') );
		}
		
	}

	// Override in Basic and Commercial
	public function pdfemb_attachment_fields_to_edit($form_fields, $post) {
		return $form_fields;
	}

	// Override in Premium
	public function pdfemb_init() {
		add_shortcode( 'pdf-embedder', array($this, 'pdfemb_shortcode_display_pdf') );

		// Gutenberg block
		if (function_exists('register_block_type')) {
			register_block_type( 'pdfemb/pdf-embedder-viewer', array(
				'render_callback' => array($this, 'pdfemb_shortcode_display_pdf')
			) );
		}
		if (is_admin()) {
			add_action( 'enqueue_block_assets', array($this, 'gutenberg_enqueue_block_assets') );
		}
		
	}

    public function pdfemb_plugin_action_links( $links, $file ) {
        if ($file == $this->my_plugin_basename()) {
            $links = $this->extra_plugin_action_links($links);

            $settings_link = '<a href="' . $this->get_settings_url() . '">' . __('Settings', 'pdf-embedder') .'</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    protected function extra_plugin_action_links( $links ) {
        return $links;
    }

	public function pdfemb_plugins_loaded() {
		load_plugin_textdomain( 'pdf-embedder', false, dirname($this->my_plugin_basename()).'/lang/' );
	}

	protected function add_actions() {

		add_action( 'plugins_loaded', array($this, 'pdfemb_plugins_loaded') );

		add_action( 'init', array($this, 'pdfemb_init') );
		
		add_action( 'wp_enqueue_scripts', array($this, 'pdfemb_wp_enqueue_scripts'), 5, 0 );

		if (is_admin()) {
			add_action( 'admin_init', array($this, 'pdfemb_admin_init'), 5, 0 );
			
			add_action($this->is_multisite_and_network_activated() ? 'network_admin_menu' : 'admin_menu', array($this, 'pdfemb_admin_menu'));
			
			if ($this->is_multisite_and_network_activated()) {
				add_action('network_admin_edit_'.$this->get_options_menuname(), array($this, 'pdfemb_save_network_options'));
			}

            add_filter($this->is_multisite_and_network_activated() ? 'network_admin_plugin_action_links' : 'plugin_action_links', array($this, 'pdfemb_plugin_action_links'), 10, 2 );
		}
	}

	// Gutenberg enqueues

	function gutenberg_enqueue_block_editor_assets() {
		wp_enqueue_script(
			'pdfemb-gutenberg-block-js', // Unique handle.
			$this->my_plugin_url(). 'js/pdfemb-blocks-'.$this->PLUGIN_VERSION.'.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ), // Dependencies, defined above.
			$this->PLUGIN_VERSION
		);

		wp_enqueue_style(
			'pdfemb-gutenberg-block-css', // Handle.
			$this->my_plugin_url(). 'css/pdfemb-blocks-'.$this->PLUGIN_VERSION.'.css', // editor.css: This file styles the block within the Gutenberg editor.
			//array( 'wp-edit-blocks' ), // Dependencies, defined above.
			$this->PLUGIN_VERSION
		);
	}

	function gutenberg_enqueue_block_assets() {
		wp_enqueue_style(
			'pdfemb-gutenberg-block-backend-js', // Handle.
			$this->my_plugin_url(). 'css/pdfemb-blocks-'.$this->PLUGIN_VERSION.'.css', // style.css: This file styles the block on the frontend.
			//array( 'wp-blocks' ), // Dependencies, defined above.
			$this->PLUGIN_VERSION
		);
	}
}


?>