<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Importer;

use EveryWorkflow\EavBundle\Attribute\BaseAttributeInterface;
use EveryWorkflow\EavBundle\Repository\AttributeRepositoryInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\Importer;
use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\MagentoServiceInterface;
use EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\AttributeSearchResponseInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponseInterface;
use Psr\Log\LoggerInterface;

class CatalogProductAttributeImporter extends Importer implements CatalogProductAttributeImporterInterface
{
    protected MagentoServiceInterface $magentoService;
    protected AttributeRepositoryInterface $attributeRepository;
    protected LoggerInterface $logger;

    public function __construct(
        MagentoServiceInterface      $magentoService,
        AttributeRepositoryInterface $attributeRepository,
        LoggerInterface $logger
    ) {
        $this->magentoService = $magentoService;
        $this->attributeRepository = $attributeRepository;
        $this->logger = $logger;
    }

    public function execute(string $type = ImportProcessorInterface::TYPE_INCREMENTAL): void
    {
        $pageSize = 100;
        $currentPage = 1;
        $totalPage = 1;
        while ($currentPage <= $totalPage) {
            $this->magentoService->getSearchCriteria()
                ->setPageSize($pageSize)
                ->setCurrentPage($currentPage);
            /** @var AttributeSearchResponseInterface $response */
            $response = $this->magentoService->send();
            $this->saveAttributesFromResponse($response);
            $totalPage = $this->getTotalPageCount($response, $pageSize);
            $currentPage++;
            sleep(2);
        }
    }

    protected function saveAttributesFromResponse(AttributeSearchResponseInterface $response): void
    {
        foreach ($response->getAttributes() as $attribute) {
            if ($attribute instanceof BaseAttributeInterface) {
                try {
                    $this->attributeRepository->save($attribute);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }
}
