<?php namespace BComp\Provider;

use Bono\App;
use Bono\Provider\Provider;

/**
 * VersionProvider
 *
 * @property  mixed  options
 * @property  mixed  app
 * @category  Provider
 * @package   Bono
 * @author    Krisan Alfa Timur <krisan47@gmail.com>
 * @copyright 2013 PT Sagara Xinix Solusitama
 */
class VersionProvider extends Provider
{
    /**
     * The environment being used for application
     *
     * @var string
     */
    protected $env = null;

    /**
     * The mode being used for application
     *
     * @var string
     */
    protected $mode = null;

    /**
     * The hostname that run this application
     *
     * @var string
     */
    protected $hostname = null;

    /**
     * Bono Application
     *
     * @var Bono\App
     */
    protected $app = null;

    /**
     * Configuration path
     *
     * @var string
     */
    protected $configPath = null;

    /**
     * Initialize the provider
     *
     * @return void
     */
    public function initialize()
    {
        // Get current machine hostname
        $this->hostname = $hostname = gethostname();

        // Bind the Application instance to our class property
        $this->app = $app = App::getInstance();

        // Bind the configuration path to our class property
        $this->configPath = realpath($app->config('config.path'));

        // Get the current mode of application, and bind it to our class property
        $this->mode = $app->config('mode');

        // Override mode configuration
        $this->mergeModeConfig();

        // Determine if there's array contain local machine name in 'local' array key of the options given
        if (isset($this->options['local'])) {
            foreach ($this->options['local'] as $host) {
                if ($host === $hostname) {
                    $this->env = 'local';
                    $this->mergeEnvConfig();

                    break;
                }
            }
        }

        // Determine if there's array contain remote machine name in 'remote' array key of the options given
        if (is_null($this->env) and isset($this->options['remote'])) {
            foreach ($this->options['remote'] as $host) {
                if ($host === $hostname) {
                    $this->env = 'remote';
                    $this->mergeEnvConfig();

                    break;
                }
            }
        }

        // Let's merge our own unique configuration based on our hostname
        $this->mergeHostConfig();
    }

    /**
     * Merge configuration from application and given configuration file path
     *
     * @param string $path Path of configuration file
     *
     * @return void
     */
    protected function mergeConfig($path)
    {
        // Only do if configuration file is readable
        if (is_readable($path)) {
            $config = require_once $path;

            // Only do if configuration file return an array
            if (is_array($config)) {
                foreach ($config as $key => $value) {
                    // Only do if value is not empty and not null
                    if ($value) {
                        $this->app->config($key, $value);
                    }
                }
            }
        }
    }

    /**
     * Merge configuration between now and the current env
     *
     * @return void
     */
    protected function mergeEnvConfig()
    {
        $this->mergeConfig($this->configPath . DIRECTORY_SEPARATOR . 'env' . DIRECTORY_SEPARATOR . $this->env . '.php');
    }

    /**
     * Merge configuration between now and the current mode
     *
     * @return void
     */
    protected function mergeModeConfig()
    {
        $this->mergeConfig($this->configPath . DIRECTORY_SEPARATOR . 'mode' . DIRECTORY_SEPARATOR . $this->mode . '.php');
    }

    /**
     * Merge configuration between now and the current host
     *
     * @return void
     */
    protected function mergeHostConfig()
    {
        $this->mergeConfig($this->configPath . DIRECTORY_SEPARATOR . 'host' . DIRECTORY_SEPARATOR . $this->hostname . '.php');
    }
}
