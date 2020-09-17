<?php

require_once('../vendor/autoload.php');
require_once('./helpers.php');

$infusionsoft = set_up_and_get_infusionsoft(__FILE__);

try {

    $contacts = $infusionsoft->data()
        ->query('Contact', 1, 0, ['Id' => '%'], ['Id', 'FirstName', 'LastName'], 'Id', true);

    var_dump($contacts->toArray());

} catch (\Infusionsoft\InfusionsoftException $e) {
    die($e->getMessage());
}

