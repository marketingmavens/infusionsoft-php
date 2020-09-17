<?php

namespace Infusionsoft\Api\Contracts;

interface Jsonable
{

    /**
     * Get the instance as an JSON object.
     *
     * @return mixed
     */
    public function toJson();
}