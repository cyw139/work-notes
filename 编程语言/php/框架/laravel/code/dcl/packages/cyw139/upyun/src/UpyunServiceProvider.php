<?php


namespace Cyw139\Upyun;

use Cyw139\Upyun\Plugin\AudioOrVideoMeta;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class UpyunServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('upyun', function ($app, $config) {
            $adapter = new UpyunAdapter(
                $config['serviceName'],
                $config['operator'],
                $config['password'],
                $config['domain'],
                $config['protocol']
            );
            $filesystem = new Filesystem($adapter);
            $filesystem->addPlugin(new AudioOrVideoMeta());

            return $filesystem;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
