<?php

namespace App\Modules\PricesApi\Application\Dtos;

use App\Modules\PricesApi\Connectors\Ports\helpers\GetPricesSearchResult;

class GetPricesOutputDto {
    public static function transform(GetPricesSearchResult $allPrices): array
    {
        return [
            'found' => $allPrices->found,
            'not_found' => $allPrices->notFound,
        ];
    }
}