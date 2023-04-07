<?php

namespace App\Modules\PricesApi\Application\UseCases;

use App\Modules\PricesApi\Application\Dtos\GetPricesDto;
use App\Modules\PricesApi\Connectors\Ports\GetPriceInboundPort;
use App\Modules\PricesApi\Connectors\Ports\ImportPriceOutboundPort;
use App\Modules\PricesApi\Connectors\Ports\Helpers\GetPricesSearchResult;

class GetPricesUseCase implements GetPriceInboundPort
{
    public function __construct(
        private ImportPriceOutboundPort $firstFinderOption,
        private ImportPriceOutboundPort $secondFinderOption,
    ) {}

    public function execute(GetPricesDto $getProductDto): GetPricesSearchResult
    {
        $accountReference = $getProductDto->accountReference;
        $productsSkus = $getProductDto->productsIds;

        $firstOptionResults = $this->firstFinderOption->get($productsSkus, $accountReference);
        $justFirstOptionFoundResults = $firstOptionResults->found;
        $justFirstOptionNotFoundResults = $firstOptionResults->notFound;

        if (count($justFirstOptionNotFoundResults) > 0) {
            $secondOptionResults = $this->secondFinderOption->get($justFirstOptionNotFoundResults, $accountReference);
            $justSecondOptionFoundResults = $secondOptionResults->found;
            $justSecondOptionNotFoundResults = $secondOptionResults->notFound;
        }

        $allResultsFound = array_merge($justFirstOptionFoundResults, $justSecondOptionFoundResults);

        $result = new GetPricesSearchResult();
        $result->found = $allResultsFound;
        $result->notFound = $justSecondOptionNotFoundResults;
        
        return $result;
    }
}