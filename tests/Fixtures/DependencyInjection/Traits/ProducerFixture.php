<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Tests\Fixtures\DependencyInjection\Traits;

use SymfonyBundles\KafkaBundle\Producer\Producer;
use SymfonyBundles\KafkaBundle\DependencyInjection\Traits\ProducerTrait;

final class ProducerFixture
{
    use ProducerTrait;

    public function getProducer(): Producer
    {
        return $this->producer;
    }
}
