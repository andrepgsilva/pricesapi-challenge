<?php

namespace App\Modules\PricesApi\Connectors\Adapters;
use App\Modules\PricesApi\Connectors\Ports\ImportPriceOutboundPort;
use App\Modules\PricesApi\Connectors\Adapters\Helpers\PricesSearchResult;

class ImportPriceFromJsonFileAdapter implements ImportPriceOutboundPort
{
    private function getJsonContentDecoded(): array
    {
        $fileImported = file_get_contents(config('liveprices.json.filePath'));
     
        return json_decode($fileImported, true);
    }

    private function searchForPrice(array $productsSkus, string $accountReference = null): array
    {
        $allPrices = $this->getJsonContentDecoded();

        $skuPricesNotFound = $productsSkus;
        $lowestPrices = [];

        foreach ($productsSkus as $sku) {
            foreach($allPrices as $priceBlock) {
                if (isset($priceBlock['account'])) {
                    if ($accountReference === null) continue;
                    if ($accountReference !== $priceBlock['account']) continue;
                    if ($sku !== $priceBlock['sku']) continue;

                    if (! isset($lowestPrices[$sku])) {
                        $lowestPrices[$sku] = $priceBlock;
                        continue;
                    }
                    
                    if ($priceBlock['price'] < $lowestPrices[$sku]['price']) {
                        $lowestPrices[$sku] = $priceBlock;
                        continue;
                    }
                }

                if ($sku == $priceBlock['sku']) {
                    if (! isset($lowestPrices[$sku])) {
                        $lowestPrices[$sku] = $priceBlock;
                        continue;
                    }
                    
                    if ($priceBlock['price'] < $lowestPrices[$sku]['price']) {
                        $lowestPrices[$sku] = $priceBlock;
                        continue;
                    }
                }
            }

            $skuPricesNotFound = array_diff($skuPricesNotFound, array_keys($lowestPrices));
            $lowestPrices = array_values($lowestPrices);
        }

        return [
            'found' => $lowestPrices,
            'notFound' => $skuPricesNotFound
        ]; 
    }

    function get(array $productsSkus, string $accountReference = null): PricesSearchResult
    {
        $searchResult = $this->searchForPrice($productsSkus, $accountReference);

        $result = new PricesSearchResult();
        $result->found = $searchResult['found'];
        $result->notFound = $searchResult['notFound'];
        
        return $result;
    }
}