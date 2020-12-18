<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\DependencyInjection\Traits;

use SymfonyBundles\KafkaBundle\Consumer\Consumer;

trait ConsumerTrait
{
    protected Consumer $consumer;

    /**
     * @required
     *
     * @param Consumer $consumer
     */
    public function setConsumer(Consumer $consumer): void
    {
        $this->consumer = $consumer;
    }
}
