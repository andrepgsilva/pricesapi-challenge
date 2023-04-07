<?php

namespace App\Modules\PricesApi\Connectors\Ports;

use App\Modules\PricesApi\Application\Dtos\GetPricesDto;
use App\Modules\PricesApi\Connectors\Ports\Helpers\GetPricesSearchResult;

interface GetPriceInboundPort 
{
    function execute(GetPricesDto $getProductDto): GetPricesSearchResult;
}