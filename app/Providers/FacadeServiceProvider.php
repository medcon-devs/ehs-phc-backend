<?php

namespace App\Providers;

use App\Services\Facades\AgendaDetailsFacade;
use App\Services\Facades\AgendaFacade;
use App\Services\Facades\BaseFacade;
use App\Services\Facades\EventFacade;
use App\Services\Facades\FacultyFacade;
use App\Services\Facades\UserFacade;
use App\Services\Facades\UserWorkshopFacade;
use App\Services\Interfaces\AgendaDetailsInterface;
use App\Services\Interfaces\AgendaInterface;
use App\Services\Interfaces\BaseInterface;
use App\Services\Interfaces\EventInterface;
use App\Services\Interfaces\UserWorkshopsInterface;
use App\Services\Interfaces\FacultyInterface;
use App\Services\Interfaces\UserInterface;
use Illuminate\Support\ServiceProvider;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(BaseInterface::class, BaseFacade::class);
        $this->app->singleton(UserInterface::class, UserFacade::class);
        $this->app->singleton(EventInterface::class, EventFacade::class);
        $this->app->singleton(FacultyInterface::class, FacultyFacade::class);
        $this->app->singleton(AgendaInterface::class, AgendaFacade::class);
        $this->app->singleton(AgendaDetailsInterface::class, AgendaDetailsFacade::class);
        $this->app->singleton(UserWorkshopsInterface::class, UserWorkshopFacade::class);
    }
}
