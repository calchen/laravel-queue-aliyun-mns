<?php

namespace Calchen\LaravelQueueAliyunMns;

use AliyunMNS\Client;
use AliyunMNS\Exception\MessageNotExistException;
use AliyunMNS\Model\QueueAttributes;
use AliyunMNS\Requests\SendMessageRequest;
use Calchen\LaravelQueueAliyunMns\Jobs\MnsJob;
use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Foundation\Application;
use Illuminate\Queue\Queue;
use Illuminate\Support\Str;

class MnsQueue extends Queue implements QueueContract
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $queueName;

    /**
     * 在 receiveMessage 时等待时长
     *
     * @link https://help.aliyun.com/document_detail/35136.html?spm=a2c4g.11186623.6.675.37e35c40Hzv2FW#h2-request
     *
     * @var int?
     */
    private $waitSeconds;

    /**
     * MnsQueue constructor.
     *
     * @param Client $client
     * @param string $queue
     * @param int    $waitSeconds
     */
    public function __construct(Client $client, string $queue, int $waitSeconds = null)
    {
        $this->client = $client;
        $this->queueName = $queue;
        $this->waitSeconds = $waitSeconds;
    }

    /**
     * Get the size of the queue.
     *
     * @param string|null $queue
     *
     * @return int
     */
    public function size($queue = null)
    {
        /** @var QueueAttributes $attributes */
        $attributes = $this->getQueue($queue)->getAttribute()->getQueueAttributes();

        return $attributes->getActiveMessages();
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string|object $job
     * @param mixed         $data
     * @param string|null   $queue
     *
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        return $this->pushRaw($this->getPayload($job, $data, $queue), $queue);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param string      $payload
     * @param string|null $queue
     * @param array       $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        return $this->getQueue($queue)
            ->sendMessage(new SendMessageRequest($payload))
            ->getMessageId();
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param DateTimeInterface|DateInterval|int $delay
     * @param string|object                       $job
     * @param mixed                               $data
     * @param string|null                         $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->getQueue($queue)
            ->sendMessage(new SendMessageRequest($this->getPayload($job, $data, $queue), $this->secondsUntil($delay)))
            ->getMessageId();
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     *
     * @return Job|null
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);
        try {
            $message = $queue->receiveMessage($this->waitSeconds);
        } catch (MessageNotExistException $e) {
            return;
        }

        return new MnsJob($this->container, $queue, $message, $this->connectionName);
    }

    /**
     * Returns a queue reference for operating on the queue.
     *
     * @param null $queueName
     *
     * @return \AliyunMNS\Queue
     */
    private function getQueue($queueName = null)
    {
        return $this->client->getQueueRef($queueName ?: $this->queueName);
    }

    /**
     * 5.5-5.6 与 5.7+ 的 createPayload 入参方法有所不同，这里兼容处理一下.
     *
     * @param        $job
     * @param string $data
     * @param null   $queue
     *
     * @return string
     */
    private function getPayload($job, $data = '', $queue = null): string
    {
        $version = Application::VERSION;

        return Str::start($version, '5.5') || Str::start($version, '5.6') ?
            $this->createPayload($job, $data) : $this->createPayload($job, $this->getQueue($queue), $data);
    }
}
