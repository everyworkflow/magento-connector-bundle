<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Importer;

use EveryWorkflow\CatalogProductBundle\Entity\CatalogProductEntityInterface;
use EveryWorkflow\CatalogProductBundle\Repository\CatalogProductRepositoryInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\Importer;
use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\MagentoServiceInterface;
use EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\SearchResponseInterface;
use Psr\Log\LoggerInterface;

class CatalogProductImporter extends Importer implements CatalogProductImporterInterface
{
    protected MagentoServiceInterface $magentoService;
    protected CatalogProductRepositoryInterface $catalogProductRepository;
    protected LoggerInterface $logger;

    public function __construct(
        MagentoServiceInterface           $magentoService,
        CatalogProductRepositoryInterface $catalogProductRepository,
        LoggerInterface $logger
    ) {
        $this->magentoService = $magentoService;
        $this->catalogProductRepository = $catalogProductRepository;
        $this->logger = $logger;
    }

    public function execute(string $type = ImportProcessorInterface::TYPE_INCREMENTAL): void
    {
        switch ($type) {
            case ImportProcessorInterface::TYPE_INCREMENTAL:
            {
                $this->importIncremental();
            }
            case ImportProcessorInterface::TYPE_FULL:
            {
                $this->importFull();
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
            $this->magentoService->getSearchCriteria()
                ->addFilter('created_at', $lastSyncCustomer->getData('updated_at_magento'), 'gteq')
                ->setPageSize($pageSize)
                ->setCurrentPage($currentPage);
            /** @var SearchResponseInterface $response */
            $response = $this->magentoService->send();
            $this->saveProductsFromResponse($response);
            $totalPage = $this->getTotalPageCount($response, $pageSize);
            $currentPage++;
            sleep(2);
        }
    }

    protected function importFull(): void
    {
        $pageSize = 100;
        $currentPage = 1;
        $totalPage = 1;
        while (($currentPage <= $totalPage) && ($currentPage < 5)) {
            $this->magentoService->getSearchCriteria()
                ->setPageSize($pageSize)
                ->setCurrentPage($currentPage);
            /** @var SearchResponseInterface $response */
            $response = $this->magentoService->send();
            $this->saveProductsFromResponse($response);
            $totalPage = $this->getTotalPageCount($response, $pageSize);
            $currentPage++;
            sleep(2);
        }
    }

    protected function saveProductsFromResponse(SearchResponseInterface $searchResponse): void
    {
        foreach ($searchResponse->getProducts() as $product) {
            if ($product instanceof CatalogProductEntityInterface) {
                try {
                    $this->catalogProductRepository->save($product);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }
}
