<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Command\Import;

use EveryWorkflow\MagentoConnectorBundle\Model\ImportProcessorInterface;
use EveryWorkflow\MagentoConnectorBundle\Importer\CatalogProductAttributeImporterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProductAttributeCommand extends Command
{
    protected static $defaultName = 'magento-connector:import:product-attribute';
    

    public function __construct(
        protected CatalogProductAttributeImporterInterface $productAttributeImporter,
        string $name = null
    ) {
        parent::__construct($name);
        
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Import product attribute set data data')
            ->setHelp(
                'bin/console magento-connector:import:product-attribute --type=incremental' .
                    PHP_EOL .
                    'bin/console magento-connector:import:product-attribute --type=full'
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

        $inputOutput->info('Magento product import started import started');
        $this->productAttributeImporter->execute($type);
        $inputOutput->success('Magento product attribute set data import completed');

        return Command::SUCCESS;
    }
}
