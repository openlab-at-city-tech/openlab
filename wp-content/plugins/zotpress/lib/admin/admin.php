<?php

// ADMIN -----------------------------------------------------------------------------------------

function Zotpress_options()
{
    // Prevent access to users who are not editors
	if ( ! current_user_can('edit_others_posts') && ! is_admin() )
		wp_die( __('Only logged-in editors can access this page.', 'zotpress'), __('Zotpress: 403 Access Denied', 'zotpress'), array( 'response' => 403 ) );


	// +--------------------+
	// | Set up admin pages |
	// +--------------------+

    if (isset($_GET['setup'])) {
        include( dirname(__FILE__) . '/admin.setup.php' );
    } elseif (isset($_GET['accounts'])) {
        include( dirname(__FILE__) . '/admin.accounts.php' );
    } elseif (isset($_GET['options'])) {
        include( dirname(__FILE__) . '/admin.options.php' );
    } elseif (isset($_GET['help'])) {
        include( dirname(__FILE__) . '/admin.help.php' );
    } else {
        include( dirname(__FILE__) . '/admin.browse.php' );
    }
}


function zp_Get_Default_Style()
{
	$zp_default_style = "apa";
	if (get_option("Zotpress_DefaultStyle"))
		$zp_default_style = get_option("Zotpress_DefaultStyle");

	return $zp_default_style;
}


