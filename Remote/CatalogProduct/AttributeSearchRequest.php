<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct;

use EveryWorkflow\MagentoConnectorBundle\Model\MagentoRequest;

class AttributeSearchRequest extends MagentoRequest implements AttributeSearchRequestInterface
{
    protected string $uri = '/default/V1/products/attributes';
}
