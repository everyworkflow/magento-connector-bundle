<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Factory;

use EveryWorkflow\MagentoConnectorBundle\Model\Client\MagentoRestClient;
use EveryWorkflow\MagentoConnectorBundle\Model\Client\MagentoRestClientInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\ConfigHelperInterface;
use Psr\Log\LoggerInterface;

class MagentoClientFactory implements MagentoClientFactoryInterface
{
    protected MagentoJsonFormatterFactoryInterface $magentoJsonFormatterFactory;
    protected LoggerInterface $logger;
    protected ConfigHelperInterface $configHelper;

    public function __construct(
        MagentoJsonFormatterFactoryInterface $magentoJsonFormatterFactory,
        LoggerInterface $ewRemoteLogger,
        ConfigHelperInterface $configHelper
    ) {
        $this->magentoJsonFormatterFactory = $magentoJsonFormatterFactory;
        $this->logger = $ewRemoteLogger;
        $this->configHelper = $configHelper;
    }

    public function create(array $configs = []): MagentoRestClientInterface
    {
        return new MagentoRestClient(
            $this->magentoJsonFormatterFactory->create(),
            $this->logger,
            $this->configHelper,
            $configs
        );
    }
}
