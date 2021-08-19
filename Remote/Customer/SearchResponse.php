<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\Customer;

use EveryWorkflow\CustomerBundle\Entity\CustomerEntityInterface;
use EveryWorkflow\CustomerBundle\Repository\CustomerRepositoryInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponse;
use Psr\Log\LoggerInterface;

class SearchResponse extends RemoteResponse implements SearchResponseInterface
{
    protected CustomerRepositoryInterface $customerRepository;
    protected LoggerInterface $logger;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface             $ewRemoteErrorLogger,
        array                       $data = []
    ) {
        parent::__construct($data);
        $this->customerRepository = $customerRepository;
        $this->logger = $ewRemoteErrorLogger;
    }

    public function getCustomers(): array
    {
        $customers = [];
        $customerItems = $this->data['items'] ?? [];

        foreach ($customerItems as $item) {
            try {
                $customers[] = $this->mapMageCustomer($item);
            } catch (\Exception $e) {
                try {
                    $itemJson = json_encode($item);
                } catch (\Exception $e) {
                    // Skip if unable to capture attribute data
                    $itemJson = '';
                }
                $this->logger->error('Error: magento_customer_map | Item: ' . $itemJson . ' | Message: ' . $e->getMessage());
            }
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
                if (isset($customAttr['attribute_code'], $customAttr['value'])) {
                    if (in_array($customAttr['attribute_code'], $availableAttributeCodes, true)) {
                        $customerData[$customAttr['attribute_code']] = $customAttr['value'];
                    }
                }
            }
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->customerRepository->getNewDocument($customerData);
    }
}
