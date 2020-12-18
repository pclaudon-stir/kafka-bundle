<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Tests\Fixtures\DependencyInjection\Traits;

use Psr\Log\LoggerInterface;
use SymfonyBundles\KafkaBundle\DependencyInjection\Traits\LoggerTrait;

final class LoggerFixture
{
    use LoggerTrait;

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
