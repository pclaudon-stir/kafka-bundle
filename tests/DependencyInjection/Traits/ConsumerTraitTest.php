<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Tests\DependencyInjection\Traits;

use SymfonyBundles\KafkaBundle\Tests\TestCase;
use SymfonyBundles\KafkaBundle\Consumer\Consumer;
use SymfonyBundles\KafkaBundle\Tests\Fixtures\DependencyInjection\Traits\ConsumerFixture;

final class ConsumerTraitTest extends TestCase
{
    public function testAutowiring()
    {
        $fixture = $this->container->get(ConsumerFixture::class);

        $this->assertInstanceOf(Consumer::class, $fixture->getConsumer());
    }
}
