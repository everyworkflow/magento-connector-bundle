<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\CatalogProduct;

use EveryWorkflow\CatalogProductBundle\Entity\CatalogProductEntityInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponseInterface;

interface SearchResponseInterface extends RemoteResponseInterface
{
    /**
     * @return CatalogProductEntityInterface[]
     */
    public function getProducts(): array;
}
