<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\Customer;

use EveryWorkflow\CustomerBundle\Repository\CustomerRepositoryInterface;
use EveryWorkflow\EavBundle\Attribute\BaseAttributeInterface;
use EveryWorkflow\EavBundle\Factory\AttributeFactoryInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponse;
use Psr\Log\LoggerInterface;

class AttributeResponse extends RemoteResponse implements AttributeResponseInterface
{
    protected const USED_IN_GRID_ATTRIBUTES = [
        'firstname',
        'lastname',
        'name',
        'email',
        'created_at',
        'updated_at',
    ];

    protected AttributeFactoryInterface $attributeFactory;
    protected CustomerRepositoryInterface $customerRepository;
    protected LoggerInterface $logger;

    public function __construct(
        AttributeFactoryInterface   $attributeFactory,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface             $ewRemoteErrorLogger,
        array                       $data = []
    ) {
        parent::__construct($data);
        $this->attributeFactory = $attributeFactory;
        $this->customerRepository = $customerRepository;
        $this->logger = $ewRemoteErrorLogger;
    }

    public function handle(array $responseData): self
    {
        $this->data['items'] = $responseData;
        return $this;
    }

    /**
     * @return BaseAttributeInterface[]
     */
    public function getAttributes(): array
    {
        $attributes = [];
        $attributeItems = $this->data['items'] ?? [];

        foreach ($attributeItems as $item) {
            try {
                $attributes[] = $this->mapMageAttribute($item);
            } catch (\Exception $e) {
                try {
                    $itemJson = json_encode($item);
                } catch (\Exception $e) {
                    // Skip if unable to capture attribute data
                    $itemJson = '';
                }
                $this->logger->error('Error: magento_customer_attribute_map | Item: ' . $itemJson . ' | Message: ' . $e->getMessage());
            }
        }

        return $attributes;
    }

    protected function mapMageAttribute(array $mageAttrData): BaseAttributeInterface
    {
        $attributeData = [
            'code' => $mageAttrData['attribute_code'] ?? '',
            'name' => $mageAttrData['frontend_label'] ?? '',
            'entity_code' => $this->customerRepository->getEntityCode(),
            'is_used_in_grid' => in_array($mageAttrData['attribute_code'], self::USED_IN_GRID_ATTRIBUTES, true),
            'is_used_in_form' => true, // Enable attribute by default in form
            'is_required' => $mageAttrData['required'] ?? false,
            'sort_order' => $mageAttrData['sort_order'] ?? '999',
        ];

        $frontendInput = $mageAttrData['frontend_input'] ?? 'text';
        switch ($frontendInput) {
            case 'hidden':
            {
                $attributeData['is_used_in_form'] = false;
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
