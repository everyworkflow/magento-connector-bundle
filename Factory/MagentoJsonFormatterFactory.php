<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Factory;

use EveryWorkflow\MagentoConnectorBundle\Model\Formatter\MagentoJsonFormatter;
use EveryWorkflow\MagentoConnectorBundle\Model\Formatter\MagentoJsonFormatterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MagentoJsonFormatterFactory implements MagentoJsonFormatterFactoryInterface
{
    protected LoggerInterface $logger;
    protected TranslatorInterface $translator;
    
    public function __construct(
        LoggerInterface $ewRemoteLogger,
        TranslatorInterface $translator
    ) {
        $this->logger = $ewRemoteLogger;
        $this->translator = $translator;
    }

    public function create(): MagentoJsonFormatterInterface
    {
        return new MagentoJsonFormatter($this->logger, $this->translator);
    }
}
