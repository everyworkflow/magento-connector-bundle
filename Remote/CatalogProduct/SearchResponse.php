<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct;

use EveryWorkflow\CatalogProductBundle\Entity\CatalogProductEntityInterface;
use EveryWorkflow\CatalogProductBundle\Repository\CatalogProductRepositoryInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponse;

class SearchResponse extends RemoteResponse implements SearchResponseInterface
{
    protected CatalogProductRepositoryInterface $catalogProductRepository;

    public function __construct(
        CatalogProductRepositoryInterface $catalogProductRepository,
        array                             $data = []
    ) {
        parent::__construct($data);
        $this->catalogProductRepository = $catalogProductRepository;
    }

    /**
     * @return CatalogProductEntityInterface[]
     */
    public function getProducts(): array
    {
        $products = [];
        $productItems = $this->data['items'] ?? [];

        foreach ($productItems as $item) {
            $products[] = $this->mapMageProduct($item);
        }

        return $products;
    }

    protected function mapMageProduct(array $mageProductData): CatalogProductEntityInterface
    {
        $productData = [
            'magento_id' => $mageProductData['id'] ?? '',
            'updated_at_magento' => $mageProductData['updated_at'] ?? '',
        ];
        $availableAttributeCodes = [];

        foreach ($this->catalogProductRepository->getAttributes() as $attribute) {
            $availableAttributeCodes[] = $attribute->getCode();
            if (isset($mageProductData[$attribute->getCode()])) {
                $productData[$attribute->getCode()] = $mageProductData[$attribute->getCode()];
            }
        }

        if (isset($mageProductData['custom_attributes']) && is_array($mageProductData['custom_attributes'])) {
            foreach ($mageProductData['custom_attributes'] as $customAttr) {
                if (isset($customAttr['attribute_code'])) {
                    if (isset($availableAttributeCodes[$customAttr['attribute_code']])) {
                        $productData[$customAttr['attribute_code']] = $mageProductData[$customAttr['attribute_code']];
                    }
                }
            }
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->catalogProductRepository->getNewDocument($productData);
    }
}
