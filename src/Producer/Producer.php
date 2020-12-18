<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Producer;

final class Producer
{
    public const DEFAULT_TIMEOUT = 1000;
    public const FLUSH_RETRY_COUNT = 10;
    public const FLUSH_ERROR_MESSAGE = 'Kafka Producer: Was unable to flush, messages might be lost!';

    private Configuration $configuration;

    /**
     * @var \RdKafka\ProducerTopic[]
     */
    private array $topics = [];

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string       $name
     * @param array<mixed> $data
     * @param int          $timeout
     *
     * @throws \RuntimeException
     */
    public function send(string $name, array $data, int $timeout = self::DEFAULT_TIMEOUT): void
    {
        $this->getTopic($name)->produce(\RD_KAFKA_PARTITION_UA, 0, \json_encode($data));

        for ($i = 0; $i < self::FLUSH_RETRY_COUNT; ++$i) {
            if (\RD_KAFKA_RESP_ERR_NO_ERROR === $this->getClient()->flush($timeout)) {
                return;
            }
        }

        throw new \RuntimeException(self::FLUSH_ERROR_MESSAGE);
    }

    /**
     * @return \RdKafka\Producer
     */
    private function getClient(): \RdKafka\Producer
    {
        static $client = null;

        if (null === $client) {
            $client = new \RdKafka\Producer($this->configuration);
        }

        return $client;
    }

    /**
     * @param string $name
     *
     * @return \RdKafka\ProducerTopic
     */
    private function getTopic(string $name): \RdKafka\ProducerTopic
    {
        if (false === isset($this->topics[$name])) {
            $this->topics[$name] = $this->getClient()->newTopic($name);
        }

        return $this->topics[$name];
    }
}
