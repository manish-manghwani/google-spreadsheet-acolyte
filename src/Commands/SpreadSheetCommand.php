<?php

namespace M3\GoogleSpreadSheet\Commands;

use Illuminate\Console\Command;
use M3\GoogleSpreadSheet\SpreadSheetService;


class SpreadSheetCommand extends Command
{
    private $importFileService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:sheet
    {--file-url= : Url of Google sheets}
    {--table-name= : Table name in which data needs to be inserted}
    {--credentials-file-name= : credentials json file for service account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that imports google sheet and insert it into database table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SpreadSheetService $importFileService)
    {
        parent::__construct();
        $this->importFileService = $importFileService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = $this->option('file-url');
        $table = $this->option('table-name');
        $credFile = $this->option('credentials-file-name');

        $postValidation = $this->importFileService->validateInput($url, $table);
        
        if($postValidation['error']) {
            $this->line('  ');
            $this->error($postValidation['message']);
            $this->line('  ');
            return false;
        }

        $processResponse = $this->importFileService->process($url, $table, $credFile);
        
        if($processResponse['error']) {
            $this->line('  ');
            $this->error($processResponse['message']);
            $this->line('  ');
            return false;
        }

        $this->line('  ');
            $this->line('<bg=green>'.$processResponse['message'].' in table '.$table.'</>');
            $this->line('  ');
            return false;
    }
}
