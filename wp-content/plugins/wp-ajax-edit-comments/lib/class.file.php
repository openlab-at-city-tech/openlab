<?php
//Code from iThemes Builder
class AECFile {

	public static function get_writable_directory( $args ) {
			if ( is_string( $args ) )
				$args = array( 'name' => $args );
			else if ( ! is_array( $args ) )
				$args = array();
			
			$default_args = array(
				'name'					=> '',
				'create_new'			=> false,
				'rename'				=> false,
				'random'				=> false,
				'permissions'			=> 0755,
				'default_search_paths'	=> array( 'uploads_basedir', 'uploads_path', 'wp-content', 'abspath' ),
				'custom_search_paths'	=> array(),
			);
			$args = array_merge( $default_args, $args );
			
			
			if ( empty( $args['name'] ) && ( true === $args['create_new'] ) && ( false === $args['random'] ) )
				return new WP_Error( 'get_writable_directory_no_name', 'The call to AECFile::get_writable_directory is missing the name attribute' );
			
			
			$uploads = wp_upload_dir();
			
			if ( ! is_array( $uploads ) || ( false !== $uploads['error'] ) )
				$uploads = array( 'basedir' => false, 'path' => false );
			
			
			$default_search_paths = array(
				'uploads_basedir'	=> $uploads['basedir'],
				'uploads_path'		=> $uploads['path'],
				'wp-content'		=> WP_CONTENT_DIR,
				'abspath'			=> ABSPATH,
			);
			
			
			$search_paths = array_merge( (array) $args['custom_search_paths'], (array) $args['default_search_paths'] );
			$path = false;
			
			foreach ( (array) $search_paths as $search_path ) {
				if ( isset( $default_search_paths[$search_path] ) ) {
					if ( false === $default_search_paths[$search_path] )
						continue;
					
					$search_path = $default_search_paths[$search_path];
				}
				
				if ( is_dir( $search_path ) && is_writable( $search_path ) ) {
					$path = $search_path;
					break;
				}
			}
			
			
			if ( false === $path )
				return new WP_Error( 'get_writable_base_directory_failed', 'Unable to find a writable base directory' );
			
			if ( empty( $args['name'] ) && ( false === $args['random'] ) )
				return $path;
			
			
			if ( true === $args['random'] ) {
				$name = ( isset( $args['name'] ) ) ? $args['name'] : '';
				$uid = uniqid( "$name-", true );
				
				while ( is_dir( "$path/$uid" ) )
					$uid = uniqid( "$name-", true );
				
				$name = $uid;
			}
			else
				$name = $args['name'];
			
			if ( is_dir( "$path/$name" ) ) {
				if ( true === $args['create_new'] ) {
					if ( false === $args['rename'] )
						return new WP_Error( 'get_writable_directory_no_rename', 'Unable to create the named writable directory' );
					
					$name = AECFile::get_unique_name( $path, $name );
				}
				else {
					if ( is_writable( "$path/$name" ) )
						return "$path/$name";
					
					return new WP_Error( 'get_writable_directory_cannot_write', 'Required directory exists but is not writable' );
				}
			}
			else if ( false === $args['create_new'] )
				return new WP_Error( 'get_writable_directory_does_not_exist', 'Required writable directory does not exist' );
			
			if ( true === AECFile::mkdir( "$path/$name", $args['permissions'] ) )
				return "$path/$name";
			
			return new WP_Error( 'get_writable_directory_failed', 'Unable to create a writable directory' );
		} //end get_writable_directory
		
		public static function create_writable_directory( $args ) {
			if ( is_string( $args ) )
				$args = array( 'name' => $args );
			else if ( ! is_array( $args ) )
				$args = array();
			
			$default_args = array(
				'create_new'	=> true,
				'rename'		=> true,
				'random'		=> false,
			);
			$args = array_merge( $default_args, $args );
			
			return AECFile::get_writable_directory( $args );
		} //end create_writable_directory
		
