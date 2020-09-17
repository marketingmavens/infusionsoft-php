<?php

namespace Infusionsoft\Api\Contracts;

interface Arrayable
{

    /**
     * Get the instance as an array.
     *
     * @return mixed
     */
    public function toArray();
}