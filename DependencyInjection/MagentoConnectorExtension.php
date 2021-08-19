<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\DependencyInjection;

use EveryWorkflow\MagentoConnectorBundle\Model\ImporterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MagentoConnectorExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

//        $container->registerForAutoconfiguration(ImporterInterface::class)
//            ->addTag('everyworkflow.magento_connector.importer');
    }

    /**
     * @return void
     */
    public function prepend(ContainerBuilder $container)
    {
        $ymlLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $ymlLoader->load('magento_connector.yaml');

        $configs = $container->getExtensionConfig($this->getAlias());
        asort($configs); // Reverse priority -> bundle config then project config
        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter('magento_connector', $config);
    }
}
