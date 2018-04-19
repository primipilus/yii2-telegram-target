# Yii 2.0 Telegram Log Target #


## Installation ##

run

```
composer require primipilus/yii2-telegram-target:^1.0
```

or add 

```
"primipilus/yii2-telegram-target": "^1.0"
```

## How To Use ##

```
'log' => [
    'targets' => [
        [
            'class'         => \primipilus\log\TelegramTarget::class,
            'levels'        => ['error'],
            'timeout'       => 0.4,
            'token'         => '123456:abc', 
            'chatId'        => '123456', 
            'prefixMessage' => 'prefixMessage', 
            'proxy'         => 'protocol://login:password@host:port', 
        ],
    ],
],
```