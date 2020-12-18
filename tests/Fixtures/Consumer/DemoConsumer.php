<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Tests\Fixtures\Consumer;

use RdKafka\Message;
use SymfonyBundles\KafkaBundle\Command\Consumer;

final class DemoConsumer extends Consumer
{
    public const QUEUE_NAME = 'test_demo';

    protected function handle(Message $message): void
    {
        $message->err = $this->getPayload($message)['code'];

        parent::handle($message);
    }

    protected function onMessage(array $data): void
    {
        if (isset($data['exception'])) {
            throw new \Exception('Test exception');
        }

        $this->logger->debug('success');
    }
}
