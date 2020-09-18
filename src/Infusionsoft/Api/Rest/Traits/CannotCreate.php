<?php

namespace Infusionsoft\Api\Rest\Traits;

use Infusionsoft\InfusionsoftException;

trait CannotCreate
{

    public function create(array $params = [])
    {
        throw new InfusionsoftException(
            __CLASS__ . ' cannot use ' . __FUNCTION__ . ' function.'
        );
    }

}