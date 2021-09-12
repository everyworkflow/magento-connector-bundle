<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Importer;

use EveryWorkflow\EavBundle\Attribute\BaseAttributeInterface;
use EveryWorkflow\EavBundle\Repository\AttributeRepositoryInterface;
use EveryWorkflow\MagentoConnectorBundle\Factory\MagentoServiceFactoryInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\Importer;
use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\AttributeSearchResponseInterface;
use Psr\Log\LoggerInterface;

class CatalogProductAttributeImporter extends Importer implements CatalogProductAttributeImporterInterface
{
    protected MagentoServiceFactoryInterface $magentoServiceFactory;
    protected AttributeRepositoryInterface $attributeRepository;
    protected LoggerInterface $logger;

    public function __construct(
        MagentoServiceFactoryInterface $magentoServiceFactory,
        AttributeRepositoryInterface $attributeRepository,
        LoggerInterface              $ewRemoteErrorLogger
    ) {
        $this->magentoServiceFactory = $magentoServiceFactory;
        $this->attributeRepository = $attributeRepository;
        $this->logger = $ewRemoteErrorLogger;
    }

    public function execute(string $type = ImportProcessorInterface::TYPE_INCREMENTAL): void
    {
        $pageSize = 100;
        $currentPage = 1;
        $totalPage = 1;
        while ($currentPage <= $totalPage) {
            try {
                $magentoService = $this->magentoServiceFactory
                    ->setRequestClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\AttributeSearchRequest::class)
                    ->setResponseHandlerClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct\AttributeSearchResponse::class)
                    ->create([
                        'page_size' => $pageSize,
                        'current_page' => $currentPage,
                    ]);
                /** @var AttributeSearchResponseInterface $response */
                $response = $magentoService->send();
                $this->saveAttributesFromResponse($response);
                $totalPage = $this->getTotalPageCount($response, $pageSize);
            } catch (\Exception $e) {
                $this->logger->error('Error: catalog_product_attribute_import | PageNo: ' . $currentPage . ' | Message: ' . $e->getMessage());
            }
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