		public static function get_writable_file( $args, $extension = null ) {
			if ( is_string( $args ) )
				$args = array( 'name' => $args );
			else if ( ! is_array( $args ) )
				$args = array();
			
			$default_args = array(
				'name'					=> '',
				'extension'				=> '',
				'create_new'			=> false,
				'rename'				=> false,
				'permissions'			=> 0644,
				'path_permissions'		=> 0755,
				'default_search_paths'	=> array( 'uploads_basedir', 'uploads_path', 'wp-content', 'abspath' ),
				'custom_search_paths'	=> array(),
			);
			$args = array_merge( $default_args, $args );
			
			if ( empty( $args['name'] ) )
				return new WP_Error( 'get_writable_file_no_name', 'The call to AECFile::get_writable_file is missing the name attribute' );
			
			if ( is_null( $extension ) )
				$extension = $args['extension'];
			
			if ( ! empty( $extension ) && ! preg_match( '/^\./', $extension ) )
				$extension = ".$extension";
			
			
			$base_path = AECFile::get_writable_directory( array( 'permissions' => $args['path_permissions'], 'default_search_paths' => $args['default_search_paths'], 'custom_search_paths' => $args['custom_search_paths'] ) );
			
			if ( is_wp_error( $base_path ) )
				return $base_path;
			
			
			$name = $args['name'];
			
			if ( preg_match( '|/|', $name ) ) {
				$base_path .= '/' . dirname( $name );
				$name = basename( $name );
			}
			
			
			$file = "$base_path/$name$extension";
			
			
			if ( is_file( $file ) ) {
				if ( true === $args['create_new'] ) {
					if ( false === $args['rename'] )
						return new WP_Error( 'get_writable_file_no_rename', 'Unable to create the named writable file' );
					
					$name = AECFile::get_unique_name( $base_path, $name, $extension );
					$file = "$base_path/$name";
				}
				else {
					if ( is_writable( $file ) )
						return $file;
					
					return new WP_Error( 'get_writable_file_cannot_write', 'Required file exists but is not writable' );
				}
			}
			else if ( false === $args['create_new'] )
				return new WP_Error( 'get_writable_file_does_not_exist', 'Required writable file does not exist' );
			
			if ( true === AECFile::is_file_writable( $file ) ) {
				@chmod( $file, $args['permissions'] );
				return $file;
			}
			
			return new WP_Error( 'get_writable_file_failed', 'Unable to create a writable file' );
		} //end create_writable_directory
		
