<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model\Client;

use EveryWorkflow\MagentoConnectorBundle\Model\ConfigHelperInterface;
use EveryWorkflow\MagentoConnectorBundle\Model\Formatter\MagentoJsonFormatterInterface;
use EveryWorkflow\RemoteBundle\Model\Client\RestClient;
use EveryWorkflow\RemoteBundle\Model\RemoteRequestInterface;
use Psr\Log\LoggerInterface;

class MagentoRestClient extends RestClient implements MagentoRestClientInterface
{
    protected ConfigHelperInterface $configHelper;

    public function __construct(
        MagentoJsonFormatterInterface $formatter,
        LoggerInterface $ewRemoteLogger,
        ConfigHelperInterface $configHelper,
        array $config = []
    ) {
        parent::__construct($formatter, $ewRemoteLogger, $config);
        $this->configHelper = $configHelper;
    }

    protected function getUrlFromUri(RemoteRequestInterface $request): string
    {
        $uri = $request->getUri();
        if (false !== strpos($uri, 'http://') || false !== strpos($uri, 'https://')) {
            return $uri;
        }

        return (string)$this->configHelper->getConnection('base_url') .
            $this->configHelper->get('connection.api_end_point') .
            $uri;
    }

    protected function getOptions(RemoteRequestInterface $request): array
    {
        $options = parent::getOptions($request);
        $accessToken = $this->configHelper->getConnection('access_token');
        if ($accessToken) {
            $options['headers']['Authorization'] = 'Bearer ' . $accessToken;
        }
        return $options;
    }
}
