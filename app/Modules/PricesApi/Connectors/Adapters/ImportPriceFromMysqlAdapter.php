<?php

namespace App\Modules\PricesApi\Connectors\Adapters;

use App\Modules\PricesApi\Connectors\Ports\ImportPriceOutboundPort;
use App\Modules\PricesApi\Connectors\Adapters\Helpers\PricesSearchResult;
use Illuminate\Support\Facades\DB;

class ImportPriceFromMysqlAdapter implements ImportPriceOutboundPort
{
    private function searchForPrivatePrices(array $productsSkus, string $accountReference): array
    {
        $binders = implode(',', array_fill(0, count($productsSkus), '?'));
        array_unshift($productsSkus, $accountReference);

        return DB::select(
            "SELECT 
                a.id, a.external_reference AS account_reference, 
                pd.id, pd.sku, pr.product_id, pr.account_id, 
                pr.quantity, pr.value AS price
                FROM prices AS pr
                JOIN accounts AS a ON pr.account_id = a.id
                JOIN products AS pd ON pr.product_id = pd.id
                WHERE 
                    a.external_reference = ?
                    AND pd.sku IN (" . $binders . ") ORDER BY pr.value ASC;",
                    $productsSkus
        );
    }

    private function searchForPublicPrices(array $productsSkus): array
    {
        $binders = implode(',', array_fill(0, count($productsSkus), '?'));

        return DB::select(
            "SELECT 
                pd.sku, MIN(pr.value) AS price FROM prices AS pr
                JOIN products AS pd ON pr.product_id = pd.id
                WHERE 
                    pr.account_id IS NULL AND pd.sku IN (" . $binders . ")
                    GROUP BY pd.sku ORDER BY MIN(pr.value) ASC;",
            $productsSkus
        );
    }

    private function searchForPrice(array $productsSkus, string $accountReference = null): array
    {
        $result = [];
        $skusNotFound = $productsSkus;

        if ($accountReference != null) {
            $result['found'] = $this->searchForPrivatePrices($productsSkus, $accountReference);
        }

        if ($accountReference === null || count($result['found']) === 0) {
            $result['found'] = $this->searchForPublicPrices($productsSkus);
        }
        
        foreach($result['found'] as $priceRow) {
            $index = array_search($priceRow->sku, $productsSkus);
            if ($index !== false) {
                array_splice($skusNotFound, $index, 1);
            }
        }

        $result['notFound'] = $skusNotFound;

        return $result;
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
