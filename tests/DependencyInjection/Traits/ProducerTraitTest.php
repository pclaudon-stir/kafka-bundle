<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Tests\DependencyInjection\Traits;

use SymfonyBundles\KafkaBundle\Tests\TestCase;
use SymfonyBundles\KafkaBundle\Producer\Producer;
use SymfonyBundles\KafkaBundle\Tests\Fixtures\DependencyInjection\Traits\ProducerFixture;

final class ProducerTraitTest extends TestCase
{
    public function testAutowiring()
    {
        $fixture = $this->container->get(ProducerFixture::class);

        $this->assertInstanceOf(Producer::class, $fixture->getProducer());
    }
}
