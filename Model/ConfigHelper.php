<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use EveryWorkflow\CoreBundle\Model\BaseConfigProvider;

class ConfigHelper extends BaseConfigProvider implements ConfigHelperInterface
{
    public function getConnection(?string $path = null): mixed
    {
        return $this->get('connection.' . $path);
    }
}
