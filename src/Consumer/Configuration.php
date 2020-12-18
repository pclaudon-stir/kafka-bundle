<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Consumer;

class Configuration extends \RdKafka\Conf
{
    /**
     * @param array<mixed> $configs
     */
    public function __construct(array $configs)
    {
        parent::__construct();

        foreach ($configs as $name => $value) {
            $this->set($name, (string) $value);
        }
    }
}
