<?php

namespace App\Providers;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

ini_set("memory_limit",-1);
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {


    }

    /**
     * Bootstrap any application services.
     *
     */
    public function boot(Request $request)
    {
        if(env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        if (!App::runningInConsole()) {
            Config::set('default_pagination', 25);
            Paginator::useBootstrap();
            try {
                $view_keys = BusinessSetting::whereIn('key', ['business_name','logo','favicon','currency_symbol_position','country'])->get();
                foreach($view_keys as $key=>$value)
                {
                    view()->share($key, $value);
                }
            } catch (\Exception $e){

            }
        }
    }
}
