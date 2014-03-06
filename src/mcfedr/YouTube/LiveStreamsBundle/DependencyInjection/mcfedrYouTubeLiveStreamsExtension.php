<?php

namespace mcfedr\YouTube\LiveStreamsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class mcfedrYouTubeLiveStreamsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('mcfedr_you_tube_live_streams.channel_id', $config['channel_id']);
        $container->setParameter('mcfedr_you_tube_live_streams.api_key', $config['api_key']);
        if (isset($config['cache_timeout'])) {
            $container->setParameter('mcfedr_you_tube_live_streams.cache_timeout', $config['cache_timeout']);
        }
    }
}