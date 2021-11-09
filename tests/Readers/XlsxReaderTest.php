<?php

require_once('traits/ImportTrait.php');
require_once('traits/SingleImportTestingTrait.php');

use Mockery as m;
use Bhargavaaaa\Excel\Readers\LaravelExcelReader;
use Bhargavaaaa\Excel\Classes;

class XlsxReaderTest extends TestCase {

    /**
     * Import trait
     */
    use ImportTrait, SingleImportTestingTrait;

    /**
     * Filename
     * @var string
     */
    protected $fileName = 'files/test.xlsx';

}