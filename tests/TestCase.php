<?php

namespace Calchen\LaravelQueueAliyunMns\Test;

use Calchen\LaravelQueueAliyunMns\AliyunMnsServiceProvider;
use Calchen\LaravelQueueAliyunMns\Jobs\MnsJob;
use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [AliyunMnsServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('queue.default', 'mns');
        $app['config']->set('queue.connections.mns', [
            'driver' => 'mns',
            'access_key_id' => env('ALIYUN_ACCESS_KEY_ID'),
            'access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET'),
            'endpoint' => env('ALIYUN_MNS_ENDPOINT'),
            'queue' => env('ALIYUN_MNS_QUEUE'),
        ]);
    }

    protected function getJob(): MnsJob
    {
        /** @var MnsJob $job */
        $job = Queue::pop();

        $times = 1;
        dump(dump($job));
        while (is_null($job)) {
            throw_if(
                $times >= 15,
                new \Exception('尝试获取 Job 次数过多')
            );

            sleep(5);
            $job = Queue::pop();
            dump('sleep');
            $times++;
        }

        return $job;
    }
}
