<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Factory;

use EveryWorkflow\MagentoConnectorBundle\Model\MagentoSearchCriteria;
use EveryWorkflow\MagentoConnectorBundle\Model\MagentoSearchCriteriaInterface;

class MagentoSearchCriteriaFactory implements MagentoSearchCriteriaFactoryInterface
{
    public function create(array $data): MagentoSearchCriteriaInterface
    {
        return new MagentoSearchCriteria($data);
    }
}
