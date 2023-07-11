<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Support Request <?php echo site_url() ?></title>
</head>
<body>
<p><?php echo esc_html( $name ) ?><br/><?php echo esc_html( $email ) ?></p>
<p><?php echo nl2br( esc_html( $msg ) ) ?></p>
<?php if ( $cloud_email ) : ?>
<p>Bookly Cloud: <?php echo esc_html( $cloud_email ) ?></p>
<?php endif ?>
<ol>
    <?php foreach ( $plugins as $plugin ): ?>
        <li><?php echo esc_html( $plugin::getTitle() ) ?> v<?php echo esc_html( $plugin::getVersion() ) ?>: <b><?php echo esc_html( $plugin::getPurchaseCode() ) ?></b></li>
    <?php endforeach ?>
</ol>
<p><?php echo esc_html( $_SERVER['HTTP_REFERER'] ) ?></p>
</body>
</html>