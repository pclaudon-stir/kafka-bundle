<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Tests\Fixtures\DependencyInjection\Traits;

use SymfonyBundles\KafkaBundle\Consumer\Consumer;
use SymfonyBundles\KafkaBundle\DependencyInjection\Traits\ConsumerTrait;

final class ConsumerFixture
{
    use ConsumerTrait;

    public function getConsumer(): Consumer
    {
        return $this->consumer;
    }
}
