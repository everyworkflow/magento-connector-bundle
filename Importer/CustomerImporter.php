<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Importer;

use EveryWorkflow\CustomerBundle\Entity\CustomerEntityInterface;
use EveryWorkflow\CustomerBundle\Repository\CustomerRepositoryInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\Importer;
use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\MagentoServiceInterface;
use EveryWorkflow\MagentoConnectorBundle\Remote\Customer\SearchResponseInterface;
use Psr\Log\LoggerInterface;

class CustomerImporter extends Importer implements CustomerImporterInterface
{
    protected MagentoServiceInterface $magentoService;
    protected CustomerRepositoryInterface $customerRepository;
    protected LoggerInterface $logger;

    public function __construct(
        MagentoServiceInterface     $magentoService,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface             $logger
    ) {
        $this->magentoService = $magentoService;
        $this->customerRepository = $customerRepository;
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
        $lastSyncCustomer = $this->customerRepository->findOne([], ['sort' => ['updated_at_magento' => -1]]);
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
            $this->saveCustomersFromResponse($response);
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
            $this->saveCustomersFromResponse($response);
            $totalPage = $this->getTotalPageCount($response, $pageSize);
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
