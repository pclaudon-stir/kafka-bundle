<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\DependencyInjection\Traits;

use Psr\Log\LoggerInterface;

trait LoggerTrait
{
    protected LoggerInterface $logger;

    /**
     * @required
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
