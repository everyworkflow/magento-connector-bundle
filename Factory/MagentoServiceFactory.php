<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Factory;

use EveryWorkflow\MagentoConnectorBundle\Model\MagentoRequest;
use EveryWorkflow\MagentoConnectorBundle\Model\MagentoService;
use EveryWorkflow\MagentoConnectorBundle\Model\MagentoServiceInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MagentoServiceFactory implements MagentoServiceFactoryInterface
{
    protected string $requestClassName;
    protected string $responseHandlerClassName;

    protected ContainerInterface $container;
    protected MagentoSearchCriteriaFactoryInterface $magentoSearchCriteriaFactory;
    protected MagentoClientFactoryInterface $magentoClientFactory;

    public function __construct(
        ContainerInterface $container,
        MagentoSearchCriteriaFactoryInterface $magentoSearchCriteriaFactory,
        MagentoClientFactoryInterface $magentoClientFactory,
        string $requestClassName = MagentoRequest::class,
        string $responseHandlerClassName = RemoteResponse::class
    ) {
        $this->container = $container;
        $this->magentoSearchCriteriaFactory = $magentoSearchCriteriaFactory;
        $this->magentoClientFactory = $magentoClientFactory;
        $this->requestClassName = $requestClassName;
        $this->responseHandlerClassName = $responseHandlerClassName;
    }

    public function setRequestClassName(string $requestClassName): self
    {
        $this->requestClassName = $requestClassName;
        return $this;
    }

    public function setResponseHandlerClassName(string $responseHandlerClassName): self
    {
        $this->responseHandlerClassName = $responseHandlerClassName;
        return $this;
    }

    public function create(array $searchCriteriaData = []): MagentoServiceInterface
    {
        return new MagentoService(
            $this->magentoClientFactory->create(),
            $this->container->get($this->requestClassName),
            $this->container->get($this->responseHandlerClassName),
            $this->magentoSearchCriteriaFactory->create($searchCriteriaData)
        );
    }
}
