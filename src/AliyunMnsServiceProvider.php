<?php

namespace Calchen\LaravelQueueAliyunMns;

use Calchen\LaravelQueueAliyunMns\Connectors\MnsConnector;
use Illuminate\Support\ServiceProvider;

class AliyunMnsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMnsConnector($this->app['queue']);
    }

    /**
     * Register the Redis queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerMnsConnector($manager)
    {
        $manager->addConnector('mns', function () {
            return new MnsConnector();
        });
    }
}
