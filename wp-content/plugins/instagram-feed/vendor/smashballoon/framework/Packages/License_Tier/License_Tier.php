<?php

/**
 * License Tier class.
 *
 * @package License_Tier
 */
namespace InstagramFeed\Vendor\Smashballoon\Framework\Packages\License_Tier;

use function InstagramFeed\Vendor\Smashballoon\Framework\flatten_array;
abstract class License_Tier
{
    /**
     * This gets the license key
     *
     * @var string
     */
    public $license_key_option_name;
    /**
     * This gets the license status
     *
     * @var string
     */
    public $license_status_option_name;
    /**
     * This gets the license data
     *
     * @var string
     */
    public $license_data_option_name;
    /**
     * Item ID of the basic, plus, elite and all access license tier
     *
     * @var integer
     */
    public $item_id_basic;
    public $item_id_plus;
    public $item_id_elite;
    public $item_id_all_access;
    /**
     * Is all access
     *
     * @var boolean
     */
    public $is_all_access;
    /**
     * Name of the basic, plus, and elite license tier
     *
     * @var string
     */
    public $license_tier_free_name;
    public $license_tier_basic_name;
    public $license_tier_plus_name;
    public $license_tier_elite_name;
    /**
     * Legacy item IDs
     *
     * @var integer
     */
    public $item_id_personal;
    public $item_id_business;
    public $item_id_developer;
    /**
     * Legacy license tier names
     *
     * @var string
     */
    public $license_tier_personal_name;
    public $license_tier_business_name;
    public $license_tier_developer_name;
    /**
     * This holds the license data
     *
     * @var array
     */
    public $license_data = [];
    /**
     * This holds the plugin features list
     *
     * @var array
     */
    protected $plugin_features = [];
    /**
     * This holds the legacy features list
     *
     * @var array
     */
    protected $legacy_features = [];
    /**
     * This holds the active license tier name
     *
     * @var string
     */
    protected $license_tier;
    public $edd_item_name;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->features_list();
        $this->legacy_features_list();
        $this->license_data();
    }
    /**
     * This defines the features list of the plugin
     *
     * @return void
     */
    abstract public function features_list();
    /**
     * This defines the legacy features list of the plugin
     *
     * @return void
     */
    public function legacy_features_list()
    {
        $this->legacy_features = [];
    }
    /**
     * Get license data
     *
     * @return void
     */
    public function license_data()
    {
        $license_data = (array) get_option($this->license_data_option_name);
        $license_tier = $this->license_tier_free_name;
        if (is_array($license_data) && !empty($license_data['item_id']) && !empty($license_data['price_id'])) {
            $license_tier = $this->convert_to_readable_plan_name($license_data);
        }
        $this->license_data = $license_data;
        $this->license_tier = $license_tier;
    }
    /**
     * This gets the license tier plan name in a readable format
     *
     * @return string|null
     */
    public function get_license_tier()
    {
        return $this->license_tier;
    }
    /**
     * Returns list of available features for a given plan
     *
     * @return array $tier_features returns tier features or empty array
     */
    public function tier_features()
    {
        $all_features = $this->plugin_features;
        $plan_name = $this->get_license_tier();
        $tier_features = [];
        $legacy_plans = [$this->license_tier_personal_name, $this->license_tier_business_name, $this->license_tier_developer_name];
        if (in_array($plan_name, $legacy_plans)) {
            $tier_features = $this->legacy_tier_features();
            return $tier_features;
        }
        if ($plan_name == $this->license_tier_free_name) {
            $tier_features = isset($all_features[$plan_name]) ? $all_features[$plan_name] : [];
        }
        if ($plan_name == $this->license_tier_basic_name) {
            $tier_features = isset($all_features[$plan_name]) ? $all_features[$plan_name] : [];
        }
        if ($plan_name == $this->license_tier_plus_name) {
            $tier_features = isset($all_features[$this->license_tier_basic_name]) && isset($all_features[$plan_name]) ? array_merge($all_features[$this->license_tier_basic_name], $all_features[$plan_name]) : [];
        }
        if ($plan_name == $this->license_tier_elite_name || $this->is_all_access) {
            $tier_features = flatten_array($all_features);
        }
        return $tier_features;
    }
    /**
     * Returns list of available features for a given legacy plan
     *
     * @return array $tier_features returns tier features or empty array
     */
    public function legacy_tier_features()
    {
        $all_features = $this->legacy_features;
        $plan_name = $this->get_license_tier();
        $tier_features = [];
        if ($plan_name == $this->license_tier_personal_name) {
            $tier_features = isset($all_features[$plan_name]) ? $all_features[$plan_name] : [];
        }
        if ($plan_name == $this->license_tier_business_name) {
            $tier_features = isset($all_features[$this->license_tier_personal_name]) && isset($all_features[$plan_name]) ? array_merge($all_features[$this->license_tier_personal_name], $all_features[$plan_name]) : [];
        }
        if ($plan_name == $this->license_tier_developer_name) {
            $tier_features = flatten_array($all_features);
        }
        return $tier_features;
    }
    /**
     * This is a helpful function to look for any specific feature on a given plan
     *
     * @params string $feature_name Expects a feature name as a string
     * @params string $plan_name Expects a given pricing plan as a string
     *
     * @return boolean $plan_exists returns boolean value
     */
    public function has_feature($feature_name, $plan_name)
    {
        $features_list = $this->plugin_features;
        $plan_exists = \false;
        if ($plan_name == $this->license_tier_basic_name) {
            $plan_exists = in_array($feature_name, $features_list[$this->license_tier_basic_name]);
        }
        if ($plan_name == $this->license_tier_plus_name) {
            $plan_exists = in_array($feature_name, $features_list[$this->license_tier_basic_name]) || in_array($feature_name, $features_list[$this->license_tier_plus_name]);
        }
        if ($plan_name == $this->license_tier_elite_name) {
            $plan_exists = in_array($feature_name, $features_list[$this->license_tier_basic_name]) || in_array($feature_name, $features_list[$this->license_tier_plus_name]) || in_array($feature_name, $features_list[$this->license_tier_elite_name]);
        }
        return $plan_exists;
    }
    /**
     * Map item id to tier name
     *
     * @return array
     */
    public function item_id_to_tier_name()
    {
        $item_id_to_tier_name = [$this->item_id_basic => $this->license_tier_basic_name, $this->item_id_plus => $this->license_tier_plus_name, $this->item_id_elite => $this->license_tier_elite_name, $this->item_id_personal => $this->license_tier_personal_name, $this->item_id_business => $this->license_tier_business_name, $this->item_id_developer => $this->license_tier_developer_name];
        return $item_id_to_tier_name;
    }
    /**
     * Get tier name
     *
     * @since string $license_tier_name
     */
    public function item_name_to_tier_name()
    {
        $license_tier_name = '';
        if (strpos($this->edd_item_name, 'Personal') !== \false) {
            $license_tier_name = $this->license_tier_personal_name;
        }
        if (strpos($this->edd_item_name, 'Business') !== \false) {
            $license_tier_name = $this->license_tier_business_name;
        }
        if (strpos($this->edd_item_name, 'Developer') !== \false) {
            $license_tier_name = $this->license_tier_developer_name;
        }
        if (strpos($this->edd_item_name, 'Basic') !== \false) {
            $license_tier_name = $this->license_tier_basic_name;
        }
        if (strpos($this->edd_item_name, 'Plus') !== \false) {
            $license_tier_name = $this->license_tier_plus_name;
        }
        if (strpos($this->edd_item_name, 'Elite') !== \false) {
            $license_tier_name = $this->license_tier_elite_name;
        }
        return $license_tier_name;
    }
    /**
     * This converts plan price id to a readable plan name
     *
     * @param array $license_data License data.
     *
     * @return string
     */
    public function convert_to_readable_plan_name(array $license_data)
    {
        $item_id = (int) $license_data['item_id'];
        $price_id = (int) $license_data['price_id'];
        $item_name_to_tier_name = $this->item_name_to_tier_name();
        $license_tier_name = $this->license_tier_basic_name;
        if (!empty($item_name_to_tier_name)) {
            // Check if it's all access.
            if ($item_id === $this->item_id_elite) {
                $license_tier_name = 'all_access';
                $this->is_all_access = \true;
            } else {
                $license_tier_name = $item_name_to_tier_name;
            }
        }
        return $license_tier_name;
    }
    /**
     * Check if the feature is available for the current license tier
     *
     * @param string $feature Feature to check.
     *
     * @return boolean
     */
    public function is_feature_available($feature)
    {
        $tier_features = $this->tier_features();
        if (in_array($feature, $tier_features)) {
            return \true;
        }
        return \false;
    }
}
