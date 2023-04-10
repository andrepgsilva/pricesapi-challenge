<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Modules\PricesApi\Application\Dtos\GetPricesDto;
use App\Modules\PricesApi\Application\Dtos\GetPricesOutputDto;
use App\Modules\PricesApi\Connectors\Ports\GetPriceInboundPort;

class ProductsController extends Controller
{
    public function __construct(
        private GetPriceInboundPort $getPricesUseCase
    ) {}
    
    public function index(): Response
    {
        $validatedRequest = [
            'account_reference' => 'JVNHZDYBSEHRND',
            'products_skus' => ['XPHKUD','QSCWER', 'EEAGVO', 'CHXNLG', 'asoinoidwq', 'IUQWDAS', 'GLWLLK', 'AHPHCA', 'CGPPHD']
        ];

        $productSkus = $validatedRequest['products_skus'];
        $accountReference = $validatedRequest['account_reference'] ?? null;

        $dto = new GetPricesDto();
        $dto->accountReference = $accountReference;
        $dto->productsIds = $productSkus;

        $pricesResult = $this->getPricesUseCase->execute($dto);

        $outputResult = GetPricesOutputDto::transform($pricesResult);

        return Inertia::render('Prices/Products', ['result' => $outputResult]);
    }    
}
