<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use EveryWorkflow\CoreBundle\Model\BaseConfigProviderInterface;

interface ConfigHelperInterface extends BaseConfigProviderInterface
{
    public function getConnection(?string $path = null): mixed;
}
