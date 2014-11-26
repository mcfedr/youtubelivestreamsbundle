<?php

namespace Mcfedr\YouTube\LiveStreamsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class McfedrYouTubeLiveStreamsExtension extends Extension
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

        $container->setParameter('mcfedr_you_tube_live_streams.api_key', $config['api_key']);
        if (isset($config['channel_id'])) {
            $container->setParameter('mcfedr_you_tube_live_streams.channel_id', $config['channel_id']);
        }

        if (isset($config['cache'])) {
            $cacheName = $config['cache'];
        } else {
            $cacheName = 'mcfedr_you_tube_live_streams.cache';
            $container->setDefinition(
                $cacheName,
                new Definition('Doctrine\Common\Cache\FilesystemCache', [
                    "%kernel.cache_dir%/mcfedr_you_tube_live_streams"
                ])
            );
        }

        $container->setDefinition(
            'mcfedr_you_tube_live_streams.loader',
            new Definition('Mcfedr\YouTube\LiveStreamsBundle\Streams\YouTubeStreamsLoader', [
                new Reference('mcfedr_you_tube_live_streams.youtube_client'),
                $container->getParameter('mcfedr_you_tube_live_streams.channel_id'),
                new Reference($cacheName),
                isset($config['cache_timeout']) ? $config['cache_timeout'] : 3600
            ])
        );
    }
}
