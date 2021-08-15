<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Remote\Customer;

use EveryWorkflow\MagentoConnectorBundle\Model\MagentoRequest;

class AttributeRequest extends MagentoRequest implements AttributeRequestInterface
{
    protected string $uri = '/default/V1/attributeMetadata/customer';
}
