<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\Customer;

use EveryWorkflow\MagentoConnectorBundle\Model\MagentoRequest;

class SearchRequest extends MagentoRequest implements SearchRequestInterface
{
//    protected string $uri = '/default/V1/customers/search?searchCriteria[pageSize]=20&searchCriteria[currentPage]=1';
    protected string $uri = '/default/V1/customers/search';
}
