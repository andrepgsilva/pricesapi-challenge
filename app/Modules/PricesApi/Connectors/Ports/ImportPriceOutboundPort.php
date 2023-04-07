<?php

namespace App\Modules\PricesApi\Connectors\Ports;

use App\Modules\PricesApi\Connectors\Adapters\Helpers\PricesSearchResult;

interface ImportPriceOutboundPort 
{
    function get(array $productsSkus, string $accountReference = null): PricesSearchResult;
}