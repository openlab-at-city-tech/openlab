<?php

/**
 * Class TrustedLoginUsersTest
 *
 * @package Trustedlogin_Button
 */
use TEC\Common\TrustedLogin\SupportRole;
use TEC\Common\TrustedLogin\SupportUser;
/**
 * Sample test case.
 */
class TrustedLoginUsersTest extends WP_UnitTestCase
{
    /**
     * @var \TEC\Common\TrustedLogin\Client
     */
    private $TrustedLogin;
    /**
     * @var \TEC\Common\TrustedLogin\Config
     */
    private $config;
    /**
     * @var \TEC\Common\TrustedLogin\Logging
     */
    private $logging;
    /**
     * @var \TEC\Common\TrustedLogin\Endpoint
     */
    private $endpoint;
    private $default_settings = array();
    static $role_key;
    static $default_roles;
    public function setUp(): void
    {
        global $wpdb;
        parent::setUp();
        // role settings name in options table
        self::$role_key = $wpdb->get_blog_prefix(get_current_blog_id()) . 'user_roles';
        // copy current roles
        self::$default_roles = get_option(self::$role_key);
        $this->default_settings = array('role' => 'editor', 'caps' => array('add' => array('manage_options' => 'we need this to make things work real gud', 'edit_posts' => 'Access the posts that you created')), 'webhook_url' => 'https://www.trustedlogin.com/webhook-example/', 'auth' => array('api_key' => '9946ca31be6aa948'), 'decay' => WEEK_IN_SECONDS, 'vendor' => array(
            'namespace' => 'gravityview',
            'title' => 'GravityView',
            'display_name' => 'Floaty the Astronaut',
            'email' => 'support@gravityview.co',
            'website' => 'https://gravityview.co',
            'support_url' => 'https://gravityview.co/support/',
            // Backup to redirect users if TL is down/etc
            'logo_url' => '',
        ), 'reassign_posts' => true);
        $this->config = new \TEC\Common\TrustedLogin\Config($this->default_settings);
        $this->TrustedLogin = new \TEC\Common\TrustedLogin\Client($this->config);
        $this->logging = new \TEC\Common\TrustedLogin\Logging($this->config);
        $this->endpoint = new \TEC\Common\TrustedLogin\Endpoint($this->config, $this->logging);
    }
    private function _get_public_property($name, $object = null)
    {
        $class = '\TEC\Common\TrustedLogin\Client';
        if (is_object($object)) {
            $class = get_class($object);
        }
        $Reflection = new \ReflectionClass($class);
        $prop = $Reflection->getProperty($name);
        $prop->setAccessible(true);
        return $prop;
    }
    private function _get_public_method($name)
    {
        $method = $this->TrustedLoginReflection->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
    /**
     * @covers \TrustedLogin::support_user_create_role
     */
    public function test_support_user_create_role()
    {
        $this->_test_cloned_cap('administrator');
        $this->_test_cloned_cap('editor');
        $this->_test_cloned_cap('author');
        $this->_test_cloned_cap('contributor');
        $this->_test_cloned_cap('subscriber');
        $support_user = $this->_get_public_property('support_user');
        $TL_Support_Role = new SupportRole($this->config, $this->logging);
        $not_string_new_role = $TL_Support_Role->create(array('asdasd'), 'administrator');
        $this->assertWPError($not_string_new_role, 'not string new role');
        $this->assertEquals('new_role_slug_not_string', $not_string_new_role->get_error_code(), 'not string new role');
        $not_string_clone_role = $TL_Support_Role->create('administrator', array('asdasd'));
        $this->assertWPError($not_string_clone_role, 'not string clone role');
        $this->assertEquals('cloned_role_slug_not_string', $not_string_clone_role->get_error_code(), 'not string clone role');
        $this->assertTrue($TL_Support_Role->create('administrator', '1') instanceof WP_Role, 'role already exists');
    }
    /**
     * @param $role
     */
    private function _test_cloned_cap($role)
    {
        $new_role = microtime();
        $TL_Support_Role = new SupportRole($this->config, $this->logging);
        $support_user = $this->_get_public_property('support_user');
        $new_role = $TL_Support_Role->create($new_role, $role);
        $this->assertTrue($new_role instanceof WP_Role);
        $remove_caps = array('create_users', 'delete_users', 'edit_users', 'promote_users', 'delete_site', 'remove_users', 'list_users');
        $new_role_caps = $new_role->capabilities;
        $cloned_caps = get_role($role)->capabilities;
        foreach ($remove_caps as $remove_cap) {
            $this->assertFalse(in_array($remove_cap, $new_role_caps, true));
            unset($cloned_caps[$remove_cap]);
        }
        $added_caps = $this->config->get_setting('caps/add');
        foreach ((array) $added_caps as $added_cap => $reason) {
            // The caps that were requested to be added are not allowed
            if (in_array($added_cap, $remove_caps, true)) {
                $this->assertFalse(in_array($added_cap, array_keys($new_role_caps), true), 'restricted caps were added, but should not have been');
            } else {
                $this->assertTrue(in_array($added_cap, array_keys($new_role_caps), true), $added_cap . ' was not added, but should have been (for ' . $role . ' role)');
                $cloned_caps[$added_cap] = true;
            }
        }
        /**
         *  This cap is added by {@see SupportRole::create()} for all cloned roles.
         */
        $this->assertTrue($new_role_caps[SupportRole::get_capability_flag($this->config->ns())]);
        // Now remove it from the list of caps to make sure the rest of caps for each role are equal.
        unset($new_role_caps[SupportRole::get_capability_flag($this->config->ns())]);
        $this->assertEquals($new_role_caps, $cloned_caps);
    }
    /**
     * @covers \TrustedLogin::create_support_user
     * @covers TrustedLogin::support_user_create_role
     * @throws Exception
     */
    public function test_create_support_user()
    {
        global $wp_roles;
        $this->_reset_roles();
        $support_user = new SupportUser($this->config, $this->logging);
        $user_id = $support_user->create();
        // Was the user created?
        $this->assertNotFalse($user_id);
        $this->assertNotWPError($user_id);
        $support_user = new \WP_User($user_id);
        $this->assertTrue($support_user->exists());
        // Was the role created?
        $TL_Support_Role = new SupportRole($this->config, $this->logging);
        $support_role_key = $TL_Support_Role->get_name();
        $this->assertTrue($wp_roles->is_role($support_role_key));
        $support_role = $wp_roles->get_role($support_role_key);
        $this->assertInstanceOf('WP_Role', $support_role, 'The support role key is "' . $support_role_key . '"');
        if (get_option('link_manager_enabled')) {
            $support_user->add_cap('manage_links');
        }
        $this->assertTrue(in_array($support_role_key, $support_user->roles, true));
        foreach ($support_role->capabilities as $expected_cap => $enabled) {
            $expect = true;
            // manage_links is magical.
            if ('manage_links' === $expected_cap) {
                $link_manager_enabled = get_option('link_manager_enabled');
                $expect = !empty($link_manager_enabled);
            }
            /**
             * This cap requires `delete_users` for normal admins, or is_super_admin() for MS, which we aren't testing
             *
             * @see map_meta_cap():393
             */
            if ('unfiltered_html' === $expected_cap) {
                $expect = !is_multisite();
            }
            if (!is_numeric($expected_cap)) {
                $this->assertSame($expect, $support_user->has_cap($expected_cap), 'Did not have ' . $expected_cap . ', which was set to ' . var_export($enabled, true));
            }
        }
        $username = sprintf(esc_html__('%s Support', 'trustedlogin'), $this->config->get_setting('vendor/title'));
        $this->assertSame($this->config->get_setting('vendor/display_name'), $support_user->display_name);
        $this->assertSame($this->config->get_setting('vendor/email'), $support_user->user_email);
        $this->assertSame(sanitize_user($username), $support_user->user_login);
        //
        //
        // Test error messages
        //
        //
        $this->_reset_roles();
        $TL_Support_User = new SupportUser($this->config, $this->logging);
        $support_user_id = $TL_Support_User->create();
        $support_user_1 = get_userdata($support_user_id);
        $this->assertEquals(sanitize_user($support_user_1->user_login), $support_user_1->user_login);
        $TL_Support_User = new SupportUser($this->config, $this->logging);
        $support_user_id_2 = $TL_Support_User->create();
        $support_user_2 = get_userdata($support_user_id_2);
        #$this->assertEquals( sanitize_user( $support_user_1->user_login ) . ' 1', $support_user_2->user_login );
        $this->_reset_roles();
        $config_with_bad_role = $this->default_settings;
        $config_with_bad_role['vendor']['title'] = microtime();
        $config_with_bad_role['vendor']['namespace'] = microtime();
        $config_with_bad_role['vendor']['email'] = microtime() . '@example.com';
        $config_with_bad_role['role'] = 'madeuprole';
        $this->assertEmpty(get_role('gravityview-support'), 'sanity check - gravityview-support role shouldn\'t exist');
        $this->assertEmpty(get_role($config_with_bad_role['role']), 'sanity check - madeuprole role shouldn\'t exist');
        $config_with_bad_role = new \TEC\Common\TrustedLogin\Config($config_with_bad_role);
        $TL_config_with_bad_role = new SupportUser($config_with_bad_role, $this->logging);
        $should_be_role_does_not_exist = $TL_config_with_bad_role->create();
        $this->assertWPError($should_be_role_does_not_exist);
        $this->assertSame('role_does_not_exist', $should_be_role_does_not_exist->get_error_code());
        $valid_config = $this->default_settings;
        $valid_config['vendor']['title'] = microtime();
        $valid_config['vendor']['namespace'] = microtime();
        $valid_config['vendor']['email'] = microtime() . '@example.com';
        $valid_config = new \TEC\Common\TrustedLogin\Config($valid_config);
        $TL_valid_config = new \TEC\Common\TrustedLogin\SupportUser($valid_config, $this->logging);
        // Check to see what happens when an error is returned during wp_insert_user()
        add_filter('pre_user_login', '__return_empty_string');
        $should_be_empty_login = $TL_valid_config->create();
        $this->assertWPError($should_be_empty_login);
        $this->assertSame('empty_user_login', $should_be_empty_login->get_error_code());
        remove_filter('pre_user_login', '__return_empty_string');
    }
    /**
     * Make sure the user meta and cron are added correctly
     *
     * @covers SupportUser::setup
     */
    function test_support_user_setup()
    {
        $current = $this->factory->user->create_and_get(array('role' => 'administrator'));
        wp_set_current_user($current->ID);
        $user = $this->factory->user->create_and_get(array('role' => 'administrator'));
        $hash = 'asdsdasdasdasdsd';
        $hash_bin = sodium_crypto_generichash($hash, '', 16);
        $generichash = sodium_bin2hex($hash_bin);
        $expiry = $this->config->get_expiration_timestamp(DAY_IN_SECONDS);
        $cron = new \TEC\Common\TrustedLogin\Cron($this->config, $this->logging);
        $TL_Support_User = new SupportUser($this->config, $this->logging);
        $this->assertSame($generichash, $TL_Support_User->setup($user->ID, $hash, $expiry, $cron));
        $expires_meta_key = $this->_get_public_property('expires_meta_key', $TL_Support_User)->getValue($TL_Support_User);
        $this->assertSame((string) $expiry, get_user_option($expires_meta_key, $user->ID));
        $created_by_meta_key = $this->_get_public_property('created_by_meta_key', $TL_Support_User)->getValue($TL_Support_User);
        $this->assertSame((string) $current->ID, get_user_option($created_by_meta_key, $user->ID));
        // We are scheduling a single event cron, so it will return `false` when using wp_get_schedule().
        // False is the same result as an error, so we're doing more legwork here to validate.
        $crons = _get_cron_array();
        /** @see wp_get_schedule The hash/serialize/array/hash nonsense is replicating that behavior */
        $cron_id = md5(serialize(array($generichash)));
        $cron_key = 'trustedlogin/' . $this->config->ns() . '/access/revoke';
        $this->assertArrayHasKey($expiry, $crons);
        $this->assertArrayHasKey($cron_key, $crons[$expiry]);
        $this->assertArrayHasKey($cron_id, $crons[$expiry][$cron_key]);
        $this->assertSame($generichash, $crons[$expiry][$cron_key][$cron_id]['args'][0]);
    }
    /**
     * Reset the roles to default WordPress roles
     */
    private function _reset_roles()
    {
        update_option(self::$role_key, self::$default_roles);
        // we want to make sure we're testing against the db, not just in-memory data
        // this will flush everything and reload it from the db
        unset($GLOBALS['wp_user_roles']);
        global $wp_roles;
        $wp_roles = new WP_Roles();
    }
}