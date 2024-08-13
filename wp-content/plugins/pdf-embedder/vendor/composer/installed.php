<?php return array(
    'root' => array(
        'name' => 'wpauth/pdf-embedder',
        'pretty_version' => '4.8.2',
        'version' => '4.8.2.0',
        'reference' => null,
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'roave/security-advisories' => array(
            'pretty_version' => 'dev-latest',
            'version' => 'dev-latest',
            'reference' => 'cac81dc38cb1ea099552433245d0790b6e172211',
            'type' => 'metapackage',
            'install_path' => null,
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => true,
        ),
        'woocommerce/action-scheduler' => array(
            'pretty_version' => '3.7.4',
            'version' => '3.7.4.0',
            'reference' => '5fb655253dc004bb7a6d840da807f0949aea8bcd',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../woocommerce/action-scheduler',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'wpauth/pdf-embedder' => array(
            'pretty_version' => '4.8.2',
            'version' => '4.8.2.0',
            'reference' => null,
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
