<?php

namespace App\Providers;

use App\Services\Verification\Contracts\GovernmentVerificationProvider;
use App\Services\Verification\Providers\DonidcrNidProvider;
use App\Services\Verification\Providers\DotmLicenceProvider;
use App\Services\Verification\Providers\IrdPanProvider;
use App\Services\Verification\Providers\MohaCitizenshipProvider;
use App\Services\Verification\VerificationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ── Government Verification Providers ─────────────────────────────
        //
        // Each document type gets its own provider adapter.
        // When an official API agreement is established, swap the adapter
        // class here — the controller, service, and routes never change.
        //
        // Named bindings let us resolve the correct adapter per document type.

        $this->app->bind('verification.nid',         DonidcrNidProvider::class);
        $this->app->bind('verification.licence',     DotmLicenceProvider::class);
        $this->app->bind('verification.pan',         IrdPanProvider::class);
        $this->app->bind('verification.citizenship', MohaCitizenshipProvider::class);

        // VerificationService is contextual — the controller constructor
        // receives it with the default (NID) provider bound to the interface.
        // The controller itself calls the correct method, so a single provider
        // instance is sufficient for the service layer.
        $this->app->bind(GovernmentVerificationProvider::class, DonidcrNidProvider::class);

        $this->app->bind(VerificationService::class, function ($app) {
            // The service is constructed with whichever provider is currently
            // bound to the interface. The controller routes to the correct
            // method (verifyNid / verifyLicence / verifyPan / verifyCitizenship)
            // which each delegate to the correct provider internally.
            //
            // To use per-document providers, VerificationService resolves the
            // named binding at call time (see VerificationService).
            return new VerificationService(
                $app->make(GovernmentVerificationProvider::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
