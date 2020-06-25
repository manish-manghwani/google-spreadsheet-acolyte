# google-spreadsheet-acolyte
[![Latest Version on Packagist](https://img.shields.io/packagist/v/manish-manghwani/google-spreadsheet-acolyte.svg?style=flat-square)](https://packagist.org/packages/manish-manghwani/google-spreadsheet-acolyte)
[![Total Downloads](https://img.shields.io/packagist/dt/manish-manghwani/google-spreadsheet-acolyte.svg?style=flat-square)](https://packagist.org/packages/amitavroy/laravel-sort-and-filter)

Laravel package to insert records from Google Spreadsheet

This package allows you to hassle-free insert records directly from Google Spreadsheet into your desired table.

It saves time to download the sheet into csv format and then write sql script to insert it into your database. 

## Pre requisite

1. You need to have google account.
2. Login to your account via link https://console.developers.google.com
3. Create one project.
4. Enable Google Sheet API
5. Create Service Account
6. Download Credentials.json
7. Create one spreadsheet and shared it with your service account email id

Add credentials.josn file to your root directory and add it in .gitignore list

## Installation

You can install the package via composer:

```bash
composer require manish-manghwani/google-spreadsheet-acolyte
```

## Usage

1. Get the Google Spreadsheet Url. It may look similiar to this https://docs.google.com/spreadsheets/d/1auqTdpciifOA6PH5JbSoxRFegdgdr48icvgwqsfWqrqI/edit#gid=0 
2. You should have name of the credential file that was dowloaded from service account (for eg: credentials.json)
3. You should have table name in which data will be inserted (for eg: dummy)

With above things in place you can run below command.

```bash
php artisan import:sheet --file-url=https://docs.google.com/spreadsheets/d/1auqTdpciifOA6PH5JbSoxRFegdgdr48icvgwqsfWqrqI/edit#gid=0 --table-name=dummy --credentials-file-name=credentials

```
NOTE: 
1. If sheet is not shared with service account email id, it will fail. 
2. First row of spreadsheet must be exactly same as of columns present in table.
3. Spreadsheet name should not be changed it must be Sheet1
4. Data to be inserted must be present in Sheet1

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email manghwani.manish1996@gmail.com instead of using the issue tracker.

## Credits

-   [Manish Manghwani](https://github.com/manish-manghwani)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
