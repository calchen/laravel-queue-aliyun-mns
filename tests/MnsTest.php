<?php

namespace Calchen\LaravelQueueAliyunMns\Test;

use Calchen\LaravelQueueAliyunMns\Jobs\MnsJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;

class MnsTest extends TestCase
{
    public function test()
    {
        $r = Queue::push(new DemoJob());
        dump($r, Queue::size());
        $this->assertGreaterThan(0, Queue::size());

        /** @var MnsJob $job */
        $job = Queue::pop();
        $this->assertTrue($job instanceof MnsJob);
        $this->assertTrue($job->attempts() == 1);

        $job->delete();
        $this->assertTrue($job->isDeleted());
    }

    public function testDelay()
    {
        Queue::later(Carbon::now()->addSeconds(5), new DemoJob());
        $this->assertEquals(0, Queue::size());

        sleep(8);

        /** @var MnsJob $job */
        $job = Queue::pop();
        $this->assertTrue($job instanceof MnsJob);
        $this->assertTrue($job->attempts() == 1);

        $job->delete();
        $this->assertTrue($job->isDeleted());
    }
}
