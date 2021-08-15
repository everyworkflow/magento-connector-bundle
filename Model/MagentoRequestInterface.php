<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use EveryWorkflow\RemoteBundle\Model\RemoteRequestInterface;

interface MagentoRequestInterface extends RemoteRequestInterface
{
    public function setSearchCriteria(MagentoSearchCriteriaInterface $searchCriteria): self;

    public function getSearchCriteria(): MagentoSearchCriteriaInterface;
}
