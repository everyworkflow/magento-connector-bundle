<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use EveryWorkflow\CoreBundle\Model\DataObject;

class MagentoSearchCriteria extends DataObject implements MagentoSearchCriteriaInterface
{
    public function setPageSize(int $pageSize): self
    {
        $this->setData(self::KEY_PAGE_SIZE, $pageSize);
        return $this;
    }

    public function getPageSize(): int
    {
        return (int) $this->getData(self::KEY_PAGE_SIZE);
    }

    public function setCurrentPage(int $currentPage): self
    {
        $this->setData(self::KEY_CURRENT_PAGE, $currentPage);
        return $this;
    }

    public function getCurrentPage(): int
    {
        return (int) $this->getData(self::KEY_CURRENT_PAGE);
    }

    public function setSortOrders(array $sortOrders): self
    {
        $this->setData(self::KEY_SORT_ORDERS, $sortOrders);
        return $this;
    }

    public function getSortOrders(): array
    {
        return (array) $this->getData(self::KEY_SORT_ORDERS);
    }

    public function addSortOrders(string $field, string $direction): self
    {
        $this->data[self::KEY_SORT_ORDERS][] = [
            self::SORT_FIELD => $field,
            self::SORT_DIRECTION => $direction,
        ];
        return $this;
    }

    public function setFilterGroups(array $filterGroups): self
    {
        $this->setData(self::KEY_FILTER_GROUPS, $filterGroups);
        return $this;
    }

    public function getFilterGroups(): array
    {
        return (array) $this->getData(self::KEY_FILTER_GROUPS);
    }

    public function setFilters(array $filters, int $groupId = 0): self
    {
        $this->data[self::KEY_FILTER_GROUPS][$groupId][self::KEY_FILTERS] = $filters;
        return $this;
    }

    public function getFilters(int $groupId = 0): array
    {
        if (!isset($this->data[self::KEY_FILTER_GROUPS][$groupId])) {
            $this->data[self::KEY_FILTER_GROUPS][$groupId] = [];
        }
        return (array) $this->data[self::KEY_FILTER_GROUPS][$groupId];
    }

    /**
     * https://devdocs.magento.com/guides/v2.4/rest/performing-searches.html
     */
    public function addFilter(string $field, string $value, string $conditionType = 'eq', int $groupId = 0): self
    {
        $this->data[self::KEY_FILTER_GROUPS][$groupId][self::KEY_FILTERS][] = [
            self::FILTER_FIELD => $field,
            self::FILTER_VALUE => $value,
            self::FILTER_CONDITION_TYPE => $conditionType,
        ];
        return $this;
    }
}
