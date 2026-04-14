<?php

/**
 * Sample Plugin License Tier
 * 
 * @since 1.0
 */
namespace Smashballoon\Customizer;

use InstagramFeed\Vendor\Smashballoon\Framework\Packages\License_Tier\License_Tier;
class Sample_Plugin_License_Tier extends License_Tier
{
    /**
     * This gets the license key 
     */
    public $license_key_option_name = 'sbi_license_key';
    /**
     * This gets the license status
     */
    public $license_status_option_name = 'sbi_license_status';
    /**
     * This gets the license data
     */
    public $license_data_option_name = 'sbi_license_data';
    public $item_id_basic = 762236;
    // put item id for the basic tier
    public $item_id_plus = 762320;
    // put item id for the plus tier
    public $item_id_elite = 762322;
    // put item id for the elite tier
    public $item_id_all_access = 789157;
    // this is the all access item id, no need to change
    public $license_tier_basic_name = 'personal';
    // basic tier name
    public $license_tier_plus_name = 'business';
    // plus tier name
    public $license_tier_elite_name = 'developer';
    // elite tier name
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * This defines the features list of the plugin
     * 
     * @return void
     */
    public function features_list()
    {
        $features_list = ['personal' => [], 'business' => [], 'developer' => []];
        $this->plugin_features = $features_list;
    }
}
