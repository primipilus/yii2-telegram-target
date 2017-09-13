<?php
namespace fotoskladru\log;

use fotoskladru\log\src\ApiClient;
use GuzzleHttp\Client;
use yii\base\InvalidConfigException;
use yii\log\Target;

/**
 * Class TelegramTarget
 *
 * @package fotoskladru\log
 */
class TelegramTarget extends Target
{

    /** @var  string */
    public $botToken;

    /** @var  integer */
    public $chatId;

    /** @var  ApiClient */
    public $client;

    /**
     * Check required properties
     */
    public function init()
    {
        parent::init();
        foreach (['botToken', 'chatId'] as $property) {
            if (null === $this->$property) {
                throw new InvalidConfigException(self::class . "::\$$property property must be set");
            }
        }
        $this->client = new ApiClient((new Client()), $this->botToken);
    }

    /**
     *
     */
    public function export()
    {
        $messages = array_map([$this, 'formatMessage'], $this->messages);
        foreach ($messages as $message) {
            $this->client->sendMessage([
                'chat_id' => $this->chatId,
                'text'    => $message,
                'limit'   => 4048,
            ]);
        }
    }
}
