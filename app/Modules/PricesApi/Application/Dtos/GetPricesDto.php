<?php

namespace App\Modules\PricesApi\Application\Dtos;

class GetPricesDto 
{
    public array $productsIds;
    public string|null $accountReference = null;
}