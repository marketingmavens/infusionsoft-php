<?php

require_once('../vendor/autoload.php');

require_once('./helpers.php');

$config = require_once('.env.php');

if (isset($_GET['redirect_back_to'])) {
    $config['redirect_back_to'] = $_GET['redirect_back_to'];
    save_config($config);
} elseif ( ! isset($_GET['code'])) {
    $config['redirect_back_to'] = '';
    save_config($config);
}

if (isset($_GET['code']) && empty($config['token'])) {
    $infusionsoft = new \Infusionsoft\Infusionsoft($config);

    $infusionsoft->requestAccessToken($_GET['code']);

    $config['token'] = serialize($infusionsoft->getToken());

    save_config($config);

    if ( ! empty($config['redirect_back_to'])) {
        header("Location: {$config['redirect_back_to']}");
        die();
    }

    die('Authorized, and saved');
}


if (empty($config['token']) && ! isset($_GET['code'])) {
    $config['redirect_uri'] = current_uri();
    save_config($config);

    $infusionsoft = new \Infusionsoft\Infusionsoft($config);

    echo '<a href="' . $infusionsoft->getAuthorizationUrl() . '">Click here to authorize</a>';

    die();
}