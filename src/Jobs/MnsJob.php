<?php

namespace Calchen\LaravelQueueAliyunMns\Jobs;

use AliyunMNS\Queue;
use AliyunMNS\Responses\ReceiveMessageResponse;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;

class MnsJob extends Job implements JobContract
{
    /**
     * @var Queue
     */
    protected $mns;

    /**
     * 阿里云 MNS 接受到的消息.
     *
     * @var ReceiveMessageResponse
     */
    protected $job;

    public function __construct(Container $container, Queue $mns, ReceiveMessageResponse $message, $connectionName)
    {
        $this->container = $container;
        $this->mns = $mns;
        $this->job = $message;
        $this->queue = $mns->getQueueName();
        $this->connectionName = $connectionName;
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 1)
    {
        parent::release($delay);

        $this->mns->changeMessageVisibility($this->job->getReceiptHandle(), $delay);
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->mns->deleteMessage($this->job->getReceiptHandle());
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->job->getMessageId();
    }

    /**
     * Get the raw body of the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->job->getMessageBody();
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return $this->job->getDequeueCount();
    }
}
