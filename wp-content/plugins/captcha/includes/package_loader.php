<?php
/**
 * Load images for CAPTCHA
 * @package Captcha by BestWebSoft
 * @since 4.1.6
 */
if ( ! class_exists( 'Cptch_package_loader' ) ) {
	class Cptch_package_loader {

		public $error;
		public $message;
		public $upload_dir;
		public $packages;
		public $images;

		/**
		 * Constructor of class
		 */
		function __construct() {
			if ( is_multisite() ) {
				switch_to_blog( 1 );
				$upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$upload_dir = wp_upload_dir();
			}
			$this->upload_dir      = $upload_dir['basedir'] . '/bws_captcha_images';
			$this->error           = '';
			if( ! file_exists( $this->upload_dir ) ) {
				if ( is_writable( $upload_dir['basedir'] ) )
					mkdir( $this->upload_dir );
				else
					$this->error = __( 'Can not load images in to the "uploads" folder. Please, check your permissions.', 'captcha' );
			}
		}

		/**
		 * Get package info from package.txt
		 * @param     string       $package_path     absolute path to folder with images
		 * @return    boolean
		 */
		function parse_packages( $package_path ) {
			/* check file packages.txt */
			if ( ! file_exists( $package_path . '/packages.txt' ) ) {
				$this->error = __( 'File packages.txt not found. Package not saved.', 'captcha' );
				return false;
			}

			$file_content = file_get_contents( $package_path . '/' . '/packages.txt' );
			if ( ! $file_content || empty( $file_content ) ) {
				$this->error = __( 'Can not read data from packages.txt. Packages not saved.', 'captcha' );
				return false;
			}

			global $wpdb;
			$temp_array       = explode( "\n", $file_content );
			$this->packages   = 
			$this->images     = 
			$compare_images   = 
			$compare_packages = array();
			$i                = 0;
			/* get info about loaded packages */
			$temp = $wpdb->get_results( "SELECT `id`, `folder` FROM `{$wpdb->base_prefix}cptch_packages`;" );
			if ( empty( $temp ) ) {
				$current_package = 'uncategorized';
				$package_id      = 1;
				$compare_packages[ $package_id ] = $current_package;
			} else {
				foreach( $temp as $pack )
					$compare_packages[ $pack->id ] = $pack->folder;
				$current_package = $compare_packages[ $pack->id ];
				$package_id      = $pack->id;
			}
			/* get info about loaded images */
			$temp = $wpdb->get_results( "SELECT `id`, `name` FROM `{$wpdb->base_prefix}cptch_images`;" );
			if ( ! empty( $temp ) ) {
				foreach( $temp as $image ) {
					$compare_images[ $image->id ] = $image->name;
				}
			}
			foreach ( (array)$temp_array as $string ) {
				if ( empty( $string ) ) 
					continue;
				/* add info about package */
				if ( preg_match( '/^#(.+?):(.*?)$/', $string, $matches ) && ! empty( $matches[1] ) ) {
					$property = stripslashes( esc_html( trim( $matches[1] ) ) );
					$value    = $matches[2];
					switch( $property ) {
						case 'package': 
							$package_folder = stripslashes( esc_html( trim( $value ) ) );
							if ( ! in_array( $package_folder, $compare_packages ) ) {
								$current_package = $package_folder;
								$package_id      = empty( $compare_packages ) ? 1 : max( array_keys( $compare_packages ) ) + 1;
								$this->packages[ $package_id ]['folder'] = $current_package;
								$compare_packages[ $package_id ]         = $current_package;
							}

							break;
						case 'name':
							if (! isset( $this->packages[ $package_id ]['folder'] ) )
								break;
							$package_name = stripslashes( esc_html( trim( $value ) ) );
							if ( ! empty( $package_name ) )
								$this->packages[ $package_id ]['name'] = $package_name;
							
							break;
						case 'disabled':
							$this->packages[ $package_id ]['disabled'] = true;
							break;
						default:
							break;
					}
				/* add info about image */
				} else {
					$image_data = explode( ' ', $string );
					if ( ! is_array( $image_data ) || empty( $image_data ) && 2 > count( $image_data ) )
						continue;
					$file_name = stripslashes( esc_html( trim( $image_data[0] ) ) );
					if ( ! $this->is_image( $file_name ) )
						continue;
					$number    = stripslashes( esc_html( trim( $image_data[1] ) ) );
					if ( 2 < strlen( $number ) )
						$number = substr( $number, 0, 2 );
					$file      = $package_path . '/' . $current_package . '/' . $file_name;
					$new_file  = sanitize_file_name( $file_name );
					$dest      = $this->upload_dir . '/' . $current_package;
					$dest_file = $dest . '/' . $new_file;
					if ( 
						/* image not loaded to database yet */
						! in_array( $file_name, $compare_images ) &&
						/* second element is number */
						is_numeric( $number ) && 
						/* file exists in package */
						file_exists( $file ) &&
						/* get new name of file */
						! empty( $new_file ) &&
						/* file not copied to 'uploads/bws_captcha_images' yet */
						! file_exists( $dest_file )
					) {
						/* folder not exists */
						if ( ! file_exists( $dest ) ) {
							if ( is_writable( $this->upload_dir ) ) {
								mkdir( $dest );
							} else {
								$this->error = __( 'Can not load images in to the "uploads" folder. Please, check your permissions.', 'captcha' );
								return false;
							}
						}
						/* if we finaly copied this image :) */
						if ( copy( $file, $dest_file ) ) {
							$image_id = empty( $this->images ) ? 1 : max( array_keys( $this->images ) ) + 1;
							$this->images[ $image_id ] = array(
								'name'       => $new_file,
								'package_id' => $package_id,
								'number'     => $number,
							);
						}
					}
				}
				/* insert data to database after every 500th iteration */
				if ( $i >= 500 ) {
					if ( ! $this->insert_data() )
						return false;
					$i = 0;
				} else {
					$i ++;
				}
			}
			/* insert recent data */
			if ( ! $this->insert_data() ) {
				return false;
			} else {
				$this->message = __( 'Package successfully loaded.', 'captcha' );
				return true;
			}
		}

		/**
		 * Check file extension
		 * @param   string  path to file
		 * @return  string  image`s extension or false
		 */
		function is_image( $file_name ) {
			$allowed_formats = $mimes = array(
				'gif'          => 'image/gif',
				'png'          => 'image/png',
				'jpg|jpeg|jpe' => 'image/jpeg'
			);
			$data = wp_check_filetype( $file_name, $allowed_formats );
			return $data['ext'];
		}

		/**
		 * Insert data in to data base
		 * @return   boolean
		 */
		function insert_data() {
			global $wpdb, $cptch_options;
			if ( empty( $cptch_options ) )
				$cptch_options = get_option( 'cptch_options' );
			$update_data = $cptch_options['used_packages'];
			$need_update = false;
			$insert_data = array();
			/* insert data about packages */
			if ( ! empty( $this->packages ) ) {
				foreach ( $this->packages as $id => $value ) {
					if ( ! in_array( $id, $update_data ) && ! isset( $value['disabled'] ) )
						$update_data[] = $id;
					if ( isset( $value['folder'] ) ) {
						if ( ! isset( $value['name'] ) )
							$value['name'] = $value['folder'];
						$insert_data[] = "( {$id}, '{$value['name']}', '{$value['folder']}' )";
					}
				}
				if ( ! empty( $insert_data ) ) {
					$wpdb->query( "INSERT IGNORE INTO `{$wpdb->base_prefix}cptch_packages` ( `id`, `name`, `folder` ) VALUES " . implode( ',', $insert_data ) . ";" );
					if ( $wpdb->last_error ) {
						$this->error = $wpdb->last_error;
						return false;
					} else {
						$need_update = true;
					}
				}
				$this->packages = array();
			}
			$insert_data = array();
			/* insert data about images */
			if ( ! empty( $this->images ) ) {
				foreach ( $this->images as $id => $data )
					$insert_data[] = "( {$id}, '{$data['name']}', {$data['package_id']}, {$data['number']} )";
				if ( ! empty( $insert_data ) ) {
					$wpdb->query( "INSERT IGNORE INTO `{$wpdb->base_prefix}cptch_images` ( `id`, `name`, `package_id`, `number` ) VALUES " . implode( ',', $insert_data ) . ";" );
					if ( $wpdb->last_error ) {
						$this->error = $wpdb->last_error;
						return false;
					} else {
						$need_update = true;
					}
				}
				$this->images = array();
			}
			if ( $need_update ) {
				$cptch_options['used_packages']          = $update_data;
				$cptch_options['cptch_difficulty_image'] = 1;
				update_option( 'cptch_options', $cptch_options );
			}
			return true;
		}
	}
}