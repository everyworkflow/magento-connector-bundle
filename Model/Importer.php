<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use EveryWorkflow\RemoteBundle\Model\RemoteResponseInterface;

class Importer implements ImporterInterface
{
    protected function getTotalPageCount(RemoteResponseInterface $response, int $pageSize): int
    {
        $totalCount = $response->getData('total_count');
        if ($totalCount && $totalCount > 1 && $pageSize > 0) {
            return (int)ceil($totalCount / $pageSize);
        }
        return 0;
    }

    public function execute(string $type = ImportProcessorInterface::TYPE_INCREMENTAL): void
    {
        // Something
    }
}
