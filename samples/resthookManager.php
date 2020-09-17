<?php

require_once('../vendor/autoload.php');
require_once('./helpers.php');

$infusionsoft = set_up_and_get_infusionsoft(__FILE__);

/**
 * @param \Infusionsoft\Infusionsoft $infusionsoft
 *
 * @return mixed
 */
function resthookManager($infusionsoft)
{
    $resthooks = $infusionsoft->resthooks();

    // first, create a new task
    $resthook = $resthooks->create([
        'eventKey' => 'contact.add',
        'hookUrl'  => 'http://infusionsoft.app/verifyRestHook.php'
    ]);

    $resthook = $resthooks->find($resthook->id)->verify();

    return $resthook;
}

try {
    $resthook = resthookManager($infusionsoft);

    var_dump($resthook);

} catch (\Infusionsoft\InfusionsoftException $e) {
    die($e->getMessage());
}