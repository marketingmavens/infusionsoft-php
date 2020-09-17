<?php

require_once('../vendor/autoload.php');

function current_uri()
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[DOCUMENT_URI]";
}

function save_config($config)
{
    file_put_contents('.env.php', "<?php \n\nreturn " . str_replace(['array (', ')'], ['[', ']'],
            var_export($config, true) . ';'));

    return true;
}

function redirect_to_authorize($file)
{
    $currentUrl = rawurlencode(current_uri());
    $authorizeUrl = str_replace(basename($file, '.php'), 'authorize', current_uri());

    return redirect("$authorizeUrl?redirect_back_to=$currentUrl");
}

function redirect($url)
{
    header("Location: $url");
    die();
}

/**
 * @param null $file
 *
 * @return \Infusionsoft\Infusionsoft
 * @throws \Infusionsoft\InfusionsoftException
 */
function set_up_and_get_infusionsoft($file = null)
{
    $config = require_once(__DIR__ . '/.env.php');
    if (empty($file) && empty($config['token'])) {
        throw new Exception('You need to authorize token first');
    }

    if (empty($config['token'])) {
        redirect_to_authorize($file);
    }

    $infusionsoft = new \Infusionsoft\Infusionsoft($config);
    $infusionsoft->setToken(unserialize($config['token']));

    if ($infusionsoft->isTokenExpired()) {
        $token = $infusionsoft->refreshAccessToken();
        $config['token'] = serialize($token);
        save_config($config);
    }

    return $infusionsoft;
}