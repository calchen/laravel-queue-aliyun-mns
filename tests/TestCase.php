<?php

namespace Calchen\LaravelQueueAliyunMns\Test;

use Calchen\LaravelQueueAliyunMns\AliyunMnsServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [AliyunMnsServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('queue.connections.mns', [
            'driver' => 'mns',
            'access_key_id' => env('ALIYUN_ACCESS_KEY_ID'),
            'access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET'),
            'endpoint' => env('ALIYUN_MNS_ENDPOINT'),
            'queue' => env('ALIYUN_MNS_QUEUE'),
        ]);
    }
}