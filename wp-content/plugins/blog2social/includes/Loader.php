<?php

class B2S_Loader {

    public $blogUserData;
    public $lastVersion;

    public function __construct() {
        
    }

    public function load() {
        if (!is_admin()) {
            $this->call_public_hooks();
        }
        $this->call_global_hooks();
        if (is_admin()) {
            $this->call_admin_hooks();
        }
    }

    public function call_global_hooks() {

        $this->b2s_register_custom_post_type();

        require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
        require_once(B2S_PLUGIN_DIR . 'includes/B2S/AutoPost.php');
        require_once(B2S_PLUGIN_DIR . 'includes/B2S/Rating.php');
        require_once(B2S_PLUGIN_DIR . 'includes/B2S/Heartbeat.php');
        require_once(B2S_PLUGIN_DIR . 'includes/B2S/Api/Post.php');
        require_once(B2S_PLUGIN_DIR . 'includes/B2S/Api/Get.php');
        require_once(B2S_PLUGIN_DIR . 'includes/Util.php');
        require_once(B2S_PLUGIN_DIR . 'includes/Tools.php');
        require_once(B2S_PLUGIN_DIR . 'includes/B2S/Hook/Filter.php');

        define('B2S_PLUGIN_POSTPERPAGE', '15');
        define('B2S_PLUGIN_VERSION_TYPE', serialize(array(0 => 'Free', 1 => 'Smart', 2 => 'Pro', 3 => 'Business', 4 => 'Premium')));
        define('B2S_PLUGIN_NETWORK', serialize(array(1 => 'Facebook', 2 => 'Twitter', 3 => 'Linkedin', 4 => 'Tumblr', 5 => 'Storify', 6 => 'Pinterest', 7 => 'Flickr', 8 => 'Xing', 9 => 'Diigo', 10 => 'Google+', 11 => 'Medium', 12 => 'Instagram', 13 => 'Delicious', 14 => 'Torial', 15 => 'Reddit', 16 => 'Bloglovin', 17 => 'VKontakte', 18 => 'Google My Business', 19 => 'Xing', 20 => 'Pinterest', 21 => 'Imgur', 24 => 'Telegram')));
        define('B2S_PLUGIN_SCHED_DEFAULT_TIMES', serialize(array(1 => array(18, 22), 2 => array(8, 10), 3 => array(7, 10), 4 => array(20, 22), 5 => array(), 6 => array(19, 22), 7 => array(7, 9), 8 => array(7, 10), 9 => array(16, 19), 10 => array(7, 10), 11 => array(8, 10), 12 => array(19, 22), 13 => array(11, 13), 14 => array(6, 8), 15 => array(6, 9), 16 => array(16, 19), 17 => array(21, 24), 18 => array(19, 21), 19 => array(7, 10), 24 => array(10, 12))));
        define('B2S_PLUGIN_SCHED_DEFAULT_TIMES_INFO', serialize(array(1 => array(0 => array(13, 16), 1 => array(18, 22)), 2 => array(0 => array(7, 10), 1 => array(11, 13), 2 => array(16, 19)), 3 => array(0 => array(7, 10), 1 => array(17, 18)), 4 => array(0 => array(20, 22)), 5 => array(), 6 => array(0 => array(12, 14), 1 => array(19, 22)), 7 => array(0 => array(7, 9), 1 => array(17, 19)), 8 => array(0 => array(7, 10), 1 => array(17, 18)), 9 => array(0 => array(8, 10), 1 => array(11, 13), 2 => array(16, 19)), 10 => array(0 => array(7, 10), 1 => array(14, 15)), 11 => array(0 => array(8, 10)), 12 => array(0 => array(11, 13), 1 => array(19, 22)), 13 => array(0 => array(8, 10), 1 => array(11, 13), 2 => array(16, 19)), 14 => array(0 => array(6, 8)), 15 => array(0 => array(6, 9)), 16 => array(0 => array(16, 19)), 17 => array(0 => array(21, 1)), 18 => array(0 => array(9, 11), 1 => array(19, 21)), 19 => array(0 => array(7, 10), 1 => array(17, 18)), 20 => array(0 => array(12, 14), 1 => array(19, 22)), 24 => array(0 => array(10, 12), 1 => array(17, 19)))));
        define('B2S_PLUGIN_NETWORK_ALLOW_PROFILE', serialize(array(1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24)));
        define('B2S_PLUGIN_NETWORK_ALLOW_PAGE', serialize(array(1, 3, 10, 12, 17, 19)));
        define('B2S_PLUGIN_NETWORK_ALLOW_GROUP', serialize(array(1, 10, 11, 17, 19)));
        define('B2S_PLUGIN_NETWORK_CROSSPOSTING_LIMIT', serialize(array(19 => array(2 => 3)))); //2=group
        define('B2S_PLUGIN_NETWORK_ALLOW_MODIFY_BOARD_AND_GROUP', serialize(array(6 => array('TYPE' => array(0), 'TITLE' => esc_html__('Modify pin board', 'blog2social')), 8 => array('TYPE' => array(2), 'TITLE' => esc_html__('Edit group settings', 'blog2social')), 15 => array('TYPE' => array(0), 'TITLE' => esc_html__('Modify subreddit', 'blog2social')), 19 => array('TYPE' => array(2), 'TITLE' => esc_html__('Modify forum', 'blog2social')), 20 => array('TYPE' => array(0), 'TITLE' => esc_html__('Modify pin board', 'blog2social')))));
        define('B2S_PLUGIN_AUTO_POST_LIMIT', serialize(array(0 => 0, 1 => 25, 2 => 50, 3 => 100, 4 => 100)));
        define('B2S_PLUGIN_RE_POST_LIMIT', serialize(array(0 => 0, 1 => 25, 2 => 50, 3 => 100, 4 => 100)));
        define('B2S_PLUGIN_NETWORK_OAUTH', serialize(array(1, 2, 3, 4, 7, 8, 11, 15, 17, 18, 20, 21)));
        define('B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT', serialize(array(
            1 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 400, 'excerpt_range_min' => 200, 'excerpt_range_max' => 400, 'limit' => 500), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => 0, 'addLink' => false),
                1 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 400, 'excerpt_range_min' => 200, 'excerpt_range_max' => 400, 'limit' => 0), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => 0, 'addLink' => false),
                2 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 400, 'excerpt_range_min' => 200, 'excerpt_range_max' => 400, 'limit' => 0), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => 0, 'addLink' => false)
            ),
            2 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 254, 'excerpt_range_min' => 200, 'excerpt_range_max' => 254, 'limit' => 280), 'content' => '{CONTENT} {KEYWORDS}', 'format' => 1, 'addLink' => true)),
            3 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 400, 'excerpt_range_min' => 200, 'excerpt_range_max' => 400, 'limit' => 1300), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => 0),
                1 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 400, 'excerpt_range_min' => 200, 'excerpt_range_max' => 400, 'limit' => 1300), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => 0),
            ),
            4 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 1000, 'range_max' => 20000, 'excerpt_range_min' => 1000, 'excerpt_range_max' => 20000, 'limit' => 0), 'content' => '{CONTENT}', 'format' => false, 'disableKeywords' => true)),
            6 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 350, 'range_max' => 421, 'excerpt_range_min' => 350, 'excerpt_range_max' => 421, 'limit' => 495), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => false)),
            7 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 1000, 'range_max' => 1500, 'excerpt_range_min' => 1000, 'excerpt_range_max' => 1500, 'limit' => 0), 'content' => '{CONTENT}', 'format' => false, 'disableKeywords' => true)),
            9 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 250, 'excerpt_range_min' => 200, 'excerpt_range_max' => 250, 'limit' => 250), 'content' => '{CONTENT}', 'format' => false, 'disableKeywords' => true)),
            11 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 1000, 'range_max' => 20000, 'excerpt_range_min' => 1000, 'excerpt_range_max' => 20000, 'limit' => 0), 'content' => '{CONTENT}', 'format' => false, 'separateKeywords' => true),
                2 => array('short_text' => array('active' => 0, 'range_min' => 1000, 'range_max' => 20000, 'excerpt_range_min' => 1000, 'excerpt_range_max' => 20000, 'limit' => 0), 'content' => '{CONTENT}', 'format' => false, 'separateKeywords' => true)),
            12 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 400, 'excerpt_range_min' => 240, 'excerpt_range_max' => 400, 'limit' => 2000), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => 1, 'addLink' => false, 'shuffleHashtags' => false),
                1 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 400, 'excerpt_range_min' => 240, 'excerpt_range_max' => 400, 'limit' => 2000), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => 1, 'addLink' => false, 'shuffleHashtags' => false)),
            14 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 1000, 'range_max' => 20000, 'excerpt_range_min' => 1000, 'excerpt_range_max' => 20000, 'limit' => 0), 'content' => '{CONTENT}', 'format' => false, 'disableKeywords' => true)),
            15 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 300, 'excerpt_range_min' => 200, 'excerpt_range_max' => 300, 'limit' => 0), 'content' => '{CONTENT}', 'format' => false, 'disableKeywords' => true)),
            16 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 1000, 'range_max' => 1500, 'excerpt_range_min' => 1000, 'excerpt_range_max' => 1500, 'limit' => 0), 'content' => '{CONTENT}', 'format' => false, 'disableKeywords' => true)),
            17 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 1000, 'range_max' => 1500, 'excerpt_range_min' => 1000, 'excerpt_range_max' => 1500, 'limit' => 0), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => 0),
                1 => array('short_text' => array('active' => 0, 'range_min' => 1000, 'range_max' => 1500, 'excerpt_range_min' => 1000, 'excerpt_range_max' => 1500, 'limit' => 0), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => 0),
                2 => array('short_text' => array('active' => 0, 'range_min' => 1000, 'range_max' => 1500, 'excerpt_range_min' => 1000, 'excerpt_range_max' => 1500, 'limit' => 0), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => 0)),
            18 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 1000, 'range_max' => 1500, 'excerpt_range_min' => 1000, 'excerpt_range_max' => 1500, 'limit' => 1500), 'content' => "{CONTENT}\n{KEYWORDS}", 'format' => false)),
            19 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 320, 'excerpt_range_min' => 200, 'excerpt_range_max' => 320, 'limit' => 1000), 'content' => '{CONTENT}', 'format' => 0, 'disableKeywords' => true),
                1 => array('short_text' => array(0 => array('active' => 0, 'range_min' => 200, 'range_max' => 320, 'excerpt_range_min' => 200, 'excerpt_range_max' => 320, 'limit' => 1000), 4 => array('active' => 0, 'range_min' => 880, 'range_max' => 920, 'excerpt_range_min' => 880, 'excerpt_range_max' => 920, 'limit' => 1000)), 'content' => '{CONTENT}', 'format' => 1, 'disableKeywords' => true),
                2 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 320, 'excerpt_range_min' => 200, 'excerpt_range_max' => 320, 'limit' => 10000), 'content' => '{CONTENT}', 'format' => 1, 'disableKeywords' => true)),
            24 => array(0 => array('short_text' => array('active' => 0, 'range_min' => 200, 'range_max' => 320, 'excerpt_range_min' => 200, 'excerpt_range_max' => 320, 'limit' => 420), 'content' => '{CONTENT}', 'format' => false, 'addLink' => true))
        )));
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_WORDPRESSVERSION', '4.7.0');
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_PHPVERSION', '5.5.3');
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_MYSQLVERSION', '5.5.3');
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_DATABASERIGHTS', true);
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_HEARTBEAT', true);
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_PHPCURL', true);
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_PHPMBSTRING', true);
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_PHPDOM', true);
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_HOTLINKPROTECTION', true);
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_PLUGINWARNING_WORDS', serialize(array('hotlink', 'firewall', 'security', 'heartbeat', 'disable')));
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_WPJSON', true);
        define('B2S_PLUGIN_SYSTEMREQUIREMENT_OPENSSL', true);
        define('B2S_PLUGIN_PAGE_SLUG', serialize(array('blog2social', 'blog2social-post', 'blog2social-calendar', 'blog2social-curation', 'blog2social-network', 'blog2social-settings', 'prg-post', 'blog2social-support', 'blog2social-premium', 'blog2social-sched', 'blog2social-approve', 'blog2social-publish', 'blog2social-notice', 'blog2social-ship', 'blog2social-curation-draft', 'blog2social-draft-post', 'prg-login', 'prg-ship')));
        define('B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF', json_encode(array(16, 18)));
        define('B2S_PLUGIN_NETWORK_ANIMATE_GIF', json_encode(array(1 => array(0 => false, 1 => true), 2 => array(0 => false, 1 => true), 3 => array(0 => true, 1 => true), 4 => array(0 => true), 6 => array(0 => true), 7 => array(0 => false), 11 => array(0 => true), 12 => array(0 => false, 1 => false), 14 => array(0 => true), 15 => array(0 => false), 17 => array(0 => false), 19 => array(0 => true, 1 => true), 21 => array(0 => true), 24 => array(0 => true))));
        define('B2S_PLUGIN_NETWORK_META_TAGS', json_encode(array('og' => array(1, 3, 15, 19, 17), 'twitter' => array(2))));
        define('B2S_PLUGIN_SHORTENER', serialize(array(0 => esc_html__('Bitly', 'blog2social'), 1 => esc_html__('Rebrandly', 'blog2social'), 2 => esc_html__('Sniply', 'blog2social'))));

        add_filter('heartbeat_received', array(B2S_Heartbeat::getInstance(), 'init'), 10, 2);
        add_action('wp_logout', array($this, 'releaseLocks'));
        add_action('transition_post_status', array($this, 'b2s_auto_post_import'), 9999, 3); //for Auto-Posting imported + manuell
        //add_action('rest_api_inserted_post', array($this, 'b2s_auto_post_import_rest_api_jetpack'), 9999, 3); //call via transition_post_status hook
        //add_action('rest_api_init', array($this, 'b2s_rest_api_init'));
    }

    public function call_admin_hooks() {

        require_once(B2S_PLUGIN_DIR . 'includes/Meta.php');
        require_once(B2S_PLUGIN_DIR . 'includes/B2S/PostBox.php');
        require_once(B2S_PLUGIN_DIR . 'includes/Notice.php');
        require_once(B2S_PLUGIN_DIR . 'includes/PRG/Api/Post.php');
        require_once(B2S_PLUGIN_DIR . 'includes/PRG/Api/Get.php');
        require_once(B2S_PLUGIN_DIR . 'includes/Ajax/Post.php');
        require_once(B2S_PLUGIN_DIR . 'includes/Ajax/Get.php');
        require_once(B2S_PLUGIN_DIR . 'includes/B2S/AutoPost.php');
        require_once(B2S_PLUGIN_DIR . 'includes/B2S/Rating.php');
        require_once(B2S_PLUGIN_DIR . 'includes/Util.php');
        require_once(B2S_PLUGIN_DIR . 'includes/Tools.php');

        define('B2S_PLUGIN_BLOG_USER_ID', get_current_user_id());
        define('B2S_PLUGIN_ADMIN', current_user_can('edit_others_posts'));

        $this->blogUserData = get_userdata(B2S_PLUGIN_BLOG_USER_ID);

        //deactivated since 4.2.0
        //add_action('plugins_loaded', array($this, 'update_db_check'));

        $this->update_db_check();

        add_action('admin_init', array($this, 'registerAssets'));
        add_action('admin_enqueue_scripts', array($this, 'addBootAssets'));
        add_action('admin_menu', array($this, 'createMenu'));
        add_action('admin_bar_menu', array($this, 'createToolbarMenu'), 94);
        add_action('admin_notices', array('B2S_Notice', 'getProVersionNotice'));
        add_action('wp_loaded', array('B2S_Notice', 'hideProVersionNotice'));
        add_action('admin_notices', array($this, 'b2s_save_post_alert_meta_box'));
        add_action('add_meta_boxes', array($this, 'b2s_load_post_box'));
        add_action('save_post', array($this, 'b2s_save_post_box'), 1, 3);
        add_action('trash_post', array($this, 'b2s_delete_sched_post'), 10);
        add_action('wp_trash_post', array($this, 'b2s_delete_sched_post'), 10);


        add_action('admin_footer', array($this, 'plugin_deactivate_add_modal'));
        add_filter('plugin_action_links_' . B2S_PLUGIN_BASENAME, array($this, 'override_plugin_action_links'));
        add_filter('network_admin_plugin_action_links_' . B2S_PLUGIN_BASENAME, array($this, 'override_multisite_plugin_action_links'));

        Ajax_Get::getInstance();
        Ajax_Post::getInstance();

        if ((int) B2S_PLUGIN_BLOG_USER_ID > 0) {
            $this->getToken();
            $this->getUserDetails();
        }
        $this->plugin_init_language();
    }

    public function call_public_hooks() {
        add_filter('wp_footer', array($this, 'b2s_get_full_content'), 99); //for shortcodes
        add_action('wp_head', array($this, 'b2s_build_frontend_meta'), 1); // for MetaTags
    }

    public function b2s_build_frontend_meta() {
        require_once(B2S_PLUGIN_DIR . 'includes/Meta.php');
        B2S_Meta::getInstance()->_run();
    }

    /* public function b2s_rest_api_init() {
      register_rest_route('blog2social/v1/post', '/authorize', array(
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => array($this, 'b2s_rest_api_post_authorize')
      ));
      } */

    /* public function b2s_rest_api_post_authorize($post = array()) {
      $contentType = 'application/json';
      if (isset($post['token']) && !empty($post['token']) && isset($post['user']) && !empty($post['user']) && isset($post['network_id']) && (int) $post['network_id'] > 0) {
      $privateKey = B2S_PLUGIN_DIR . '/includes/B2S/Api/Network/private_key.pem';
      if (function_exists('openssl_public_decrypt') && file_exists($privateKey)) {
      if ((int) $post['network_id'] == 6) {
      try {
      require_once(B2S_PLUGIN_DIR . 'includes/B2S/Api/Network/Pinterest.php');
      $user = '';
      $pass = '';
      openssl_private_decrypt(base64_decode(trim($post['user']['pass'])), $pass, $getPrivateKey);
      openssl_private_decrypt(base64_decode(trim($post['user']['name'])), $user, $getPrivateKey);
      $pt = new B2S_Api_Network_Pinterest();
      $result = $pt->authorize($user, $pass);
      $status = isset($result['cookie_data']) && !empty($result['cookie_data']) ? 200 : 400;
      $response = array('code' => $status, 'result' => array_merge($result, array('token' => $post['token'], 'network_id' => (int) $post['network_id'])));
      return new WP_REST_Response($response, $status, array('Content-Type: ' . $contentType));
      } catch (Exception $ex) {
      $response = array('code' => 500, 'result' => array('token' => $post['token'], 'network_id' => (int) $post['network_id'], 'error' => 1, 'error_pos' => 8, 'error_data' => serialize($ex->getMessage())));
      return new WP_REST_Response($response, 500, array('Content-Type: ' . $contentType));
      }
      }
      $response = array('code' => 409, 'result' => array('token' => $post['token'], 'network_id' => (int) $post['network_id'], 'error' => 1, 'error_pos' => 9, 'error_data' => 'network_not_exists'));
      return new WP_REST_Response($response, 409, array('Content-Type: ' . $contentType));
      }
      $response = array('code' => 501, 'result' => array('token' => $post['token'], 'network_id' => (int) $post['network_id'], 'error' => 1, 'error_pos' => 9, 'error_data' => 'openssl_not_exists'));
      return new WP_REST_Response($response, 501, array('Content-Type: ' . $contentType));
      }
      $response = array('code' => 502, 'result' => array('error' => 1, 'error_pos' => 10, 'error_data' => 'bad_request'));
      return new WP_REST_Response($response, 502, array('Content-Type: ' . $contentType));
      } */

    private function b2s_register_custom_post_type() {
        if (post_type_exists("b2s_ex_post")) {
            return;
        }
        register_post_type('b2s_ex_post', array('public' => false, 'label' => 'Related Posts for Blog2Social'));
    }

    public function plugin_deactivate_add_modal() {
        include_once(B2S_PLUGIN_DIR . '/views/b2s/partials/plugin-deactivate-modal.php');
    }

    //Insert Posts via wordpress.com (mobile app) & jetpack
    //https://developer.jetpack.com/hooks/rest_api_inserted_post/
    //NOTICE: jetpack call transition_post_status hook
    /* public function b2s_auto_post_import_rest_api_jetpack($post_id, $insert, $new) {
      if ((int) $post_id > 0 && is_array($insert) && isset($insert['post_status']) && $insert['post_status'] == 'publish') {
      $post = get_post((int) $post_id);
      $this->b2s_auto_post_import($insert['post_status'], 'draft', $post);
      }
      } */

    public function b2s_auto_post_import($new_status, $old_status, $post) {
        //is first publish
        if ($old_status != 'publish' && $old_status != 'trash' && $new_status == 'publish' && isset($post->post_author) && (int) $post->post_author > 0) {
            if (wp_is_post_revision($post->ID)) {
                return;
            }
            //is lock if manuell Auto-Posting in form
            $isLock = get_option('B2S_LOCK_AUTO_POST_IMPORT_' . (int) $post->post_author);
            if ($isLock === false) {
                $options = new B2S_Options((int) $post->post_author);
                $autoPostData = $options->_getOption('auto_post_import');
                if ($autoPostData !== false && is_array($autoPostData)) {
                    if (isset($autoPostData['active']) && (int) $autoPostData['active'] == 1) {
                        //Premium
                        $tokenInfo = get_option('B2S_PLUGIN_USER_VERSION_' . (int) $post->post_author);
                        if ($tokenInfo !== false && isset($tokenInfo['B2S_PLUGIN_USER_VERSION']) && (int) $tokenInfo['B2S_PLUGIN_USER_VERSION'] >= 1) {
                            $filter = true;
                            if (isset($autoPostData['post_filter']) && (int) $autoPostData['post_filter'] == 1) {
                                if (isset($autoPostData['post_type']) && is_array($autoPostData['post_type']) && !empty($autoPostData['post_type'])) {
                                    if (isset($autoPostData['post_type_state']) && (int) $autoPostData['post_type_state'] == 0) { //include
                                        if (!in_array($post->post_type, $autoPostData['post_type'])) {
                                            $filter = false;
                                        }
                                    } else { //exclude
                                        if (in_array($post->post_type, $autoPostData['post_type'])) {
                                            $filter = false;
                                        }
                                    }
                                }
                            }
                            if ($filter && isset($autoPostData['network_auth_id']) && !empty($autoPostData['network_auth_id']) && is_array($autoPostData['network_auth_id'])) {
                                //LIMIT
                                $limit = false;
                                $ship = false;
                                $count = 0;
                                $optionUserTimeZone = $options->_getOption('user_time_zone');
                                $userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
                                $userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
                                $current_utc_datetime = gmdate('Y-m-d H:i:s');
                                $current_user_date = date('Y-m-d', strtotime(B2S_Util::getUTCForDate($current_utc_datetime, $userTimeZoneOffset)));
                                $userVersion = (int) $tokenInfo['B2S_PLUGIN_USER_VERSION'];

                                $autoPostCon = $options->_getOption('auto_post_import_condition');
                                $conData = array();
                                if ($autoPostCon !== false && is_array($autoPostCon) && isset($autoPostCon['count']) && isset($autoPostCon['last_call_date'])) {
                                    $con = unserialize(B2S_PLUGIN_AUTO_POST_LIMIT);
                                    $limitCount = (isset($con[$userVersion]) && !empty($con[$userVersion])) ? $con[$userVersion] : $con[1]; //25 default
                                    if (($autoPostCon['count'] < $limitCount) || ($current_user_date != $autoPostCon['last_call_date'])) {
                                        $limit = true;
                                        $count = ($current_user_date != $autoPostCon['last_call_date']) ? 1 : $autoPostCon['count'] + 1;
                                        $conData = array('count' => $count, 'last_call_date' => $current_user_date);
                                    }
                                } else {
                                    $limit = true;
                                    $count = 1;
                                    $conData = array('count' => $count, 'last_call_date' => $current_user_date);
                                }
                                if (!empty($conData)) {
                                    $options->_setOption('auto_post_import_condition', $conData);
                                }

                                if ($limit) {
                                    global $wpdb;
                                    $hook_filter = new B2S_Hook_Filter();
                                    $optionPostFormat = $options->_getOption('post_template');
                                    $url = get_permalink($post->ID);
                                    $title = isset($post->post_title) ? B2S_Util::getTitleByLanguage(strip_tags($post->post_title)) : '';
                                    $keywords = $hook_filter->get_wp_post_hashtag((int) $post->ID, $post->post_type);
                                    if (($keywords == false || empty($keywords)) && is_plugin_active('wp-automatic/wp-automatic.php')) {
                                        $keywords = B2S_Tools::extractKeywords($title);
                                    }
                                    $content = (isset($post->post_content) && !empty($post->post_content)) ? trim($post->post_content) : '';
                                    $excerpt = (isset($post->post_excerpt) && !empty($post->post_excerpt)) ? trim($post->post_excerpt) : '';
                                    $images_urls = $hook_filter->get_wp_post_image((int) $post->ID, true, $content);
                                    $image_url = ((!empty($images_urls) && isset(array_values($images_urls)[0][0])) ? array_values($images_urls)[0][0] : false);
                                    $delay = (isset($autoPostData['ship_state']) && (int) $autoPostData['ship_state'] = 0) ? 0 : (isset($autoPostData['ship_delay_time']) ? (int) $autoPostData['ship_delay_time'] : 0);
                                    $current_user_datetime = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($current_utc_datetime, $userTimeZoneOffset)));

//ShareNow
                                    $sched_type = 3;
                                    $time = ($delay == 0) ? "-30 seconds" : "+" . $delay . " minutes";
                                    $sched_date = date('Y-m-d H:i:s', strtotime($time, strtotime($current_user_datetime)));
                                    $sched_date_utc = date('Y-m-d H:i:s', strtotime($time, strtotime($current_utc_datetime)));

                                    $defaultPostData = array('default_titel' => $title,
                                        'image_url' => ($image_url !== false) ? trim(urldecode($image_url)) : '',
                                        'lang' => trim(strtolower(substr(B2S_LANGUAGE, 0, 2))),
                                        'no_cache' => 0, //default inactive , 1=active 0=not
                                        'board' => '', 'group' => '', 'url' => $url, 'user_timezone' => $userTimeZoneOffset);

                                    $defaultBlogPostData = array('post_id' => (int) $post->ID, 'blog_user_id' => (int) $post->post_author, 'user_timezone' => $userTimeZoneOffset, 'sched_type' => $sched_type, 'sched_date' => $sched_date, 'sched_date_utc' => $sched_date_utc);

                                    $autoShare = new B2S_AutoPost((int) $post->ID, $defaultBlogPostData, $current_user_date, false, $title, $content, $excerpt, $url, $image_url, $keywords, trim(strtolower(substr(B2S_LANGUAGE, 0, 2))), $optionPostFormat, true, $userVersion);

//TOS Twitter 032018 - none multiple Accounts - User select once
                                    $networkTos = true;

                                    foreach ($autoPostData['network_auth_id'] as $k => $value) {
                                        $networkDetails = $wpdb->get_results($wpdb->prepare("SELECT postNetworkDetails.network_id, postNetworkDetails.network_type, postNetworkDetails.network_display_name FROM {$wpdb->prefix}b2s_posts_network_details AS postNetworkDetails WHERE postNetworkDetails.network_auth_id = %s", $value));
                                        if ((int) $networkDetails[0]->network_id == 1 || (int) $networkDetails[0]->network_id == 3 || (int) $networkDetails[0]->network_id == 19) {
                                            $linkNoCache = B2S_Tools::getNoCacheData((int) $post->post_author);
                                            if (is_array($linkNoCache) && isset($linkNoCache[$networkDetails[0]->network_id]) && (int) $linkNoCache[$networkDetails[0]->network_id] > 0) {
                                                $defaultPostData['no_cache'] = $linkNoCache[$networkDetails[0]->network_id];
                                            }
                                        }
                                        if (is_array($networkDetails) && isset($networkDetails[0]->network_id) && isset($networkDetails[0]->network_type) && isset($networkDetails[0]->network_display_name)) {
                                            //TOS Twitter 032018 - none multiple Accounts - User select once
                                            if ((int) $networkDetails[0]->network_id != 2 || ( (int) $networkDetails[0]->network_id == 2 && $networkTos)) {
                                                //at first: set one profile
                                                if ((int) $networkDetails[0]->network_id == 2) {
                                                    $networkTos = false;
                                                }
                                                $res = $autoShare->prepareShareData($value, $networkDetails[0]->network_id, $networkDetails[0]->network_type);
                                                if ($res !== false && is_array($res)) {
                                                    $ship = true;
                                                    $res = array_merge($res, $defaultPostData);
                                                    $autoShare->saveShareData($res, $networkDetails[0]->network_id, $networkDetails[0]->network_type, $value, 0, strip_tags($networkDetails[0]->network_display_name));
                                                }
                                            }
                                        }
                                    }
                                    if ($ship) {
                                        B2S_Heartbeat::getInstance()->postToServer();
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                //Unlock Auto-Post-Import
                delete_option('B2S_LOCK_AUTO_POST_IMPORT_' . (int) $post->post_author);
            }
        }
    }

    public function update_db_check() {
        $this->lastVersion = get_option('b2s_plugin_version');
        if ($this->lastVersion == false || (int) $this->lastVersion < B2S_PLUGIN_VERSION) {
            $this->activatePlugin();
            update_option('b2s_plugin_version', B2S_PLUGIN_VERSION, false);
        }
    }

    public function b2s_delete_sched_post($post_id) {
        wp_enqueue_script('B2SPOSTSCHEDHEARTBEATJS');
        if ((int) $post_id > 0) {
            global $wpdb;
            //Heartbeat => b2s_delete_sched_post
            $sql = "SELECT id, post_for_approve FROM {$wpdb->prefix}b2s_posts WHERE post_id = %d AND hook_action <= %d AND hide = %d AND sched_date_utc != %s AND publish_date = %s";
            $deleteData = $wpdb->get_results($wpdb->prepare($sql, $post_id, 2, 0, "0000-00-00 00:00:00", "0000-00-00 00:00:00"), ARRAY_A);
            if (is_array($deleteData) && !empty($deleteData) && isset($deleteData[0])) {
                foreach ($deleteData as $k => $value) {
                    if ((int) $value['id'] > 0) {
                        if ((int) $value['post_for_approve'] == 1) {
                            $data = array('hook_action' => '0', 'hide' => 1);
                        } else {
                            $data = array('hook_action' => '3', 'hide' => 1);
                        }
                        $where = array('id' => (int) $value['id']);
                        $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));
                    }
                }
            }
        }
    }

    public function b2s_get_full_content() {
        if (isset($_GET['b2s_get_full_content'])) {
            $b2sPostContent = do_shortcode(get_the_content());
            $b2sPostId = get_the_ID();
            update_option('B2S_PLUGIN_POST_CONTENT_' . $b2sPostId, $b2sPostContent, false);
        }
    }

    public function b2s_load_post_box() {
        if (defined("B2S_PLUGIN_TOKEN")) {
            $post_types = get_post_types(array('public' => true));
            if (is_array($post_types) && !empty($post_types)) {
                foreach ($post_types as $post_type) {
                    if ($post_type != 'attachment' && $post_type != 'nav_menu_item') {
                        add_meta_box('b2s-post-meta-box-auto', esc_html__('Blog2Social: Autoposter', 'blog2social'), array($this, 'b2s_view_post_box'), $post_type, 'side', 'high');
                        add_meta_box('b2s-post-box-calendar-header', esc_html__('Blog2Social: Social Media Content Calendar', 'blog2social'), array($this, 'b2s_view_post_box_calendar'), $post_type, 'normal', 'high');
                    }
                }
            }
        }
    }

    public function b2s_view_post_box() {
        wp_enqueue_style('B2SAIRDATEPICKERCSS');
        wp_enqueue_style('B2SPOSTBOXCSS');
        wp_enqueue_script('B2SAIRDATEPICKERJS');
        wp_enqueue_script('B2SAIRDATEPICKERDEJS');
        wp_enqueue_script('B2SAIRDATEPICKERENJS');
        wp_enqueue_script('B2SPOSTBOXJS');

        wp_nonce_field("b2s-meta-box-nonce-post-area", "b2s-meta-box-nonce");
        wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
        $postId = (isset($_GET['post']) && (int) $_GET['post'] > 0) ? (int) $_GET['post'] : 0;
        $postType = (isset($_GET['post_type']) && !empty($_GET['post_type'])) ? sanitize_text_field($_GET['post_type']) : get_post_type($postId);
        $postStatus = ($postId != 0) ? get_post_status($postId) : '';
        $postBox = new B2S_PostBox();
        echo $postBox->getPostBox($postId, $postType, $postStatus);
    }

    public function b2s_view_post_box_calendar() {
        wp_enqueue_style('B2SFULLCALLENDARCSS');
        wp_enqueue_style('B2SCALENDARCSS');
        wp_enqueue_script('B2SFULLCALENDARMOMENTJS');
        wp_enqueue_script('B2SFULLCALENDARJS');
        wp_enqueue_script('B2SFULLCALENDARLOCALEJS');
        wp_enqueue_script('B2SLIB');
        echo '<div class="b2s-post-box-calendar-content"></div>';
    }

    public function b2s_save_post_box() {

        if (!isset($_POST['wphb-clear-cache'])) {  // WP-Hummingbird  BTN clear cache - protection
            if (!isset($_POST['wp-preview']) || (isset($_POST['wp-preview']) && $_POST['wp-preview'] != 'dopreview')) {
                if (isset($_POST['post_ID']) && (int) $_POST['post_ID'] > 0) {

                    //Gutenberg WP V5.0 - B2S V5.1.0 optimization
                    if (!isset($_POST['post_title']) || !isset($_POST['content'])) {
                        $content = get_post((int) $_POST['post_ID']);
                        if (!isset($_POST['post_title'])) {
                            $_POST['post_title'] = $content->post_title;
                        }
                        if (!isset($_POST['content'])) {
                            $_POST['content'] = $content->post_content;
                        }
                    }
                    $hook_filter = new B2S_Hook_Filter();
                    $b2sPostLang = (isset($_POST['b2s-user-lang']) && !empty($_POST['b2s-user-lang'])) ? sanitize_text_field($_POST['b2s-user-lang']) : 'en';
                    //OgMeta
                    if (isset($_POST['isOgMetaChecked']) && (int) $_POST['isOgMetaChecked'] == 1 && (int) $_POST['post_ID'] > 0 && isset($_POST['content']) && isset($_POST['post_title'])) {
                        $meta = B2S_Meta::getInstance();
                        $meta->getMeta(((int) $_POST['post_ID']));
                        $title = B2S_Util::getTitleByLanguage(sanitize_text_field($_POST['post_title']), strtolower($b2sPostLang));
                        if (has_excerpt((int) $_POST['post_ID'])) {
                            $desc = sanitize_textarea_field(get_the_excerpt());
                        } else {
                            $desc = str_replace("\r\n", ' ', substr(sanitize_textarea_field(strip_shortcodes($_POST['content'])), 0, 160));
                        }
                        $images_urls = $hook_filter->get_wp_post_image((int) $_POST['post_ID'], true, ((isset($_POST['content']) && !empty($_POST['content'])) ? trim($_POST['content']) : ''));
                        $image_url = ((!empty($images_urls) && isset(array_values($images_urls)[0][0])) ? array_values($images_urls)[0][0] : false);
                        $meta->setMeta('og_title', $title);
                        $meta->setMeta('og_desc', $desc);
                        $meta->setMeta('og_image', (($image_url !== false) ? trim(esc_url(urldecode($image_url))) : ''));
                        $meta->updateMeta((int) $_POST['post_ID']);
                    }

                    //CardMeta
                    if (isset($_POST['isCardMetaChecked']) && (int) $_POST['isCardMetaChecked'] == 1 && (int) $_POST['post_ID'] > 0 && isset($_POST['content']) && isset($_POST['post_title'])) {
                        $meta = B2S_Meta::getInstance();
                        $meta->getMeta(((int) $_POST['post_ID']));
                        $title = B2S_Util::getTitleByLanguage(sanitize_text_field($_POST['post_title']), strtolower($b2sPostLang));
                        if (has_excerpt((int) $_POST['post_ID'])) {
                            $desc = sanitize_textarea_field(get_the_excerpt());
                        } else {
                            $desc = str_replace("\r\n", ' ', substr(sanitize_textarea_field(strip_shortcodes($_POST['content'])), 0, 160));
                        }
                        $images_urls = $hook_filter->get_wp_post_image((int) $_POST['post_ID'], true, ((isset($_POST['content']) && !empty($_POST['content'])) ? trim($_POST['content']) : ''));
                        $image_url = ((!empty($images_urls) && isset(array_values($images_urls)[0][0])) ? array_values($images_urls)[0][0] : false);
                        $meta->setMeta('card_title', $title);
                        $meta->setMeta('card_desc', $desc);
                        $meta->setMeta('card_image', (($image_url !== false) ? trim(esc_url(urldecode($image_url))) : ''));
                        $meta->updateMeta((int) $_POST['post_ID']);
                    }

                    if (isset($_POST['post_ID']) && isset($_POST['user_ID']) && (int) $_POST['post_ID'] > 0 && (int) $_POST['user_ID'] > 0 && !defined("B2S_SAVE_META_BOX_AUTO_SHARE") && !wp_is_post_autosave($_POST['post_ID']) && isset($_POST['b2s-meta-box-nonce']) && wp_verify_nonce($_POST['b2s-meta-box-nonce'], 'b2s-meta-box-nonce-post-area') && isset($_POST['post_status'])) {
                        $ship = false;
                        if (isset($_POST['b2s-enable-auto-post'])) {
                            if ((int) $_POST['b2s-enable-auto-post'] == 1) {
                                if ((strtolower($_POST['post_status']) == "publish" || strtolower($_POST['post_status']) == "future") && isset($_POST['b2s-post-meta-box-profil-dropdown'])) {
                                    $profilId = (int) $_POST['b2s-post-meta-box-profil-dropdown'];

                                    //save network settings and besttimes if different than default
                                    $postOptions = new B2S_Options($_POST['post_ID'], 'B2S_PLUGIN_POST_OPTIONS');
                                    $newPostOption = $postOptions->_getOption('auto_post_manuell');
                                    if ($newPostOption == false || !is_array($newPostOption)) {
                                        $newPostOption = array();
                                    }
                                    $tempOption = array();
                                    if (isset($_POST['b2s-profile-default']) && $profilId != (int) $_POST['b2s-profile-default']) {
                                        $tempOption['profile'] = $profilId;
                                    } else {
                                        unset($tempOption['profile']);
                                    }
                                    if (isset($_POST['b2s-post-meta-box-profil-dropdown-twitter']) && (int) $_POST['b2s-post-meta-box-profil-dropdown-twitter'] > 0 && isset($_POST['b2s-twitter-default']) && (int) $_POST['b2s-post-meta-box-profil-dropdown-twitter'] != (int) $_POST['b2s-twitter-default']) {
                                        $tempOption['twitter'] = (int) $_POST['b2s-post-meta-box-profil-dropdown-twitter'];
                                    } else {
                                        unset($tempOption['twitter']);
                                    }
                                    if (isset($_POST['b2s-post-meta-box-sched-select']) && isset($_POST['b2s-best-times-default']) && (int) $_POST['b2s-post-meta-box-sched-select'] != (int) $_POST['b2s-best-times-default']) {
                                        $tempOption['best_times'] = (int) $_POST['b2s-post-meta-box-sched-select'];
                                    } else {
                                        unset($tempOption['best_times']);
                                    }
                                    $newPostOption[B2S_PLUGIN_BLOG_USER_ID] = $tempOption;
                                    $postOptions->_setOption('auto_post_manuell', $newPostOption);

                                    if (isset($_POST['b2s-post-meta-box-profil-data-' . $profilId]) && !empty($_POST['b2s-post-meta-box-profil-data-' . $profilId])) {
                                        $networkData = json_decode(base64_decode($_POST['b2s-post-meta-box-profil-data-' . $profilId]));
                                        if ($networkData !== false && is_array($networkData) && !empty($networkData)) {
                                            $user_timezone = isset($_POST['b2s-user-timezone']) ? (int) $_POST['b2s-user-timezone'] : 0;
                                            $current_utc_date = gmdate('Y-m-d H:i:s');
                                            $current_user_date = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($current_utc_date, $user_timezone)));
                                            $post_date = '';
                                            //WP User Sched Post + B2S Share NOW
                                            if (isset($_POST['mm']) && isset($_POST['jj']) && isset($_POST['aa']) && isset($_POST['hh']) && isset($_POST['mn']) && isset($_POST['ss'])) {
                                                $wp_user_sched_post_date = $_POST['aa'] . '-' . $_POST['mm'] . '-' . $_POST['jj'] . ' ' . $_POST['hh'] . ':' . $_POST['mn'] . ':' . $_POST['ss'];
                                            } else {
                                                //V5.0.0 Gutenberg Editor
                                                $wp_user_sched_post_date = get_the_date('Y-m-d H:i:s', $_POST['post_ID']);
                                            }
                                            $post_date = date('Y-m-d H:i:s', strtotime($wp_user_sched_post_date));

                                            //ShareNow
                                            $sched_type = 3;
                                            $sched_date = $current_user_date;
                                            $sched_date_utc = date('Y-m-d H:i:s', strtotime("-30 seconds", strtotime($current_utc_date)));
                                            $myTimeSettings = false;

                                            //allow for User Post Date (Schedule)
                                            if (!empty($post_date) && $current_user_date <= $post_date) {
                                                $sched_type = 2;
                                                $sched_date = date('Y-m-d H:i:59', strtotime($post_date));
                                                $sched_date_utc = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($sched_date, $user_timezone * (-1))));
                                            }

                                            //Schedule post for best times
                                            if (isset($_POST['b2s-post-meta-box-sched-select']) && $_POST['b2s-post-meta-box-sched-select'] == 1) {
                                                if (isset($_POST['b2s-post-meta-box-best-time-settings'])) {
                                                    $sched_type = 2;
                                                    $myTimeSettings = unserialize(stripslashes($_POST['b2s-post-meta-box-best-time-settings']));
                                                    if ($myTimeSettings !== false && is_array($myTimeSettings) && isset($myTimeSettings['times']) && isset($myTimeSettings['type'])) {
                                                        $myTimeSettings = $myTimeSettings;
                                                    }
                                                }
                                            }

                                            delete_option('B2S_PLUGIN_POST_CONTENT_' . (int) $_POST['post_ID']);
                                            $keywords = $hook_filter->get_wp_post_hashtag((int) $_POST['post_ID'], get_post_type((int) $_POST['post_ID']));
                                            $url = get_permalink($_POST['post_ID']);
                                            $title = isset($_POST['post_title']) ? B2S_Util::getTitleByLanguage(strip_tags($_POST['post_title']), strtolower($b2sPostLang)) : '';
                                            $content = (isset($_POST['content']) && !empty($_POST['content'])) ? trim($_POST['content']) : '';
                                            $excerpt = (isset($_POST['excerpt']) && !empty($_POST['excerpt'])) ? trim($_POST['excerpt']) : get_the_excerpt((int) $_POST['post_ID']);
                                            $images_urls = $hook_filter->get_wp_post_image((int) $_POST['post_ID'], true, $content);
                                            $image_url = ((!empty($images_urls) && isset(array_values($images_urls)[0][0])) ? array_values($images_urls)[0][0] : false);

                                            $options = new B2S_Options((int) $_POST['user_ID']);
                                            $optionPostFormat = $options->_getOption('post_template');
                                           
                                            $defaultPostData = array('default_titel' => $title,
                                                'image_url' => ($image_url !== false) ? trim(urldecode($image_url)) : '',
                                                'lang' => trim(strtolower(substr(B2S_LANGUAGE, 0, 2))),
                                                'no_cache' => 0, //default inactive , 1=active 0=not
                                                'board' => '', 'group' => '', 'url' => $url, 'user_timezone' => $user_timezone); // 'publish_date' => $sched_date, OLD FOR Share Now?

                                            $defaultBlogPostData = array('post_id' => (int) $_POST['post_ID'], 'blog_user_id' => (int) $_POST['user_ID'], 'user_timezone' => $user_timezone, 'sched_type' => $sched_type, 'sched_date' => $sched_date, 'sched_date_utc' => $sched_date_utc);

                                            $autoShare = new B2S_AutoPost((int) $_POST['post_ID'], $defaultBlogPostData, $current_user_date, $myTimeSettings, $title, $content, $excerpt, $url, $image_url, $keywords, $b2sPostLang, $optionPostFormat);
                                            define('B2S_SAVE_META_BOX_AUTO_SHARE', (int) $_POST['post_ID']);
                                            if (isset($_POST['b2s-user-last-selected-profile-id']) && (int) $_POST['b2s-user-last-selected-profile-id'] != (int) $_POST['b2s-post-meta-box-profil-dropdown'] && (int) $_POST['b2s-post-meta-box-profil-dropdown'] != 0) {
                                                update_option('B2S_PLUGIN_SAVE_META_BOX_AUTO_SHARE_PROFILE_USER_' . (int) $_POST['user_ID'], (int) $_POST['b2s-post-meta-box-profil-dropdown'], false);
                                            }

                                            $metaOg = false;
                                            $metaCard = false;
                                            $tosCrossPosting = unserialize(B2S_PLUGIN_NETWORK_CROSSPOSTING_LIMIT);

                                            //Delete old sched posts (don't delete, if network group changed)
                                            if (!isset($_POST['b2s-profile-selected']) || $_POST['b2s-profile-selected'] < 0 || !isset($_POST['b2s-post-meta-box-profil-dropdown']) || (int) $_POST['b2s-post-meta-box-profil-dropdown'] == (int) $_POST['b2s-profile-selected']) {
                                                global $wpdb;
                                                $getSchedData = $wpdb->prepare("SELECT b.id as b2sPostId,d.network_id as networkId,d.network_type as networkType,d.network_auth_id as networkAuthId,d.network_display_name as networkUserName FROM {$wpdb->prefix}b2s_posts b LEFT JOIN {$wpdb->prefix}b2s_posts_network_details d ON (d.id = b.network_details_id) WHERE b.post_id = %d AND b.sched_type = %d AND b.publish_date = %s AND b.hide = %d", (int) $_POST['post_ID'], 3, "0000-00-00 00:00:00", 0);
                                                $schedDataResult = $wpdb->get_results($getSchedData);
                                                $delete_scheds = array();
                                                foreach ($schedDataResult as $k => $value) {
                                                    array_push($delete_scheds, $value->b2sPostId);
                                                }
                                                if (!empty($delete_scheds)) {
                                                    require_once (B2S_PLUGIN_DIR . '/includes/B2S/Post/Tools.php');
                                                    $ship = true;
                                                    B2S_Post_Tools::deleteUserSchedPost($delete_scheds);
                                                }
                                            }

                                            //TOS Twitter 032018 - none multiple Accounts - User select once
                                            $selectedTwitterProfile = (isset($_POST['b2s-post-meta-box-profil-dropdown-twitter']) && !empty($_POST['b2s-post-meta-box-profil-dropdown-twitter'])) ? (int) $_POST['b2s-post-meta-box-profil-dropdown-twitter'] : '';
                                            $otherTwitterProfiles = array();
                                            $initialTwitterPostId = 0;
                                            foreach ($networkData as $k => $value) {
                                                $initialTwitterPost = false;
                                                if ((int) $value->networkId == 1 || (int) $value->networkId == 3 || (int) $value->networkId == 19) {
                                                    $linkNoCache = B2S_Tools::getNoCacheData((int) $_POST['user_ID']);
                                                    if (is_array($linkNoCache) && isset($linkNoCache[$value->networkId]) && (int) $linkNoCache[$value->networkId] > 0) {
                                                        $defaultPostData['no_cache'] = $linkNoCache[$value->networkId];
                                                    }
                                                }
                                                if (isset($value->networkAuthId) && (int) $value->networkAuthId > 0 && isset($value->networkId) && (int) $value->networkId > 0 && isset($value->networkType)) {
                                                    //TOS Twitter 032018 - none multiple Accounts - User select once
                                                    if ((int) $value->networkId == 2) {
                                                        if ((int) $selectedTwitterProfile > 0 && (int) $selectedTwitterProfile == (int) $value->networkAuthId) {
                                                            $initialTwitterPost = true;
                                                        } else {
                                                            array_push($otherTwitterProfiles, (int) $value->networkAuthId);
                                                            continue;
                                                        }
                                                        //TOS Crossposting ignore
                                                    }
                                                    //Filter: TOS Crossposting ignore
                                                    if (isset($tosCrossPosting[$value->networkId][$value->networkType])) {
                                                        continue;
                                                    }
                                                    $res = $autoShare->prepareShareData($value->networkAuthId, $value->networkId, $value->networkType, ((isset($value->networkKind) && (int) $value->networkKind >= 0) ? $value->networkKind : 0));
                                                    if ($res !== false && is_array($res)) {
                                                        $ship = true;
                                                        $res = array_merge($res, $defaultPostData);
                                                        if (((int) $value->networkId == 12 && isset($optionPostFormat[12][0]['addLink']) && $optionPostFormat[12][0]['addLink'] === false) || ((int) $value->networkId == 24 && isset($optionPostFormat[24][0]['addLink']) && $optionPostFormat[24][0]['addLink'] === false)) {
                                                            $res['url'] = '';
                                                        }
                                                        if (((int) $value->networkId == 1 || (int) $value->networkId == 2) && isset($optionPostFormat[1][$value->networkType]['format']) && $optionPostFormat[1][$value->networkType]['format'] == 1 && isset($optionPostFormat[1][$value->networkType]['addLink']) && $optionPostFormat[1][$value->networkType]['addLink'] === false) {
                                                            $res['url'] = '';
                                                        }
                                                        $shareApprove = (isset($value->instant_sharing) && (int) $value->instant_sharing == 1) ? 1 : 0;
                                                        $insert = $autoShare->saveShareData($res, $value->networkId, $value->networkType, $value->networkAuthId, $shareApprove, strip_tags($value->networkUserName));

                                                        if ($initialTwitterPost && (int) $insert > 0) {
                                                            $initialTwitterPostId = $insert;
                                                        }

                                                        //Start - Change/Set MetaTags
                                                        //TODO Check Enable Feature
                                                        if ((int) $value->networkId == 1 && $metaOg == false && (int) $_POST['post_ID'] > 0 && isset($res['post_format']) && (int) $res['post_format'] == 0) {  //LinkPost
                                                            $metaOg = true;
                                                            $meta = B2S_Meta::getInstance();
                                                            $meta->getMeta((int) $_POST['post_ID']);
                                                            if (isset($res['image_url']) && !empty($res['image_url'])) {
                                                                $meta->setMeta('og_image', trim(esc_url($res['image_url'])));
                                                                $meta->updateMeta((int) $_POST['post_ID']);
                                                            }
                                                        }
                                                        if ((int) $value->networkId == 2 && $metaCard == false && (int) $_POST['post_ID'] > 0 && isset($res['post_format']) && (int) $res['post_format'] == 0) {  //LinkPost
                                                            $metaCard = true;
                                                            $meta = B2S_Meta::getInstance();
                                                            $meta->getMeta((int) $_POST['post_ID']);
                                                            if (isset($res['image_url']) && !empty($res['image_url'])) {
                                                                $meta->setMeta('card_image', trim(esc_url($res['image_url'])));
                                                                $meta->updateMeta((int) $_POST['post_ID']);
                                                            }
                                                        }
                                                        //END MetaTags
                                                    }
                                                }
                                            }
                                            //Reweet Twitter
                                            if (!empty($otherTwitterProfiles) && (int) $initialTwitterPostId > 0 && (int) $_POST['post_ID'] > 0) {
                                                global $wpdb;
                                                $updateIntTwitter = $wpdb->prepare("UPDATE {$wpdb->prefix}b2s_posts SET post_for_relay = 1 WHERE id = %d", (int) $initialTwitterPostId);
                                                $wpdb->get_results($updateIntTwitter);
                                                $tw_sched_date = date('Y-m-d H:i:s', strtotime("+15 minutes", strtotime($sched_date)));
                                                $tw_sched_date_utc = date('Y-m-d H:i:s', strtotime("+15 minutes", strtotime($sched_date_utc)));
                                                foreach ($otherTwitterProfiles as $key => $value) {
                                                    $getNetworkDetails = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}b2s_posts_network_details WHERE network_auth_id = %d", (int) $value);
                                                    $networkDetails = $wpdb->get_results($getNetworkDetails);
                                                    if (isset($networkDetails[0]->id) && $networkDetails[0]->id > 0) {
                                                        $wpdb->insert($wpdb->prefix . 'b2s_posts', array(
                                                            'post_id' => (int) $_POST['post_ID'],
                                                            'blog_user_id' => B2S_PLUGIN_BLOG_USER_ID,
                                                            'user_timezone' => $user_timezone,
                                                            'sched_type' => 4, // replay, retweet
                                                            'sched_date' => $tw_sched_date,
                                                            'sched_date_utc' => $tw_sched_date_utc,
                                                            'network_details_id' => (int) $networkDetails[0]->id,
                                                            'relay_primary_post_id' => (int) $initialTwitterPostId,
                                                            'relay_delay_min' => (int) 15,
                                                            'hook_action' => 1), array('%d', '%d', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d'));
                                                    }
                                                }
                                            }
                                            if ($ship) {
                                                B2S_Heartbeat::getInstance()->deleteSchedPost();
                                                B2S_Heartbeat::getInstance()->postToServer();
                                            }

                                            if ($sched_type != 3) {
                                                if (isset($_POST['b2s-user-lang']) && !empty($_POST['b2s-user-lang'])) {
                                                    $dateFormat = ($_POST['b2s-user-lang'] == 'de') ? 'd.m.Y' : 'Y-m-d';
                                                    $_POST['b2s_update_publish_date'] = date($dateFormat, strtotime($sched_date));
                                                }
                                            }

                                            add_filter('redirect_post_location', array($this, 'b2s_add_param_auto_share_meta_box'));
                                        }
                                    } else {
                                        add_filter('redirect_post_location', array($this, 'b2s_add_param_auto_share_error_data_meta_box'));
                                    }
                                } else {
                                    if (strtolower($_POST['post_status']) == "publish" || strtolower($_POST['post_status']) == "future") {
                                        add_filter('redirect_post_location', array($this, 'b2s_add_param_auto_share_error_meta_box'));
                                    }
                                }
                            }
                        } else if (isset($_POST['post_status']) && strtolower($_POST['post_status']) == "future") {
                            //update existing posts, if shed date before future date
                            $user_timezone = isset($_POST['b2s-user-timezone']) ? (int) $_POST['b2s-user-timezone'] : 0;
                            $current_utc_date = gmdate('Y-m-d H:i:s');
                            $current_user_date = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($current_utc_date, $user_timezone)));

                            $post_date = '';
                            if (isset($_POST['post_date']) && !empty($_POST['post_date'])) {
                                $post_date = date('Y-m-d H:i:s', strtotime($_POST['post_date']));
                            }

                            if (empty($post_date)) {
                                if (isset($_POST['mm']) && isset($_POST['jj']) && isset($_POST['aa']) && isset($_POST['hh']) && isset($_POST['mn']) && isset($_POST['ss'])) {
                                    $wp_user_sched_post_date = $_POST['aa'] . '-' . $_POST['mm'] . '-' . $_POST['jj'] . ' ' . $_POST['hh'] . ':' . $_POST['mn'] . ':' . $_POST['ss'];
                                } else {
                                    //V5.0.0 Gutenberg Editor
                                    $wp_user_sched_post_date = get_the_date('Y-m-d H:i:s', $_POST['post_ID']);
                                }
                                $post_date = date('Y-m-d H:i:s', strtotime($wp_user_sched_post_date));
                            }

                            $sched_date = date('Y-m-d H:i:59', strtotime($post_date));
                            $sched_date_utc = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($sched_date, $user_timezone * (-1))));

                            global $wpdb;
                            $getSchedData = $wpdb->prepare("SELECT b.sched_date_utc, b.id as b2sPostId,d.network_id as networkId,d.network_type as networkType,d.network_auth_id as networkAuthId,d.network_display_name as networkUserName FROM {$wpdb->prefix}b2s_posts b LEFT JOIN {$wpdb->prefix}b2s_posts_network_details d ON (d.id = b.network_details_id) WHERE b.post_id = %d AND b.sched_type = %d AND b.publish_date = %s AND b.hide = %d", (int) $_POST['post_ID'], 2, "0000-00-00 00:00:00", 0);
                            $schedDataResult = $wpdb->get_results($getSchedData);
                            foreach ($schedDataResult as $k => $value) {
                                if ($value->sched_date_utc <= $sched_date_utc) {
                                    $ship = true;
                                    require_once (B2S_PLUGIN_DIR . '/includes/B2S/Post/Tools.php');
                                    B2S_Post_Tools::updateUserSchedTimePost($value->b2sPostId, substr($sched_date, 0, 10), substr($sched_date, 11), $user_timezone);
                                }
                            }

                            if ($ship) {
                                B2S_Heartbeat::getInstance()->updateSchedTimePost();
                            }
                        }
                    }
                }
            }
        }
    }

    public function b2s_add_param_auto_share_meta_box($location) {
        remove_filter('redirect_post_location', array($this, 'b2s_add_param_auto_share_meta_box'));
        if (isset($_POST['b2s_update_publish_date'])) {
            return add_query_arg(array('b2s_action' => 1, 'b2s_update_publish_date' => $_POST['b2s_update_publish_date']), $location);
        }
        return add_query_arg(array('b2s_action' => 1), $location);
    }

    public function b2s_add_param_auto_share_error_meta_box($location) {
        remove_filter('redirect_post_location', array($this, 'b2s_add_param_auto_share_error_meta_box'));
        return add_query_arg(array('b2s_action' => 2), $location);
    }

    public function b2s_add_param_auto_share_error_data_meta_box($location) {
        remove_filter('redirect_post_location', array($this, 'b2s_add_param_auto_share_error_data_meta_box'));
        return add_query_arg(array('b2s_action' => 3), $location);
    }

    public function b2s_save_post_alert_meta_box() {
        if (isset($_GET['b2s_action'])) {
            $b2sAction = $_GET['b2s_action'];
            if ((int) $b2sAction == 1) {
                $b2sLink = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=';
                if (isset($_GET['b2s_update_publish_date']) && !empty($_GET['b2s_update_publish_date'])) {
                    $publishDate = htmlspecialchars($_GET['b2s_update_publish_date']);
                    echo '<div class="updated"><p>' . esc_html__('This post will be shared into your social media from', 'blog2social') . ' ' . $publishDate . ' <a target="_blank" href="' . $b2sLink . 'blog2social-sched">' . esc_html__('show details', 'blog2social') . '</a></p></div>';
                } else {
                    echo '<div class="updated"><p>' . esc_html__('This post will be shared on social media in 2-3 minutes!', 'blog2social') . ' <a target="_blank" href="' . $b2sLink . 'blog2social-publish">' . esc_html__('show details', 'blog2social') . '</a></p></div>';
                }
            }
            if ((int) $b2sAction == 2) {
                echo '<div class="error"><p>' . esc_html__('Please make sure that your post, page or custom post type is published or scheduled to be published on this blog before you try to post it with Blog2Social. Published WP posts will be shared with your chosen permalink, scheduled WP posts will be shared with the posting id link.', 'blog2social') . '</p></div>';
            }
            if ((int) $b2sAction == 3) {
                echo '<div class="error"><p>' . esc_html__('There are no social network accounts assigned to your selected network collection. Please assign at least one social network account or select another network collection.', 'blog2social') . '</p></div>';
            }
        }
    }

    public function plugin_init_language() {
        load_plugin_textdomain('blog2social', false, B2S_PLUGIN_LANGUAGE_PATH);
        $this->defineText();
    }

    public function override_plugin_action_links($links) {
        $premium = array();
        if (defined("B2S_PLUGIN_USER_VERSION") && B2S_PLUGIN_USER_VERSION == 0) {
            $premium = array('<a target="_blank" style="color: rgba(10, 154, 62, 1); font-weight: bold; font-size: 13px;" href="' . esc_url(B2S_Tools::getSupportLink('affiliate')) . '">' . esc_html__('Upgrade to Premium', 'blog2social') . '</a>');
        }
        /* Settings & Support */
        $links = array_merge($premium, array('settings' => sprintf('<a href="%s">%s</a>', 'admin.php?page=blog2social-settings', esc_html__('Settings', 'blog2social'))), array('support' => sprintf('<a href="%s">%s</a>', 'admin.php?page=blog2social-support', esc_html__('Support', 'blog2social'))), $links);

        if (!isset($links['deactivate'])) {
            return $links;
        }//end if

        if (is_network_admin()) {
            return $links;
        }//end if

        preg_match_all('/<a[^>]+href="(.+?)"[^>]*>/i', $links['deactivate'], $matches);
        if (empty($matches) || !isset($matches[1][0])) {
            return $links;
        }//end if

        if (isset($matches[1][0])) {
            $links['deactivate'] = sprintf(
                    '<a id="b2s-deactivate" href="%s">%s</a>', $matches[1][0], // @codingStandardsIgnoreLine
                    esc_html__('Deactivate')
            );
        }
        wp_enqueue_style('B2SPOSTBOXCSS');
        wp_enqueue_script('B2SPOSTBOXJS');

        return $links;
    }

    public function override_multisite_plugin_action_links($links) {
        if (!isset($links['deactivate'])) {
            return $links;
        }//end if

        preg_match_all('/<a[^>]+href="(.+?)"[^>]*>/i', $links['deactivate'], $matches);
        if (empty($matches) || !isset($matches[1][0])) {
            return $links;
        }//end if

        if (isset($matches[1][0])) {
            $links['deactivate'] = sprintf(
                    '<a id="b2s-deactivate" href="%s">%s</a>', $matches[1][0], // @codingStandardsIgnoreLine
                    esc_html__('Network Deactivate')
            );
        }
        wp_enqueue_style('B2SPOSTBOXCSS');
        wp_enqueue_script('B2SPOSTBOXJS');

        return $links;
    }

    public function defineText() {
        define('B2S_PLUGIN_PAGE_TITLE', serialize(array('blog2social-notice' => esc_html__('Notifications', 'blog2social'), 'blog2social-publish' => esc_html__('Shared Posts', 'blog2social'), 'blog2social-approve' => esc_html__('Instant Sharing', 'blog2social'), 'blog2social-draft-post' => esc_html__('Drafts', 'blog2social'), 'blog2social-sched' => esc_html__('Scheduled Posts', 'blog2social'), 'blog2social-curation-draft' => esc_html__('Social Media Post Drafts', 'blog2social'), 'blog2social-favorites' => esc_html__('Favorites', 'blog2social'), 'blog2social-autopost' => esc_html__('Auto-Post', 'blog2social'), 'blog2social-repost' => esc_html__('Re-Share Posts', 'blog2social') . ' <span class="label label-success label-sm">' . esc_html__("NEW", "blog2social") . '</span>')));
        define('B2S_PLUGIN_NETWORK_TYPE', serialize(array(esc_html__('Profile', 'blog2social'), esc_html__('Page', 'blog2social'), esc_html__('Group', 'blog2social'))));
        define('B2S_PLUGIN_NETWORK_KIND', serialize(array(esc_html__('Company-Page (Employer Branding Profile)', 'blog2social'), esc_html__('Business', 'blog2social'), '', '', esc_html__('Company-Page (Employer Branding Profile)', 'blog2social'))));
        define('B2S_PLUGIN_NETWORK_ERROR', serialize(array('DEFAULT' => sprintf(__('The network could not publish your post. Please see the following <a target="_blank" href="%s">guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('share_error'))),
            'TOKEN' => esc_html__('Your authorization has expired. Please reconnect your account in the Blog2Social network settings.', 'blog2social'),
            'CONTENT' => sprintf(__('The content of your post could not be approved by the network. Please see the following <a target="_blank" href="%s">guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('content_error'))),
            'RIGHT' => esc_html__('We don\'t have the permission to publish your post. Please check your authorization.', 'blog2social'),
            'LOGIN' => sprintf(__('The connection to your social media account is interrupted. Please check your authorization and reconnect your account. The <a target="_blank" href="%s">troubleshooting guide</a> shows you how to fix the connection to your social media account.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('troubleshoot_auth'))),
            'LIMIT' => esc_html__('Your daily limit has been reached.', 'blog2social'),
            'IMAGE' => esc_html__('Your post could not be posted, because your image is not available or the image source does not allow to publish', 'blog2social'),
            'PROTECT' => sprintf(__('The network has blocked your account. Please see the following <a target="_blank" href="%s">guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('share_error'))),
            'IMAGE_LIMIT' => sprintf(__('The number of images is reached. Please see the following <a target="_blank" href="%s">guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('share_error'))),
            'RATE_LIMIT' => esc_html__('Your daily limit for this network has been reached. Please try again later.', 'blog2social'),
            'INVALID_CONTENT' => sprintf(__('The network can not publish special characters such as Emoji. Please see the following <a target="_blank" href="%s">guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('share_error'))),
            'EXISTS_CONTENT' => esc_html__('Your post is a duplicate.', 'blog2social'),
            'URL_CONTENT' => esc_html__('The network requires a public url.', 'blog2social'),
            'BLOGPOST_NOT_PUBLISHED' => esc_html__('Your blog post was not available for the network at the time of publishing.', 'blog2social'),
            'EXISTS_RELAY' => esc_html__('You have already retweeted this post.', 'blog2social'),
            'DEPRECATED_NETWORK_8' => esc_html__('This XING API is no longer supported by XING. Please connect your XING accounts with the new XING interface to reschedule your posts.', 'blog2social'),
            'IMAGE_FOR_CURATION' => esc_html__('An image is required to post on this social network.', 'blog2social'), // special for content curation V.5.0.0
            'LINK_FOR_CURATION' => esc_html__('To share social media posts on Reddit or Diigo, a link is required.', 'blog2social'), // special for content curation V.6.0.0
            'IMAGE_NETWORK' => esc_html__('Your post could not be posted, because your image can not be processed by the network.', 'blog2social'),
            'TEXT_NOT_PUBLISHED_12' => sprintf(__('Instagram published your post without text. Please see the following <a target="_blank" href="%s">guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('instagram_without_text'))),
            'GROUP_CONTENT' => esc_html__('Your group can not be found by the network.', 'blog2social'))));
    }

    public function getToken() {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT token FROM `{$wpdb->prefix}b2s_user` WHERE `blog_user_id` = %d", $this->blogUserData->ID);
        $userExist = $wpdb->get_row($sql);
        if (empty($userExist) || !isset($userExist->token)) {
            if (isset($_GET['page']) && !empty($_GET['page']) && in_array($_GET['page'], unserialize(B2S_PLUGIN_PAGE_SLUG))) {
                $postData = array('action' => 'getToken', 'blog_user_id' => $this->blogUserData->ID, 'blog_url' => get_option('home'), 'email' => $this->blogUserData->user_email, 'is_multisite' => is_multisite());
                $result = json_decode(B2S_Tools::getToken($postData));
                if (isset($result->result) && (int) $result->result == 1 && isset($result->token)) {
                    $state_url = (isset($result->state_url)) ? (int) $result->state_url : 0;
                    $sqlInsertToken = $wpdb->prepare("INSERT INTO `{$wpdb->prefix}b2s_user` (`token`, `blog_user_id`,`register_date`,`state_url`) VALUES (%s,%d,%s,%d);", $result->token, (int) $this->blogUserData->ID, date('Y-m-d H:i:s'), $state_url);
                    $wpdb->query($sqlInsertToken);
                    define('B2S_PLUGIN_TOKEN', $result->token);
                } else {
                    define('B2S_PLUGIN_NOTICE', 'CONNECTION');
                }
            }
        } else {
            define('B2S_PLUGIN_TOKEN', $userExist->token);
        }
    }

    public function getUserDetails() {
        $tokenInfo = get_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID);
        if ($tokenInfo == false || !isset($tokenInfo['B2S_PLUGIN_USER_VERSION']) || !isset($tokenInfo['B2S_PLUGIN_VERSION']) || $tokenInfo['B2S_PLUGIN_USER_VERSION_NEXT_REQUEST'] < time() || (isset($tokenInfo['B2S_PLUGIN_VERSION']) && (int) $tokenInfo['B2S_PLUGIN_VERSION'] < (int) B2S_PLUGIN_VERSION) || (isset($tokenInfo['B2S_PLUGIN_TRAIL_END']) && strtotime($tokenInfo['B2S_PLUGIN_TRAIL_END']) < strtotime(gmdate('Y-m-d H:i:s')))) {
            B2S_Tools::setUserDetails();
        } else {
            define('B2S_PLUGIN_USER_VERSION', $tokenInfo['B2S_PLUGIN_USER_VERSION']);
            if (isset($tokenInfo['B2S_PLUGIN_TRAIL_END'])) {
                define('B2S_PLUGIN_TRAIL_END', $tokenInfo['B2S_PLUGIN_TRAIL_END']);
                update_option('B2S_PLUGIN_DISABLE_TRAIL', true, false);
            }
        }
        
        $checkUpdateOption = get_option('B2S_PLUGIN_NEXT_CHECK_UPDATE_REQUEST');
        if($checkUpdateOption == false || $checkUpdateOption < time()) {
            $this->checkUpdate();
            update_option('B2S_PLUGIN_NEXT_CHECK_UPDATE_REQUEST', time() + 86400, false);
        }
    }

    private function checkUpdate() {
        $args = array(
            'timeout' => '5',
            'redirection' => '5',
            'user-agent' => "Blog2Social/" . B2S_PLUGIN_VERSION . " (Wordpress/Plugin)"
        );
        $result = wp_remote_retrieve_body(wp_remote_get(B2S_PLUGIN_API_ENDPOINT . 'update.txt', $args));
        $currentVersion = explode('#', $result);
        if (isset($currentVersion[0]) && (int) $currentVersion[0] > (int) B2S_PLUGIN_VERSION) {
            define('B2S_PLUGIN_NOTICE', 'UPDATE');
        }
    }

    public function createMenu() {
        $subPages = array();
//pageTitle,menutitle,$capability, $menu_slug, $function, $icon_url, $position
        add_menu_page('Blog2Social', 'Blog2Social', 'blog2social_access', 'blog2social', null, plugins_url('/assets/images/b2s_icon.png', B2S_PLUGIN_FILE));
//$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function
        $subPages[] = add_submenu_page('blog2social', esc_html__('Dashboard', 'blog2social'), esc_html__('Dashboard', 'blog2social'), 'blog2social_access', 'blog2social', array($this, 'b2sstart'));
        $subPages[] = add_submenu_page('blog2social', esc_html__('Share Website & Blog Content', 'blog2social'), esc_html__('Site & Blog Content', 'blog2social'), 'blog2social_access', 'blog2social-post', array($this, 'b2sPost'));
        $subPages[] = add_submenu_page('blog2social', esc_html__('Create Social Media Posts', 'blog2social'), esc_html__('Social Media Posts', 'blog2social'), 'blog2social_access', 'blog2social-curation', array($this, 'b2sContentCuration'));
        $subPages[] = add_submenu_page('blog2social', esc_html__('Calendar', 'blog2social'), esc_html__('Calendar', 'blog2social'), 'blog2social_access', 'blog2social-calendar', array($this, 'b2sPostCalendar'));
        $subPages[] = add_submenu_page('blog2social', esc_html__('Networks', 'blog2social'), esc_html__('Networks', 'blog2social'), 'blog2social_access', 'blog2social-network', array($this, 'b2sNetwork'));
        $subPages[] = add_submenu_page('blog2social', esc_html__('Settings', 'blog2social'), esc_html__('Settings', 'blog2social'), 'blog2social_access', 'blog2social-settings', array($this, 'b2sSettings'));
        if (!B2S_System::isblockedArea('B2S_MENU_ITEM_WP_PR_SERVICE', B2S_PLUGIN_ADMIN)) {
            $subPages[] = add_submenu_page('blog2social', esc_html__('PR-Service', 'blog2social'), esc_html__('PR-Service', 'blog2social'), 'blog2social_access', 'prg-post', array($this, 'prgPost'));
        }
        $subPages[] = add_submenu_page('blog2social', esc_html__('Help & Support', 'blog2social'), esc_html__('Help & Support', 'blog2social'), 'blog2social_access', 'blog2social-support', array($this, 'b2sSupport'));
        if (!B2S_System::isblockedArea('B2S_MENU_ITEM_WP_LICENSE', B2S_PLUGIN_ADMIN)) {
            $subPages[] = add_submenu_page('blog2social', esc_html__('Premium', 'blog2social'), '<span class="dashicons dashicons-star-filled"></span> ' . esc_html__('PREMIUM', 'blog2social'), 'blog2social_access', 'blog2social-premium', array($this, 'b2sPremium'));
        }
        $subPages[] = add_submenu_page(null, 'B2S Post Draft', 'B2S Post Draft', 'blog2social_access', 'blog2social-draft-post', array($this, 'b2sPostDraft'));
        $subPages[] = add_submenu_page(null, 'B2S Post Favorites', 'B2S Post Favorites', 'blog2social_access', 'blog2social-favorites', array($this, 'b2sPostFavorites'));
        $subPages[] = add_submenu_page(null, 'B2S Post Sched', 'B2S Post Sched', 'blog2social_access', 'blog2social-sched', array($this, 'b2sPostSched'));
        $subPages[] = add_submenu_page(null, 'B2S Post Approve', 'B2S Post Approve', 'blog2social_access', 'blog2social-approve', array($this, 'b2sPostApprove'));
        $subPages[] = add_submenu_page(null, 'B2S Post Publish', 'B2S Post Publish', 'blog2social_access', 'blog2social-publish', array($this, 'b2sPostPublish'));
        $subPages[] = add_submenu_page(null, 'B2S Post Notice', 'B2S Post Notice', 'blog2social_access', 'blog2social-notice', array($this, 'b2sPostNotice')); //Error post page since 4.8.0
        $subPages[] = add_submenu_page(null, 'B2S Ship', 'B2S Ship', 'blog2social_access', 'blog2social-ship', array($this, 'b2sShip'));
        $subPages[] = add_submenu_page(null, 'B2S Curation Drafts', 'B2S Curation Drafts', 'blog2social_access', 'blog2social-curation-draft', array($this, 'b2sCurationDraft'));
        $subPages[] = add_submenu_page(null, 'B2S Autoposter', 'B2S Autoposter', 'blog2social_access', 'blog2social-autopost', array($this, 'b2sAutoPost'));
        $subPages[] = add_submenu_page(null, 'B2S Reposter', 'B2S Reposter', 'blog2social_access', 'blog2social-repost', array($this, 'b2sRePost'));
        $subPages[] = add_submenu_page(null, 'PRG Login', 'PRG Login', 'blog2social_access', 'prg-login', array($this, 'prgLogin'));
        $subPages[] = add_submenu_page(null, 'PRG Ship', 'PRG Ship', 'blog2social_access', 'prg-ship', array($this, 'prgShip'));
        foreach ($subPages as $var) {
            add_action($var, array($this, 'addAssets'));
        }
    }

    public function createToolbarMenu() {
        if (!current_user_can('blog2social_access')) {
            return;
        }
        global $wp_admin_bar;
        $seo_url = strtolower(get_admin_url(null, 'admin.php?page='));
        $title = '<div id="blog2social-ab-icon" class="ab-item" style="padding-left: 25px; background-repeat: no-repeat; background-size: 16px auto; background-position: left center; background-image: url(\'' . plugins_url('/assets/images/b2s_icon.png', B2S_PLUGIN_FILE) . '\');">' . esc_html__('Blog2Social', 'blog2social') . '</div>';
        $wp_admin_bar->add_node(array(
            'id' => 'blog2social',
            'title' => $title,
            'href' => $seo_url . 'blog2social'
        ));

        $wp_admin_bar->add_node(array(
            'id' => 'blog2social-dashboard',
            'title' => esc_html__('Dashboard', 'blog2social'),
            'href' => $seo_url . 'blog2social',
            'parent' => 'blog2social'
        ));

        $wp_admin_bar->add_node(array(
            'id' => 'blog2social-post',
            'title' => esc_html__('Site & Blog Content', 'blog2social'),
            'href' => $seo_url . 'blog2social-post',
            'parent' => 'blog2social'
        ));

        $wp_admin_bar->add_node(array(
            'id' => 'blog2social-curation',
            'title' => esc_html__('Social Media Posts', 'blog2social'),
            'href' => $seo_url . 'blog2social-curation',
            'parent' => 'blog2social'
        ));

        $wp_admin_bar->add_node(array(
            'id' => 'blog2social-calendar',
            'title' => esc_html__('Calendar', 'blog2social'),
            'href' => $seo_url . 'blog2social-calendar',
            'parent' => 'blog2social'
        ));

        $wp_admin_bar->add_node(array(
            'id' => 'blog2social-network',
            'title' => esc_html__('Networks', 'blog2social'),
            'href' => $seo_url . 'blog2social-network',
            'parent' => 'blog2social'
        ));
        $wp_admin_bar->add_node(array(
            'id' => 'blog2social-settings',
            'title' => esc_html__('Settings', 'blog2social'),
            'href' => $seo_url . 'blog2social-settings',
            'parent' => 'blog2social'
        ));

        if (!B2S_System::isblockedArea('B2S_MENU_ITEM_WP_PR_SERVICE', B2S_PLUGIN_ADMIN)) {
            $wp_admin_bar->add_node(array(
                'id' => 'blog2social-prg-post',
                'title' => esc_html__('PR-Service', 'blog2social'),
                'href' => $seo_url . 'prg-post',
                'parent' => 'blog2social'
            ));
        }

        $wp_admin_bar->add_node(array(
            'id' => 'blog2social-support',
            'title' => esc_html__('Help & Support', 'blog2social'),
            'href' => $seo_url . 'blog2social-support',
            'parent' => 'blog2social'
        ));

        if (!B2S_System::isblockedArea('B2S_MENU_ITEM_WP_LICENSE', B2S_PLUGIN_ADMIN)) {
            $wp_admin_bar->add_node(array(
                'id' => 'blog2social-premium',
                'title' => '<span class="ab-icon dashicons dashicons-star-filled"></span> ' . esc_html__('PREMIUM', 'blog2social'),
                'href' => $seo_url . 'blog2social-premium',
                'parent' => 'blog2social'
            ));
        }
    }

//PageFunktion
    public function b2sstart() {

        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SFULLCALLENDARCSS');
            wp_enqueue_style('B2SCALENDARCSS');
            wp_enqueue_script('B2SFULLCALENDARMOMENTJS');
            wp_enqueue_script('B2SFULLCALENDARJS');
            wp_enqueue_script('B2SFULLCALENDARLOCALEJS');
            wp_enqueue_script('B2SLIB');

            wp_enqueue_script('B2SMOMENT');
            wp_enqueue_script('B2SCHARTJS');
            wp_enqueue_style('B2SSTARTCSS');
            wp_enqueue_script('B2SSTARTJS');
            wp_enqueue_style('B2SPOSTCSS');
            wp_enqueue_script('B2SPOSTJS');
            wp_enqueue_style('B2SAIRDATEPICKERCSS');
            wp_enqueue_script('B2SAIRDATEPICKERJS');
            wp_enqueue_script('B2SAIRDATEPICKERDEJS');
            wp_enqueue_script('B2SAIRDATEPICKERENJS');
            require_once( B2S_PLUGIN_DIR . 'views/b2s/dashboard.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sPost() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SPOSTCSS');
            wp_enqueue_script('B2SPOSTJS');
            require_once( B2S_PLUGIN_DIR . 'views/b2s/post.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

    //Page Curation
    public function b2sContentCuration() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SCURATIONCSS');
            wp_enqueue_script('B2SEMOJIBUTTONJS');
            wp_enqueue_script('B2SCURATIONJS');
            wp_enqueue_style('B2SAIRDATEPICKERCSS');
            wp_enqueue_script('B2SAIRDATEPICKERJS');
            wp_enqueue_script('B2SAIRDATEPICKERDEJS');
            wp_enqueue_script('B2SAIRDATEPICKERENJS');
            if (current_user_can('upload_files')) {
//Capability by Super Admin ,Administrator ,Editor ,Author
                wp_enqueue_media();
            }
            require_once( B2S_PLUGIN_DIR . 'views/b2s/curation.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sNetwork() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SNETWORKCSS');
            wp_enqueue_script('B2SNETWORKJS');
            wp_enqueue_style('B2STIMEPICKERCSS');
            wp_enqueue_script('B2STIMEPICKERJS');
            require_once( B2S_PLUGIN_DIR . 'views/b2s/network.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sSettings() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SSETTINGSCSS');
            wp_enqueue_script('B2SSETTINGSJS');
            wp_enqueue_style('B2STIMEPICKERCSS');
            wp_enqueue_script('B2STIMEPICKERJS');
            wp_enqueue_style('B2SBTNTOOGLECSS');
            wp_enqueue_script('B2SBTNTOOGLEJS');
            wp_enqueue_style('B2SCHOSENCSS');
            wp_enqueue_script('B2SCHOSENJS');

            if (current_user_can('upload_files')) {
//Capability by Super Admin ,Administrator ,Editor ,Author
                wp_enqueue_media();
            }
            require_once( B2S_PLUGIN_DIR . 'views/b2s/settings.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sShip() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SFULLCALLENDARCSS');
            wp_enqueue_style('B2SCALENDARCSS');
            wp_enqueue_script('B2SFULLCALENDARMOMENTJS');
            wp_enqueue_script('B2SFULLCALENDARJS');
            wp_enqueue_script('B2SFULLCALENDARLOCALEJS');
            wp_enqueue_script('B2SLIB');
            wp_enqueue_style('B2SSHIPCSS');
            wp_enqueue_style('B2SDATEPICKERCSS');
            wp_enqueue_style('B2STIMEPICKERCSS');
            wp_enqueue_style('B2SWYSIWYGCSS');
            wp_enqueue_script('B2SWYSIWYGJS');
            wp_enqueue_script('B2SEMOJIBUTTONJS');
            if (substr(B2S_LANGUAGE, 0, 2) == 'de') {
                wp_enqueue_script('B2SWYSIWYGLANGDEJS');
            } else {
                wp_enqueue_script('B2SWYSIWYGLANGENJS');
            }
            wp_enqueue_script('B2SDATEPICKERJS');
            wp_enqueue_script('B2SDATEPICKERDEJS');
            wp_enqueue_script('B2SDATEPICKERENJS');
            wp_enqueue_script('B2STIMEPICKERJS');
            wp_enqueue_script('B2SSHIPJS');
            if (current_user_can('upload_files')) {
//Capability by Super Admin ,Administrator ,Editor ,Author
                wp_enqueue_media();
            }
            require_once( B2S_PLUGIN_DIR . 'views/b2s/ship.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sCurationDraft() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SCURATIONDRAFTCSS');
            wp_enqueue_script('B2SCURATIONDRAFTJS');
            require_once( B2S_PLUGIN_DIR . 'views/b2s/curation.draft.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sAutoPost() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SAUTOPOSTCSS');
            wp_enqueue_script('B2SAUTOPOSTJS');
            wp_enqueue_style('B2SBTNTOOGLECSS');
            wp_enqueue_script('B2SBTNTOOGLEJS');
            wp_enqueue_style('B2SCHOSENCSS');
            wp_enqueue_script('B2SCHOSENJS');
            require_once( B2S_PLUGIN_DIR . 'views/b2s/autopost.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sRePost() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SREPOSTCSS');
            wp_enqueue_script('B2SREPOSTJS');
            wp_enqueue_style('B2SCHOSENCSS');
            wp_enqueue_script('B2SCHOSENJS');
            wp_enqueue_script('B2SLIB');
            wp_enqueue_style('B2SDATEPICKERCSS');
            wp_enqueue_style('B2STIMEPICKERCSS');
            wp_enqueue_script('B2SDATEPICKERJS');
            wp_enqueue_script('B2SDATEPICKERDEJS');
            wp_enqueue_script('B2SDATEPICKERENJS');
            wp_enqueue_script('B2STIMEPICKERJS');
            wp_enqueue_style('B2SWYSIWYGCSS');
            wp_enqueue_script('B2SWYSIWYGJS');
            wp_enqueue_script('B2SEMOJIBUTTONJS');
            wp_enqueue_script('B2SSHIPJS');
            if (substr(B2S_LANGUAGE, 0, 2) == 'de') {
                wp_enqueue_script('B2SWYSIWYGLANGDEJS');
            } else {
                wp_enqueue_script('B2SWYSIWYGLANGENJS');
            }
            if (current_user_can('upload_files')) {
                //Capability by Super Admin ,Administrator ,Editor ,Author
                wp_enqueue_media();
            }
            require_once( B2S_PLUGIN_DIR . 'views/b2s/repost.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function prgLogin() {
        wp_enqueue_script('B2SVALIDATEJS');
        if (B2S_Tools::showNotice() == false) {
            $prgInfo = get_option('B2S_PLUGIN_PRG_' . B2S_PLUGIN_BLOG_USER_ID);
            if ($prgInfo != false && isset($prgInfo['B2S_PRG_ID']) && (int) $prgInfo['B2S_PRG_ID'] > 0 && isset($prgInfo['B2S_PRG_TOKEN']) && !empty($prgInfo['B2S_PRG_TOKEN'])) {
                $postId = (int) $_GET['postId'];
                echo'<script> window.location="' . admin_url('/admin.php?page=prg-ship&postId=' . $postId, 'http') . '"; </script> ';
                wp_die();
            } else {
                wp_enqueue_style('PRGLOGINCSS');
                wp_enqueue_script('PRGLOGINJS');
                require_once( B2S_PLUGIN_DIR . 'views/prg/login.php');
            }
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function prgShip() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('PRGSHIPCSS');
            wp_enqueue_script('PRGSHIPJS');
            wp_enqueue_script('PRGGENERALJS');
            require_once( B2S_PLUGIN_DIR . 'views/prg/ship.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sPostSched() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_script('B2SLIB');
            wp_enqueue_style('B2SPOSTSCHEDCSS');
            wp_enqueue_style('B2SDATEPICKERCSS');
            wp_enqueue_style('B2STIMEPICKERCSS');
            wp_enqueue_script('B2SDATEPICKERJS');
            wp_enqueue_script('B2SDATEPICKERDEJS');
            wp_enqueue_script('B2SDATEPICKERENJS');
            wp_enqueue_script('B2STIMEPICKERJS');
            wp_enqueue_script('B2SPOSTJS');
            wp_enqueue_style('B2SWYSIWYGCSS');
            wp_enqueue_script('B2SWYSIWYGJS');
            wp_enqueue_script('B2SEMOJIBUTTONJS');
            wp_enqueue_script('B2SSHIPJS');
            if (substr(B2S_LANGUAGE, 0, 2) == 'de') {
                wp_enqueue_script('B2SWYSIWYGLANGDEJS');
            } else {
                wp_enqueue_script('B2SWYSIWYGLANGENJS');
            }
            if (current_user_can('upload_files')) {
                //Capability by Super Admin ,Administrator ,Editor ,Author
                wp_enqueue_media();
            }
            require_once( B2S_PLUGIN_DIR . 'views/b2s/post.sched.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

    //PageFunktion
    public function b2sPostApprove() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SPOSTAPPROVECSS');
            wp_enqueue_script('B2SPOSTJS');
            require_once( B2S_PLUGIN_DIR . 'views/b2s/post.approve.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

    //PageFunktion
    public function b2sPostDraft() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SPOSTDRAFTCSS');
            wp_enqueue_style('B2SDATEPICKERCSS');
            wp_enqueue_script('B2SDATEPICKERJS');
            wp_enqueue_script('B2SDATEPICKERDEJS');
            wp_enqueue_script('B2SDATEPICKERENJS');
            wp_enqueue_script('B2SPOSTJS');
            require_once( B2S_PLUGIN_DIR . 'views/b2s/post.draft.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

    //PageFunktion
    public function b2sPostFavorites() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SPOSTCSS');
            wp_enqueue_script('B2SPOSTJS');
            require_once( B2S_PLUGIN_DIR . 'views/b2s/post.favorites.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sPostCalendar() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_script('B2SLIB');
            wp_enqueue_style('B2SFULLCALLENDARCSS');
            wp_enqueue_style('B2SCALENDARCSS');
            wp_enqueue_style('B2SDATEPICKERCSS');
            wp_enqueue_style('B2STIMEPICKERCSS');
            wp_enqueue_script('B2SDATEPICKERJS');
            wp_enqueue_script('B2SDATEPICKERDEJS');
            wp_enqueue_script('B2SDATEPICKERENJS');
            wp_enqueue_script('B2STIMEPICKERJS');
            wp_enqueue_script('B2SCALENDARJS');
            wp_enqueue_script('B2SFULLCALENDARMOMENTJS');
            wp_enqueue_script('B2SFULLCALENDARJS');
            wp_enqueue_script('B2SFULLCALENDARLOCALEJS');
            wp_enqueue_style('B2SWYSIWYGCSS');
            wp_enqueue_script('B2SWYSIWYGJS');
            wp_enqueue_script('B2SEMOJIBUTTONJS');
            wp_enqueue_script('B2SSHIPJS');
            if (substr(B2S_LANGUAGE, 0, 2) == 'de') {
                wp_enqueue_script('B2SWYSIWYGLANGDEJS');
            } else {
                wp_enqueue_script('B2SWYSIWYGLANGENJS');
            }

            if (current_user_can('upload_files')) {
//Capability by Super Admin ,Administrator ,Editor ,Author
                wp_enqueue_media();
            }
            require_once( B2S_PLUGIN_DIR . 'views/b2s/post.calendar.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sSupport() {
        wp_enqueue_script('B2SVALIDATEJS');
        wp_enqueue_style('B2SSUPPORT');
        wp_enqueue_script('B2SSUPPORTJS');
        require_once( B2S_PLUGIN_DIR . 'views/b2s/support.php');
    }

//PageFunktion
    public function b2sPremium() {
        wp_enqueue_script('B2SVALIDATEJS');
        wp_enqueue_style('B2SPREMIUM');
        wp_enqueue_style('B2SCHOSENCSS');
        wp_enqueue_script('B2SCHOSENJS');
        wp_enqueue_script('B2SPREMIUMJS');
        require_once( B2S_PLUGIN_DIR . 'views/b2s/premium.php');
    }

//PageFunktion
    public function b2sPostPublish() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SPOSTPUBLISHCSS');
            wp_enqueue_script('B2SPOSTJS');
            wp_enqueue_script('PRGGENERALJS');
            require_once( B2S_PLUGIN_DIR . 'views/b2s/post.publish.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function b2sPostNotice() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('B2SPOSTNOTICECSS');
            wp_enqueue_script('B2SPOSTJS');
            wp_enqueue_script('PRGGENERALJS');
            require_once( B2S_PLUGIN_DIR . 'views/b2s/post.notice.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

//PageFunktion
    public function prgPost() {
        if (B2S_Tools::showNotice() == false) {
            wp_enqueue_script('B2SVALIDATEJS');
            wp_enqueue_style('PRGPOSTCSS');
            wp_enqueue_script('PRGPOSTJS');
            wp_enqueue_script('PRGGENERALJS');
            require_once( B2S_PLUGIN_DIR . 'views/prg/post.php');
        } else {
            require_once( B2S_PLUGIN_DIR . 'views/notice.php');
        }
    }

    public function addBootAssets($hook) {
        if ($hook == 'edit.php') {
            wp_enqueue_script('B2SPOSTSCHEDHEARTBEATJS');
        }

        if ($hook == 'plugins.php') {
            wp_enqueue_script('B2SPLUGINDEACTIVATEJS');
            wp_enqueue_style('B2SPLUGINDEACTIVATECSS');
        }
    }

    public function addAssets() {
        wp_enqueue_style('B2SBOOTCSS');
        wp_enqueue_script('B2SBOOTJS');
        wp_enqueue_script('B2SGENERALJS');
    }

    public function registerAssets() {
        wp_register_style('B2SBOOTCSS', plugins_url('assets/css/general.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SSTARTCSS', plugins_url('assets/css/b2s/start.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SPOSTCSS', plugins_url('assets/css/b2s/post.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SSHIPCSS', plugins_url('assets/css/b2s/ship.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SCURATIONCSS', plugins_url('assets/css/b2s/curation.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);

        wp_register_style('B2SPOSTSCHEDCSS', plugins_url('assets/css/b2s/post.sched.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SPOSTDRAFTCSS', plugins_url('assets/css/b2s/post.draft.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SPOSTAPPROVECSS', plugins_url('assets/css/b2s/post.approve.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SPOSTPUBLISHCSS', plugins_url('assets/css/b2s/post.publish.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SPOSTNOTICECSS', plugins_url('assets/css/b2s/post.notice.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SNETWORKCSS', plugins_url('assets/css/b2s/network.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SSUPPORT', plugins_url('assets/css/b2s/support.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SPREMIUM', plugins_url('assets/css/b2s/premium.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SSETTINGSCSS', plugins_url('assets/css/b2s/settings.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('PRGSHIPCSS', plugins_url('assets/css/prg/ship.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('PRGLOGINCSS', plugins_url('assets/css/prg/login.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SDATEPICKERCSS', plugins_url('assets/lib/datepicker/css/bootstrap-datepicker3.min.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SAIRDATEPICKERCSS', plugins_url('assets/lib/air-datepicker/css/datepicker.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2STIMEPICKERCSS', plugins_url('assets/lib/timepicker/timepicker.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('PRGPOSTCSS', plugins_url('assets/css/prg/post.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SWYSIWYGCSS', plugins_url('assets/lib/wysiwyg/square.min.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SPOSTBOXCSS', plugins_url('assets/css/b2s/wp/post-box.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SNOTICECSS', plugins_url('assets/css/notice.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SFULLCALLENDARCSS', plugins_url('assets/lib/fullcalendar/fullcalendar.min.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SCALENDARCSS', plugins_url('assets/css/b2s/calendar.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SBTNTOOGLECSS', plugins_url('assets/lib/btn-toogle/bootstrap-toggle.min.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SCHOSENCSS', plugins_url('assets/lib/chosen/chosen.min.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SPLUGINDEACTIVATECSS', plugins_url('assets/css/b2s/wp/plugin-deactivate.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SCURATIONDRAFTCSS', plugins_url('assets/css/b2s/curation.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SAUTOPOSTCSS', plugins_url('assets/css/b2s/autopost.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_style('B2SREPOSTCSS', plugins_url('assets/css/b2s/repost.css', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);

        wp_register_script('B2SNETWORKJS', plugins_url('assets/js/b2s/network.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SSETTINGSJS', plugins_url('assets/js/b2s/settings.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SAUTOPOSTJS', plugins_url('assets/js/b2s/autopost.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SREPOSTJS', plugins_url('assets/js/b2s/repost.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SSTARTJS', plugins_url('assets/js/b2s/start.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SPOSTJS', plugins_url('assets/js/b2s/post.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SSHIPJS', plugins_url('assets/js/b2s/ship.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SCURATIONJS', plugins_url('assets/js/b2s/curation.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('PRGSHIPJS', plugins_url('assets/js/prg/ship.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('PRGLOGINJS', plugins_url('assets/js/prg/login.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SBOOTJS', plugins_url('assets/js/general.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SVALIDATEJS', plugins_url('assets/js/validate.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);

        wp_register_script('B2SSUPPORTJS', plugins_url('assets/js/b2s/support.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SGENERALJS', plugins_url('assets/js/b2s/general.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SDATEPICKERJS', plugins_url('assets/lib/datepicker/js/bootstrap-datepicker.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SDATEPICKERDEJS', plugins_url('assets/lib/datepicker/locales/bootstrap-datepicker.de_DE.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SDATEPICKERENJS', plugins_url('assets/lib/datepicker/locales/bootstrap-datepicker.en_US.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SAIRDATEPICKERJS', plugins_url('assets/lib/air-datepicker/js/datepicker.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SAIRDATEPICKERDEJS', plugins_url('assets/lib/air-datepicker/js/locales/datepicker.de.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SAIRDATEPICKERENJS', plugins_url('assets/lib/air-datepicker/js/locales/datepicker.en.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SCHARTJS', plugins_url('assets/lib/chartjs/Chart.bundle.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SMOMENT', plugins_url('assets/lib/moment/moment-with-locales.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2STIMEPICKERJS', plugins_url('assets/lib/timepicker/timepicker.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('PRGPOSTJS', plugins_url('assets/js/prg/post.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('PRGGENERALJS', plugins_url('assets/js/prg/general.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SWYSIWYGJS', plugins_url('assets/lib/wysiwyg/jquery.sceditor.xhtml.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SWYSIWYGLANGDEJS', plugins_url('assets/lib/wysiwyg/languages/de.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SWYSIWYGLANGENJS', plugins_url('assets/lib/wysiwyg/languages/en.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SPOSTBOXJS', plugins_url('assets/js/b2s/wp/post-box.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SPOSTSCHEDHEARTBEATJS', plugins_url('assets/js/b2s/wp/post-sched-heartbeat.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SCALENDARJS', plugins_url('assets/js/b2s/calendar.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SFULLCALENDARMOMENTJS', plugins_url('assets/lib/fullcalendar/lib/moment.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SFULLCALENDARJS', plugins_url('assets/lib/fullcalendar/fullcalendar.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SFULLCALENDARLOCALEJS', plugins_url('assets/lib/fullcalendar/locale-all.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SBTNTOOGLEJS', plugins_url('assets/lib/btn-toogle/bootstrap-toggle.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SCHOSENJS', plugins_url('assets/lib/chosen/chosen.jquery.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SLIB', plugins_url('assets/js/b2s/lib.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SPLUGINDEACTIVATEJS', plugins_url('assets/js/b2s/wp/plugin-deactivate.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SCURATIONDRAFTJS', plugins_url('assets/js/b2s/curation.draft.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SPREMIUMJS', plugins_url('assets/js/b2s/premium.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
        wp_register_script('B2SEMOJIBUTTONJS', plugins_url('assets/lib/emoji-button/emoji-button.min.js', B2S_PLUGIN_FILE), array(), B2S_PLUGIN_VERSION);
    }

    public function initCaps() {
        global $wp_roles;
        if (!class_exists('WP_Roles')) {
            wp_die(esc_html__('Blog2Social needs Wordpress Version 4.7.0 or higher.', 'blog2social') . ' ' . sprintf(__('<a href="%s" target="_blank">Please find more Information and help in our FAQ</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('system'))) . ' ' . esc_html__('or', 'blog2social') . '  <a href="' . esc_url(admin_url("/plugins.php", "http")) . '/">' . esc_html__('back to install plugins', 'blog2social') . '</a>');
        }
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
        }
        if (!function_exists('get_editable_roles')) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }
        foreach (get_editable_roles() as $role_name => $role_info) {
            $wp_roles->add_cap($role_name, 'blog2social_access');
        }
    }

    public function activatePlugin() {
        require_once(B2S_PLUGIN_DIR . 'includes/Tools.php');
        require_once (B2S_PLUGIN_DIR . 'includes/System.php');
        require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
        $b2sSystem = new B2S_System();
        $b2sCheckBefore = $b2sSystem->check('before');
        if (is_array($b2sCheckBefore)) {
            $b2sSystem->deactivatePlugin();
            wp_die($b2sSystem->getErrorMessage($b2sCheckBefore) . ' ' . esc_html__('or', 'blog2social') . '  <a href="' . esc_url(admin_url("/plugins.php", "http")) . '/">' . esc_html__('back to install plugins', 'blog2social') . '</a>');
        }

        global $wpdb;
        $mySqlPermission = true;
        /*
         * Change Table Names with Prefix
         */
        if ($wpdb->base_prefix != 'b2s_' && $wpdb->prefix != 'b2s_') {
            $oldTables = $wpdb->get_results('SHOW TABLES LIKE "b2s_%%"');
            foreach ($oldTables as $v => $table) {
                $tableVars = array_values(get_object_vars($table));
                if (isset($tableVars[0]) && !empty($tableVars[0])) {
                    $wpdb->query("ALTER TABLE `{$tableVars[0]}` RENAME `{$wpdb->base_prefix}{$tableVars[0]}`;");
                }
            }
        }

        $sqlCreateUser = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}b2s_user` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `token` varchar(255) NOT NULL,
          `blog_user_id` int(11) NOT NULL,
          `feature` TINYINT(2) NOT NULL,
          `state_url` TINYINT(2) NOT NULL,
          `register_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
          PRIMARY KEY (`id`), INDEX `blog_user_id` (`blog_user_id`), INDEX `token` (`token`), INDEX `feature` (`feature`)
          ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1;";
        $wpdb->query($sqlCreateUser);

        $b2sUserCols = $wpdb->get_results('SHOW COLUMNS FROM ' . $wpdb->prefix . 'b2s_user');
        if (is_array($b2sUserCols) && isset($b2sUserCols[0])) {
            $b2sUserColsData = array();
            foreach ($b2sUserCols as $key => $value) {
                if (isset($value->Field) && !empty($value->Field)) {
                    $b2sUserColsData[] = $value->Field;
                }
            }
            if (!in_array("register_date", $b2sUserColsData)) {
                $wpdb->query("ALTER TABLE {$wpdb->prefix}b2s_user ADD register_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
            }
            if (!in_array("state_url", $b2sUserColsData)) {
                $wpdb->query("ALTER TABLE {$wpdb->prefix}b2s_user ADD state_url TINYINT(2) NOT NULL DEFAULT '1'");
            }
        }

//Notice Rating - stop rating remember again
//$wpdb->query('UPDATE `b2s_user` SET `feature` = 0');

        $keys = $wpdb->get_results('SHOW INDEX FROM `' . $wpdb->prefix . 'b2s_user`');
        $allowIndexUser = array('PRIMARY', 'blog_user_id', 'token', 'feature');
        foreach ($keys as $k => $value) {
            if (!in_array($value->Key_name, $allowIndexUser)) {
                $wpdb->query('ALTER TABLE `' . $wpdb->prefix . 'b2s_user` DROP INDEX ' . $value->Key_name);
            }
        }

        $sqlCreateUserPosts = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}b2s_posts` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `post_id` int(11) NOT NULL,
          `blog_user_id` int(11) NOT NULL,
          `last_edit_blog_user_id` int(11) NOT NULL,
          `user_timezone` TINYINT NOT NULL DEFAULT '0',
          `sched_details_id` INT NOT NULL,
          `sched_type` TINYINT NOT NULL DEFAULT '0',
          `sched_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
          `sched_date_utc` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
          `publish_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
          `publish_link` varchar(255) NOT NULL,
          `publish_error_code` varchar(100) NOT NULL,
          `network_details_id` int(11) NOT NULL,
          `post_for_relay` TINYINT NOT NULL DEFAULT '0',
          `post_for_approve` TINYINT NOT NULL DEFAULT '0',
          `relay_primary_post_id` int(11) NOT NULL DEFAULT '0',
          `relay_delay_min` int(11) NOT NULL DEFAULT '0',
          `hook_action` TINYINT NOT NULL DEFAULT '0',
          `hide` TINYINT NOT NULL DEFAULT '0',
          `v2_id` int(11) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`), INDEX `post_id` (`post_id`), INDEX `blog_user_id` (`blog_user_id`) , INDEX `sched_details_id` (`sched_details_id`),
            INDEX `sched_date` (`sched_date`), INDEX `sched_date_utc` (`sched_date_utc`), INDEX `publish_date` (`publish_date`) , INDEX `relay_primary_post_id` (`relay_primary_post_id`) ,
            INDEX `hook_action` (`hook_action`), INDEX `hide` (`hide`)
          ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;";
        $wpdb->query($sqlCreateUserPosts);

        //since V4.8.0
        $b2sPostsCols = $wpdb->get_results('SHOW COLUMNS FROM ' . $wpdb->prefix . 'b2s_posts');
        if (is_array($b2sPostsCols) && isset($b2sPostsCols[0])) {
            $b2sPostsColsData = array();
            foreach ($b2sPostsCols as $key => $value) {
                if (isset($value->Field) && !empty($value->Field)) {
                    $b2sPostsColsData[] = $value->Field;
                }
            }
            if (!in_array("last_edit_blog_user_id", $b2sPostsColsData)) {
                $wpdb->query("ALTER TABLE {$wpdb->prefix}b2s_posts ADD last_edit_blog_user_id INT NOT NULL DEFAULT '0'");
            }
            if (!in_array("post_for_relay", $b2sPostsColsData)) {
                $wpdb->query("ALTER TABLE {$wpdb->prefix}b2s_posts ADD post_for_relay TINYINT NOT NULL DEFAULT '0'");
            }
            if (!in_array("post_for_approve", $b2sPostsColsData)) {
                $wpdb->query("ALTER TABLE {$wpdb->prefix}b2s_posts ADD post_for_approve TINYINT NOT NULL DEFAULT '0'");
            }
            if (!in_array("relay_primary_post_id", $b2sPostsColsData)) {
                $wpdb->query("ALTER TABLE {$wpdb->prefix}b2s_posts ADD relay_primary_post_id int(11) NOT NULL DEFAULT '0'");
                $wpdb->query('ALTER TABLE `' . $wpdb->prefix . 'b2s_posts` ADD INDEX(`relay_primary_post_id`)');
            }
            if (!in_array("relay_delay_min", $b2sPostsColsData)) {
                $wpdb->query("ALTER TABLE {$wpdb->prefix}b2s_posts ADD relay_delay_min int(11) NOT NULL DEFAULT '0'");
            }
        }

        $keys = $wpdb->get_results('SHOW INDEX FROM `' . $wpdb->prefix . 'b2s_posts`');
        $allowIndexPosts = array('PRIMARY', 'post_id', 'blog_user_id', 'sched_details_id', 'sched_date', 'sched_date_utc', 'publish_date', 'relay_primary_post_id', 'hook_action', 'hide');
        foreach ($keys as $k => $value) {
            if (!in_array($value->Key_name, $allowIndexPosts)) {
                $wpdb->query('ALTER TABLE `' . $wpdb->prefix . 'b2s_posts` DROP INDEX ' . $value->Key_name);
            }
        }

        //Change Collation >=V4.0 Emoji
        $existsTable = $wpdb->get_results('SHOW TABLES LIKE "' . $wpdb->prefix . 'b2s_posts_sched_details"');
        if (is_array($existsTable) && !empty($existsTable)) {
            $wpdb->query('ALTER TABLE `' . $wpdb->prefix . 'b2s_posts_sched_details` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
            $wpdb->query('ALTER TABLE `' . $wpdb->prefix . 'b2s_posts_sched_details` CHANGE sched_data sched_data TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
            $wpdb->query('REPAIR TABLE `' . $wpdb->prefix . 'b2s_posts_sched_details`');
            $wpdb->query('OPTIMIZE TABLE `' . $wpdb->prefix . 'b2s_posts_sched_details`');
        } else {
            $sqlCreateUserSchedDetails = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}b2s_posts_sched_details` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `sched_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `image_url` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci AUTO_INCREMENT=1 ;";
            $wpdb->query($sqlCreateUserSchedDetails);
        }

        $sqlCreateUserNetworkDetails = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}b2s_posts_network_details` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `network_id` TINYINT NOT NULL,
            `network_type` TINYINT NOT NULL,
            `network_auth_id` int(11) NOT NULL,
            `network_display_name` varchar(100) NOT NULL,
            `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            PRIMARY KEY (`id`)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;";
        $wpdb->query($sqlCreateUserNetworkDetails);

        //since 6.1.0 add settings
        $b2sNetworkDetailsCols = $wpdb->get_results('SHOW COLUMNS FROM ' . $wpdb->prefix . 'b2s_posts_network_details');
        if (is_array($b2sNetworkDetailsCols) && isset($b2sNetworkDetailsCols[0])) {
            $b2sNetworkDetailsColsData = array();
            foreach ($b2sNetworkDetailsCols as $key => $value) {
                if (isset($value->Field) && !empty($value->Field)) {
                    $b2sNetworkDetailsColsData[] = $value->Field;
                }
            }
            if (!in_array("data", $b2sNetworkDetailsColsData)) {
                $wpdb->query("ALTER TABLE {$wpdb->prefix}b2s_posts_network_details ADD data text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        }

        $sqlCreateUserContact = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}b2s_user_contact`(
          `id` int(5) NOT  NULL  AUTO_INCREMENT ,
          `blog_user_id` int(11)  NOT  NULL ,
          `name_mandant` varchar(100)  NOT  NULL ,
          `created` datetime NOT  NULL DEFAULT  '0000-00-00 00:00:00',
          `name_presse` varchar(100)  NOT  NULL ,
          `anrede_presse` enum('0','1','2')  NOT  NULL DEFAULT  '0' COMMENT  '0=Frau,1=Herr 2=keine Angabe',
          `vorname_presse` varchar(50)  NOT  NULL ,
          `nachname_presse` varchar(50)  NOT  NULL ,
          `strasse_presse` varchar(100)  NOT  NULL ,
          `nummer_presse` varchar(5)  NOT  NULL DEFAULT  '',
          `plz_presse` varchar(10)  NOT  NULL ,
          `ort_presse` varchar(75)  NOT  NULL ,
          `land_presse` varchar(3)  NOT  NULL DEFAULT  'DE',
          `email_presse` varchar(75)  NOT  NULL ,
          `telefon_presse` varchar(30)  NOT  NULL ,
          `fax_presse` varchar(30)  NOT  NULL ,
          `url_presse` varchar(150)  NOT  NULL ,
          PRIMARY  KEY (`id`) ,
          KEY `blog_user_id`(`blog_user_id`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1";
        $wpdb->query($sqlCreateUserContact);


        $sqlCreateNetworkSettings = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'b2s_user_network_settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `blog_user_id` int(11) NOT NULL,
            `mandant_id` int(11) NOT NULL,
            `network_auth_id` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `blog_user_id` (`blog_user_id`), INDEX `mandant_id` (`mandant_id`)
          ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1;';
        $wpdb->query($sqlCreateNetworkSettings);


        $sqlCreateDrafts = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}b2s_posts_drafts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `last_save_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            `blog_user_id` int(11) NOT NULL,
            `post_id` int(11) NOT NULL,
            `save_origin` tinyint(4) NOT NULL DEFAULT 0,
            `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `blog_user_id` (`blog_user_id`), INDEX `post_id` (`post_id`)
            ) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci AUTO_INCREMENT=1;";
        $wpdb->query($sqlCreateDrafts);

        //since V6.1.0
        $b2sPostsDraftsCols = $wpdb->get_results('SHOW COLUMNS FROM ' . $wpdb->prefix . 'b2s_posts_drafts');
        if (is_array($b2sPostsDraftsCols) && isset($b2sPostsDraftsCols[0])) {
            $b2sPostsDraftsColsData = array();
            foreach ($b2sPostsDraftsCols as $key => $value) {
                if (isset($value->Field) && !empty($value->Field)) {
                    $b2sPostsDraftsColsData[] = $value->Field;
                }
            }
            if (!in_array("save_origin", $b2sPostsDraftsColsData)) {
                $wpdb->query("ALTER TABLE {$wpdb->prefix}b2s_posts_drafts ADD save_origin TINYINT NOT NULL DEFAULT '0'");
            }
        }

        $sqlCreateFavorites = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}b2s_posts_favorites` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `blog_user_id` int(11) NOT NULL,
            `post_id` int(11) NOT NULL,
            `save_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`id`),
            INDEX `blog_user_id` (`blog_user_id`), INDEX `post_id` (`post_id`)
            ) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci AUTO_INCREMENT=1;";

        if (!$wpdb->query($sqlCreateFavorites)) {
            $mySqlPermission = false;
        }

        /*
         * SET SAFETY AUTO-INCREMENT
         */

        $wpdb->query("ALTER TABLE `{$wpdb->prefix}b2s_posts` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}b2s_posts_sched_details` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}b2s_posts_network_details` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}b2s_user` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}b2s_user_contact` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}b2s_user_network_settings` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
        $wpdb->query("ALTER TABLE `{$wpdb->prefix}b2s_posts_drafts` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
        if (!$wpdb->query("ALTER TABLE `{$wpdb->prefix}b2s_posts_favorites` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;")) {
            $mySqlPermission = false;
        }

        if (!$mySqlPermission) {
            $b2sSystem->deactivatePlugin();
            wp_die($b2sSystem->getErrorMessage(array('dbTable' => false)) . ' ' . esc_html__('or', 'blog2social') . '  <a href="' . esc_url(admin_url("/plugins.php", "http")) . '/">' . esc_html__('back to install plugins', 'blog2social') . '</a>');
        }

        //Activate Social Meta Tags
        $options = new B2S_Options(0, 'B2S_PLUGIN_GENERAL_OPTIONS');
        if ($options->_getOption('og_active') === false || $options->_getOption('card_active') === false) {
            $options->_setOption('og_active', 1);
            $options->_setOption('card_active', 1);
        }
        //init roles & capabilities
        $this->initCaps();
    }

    public function deactivatePlugin($allBlogs) {
        global $wpdb;
        $optionDeleteSchedPosts = get_option('B2S_PLUGIN_DEACTIVATE_SCHED_POST');
        if ($allBlogs && is_multisite()) {
            $sql = "SELECT blog_id FROM {$wpdb->base_prefix}blogs";
            $blogIds = $wpdb->get_results($sql, ARRAY_A);
            if (is_array($blogIds) && !empty($blogIds)) {
                foreach ($blogIds as $blogId) {
                    switch_to_blog($blogId['blog_id']);
                    if ($optionDeleteSchedPosts !== false && (int) $optionDeleteSchedPosts == 1) {
                        update_option("B2S_PLUGIN_DEACTIVATE_SCHED_POST", 1, false);
                    } else {
                        delete_option("B2S_PLUGIN_DEACTIVATE_SCHED_POST");
                    }
                    deactivate_plugins(B2S_PLUGIN_HOOK, false, false);
                    restore_current_blog();
                }
                return true;
            }
        }
        if ($optionDeleteSchedPosts !== false && (int) $optionDeleteSchedPosts == 1) {
            $existsTable = $wpdb->get_results('SHOW TABLES LIKE "' . $wpdb->prefix . 'b2s_user"');
            if (is_array($existsTable) && !empty($existsTable)) {
                $sqlPosts = "SELECT a.token FROM `{$wpdb->prefix}b2s_user` a INNER JOIN {$wpdb->prefix}b2s_posts ON a.`blog_user_id` = {$wpdb->prefix}b2s_posts.`blog_user_id` where {$wpdb->prefix}b2s_posts.`hide` = 0 AND {$wpdb->prefix}b2s_posts.`sched_type` != 3 AND {$wpdb->prefix}b2s_posts.`publish_date`= '0000-00-00 00:00:00' GROUP by a.blog_user_id";
                $results = $wpdb->get_results($sqlPosts, ARRAY_A);
                if (is_array($results) && !empty($results)) {
                    $tempData = array('action' => 'deleteBlogSchedPost', 'host' => get_site_url(), 'data' => serialize($results));
                    $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData));
                    if (isset($result->result) && $result->result == true) {
                        $data = array('hide' => '1', 'hook_action' => '0');
                        $where = array('publish_date' => '0000-00-00 00:00:00', 'hide' => '0');
                        $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d', '%d'), array('%d', '%d'));
                        delete_option('B2S_PLUGIN_DEACTIVATE_SCHED_POST');
                    }
                }
            }
        }
    }

    public function releaseLocks() {
        require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
        $options = new B2S_Options(get_current_user_id());
        $lock = $options->_getOption("B2S_PLUGIN_USER_CALENDAR_BLOCKED");

        if ($lock) {
            delete_option("B2S_PLUGIN_CALENDAR_BLOCKED_" . $lock);
            $options->_setOption("B2S_PLUGIN_USER_CALENDAR_BLOCKED", false);
        }
    }

}