		public static function create_writable_file( $args, $extension = null ) {
			if ( is_string( $args ) )
				$args = array( 'name' => $args );
			else if ( ! is_array( $args ) )
				$args = array();
			
			$default_args = array(
				'create_new'	=> true,
				'rename'		=> true,
			);
			$args = array_merge( $default_args, $args );
			
			return AECFile::get_writable_file( $args, $extension );
		} //end create_writable_file
		public static function get_url_from_file( $file ) {
			$url = '';
			
			if ( ( $uploads = wp_upload_dir() ) && ( false === $uploads['error'] ) ) {
				if ( 0 === strpos( $file, $uploads['basedir'] ) )
					$url = str_replace( $uploads['basedir'], $uploads['baseurl'], $file );
				else if ( false !== strpos( $file, 'wp-content/uploads' ) )
					$url = $uploads['baseurl'] . substr( $file, strpos( $file, 'wp-content/uploads' ) + 18 );
			}
			//Store an AEC site option of the basename of the file directory
			if ( empty( $url ) )
				$url = get_option( 'siteurl' ) . str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $file ) );
			
			if ( is_network_admin() ) {
				$dependency_upload_dir = str_replace( basename( $url ), '', $url );
				update_site_option( 'aec_dependency_upload_dir', $dependency_upload_dir );
			}
			//Try to get the site option
			$dependency_url = get_site_option( 'aec_dependency_upload_dir' );
			if ( $dependency_url ) {
				$url = $dependency_url . basename( $url );
			}
			
			return $url;
		} //end get_url_from_file
		public static function get_writable_uploads_directory( $directory ) {
			$uploads = wp_upload_dir();
			
			if ( ! is_array( $uploads ) || ( false !== $uploads['error'] ) )
				return false;
			
			
			$path = "{$uploads['basedir']}/$directory";
			
			if ( ! is_dir( $path ) ) {
				AECFile::mkdir( $path );
				
				if ( ! is_dir( $path ) )
					return false;
			}
			if ( ! is_writable( $path ) )
				return false;
			
			$directory_info = array(
				'path'		=> $path,
				'url'		=> "{$uploads['baseurl']}/$directory",
			);
			
			return $directory_info;
		} //end get_writable_uploads_directory
		
		public static function find_writable_path( $args = array(), $vars = array() ) {
			$default_args = array(
				'private'			=> true,
				'possible_paths'	=> array(),
				'permissions'		=> 0755,
			);
			$args = array_merge( $default_args, $args );
			
			$uploads_dir_data = wp_upload_dir();
			
			$default_vars = array(
				'uploads_basedir'	=> $uploads_dir_data['basedir'],
				'uploads_path'		=> $uploads_dir_data['path'],
			);
			$vars = array_merge( $default_vars, $vars );
			
			
			foreach ( (array) $args['possible_paths'] as $path ) {
				foreach ( (array) $vars as $var => $val )
					$path = preg_replace( '/%' . preg_quote( $var, '/' ) . '%/', $val, $path );
				
				if ( ! is_dir( $path ) )
					AECFile::mkdir( $path, $args['permissions'] );
				
				$path = realpath( $path );
				
				if ( ! empty( $path ) && is_writable( $path ) ) {
					$writable_dir = $path;
					break;
				}
			}
			
			if ( empty( $writable_dir ) || ! is_writable( $writable_dir ) ) {
				if ( is_writable( $uploads_dir_data['basedir'] ) )
					$writable_dir = $uploads_dir_data['basedir'];
				else if ( is_writable( $uploads_dir_data['path'] ) )
					$writable_dir = $uploads_dir_data['path'];
				else if ( is_writable( dirname( __FILE__ ) ) )
					$writable_dir = dirname( __FILE__ );
				else if ( is_writable( ABSPATH ) )
					$writable_dir = ABSPATH;
				else if ( true === $args['private'] )
					return new WP_Error( 'no_private_writable_path', 'Unable to find a writable path within the private space' );
				else
					$writable_dir = sys_get_temp_dir();
			}
			
			if ( empty( $writable_dir ) || ! is_dir( $writable_dir ) || ! is_writable( $writable_dir ) )
				return new WP_Error( 'no_writable_path', 'Unable to find a writable path' );
			
			$writable_dir = preg_replace( '|/+$|', '', $writable_dir );
			
			return $writable_dir;
		} //end find_writable_path
		
		public static function create_writable_path( $args = array() ) {
			if ( is_string( $args ) )
				$args = array( 'name' => $args );
			else if ( ! is_array( $args ) )
				$args = array();
			
			$default_args = array(
				'name'				=> 'temp-deleteme',
				'private'			=> true,
				'possible_paths'	=> array(),
				'permissions'		=> 0755,
				'rename'			=> false,
			);
			$args = array_merge( $default_args, $args );
			
			
			$writable_dir = AECFile::find_writable_path( array( 'private' => $args['private'], 'possible_paths' => $args['possible_paths'], 'permissions' => $args['permissions'] ) );
			
			if ( is_wp_error( $writable_dir ) )
				return $writable_dir;
			
			
			$test_dir_name = $args['name'];
			$path = "$writable_dir/$test_dir_name";
			
			
			if ( file_exists( $path ) && ( false === $args['rename'] ) ) {
				if ( is_writable( $path ) )
					return $path;
				else
					return new WP_Error( 'create_writable_path_failed', 'Requested path exists and cannot be written to' );
			}
			
			
			$count = 0;
			
			while ( is_dir( "$writable_dir/$test_dir_name" ) ) {
				$count++;
				$test_dir_name = "{$args['name']}-$count";
			}
			
			$path = "$writable_dir/$test_dir_name";
			
			if ( false === AECFile::mkdir( $path, $args['permissions'] ) )
				return new WP_Error( 'create_path_failed', 'Unable to create a writable path' );
			if ( ! is_writable( $path ) )
				return new WP_Error( 'create_writable_path_failed', 'Unable to create a writable path' );
			
			return $path;
		} //end create_writable_path
		
		public static function create_writable_file_old( $args ) {
			$default_args = array(
				'name'				=> 'deleteme',
				'extension'			=> '.tmp',
				'path_name'			=> '',
				'private'			=> true,
				'possible_paths'	=> array(),
				'permissions'		=> 0644,
				'path_permissions'	=> 0755,
				'overwrite'			=> false,
				'rename'			=> true,
			);
			$args = array_merge( $default_args, $args );
			
			
			if ( empty( $args['path_name'] ) )
				$writable_dir = AECFile::find_writable_path( array( 'private' => $args['private'], 'possible_paths' => $args['possible_paths'], 'permissions' => $args['path_permissions'] ) );
			else
				$writable_dir = AECFile::create_writable_path( array( 'name' => $args['path_name'], 'private' => $args['private'], 'possible_paths' => $args['possible_paths'], 'permissions' => $args['path_permissions'] ) );
			
			if ( is_wp_error( $writable_dir ) )
				return $writable_dir;
			
			
			$test_file_name = "{$args['name']}{$args['extension']}";
			
			if ( file_exists( "$writable_dir/$test_file_name" ) ) {
				if ( false === $args['overwrite'] ) {
					if ( false === $args['rename'] )
						return new WP_Error( 'requested_file_exists', 'The requested file exists and settings don\'t allow overwriting' );
					
					$count = 0;
					
					while ( is_file( "$writable_dir/$test_file_name" ) ) {
						$count++;
						$test_file_name = "{$args['name']}-$count{$args['extension']}";
					}
				}
			}
			
			$file = "$writable_dir/$test_file_name";
			
			
			if ( false === AECFile::is_file_writable( $file ) )
				return new WP_Error( 'create_file_failed', 'Unable to create the file' );
			@chmod( $file, $args['permissions'] );
			
			if ( ! is_writable( $file ) )
				return new WP_Error( 'create_writable_file_failed', 'The file was successfully created but cannot be written to' );
			
			return $file;
		} //end create_writable_file_old
		public static function mkdir( $directory, $args = array() ) {
			if ( is_dir( $directory ) )
				return true;
			if ( is_file( $directory ) )
				return false;
			
			
			if ( is_int( $args ) )
				$args = array( 'permissions' => $args );
			if ( is_bool( $args ) )
				$args = array( 'create_index' => false );
			
			$default_args = array(
				'permissions'	=> 0755,
				'create_index'	=> true,
			);
			$args = array_merge( $default_args, $args );
			
			
			if ( ! is_dir( dirname( $directory ) ) ) {
				if ( false === AECFile::mkdir( dirname( $directory ), $args ) )
					return false;
			}
			
			if ( false === @mkdir( $directory, $args['permissions'] ) )
				return false;
			
			if ( true === $args['create_index'] )
				AECFile::write( "$directory/index.php", '<?php // Silence is golden.' );
			
			return true;
		} //end mkdir
		public static function copy( $source, $destination, $args = array() ) {
			$default_args = array(
				'max_depth'		=> 100,
				'folder_mode'	=> 0755,
				'file_mode'		=> 0744,
				'ignore_files'	=> array(),
			);
			$args = array_merge( $default_args, $args );
			
			AECFile::_copy( $source, $destination, $args );
		} //end copy
		
		public static function _copy( $source, $destination, $args, $depth = 0 ) {
			if ( $depth > $args['max_depth'] )
				return true;
			
			if ( is_file( $source ) ) {
				if ( is_dir( $destination ) || preg_match( '|/$|', $destination ) ) {
					$destination = preg_replace( '|/+$|', '', $destination );
					
					$destination = "$destination/" . basename( $source );
				}
				
				if ( false === AECFile::mkdir( dirname( $destination ), $args['folder_mode'] ) )
					return false;
				
				if ( false === @copy( $source, $destination ) )
					return false;
				
				@chmod( $destination, $args['file_mode'] );
				
				return true;
			}
			else if ( is_dir( $source ) || preg_match( '|/\*$|', $source ) ) {
				if ( preg_match( '|/\*$|', $source ) )
					$source = preg_replace( '|/\*$|', '', $source );
				else if ( preg_match( '|/$|', $destination ) )
					$destination = $destination . basename( $source );
				
				$destination = preg_replace( '|/$|', '', $destination );
				
				$files = array_diff( array_merge( glob( $source . '/.*' ), glob( $source . '/*' ) ), array( $source . '/.', $source . '/..' ) );
				
				if ( false === AECFile::mkdir( $destination, $args['folder_mode'] ) )
					return false;
				
				$result = true;
				
				foreach ( (array) $files as $file ) {
					if ( false === AECFile::_copy( $file, "$destination/", $args, $depth + 1 ) )
						$result = false;
				}
				
				return $result;
			}
			
			return false;
		} //end _copy
		
		public static function delete_directory( $path ) {
			if ( ! is_dir( $path ) )
				return true;
			
			$files = array_merge( glob( "$path/*" ), glob( "$path/.*" ) );
			$contents = array();
			
			foreach ( (array) $files as $file ) {
				if ( in_array( basename( $file ), array( '.', '..' ) ) )
					continue;
				
				if ( is_dir( $file ) )
					AECFile::delete_directory( $file );
				else if ( is_file( $file ) )
					@unlink( $file );
			}
			
			@rmdir( $path );
			
			if ( ! is_dir( $path ) )
				return true;
			return false;
		} //end delete_directory
		
		public static function get_unique_name( $path, $prefix, $postfix = '' ) {
			$count = 0;
			
			$test_name = "$prefix$postfix";
			
			while ( file_exists( "$path/$test_name" ) ) {
				$count++;
				$test_name = "$prefix-$count$postfix";
			}
			
			return $test_name;
		} //end get_unique_name
		public static function is_file_writable( $path ) {
			return AECFile::write( $path, '', array( 'append' => true ) );
		} //end is_file_writable
		public static function write( $path, $content, $args = array() ) {
			if ( is_bool( $args ) )
				$args = array( 'append' => $args );
			else if ( is_int( $args ) )
				$args = array( 'permissions' => $args );
			else if ( ! is_array( $args ) )
				$args = array();
			
			$default_args = array(
				'append'		=> false,
				'permissions'	=> 0644,
			);
			$args = array_merge( $default_args, $args );
			
			
			$mode = ( false === $args['append'] ) ? 'w' : 'a';
			
			if ( ! is_dir( dirname( $path ) ) ) {
				AECFile::mkdir( dirname( $path ) );
				
				if ( ! is_dir( dirname( $path ) ) )
					return false;
			}
			
			$created = ! is_file( $path );
			
			if ( false === ( $handle = fopen( $path, $mode ) ) )
				return false;
			
			$result = fwrite( $handle, $content );
			fclose( $handle );
			
			if ( false === $result )
				return false;
			
			if ( ( true === $created ) && is_int( $args['append'] ) )
				@chmod( $path, $args['append'] );
			
			return true;
		} //end write
} //end class
?>