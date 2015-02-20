<?php namespace BComp\Log;

use Slim\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * LogWriter
 *
 * @property  mixed  options
 * @property  mixed  app
 * @category  Log
 * @package   Bono
 * @author    Krisan Alfa Timur <krisan47@gmail.com>
 * @copyright 2015 PT Sagara Xinix Solusitama
 */
class LogWriter
{
    /**
     * @var Monolog\Logger
     */
    protected $resource;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Converts Slim log level to Monolog log level
     *
     * @var array
     */
    protected $logLevel = array(
        Log::EMERGENCY => Logger::EMERGENCY,
        Log::ALERT     => Logger::ALERT,
        Log::CRITICAL  => Logger::CRITICAL,
        Log::ERROR     => Logger::ERROR,
        Log::WARN      => Logger::WARNING,
        Log::NOTICE    => Logger::NOTICE,
        Log::INFO      => Logger::INFO,
        Log::DEBUG     => Logger::DEBUG,
    );

    /**
     * Constructor
     *
     * Prepare this log writer. Available settings are:
     *
     * @param array $settings
     *
     * @return void
     */
    public function __construct($settings = array())
    {
        $this->settings = $settings;
    }

    /**
     * Write to log
     *
     * @param string $message
     * @param int    $level
     * @param array  $context
     *
     * @return void
     */
    public function write($level, $message, array $context = array())
    {
        if (! $this->resource) {
            // create a log channel
            $this->makeResource();
        }

        // Don't bother typesetting $message, Monolog will do this for us
        $this->resource->addRecord($this->getLogLevel($level, Logger::WARNING), $message, $context);
    }

    /**
     * Make resource to write log
     *
     * @return void
     */
    protected function makeResource()
    {
        $this->resource = new Logger($this->settings['name']);

        foreach ($this->settings['handlers'] as $handler) {
            $this->resource->pushHandler($handler);
        }

        foreach ($this->settings['processors'] as $processor) {
            $this->resource->pushProcessor($processor);
        }
    }

    /**
     * Converts Slim log level to Monolog log level
     *
     * @param int $slimLogLevel        Slim log level we're converting from
     * @param int $defaultMonologLevel Monolog log level to use if $slimLogLevel not found
     *
     * @return int Monolog log level
     */
    protected function getLogLevel($slimLogLevel, $defaultMonologLevel)
    {
        return isset($this->logLevel[$slimLogLevel]) ? $this->logLevel[$slimLogLevel] : $defaultMonologLevel;
    }

    /**
     * Get the resource we used to write the log
     *
     * @return Monolog\Logger
     */
    public function getResource()
    {
        if (! $this->resource) {
            // create a log channel
            $this->makeResource();
        }

        return $this->resource;
    }
}
