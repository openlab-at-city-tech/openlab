<?php
/*
Plugin Name: WP Post to PDF
Plugin URI: http://www.techna2.com/blog
Description: Allow visitor to download post in PDF format. No addon or change required on server. Uses TCPDF class to generate PDF. Click 'Settings' link to configure plugin.
Version: 2.3.1
License: GPL
Author: Neerav Dobaria
Author URI: http://www.techna2.com/blog

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
//avoid direct calls to this file, because now WP core and framework has been used
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Define certain terms which may be required throughout the plugin
global $blog_id;
define('WPPT0PDF_NAME', 'WP Post to PDF');
define('WPPT0PDF_SNAME', 'wpptopdf');
define('WPPT0PDF_PATH', WP_PLUGIN_DIR . '/wp-post-to-pdf');
define('WPPT0PDF_URL', WP_PLUGIN_URL . '/wp-post-to-pdf');
define('WPPT0PDF_BASENAME', plugin_basename(__FILE__));
define('WPPT0PDF_CACHE_DIR', WP_CONTENT_DIR . '/uploads/wp-post-to-pdf-cache/' . $blog_id);

if (!class_exists(wpptopdf)) {

    class wpptopdf
    {

        private $options;

        function wpptopdf()
        {
            $this->options = get_option('wpptopdf');
            if (is_admin()) {
                add_action('admin_init', array(&$this, 'on_admin_init'));
                add_action('admin_menu', array(&$this, 'on_admin_menu'));
                add_filter("plugin_action_links_" . WPPT0PDF_BASENAME, array(&$this, 'action_links'));
                register_activation_hook(WPPT0PDF_BASENAME, array(&$this, 'on_activate'));
                add_action('pre_post_update', array(&$this, 'on_pre_post_update'));
                add_action('post_updated', array(&$this, 'generate_pdf_file'));
            } else {
                add_action('wp', array(&$this, 'generate_pdf'));
                add_filter('the_content', array(&$this, 'add_button'));
            }
        }

        function on_admin_init()
        {
            register_setting('wpptopdf_options', 'wpptopdf', array(&$this, 'on_update_options'));
        }

        function on_update_options($post)
        {
            if (isset($post['submit']) and 'Save and Reset PDF Cache' == $post['submit']) {
                $this->delete_cache(WPPT0PDF_CACHE_DIR);
            }
            return $post;
        }

        function delete_cache($path)
        {
            if (is_dir($path) === true) {
                $files = array_diff(scandir($path), array('.', '..'));
                foreach ($files as $file) {
                    $this->delete_cache(realpath($path) . '/' . $file);
                }
                return true;
            }
            else if (is_file($path) === true) {
                return unlink($path);
            }
            return false;
        }

        function on_admin_menu()
        {
            $option_page = add_options_page('WP Post to PDF Options', 'WP Post to PDF', 'administrator', WPPT0PDF_BASENAME, array(&$this, 'options_page'));
            //add_action("admin_print_scripts-$option_page", array(&$this, 'on_admin_print_scripts'));
            add_action("admin_print_styles-$option_page", array(&$this, 'on_admin_print_styles'));
        }

        function options_page()
        {
            include(WPPT0PDF_PATH . '/wpptopdf_options.php');
        }

        function on_admin_print_styles()
        {
            wp_enqueue_style('wpptopdfadminstyle', WPPT0PDF_URL . '/asset/css/admin.css', false, '1.0', 'all');
        }

        /*function on_admin_print_scripts() {
          wp_enqueue_script('wpptopdfadminstyle', WPPT0PDF_URL . '/asset/css/admin.css',false, '1.0', 'all');
        }*/

        function action_links($links)
        {
            $settings_link = '<a href="options-general.php?page=' . WPPT0PDF_BASENAME . '">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        function generate_pdf()
        {
            if ('pdf' == $_GET['format']) {
                if ($this->options['nonPublic'] and !is_user_logged_in())
                    return false;

                global $post;

                $include = $this->options['include'];
                $excludeThis = explode(',', $this->options['excludeThis']);
                if ($include and !in_array($post->ID, $excludeThis))
                    return false;
                if (!$include and in_array($post->ID, $excludeThis))
                    return false;

	            $filePath = WPPT0PDF_CACHE_DIR . '/' . $post->post_name . '.pdf';
                $fileMime = 'pdf';
                $fileName = $post->post_name . '.pdf';
	            $includeCache = $this->options['includeCache'];
                $excludeThisCache = explode(',', $this->options['excludeThisCache']);
	            if ($includeCache and !in_array($post->ID, $excludeThisCache)){
		            $this->generate_pdf_file($post->ID);
	            } elseif (!$includeCache and in_array($post->ID, $excludeThisCache)){
		            $this->generate_pdf_file($post->ID);
	            } else{
		            if (!file_exists($filePath)) {
                        $this->generate_pdf_file($post->ID);
                    }
	            }
				$output = $this->output_pdf_file($filePath, $fileName, $fileMime);
            }
        }

        function output_pdf_file($file, $name, $mime_type = '')
        {
            if (!is_readable($file))
                return false;

            $size = filesize($file);
            $name = rawurldecode($name);

            /* Figure out the MIME type (if not specified) */
            $known_mime_types = array(
                "pdf" => "application/pdf",
                "txt" => "text/plain",
                "html" => "text/html",
                "htm" => "text/html",
                "exe" => "application/octet-stream",
                "zip" => "application/zip",
                "doc" => "application/msword",
                "xls" => "application/vnd.ms-excel",
                "ppt" => "application/vnd.ms-powerpoint",
                "gif" => "image/gif",
                "png" => "image/png",
                "jpeg" => "image/jpg",
                "jpg" => "image/jpg",
                "php" => "text/plain"
            );

            if ($mime_type == '') {
                $file_extension = strtolower(substr(strrchr($file, "."), 1));
                if (array_key_exists($file_extension, $known_mime_types)) {
                    $mime_type = $known_mime_types[$file_extension];
                } else {
                    $mime_type = "application/force-download";
                }
            }

            @ob_end_clean(); //turn off output buffering to decrease cpu usage
            // required for IE, otherwise Content-Disposition may be ignored
            if (ini_get('zlib.output_compression'))
                ini_set('zlib.output_compression', 'Off');

            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header("Content-Transfer-Encoding: binary");
            header('Accept-Ranges: bytes');

            /* The three lines below basically make the
               download non-cacheable */
            header("Cache-control: private");
            header('Pragma: private');
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

            // multipart-download and download resuming support
            if (isset($_SERVER['HTTP_RANGE'])) {
                list($a, $range) = explode("=", $_SERVER['HTTP_RANGE'], 2);
                list($range) = explode(",", $range, 2);
                list($range, $range_end) = explode("-", $range);
                $range = intval($range);
                if (!$range_end) {
                    $range_end = $size - 1;
                } else {
                    $range_end = intval($range_end);
                }

                $new_length = $range_end - $range + 1;
                header("HTTP/1.1 206 Partial Content");
                header("Content-Length: $new_length");
                header("Content-Range: bytes $range-$range_end/$size");
            } else {
                $new_length = $size;
                header("Content-Length: " . $size);
            }

            /* output the file itself */
            $chunksize = 1 * (1024 * 1024); //you may want to change this
            $bytes_send = 0;
            if ($file = fopen($file, 'r')) {
                if (isset($_SERVER['HTTP_RANGE']))
                    fseek($file, $range);

                while (!feof($file) &&
                    (!connection_aborted()) &&
                    ($bytes_send < $new_length)
                ) {
                    $buffer = fread($file, $chunksize);
                    print($buffer); //echo($buffer); // is also possible
                    flush();
                    $bytes_send += strlen($buffer);
                }
                fclose($file);
            } else
                return false;

            return true;
        }

        function generate_pdf_file($id, $forceDownload = false)
        {
            $post = get_post($id);

            if (!$this->options[$post->post_type])
                return false;

            require_once(WPPT0PDF_PATH . '/tcpdf/config/lang/eng.php');
			// to avoid duplicate function error
            if(!class_exists('TCPDF'))
                require_once(WPPT0PDF_PATH . '/tcpdf/tcpdf.php');

            // to avoid duplicate function error ( conflict with Lightbox Plus v2.4.6 )
            if(!class_exists('simple_html_dom'))
                require_once(WPPT0PDF_PATH . '/simplehtmldom/simple-html-dom.php');

            $filePath = WPPT0PDF_CACHE_DIR . '/' . $post->post_name . '.pdf';

            // create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Let other filter modify content if selected
            if ($this->options['otherPlugin'])
                $post->post_content = apply_filters('the_content', $post->post_content);
            else
                $post->post_content = wpautop($post->post_content);

            // Process shortcodes if selected
            if ($this->options['processShortcodes'])
                $post->post_content = do_shortcode($post->post_content);

            // set document information
            $pdf->SetCreator('WP Post to PDF plugin by Neerav Dobaria (http://www.techna2.com/blog) with ' . PDF_CREATOR);
            $pdf->SetAuthor(get_bloginfo('name'));
            $pdf->SetTitle($post->post_title);

            // Count width of logo for better presentation
            $logodata = getimagesize(PDF_HEADER_LOGO);
            $logowidth = (int)((14 * $logodata[0]) / $logodata[1]);

            //$pdf->SetSubject('TCPDF Tutorial');
            //$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
            // set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, $logowidth, html_entity_decode(get_bloginfo('name')), html_entity_decode(get_bloginfo('description') . "\n" . get_bloginfo('siteurl')));
            // set header and footer fonts
            $pdf->setHeaderFont(Array($this->options['headerFont'], '', $this->options['headerFontSize']));
            $pdf->setFooterFont(Array($this->options['footerFont'], '', $this->options['footerFontSize']));
            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            //set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            //set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            //set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            // ---------------------------------------------------------
            // set default font subsetting mode
            $pdf->setFontSubsetting(true);
            // Set font
            // dejavusans is a UTF-8 Unicode font, if you only need to
            // print standard ASCII chars, you can use core fonts like
            // helvetica or times to reduce file size.
            $pdf->SetFont($this->options['contentFont'], '', $this->options['contentFontSize'], '', true);
            // Add a page
            // This method has several options, check the source code documentation for more information.
            $pdf->AddPage();
            // Set some content to print
            $html = '<h1>' . html_entity_decode($post->post_title, ENT_QUOTES) . '</h1>';

            // Display author name is set in config
            if ($this->options['authorDetail']){
                $author = get_the_author_meta('user_nicename', $post->post_author);
                $html .= '<p><strong>Author : </strong>'.$author.'</p>';
            }

            // Display featured image if set in config and post/page
            if ($this->options['featuredImage']){
                if(has_post_thumbnail($post->ID)){
                    $html .= get_the_post_thumbnail($post->ID);
                }
            }

            $html .= htmlspecialchars_decode(htmlentities($post->post_content, ENT_NOQUOTES, 'UTF-8', false), ENT_NOQUOTES);
            $dom = new simple_html_dom();
            $dom->load($html);

            foreach($dom->find('img') as $e){
                $e->align = null;
                $e->outertext = '<div style="text-align:center;">' . $e->outertext . '</div>';
            }

            $html = $dom->save();

            $dom->clear();

            // Print text using writeHTML
            $pdf->writeHTML($html, true, 0, true, 0);
            // ---------------------------------------------------------
            // Close and output PDF document
            // This method has several options, check the source code documentation for more information.

            // Create directory if not exist
            if (!is_dir(WPPT0PDF_CACHE_DIR))
                mkdir(WPPT0PDF_CACHE_DIR);

            if ($forceDownload)
                $pdf->Output($filePath, 'FI');
            else
                $pdf->Output($filePath, 'F');
        }

        function add_button($content)
        {
            // If manual is selected, let user decide where to add button
            if ('manual' == $this->options['iconPosition'])
                return $content;

            // get button html
            $button = $this->display_icon();
            // Set button after and before post
            if ('beforeandafter' == $this->options['iconPosition'])
                $content = $button . $content . $button;
            // Set button after post
            elseif ('after' == $this->options['iconPosition'])
                $content = $content . $button;
            // by default Set button before post
            else
                $content = $button . $content;
            return $content;
        }

        function display_icon()
        {
            // return nothing if no permission
            if ($this->options['nonPublic'] and !is_user_logged_in())
                return;

            if ($this->options['onSingle'] and !(is_single() or is_page()))
                return;

            // remove icon from PDF file
            if('pdf' == $_GET['format']){
                return;
            }

            global $post;

            if (!$this->options[$post->post_type])
                return false;

            // return nothing if post in exclude list
            $include = $this->options['include'];
            $excludeThis = explode(',', $this->options['excludeThis']);
            if ($include and !in_array($post->ID, $excludeThis))
                return;

            if (!$include and in_array($post->ID, $excludeThis))
                return;

            // Change querystring separator for those who do not have pretty URL enabled
            $qst = get_permalink($post->ID);
            $qst = parse_url($qst);
            if ($qst['query'])
                $qst = '&format=pdf';
            else
                $qst = '?format=pdf';

            return '<a class="wpptopdf" target="_blank" rel="noindex,nofollow" href="' . get_permalink($post->ID) . $qst . '" title="Download PDF">' . $this->options['imageIcon'] . '</a>';
        }

        function on_activate()
        {
            // set default options on activate
            $default = array(
                    'post' => 1,
                    'page' => 1,
                    'include' => 0,
                    'includeCache' => 0,
                    'iconPosition' => 'before',
                    'imageIcon' => '<img alt="Download PDF" src="' . WPPT0PDF_URL . '/asset/images/pdf.png">',
                    'headerFont' => 'helvetica',
                    'headerFontSize' => 10,
                    'footerFont' => 'helvetica',
                    'footerFontSize' => 10,
                    'contentFont' => 'helvetica',
                    'contentFontSize' => 12,
                );
            if (!get_option('wpptopdf')) {
                add_option('wpptopdf', $default);
            }

            // create directory and move logo to upload directory
            if (!is_dir(WP_CONTENT_DIR . '/uploads'))
                mkdir(WP_CONTENT_DIR . '/uploads');
            if (!file_exists(WP_CONTENT_DIR . '/uploads/wp-post-to-pdf-logo.png'))
                copy(WPPT0PDF_PATH . '/asset/images/logo.png', WP_CONTENT_DIR . '/uploads/wp-post-to-pdf-logo.png');
            if (!is_dir(WP_CONTENT_DIR . '/uploads/wp-post-to-pdf-cache'))
                mkdir(WP_CONTENT_DIR . '/uploads/wp-post-to-pdf-cache');
        }

        function on_pre_post_update($id)
        {
            $post = get_post($id);
            $filePath = WPPT0PDF_CACHE_DIR . '/' . $post->post_name . '.pdf';
            if (file_exists($filePath))
                unlink($filePath);
        }

    }

    $wpptopdf = new wpptopdf();

    /**
     * Display PDF download icon if applicable
     * @return void
     */
    if(!function_exists('wpptopdf_display_icon')){
        function wpptopdf_display_icon()
        {
            global $wpptopdf;
            //$wpptopdf = new wpptopdf();
            return $wpptopdf->display_icon();
        }
    }
}