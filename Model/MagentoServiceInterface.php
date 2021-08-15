<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use EveryWorkflow\RemoteBundle\Model\RemoteServiceInterface;

interface MagentoServiceInterface extends RemoteServiceInterface
{
    public function setSearchCriteria(MagentoSearchCriteriaInterface $searchCriteria): self;

    public function getSearchCriteria(): MagentoSearchCriteriaInterface;
}
