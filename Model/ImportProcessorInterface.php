<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

interface ImportProcessorInterface
{
    public const TYPE_INCREMENTAL = 'incremental';
    public const TYPE_FULL = 'full';

    public function execute(string $type = self::TYPE_INCREMENTAL): void;
}
