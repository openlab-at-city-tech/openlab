<?php return array(
    'root' => array(
        'name' => 'wpauth/pdf-embedder',
        'pretty_version' => '4.9.2',
        'version' => '4.9.2.0',
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
            'reference' => '3fe23dd6d9c692ec019ba1cff225f960699a0da3',
            'type' => 'metapackage',
            'install_path' => null,
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => true,
        ),
        'woocommerce/action-scheduler' => array(
            'pretty_version' => '3.9.2',
            'version' => '3.9.2.0',
            'reference' => 'efbb7953f72a433086335b249292f280dd43ddfe',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../woocommerce/action-scheduler',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'wpauth/pdf-embedder' => array(
            'pretty_version' => '4.9.2',
            'version' => '4.9.2.0',
            'reference' => null,
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
