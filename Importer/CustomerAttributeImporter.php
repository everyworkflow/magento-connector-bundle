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
use EveryWorkflow\MagentoConnectorBundle\Remote\Customer\AttributeResponseInterface;
use Psr\Log\LoggerInterface;

class CustomerAttributeImporter extends Importer implements CustomerAttributeImporterInterface
{
    protected MagentoServiceFactoryInterface $magentoServiceFactory;
    protected AttributeRepositoryInterface $attributeRepository;
    protected LoggerInterface $logger;

    public function __construct(
        MagentoServiceFactoryInterface $magentoServiceFactory,
        AttributeRepositoryInterface $attributeRepository,
        LoggerInterface $ewRemoteErrorLogger
    ) {
        $this->magentoServiceFactory = $magentoServiceFactory;
        $this->attributeRepository = $attributeRepository;
        $this->logger = $ewRemoteErrorLogger;
    }

    public function execute(string $type = ImportProcessorInterface::TYPE_INCREMENTAL): void
    {
        try {
            /** @var AttributeResponseInterface $response */
            $response = $this->magentoServiceFactory
                ->setRequestClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\Customer\AttributeRequest::class)
                ->setResponseHandlerClassName(\EveryWorkflow\MagentoConnectorBundle\Remote\Customer\AttributeResponse::class)
                ->create()
                ->send();
            $this->saveAttributesFromResponse($response);
        } catch (\Exception $e) {
            $this->logger->error('Error: customer_attribute_import | Message: ' . $e->getMessage());
        }
    }

    protected function saveAttributesFromResponse(AttributeResponseInterface $response): void
    {
        foreach ($response->getAttributes() as $attribute) {
            if ($attribute instanceof BaseAttributeInterface) {
                try {
                    $this->attributeRepository->saveOne($attribute);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }
}
