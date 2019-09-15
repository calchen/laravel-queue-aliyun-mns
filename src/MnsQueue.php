<?php

namespace Calchen\LaravelQueueAliyunMns;

use AliyunMNS\Client;
use Illuminate\Queue\Queue;
use AliyunMNS\Model\QueueAttributes;
use AliyunMNS\Requests\SendMessageRequest;
use Calchen\LaravelQueueAliyunMns\Jobs\MnsJob;
use Illuminate\Contracts\Queue\Queue as QueueContract;

class MnsQueue extends Queue implements QueueContract
{
    /**
     * @var Client
     */
    private $client;

    private $queueName;

    /**
     * MnsQueue constructor.
     *
     * @param Client $client
     * @param string $queue
     */
    public function __construct(Client $client, string $queue)
    {
        $this->client = $client;
        $this->queueName = $queue;
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
     * @param mixed $data
     * @param string|null $queue
     *
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        return $this->pushRaw($this->createPayload($job, $this->getQueue($queue), $data), $queue);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param string $payload
     * @param string|null $queue
     * @param array $options
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
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string|object $job
     * @param mixed $data
     * @param string|null $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $payload = $this->createPayload($job, $this->getQueue($queue), $data);

        return $this->getQueue($queue)
            ->sendMessage(new SendMessageRequest($payload, $this->secondsUntil($delay)))
            ->getMessageId();
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     *
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);
        $message = $queue->receiveMessage();

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
}
