<?php
/**
 * CubePoints API
 */

function cp_api(){
	echo json_encode((array)cp_api_do());
	die();
} 

function cp_api_do(){
	if(get_option('cp_auth_key')!=$_REQUEST['cp_api_key']){
		$r['error'] = 'Invalid API key';
		return $r;
	}

	$s = $_REQUEST['cp_api'];
	$q = explode('/', $s);

	switch ($q[0]) {
	
		case 'user':
			switch($q[1]){
				case 'login':
					$user = get_userdatabylogin($q[2]);
					break;
				case 'id':
					$user = get_userdata($q[2]);
					break;
				default:
					$r['error'] = 'Method not implemented';
					return $r;
			}
			if($user->ID==''){
				$r['error'] = 'Invalid user';
				return $r;
			}
			switch($q[3]){
				case '':
					$r = $user;
					return $r;
					break;
				case 'points':
					switch($q[4]){
						case '':
							$r['points'] = cp_getPoints($user->ID);
							return $r;
							break;
						case 'get':
							$r['points'] = cp_getPoints($user->ID);
							return $r;
							break;
						case 'set':
							if(!is_numeric($q[5])){
								$r['error'] = 'Points must be integers';
								return $r;
							}
							else{
								cp_updatePoints($user->ID, (int)$q[5]);
								$r['points'] = cp_getPoints($user->ID);
								$r['message'] = 'Points updated';
								return $r;
							}
							break;
						case 'add':
							if(!is_numeric($q[5])){
								$r['error'] = 'Points must be integers';
								return $r;
							}
							else{
								switch($q[6]){
									case '':
										cp_alterPoints($user->ID, $q[5]);
										$r['points'] = cp_getPoints($user->ID);
										$r['message'] = 'Points updated';
										return $r;
										break;
									case 'log':
										if($q[7]==''){
											$r['error'] = 'Log item type must not be empty';
											return $r;
										}
										$data = explode('/', $s, 9);
										cp_points($q[7], $user->ID, $q[5], $data[8]);
										$r['points'] = cp_getPoints($user->ID);
										$r['message'] = 'Points updated';
										return $r;
										break;
									default:
										$r['error'] = 'Method not implemented';
										return $r;
								}
							}
							break;
						default:
							$r['error'] = 'Method not implemented';
							return $r;
					}
					break;
				default:
					$r['error'] = 'Method not implemented';
					return $r;
			}
			break;

		default:
			$r['error'] = 'Method not implemented';
			return $r;
	
	}
}

if( isset($_REQUEST['cp_api']) ){
	add_action('init', 'cp_api');
}
 
?>