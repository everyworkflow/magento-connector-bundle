<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProcessor implements ImportProcessorInterface
{
    protected ?OutputInterface $output;

    protected iterable $importers;
    protected LoggerInterface $logger;
    protected LoggerInterface $errorLogger;

    public function __construct(
        LoggerInterface $ewRemoteLogger,
        LoggerInterface $ewRemoteErrorLogger,
        iterable        $importers
    ) {
        $this->importers = $importers;
        $this->logger = $ewRemoteLogger;
        $this->errorLogger = $ewRemoteErrorLogger;
    }

    public function setOutputLogger(OutputInterface $output): self
    {
        $this->output = $output;
        return $this;
    }

    public function execute(string $type = self::TYPE_INCREMENTAL): void
    {
        foreach ($this->importers as $importer) {
            $processingText = 'Processing importer: ' . get_class($importer);
            $this->logger->info($processingText);
            $this->output->writeln($processingText);
            if ($importer instanceof ImporterInterface) {
                try {
                    $importer->execute($type);
                } catch (\Exception $e) {
                    $this->errorLogger->error($e->getMessage());
                }
            }
        }
    }
}
