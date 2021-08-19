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

    $services->set(MagentoRestClient::class)
        ->arg('$formatter', service(MagentoJsonFormatter::class))
        ->arg('$config', ['connect_timeout' => 120.0]);
    $services->set(MagentoService::class)
        ->arg('$client', service(MagentoRestClient::class));

    $services->set(MagentoSearchCriteria::class)->share(false);

    $services->set(ImportProcessor::class)
        ->arg('$importers', tagged_iterator('everyworkflow.magento_connector.importer'));

    /*
     * For customer attribute importer
     * */
    $services->set('ew_magento_customer_attribute_service', service(MagentoService::class))
        ->arg('$client', service(MagentoRestClient::class))
        ->arg('$request', service(\EveryWorkflow\MagentoConnectorBundle\Remote\Customer\AttributeRequest::class))
        ->arg(
            '$responseHandler',
            service(\EveryWorkflow\MagentoConnectorBundle\Remote\Customer\AttributeResponse::class)
        );
    $services->set(CustomerAttributeImporter::class)
        ->arg('$magentoService', service('ew_magento_customer_attribute_service'))
        ->tag('everyworkflow.magento_connector.importer', ['priority' => 10000]);

    /*
     * For customer importer
     * */
    $services->set('ew_magento_customer_search_service', service(MagentoService::class))
        ->arg('$client', service(MagentoRestClient::class))
        ->arg('$request', service(\EveryWorkflow\MagentoConnectorBundle\Remote\Customer\SearchRequest::class))
        ->arg(
            '$responseHandler',
            service(\EveryWorkflow\MagentoConnectorBundle\Remote\Customer\SearchResponse::class)
        );
    $services->set(CustomerImporter::class)
        ->arg('$magentoService', service('ew_magento_customer_search_service'))
        ->tag('everyworkflow.magento_connector.importer', ['priority' => 9090]);

    /*
     * For catalog product attribute importer
     * */
    $services->set('ew_magento_catalog_product_attribute_service', service(MagentoService::class))
        ->arg('$client', service(MagentoRestClient::class))
        ->arg(
            '$request',
            service(\EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\AttributeSearchRequest::class)
        )
        ->arg(
            '$responseHandler',
            service(\EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\AttributeSearchResponse::class)
        );
    $services->set(CatalogProductAttributeImporter::class)
        ->arg('$magentoService', service('ew_magento_catalog_product_attribute_service'))
        ->tag('everyworkflow.magento_connector.importer', ['priority' => 9000]);

    /*
     * For catalog product importer
     * */
    $services->set('ew_magento_catalog_product_search_service', service(MagentoService::class))
        ->arg('$client', service(MagentoRestClient::class))
        ->arg('$request', service(\EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\SearchRequest::class))
        ->arg(
            '$responseHandler',
            service(\EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\SearchResponse::class)
        );
    $services->set(CatalogProductImporter::class)
        ->arg('$magentoService', service('ew_magento_catalog_product_search_service'))
        ->tag('everyworkflow.magento_connector.importer', ['priority' => 8090]);
};
