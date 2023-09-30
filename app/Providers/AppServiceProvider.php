<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GeneralSetting;
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Illuminate\Support\Facades\URL;
use View;
use Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      if(config('app.https')==true){
        URL::forceScheme('https');
      }
  
      $generalSetting = GeneralSetting::first() ?? new GeneralSetting();
      if ($generalSetting->site_title === null) {
        $generalSetting->site_title = 'KS Lab';
        $generalSetting->enable_over_sale = 0;
      }

      $generalSetting->enable_over_sale = $generalSetting->enable_over_sale==1 ? true : false;

      Config::set([
        'settings' => $generalSetting->toArray()
      ]);
      View::share([
        'generalSetting' => $generalSetting,
      ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
