<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model;

use EveryWorkflow\RemoteBundle\Model\Client\RemoteClientInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteRequestInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteResponseInterface;
use EveryWorkflow\RemoteBundle\Model\RemoteService;

class MagentoService extends RemoteService implements MagentoServiceInterface
{
    protected MagentoSearchCriteriaInterface $searchCriteria;

    public function __construct(
        RemoteClientInterface          $client,
        RemoteRequestInterface         $request,
        RemoteResponseInterface        $responseHandler,
        MagentoSearchCriteriaInterface $searchCriteria
    ) {
        parent::__construct($client, $request, $responseHandler);
        $this->searchCriteria = $searchCriteria;
    }

    public function setSearchCriteria(MagentoSearchCriteriaInterface $searchCriteria): self
    {
        $this->searchCriteria = $searchCriteria;
        return $this;
    }

    public function getSearchCriteria(): MagentoSearchCriteriaInterface
    {
        return $this->searchCriteria;
    }

    public function send(): RemoteResponseInterface
    {
        $request = $this->getRequest();
        if ($request instanceof MagentoRequestInterface) {
            $request->setSearchCriteria($this->getSearchCriteria());
        }
        return $this->client
            ->setResponseHandler($this->getResponseHandler())
            ->send($request);
    }
}