function Zotpress_process_accounts_AJAX()
{
	check_ajax_referer( 'zpAccountsAJAX_nonce_val', 'zpAccountsAJAX_nonce' );

	global $wpdb;

	include( dirname(__FILE__) . '/../request/request.functions.php' );

	$xml = "";


    // +-------------+
    // | ADD ACCOUNT |
    // +-------------+

	if ( isset($_GET['action_type'])
            && $_GET['action_type'] == "add_account" )
    {
        // Set up error array
        $errors =
			array(
				"api_user_id_blank"=>array(0,"<strong>User ID</strong> was left blank."),
				"api_user_id_format"=>array(0,"<strong>User ID</strong> was formatted incorrectly."),
				"public_key_blank"=>array(0,"<strong>Public Key</strong> was left blank."),
				"public_key_format"=>array(0,"<strong>Public Key</strong> was formatted incorrectly."),
				"nickname_format"=>array(0,"<strong>Nickname</strong> was formatted incorrectly.")
			);

        // ACCOUNT TYPE
        if ($_GET['account_type'] != "")
			$account_type = $_GET['account_type'] == "groups" ? "groups" : "users";
		else
			$account_type = "users";

        // API USER ID
        if ($_GET['api_user_id'] != "")
		    if (preg_match("/^\\d+\$/", $_GET['api_user_id']) == 1)
    				$api_user_id = htmlentities($_GET['api_user_id']);
			else
				$errors['api_user_id_format'][0] = 1;
		else
			$errors['api_user_id_blank'][0] = 1;

        // PUBLIC KEY
        $public_key = false;

        if ($_GET['public_key'] != "")
			if (preg_match("/^[0-9a-zA-Z]+$/", $_GET['public_key']) == 1) {
               $public_key = htmlentities(trim($_GET['public_key']));
            } elseif ($account_type == "users") {
               $errors['public_key_format'][0] = 1;
            } elseif ($account_type == "users") {
               $errors['public_key_blank'][0] = 1;
            }

        // NICKNAME
        $nickname = false;
        if (isset($_GET['nickname']) && trim($_GET['nickname']) != '')
			if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['nickname'])) == 1)
				$nickname = str_replace("'", "", str_replace(" ", "", trim(urldecode($_GET['nickname']))));
			else
				$errors['nickname_format'][0] = 1;

        // CHECK ERRORS
        $errorCheck = false;
        foreach ($errors as $field => $error)
        {
			if ( $error[0] == 1 ) {
				$errorCheck = true;
				break;
			}
		}

        // ADD ACCOUNT
        if ( $errorCheck == false )
        {
            $query = "INSERT INTO ".$wpdb->prefix."zotpress (account_type, api_user_id, public_key";
            if ($nickname) $query .= ", nickname";
            $query .= ") ";
            $query .= " VALUES ('%s', '%s', '%s'";
            // $query .= "VALUES ('$account_type', '$api_user_id', '$public_key'";
            // if ($nickname) $query .= ", '$nickname'";
            if ($nickname) $query .= ", '%s'";
            $query .= ")";

            $temp_arr = array( $account_type, $api_user_id, $public_key);
            if ($nickname) array_push($temp_arr, $nickname);

            // Insert new list item into the list:
            $wpdb->query(
                $wpdb->prepare(
                    $query,
                    $temp_arr
                )
            );

            // Display success XML
            $xml .= "<result success='true' api_user_id='".$api_user_id."' public_key='".$public_key."' />\n";
        }

        // DISPLAY ERRORS
        else
        {
            $xml .= "<result success='false' />\n";
            $xml .= "<citation>\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                if ($error[0] == 1)
                    $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
            $xml .= "</citation>\n";
        }
    }


    // +----------------+
    // | DELETE ACCOUNT |
    // +----------------+

    elseif ( isset($_GET['action_type'])
            && $_GET['action_type'] == "delete_account" )
    {
        if ( preg_match("/^\\d+\$/", $_GET['api_user_id']) )
        {
            $api_user_id = $_GET['api_user_id'];

            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM ".$wpdb->prefix."zotpress 
                    WHERE api_user_id='%s'",
                    array($api_user_id)
                )
            );
            zp_clear_cache_for_user ($wpdb, $api_user_id);

            // Check if default account
            if ( get_option("Zotpress_DefaultAccount") 
                    && get_option("Zotpress_DefaultAccount") == $api_user_id )
                delete_option( "Zotpress_DefaultAccount" );

            $total_accounts = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."zotpress;" );

            // Display success XML
            $xml .= "<result success='true' total_accounts='".$total_accounts."' />\n";
            $xml .= "<account id='".$api_user_id."' type='delete' />\n";

            $wpdb->flush();
            unset($api_user_id);
        }
        else // die
        {
            exit();
        }
    }


    // +-------------+
    // | CLEAR CACHE |
    // +-------------+

    elseif ( isset($_GET['action_type']) 
            && $_GET['action_type'] == "clear_cache" )
    {
        if ( preg_match("/^\\d+\$/", $_GET['api_user_id']) )
        {
            $api_user_id = $_GET['api_user_id'];

            // Clear the cache
            zp_clear_cache_for_user ($wpdb, $api_user_id);

            // Display success XML
            $xml .= "<result success='true' cache_cleared='true' />\n";
            $xml .= "<account id='".$api_user_id."' type='cache' />\n";

            $wpdb->flush();
            unset($api_user_id);
        }
        else // die
        {
            exit();
        }
    }


    // +---------------------+
    // | SET DEFAULT ACCOUNT |
    // +---------------------+

    elseif ( isset($_GET['action_type'])
            && $_GET['action_type'] == "default_account" )
    {
        $errors = array("account_empty"=>array(0,"<strong>Account</strong> was left blank."),
						"account_format"=>array(0,"<strong>Account</strong> was incorrectly formatted."));

        // Check the post variables and record errors
        if ( trim($_GET['api_user_id']) != '' )
            if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['api_user_id'])) == 1)
                $account = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['api_user_id']))));
            else
                $errors['account_format'][0] = 1;
        else
            $errors['account_empty'][0] = 1;

        // CHECK ERRORS
        $errorCheck = false;

        foreach ( $errors as $field => $error ) {
          if ( $error[0] == 1 ) {
              $errorCheck = true;
              break;
          }
        }

        // SET DEFAULT ACCOUNT
        if ( $errorCheck === false )
        {
            update_option( "Zotpress_DefaultAccount", $account );
            $xml .= "<result success='true' account='".$account."' />\n";
        }
    }


    // +-------------------+
    // | SET DEFAULT STYLE |
    // +-------------------+

    elseif ( isset($_GET['action_type'])
            && $_GET['action_type'] == "default_style" )
    {
        $errors = array("style_empty"=>array(0,"<strong>Style</strong> was left blank."),
						"style_format"=>array(0,"<strong>Style</strong> was incorrectly formatted."));

        // Check the post variables and record errors
        if ( trim($_GET['style']) != '' )
          if ( preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['style'])) == 1 )
              $style = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['style']))));
          else
              $errors['style_format'][0] = 1;
        else
          $errors['style_empty'][0] = 1;

        // CHECK ERRORS
        $errorCheck = false;

        foreach ($errors as $field => $error) {
          if ($error[0] == 1) {
              $errorCheck = true;
              break;
          }
        }

        // SET DEFAULT ACCOUNT
        if ( $errorCheck === false )
        {
          // Update style list
          if ( strpos(get_option("Zotpress_StyleList"), $style) === false )
              update_option( "Zotpress_StyleList", get_option("Zotpress_StyleList") . ", " . $style);

          // Update default style
          update_option("Zotpress_DefaultStyle", $style);
          $xml .= "<result success='true' style='".$style."' />\n";
        }
    }


    // +------------------------------+
    // | SET REFERENCE WIDGET FOR CPT |
    // +------------------------------+

    elseif ( isset($_GET['action_type'])
            && $_GET['action_type'] == "ref_widget_cpt" )
    {
        $errors = array("cpt_empty"=>array(0,"<strong>Content Type</strong> was left blank."),
						"cpt_format"=>array(0,"<strong>Content Type</strong> was incorrectly formatted."));

        // Check the post variables and record errors
        if ( trim($_GET['cpt']) != '' )
          if ( preg_match('/^[\'0-9a-zA-Z -_,]+$/', stripslashes($_GET['cpt'])) == 1 )
              $cpt = trim($_GET['cpt']);
          else
              $errors['cpt_format'][0] = 1;
        else
          $errors['cpt_empty'][0] = 1;

        // CHECK ERRORS
        $errorCheck = false;

        foreach ( $errors as $field => $error ) {
          if ( $error[0] == 1 ) {
              $errorCheck = true;
              break;
          }
        }

        // SET DEFAULT ACCOUNT
        if ( $errorCheck === false )
        {
          update_option("Zotpress_DefaultCPT", $cpt);
          $xml .= "<result success='true' cpt='".$cpt."' />\n";
        }
    }


    // +----------------+
    // | RESET ZOTPRESS |
    // +----------------+

    elseif ( isset($_GET['action_type'])
            && $_GET['action_type'] == "reset" )
    {
        $errors = array("reset_empty"=>array(0,"<strong>Reset</strong> was left blank."));

        // Check the post variables and record errors
        if ( trim($_GET['reset']) == 'true' )
          $reset = $_GET['reset'];
        else
          $errors['reset_empty'][0] = 1;

        // CHECK ERRORS
        $errorCheck = false;

        foreach ( $errors as $field => $error ) {
          if ( $error[0] == 1 ) {
              $errorCheck = true;
              break;
          }
        }

        if ( $errorCheck === false )
        {
          global $wpdb;
          global $current_user;

          // Drop all tables except accounts/main
          $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress;");
          $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_oauth;");
          $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItems;");
          $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroCollections;");
          $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroTags;");
          $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroRelItemColl;");
          $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroRelItemTags;");
          $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_cache ;");
          $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItemImages ;");

          delete_option( 'Zotpress_cache_version' );
          delete_option( 'Zotpress_DefaultCPT' );
          delete_option( 'Zotpress_DefaultAccount' );
          delete_option( 'Zotpress_DefaultEditor' );
          delete_option( 'Zotpress_DefaultStyle' );
          delete_option( 'Zotpress_StyleList' );
          delete_option( 'Zotpress_update_version' );
          delete_option( 'Zotpress_main_db_version' );
          delete_option( 'Zotpress_oauth_db_version' );
          delete_option( 'Zotpress_zoteroItems_db_version' );
          delete_option( 'Zotpress_zoteroCollections_db_version' );
          delete_option( 'Zotpress_zoteroTags_db_version' );
          delete_option( 'Zotpress_zoteroRelItemColl_db_version' );
          delete_option( 'Zotpress_zoteroRelItemTags_db_version' );
          delete_option( 'Zotpress_zoteroItemImages_db_version' );
          delete_option( 'Zotpress_update_notice_dismissed' );
          delete_option( 'Zotpress_zoteroItemImages_db_version' );

          delete_user_meta( $current_user->ID, 'zotpress_5_2_ignore_notice' );
          delete_user_meta( $current_user->ID, 'zotpress_survey_notice_ignore' );

          $xml .= "<result success='true' reset='complete' />\n";
        }
    }


    // +-----------+
    // | ADD IMAGE |
    // +-----------+

    elseif ( isset($_GET['action_type'])
        && $_GET['action_type'] == "add_image" )
    {
        // Set up error array
        $errors = array(
        			"item_key_blank"=>array(0,"<strong>Entry ID</strong> was left blank or formatted incorrectly."),
        			"image_id_blank"=>array(0,"<strong>Image ID</strong> was left blank or formatted incorrectly."),
        			"api_user_id_blank"=>array(0,"<strong>API User ID</strong> was left blank or formatted incorrectly.")
        		);

        // BASIC VARS
        $api_user_id = false;
        if ( preg_match("/^\\d+\$/", $_GET['api_user_id']) )
        			$api_user_id = htmlentities(trim($_GET['api_user_id']));
        		else
        			$errors['api_user_id_blank'][0] = 1;
        
        $item_key = false;
        if ( preg_match("/^[a-zA-Z0-9]+$/", $_GET['item_key']) )
        			$item_key = htmlentities(trim($_GET['item_key']));
        		else
        			$errors['item_key_blank'][0] = 1;
        
        $image_id = false;
        if ( preg_match("/^\\d+\$/", $_GET['image_id']) )
        			$image_id = htmlentities(trim($_GET['image_id']));
        		else
        			$errors['image_id_blank'][0] = 1;

        // CHECK ERRORS
        $errorCheck = false;

        foreach ( $errors as $field => $error ) {
        	if ( $error[0] == 1 ) {
        		$errorCheck = true;
        		break;
        	}
        }

        // SET FEATURED IMAGE
        if ( $errorCheck == false )
		{
			$wpdb->query(
				$wpdb->prepare(
                "
				INSERT INTO ".$wpdb->prefix."zotpress_zoteroItemImages (api_user_id, item_key, image)
				VALUES (%s, %s, %s)
				ON DUPLICATE KEY UPDATE image=%s
				",
					$api_user_id, $item_key, $image_id, $image_id
				)
			);

			$xml .= "<result success='true' citation_id='".$item_key."' />\n";
		}

		// DISPLAY ERRORS
		else
		{
			$xml .= "<result success='false' />\n";
			$xml .= "<citation>\n";
			$xml .= "<errors>\n";
			foreach ( $errors as $field => $error )
				if ( $error[0] == 1 )
					$xml .= $error[1]."\n";
			$xml .= "</errors>\n";
			$xml .= "</citation>\n";
		}
    }


    // +--------------+
    // | REMOVE IMAGE |
    // +--------------+

    elseif ( isset($_GET['action_type'])
            && $_GET['action_type'] == "remove_image" )
    {
		// Set up error array
		$errors = array(
			"item_key_blank"=>array(0,"<strong>Item Key</strong> was left blank or formatted incorrectly."),
			"api_user_id_blank"=>array(0,"<strong>API User ID</strong> was left blank or formatted incorrectly.")
		);

		// BASIC VARS
		$item_key = false;
		if ( preg_match("/^[A-Z0-9]+$/", $_GET['item_key']) )
			$item_key = htmlentities(trim($_GET['item_key']));
		else
			$errors['item_key_blank'][0] = 1;

		$api_user_id = false;
		if ( preg_match("/^[A-Z0-9]+$/", $_GET['api_user_id']) )
			$api_user_id = htmlentities(trim($_GET['api_user_id']));
		else
			$errors['api_user_id_blank'][0] = 1;

        // CHECK FOR ERRORS
        $errorCheck = false;
        foreach ( $errors as $field => $error ) {
			if ( $error[0] == 1 ) {
				$errorCheck = true;
				break;
			}
		}

        // REMOVE FEATURED IMAGE
        if ( $errorCheck == false )
		{
			$wpdb->query(
				$wpdb->prepare(
					"
				DELETE FROM ".$wpdb->prefix."zotpress_zoteroItemImages
				WHERE item_key=%s AND api_user_id=%s
				",
					$item_key, $api_user_id
				)
			);

			$xml .= "<result success='true' item_key='".$item_key."' />\n";
		}


		// +----------------+
		// | Display errors |
		// +----------------+

		else
		{
			$xml .= "<result success='false' />\n";
			$xml .= "<citation>\n";
			$xml .= "<errors>\n";
			foreach ( $errors as $field => $error )
				if ( $error[0] == 1 )
					$xml .= $error[1]."\n";
			$xml .= "</errors>\n";
			$xml .= "</citation>\n";
		}
    }


	// +-------------+
	// | Display XML |
	// +-------------+

	header('Content-Type: application/xml; charset=ISO-8859-1');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
	echo "<accounts>\n";
	echo $xml;
	echo "</accounts>";


	$wpdb->flush();

	exit();
}

add_action( 'wp_ajax_zpAccountsViaAJAX', 'Zotpress_process_accounts_AJAX' );

// ADMIN ------------------------------------------------------------------------------------------

?>
