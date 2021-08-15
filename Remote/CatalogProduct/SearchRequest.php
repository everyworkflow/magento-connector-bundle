<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct;

use EveryWorkflow\MagentoConnectorBundle\Model\MagentoRequest;

class SearchRequest extends MagentoRequest implements SearchRequestInterface
{
    protected string $uri = '/default/V1/products';
}
