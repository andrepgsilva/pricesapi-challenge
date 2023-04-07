<?php

namespace App\Providers;

use App\Modules\PricesApi\Application\UseCases\GetPricesUseCase;
use App\Modules\PricesApi\Connectors\Adapters\ImportPriceFromJsonFileAdapter;
use App\Modules\PricesApi\Connectors\Adapters\ImportPriceFromMysqlAdapter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use App\Modules\PricesApi\Connectors\Ports\GetPriceInboundPort;

class PricesApiModuleServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(GetPriceInboundPort::class, function (Application $app) {
            return new GetPricesUseCase(
                new ImportPriceFromJsonFileAdapter,
                new ImportPriceFromMysqlAdapter,
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
