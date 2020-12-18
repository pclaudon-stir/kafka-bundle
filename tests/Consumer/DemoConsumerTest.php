<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Tests\Consumer;

use Symfony\Component\Console;
use SymfonyBundles\KafkaBundle\Tests\TestCase;
use SymfonyBundles\KafkaBundle\Producer\Producer;
use SymfonyBundles\KafkaBundle\Tests\Fixtures\Consumer\DemoConsumer;

final class DemoConsumerTest extends TestCase
{
    private Console\Tester\CommandTester $tester;

    protected function setUp(): void
    {
        parent::setUp();

        $consumer = $this->getDemoConsumer();
        $application = new Console\Application();
        $application->add($consumer);

        $this->tester = new Console\Tester\CommandTester($application->find($consumer->getName()));
    }

    public function testConsumerName(): void
    {
        $this->assertSame(
            'symfony-bundles:kafka-bundle:tests:fixtures:consumer:demo',
            $this->getDemoConsumer()->getName()
        );
    }

    public function testProduceConsume(): void
    {
        foreach ($this->getMessages() as $message) {
            $this->container->get(Producer::class)->send(DemoConsumer::QUEUE_NAME, $message);
        }

        $this->executeConsumer();

        $this->assertSame($this->getExpectedLoggerMessages(), $this->container->get('logger')->messages);
    }

    public function testProduceFlushException(): void
    {
        $this->expectExceptionMessage(Producer::FLUSH_ERROR_MESSAGE);

        $this->container->get(Producer::class)->send(__FUNCTION__, [], 0);
    }

    private function getMessages(): array
    {
        return [
            ['payload' => 'no-error', 'code' => \RD_KAFKA_RESP_ERR_NO_ERROR],
            ['exception' => true, 'code' => \RD_KAFKA_RESP_ERR_NO_ERROR],
            ['payload' => 'bad-compression', 'code' => \RD_KAFKA_RESP_ERR__BAD_COMPRESSION],
            ['payload' => 'timed-out', 'code' => \RD_KAFKA_RESP_ERR__TIMED_OUT],
            ['payload' => 'partition-eof', 'code' => \RD_KAFKA_RESP_ERR__PARTITION_EOF],
            ['payload' => 'not-coordinator-for-group', 'code' => \RD_KAFKA_RESP_ERR_NOT_COORDINATOR_FOR_GROUP],
        ];
    }

    private function getExpectedLoggerMessages(): array
    {
        return [
            'debug' => [
                [
                    'message' => 'success',
                    'context' => [],
                ],
                [
                    'message' => 'Local: Timed out',
                    'context' => ['code' => \RD_KAFKA_RESP_ERR__TIMED_OUT],
                ],
                [
                    'message' => 'Broker: No more messages',
                    'context' => ['code' => \RD_KAFKA_RESP_ERR__PARTITION_EOF],
                ],
            ],
            'error' => [
                [
                    'message' => 'Test exception',
                    'context' => ['payload' => '{"exception":true,"code":0}'],
                ],
                [
                    'message' => 'Local: Invalid compressed data',
                    'context' => [
                        'code' => \RD_KAFKA_RESP_ERR__BAD_COMPRESSION,
                        'payload' => '{"payload":"bad-compression","code":-198}',
                    ],
                ],
                [
                    'message' => 'Broker: Not coordinator for group',
                    'context' => [
                        'code' => \RD_KAFKA_RESP_ERR_NOT_COORDINATOR_FOR_GROUP,
                        'payload' => '{"payload":"not-coordinator-for-group","code":16}',
                    ],
                ],
            ],
            'info' => [
                [
                    'message' => 'Process termination',
                    'context' => [
                        'signal' => \RD_KAFKA_RESP_ERR_NOT_COORDINATOR_FOR_GROUP,
                    ],
                ],
            ],
        ];
    }

    private function getDemoConsumer(): DemoConsumer
    {
        return $this->container->get(DemoConsumer::class);
    }

    private function executeConsumer(): void
    {
        $this->tester->execute([]);
    }
}
