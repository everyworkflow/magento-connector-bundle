<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

class ImportProcessor implements ImportProcessorInterface
{
    protected iterable $importers;

    public function __construct(iterable $importers)
    {
        $this->importers = $importers;
    }

    public function execute(string $type = self::TYPE_INCREMENTAL): void
    {
        foreach ($this->importers as $importer) {
            if ($importer instanceof ImporterInterface) {
                $importer->execute($type);
            }
        }
    }
}
