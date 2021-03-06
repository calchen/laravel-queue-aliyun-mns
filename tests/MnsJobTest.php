<?php

namespace Calchen\LaravelQueueAliyunMns\Test;

use Calchen\LaravelQueueAliyunMns\Jobs\MnsJob;
use Illuminate\Support\Facades\Queue;

class MnsJobTest extends TestCase
{
    /**
     * 测试 MnsJob 的 release 方法.
     */
    public function testRelease()
    {
        Queue::push(new DemoJob());
        /** @var MnsJob $job */
        $job = Queue::pop();
        $job->release();
        $this->assertTrue($job->isReleased());
        sleep(5);
        $job = Queue::pop();
        $job->delete();
    }

    /**
     * 测试 MnsJob 的 delete 方法.
     */
    public function testDelete()
    {
        Queue::push(new DemoJob());
        /** @var MnsJob $job */
        $job = Queue::pop();
        $job->delete();
        $this->assertTrue($job->isDeleted());
    }

    /**
     * 测试 MnsJob 的 getJobId 方法.
     */
    public function testGetJobId()
    {
        Queue::push(new DemoJob());
        /** @var MnsJob $job */
        $job = Queue::pop();
        $this->assertStringMatchesFormat('%s', $job->getJobId());
        $job->delete();
    }

    /**
     * 测试 MnsJob 的 getRawBody 方法.
     */
    public function testGetRawBody()
    {
        Queue::push(new DemoJob());
        /** @var MnsJob $job */
        $job = Queue::pop();
        $this->assertStringMatchesFormat('%s', $job->getRawBody());
        $job->delete();
    }

    /**
     * 测试 MnsJob 的 attempts 方法.
     */
    public function testAttempts()
    {
        Queue::push(new DemoJob());
        /** @var MnsJob $job */
        $job = Queue::pop();
        $this->assertGreaterThanOrEqual(1, $job->attempts());
        $job->delete();
    }
}
