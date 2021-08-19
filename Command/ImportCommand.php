<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Command;

use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName = 'magento-connector:import';
    protected ImportProcessorInterface $importProcessor;

    public function __construct(
        ImportProcessorInterface $importProcessor,
        string                   $name = null
    ) {
        parent::__construct($name);
        $this->importProcessor = $importProcessor;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Import magento data')
            ->setHelp(
                'bin/console magento-connector:import --type=incremental' .
                PHP_EOL .
                'bin/console magento-connector:import --type=full'
            )
            ->addOption(
                'type',
                't',
                InputOption::VALUE_REQUIRED,
                'Import type: incremental or full'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputOutput = new SymfonyStyle($input, $output);

        $type = $input->getOption('type');
        if (!in_array($type, [ImportProcessorInterface::TYPE_INCREMENTAL, ImportProcessorInterface::TYPE_FULL], true)) {
            $inputOutput->error('Import type must be defined: incremental or full');
            return Command::FAILURE;
        }

        $inputOutput->info('Magento data import started');
        $this->importProcessor->setOutputLogger($output)->execute($type);
        $inputOutput->success('Magento data import completed');

        return Command::SUCCESS;
    }
}
