<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Tests\DependencyInjection\Traits;

use Psr\Log\LoggerInterface;
use SymfonyBundles\KafkaBundle\Tests\TestCase;
use SymfonyBundles\KafkaBundle\Tests\Fixtures\DependencyInjection\Traits\LoggerFixture;

final class LoggerTraitTest extends TestCase
{
    public function testAutowiring()
    {
        $fixture = $this->container->get(LoggerFixture::class);

        $this->assertInstanceOf(LoggerInterface::class, $fixture->getLogger());
    }
}
