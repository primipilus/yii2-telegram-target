<?php

namespace primipilus\log;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\base\InvalidConfigException;
use yii\log\Target;

/**
 * Class TelegramTarget
 *
 * @package primipilus\log
 */
class TelegramTarget extends Target
{

    const MESSAGE_LIMIT = 4000;

    const TIMEOUT = 0.4;

    /** @var  Client */
    protected $client;

    /** @var  string */
    public $token;

    /** @var  integer */
    public $chatId;

    /** @var  string */
    public $prefixMessage = '';

    /** @var  string */
    public $proxy;

    /**
     * Check required properties
     */
    public function init()
    {
        parent::init();
        foreach (['token', 'chatId'] as $property) {
            if (null === $this->$property) {
                throw new InvalidConfigException(self::class . "::\$$property property must be set");
            }
        }
        $config = (null !== $this->proxy) ? ['proxy' => $this->proxy] : [];
        $this->client = new Client($config);
    }

    public function export()
    {
        $messages = array_map([$this, 'formatMessage'], $this->messages);
        foreach ($messages as $message) {
            foreach ($this->splitMessage($message) as $text) {
                $this->sendMessage([
                    'chat_id' => $this->chatId,
                    'text'    => $text,
                ]);
            }
        }
    }

    /**
     * @param array $params
     */
    protected function sendMessage(array $params) : void
    {
        $options = [
            'json'        => $params,
            'timeout'     => self::TIMEOUT,
            'http_errors' => false,
        ];
        $uri = 'https://api.telegram.org/bot' . $this->token . '/sendMessage';
        try {
            $this->client->post($uri, $options);
        } catch (RequestException $e) {
        }
    }

    /**
     * @param string $message
     *
     * @return array
     */
    protected function splitMessage(string $message) : array
    {
        if (strlen($message) > self::MESSAGE_LIMIT) {
            $i = 0;
            $date = date("Y-m-d H:m:s");
            $messages = array_map(function ($message) use (&$i, $date) {
                return sprintf("%s \n №%d %s \n %s", $this->prefixMessage, ++$i, $date, $message);
            }, str_split($message, self::MESSAGE_LIMIT));
        } else {
            $messages[] = sprintf("%s \n %s", $this->prefixMessage, $message);
        }
        return $messages;
    }
}
