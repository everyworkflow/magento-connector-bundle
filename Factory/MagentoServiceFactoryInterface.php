<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Factory;

use EveryWorkflow\MagentoConnectorBundle\Model\MagentoServiceInterface;

interface MagentoServiceFactoryInterface
{
    public function setRequestClassName(string $requestClassName): self;

    public function setResponseHandlerClassName(string $responseHandlerClassName): self;

    public function create(array $searchCriteriaData = []): MagentoServiceInterface;
}
