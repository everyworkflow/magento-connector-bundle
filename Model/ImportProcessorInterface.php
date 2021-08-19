<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use Symfony\Component\Console\Output\OutputInterface;

interface ImportProcessorInterface
{
    public const TYPE_INCREMENTAL = 'incremental';
    public const TYPE_FULL = 'full';

    public function setOutputLogger(OutputInterface $output): self;

    public function execute(string $type = self::TYPE_INCREMENTAL): void;
}
