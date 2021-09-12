<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Importer;

use EveryWorkflow\CustomerBundle\Entity\CustomerEntityInterface;
use EveryWorkflow\CustomerBundle\Repository\CustomerRepositoryInterface;
use EveryWorkflow\MagentoConnectorBundle\Factory\MagentoServiceFactoryInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\Importer;
use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use EveryWorkflow\MagentoConnectorBundle\Remote\Customer\SearchResponseInterface;
use Psr\Log\LoggerInterface;

class CustomerImporter extends Importer implements CustomerImporterInterface
{
    protected MagentoServiceFactoryInterface $magentoServiceFactory;
    protected CustomerRepositoryInterface $customerRepository;
    protected LoggerInterface $logger;

    public function __construct(
        MagentoServiceFactoryInterface $magentoServiceFactory,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $ewRemoteErrorLogger
    ) {
        $this->magentoServiceFactory = $magentoServiceFactory;
        $this->customerRepository = $customerRepository;
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
        $lastSyncCustomer = $this->customerRepository->findOne([], ['sort' => ['updated_at_magento' => -1]]);
        $pageSize = 100;
        $currentPage = 1;
        $totalPage = 1;
        while ($currentPage <= $totalPage) {
            try {
                $magentoService = $this->magentoServiceFactory
                    ->setRequestClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\Customer\SearchRequest::class)
                    ->setResponseHandlerClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\Customer\SearchResponse::class)
                    ->create();
                $magentoService->getSearchCriteria()
                    ->addFilter('updated_at', $lastSyncCustomer->getData('updated_at_magento'), 'gteq')
                    ->setPageSize($pageSize)
                    ->setCurrentPage($currentPage);
                /** @var SearchResponseInterface $response */
                $response = $magentoService->send();
                $this->saveCustomersFromResponse($response);
                $totalPage = $this->getTotalPageCount($response, $pageSize);
            } catch (\Exception $e) {
                $this->logger->error('Error: customer_incremental_import | PageNo: ' . $currentPage . ' | Message: ' . $e->getMessage());
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
                    ->setRequestClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\Customer\SearchRequest::class)
                    ->setResponseHandlerClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\Customer\SearchResponse::class)
                    ->create([
                        'page_size' => $pageSize,
                        'current_page' => $currentPage,
                    ]);
                /** @var SearchResponseInterface $response */
                $response = $magentoService->send();
                $this->saveCustomersFromResponse($response);
                $totalPage = $this->getTotalPageCount($response, $pageSize);
            } catch (\Exception $e) {
                $this->logger->error('Error: customer_full_import | PageNo: ' . $currentPage . ' | Message: ' . $e->getMessage());
            }
            $currentPage++;
            sleep(2);
        }
    }

    protected function saveCustomersFromResponse(SearchResponseInterface $searchResponse): void
    {
        foreach ($searchResponse->getCustomers() as $customer) {
            if ($customer instanceof CustomerEntityInterface) {
                try {
                    $this->customerRepository->save($customer);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }
}
