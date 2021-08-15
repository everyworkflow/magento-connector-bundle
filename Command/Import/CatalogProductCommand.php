<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Command\Import;

use EveryWorkflow\MagentoConnectorBundle\Importer\CatalogProductAttributeImporterInterface;
use EveryWorkflow\MagentoConnectorBundle\Importer\CatalogProductImporterInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CatalogProductCommand extends Command
{
    protected static $defaultName = 'magento-connector:import:catalog_product';
    protected CatalogProductAttributeImporterInterface $catalogProductAttributeImporter;
    protected CatalogProductImporterInterface $catalogProductImporter;

    public function __construct(
        CatalogProductAttributeImporterInterface $catalogProductAttributeImporter,
        CatalogProductImporterInterface          $catalogProductImporter,
        string                                   $name = null
    ) {
        parent::__construct($name);
        $this->catalogProductAttributeImporter = $catalogProductAttributeImporter;
        $this->catalogProductImporter = $catalogProductImporter;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Import magento catalog product data')
            ->setHelp(
                'bin/console magento-connector:import:catalog_product --type=incremental' .
                PHP_EOL .
                'bin/console magento-connector:import:catalog_product --type=full'
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

        $inputOutput->info('Magento catalog product attribute data import started');
        $this->catalogProductAttributeImporter->execute($type);
        $inputOutput->success('Magento catalog product attribute data import completed');

        $inputOutput->info('Magento catalog product data import started');
        $this->catalogProductImporter->execute($type);
        $inputOutput->success('Magento catalog product data import completed');

        return Command::SUCCESS;
    }
}
