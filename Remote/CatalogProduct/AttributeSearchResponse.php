<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct;

use EveryWorkflow\CatalogProductBundle\Repository\CatalogProductRepositoryInterface;
use EveryWorkflow\EavBundle\Attribute\BaseAttributeInterface;
use EveryWorkflow\EavBundle\Factory\AttributeFactoryInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponse;

class AttributeSearchResponse extends RemoteResponse implements AttributeSearchResponseInterface
{
    protected const USED_IN_GRID_ATTRIBUTES = [
        'sku',
        'name',
        'created_at',
        'updated_at',
    ];

    protected CatalogProductRepositoryInterface $catalogProductRepository;
    protected AttributeFactoryInterface $attributeFactory;

    public function __construct(
        CatalogProductRepositoryInterface $catalogProductRepository,
        AttributeFactoryInterface         $attributeFactory,
        array                             $data = []
    ) {
        parent::__construct($data);
        $this->catalogProductRepository = $catalogProductRepository;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @return BaseAttributeInterface[]
     */
    public function getAttributes(): array
    {
        $attributes = [];
        $attributeItems = $this->data['items'] ?? [];

        foreach ($attributeItems as $item) {
            $attributes[] = $this->mapMageAttribute($item);
        }

        return $attributes;
    }

    protected function mapMageAttribute(array $mageAttrData): BaseAttributeInterface
    {
        $attributeData = [
            'code' => $mageAttrData['attribute_code'] ?? '',
            'name' => $mageAttrData['default_frontend_label'] ?? '',
            'entity_code' => $this->catalogProductRepository->getEntityCode(),
            'is_used_in_grid' => in_array($mageAttrData['attribute_code'], self::USED_IN_GRID_ATTRIBUTES, true),
            'is_required' => (bool)$mageAttrData['is_required'] ?? false,
            'sort_order' => $mageAttrData['position'] ?? '999',
        ];

        $frontendInput = $mageAttrData['frontend_input'] ?? 'text';
        switch ($frontendInput) {
            case 'hidden':
            {
                $attributeData['is_readonly'] = true;
                return $this->attributeFactory->createAttributeFromType('text_attribute', $attributeData);
            }
            case 'textarea':
            {
                return $this->attributeFactory->createAttributeFromType('long_text_attribute', $attributeData);
            }
            case 'select':
            {
                $attributeData['options'] = [];
                if (isset($mageAttrData['options']) && is_array($mageAttrData['options'])) {
                    foreach ($mageAttrData['options'] as $option) {
                        $attributeData['options'][] = [
                            'key' => $option['value'] ?? '',
                            'value' => $option['label'] ?? '',
                        ];
                    }
                }
                return $this->attributeFactory->createAttributeFromType('select_attribute', $attributeData);
            }
            case 'multiselect':
            {
                $attributeData['options'] = [];
                if (isset($mageAttrData['options']) && is_array($mageAttrData['options'])) {
                    foreach ($mageAttrData['options'] as $option) {
                        $attributeData['options'][] = [
                            'key' => $option['value'],
                            'value' => $option['label'],
                        ];
                    }
                }
                return $this->attributeFactory->createAttributeFromType('multi_select_attribute', $attributeData);
            }
            case 'boolean':
            {
                return $this->attributeFactory->createAttributeFromType('boolean_attribute', $attributeData);
            }
            case 'date':
            {
                return $this->attributeFactory->createAttributeFromType('date_time_attribute', $attributeData);
            }
            default:
            {
                return $this->attributeFactory->createAttributeFromType('text_attribute', $attributeData);
            }
        }
    }
}
