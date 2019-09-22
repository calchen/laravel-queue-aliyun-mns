<?php

namespace Calchen\LaravelQueueAliyunMns\Test;

use Calchen\LaravelQueueAliyunMns\Jobs\MnsJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;

class MnsQueueTest extends TestCase
{
    /**
     * 测试 MnsQueue 的 size 方法.
     */
    public function testSize()
    {
        $this->assertGreaterThanOrEqual(0, Queue::size());
    }

    /**
     * 测试 MnsQueue 的 push 方法.
     */
    public function testPush()
    {
        $messageId = Queue::push(new DemoJob());
        $this->assertStringMatchesFormat('%s', $messageId);
        Queue::pop()->delete();
    }

    /**
     * 测试 MnsQueue 的 later 方法.
     */
    public function testLater()
    {
        Queue::later(Carbon::now()->addSeconds(5), new DemoJob());
        sleep(8);
        /** @var MnsJob $job */
        $job = Queue::pop();
        $this->assertTrue($job instanceof MnsJob);
        $job->delete();
    }

    /**
     * 测试 MnsQueue 的 pop 方法.
     */
    public function testPop()
    {
        Queue::push(new DemoJob());
        /** @var MnsJob $job */
        $job = Queue::pop();
        $this->assertTrue($job instanceof MnsJob);
        $job->delete();
    }
}
