<?php declare(strict_types=1);

namespace SymfonyBundles\KafkaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder(KafkaExtension::EXTENSION_ALIAS);

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $builder->getRootNode();

        $this->addConfigurationNode($rootNode->children()->arrayNode('consumers')->addDefaultsIfNotSet());
        $this->addConfigurationNode($rootNode->children()->arrayNode('producers')->addDefaultsIfNotSet());

        return $builder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return NodeDefinition
     */
    private function addConfigurationNode(ArrayNodeDefinition $rootNode): NodeDefinition
    {
        return $rootNode->children()->arrayNode('configuration')->prototype('variable');
    }
}
