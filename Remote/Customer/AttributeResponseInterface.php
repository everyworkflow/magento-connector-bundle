<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\Customer;

use EveryWorkflow\EavBundle\Attribute\BaseAttributeInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponseInterface;

interface AttributeResponseInterface extends RemoteResponseInterface
{
    /**
     * @return BaseAttributeInterface[]
     */
    public function getAttributes(): array;
}
