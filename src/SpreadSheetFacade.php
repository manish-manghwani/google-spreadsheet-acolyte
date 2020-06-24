<?php

namespace M3\GoogleSpreadSheet;

use Illuminate\Support\Facades\Facade;

/**
 * @see \M3\GoogleSpreadSheet\SpreadSheet
 */
class SpreadSheetFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'spreadsheet';
    }
}
