<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\Model\Formatter;

use EveryWorkflow\RemoteBundle\Model\Formatter\JsonFormatter;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MagentoJsonFormatter extends JsonFormatter implements MagentoJsonFormatterInterface
{
    protected TranslatorInterface $translator;

    public function __construct(LoggerInterface $ewRemoteLogger, TranslatorInterface $translator)
    {
        parent::__construct($ewRemoteLogger);
        $this->translator = $translator;
    }

    public function handle(mixed $rawResponse): mixed
    {
        if (!$rawResponse instanceof Response) {
            return null;
        }

        $data = [];

        try {
            $remoteContent = $rawResponse->getBody()->getContents();
            if (is_string($remoteContent)) {
                $data += json_decode($remoteContent, true);
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        }

        if ($rawResponse->getStatusCode() !== 200) {
            if (!isset($data['message'])) {
                $data['message'] = 'Remote request unauthorized.';
            }
            $parameters = (isset($data['parameters']) && is_array($data['parameters'])) ? $data['parameters'] : [];
            foreach ($parameters as $key => $val) {
                $parameters['%' . $key] = $val;
            }
            $this->logger->warning('Remote request failed: ' . json_encode($data, 1));
            throw new \Exception($this->translator->trans($data['message'], $parameters));
        }

        return $data;
    }
}
