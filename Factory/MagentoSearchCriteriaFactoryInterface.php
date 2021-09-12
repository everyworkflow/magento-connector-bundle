<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Factory;

use EveryWorkflow\MagentoConnectorBundle\Model\MagentoSearchCriteriaInterface;

interface MagentoSearchCriteriaFactoryInterface
{
    public function create(array $data): MagentoSearchCriteriaInterface;
}
