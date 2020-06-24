<?php

namespace M3\GoogleSpreadSheet;
use Google_Client;
use GuzzleHttp\Client;
use Google_Service_Sheets;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class SpreadSheetService
{
    private $googleSheetService;
    private $progressBar;

    public function __construct()
    {
        $output = new ConsoleOutput();
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% -- %message%');
        $this->progressBar = new ProgressBar($output, 100);
        $this->progressBar->setFormat('custom');
        $this->progressBar->start();
    }

    public function validateInput($url, $table)
    {
    
        $this->progressBar->advance(5);
        $this->progressBar->setMessage('Validating Input Options');
        
        if ( is_null($url) ) {
           return [
               'error' => true,
               'message' => 'File Url is not provided.'
           ];
        }

        if ( is_null($table) ) {
            return [
                'error' => true,
                'message' => 'Table Name is not provided.'
            ];
        }

        $this->progressBar->advance(10);
        $this->progressBar->setMessage('Validated Input Options');

        return [
            'error' => false
        ];


    }

    public function process($url, $table, $credFile)
    {
        $spreadSheetId = $this->getSpreadSheetIdFromUrl($url);
        
        $this->progressBar->advance(15);
        $this->progressBar->setMessage('Setting up Goggle Client');

        $pathToCred = base_path($credFile.'.json');
        $scopeOfSheets = "https://www.googleapis.com/auth/spreadsheets";

        $client = new Google_Client();

        $client->setAuthConfig($pathToCred);
        $client->addScope($scopeOfSheets);

        $this->googleSheetService = new Google_Service_Sheets($client);
        
        $this->progressBar->advance(15);
        $this->progressBar->setMessage('Setting up Goggle Client');
        $dimensions = $this->getDimensions($spreadSheetId);


        $this->progressBar->advance(50);

        if($dimensions['error']) {
            return [
                'error' => true,
                'message' => $dimensions['message']
            ];
        }

        $colRange = 'Sheet1!1:1';
        $range = 'Sheet1!A2:'.$dimensions['colCount'];
        
        $column = $this->googleSheetService->spreadsheets_values->batchGet($spreadSheetId,['ranges' => $colRange,'majorDimension' => 'ROWS']);
        $columnNames = $column->valueRanges[0]->values[0];
        
        $data = $this->googleSheetService->spreadsheets_values->batchGet($spreadSheetId,['ranges' => $range]);
        
        $this->progressBar->advance(5);
        $this->progressBar->setMessage('Sanitising Data from Google Sheets');

        //if count reaches the treshold, it is better to chunk the data 
        //since large datasize will impact on available ram
        $tableData = $data->valueRanges[0]->values;
        $insertData = [];

        foreach ($tableData as $key => $value) {
            $insertData[] = array_combine($columnNames, $value);
        }

        DB::table($table)
            ->insert($insertData);

        $this->progressBar->setMessage('Inserted Data from Google Sheets');
        
        $this->progressBar->finish();
        return [
            'error' => false,
            'message' => 'Data Inserted Successfully'
        ];
    }

    private function getSpreadSheetIdFromUrl($url)
    {
    
        $path = parse_url($url);
        preg_match("/d\/(.*?)\/edit/", $path['path'], $matches);
        
        if(count($matches) != 2) {
            return [
                'error' => true,
                'message' => 'Invalid Url.'
            ];
        }
        
        return $matches[1];

    }

    // private function authenticate() {
    //     $pathToCred = base_path('service_account.json');
    //     $scopeOfSheets = "https://www.googleapis.com/auth/spreadsheets";

    //     $client = new Google_Client();

    //     $client->setAuthConfig($pathToCred);
    //     $client->addScope($scopeOfSheets);

    //     $googleSheetService = new Google_Service_Sheets($client);

    //     return $googleSheetService;
    // }

    private function getDimensions($spreadSheetId)
    {
        $rowDimensions = $this->googleSheetService->spreadsheets_values->batchGet(
            $spreadSheetId,
            ['ranges' => 'Sheet1!A:A','majorDimension'=>'COLUMNS']
        );

        //if data is present at nth row, it will return array till nth row
        //if all column values are empty, it returns null
        $rowMeta = $rowDimensions->getValueRanges()[0]->values;
        if (! $rowMeta) {
            return [
                'error' => true,
                'message' => 'missing row data'
            ];
        }

        $colDimensions = $this->googleSheetService->spreadsheets_values->batchGet(
            $spreadSheetId,
            ['ranges' => 'Sheet1!1:1','majorDimension'=>'ROWS']
        );
        
        //if data is present at nth col, it will return array till nth col
        //if all column values are empty, it returns null
        $colMeta = $colDimensions->getValueRanges()[0]->values;
        if (! $colMeta) {
            return [
                'error' => true,
                'message' => 'missing row data'
            ];
        }

        return [
            'error' => false,
            'rowCount' => count($rowMeta[0]),
            'colCount' => $this->colLengthToColumnAddress(count($colMeta[0]))
        ];
    }

    public  function colLengthToColumnAddress($number)
    {
        if ($number <= 0) return null;

        $temp; $letter = '';
        while ($number > 0) {
            $temp = ($number - 1) % 26;
            $letter = chr($temp + 65) . $letter;
            $number = ($number - $temp - 1) / 26;
        }
        return $letter;
    }
}
