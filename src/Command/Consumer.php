<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Command;

use Exception;
use JsonException;
use RdKafka\Message;
use Symfony\Component\Console;
use SymfonyBundles\KafkaBundle\DependencyInjection\Traits\{LoggerTrait, ConsumerTrait};

abstract class Consumer extends Console\Command\Command
{
    use ConsumerTrait, LoggerTrait;

    public const QUEUE_NAME = 'topic_name';
    public const TIMEOUT_OPTION = 'timeout';

    private bool $isAlive = true;

    /**
     * {@inheritdoc}
     */
    final public function __construct()
    {
        $name = (string) \preg_replace('#consumer$#i', '', static::class);
        $name = \mb_strtolower((string) \preg_replace('#([a-z])([A-Z])#', '$1-$2', \strtr($name, ['\\' => ':'])));

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    final protected function configure(): void
    {
        $this->addOption(
            static::TIMEOUT_OPTION,
            null,
            Console\Input\InputOption::VALUE_OPTIONAL,
            'Consuming timeout',
            \SymfonyBundles\KafkaBundle\Consumer\Consumer::DEFAULT_TIMEOUT
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RdKafka\Exception
     */
    final public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output): int
    {
        $this->addSignalHandler();

        $this->consumer->subscribe([static::QUEUE_NAME]);

        $timeout = $this->getTimeout($input);

        while ($this->isAlive) {
            $this->handle($this->consumer->consume($timeout));
        }

        return 0;
    }

    /**
     * @param Message $message
     *
     * @throws \RdKafka\Exception
     */
    protected function handle(Message $message): void
    {
        switch ($message->err) {
            case \RD_KAFKA_RESP_ERR_NO_ERROR:
                try {
                    $this->onMessage($this->getPayload($message));
                } catch (Exception $exception) {
                    $this->onException($message, $exception);
                }
                break;
            case \RD_KAFKA_RESP_ERR__PARTITION_EOF:
                $this->onEnd($message);
                break;
            case \RD_KAFKA_RESP_ERR__TIMED_OUT:
                $this->onTimeout($message);
                break;
            case \RD_KAFKA_RESP_ERR_NOT_COORDINATOR_FOR_GROUP:
                $this->onError($message);
                $this->stop(\RD_KAFKA_RESP_ERR_NOT_COORDINATOR_FOR_GROUP);
                break;
            default:
                $this->onError($message);
                break;
        }
    }

    /**
     * @param array<mixed> $data
     *
     * @throws \Throwable
     */
    abstract protected function onMessage(array $data): void;

    /**
     * @param Message   $message
     * @param Exception $exception
     */
    protected function onException(Message $message, Exception $exception): void
    {
        $this->logger->error($exception->getMessage(), ['payload' => $message->payload]);
    }

    /**
     * @param Message $message
     */
    protected function onEnd(Message $message): void
    {
        $this->logger->debug($message->errstr(), ['code' => $message->err]);
    }

    /**
     * @param Message $message
     */
    protected function onTimeout(Message $message): void
    {
        $this->logger->debug($message->errstr(), ['code' => $message->err]);
    }

    /**
     * @param Message $message
     */
    protected function onError(Message $message): void
    {
        $this->logger->error($message->errstr(), ['code' => $message->err, 'payload' => $message->payload]);
    }

    /**
     * @param Message $message
     *
     * @return array<mixed>
     *
     * @throws Exception|JsonException
     */
    protected function getPayload(Message $message): array
    {
        return \json_decode($message->payload, true, 512, \JSON_THROW_ON_ERROR);
    }

    /**
     * @param int $signal
     *
     * @throws \RdKafka\Exception
     */
    final protected function stop(int $signal = 0): void
    {
        $this->isAlive = false;

        $this->consumer->unsubscribe();

        $this->logger->info('Process termination', ['signal' => $signal]);
    }

    /**
     * @param Console\Input\InputInterface $input
     *
     * @return int
     */
    final private function getTimeout(Console\Input\InputInterface $input): int
    {
        /** @var string $timeout */
        $timeout = $input->getOption(static::TIMEOUT_OPTION);

        return (int) $timeout;
    }

    /**
     * Consumer stopping handler.
     */
    final private function addSignalHandler(): void
    {
        $handler = function (int $signal) {
            $this->stop($signal);
        };

        \pcntl_async_signals(true);
        \pcntl_signal(\SIGINT, $handler);
        \pcntl_signal(\SIGTERM, $handler);
    }
}
