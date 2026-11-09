<?php

use TEC\Common\TrustedLogin\Config;
/**
 * Class TrustedLoginConfigTest
 *
 * @package \TEC\Common\TrustedLogin\Client
 */
class TrustedLoginConfigTest extends WP_UnitTestCase
{
    /**
     * @covers \TEC\Common\TrustedLogin\Config::__construct
     * @covers \TEC\Common\TrustedLogin\Config::validate
     */
    public function test_config_vendor_stuff()
    {
        $expected_codes = array(400 => 'empty configuration array', 501 => 'replace default namespace', 406 => 'invalid configuration array');
        try {
            $config = new Config(array('vendor' => true));
            $config->validate();
        } catch (Exception $exception) {
            $this->assertEquals(406, $exception->getCode(), $exception->getMessage());
            $this->assertMatchesRegularExpression('/You need to set an API key./', $exception->getMessage());
            $this->assertMatchesRegularExpression('/vendor\/namespace/', $exception->getMessage());
            $this->assertMatchesRegularExpression('/vendor\/title/', $exception->getMessage());
            $this->assertMatchesRegularExpression('/vendor\/email/', $exception->getMessage());
            $this->assertMatchesRegularExpression('/vendor\/website/', $exception->getMessage());
            $this->assertMatchesRegularExpression('/vendor\/support_url/', $exception->getMessage());
        }
        try {
            $config = new Config(array('vendor' => true));
            $client = new \TEC\Common\TrustedLogin\Client($config);
            $this->assertTrue($client instanceof \Exception, 'When instantiating the Client, do not throw an exception; return one.');
        } catch (Exception $exception) {
        }
    }
    /**
     * @covers \TEC\Common\TrustedLogin\Config::__construct
     * @covers \TEC\Common\TrustedLogin\Config::validate
     */
    public function test_config_urls()
    {
        $valid_config = array('auth' => array('api_key' => 'not empty'), 'webhook_url' => 'https://www.google.com', 'vendor' => array('namespace' => 'jonesbeach', 'title' => 'Jones Beach Party', 'display_name' => null, 'email' => 'beach@example.com', 'website' => 'https://example.com', 'support_url' => 'https://example.com'));
        try {
            $invalid_website_url = $valid_config;
            $invalid_website_url['webhook_url'] = 'asdasdsd';
            $invalid_website_url['vendor']['support_url'] = 'asdasdsd';
            $invalid_website_url['vendor']['website'] = 'asdasdsd';
            $config = new Config($invalid_website_url);
            $config->validate();
            new \TEC\Common\TrustedLogin\Client($config);
        } catch (\Exception $exception) {
            $this->assertEquals(406, $exception->getCode());
            $this->assertMatchesRegularExpression('/webhook_url/', $exception->getMessage());
            $this->assertMatchesRegularExpression('/vendor\/support_url/', $exception->getMessage());
            $this->assertMatchesRegularExpression('/vendor\/website/', $exception->getMessage());
        }
    }
    /**
     * @covers \TEC\Common\TrustedLogin\Config::__construct
     * @covers \TEC\Common\TrustedLogin\Config::validate
     */
    public function test_config_namespace_length()
    {
        $valid_config = array('auth' => array('api_key' => 'not empty'), 'webhook_url' => 'https://www.google.com', 'vendor' => array('namespace' => 'jonesbeach', 'title' => 'Jones Beach Party', 'display_name' => null, 'email' => 'beach@example.com', 'website' => 'https://example.com', 'support_url' => 'https://example.com'));
        try {
            $invalid_config = $valid_config;
            $invalid_config['vendor']['namespace'] = str_repeat('a', Config::NAMESPACE_MIN_LENGTH - 1);
            $config = new Config($invalid_config);
            $config->validate();
            new \TEC\Common\TrustedLogin\Client($config);
        } catch (\Exception $exception) {
            $this->assertEquals(406, $exception->getCode());
            $this->assertMatchesRegularExpression('/Namespace length must be longer than/', $exception->getMessage());
        }
        try {
            $invalid_config = $valid_config;
            $invalid_config['vendor']['namespace'] = str_repeat('a', Config::NAMESPACE_MAX_LENGTH + 1);
            $config = new Config($invalid_config);
            $config->validate();
            new \TEC\Common\TrustedLogin\Client($config);
        } catch (\Exception $exception) {
            $this->assertEquals(406, $exception->getCode());
            $this->assertMatchesRegularExpression('/Namespace length must be shorter than/', $exception->getMessage());
        }
    }
    /**
     * @covers \TEC\Common\TrustedLogin\Config::__construct
     * @expectedException \TypeError
     * @expectedExceptionCode 406
     */
    public function test_config_not_array_string()
    {
        $this->expectException(TypeError::class);
        new Config('asdsadsd');
    }
    /**
     * @covers \TEC\Common\TrustedLogin\Config::__construct
     * @expectedException \Exception
     * @expectedExceptionCode 400
     */
    public function test_config_not_array_object()
    {
        $this->expectException(TypeError::class);
        $object = new ArrayObject();
        new Config($object);
    }
    /**
     * @covers \TEC\Common\TrustedLogin\Config::__construct
     * @expectedException \Exception
     * @expectedExceptionCode 400
     */
    public function test_config_empty_array()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);
        new Config(array());
    }
    /**
     * @covers \TEC\Common\TrustedLogin\Config::__construct
     * @expectedException \Exception
     * @expectedExceptionCode 400
     */
    public function test_config_empty()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);
        new Config();
    }
    /**
     * @covers \TEC\Common\TrustedLogin\Config::get_setting()
     */
    public function test_get_setting()
    {
        $config_array = array('auth' => array('api_key' => 'not empty'), 'webhook_url' => 'https://www.google.com', 'vendor' => array('namespace' => 'jones-party', 'title' => 'Jones Beach Party', 'display_name' => null, 'email' => 'beach@example.com', 'website' => 'https://example.com', 'support_url' => 'https://asdasdsd.example.com/support/'), 'paths' => array('css' => null), 'decay' => DAY_IN_SECONDS);
        $config = new Config($config_array);
        $config->validate();
        $TL = new \TEC\Common\TrustedLogin\Client($config);
        $this->assertEquals(DAY_IN_SECONDS, $config->get_setting('decay'));
        $this->assertEquals('https://www.google.com', $config->get_setting('webhook_url'));
        $this->assertEquals('Jones Beach Party', $config->get_setting('vendor/title'));
        $this->assertEquals(false, $config->get_setting('non-existent key'));
        $this->assertEquals('default override', $config->get_setting('non-existent key', 'default override'));
        $this->assertEquals(false, $config->get_setting('vendor/first_name'), 'Should use method default value (false) when returned value is NULL');
        $this->assertEquals('default override', $config->get_setting('vendor/first_name', 'default override'), 'should use default override if value is NULL');
        $this->assertEquals('', $config->get_setting('vendor/last_name'));
        $this->assertNotNull($config->get_setting('paths/css'), 'Being passed NULL should not override default.');
        $this->assertNotFalse($config->get_setting('paths/css'), 'Being passed NULL should not override default.');
        $this->assertMatchesRegularExpression('/.css$/', $config->get_setting('paths/css'), 'Being passed NULL should not override default.');
        // Test passed array values
        $passed_array = array('try' => 'and try again', 'first' => array('three_positive_integers' => 123));
        $this->assertEquals('and try again', $config->get_setting('try', null, $passed_array));
        $this->assertEquals(null, $config->get_setting('missssing', null, $passed_array));
        $this->assertEquals('123', $config->get_setting('first/three_positive_integers', null, $passed_array));
    }
    /**
     * @covers Config::get_expiration_timestamp
     */
    function test_get_expiration_timestamp()
    {
        $valid_config = array('auth' => array('api_key' => 'not empty'), 'vendor' => array('namespace' => 'asdasd', 'email' => 'asdasds', 'title' => 'asdasdsad', 'website' => 'https://example.com', 'support_url' => 'https://example.com/support/'));
        $config = new Config($valid_config);
        $this->assertSame(time() + WEEK_IN_SECONDS, $config->get_expiration_timestamp(), 'The method should have "WEEK_IN_SECONDS" set as default.');
        $this->assertSame(time() + DAY_IN_SECONDS, $config->get_expiration_timestamp(DAY_IN_SECONDS));
        $this->assertSame(time() + WEEK_IN_SECONDS, $config->get_expiration_timestamp(WEEK_IN_SECONDS));
        $this->assertSame(false, $config->get_expiration_timestamp(0));
        $valid_config['decay'] = 12345;
        $config_with_decay = new Config($valid_config);
        $this->assertSame(time() + 12345, $config_with_decay->get_expiration_timestamp());
        $valid_config['decay'] = 0;
        $config_no_decay = new Config($valid_config);
        $this->assertSame(false, $config_no_decay->get_expiration_timestamp());
    }
    /**
     * @covers Config::get_setting
     * Test the "create_ticket" setting.
     */
    function test_get_setting_create_ticket()
    {
        $config = new Config(array('auth' => array('api_key' => 'not empty'), 'vendor' => array('namespace' => 'asdasd', 'email' => 'asdasds', 'title' => 'asdasdsad', 'website' => 'https://example.com', 'support_url' => 'https://example.com/support/')));
        $this->assertSame(false, $config->get_setting('webhook/create_ticket'));
        $config = new Config(array('auth' => array('api_key' => 'not empty'), 'vendor' => array('namespace' => 'asdasd', 'email' => 'asdasds', 'title' => 'asdasdsad', 'website' => 'https://example.com', 'support_url' => 'https://example.com/support/'), 'webhook' => array('create_ticket' => true)));
        $this->assertSame(true, $config->get_setting('webhook/create_ticket'));
    }
}