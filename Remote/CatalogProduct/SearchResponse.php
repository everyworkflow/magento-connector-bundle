<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct;

use EveryWorkflow\CatalogProductBundle\Entity\CatalogProductEntityInterface;
use EveryWorkflow\CatalogProductBundle\Repository\CatalogProductRepositoryInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponse;
use Psr\Log\LoggerInterface;

class SearchResponse extends RemoteResponse implements SearchResponseInterface
{
    protected CatalogProductRepositoryInterface $catalogProductRepository;
    protected LoggerInterface $logger;

    public function __construct(
        CatalogProductRepositoryInterface $catalogProductRepository,
        LoggerInterface                   $ewRemoteErrorLogger,
        array                             $data = []
    ) {
        parent::__construct($data);
        $this->catalogProductRepository = $catalogProductRepository;
        $this->logger = $ewRemoteErrorLogger;
    }

    /**
     * @return CatalogProductEntityInterface[]
     */
    public function getProducts(): array
    {
        $products = [];
        $productItems = $this->data['items'] ?? [];

        foreach ($productItems as $item) {
            try {
                $products[] = $this->mapMageProduct($item);
            } catch (\Exception $e) {
                try {
                    $itemJson = json_encode($item);
                } catch (\Exception $e) {
                    // Skip if unable to capture attribute data
                    $itemJson = '';
                }
                $this->logger->error('Error: magento_product_map | ' . $itemJson . ' | Message: ' . $e->getMessage());
            }
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
                if (isset($customAttr['attribute_code'], $customAttr['value'])) {
                    if (in_array($customAttr['attribute_code'], $availableAttributeCodes, true)) {
                        $productData[$customAttr['attribute_code']] = $customAttr['value'];
                    }
                }
            }
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->catalogProductRepository->create($productData);
    }
}
