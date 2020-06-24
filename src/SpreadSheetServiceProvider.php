<?php

namespace M3\GoogleSpreadSheet;

use Illuminate\Support\ServiceProvider;
use M3\GoogleSpreadSheet\Commands\SpreadSheetCommand;

class SpreadSheetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/spreadshet.php' => config_path('spreadshet.php'),
            ], 'config');

            $this->commands([
                SpreadSheetCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/spreadsheet.php', 'spreadsheet');
    }
}
