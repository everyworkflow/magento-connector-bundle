<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Migration;

use EveryWorkflow\CatalogProductBundle\Repository\CatalogProductRepositoryInterface;
use EveryWorkflow\CustomerBundle\Repository\CustomerRepositoryInterface;
use EveryWorkflow\EavBundle\Repository\AttributeRepositoryInterface;
use EveryWorkflow\MongoBundle\Support\MigrationInterface;

class Mongo_2021_02_01_00_00_00_Magento_Remote_Reference_Migration implements MigrationInterface
{
    protected CustomerRepositoryInterface $customerRepository;
    protected AttributeRepositoryInterface $attributeRepository;
    protected CatalogProductRepositoryInterface $catalogProductRepository;

    public function __construct(
        CustomerRepositoryInterface       $customerRepository,
        AttributeRepositoryInterface      $attributeRepository,
        CatalogProductRepositoryInterface $catalogProductRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->attributeRepository = $attributeRepository;
        $this->catalogProductRepository = $catalogProductRepository;
    }

    public function migrate(): bool
    {
        $this->addFieldsForCustomer();

        $this->addFieldsForCatalogProduct();

        return self::SUCCESS;
    }

    protected function addFieldsForCustomer(): void
    {
        $magentoId = $this->attributeRepository->getNewDocument(
            [
                'code' => 'magento_id',
                'name' => 'Magento ID',
                'type' => 'text_attribute',
                'entity_code' => $this->customerRepository->getEntityCode(),
                'is_used_in_grid' => true,
                'is_used_in_form' => true,
                'is_readonly' => true,
                'sort_order' => 3,
            ]);
        $this->attributeRepository->save($magentoId);

        $updatedAtMagento = $this->attributeRepository->getNewDocument(
            [
                'code' => 'updated_at_magento',
                'name' => 'Updated at Magento',
                'type' => 'date_time_attribute',
                'entity_code' => $this->customerRepository->getEntityCode(),
                'is_used_in_grid' => false,
                'is_used_in_form' => true,
                'is_readonly' => true,
                'sort_order' => 3,
            ]);
        $this->attributeRepository->save($updatedAtMagento);
    }

    protected function addFieldsForCatalogProduct(): void
    {
        $magentoId = $this->catalogProductRepository->getNewDocument(
            [
                'code' => 'magento_id',
                'name' => 'Magento ID',
                'type' => 'text_attribute',
                'entity_code' => $this->catalogProductRepository->getEntityCode(),
                'is_used_in_grid' => true,
                'is_used_in_form' => true,
                'is_readonly' => true,
                'sort_order' => 3,
            ]);
        $this->attributeRepository->save($magentoId);

        $updatedAtMagento = $this->catalogProductRepository->getNewDocument(
            [
                'code' => 'updated_at_magento',
                'name' => 'Updated at Magento',
                'type' => 'date_time_attribute',
                'entity_code' => $this->catalogProductRepository->getEntityCode(),
                'is_used_in_grid' => false,
                'is_used_in_form' => true,
                'is_readonly' => true,
                'sort_order' => 3,
            ]);
        $this->attributeRepository->save($updatedAtMagento);
    }

    public function rollback(): bool
    {
        $this->attributeRepository->deleteByFilter([
            'code' => 'magento_id',
            'entity_code' => $this->customerRepository->getEntityCode(),
        ]);
        $this->attributeRepository->deleteByFilter([
            'code' => 'updated_at_magento',
            'entity_code' => $this->customerRepository->getEntityCode(),
        ]);

        $this->attributeRepository->deleteByFilter([
            'code' => 'updated_at_magento',
            'entity_code' => $this->catalogProductRepository->getEntityCode(),
        ]);
        $this->attributeRepository->deleteByFilter([
            'code' => 'updated_at_magento',
            'entity_code' => $this->catalogProductRepository->getEntityCode(),
        ]);

        return self::SUCCESS;
    }
}
