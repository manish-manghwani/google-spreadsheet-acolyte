<?php

namespace M3\GoogleSpreadSheet;

use Illuminate\Support\ServiceProvider;
use M3\GoogleSpreadSheet\Commands\SpreadSheetCommand;

class SpreadSheetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            
            $this->commands([
                SpreadSheetCommand::class,
            ]);
        }
    }
}
