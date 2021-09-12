<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use EveryWorkflow\MagentoConnectorBundle\Importer\CatalogProductAttributeImporter;
use EveryWorkflow\MagentoConnectorBundle\Importer\CatalogProductImporter;
use EveryWorkflow\MagentoConnectorBundle\Importer\CustomerAttributeImporter;
use EveryWorkflow\MagentoConnectorBundle\Importer\CustomerImporter;
use EveryWorkflow\MagentoConnectorBundle\Model\Client\MagentoRestClient;
use EveryWorkflow\MagentoConnectorBundle\Model\ConfigHelper;
use EveryWorkflow\MagentoConnectorBundle\Model\ConfigHelperInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\Formatter\MagentoJsonFormatter;
use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessor;
use EveryWorkflow\MagentoConnectorBundle\Model\MagentoSearchCriteria;
use EveryWorkflow\MagentoConnectorBundle\Model\MagentoService;
use Symfony\Component\DependencyInjection\Loader\Configurator\DefaultsConfigurator;

return function (ContainerConfigurator $configurator) {

    /** @var DefaultsConfigurator $services */
    $services = $configurator
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->load('EveryWorkflow\\MagentoConnectorBundle\\', '../../*')
        ->exclude('../../{DependencyInjection,Resources,Support,Tests}');

    $services->set(ConfigHelperInterface::class, ConfigHelper::class)
        ->arg('$configs', '%magento_connector%');

    $services->set(MagentoJsonFormatter::class)->share(false);

    $services->set(MagentoRestClient::class)
        ->arg('$formatter', service(MagentoJsonFormatter::class))
        ->arg('$config', ['connect_timeout' => 120.0]);
    $services->set(MagentoService::class)
        ->arg('$client', service(MagentoRestClient::class));

    $services->set(MagentoSearchCriteria::class)->share(false);

    $services->set(ImportProcessor::class)
        ->arg('$importers', tagged_iterator('everyworkflow.magento_connector.importer'));

    $services->set(CustomerAttributeImporter::class)
        ->tag('everyworkflow.magento_connector.importer', ['priority' => 10000]);

    $services->set(CustomerImporter::class)
        ->tag('everyworkflow.magento_connector.importer', ['priority' => 9090]);

    $services->set(CatalogProductAttributeImporter::class)
        ->tag('everyworkflow.magento_connector.importer', ['priority' => 9000]);

    $services->set(CatalogProductImporter::class)
        ->tag('everyworkflow.magento_connector.importer', ['priority' => 8090]);
};
