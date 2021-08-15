<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Controller;

use EveryWorkflow\MagentoConnectorBundle\Importer\CustomerImporterInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MageTestController extends AbstractController
{
    protected CustomerImporterInterface $customerImporter;

    public function __construct(CustomerImporterInterface $customerImporter)
    {
        $this->customerImporter = $customerImporter;
    }

    /**
     * @Route(
     *     path="magento-connector-test",
     *     name="magento.connector.test",
     *     methods="GET"
     * )
     */
    public function __invoke(): JsonResponse
    {
        $this->customerImporter->execute(ImportProcessorInterface::TYPE_INCREMENTAL);
        return new JsonResponse();
    }
}
