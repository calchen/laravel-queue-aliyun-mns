<?php

namespace Calchen\LaravelQueueAliyunMns\Connectors;

use AliyunMNS\Client;
use Calchen\LaravelQueueAliyunMns\MnsQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Support\Arr;

class MnsConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $client = new Client($config['endpoint'], $config['access_key_id'], $config['access_key_secret']);

        return new MnsQueue($client, $config['queue'], Arr::get($config, 'wait_seconds'));
    }
}
