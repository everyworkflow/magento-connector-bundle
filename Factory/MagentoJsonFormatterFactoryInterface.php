<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Factory;

use EveryWorkflow\MagentoConnectorBundle\Model\Formatter\MagentoJsonFormatterInterface;

interface MagentoJsonFormatterFactoryInterface
{
    public function create(): MagentoJsonFormatterInterface;
}
