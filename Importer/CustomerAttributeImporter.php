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
use EveryWorkflow\MagentoConnectorBundle\Remote\Customer\AttributeResponseInterface;
use Psr\Log\LoggerInterface;

class CustomerAttributeImporter extends Importer implements CustomerAttributeImporterInterface
{
    protected MagentoServiceInterface $magentoService;
    protected AttributeRepositoryInterface $attributeRepository;
    protected LoggerInterface $logger;

    public function __construct(
        MagentoServiceInterface      $magentoService,
        AttributeRepositoryInterface $attributeRepository,
        LoggerInterface              $ewRemoteErrorLogger
    ) {
        $this->magentoService = $magentoService;
        $this->attributeRepository = $attributeRepository;
        $this->logger = $ewRemoteErrorLogger;
    }

    public function execute(string $type = ImportProcessorInterface::TYPE_INCREMENTAL): void
    {
        try {
            /** @var AttributeResponseInterface $response */
            $response = $this->magentoService->send();
            $this->saveAttributesFromResponse($response);
        } catch (\Exception $e) {
            $this->logger->error('Error: customer_attribute_import | PageNo: ' . $currentPage . ' | Message: ' . $e->getMessage());
        }
    }

    protected function saveAttributesFromResponse(AttributeResponseInterface $response): void
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
