<?php

use Orchestra\Testbench\TestCase as TestBenchTestCase;

class TestCase extends TestBenchTestCase
{

    public function testExcelClass()
    {
        $excel = App::make('Bhargavaaaa\Excel\Excel');
        $this->assertInstanceOf('Bhargavaaaa\Excel\Excel', $excel);
    }


    protected function getPackageProviders($app)
    {
        return array('Bhargavaaaa\Excel\ExcelServiceProvider');
    }


    protected function getPackagePath()
    {
        return realpath(implode(DIRECTORY_SEPARATOR, array(
            __DIR__,
            '..',
            'src',
            'Bhargavaaaa',
            'Excel'
        )));
    }
}
