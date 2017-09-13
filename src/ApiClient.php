<?php

namespace fotoskladru\log\src;

use fotoskladru\log\exception\src\ApiException;

/**
 * Class ApiWrapper
 *
 * @method null|true                sendMessage(array $params)
 * @package fotoskladru\log
 */
class ApiClient
{

    const METHODS = [
        'sendMessage',
    ];

    const MSG_LIMIT_FORMAT = "msg №%d \n %s \n";

    /** @var string */
    protected $apiToken;
    /** @var \GuzzleHttp\Client */
    protected $client;

    /**
     * ApiClient constructor.
     *
     * @param $client
     * @param $apiToken
     */
    public function __construct($client, $apiToken)
    {
        $this->client = $client;
        $this->apiToken = $apiToken;
    }

    /**
     * @param $methodName
     * @param $arguments
     *
     * @return array|mixed|null
     */
    public function __call($methodName, $arguments)
    {
        if (!array_key_exists($methodName, self::METHODS)) {
            throw new \BadMethodCallException('Unsupported method: ' . $methodName);
        }
        $response = null;
        try {
            $arguments = array_shift($arguments);
            if (isset($arguments['limit']) and is_numeric($arguments['limit']) and strlen($arguments['text']) > $arguments['limit']) {
                $items = str_split($arguments['text'], $arguments['limit']);
                $i = 0;
                $date = date("d.m.Y H:m:s");
                foreach ($items as $item) {
                    $arguments['text'] = sprintf(self::MSG_LIMIT_FORMAT . $item, ++$i, $date);
                    $response = $this->call($methodName, $arguments);
                }
            } else {
                $response = $this->call($methodName, $arguments);
            }
        } catch (ApiException $e) {
        }
        if ($response == null) {
            return null;
        }
        return $response;
    }

    /**
     * @param $methodName
     * @param array $params
     *
     * @return array
     */
    protected function call($methodName, $params = [])
    {
        if (empty($this->apiToken)) {
            throw new \BadMethodCallException('Api token is not configured');
        }
        $params = $params ?: [];
        $query = array_filter($params);
        $options = array_filter([
            'json' => $query,
        ]);
        $uri = 'https://api.telegram.org/bot' . $this->apiToken . '/' . $methodName;
        $response = $this->client->post($uri, $options);
        return $response;
    }
}