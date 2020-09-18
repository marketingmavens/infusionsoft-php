<?php

namespace Infusionsoft\Api\Rest;

use Infusionsoft\Api\Rest\Traits\CannotSync;
use Infusionsoft\Api\Rest\Traits\CannotFind;
use Infusionsoft\Api\Rest\Traits\CannotDelete;

class CompanyService extends RestModel
{

    use CannotSync, CannotDelete, CannotFind;

    public $full_url = 'https://api.infusionsoft.com/crm/rest/v1/companies';
    public $return_key = 'companies';

}
