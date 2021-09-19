<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use EveryWorkflow\RemoteBundle\Model\RemoteRequest;

class MagentoRequest extends RemoteRequest implements MagentoRequestInterface
{
    protected MagentoSearchCriteriaInterface $searchCriteria;

    public function __construct(MagentoSearchCriteriaInterface $searchCriteria, array $data = [])
    {
        parent::__construct($data);
        $this->searchCriteria = $searchCriteria;
    }

    public function setSearchCriteria(MagentoSearchCriteriaInterface $searchCriteria): self
    {
        $this->searchCriteria = $searchCriteria;
        return $this;
    }

    public function getSearchCriteria(): MagentoSearchCriteriaInterface
    {
        return $this->searchCriteria;
    }

    public function getUri(): string
    {
        $searchCriteriaData = $this->getSearchCriteria()->toArray();
        if (!empty($searchCriteriaData)) {
            return $this->mapSearchCriteriaUrl($searchCriteriaData);
        }
        return $this->uri;
    }

    protected function mapSearchCriteriaUrl(array $searchCriteriaData): string
    {
        $uri = $this->uri;

        if (str_contains($uri, '?')) {
            $uri .= '&';
        } else {
            $uri .= '?';
        }

        $searchParams = [];
        foreach ($searchCriteriaData as $key => $val) {
            if ($key === 'filterGroups' && is_array($val)) {
                foreach($this->mapSearchCriteriaFilterGroup($val) as $filterCriteria) {
                    $searchParams[] = $filterCriteria;
                }
            } elseif (is_string($val) || is_numeric($val)) {
                $searchParams[] = 'searchCriteria[' . $key . ']' . '=' . $val;
            }
        }

        return $uri . implode('&', $searchParams);
    }

    protected function mapSearchCriteriaFilterGroup(array $filterGroups): array
    {
        $searchParams = [];
        foreach ($filterGroups as $groupId => $filters) {
            if (isset($filters['filters']) && is_array($filters['filters'])) {
                foreach ($filters['filters'] as $filterId => $filter) {
                    if (is_array($filter)) {
                        foreach ($filter as $filterKey => $filterVal) {
                            $searchParams[] = 'searchCriteria[filterGroups][' . $groupId . '][filters][' .
                                $filterId . '][' . $filterKey . ']' . '=' . $filterVal;
                        }
                    }
                }
            }
        }
        return $searchParams;
    }
}
