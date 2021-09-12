<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Factory;

use EveryWorkflow\MagentoConnectorBundle\Model\Client\MagentoRestClientInterface;

interface MagentoClientFactoryInterface
{
    public function create(array $configs = []): MagentoRestClientInterface;
}
