<?php
namespace fs\log\src;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use yii\base\InvalidConfigException;
use yii\log\Target;

/**
 * Class TelegramTarget
 *
 * @package fotoskladru\log
 */
class TelegramTarget extends Target
{

    const MSG_LIMIT = 4000;

    const TIMEOUT = 0.2;

    /** @var  Client */
    protected $client;

    /** @var  string */
    public $token;

    /** @var  integer */
    public $chatId;

    /** @var  string */
    public $prefixMessage = '';

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
        $this->client = new Client();
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
        $query = array_filter($params);
        $options = [
            'json'    => $query,
            'timeout' => self::TIMEOUT,
        ];
        $uri = 'https://api.telegram.org/bot' . $this->token . '/sendMessage';
        try {
            $this->client->post($uri, $options);
        } catch (ClientException $e) {
        }
    }

    /**
     * @param string $message
     *
     * @return array
     */
    protected function splitMessage(string $message) : array
    {
        if (strlen($message) > self::MSG_LIMIT) {
            $i = 0;
            $date = date("Y-m-d H:m:s");
            $messages = array_map(function ($message) use (&$i, $date) {
                return sprintf("%s \n №%d %s \n %s", $this->prefixMessage, ++$i, $date, $message);
            }, str_split($message, self::MSG_LIMIT));
        } else {
            $messages[] = sprintf("%s \n %s", $this->prefixMessage, $message);
        }
        return $messages;
    }
}
