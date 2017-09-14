# Yii 2.0 Telegram Log Target #


## Installation ##

run

```
php composer require --prefer-dist primipilus/yii2-telegram-log
```

or add 

```
"primipilus/yii2-telegram-log": "*"
```

## How To Use ##

```
'log' => [
    'targets' => [
        [
            'class'         => 'fs\log\TelegramTarget',
            'levels'        => ['error'],
            'token'         => '123456:abc', 
            'chatId'        => '123456', 
            'prefixMessage' => 'prefix', 
        ],
    ],
],
```