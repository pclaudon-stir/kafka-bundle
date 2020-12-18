<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\DependencyInjection\Traits;

use SymfonyBundles\KafkaBundle\Producer\Producer;

trait ProducerTrait
{
    protected Producer $producer;

    /**
     * @required
     *
     * @param Producer $producer
     */
    public function setProducer(Producer $producer): void
    {
        $this->producer = $producer;
    }
}
