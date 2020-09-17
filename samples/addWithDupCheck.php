<?php

require_once('../vendor/autoload.php');
require_once('./helpers.php');

$infusionsoft = set_up_and_get_infusionsoft(__FILE__);

try {
    $email = 'john.doe@example.com';

    $contact = $infusionsoft->contacts()->where('email', $email)->first();

    if ( ! $contact) {
        $contact = [
            'given_name'      => 'John',
            'family_name'     => 'Doe',
            'email_addresses' => [
                [
                    'field' => 'EMAIL1',
                    'email' => $email
                ]
            ]
        ];

        $contact = $infusionsoft->contacts()->create($contact);
    }

    $withCustomFields = $infusionsoft->contacts()->with('custom_fields')->find($contact->id);

    var_dump($withCustomFields->toArray());

} catch (\Infusionsoft\InfusionsoftException $e) {
    die($e->getMessage());
}