<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Consumer;

final class Consumer extends \RdKafka\KafkaConsumer
{
    public const DEFAULT_TIMEOUT = 10000;

    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);
    }

    /**
     * @param int $timeout
     *
     * @return \RdKafka\Message
     *
     * @throws \RdKafka\Exception
     */
    public function consume($timeout = self::DEFAULT_TIMEOUT): \RdKafka\Message
    {
        return parent::consume($timeout);
    }
}
