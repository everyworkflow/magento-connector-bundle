<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

interface ImporterInterface
{
    public function execute(string $type = ImportProcessorInterface::TYPE_INCREMENTAL): void;
}
