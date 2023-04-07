<?php

namespace App\Modules\PricesApi\Connectors\Adapters;

use App\Http\Controllers\Controller;
use App\Modules\PricesApi\Application\Dtos\GetPricesDto;
use App\Modules\PricesApi\Application\Dtos\GetPricesOutputDto;
use App\Modules\PricesApi\Connectors\Ports\GetPriceInboundPort;
use App\Modules\PricesApi\Connectors\Adapters\Validators\GetPricesRequestValidator;

class GetPricesControllerAdapter extends Controller
{
    public function __construct(
        private GetPriceInboundPort $getPricesUseCase
    ) {}

    public function execute(GetPricesRequestValidator $request) 
    {
        $validatedRequest = $request->validated();
        $productSkus = $validatedRequest['products_skus'];
        $accountReference = $validatedRequest['account_reference'] ?? null;

        $dto = new GetPricesDto();
        $dto->accountReference = $accountReference;
        $dto->productsIds = $productSkus;

        $pricesResult = $this->getPricesUseCase->execute($dto);

        $outputResult = GetPricesOutputDto::transform($pricesResult);

        return response()->json($outputResult);
    }
}