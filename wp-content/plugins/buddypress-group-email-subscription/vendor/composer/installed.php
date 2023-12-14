<?php return array(
    'root' => array(
        'name' => 'boonebgorges/buddypress-group-email-subscription',
        'pretty_version' => '4.0.0',
        'version' => '4.0.0.0',
        'reference' => NULL,
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'boonebgorges/buddypress-group-email-subscription' => array(
            'pretty_version' => '4.0.0',
            'version' => '4.0.0.0',
            'reference' => NULL,
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'composer/installers' => array(
            'pretty_version' => 'v1.12.0',
            'version' => '1.12.0.0',
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'hard-g/buddypress-12.0-url-polyfills' => array(
            'pretty_version' => '1.0.1',
            'version' => '1.0.1.0',
            'reference' => 'e61685bec96f2673b6b225510eec8b74c3d6a2cf',
            'type' => 'library',
            'install_path' => __DIR__ . '/../hard-g/buddypress-12.0-url-polyfills',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
    ),
);
