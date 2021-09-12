<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Command\Import;

use EveryWorkflow\MagentoConnectorBundle\Importer\CustomerAttributeImporterInterface;
use EveryWorkflow\MagentoConnectorBundle\Importer\CustomerImporterInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CustomerCommand extends Command
{
    protected static $defaultName = 'magento-connector:import:customer';
    protected CustomerAttributeImporterInterface $customerAttributeImporter;
    protected CustomerImporterInterface $customerImporter;

    public function __construct(
        CustomerAttributeImporterInterface $customerAttributeImporter,
        CustomerImporterInterface          $customerImporter,
        string                             $name = null
    ) {
        parent::__construct($name);
        $this->customerAttributeImporter = $customerAttributeImporter;
        $this->customerImporter = $customerImporter;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Import magento customer data')
            ->setHelp(
                'bin/console magento-connector:import:customer --type=incremental' .
                PHP_EOL .
                'bin/console magento-connector:import:customer --type=full'
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

        // $inputOutput->info('Magento customer attribute data import started');
        // $this->customerAttributeImporter->execute($type);
        // $inputOutput->success('Magento customer attribute data import completed');

        $inputOutput->info('Magento customer data import started');
        $this->customerImporter->execute($type);
        $inputOutput->success('Magento customer data import completed');

        return Command::SUCCESS;
    }
}
