<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SymfonyBundlesKafkaBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): DependencyInjection\KafkaExtension
    {
        return new DependencyInjection\KafkaExtension();
    }
}
