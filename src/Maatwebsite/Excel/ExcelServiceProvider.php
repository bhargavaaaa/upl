<?php namespace Bhargavaaaa\Excel;

use PHPExcel_Settings;
use PHPExcel_Shared_Font;
use Bhargavaaaa\Excel\Readers\Html;
use Bhargavaaaa\Excel\Classes\Cache;
use Illuminate\Support\Facades\Config;
use Bhargavaaaa\Excel\Classes\PHPExcel;
use Illuminate\Support\ServiceProvider;
use Bhargavaaaa\Excel\Parsers\CssParser;
use Bhargavaaaa\Excel\Parsers\ViewParser;
use Bhargavaaaa\Excel\Classes\FormatIdentifier;
use Bhargavaaaa\Excel\Readers\LaravelExcelReader;
use Bhargavaaaa\Excel\Writers\LaravelExcelWriter;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 *
 * LaravelExcel Excel ServiceProvider
 *
 * @category   Laravel Excel
 * @package    bhargavaaaa/excel
 * @copyright  Copyright (c) 2013 - 2014 Bhargavaaaa (http://www.bhargavaaaa.nl)
 * @author     Bhargavaaaa <info@bhargavaaaa.nl>
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class ExcelServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/excel.php' => config_path('excel.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/excel.php', 'excel'
        );

        //Set the autosizing settings
        $this->setAutoSizingSettings();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->bindClasses();
        $this->bindCssParser();
        $this->bindReaders();
        $this->bindParsers();
        $this->bindPHPExcelClass();
        $this->bindWriters();
        $this->bindExcel();
    }

    /**
     * Bind PHPExcel classes
     * @return void
     */
    protected function bindPHPExcelClass()
    {
        // Set object
        $me = $this;

        // Bind the PHPExcel class
        $this->app['phpexcel'] = $this->app->share(function () use ($me)
        {
            // Set locale
            $me->setLocale();

            // Set the caching settings
            $me->setCacheSettings();

            // Init phpExcel
            $excel = new PHPExcel();
            $excel->setDefaultProperties();
            return $excel;
        });
    }

    /**
     * Bind the css parser
     */
    protected function bindCssParser()
    {
        // Bind css parser
        $this->app['excel.parsers.css'] = $this->app->share(function ()
        {
            return new CssParser(
                new CssToInlineStyles()
            );
        });
    }

    /**
     * Bind writers
     * @return void
     */
    protected function bindReaders()
    {
        // Bind the laravel excel reader
        $this->app['excel.reader'] = $this->app->share(function ($app)
        {
            return new LaravelExcelReader(
                $app['files'],
                $app['excel.identifier']
            );
        });

        // Bind the html reader class
        $this->app['excel.readers.html'] = $this->app->share(function ($app)
        {
            return new Html(
                $app['excel.parsers.css']
            );
        });
    }

    /**
     * Bind writers
     * @return void
     */
    protected function bindParsers()
    {
        // Bind the view parser
        $this->app['excel.parsers.view'] = $this->app->share(function ($app)
        {
            return new ViewParser(
                $app['excel.readers.html']
            );
        });
    }

    /**
     * Bind writers
     * @return void
     */
    protected function bindWriters()
    {
        // Bind the excel writer
        $this->app['excel.writer'] = $this->app->share(function ($app)
        {
            return new LaravelExcelWriter(
                $app->make('Response'),
                $app['files'],
                $app['excel.identifier']
            );
        });
    }

    /**
     * Bind Excel class
     * @return void
     */
    protected function bindExcel()
    {
        // Bind the Excel class and inject its dependencies
        $this->app['excel'] = $this->app->share(function ($app)
        {
            $excel = new Excel(
                $app['phpexcel'],
                $app['excel.reader'],
                $app['excel.writer'],
                $app['excel.parsers.view']
            );

            $excel->registerFilters($app['config']->get('excel.filters', array()));

            return $excel;
        });
    }

    /**
     * Bind other classes
     * @return void
     */
    protected function bindClasses()
    {
        // Bind the format identifier
        $this->app['excel.identifier'] = $this->app->share(function ($app)
        {
            return new FormatIdentifier($app['files']);
        });
    }

    /**
     * Set cache settings
     * @return Cache
     */
    public function setCacheSettings()
    {
        return new Cache();
    }

    /**
     * Set locale
     */
    public function setLocale()
    {
        $locale = Config::get('app.locale', 'en_us');
        PHPExcel_Settings::setLocale($locale);
    }

    /**
     * Set the autosizing settings
     */
    public function setAutoSizingSettings()
    {
        $method = Config::get('excel.export.autosize-method', PHPExcel_Shared_Font::AUTOSIZE_METHOD_APPROX);
        PHPExcel_Shared_Font::setAutoSizeMethod($method);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'excel',
            'phpexcel',
            'excel.reader',
            'excel.readers.html',
            'excel.parsers.view',
            'excel.writer'
        );
    }
}
