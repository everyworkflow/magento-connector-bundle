<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\Customer;

use EveryWorkflow\RemoteBundle\Model\RemoteResponseInterface;

interface SearchResponseInterface extends RemoteResponseInterface
{
    public function getCustomers(): array;
}
