<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Importer;

use EveryWorkflow\CatalogProductBundle\Entity\CatalogProductEntityInterface;
use EveryWorkflow\CatalogProductBundle\Repository\CatalogProductRepositoryInterface;
use EveryWorkflow\MagentoConnectorBundle\Factory\MagentoServiceFactoryInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\Importer;
use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\SearchResponseInterface;
use Psr\Log\LoggerInterface;

class CatalogProductImporter extends Importer implements CatalogProductImporterInterface
{
    protected MagentoServiceFactoryInterface $magentoServiceFactory;
    protected CatalogProductRepositoryInterface $catalogProductRepository;
    protected LoggerInterface $logger;

    public function __construct(
        MagentoServiceFactoryInterface $magentoServiceFactory,
        CatalogProductRepositoryInterface $catalogProductRepository,
        LoggerInterface $ewRemoteErrorLogger
    ) {
        $this->magentoServiceFactory = $magentoServiceFactory;
        $this->catalogProductRepository = $catalogProductRepository;
        $this->logger = $ewRemoteErrorLogger;
    }

    public function execute(string $type = ImportProcessorInterface::TYPE_INCREMENTAL): void
    {
        switch ($type) {
            case ImportProcessorInterface::TYPE_INCREMENTAL: {
                    $this->importIncremental();
                    break;
                }
            case ImportProcessorInterface::TYPE_FULL: {
                    $this->importFull();
                    break;
                }
        }
    }

    protected function importIncremental(): void
    {
        $lastSyncCustomer = $this->catalogProductRepository->findOne([], ['sort' => ['updated_at_magento' => -1]]);
        $pageSize = 100;
        $currentPage = 1;
        $totalPage = 1;
        while ($currentPage <= $totalPage) {
            try {
                $magentoService = $this->magentoServiceFactory
                    ->setRequestClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\SearchRequest::class)
                    ->setResponseHandlerClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\SearchResponse::class)
                    ->create();
                $magentoService->getSearchCriteria()
                    ->addFilter('created_at', $lastSyncCustomer->getData('updated_at_magento'), 'gteq')
                    ->setPageSize($pageSize)
                    ->setCurrentPage($currentPage);
                /** @var SearchResponseInterface $response */
                $response = $magentoService->send();
                $this->saveProductsFromResponse($response);
                $totalPage = $this->getTotalPageCount($response, $pageSize);
            } catch (\Exception $e) {
                $this->logger->error('Error: catalog_product_incremental_import | PageNo: ' . $currentPage . ' | Message: ' . $e->getMessage());
            }
            $currentPage++;
            sleep(2);
        }
    }

    protected function importFull(): void
    {
        $pageSize = 100;
        $currentPage = 1;
        $totalPage = 1;
        while ($currentPage <= $totalPage) {
            try {
                $magentoService = $this->magentoServiceFactory
                    ->setRequestClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\SearchRequest::class)
                    ->setResponseHandlerClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\SearchResponse::class)
                    ->create([
                        'page_size' => $pageSize,
                        'current_page' => $currentPage,
                    ]);
                /** @var SearchResponseInterface $response */
                $response = $magentoService->send();
                $this->saveProductsFromResponse($response);
                $totalPage = $this->getTotalPageCount($response, $pageSize);
            } catch (\Exception $e) {
                $this->logger->error('Error: catalog_product_full_import | PageNo: ' . $currentPage . ' | Message: ' . $e->getMessage());
            }
            $currentPage++;
            sleep(2);
        }
    }

    protected function saveProductsFromResponse(SearchResponseInterface $searchResponse): void
    {
        foreach ($searchResponse->getProducts() as $product) {
            if ($product instanceof CatalogProductEntityInterface) {
                try {
                    $this->catalogProductRepository->saveOne($product);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }
}
