<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\Customer;

use EveryWorkflow\CustomerBundle\Entity\CustomerEntityInterface;
use EveryWorkflow\CustomerBundle\Repository\CustomerRepositoryInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponse;

class SearchResponse extends RemoteResponse implements SearchResponseInterface
{
    protected CustomerRepositoryInterface $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        array                       $data = []
    ) {
        parent::__construct($data);
        $this->customerRepository = $customerRepository;
    }

    public function getCustomers(): array
    {
        $customers = [];
        $customerItems = $this->data['items'] ?? [];

        foreach ($customerItems as $item) {
            $customers[] = $this->mapMageCustomer($item);
        }

        return $customers;
    }

    protected function mapMageCustomer(array $mageCustomerData): CustomerEntityInterface
    {
        $customerData = [
            'magento_id' => $mageCustomerData['id'] ?? '',
            'updated_at_magento' => $mageCustomerData['updated_at'] ?? '',
        ];
        $availableAttributeCodes = [];

        foreach ($this->customerRepository->getAttributes() as $attribute) {
            $availableAttributeCodes[] = $attribute->getCode();
            if (isset($mageCustomerData[$attribute->getCode()])) {
                $customerData[$attribute->getCode()] = $mageCustomerData[$attribute->getCode()];
            }
        }

        if (isset($mageCustomerData['custom_attributes']) && is_array($mageCustomerData['custom_attributes'])) {
            foreach ($mageCustomerData['custom_attributes'] as $customAttr) {
                if (isset($customAttr['attribute_code'])) {
                    if (isset($availableAttributeCodes[$customAttr['attribute_code']])) {
                        $customerData[$customAttr['attribute_code']] = $mageCustomerData[$customAttr['attribute_code']];
                    }
                }
            }
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->customerRepository->getNewDocument($customerData);
    }
}
