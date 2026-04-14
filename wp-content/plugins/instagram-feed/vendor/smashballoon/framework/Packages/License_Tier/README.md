# Licnese Tier #

This package helps to differentiate license tier features in plugins. This package contains a `License_Tier.php` abastract class and `Sample_Plugin_License_Tier.php` class which is a sample class that can be used on a plugin level with modifications.

__Usage:__

1. Copy the class `Sample_Plugin_License_Tier.php` and paste in your plugin folder.
2. Change the class name to something like `Instagram_License_Tier.php` 
3. Change `$license_key_option_name`, `$license_status_option_name`, `$license_data_option_name` properties to the respected license related options. 
4. Open the main plugin file e.g. `instagram-feed.php` and get the plugins download ID's of three different tiers. e.g. from [this line](https://github.com/awesomemotive/instagram-feed-pro/blob/development/instagram-feed.php#L30)
5. Now change the item ID for basic, plus, and elite in the respected properties of `Sample_Plugin_License_Tier.php` class.
6. Update the license features for each tier under `features_list()` method.

Suppose, we are in the Feed_Builder.php file and need to get the list of features against the license data user already have in database.

```
// Instagram License Tier
$license_tier = new Instagram_License_Tier();
$license_tier_features = $license_tier->tier_features();
```

This will return only the features are listed for that specific tier that declared under the `features_list()` method.

We can pass it to `builder.js` and access the features list. Then we can restrict the other features in the create feed flow or feed customizer.

Inside `builder.js`, under `data()` please the below state
```
license_tier_features : sbi_builder.license_tier_features,
```

Now, let's add this helpful method to check if any feature exists in the features list array.
```
hasFeature : function ( feature_name ) {
	var self = this;
	return self.license_tier_features.includes( feature_name );
}
```

For all tiers, we do not need to restrict any features for basic tier as those features will be available to all tiers. Mainly, we need to restrict features from plus and elite tiers as those will be only available to those license groups. I'll give an example of how I restrict some features in the feed creation flow and customizer.

For example, carousel feed layout is only available to "plus" tier, that means "elite" tier can also have the feature.

We need to inside the Vue method that handles clicking on feed layout toggleset. And upon clicking, we need get what feed layout is selected, if carouel is selected then check if carousel_feed is in the features list.
```
self.hasFeature('carousel_feed')
```

Note: If carousel_feed is inside the 'plus' license tier, then if the user's license is personal then automatically carousel_feed will not be available and `self.hasFeature('carousel_feed')` will return false.
Leading that, if user's license is plus or elite, then `self.hasFeature('carousel_feed')` will return true.


