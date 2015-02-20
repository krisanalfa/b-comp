<?php namespace BComp\Provider;

use Bono\App;
use Bono\Provider\Provider;
use BComp\Log\LogWriter;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\WebProcessor;
use BComp\Log\Log;

/**
 * LogProvider
 *
 * @property  array    options
 * @property  Bono\App app
 * @category  Provider
 * @package   Bono
 * @author    Krisan Alfa Timur <krisan47@gmail.com>
 * @copyright 2015 PT Sagara Xinix Solusitama
 */
class LogProvider extends Provider
{
    /**
     * Default configuration
     *
     * @var array
     */
    protected $defaultConfig = array(
        // Your log name
        'log.name'         => 'APP LOGGER',

        // Path where log will be written
        'log.path'         => 'logs',

        // The file format of logfile
        'log.dateformat'   => 'Y-m-d H:i:s',

        // Date format
        'log.fileformat'   => 'Y-m-d',

        // Output format
        'log.outputformat' => "[%datetime%] - [%level_name% ON %channel%] - [%message%]
    [MESSAGE CONTEXT]        %context%
    [ADDITIONAL INFORMATION] %extra%\n",

        // The used timezone for your logfile timestamp
        'log.timezone'     => 'Asia/Jakarta'
    );

    /**
     * Initialize the provider
     *
     * @return void
     */
    public function initialize()
    {
        $this->options = array_merge($this->defaultConfig, $this->options);

        date_default_timezone_set($this->options['log.timezone']);

        // Finally, create a formatter
        $formatter = new LineFormatter($this->options['log.outputformat'], $this->options['log.dateformat'], false);

        // Create a new directory
        $logPath = realpath($this->app->config('bono.base.path')).'/'.$this->options['log.path'];

        if (! is_dir($logPath)) {
            mkdir($logPath, 0755);
        }

        // Create a handler
        $stream = new StreamHandler($logPath.'/'.date($this->options['log.fileformat']).'.log');

        // Set our formatter
        $stream->setFormatter($formatter);

        // Create LogWriter
        $logger = new LogWriter(array(
            'name' => $this->options['log.name'],
            'handlers' => array(
                $stream,
            ),
            'processors' => array(
                new WebProcessor(),
            ),
        ));

        // Bind our logger to Bono Container
        $this->app->container->singleton('log', function ($c) {
            $log = new Log($c['logWriter']);

            $log->setEnabled($c['settings']['log.enabled']);
            $log->setLevel($c['settings']['log.level']);

            $env = $c['environment'];
            $env['slim.log'] = $log;

            return $log;
        });

        // Set the writer
        $this->app->config('log.writer', $logger);
    }
}
