<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use EveryWorkflow\CoreBundle\Model\DataObjectInterface;

interface MagentoSearchCriteriaInterface extends DataObjectInterface
{
    public const FILTER_FIELD = 'field';
    public const FILTER_VALUE = 'value';
    public const FILTER_CONDITION_TYPE = 'conditionType';

    public const SORT_FIELD = 'field';
    public const SORT_DIRECTION = 'direction';

    public const KEY_PAGE_SIZE = 'pageSize';
    public const KEY_CURRENT_PAGE = 'currentPage';
    public const KEY_SORT_ORDERS = 'sortOrders';
    public const KEY_FILTER_GROUPS = 'filterGroups';
    public const KEY_FILTERS = 'filters';

    public function setPageSize(int $pageSize): self;

    public function getPageSize(): int;

    public function setCurrentPage(int $currentPage): self;

    public function getCurrentPage(): int;

    public function setSortOrders(array $sortOrders): self;

    public function getSortOrders(): array;

    public function addSortOrders(string $field, string $direction): self;

    public function setFilterGroups(array $filterGroups): self;

    public function getFilterGroups(): array;

    public function setFilters(array $filters, int $groupId = 0): self;

    public function getFilters(int $groupId = 0): array;

    public function addFilter(string $field, string $value, string $conditionType = 'eq', int $groupId = 0): self;
}
