<?php

namespace App\Providers;

use App\Models\ParkingSpot;
use App\Policies\ParkingSpotPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(ParkingSpot::class, ParkingSpotPolicy::class);

        Scramble::extendOpenApi(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'Sanctum')
            );
        });
    }
}