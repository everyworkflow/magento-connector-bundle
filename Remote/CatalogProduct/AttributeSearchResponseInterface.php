<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct;

use EveryWorkflow\EavBundle\Attribute\BaseAttributeInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponseInterface;

interface AttributeSearchResponseInterface extends RemoteResponseInterface
{
    /**
     * @return BaseAttributeInterface[]
     */
    public function getAttributes(): array;
}
