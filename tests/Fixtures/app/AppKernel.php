<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\Tests\Fixtures\app;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Loader\LoaderInterface;

final class AppKernel extends Kernel
{
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);

        (new Filesystem())->remove($this->getCacheDir());
    }

    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \SymfonyBundles\KafkaBundle\SymfonyBundlesKafkaBundle(),
        ];
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return '/tmp/symfony-cache';
    }

    public function getLogDir()
    {
        return '/tmp/symfony-cache';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config_test.yml');
        $loader->load($this->getRootDir() . '/config/sb_kafka.yaml');
    }
}
